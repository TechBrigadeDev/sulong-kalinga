import networkx as nx
from collections import defaultdict
from typing import Dict, List, Set, Any
from tagalog_medical_terms import BODY_PARTS, get_section_keywords

def extract_sections_with_parser(doc, is_assessment=False, is_evaluation=False) -> Dict[str, str]:
    """
    Use calamanCy's parser to identify topical sections based on syntactic structure
    
    Args:
        doc: A spaCy Doc object processed with Tagalog model
        is_assessment: Whether this is an assessment document
        is_evaluation: Whether this is an evaluation document
        
    Returns:
        Dictionary of section names and their content
    """
    # Extract verb phrases as they often indicate actions/states in medical contexts
    verb_phrases = []
    verb_subtrees = defaultdict(list)
    
    # Define section names based on document type
    if is_assessment:
        section_names = ["kalagayan_pangkatawan", "mga_sintomas", "pangangailangan"]
    elif is_evaluation:
        section_names = ["pagbabago", "mga_hakbang", "rekomendasyon"]
    else:
        section_names = ["kalagayan", "obserbasyon", "rekomendasyon"]
    
    # Build a dependency tree and collect important verb subtrees
    for token in doc:
        if token.pos_ == "VERB":
            # Collect the verb and its children
            phrase = [token.text] + [child.text for child in token.children]
            verb_phrases.append(" ".join(phrase))
            
            # Group sentences by their main verb
            for sent in doc.sents:
                if token in sent:
                    verb_subtrees[token.lemma_].append(sent.text)
    
    # Create concept graph from verb phrases
    G = nx.Graph()
    
    # Add nodes for verbs and syntactic subjects/objects
    for token in doc:
        if token.pos_ == "VERB":
            G.add_node(token.i, type="verb", text=token.text, weight=2.0)
            
            # Add subject and object if they exist
            for child in token.children:
                if child.dep_ == "nsubj":  # Subject
                    G.add_node(child.i, type="subject", text=child.text, weight=1.5)
                    G.add_edge(token.i, child.i, weight=2.0)
                elif child.dep_ in ["dobj", "obj", "iobj", "pobj"]:  # Object (expanded for Tagalog)
                    G.add_node(child.i, type="object", text=child.text, weight=1.5)
                    G.add_edge(token.i, child.i, weight=1.5)
    
    # Community detection to find topic clusters
    if G.nodes and len(G.nodes) > 2:  # Need at least a few nodes for community detection
        try:
            communities = nx.community.greedy_modularity_communities(G)
            
            # Create a map of tokens to their communities
            token_community_map = {}
            for i, community in enumerate(communities):
                for node in community:
                    token_community_map[node] = i
            
            # Group sentences by community
            community_sentences = defaultdict(set)
            for sent in doc.sents:
                # Determine which community this sentence most belongs to
                community_counts = defaultdict(int)
                for token in sent:
                    if token.i in token_community_map:
                        community_counts[token_community_map[token.i]] += 1
                
                # Assign to community with highest count
                if community_counts:
                    most_common_community = max(community_counts.items(), key=lambda x: x[1])[0]
                    community_sentences[most_common_community].add(sent.text)
            
            # Convert to sections
            sections = {}
            for i, (community_idx, sentences) in enumerate(sorted(community_sentences.items(), 
                                                                key=lambda x: len(x[1]), reverse=True)):
                if i >= len(section_names):
                    break
                    
                section_name = section_names[i]
                sections[section_name] = " ".join(sentences)
            
            # If we found good sections, return them
            if sections:
                return sections
        
        except Exception as e:
            print(f"Community detection failed: {e}")
            # Fall through to the fallback method
    
    # Fallback: group by main verbs
    section_keywords = get_section_keywords()
    sections = defaultdict(list)
    
    # Process each sentence to categorize by keyword relevance
    for sent in doc.sents:
        sent_text = sent.text.lower()
        
        # Try to categorize by keywords - use appropriate keywords based on document type
        assigned = False
        
        if is_assessment:
            assessment_keys = ["kalagayan_pangkatawan", "mga_sintomas", "pangangailangan"]
            for section in assessment_keys:
                if section in section_keywords and any(keyword in sent_text for keyword in section_keywords[section]):
                    sections[section].append(sent.text)
                    assigned = True
                    break
        elif is_evaluation:
            evaluation_keys = ["pagbabago", "mga_hakbang", "rekomendasyon"]
            for section in evaluation_keys:
                if section in section_keywords and any(keyword in sent_text for keyword in section_keywords[section]):
                    sections[section].append(sent.text)
                    assigned = True
                    break
        else:
            # General section keywords as fallback
            for section, keywords in section_keywords.items():
                if any(keyword in sent_text for keyword in keywords):
                    sections[section].append(sent.text)
                    assigned = True
                    break
        
        # If not assigned, use main verb as fallback
        if not assigned:
            # Find and analyze main verbs in the sentence
            main_verbs = [token for token in sent if token.pos_ == "VERB"]
            
            if main_verbs:
                # Use the most important verb (usually root)
                main_verb = sorted(main_verbs, key=lambda x: len(list(x.children)), reverse=True)[0]
                verb_text = main_verb.lemma_
                
                if is_assessment:
                    # Condition verbs often indicate symptoms
                    if any(v in verb_text for v in ["masakit", "sumasakit", "makirot"]):
                        sections["mga_sintomas"].append(sent.text)
                    # Action verbs often indicate what was done
                    elif main_verb.dep_ == "ROOT":
                        sections["kalagayan_pangkatawan"].append(sent.text)
                    # Default to needs if nothing else fits
                    else:
                        sections["pangangailangan"].append(sent.text)
                elif is_evaluation:
                    # Progress verbs indicate changes
                    if any(v in verb_text for v in ["naging", "bumuti", "lumala"]):
                        sections["pagbabago"].append(sent.text)
                    # Action verbs indicate steps taken
                    elif any(v in verb_text for v in ["ginawa", "isinagawa", "tinulungan"]):
                        sections["mga_hakbang"].append(sent.text)
                    # Recommendation verbs
                    else:
                        sections["rekomendasyon"].append(sent.text)
                else:
                    # Default categorization
                    sections["obserbasyon"].append(sent.text)
    
    # Convert lists to joined strings
    result_sections = {}
    for section, sentences in sections.items():
        if sentences:  # Only include non-empty categories
            result_sections[section] = " ".join(sentences)
    
    return result_sections

def enhance_medical_entities(doc):
    """
    Enhance entity recognition by combining calamanCy's NER and POS tagging
    
    Args:
        doc: A spaCy Doc object processed with Tagalog model
        
    Returns:
        List of enhanced medical entities
    """
    # Create a custom ruler using calamanCy's POS tagger insights
    medical_entities = []
    
    # Additional medical indicators specific to elderly care in Tagalog
    medical_indicators = [
        # From assessment samples
        "masakit", "sumasakit", "kirot", "daing", "hirap", "nangangatal",
        "malabo", "mata", "tenga", "nanghihina", "pagka", "pabagsak",
        "sakit", "dugo", "presyon", "sugat", "lagnat", "mahahaba",
        "madumi", "balanse", "mabigat", "naduduwal", "paglalakad",
        
        # From evaluation samples
        "pampababa", "dugo", "aktibidad", "tubig", "pagtulog", "timbang",
        "pagkain", "maalat", "blood pressure", "routine"
    ]
    
    # Expanded body part detection
    body_part_terms = []
    for variations in BODY_PARTS.values():
        body_part_terms.extend(variations)
    
    # Track potential medical entities
    for sent in doc.sents:
        # Find noun phrases that might be medical terms
        noun_phrases = []
        current_phrase = []
        
        for token in sent:
            # Add adjectives and nouns to current phrase
            if token.pos_ in ["ADJ", "NOUN", "PROPN"]:
                current_phrase.append(token)
            # If we encounter something else, store the current phrase
            elif current_phrase:
                if len(current_phrase) > 0:
                    noun_phrases.append(current_phrase)
                current_phrase = []
        
        # Add any remaining phrase
        if current_phrase:
            noun_phrases.append(current_phrase)
        
        # Analyze each noun phrase for medical relevance
        for phrase in noun_phrases:
            phrase_text = " ".join([token.text for token in phrase])
            
            # Determine if it's likely a medical term
            is_medical = any(indicator in phrase_text.lower() for indicator in medical_indicators)
            
            # Check if it's a body part
            is_body_part = any(part in phrase_text.lower() for part in body_part_terms)
            
            if is_medical or is_body_part:
                entity_type = "BODY_PART" if is_body_part else "MEDICAL"
                start_char = phrase[0].idx
                end_char = phrase[-1].idx + len(phrase[-1].text)
                
                medical_entities.append({
                    "text": phrase_text,
                    "start": start_char,
                    "end": end_char,
                    "type": entity_type
                })
    
    # Look for verb phrases that might indicate symptoms (e.g., "hirap maglakad")
    for sent in doc.sents:
        for i in range(len(sent) - 1):
            if sent[i].pos_ == "ADJ" and sent[i+1].pos_ == "VERB":
                bigram = sent[i].text + " " + sent[i+1].text
                if any(indicator in bigram.lower() for indicator in medical_indicators):
                    medical_entities.append({
                        "text": bigram,
                        "start": sent[i].idx,
                        "end": sent[i+1].idx + len(sent[i+1].text),
                        "type": "MEDICAL"
                    })
    
    return medical_entities

def integrate_enhanced_entities(entities, text):
    """
    Integrate enhanced entities with original text
    
    Args:
        entities: List of entity dictionaries with start, end, text, and type
        text: Original text string
        
    Returns:
        Text with annotated entities or entity information
    """
    # Sort entities by position in text
    entities = sorted(entities, key=lambda x: x["start"])
    
    # Create a structured representation of the entities
    entity_info = {
        "body_parts": [],
        "medical_conditions": [],
        "entity_list": entities
    }
    
    for entity in entities:
        if entity["type"] == "BODY_PART":
            entity_info["body_parts"].append(entity["text"])
        elif entity["type"] == "MEDICAL":
            entity_info["medical_conditions"].append(entity["text"])
    
    # Remove duplicates
    entity_info["body_parts"] = list(set(entity_info["body_parts"]))
    entity_info["medical_conditions"] = list(set(entity_info["medical_conditions"]))
    
    return entity_info