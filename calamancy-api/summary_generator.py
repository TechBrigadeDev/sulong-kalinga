import re
import random
from typing import Dict, List, Tuple, Any, Optional, Set
from nlp_loader import nlp
from entity_extractor import extract_main_subject, extract_structured_elements
from text_processor import split_into_sentences, enhance_measurement_references
from context_analyzer import analyze_document_context, extract_measurement_context, identify_cross_section_entities, get_relevant_entities_for_section, get_semantic_relationship, get_contextual_relationship, determine_optimal_section_order

# Track used transitions to avoid repetition
_used_transitions: Set[str] = set()

def create_enhanced_multi_section_summary(doc, sections, doc_type="assessment"):
    """Create an enhanced executive summary with better sentence synthesis and Filipino cohesiveness."""
    from text_processor import split_into_sentences
    import re
    
    if not sections:
        return "Walang sapat na impormasyon para sa buod."
    
    # Clear previously used transitions for this new summary
    global _used_transitions
    _used_transitions = set()
    
    # Extract all sentences from sections
    all_section_sentences = []
    section_sentences_map = {}
    
    for section_name, section_text in sections.items():
        if section_text:
            section_sentences = split_into_sentences(section_text)
            section_sentences_map[section_name] = section_sentences
            all_section_sentences.extend([(sent, section_name) for sent in section_sentences])
    
    # Get section priorities based on document type
    section_priorities = {
        "assessment": ["mga_sintomas", "kalagayan_pangkatawan", "aktibidad", "kalagayan_mental", "kalagayan_social"],
        "evaluation": ["pangunahing_rekomendasyon", "pagbabago_sa_pamumuhay", "pangangalaga", "mga_hakbang"]
    }.get(doc_type.lower(), [])
    
    # Step 1: Score sentences
    scored_sentences = score_sentences(all_section_sentences, section_priorities, doc_type)
    
    # Step 2: Group related sentences by topics
    grouped_sentences = group_related_sentences(scored_sentences, doc, doc_type)
    
    # Step 3: Synthesize each group into a more compact representation
    synthesized_points = []
    for group in grouped_sentences:
        # Extract just the sentences and their sections from the group
        group_sentences = [(sent, sect) for sent, sect, _ in group]
        # Synthesize this group into one or more concise points
        synthesis = synthesize_sentence_group(group_sentences, doc, doc_type)
        synthesized_points.append(synthesis)
    
    # Step 4: Select most important synthesized points (limit total to ~4)
    if len(synthesized_points) > 4:
        # Prioritize points based on section priorities
        def get_priority_score(point):
            # Check which sections were used to create this point
            point_sections = [sect for _, sect in point['source_sentences']]
            # Use the highest priority section found
            priority_sections = [s for s in section_priorities if s in point_sections]
            return len(section_priorities) - section_priorities.index(priority_sections[0]) if priority_sections else 0
        
        # Sort by priority score
        synthesized_points.sort(key=lambda p: get_priority_score(p), reverse=True)
        synthesized_points = synthesized_points[:4]
    
    # Step 5: Create introduction and conclusion
    introduction = create_expanded_introduction(doc, sections, doc_type)
    conclusion = create_expanded_conclusion(doc, sections, doc_type, 
                                          [p['text'] for p in synthesized_points])
    
    # Step 6: Build the final summary with proper transitions
    connected_sentences = [introduction]
    
    # Add synthesized points with transitions
    for i, point in enumerate(synthesized_points):
        if not point['text'].strip():
            continue
            
        if i == 0:
            # First point after introduction
            transition = select_intro_to_main_transition(introduction, point['text'], doc_type)
            
            # Ensure proper capitalization when connecting
            point_text = point['text']
            if point_text and len(point_text) > 1:
                # First letter lowercase for connecting
                first_clause_lowercase = point_text[0].lower() + point_text[1:]
                sentence = transition + first_clause_lowercase
            else:
                sentence = transition + (point_text if point_text else "")
        else:
            # Subsequent points
            prev_point = synthesized_points[i-1]['text']
            relationship = point.get('relationship_to_previous', 'addition')
            transition = choose_context_aware_transition(prev_point, point['text'], relationship)
            
            # Ensure proper capitalization when connecting
            point_text = point['text']
            if point_text and len(point_text) > 1 and not has_connector(point_text):
                # First letter lowercase for connecting
                first_clause_lowercase = point_text[0].lower() + point_text[1:]
                sentence = transition + first_clause_lowercase
            else:
                # If the point already has a connector or is empty, just add as is
                sentence = transition + point_text
        
        if sentence.strip():
            connected_sentences.append(sentence)
    
    # Add conclusion with appropriate transition
    if synthesized_points and connected_sentences:
        last_point = next((p['text'] for p in reversed(synthesized_points) if p['text']), "")
        
        if last_point:
            conclusion_transition = select_main_to_conclusion_transition(last_point, conclusion)
            
            # Check if conclusion already starts with a connector phrase
            if not any(conclusion.startswith(phrase) for phrase in [
                "Sa kabuuan", "Sa pangkalahatan", "Bilang konklusyon", "Dahil dito",
                "Mula sa mga nabanggit", "Batay sa"
            ]):
                if conclusion and len(conclusion) > 1:
                    conclusion = conclusion_transition + conclusion[0].lower() + conclusion[1:]
                else:
                    conclusion = conclusion_transition + conclusion
            
            connected_sentences.append(conclusion)
        else:
            connected_sentences.append(conclusion)
    else:
        connected_sentences.append(conclusion)
    
    # Combine into final summary
    executive_summary = " ".join(connected_sentences)
    
    # Final post-processing for readability and terminology
    executive_summary = post_process_executive_summary(executive_summary, doc_type=doc_type)
    
    # Replace "pasyente" with "beneficiary"
    executive_summary = replace_with_beneficiary_term(executive_summary)
    
    return executive_summary

def group_related_sentences(scored_sentences, doc, doc_type):
    """Group sentences by related topics for better synthesis."""
    import spacy
    from collections import defaultdict
    
    # Sort by score first
    scored_sentences.sort(key=lambda x: -x[2])
    
    # Create sentence embeddings for semantic similarity
    sentence_texts = [sent for sent, _, _ in scored_sentences]
    
    # Group sentences based on shared entities and keywords
    groups = []
    used_indices = set()
    
    # Get all named entities from the document
    doc_entities = set([ent.text.lower() for ent in doc.ents])
    
    # Function to get entities and keywords from a sentence
    def get_key_elements(text):
        sent_doc = nlp(text)
        entities = set([ent.text.lower() for ent in sent_doc.ents])
        
        # Get important keywords
        important_pos = ["NOUN", "VERB", "ADJ"]
        keywords = set([token.text.lower() for token in sent_doc 
                      if token.pos_ in important_pos and len(token.text) > 3])
        
        return entities, keywords
    
    # First pass: Group by shared entities
    for i, (sent1, sect1, score1) in enumerate(scored_sentences):
        if i in used_indices:
            continue
            
        # Start a new group
        group = [(sent1, sect1, score1)]
        used_indices.add(i)
        
        entities1, keywords1 = get_key_elements(sent1)
        
        # Look for sentences with shared entities
        for j, (sent2, sect2, score2) in enumerate(scored_sentences):
            if j in used_indices or i == j:
                continue
                
            entities2, keywords2 = get_key_elements(sent2)
            
            # If sentences share multiple entities or keywords, group them
            shared_entities = entities1.intersection(entities2)
            shared_keywords = keywords1.intersection(keywords2)
            
            # Different thresholds based on sentence lengths
            len_threshold = 0.2  # Higher threshold means more similarity required
            min_shared = max(1, min(len(entities1), len(entities2)) * len_threshold)
            
            if len(shared_entities) >= min_shared or len(shared_keywords) >= 2:
                group.append((sent2, sect2, score2))
                used_indices.add(j)
        
        groups.append(group)
    
    # Add any remaining sentences as their own groups
    for i, item in enumerate(scored_sentences):
        if i not in used_indices:
            groups.append([item])
            used_indices.add(i)
    
    # Sort groups by the highest score in each group
    groups.sort(key=lambda g: max([score for _, _, score in g]), reverse=True)
    
    return groups

def has_connector(sentence):
    """Enhanced detection of Filipino connector phrases at start of sentences."""
    if not sentence:
        return False
        
    # Expanded list of Filipino connectors with more accurate matching
    connector_phrases = [
        # Multi-word connectors (need exact matching)
        "sa kabilang banda", "sa kabila nito", "bagama't ganito",
        "dahil dito", "bunga nito", "kaya naman", "bilang resulta",
        "bilang karagdagan", "dagdag pa rito", "bukod dito", 
        "upang linawin", "para sa", "sa partikular", "sa detalyadong"
    ]
    
    # Single-word connectors (need word boundary checks)
    connector_words = [
        "dahil", "bunga", "kaya", "gayunpaman", "subalit", "ngunit", 
        "bukod", "karagdagan", "gayundin", "sapagkat", "upang", "para",
        "dulot", "dagdag", "higit", "samantala"
    ]
    
    # Check for complete connector phrases first
    sentence_lower = sentence.lower().strip()
    for phrase in connector_phrases:
        if sentence_lower.startswith(phrase):
            return True
    
    # Then check for individual connector words with word boundary
    words = sentence_lower.split()
    if words:
        # Check if the first word is a connector or starts with one
        first_word = words[0]
        if first_word in connector_words or any(first_word.startswith(conn + " ") for conn in connector_words):
            return True
            
        # Check for common connector patterns in first position
        if len(words) > 1 and first_word in ["sa", "bilang", "upang", "para"] and words[1] not in ["kanyang", "aking", "aming", "ating"]:
            return True
            
    return False

def synthesize_sentence_group(sentence_group, doc, doc_type):
    """Create a synthesized version of related sentences using Filipino language patterns."""
    import re
    from collections import Counter
    
    if not sentence_group:
        return {
            'text': '',
            'source_sentences': [],
            'relationship_to_previous': 'addition'
        }
    
    # Extract sentences and their sections
    sentences = [sent for sent, _ in sentence_group]
    sections = [sect for _, sect in sentence_group]
    
    # If there's only one sentence, return it (with minor enhancements if possible)
    if len(sentences) == 1:
        # Check if we can enhance a single sentence
        enhanced = enhance_single_sentence(sentences[0])
        return {
            'text': enhanced,
            'source_sentences': sentence_group,
            'relationship_to_previous': identify_relationship_type(sentences[0])
        }
    
    # For multiple sentences, check if they're too long combined
    total_words = sum(len(sent.split()) for sent in sentences)
    if total_words > 40:  # If combined sentences would be too long
        # Extract the most important sentence and just enhance it
        main_sentence = select_most_important_sentence(sentences)
        enhanced = enhance_single_sentence(main_sentence)
        return {
            'text': enhanced,
            'source_sentences': sentence_group,
            'relationship_to_previous': identify_relationship_type(main_sentence)
        }
    
    # Extract key entities and concepts from the group
    group_doc = nlp(" ".join(sentences))
    entities = [ent.text for ent in group_doc.ents]
    entity_counter = Counter(entities)
    
    # Get most frequent entity as likely subject
    subject = entity_counter.most_common(1)[0][0] if entity_counter else extract_main_subject(doc)
    
    # Check for common relationship patterns in the sentences
    pattern_types = detect_pattern_types(sentences)
    
    # Combine information using Filipino synthesis patterns appropriate for the relationship
    if "symptom_description" in pattern_types:
        synthesized = synthesize_symptom_description(sentences, subject)
    elif "recommendation" in pattern_types:
        synthesized = synthesize_recommendations(sentences, subject)
    elif "causal_relationship" in pattern_types:
        synthesized = synthesize_causal(sentences, subject)
    elif "contrast_relationship" in pattern_types:
        synthesized = synthesize_contrast(sentences, subject)
    else:
        # Default synthesis approach for other types
        synthesized = synthesize_general(sentences, subject)
    
    # Determine relationship to previous content 
    relationship = determine_synthesis_relationship(synthesized, pattern_types)
    
    return {
        'text': synthesized,
        'source_sentences': sentence_group,
        'relationship_to_previous': relationship
    }

def select_most_important_sentence(sentences):
    """Select the most important sentence from a group based on key indicators."""
    max_score = -1
    selected_sentence = sentences[0] if sentences else ""
    
    for sentence in sentences:
        score = 0
        sent_lower = sentence.lower()
        
        # Prioritize sentences with key medical terms
        if any(term in sent_lower for term in ["agarang", "emergency", "kritikal", "urgent", "kailangan", "inirerekomenda"]):
            score += 5
            
        # Prioritize sentences with measurable data
        if re.search(r'\d+', sentence):
            score += 3
            
        # Prioritize sentences with symptoms or treatments
        sent_doc = nlp(sentence)
        if any(ent.label_ in ["SYMPTOM", "DISEASE", "TREATMENT_METHOD"] for ent in sent_doc.ents):
            score += 4
            
        # Prefer moderate-length sentences
        words = len(sentence.split())
        if 10 <= words <= 30:
            score += 2
            
        if score > max_score:
            max_score = score
            selected_sentence = sentence
            
    return selected_sentence

def detect_pattern_types(sentences):
    """Detect common relationship patterns in Filipino sentences."""
    patterns = set()
    
    # Check for symptom descriptions
    symptom_indicators = ["nakakaranas", "nararamdaman", "dumaranas", "symptoms", "sintomas", 
                         "sakit", "pananakit", "hirap", "nahihirapan"]
    
    # Check for recommendations
    recommendation_indicators = ["inirerekomenda", "iminumungkahi", "pinapayuhan", "dapat", 
                               "kailangan", "kinakailangan", "mahalagang"]
    
    # Check for causal relationships 
    causal_indicators = ["dahil", "sanhi", "bunga", "resulta", "dulot", "dala", 
                        "epekto", "nagdudulot", "nagresulta"]
    
    # Check for contrast relationships
    contrast_indicators = ["ngunit", "subalit", "gayunpaman", "datapwat", "bagamat", 
                          "sa kabila", "sa kabilang banda"]
    
    for sentence in sentences:
        sent_lower = sentence.lower()
        
        if any(indicator in sent_lower for indicator in symptom_indicators):
            patterns.add("symptom_description")
            
        if any(indicator in sent_lower for indicator in recommendation_indicators):
            patterns.add("recommendation")
            
        if any(indicator in sent_lower for indicator in causal_indicators):
            patterns.add("causal_relationship")
            
        if any(indicator in sent_lower for indicator in contrast_indicators):
            patterns.add("contrast_relationship")
    
    return patterns

def synthesize_symptom_description(sentences, subject):
    """Synthesize symptom descriptions using Filipino medical language patterns."""
    symptoms = []
    timeframes = []
    intensifiers = []
    
    # Extract key information from sentences
    for sentence in sentences:
        sent_doc = nlp(sentence)
        
        # Look for symptoms
        for ent in sent_doc.ents:
            if ent.label_ in ["SYMPTOM", "DISEASE"]:
                symptoms.append(ent.text)
        
        # Look for timeframes
        time_phrases = extract_time_phrases(sentence)
        if time_phrases:
            timeframes.extend(time_phrases)
        
        # Look for intensifiers
        intensity_phrases = extract_intensity_phrases(sentence)
        if intensity_phrases:
            intensifiers.extend(intensity_phrases)
    
    # Synthesize the information
    if symptoms:
        symptom_phrase = format_list_in_filipino(symptoms[:2])  # Limit to 2 symptoms for conciseness
        
        if timeframes:
            time_phrase = timeframes[0]
            
            if intensifiers:
                intensity = intensifiers[0]
                return f"Si {subject} ay {time_phrase} nakakaranas ng {intensity} na {symptom_phrase} na nakakaapekto sa kanyang pang-araw-araw na pamumuhay."
            else:
                return f"Si {subject} ay {time_phrase} nakakaranas ng {symptom_phrase} na nangangailangan ng atensyon."
        else:
            return f"Si {subject} ay nagpapakita ng {symptom_phrase} na kailangang mabigyan ng angkop na pangangalaga."
    else:
        # Fall back to extractive approach
        return combine_sentences_basic(sentences)

def synthesize_recommendations(sentences, subject):
    """Synthesize recommendation sentences using Filipino medical language patterns."""
    recommendations = []
    health_targets = []
    treatments = []
    
    # Extract key recommendations
    for sentence in sentences:
        sent_doc = nlp(sentence)
        
        # Look for recommendation phrases
        rec_phrases = extract_recommendation_phrases(sentence)
        if rec_phrases:
            recommendations.extend(rec_phrases)
        
        # Look for health targets
        for ent in sent_doc.ents:
            if ent.label_ in ["BODY_PART", "DISEASE", "SYMPTOM"]:
                health_targets.append(ent.text)
            elif ent.label_ in ["TREATMENT", "TREATMENT_METHOD"]:
                treatments.append(ent.text)
    
    # Synthesize the information
    if recommendations:
        rec_phrase = format_list_in_filipino(recommendations[:2])  # Limit for conciseness
        
        if health_targets:
            target = health_targets[0]
            return f"Inirerekomenda ang {rec_phrase} upang matugunan ang mga isyu sa {target} at mapabuti ang kalagayan ng beneficiary."
        elif treatments:
            treatment = treatments[0]
            return f"Iminumungkahi ang {rec_phrase} kasama ang {treatment} bilang pangunahing hakbang sa pangangalaga ng beneficiary."
        else:
            return f"Mahalagang isagawa ang {rec_phrase} para sa optimal na pangangalaga ng beneficiary."
    else:
        # Fall back to extractive approach
        return combine_sentences_basic(sentences)

def enhance_single_sentence(sentence):
    """Enhance a single sentence with minor grammatical improvements and Filipino-specific fixes."""
    import re
    
    if not sentence:
        return sentence
    
    # Fix common grammar issues in Filipino sentences
    enhanced = sentence
    
    # Fix missing prepositions in commonly misused phrases
    enhanced = re.sub(r'\b(dahil) (sakit|lumalalang|problema)\b', r'\1 sa \2', enhanced)
    enhanced = re.sub(r'\b(timing) (symptoms|ng)\b', r'\1 ng \2', enhanced)
    
    # Fix spacing issues
    enhanced = re.sub(r'\s+', ' ', enhanced)
    enhanced = re.sub(r'\s([,.;:])', r'\1', enhanced)
    
    # Fix merged words (common issues in Filipino medical writing)
    word_fixes = {
        'Tataydati': 'Tatay dati',
        'naobserbahankong': 'naobserbahan kong',
        'anakparehong': 'anak—parehong',
        'patternsa': 'pattern—sa',
        'pagtulognagigising': 'pagtulog nagigising'
    }
    
    for wrong, correct in word_fixes.items():
        enhanced = enhanced.replace(wrong, correct)
    
    # Enhance verbs and adjectives for more precise medical description
    medical_term_enhancements = {
        'nakakaranas': 'malinaw na nakakaranas',
        'mahirap': 'kapansin-pansing mahirap',
        'problema': 'makabuluhang problema',
        'sintomas': 'klinikal na sintomas',
        'pagbabago': 'kapansin-pansing pagbabago'
    }
    
    # Only apply medical term enhancements if they don't make the sentence too verbose
    for term, enhanced_term in medical_term_enhancements.items():
        if term in enhanced.lower() and enhanced.count(' ') < 20:  # Only for reasonably short sentences
            enhanced = re.sub(r'\b' + term + r'\b', enhanced_term, enhanced, flags=re.IGNORECASE, count=1)
    
    # Ensure proper capitalization at start
    if enhanced and enhanced[0].islower():
        enhanced = enhanced[0].upper() + enhanced[1:]
    
    # Ensure ending with period
    if enhanced and not enhanced[-1] in ['.', '!', '?']:
        enhanced += '.'
    
    return enhanced

def synthesize_causal(sentences, subject):
    """Synthesize causal relationship sentences using Filipino patterns."""
    causes = []
    effects = []
    
    # Try to identify causes and effects
    for sentence in sentences:
        cause_effect = extract_cause_effect(sentence)
        if cause_effect:
            cause, effect = cause_effect
            causes.append(cause)
            effects.append(effect)
    
    # If we identified clear cause-effect relationships
    if causes and effects:
        main_cause = combine_phrases(causes[:1])  # Take just the first cause for clarity
        main_effect = combine_phrases(effects[:1])  # Take just the first effect for clarity
        
        return f"Dahil sa {main_cause}, {subject} ay nakakaranas ng {main_effect}, na nangangailangan ng angkop na atensyon."
    else:
        # Fall back to extractive approach with causal connectors
        return combine_with_connector(sentences, "causal")

def synthesize_contrast(sentences, subject):
    """Synthesize contrast relationship sentences using Filipino patterns."""
    # Try to identify contrasting elements
    positive_aspects = []
    negative_aspects = []
    
    # Simple heuristic for identifying positive/negative aspects
    for sentence in sentences:
        sent_lower = sentence.lower()
        
        # Check for negative indicators
        negative_indicators = ["hindi", "wala", "problema", "hirap", "issues", "sintomas", "lumalala"]
        if any(neg in sent_lower for neg in negative_indicators):
            negative_aspects.append(sentence)
        else:
            positive_aspects.append(sentence)
    
    # If we have both positive and negative aspects
    if positive_aspects and negative_aspects:
        positive = summarize_aspect(positive_aspects[0])
        negative = summarize_aspect(negative_aspects[0])
        
        return f"Bagama't {positive}, {subject} ay {negative} na dapat tugunan sa pangangalaga."
    else:
        # Fall back to extractive approach with contrast connectors
        return combine_with_connector(sentences, "contrast")

def synthesize_general(sentences, subject):
    """General purpose synthesis for mixed sentence types."""
    # For general cases, try to extract key information and combine
    key_points = []
    
    for sentence in sentences:
        # Extract the main point from each sentence
        key_point = extract_key_point(sentence)
        if key_point:
            key_points.append(key_point)
    
    if key_points:
        # Combine key points using appropriate Filipino connectors
        combined = format_list_in_filipino(key_points[:3])  # Limit to 3 key points
        
        return f"Si {subject} ay nagpapakita ng {combined} batay sa assessment."
    else:
        # Fall back to extractive approach
        return combine_sentences_basic(sentences)

def format_list_in_filipino(items):
    """Format a list of items using appropriate Filipino conjunctions."""
    if not items:
        return ""
    elif len(items) == 1:
        return items[0]
    elif len(items) == 2:
        return f"{items[0]} at {items[1]}"
    else:
        # For 3+ items
        return ", ".join(items[:-1]) + ", at " + items[-1]

def extract_time_phrases(sentence):
    """Extract time-related phrases from Filipino sentences."""
    time_patterns = [
        r"(?:sa|noong|nitong|nitong nakaraang|sa nakaraang) ([^\.,;]+(?:araw|linggo|buwan|taon))",
        r"(nakaraang [^\.,;]+(?:araw|linggo|buwan|taon))",
        r"(ilang (?:araw|linggo|buwan|taon) na)",
        r"(kamakailan lang|kamakailang)",
        r"(noon pa|simula pa|simula noong)",
    ]
    
    results = []
    for pattern in time_patterns:
        matches = re.findall(pattern, sentence.lower())
        results.extend(matches)
    
    return results

def extract_intensity_phrases(sentence):
    """Extract phrases indicating intensity from Filipino sentences."""
    intensity_patterns = [
        r"(matinding|malubhang|grabeng|malalang)",
        r"(bahagyang|kaunting|banayad na)",
        r"(patuloy na|tuluy-tuloy na|hindi tumitigil na)",
        r"(paminsan-minsan|pana-panahong|minsanang)",
    ]
    
    results = []
    for pattern in intensity_patterns:
        matches = re.findall(pattern, sentence.lower())
        results.extend(matches)
    
    return results

def extract_recommendation_phrases(sentence):
    """Extract recommendation phrases from Filipino sentences."""
    # Look for recommendation verbs followed by an action
    rec_patterns = [
        r"inirerekomenda (?:ko|namin|kong|naming|na) ([^\.,;]+)",
        r"iminumungkahi (?:ko|namin|kong|naming|na) ([^\.,;]+)",
        r"pinapayuhan (?:ko|namin|kong|naming|na) ([^\.,;]+)",
        r"dapat ([^\.,;]+)",
        r"kailangan (?:na|ng|) ([^\.,;]+)",
    ]
    
    results = []
    for pattern in rec_patterns:
        matches = re.findall(pattern, sentence.lower())
        results.extend(matches)
    
    # If no specific recommendations found, check for noun phrases after common markers
    if not results:
        general_markers = ["inirerekomenda", "iminumungkahi", "pinapayuhan", "dapat", "kailangan"]
        for marker in general_markers:
            if marker in sentence.lower():
                # Get the part after the marker
                parts = re.split(marker, sentence.lower(), 1)
                if len(parts) > 1:
                    # Extract a reasonable chunk
                    chunk = re.split(r'[\.;,]', parts[1])[0].strip()
                    if chunk and len(chunk) > 10:  # Minimum meaningful length
                        results.append(chunk)
    
    return results

def extract_cause_effect(sentence):
    """Extract cause-effect relationships from Filipino sentences."""
    # Pattern: [cause] dahil/sanhi/bunga [effect]
    cause_effect_patterns = [
        r"(.+) dahil (sa|ng|) (.+)",
        r"(.+) sanhi (sa|ng|) (.+)",
        r"(.+) bunga (sa|ng|) (.+)",
        r"dahil (sa|ng|) (.+), (.+)",
        r"sanhi (sa|ng|) (.+), (.+)",
    ]
    
    for pattern in cause_effect_patterns:
        matches = re.findall(pattern, sentence.lower())
        if matches:
            if len(matches[0]) == 3:  # First pattern type
                return matches[0][0].strip(), matches[0][2].strip()
            elif len(matches[0]) == 2:  # Second pattern type
                return matches[0][1].strip(), matches[0][0].strip()
    
    return None

def combine_phrases(phrases):
    """Combine multiple phrases into a cohesive Filipino sentence fragment."""
    if not phrases:
        return ""
    
    # Remove duplicates while preserving order
    unique_phrases = []
    for phrase in phrases:
        if phrase not in unique_phrases:
            unique_phrases.append(phrase)
    
    # Format the list using Filipino conventions
    return format_list_in_filipino(unique_phrases)

def combine_with_connector(sentences, relationship_type):
    """Combine sentences using appropriate Filipino connectors for the relationship type."""
    if not sentences:
        return ""
    
    if len(sentences) == 1:
        return sentences[0]
    
    # Select connector based on relationship type
    if relationship_type == "causal":
        connectors = ["Dahil dito, ", "Bilang resulta, ", "Bunga nito, "]
    elif relationship_type == "contrast":
        connectors = ["Gayunpaman, ", "Sa kabilang banda, ", "Subalit, "]
    elif relationship_type == "elaboration":
        connectors = ["Higit pa rito, ", "Bukod dito, ", "Dagdag pa, "]
    else:
        connectors = ["At saka, ", "Dagdag pa rito, ", "Bukod dito, "]
    
    # Combine first two sentences with appropriate connector
    import random
    connector = random.choice(connectors)
    combined = sentences[0] + " " + connector.lower() + sentences[1][0].lower() + sentences[1][1:]
    
    # Add remaining sentences with simpler connectors
    for sentence in sentences[2:]:
        combined += " Dagdag pa rito, " + sentence[0].lower() + sentence[1:]
    
    return combined

def combine_sentences_basic(sentences):
    """Basic sentence combining when more advanced synthesis fails."""
    if not sentences:
        return ""
        
    if len(sentences) == 1:
        return sentences[0]
    
    # Extract key phrases from each sentence
    key_phrases = []
    for sentence in sentences:
        # Try to get a meaningful chunk
        chunks = re.split(r'[\.;,]', sentence)
        if chunks:
            # Take the longest chunk
            best_chunk = max(chunks, key=len).strip()
            if best_chunk and len(best_chunk) > 20:  # Minimum meaningful length
                key_phrases.append(best_chunk)
    
    # If we found meaningful chunks, combine them
    if key_phrases:
        return format_list_in_filipino(key_phrases)
    else:
        # Fallback: just return the first sentence
        return sentences[0]

def summarize_aspect(sentence):
    """Extract a key aspect from a sentence for contrast synthesis."""
    # Try to get the main clause
    parts = re.split(r'[,;]', sentence)
    if parts:
        return parts[0].strip()
    return sentence

def extract_key_point(sentence):
    """Extract the key point from a sentence."""
    sent_doc = nlp(sentence)
    
    # Try to find the main verb and its arguments
    for token in sent_doc:
        if token.pos_ == "VERB" and token.dep_ in ["ROOT", "ccomp"]:
            # Get the subject if available
            subject = None
            for child in token.children:
                if child.dep_ in ["nsubj", "nsubjpass"]:
                    subject_span = get_span_with_children(child)
                    subject = subject_span.text
                    break
            
            # Get the object if available
            obj = None
            for child in token.children:
                if child.dep_ in ["dobj", "pobj"]:
                    obj_span = get_span_with_children(child)
                    obj = obj_span.text
                    break
            
            # Construct a key point
            if subject and obj:
                return f"{subject} {token.text} {obj}"
            elif subject:
                return f"{subject} {token.text}"
    
    # Fallback: try to extract a noun phrase and a verb
    for chunk in sent_doc.noun_chunks:
        for token in sent_doc:
            if token.pos_ == "VERB":
                return f"{chunk.text} {token.text}"
    
    # If all else fails, just take the first part of the sentence
    if len(sentence) > 40:
        return sentence[:40] + "..."
    return sentence

def get_span_with_children(token):
    """Get a token and all its children as a span."""
    # This is a simplified approximation
    min_i = token.i
    max_i = token.i
    for child in token.subtree:
        min_i = min(min_i, child.i)
        max_i = max(max_i, child.i)
    return token.doc[min_i:max_i+1]

def determine_synthesis_relationship(synthesized_text, pattern_types):
    """Determine the relationship of the synthesized text to previous content."""
    if "recommendation" in pattern_types:
        return "action"
    elif "causal_relationship" in pattern_types:
        return "causation"
    elif "contrast_relationship" in pattern_types:
        return "contrast"
    elif "symptom_description" in pattern_types:
        return "elaboration"
    else:
        return "addition"

def identify_relationship_type(sentence):
    """Identify the rhetorical relationship of a sentence."""
    sent_lower = sentence.lower()
    
    # Check for causal indicators
    if any(term in sent_lower for term in ["dahil", "sanhi", "bunga", "dulot", "epekto"]):
        return "causation"
        
    # Check for contrast indicators
    if any(term in sent_lower for term in ["ngunit", "subalit", "gayunpaman", "bagamat"]):
        return "contrast"
        
    # Check for action/recommendation indicators
    if any(term in sent_lower for term in ["inirerekomenda", "iminumungkahi", "dapat", "kailangan"]):
        return "action"
        
    # Default to addition
    return "addition"

def create_expanded_introduction(doc, sections, doc_type):
    """Create comprehensive introduction based on document content analysis."""
    # Extract subject (person) from the document
    subject = extract_main_subject(doc)
    if not subject:
        subject = "Pasyente"
    
    # For assessment documents
    if doc_type.lower() == "assessment":
        # Analyze contents to determine introduction focus
        has_mobility_issues = False
        has_cognitive_issues = False
        has_mental_health_issues = False
        has_sensory_issues = False
        has_chronic_pain = False
        has_multiple_symptoms = False
        
        # Check for specific conditions by analyzing sections
        for section_name, section_text in sections.items():
            if not section_text:
                continue
                
            section_doc = nlp(section_text.lower())
            
            # Check for mobility-related issues
            if any(term in section_text.lower() for term in ["paglakad", "balanse", "pagbagsak", "pagkahulog", 
                                                           "walker", "wheelchair", "lumpo", "paralysis", "pagtayo"]):
                has_mobility_issues = True
                
            # Check for cognitive issues
            if any(term in section_text.lower() for term in ["memory", "nakalimutan", "confusion", "malito", 
                                                           "nakakalimot", "dementia", "alzheimer"]):
                has_cognitive_issues = True
                
            # Check for mental health issues
            if any(term in section_text.lower() for term in ["depression", "anxiety", "lungkot", "pagkabalisa", 
                                                           "stress", "tension", "worry"]):
                has_mental_health_issues = True
                
            # Check for sensory issues
            if any(term in section_text.lower() for term in ["pandinig", "paningin", "hearing", "vision", 
                                                           "bingi", "bulag", "blurry"]):
                has_sensory_issues = True
                
            # Check for chronic pain
            if any(term in section_text.lower() for term in ["chronic pain", "matagalang pananakit", 
                                                           "matinding sakit", "sakit na hindi nawawala"]):
                has_chronic_pain = True
            
            # Check if there are multiple major symptoms/conditions
            symptom_count = 0
            for ent in section_doc.ents:
                if ent.label_ in ["SYMPTOM", "DISEASE"]:
                    symptom_count += 1
            
            if symptom_count >= 3:
                has_multiple_symptoms = True
        
        # Create tailored introduction based on findings
        if has_mobility_issues:
            return f"Batay sa pag-aaral ng pangangatawan, si {subject} ay nagpapakita ng mga kapansin-pansing limitasyon sa mobilidad na nakakaapekto sa kanyang pang-araw-araw na pamumuhay."
        
        elif has_cognitive_issues:
            return f"Ang assessment na ito ay nagpapakita na si {subject} ay nakakaranas ng mga cognitive challenges na nangangailangan ng komprehensibong approach sa pangangalaga."
        
        elif has_mental_health_issues:
            return f"Nakita sa assessment na si {subject} ay dumaranas ng mga psychological challenges na nakakaapekto sa kanyang pangkalahatang kalusugan at kalidad ng buhay."
        
        elif has_sensory_issues:
            return f"Ang assessment ay nagpapakita na si {subject} ay nakakaranas ng sensory limitations na nakakaapekto sa kanyang komunikasyon at daily functioning."
        
        elif has_chronic_pain:
            return f"Si {subject} ay nakakaranas ng patuloy na pananakit na makabuluhang nakakaapekto sa kanyang mobility, kagalingan, at kalidad ng buhay ayon sa assessment."
        
        elif has_multiple_symptoms:
            return f"Si {subject} ay nagpapakita ng multiple health issues na magkakaugnay at nangangailangan ng holistic na pangangalaga batay sa komprehensibong assessment."
        
        else:
            # Extract key entities from document
            conditions = []
            symptoms = []
            
            for ent in doc.ents:
                if ent.label_ == "DISEASE":
                    conditions.append(ent.text)
                elif ent.label_ == "SYMPTOM":
                    symptoms.append(ent.text)
            
            # Create appropriate intro based on what's available
            if conditions:
                condition = conditions[0]
                return f"Si {subject} ay nagpapakita ng {condition} na nangangailangan ng atensyon at masinsinang pangangalaga."
            elif symptoms:
                symptom = symptoms[0]
                return f"Ayon sa assessment, si {subject} ay dumaranas ng {symptom} na nakakaapekto sa kanyang pang-araw-araw na gawain."
            else:
                return f"Ang assessment na ito ay naglalaman ng mahahalagang obserbasyon tungkol sa kasalukuyang kalagayan ni {subject}."
    
    # For evaluation documents
    else:
        # Analyze document for specific recommendation types
        has_medical_referral = False
        has_medication_recommendations = False
        has_lifestyle_recommendations = False
        has_urgent_recommendations = False
        has_home_modification = False
        
        # Check for specific recommendation types
        for section_name, section_text in sections.items():
            if not section_text:
                continue
                
            section_text_lower = section_text.lower()
            
            # Check for referrals
            if any(term in section_text_lower for term in ["konsulta", "referral", "doctor", "doktor", "specialist", 
                                                         "physical therapist", "occupational therapist"]):
                has_medical_referral = True
                
            # Check for medication recommendations
            if any(term in section_text_lower for term in ["gamot", "medication", "tabletas", "pills", 
                                                         "inirerekumendang gamot", "reseta"]):
                has_medication_recommendations = True
                
            # Check for lifestyle recommendations
            if any(term in section_text_lower for term in ["lifestyle", "diet", "ehersisyo", "exercise", 
                                                         "pagkain", "nutrition", "pamumuhay"]):
                has_lifestyle_recommendations = True
                
            # Check for urgency
            if any(term in section_text_lower for term in ["agaran", "kaagad", "immediately", "urgent", 
                                                         "emergency", "kritikal"]):
                has_urgent_recommendations = True
                
            # Check for home modifications
            if any(term in section_text_lower for term in ["bathroom", "handrails", "grab bars", "banyo", 
                                                         "hagdanan", "stairs", "modifications"]):
                has_home_modification = True
        
        # Create tailored introduction based on findings
        if has_urgent_recommendations:
            return "Batay sa komprehensibong evaluation, may mga rekomendasyon na nangangailangan ng agarang aksyon para sa kaligtasan at kalusugan ng pasyente."
        
        elif has_medical_referral:
            return "Ang evaluation na ito ay nagmumungkahi ng mga kinakailangang medical referrals at interventions para sa optimal na pangangalaga ng pasyente."
        
        elif has_medication_recommendations:
            return "Ang evaluation ay nagbibigay ng mga rekomendasyon sa medication management at therapeutic approaches para sa mas mahusay na pangangalaga."
        
        elif has_lifestyle_recommendations:
            return "Batay sa maingat na evaluation, may mga mahahalagang rekomendasyon sa lifestyle modifications at supportive interventions para mapabuti ang kalusugan ng pasyente."
        
        elif has_home_modification:
            return "Ang evaluation ay naglalaman ng mga rekomendasyon para sa environmental modifications at safety measures upang mapabuti ang kalagayan ng pasyente sa tahanan."
        
        else:
            # Check for general recommendation entities
            recommendations = []
            treatments = []
            
            for ent in doc.ents:
                if ent.label_ == "RECOMMENDATION":
                    recommendations.append(ent.text)
                elif ent.label_ == "TREATMENT" or ent.label_ == "TREATMENT_METHOD":
                    treatments.append(ent.text)
            
            # Create appropriate intro
            if recommendations:
                return "Ang evaluation na ito ay nagbibigay ng mga estratehikong rekomendasyon batay sa komprehensibong assessment ng pasyente."
            elif treatments:
                return "Batay sa evaluation, may mga naaangkop na therapeutic approaches na inirerekumenda para mapabuti ang kalusugan at kagalingan ng pasyente."
            else:
                return "Ang sumusunod ay mga rekomendasyon at hakbang na iminumungkahi matapos ang komprehensibong evaluation ng pasyente."

def create_expanded_conclusion(doc, sections, doc_type, selected_sentences):
    """Create comprehensive conclusion based on document content and selected sentences."""
    
    # For assessment documents
    if doc_type.lower() == "assessment":
        # Analyze contents to determine conclusion focus
        has_social_support_needs = False
        has_safety_concerns = False
        has_progressive_condition = False
        has_mental_health_concerns = False
        has_monitoring_needs = False
        has_caregiver_needs = False
        
        # Check sections and selected sentences for specific needs
        all_text = " ".join(selected_sentences).lower()
        for section_name, section_text in sections.items():
            if not section_text:
                continue
                
            section_text_lower = section_text.lower()
            
            # Check for social support needs
            if any(term in section_text_lower for term in ["pamilya", "social support", "kakapusan", "nag-iisa", 
                                                         "isolated", "walang katulong", "nangangailangan ng tulong"]):
                has_social_support_needs = True
                
            # Check for safety concerns
            if any(term in section_text_lower for term in ["nahulog", "nadulas", "risk", "panganib", 
                                                         "accident", "safety", "kaligtasan", "insidente"]):
                has_safety_concerns = True
                
            # Check for progressive conditions
            if any(term in section_text_lower for term in ["lumalalâ", "paglala", "progressive", "deteriorating", 
                                                         "bumababa", "gradually", "unti-unti"]):
                has_progressive_condition = True
                
            # Check for mental health concerns
            if any(term in section_text_lower for term in ["depression", "anxiety", "stress", "pagkabalisa", 
                                                         "lungkot", "pagkabahala", "mental health"]):
                has_mental_health_concerns = True
                
            # Check for explicit monitoring needs
            if any(term in section_text_lower for term in ["monitor", "bantayan", "subaybayan", 
                                                         "regular check", "obserbahan", "follow-up"]):
                has_monitoring_needs = True
                
            # Check for caregiver needs
            if any(term in section_text_lower for term in ["caregiver", "tagapag-alaga", "mag-alaga", 
                                                         "pag-aalaga", "support system", "pamilya"]):
                has_caregiver_needs = True
        
        # Create tailored conclusion based on findings
        if has_safety_concerns and has_monitoring_needs:
            return "Dahil sa mga naturang obserbasyon, kinakailangan ng regular na monitoring at pagpapatupad ng safety measures upang maiwasan ang mga aksidente at komplikasyon sa hinaharap."
            
        elif has_progressive_condition and has_monitoring_needs:
            return "Dahil sa progresibong katangian ng kanyang kondisyon, mahalagang magkaroon ng regular na assessment at maingat na monitoring upang maagapan ang anumang pagbabago sa kanyang kalagayan."
            
        elif has_mental_health_concerns and has_social_support_needs:
            return "Ang kanyang psychological well-being at social support system ay mahalagang aspeto ng komprehensibong pangangalaga at nangangailangan ng patuloy na atensyon at suporta."
            
        elif has_safety_concerns:
            return "Ang mga nabanggit na obserbasyon ay nagpapahiwatig ng pangangailangan para sa preventive safety measures at environmental modifications upang mabawasan ang risk ng aksidente o karagdagang komplikasyon."
            
        elif has_social_support_needs:
            return "Ang patuloy na suporta mula sa pamilya at healthcare team ay mahalaga para sa kanyang pangkalahatang kalusugan at pangangailangan sa pang-araw-araw."
            
        elif has_caregiver_needs:
            return "Ang edukasyon at suporta para sa mga tagapag-alaga ay kritikal sa pagbibigay ng optimal na pangangalaga habang iniiwasan ang caregiver burnout at pagod."
            
        elif has_progressive_condition:
            return "Mahalagang maging proactive sa pagharap sa progresibong katangian ng kanyang kondisyon sa pamamagitan ng regular na reassessment at pag-adjust ng care plan."
            
        elif has_monitoring_needs:
            return "Ang regular na pag-monitor at pag-assess ng kanyang kondisyon ay kritikal para sa patuloy na pangangalaga at pagpigil sa potensyal na komplikasyon."
            
        else:
            return "Ang patuloy na pag-assess at pagtugon sa kanyang mga pangangailangan ay makakatulong upang mapabuti ang kanyang pangkalahatang kalagayan at kalidad ng buhay."
    
    # For evaluation documents
    else:
        # Analyze selected sentences for recommendation types to create appropriate conclusion
        has_medical_recommendations = False
        has_lifestyle_recommendations = False
        has_monitoring_recommendations = False
        has_home_modifications = False
        has_urgent_actions = False
        has_family_involvement = False
        
        # Check selected sentences for recommendation types
        all_text = " ".join(selected_sentences).lower()
        
        if any(term in all_text for term in ["konsulta", "referral", "doctor", "doktor", "specialist", 
                                           "physical therapist", "medical assessment"]):
            has_medical_recommendations = True
            
        if any(term in all_text for term in ["diet", "nutrition", "pagkain", "ehersisyo", "exercise", 
                                           "lifestyle", "gawi", "pamumuhay"]):
            has_lifestyle_recommendations = True
            
        if any(term in all_text for term in ["monitor", "subaybayan", "bantayan", "regular check", 
                                           "track", "i-document"]):
            has_monitoring_recommendations = True
            
        if any(term in all_text for term in ["grab bars", "railings", "bathroom modifications", 
                                           "pag-adjust ng bahay", "safety equipment"]):
            has_home_modifications = True
            
        if any(term in all_text for term in ["agaran", "immediate", "emergency", "urgent", 
                                           "kritikal", "hindi dapat ipagpaliban"]):
            has_urgent_actions = True
            
        if any(term in all_text for term in ["pamilya", "asawa", "anak", "apo", "kamag-anak", 
                                           "tagapag-alaga", "caregiver"]):
            has_family_involvement = True
        
        # Create tailored conclusion based on findings
        if has_urgent_actions and has_medical_recommendations:
            return "Ang mabilis na implementasyon ng mga rekomendasyon at agarang medikal na konsultasyon ay mahalaga para sa optimal na pangangalaga at pagsugpo sa mga posibleng komplikasyon."
            
        elif has_medical_recommendations and has_monitoring_recommendations:
            return "Ang mga rekomendasyon sa medikal na konsultasyon kasama ang regular na monitoring ay dapat sundin upang masiguro ang tuloy-tuloy na pagpapabuti ng kanyang kondisyon."
            
        elif has_lifestyle_recommendations and has_monitoring_recommendations:
            return "Ang mga pagbabagong lifestyle na ito, kasama ang regular na monitoring, ay dapat ituloy at i-evaluate periodically para sa continued improvement ng kanyang kalusugan."
            
        elif has_home_modifications and has_family_involvement:
            return "Ang mga environmental modifications at aktibong pakikilahok ng pamilya ay kritikal sa pagkamit ng mas ligtas at mas mabuting kalidad ng pamumuhay para sa pasyente."
            
        elif has_medical_recommendations:
            return "Ang pagpapatupad ng mga medikal na rekomendasyon na ito ay dapat maging prayoridad at kailangang regular na i-reassess sa pamamagitan ng follow-up consultations."
            
        elif has_lifestyle_recommendations:
            return "Ang pagpapatupad ng mga rekomendasyon sa pagbabago ng lifestyle at araw-araw na gawain ay mahalagang hakbang tungo sa pagpapabuti ng kanyang pangkalahatang kalusugan at kalidad ng buhay."
            
        elif has_family_involvement:
            return "Ang patuloy na kolaborasyon ng pamilya, healthcare providers, at pasyente ay mahalaga sa matagumpay na implementasyon ng mga rekomendasyon at pangangalagang ito."
            
        elif has_monitoring_recommendations:
            return "Ang masusing pagsubaybay sa mga pagbabago at regular na komunikasyon sa healthcare team ay makakatulong sa pag-adjust at pagpapahusay ng care plan ayon sa pangangailangan."
            
        else:
            return "Ang mga rekomendasyon at interbensyong ito ay dapat regular na suriin at i-adjust ayon sa pagbabago ng kondisyon ng pasyente para sa pinakamabuting resulta."

def select_intro_to_main_transition(intro, first_point, doc_type):
    """Select appropriate transition from introduction to first main point."""
    import random
    
    # For assessment documents
    if doc_type.lower() == "assessment":
        transitions = [
            "Una sa lahat, ", 
            "Partikular na, ", 
            "Nakita sa assessment na ", 
            "Ayon sa obserbasyon, ", 
            "Base sa pagsusuri, ",
            "Kapansin-pansin na ",
            "Makikita na ",
            "Sa detalyadong assessment, "
        ]
    # For evaluation documents 
    else:
        transitions = [
            "Bilang pangunahing rekomendasyon, ",
            "Una sa lahat, ",
            "Mahalagang bigyang-pansin na ",
            "Sa evaluation, inirerekomenda na ",
            "Bilang pangunahing hakbang, ",
            "Ayon sa pagsusuri, ",
            "Alinsunod sa mga natuklasan, ",
            "Base sa evaluation, "
        ]
    
    # Avoid repetition with intro
    filtered_transitions = [t for t in transitions if t.lower() not in intro.lower()]
    if filtered_transitions:
        return random.choice(filtered_transitions)
    return transitions[0]

def select_main_to_conclusion_transition(last_point, conclusion):
    """Select appropriate transition from main points to conclusion."""
    import random
    
    transitions = [
        "Sa pangkalahatan, ", 
        "Bilang konklusyon, ", 
        "Sa kabuuan, ",
        "Sa pinal na obserbasyon, ",
        "Dahil dito, ",
        "Mula sa mga nabanggit, ",
        "Batay sa mga obserbasyon at rekomendasyon, ",
        "Bunga ng mga natuklasan, "
    ]
    
    # Avoid repetition with conclusion
    filtered_transitions = [t for t in transitions if t.lower() not in conclusion.lower()]
    if filtered_transitions:
        return random.choice(filtered_transitions)
    return transitions[0]

def select_appropriate_connector(prev_sent, curr_sent):
    """Enhanced connector selection based on content analysis."""
    import random
    
    # Different types of connectors with better Filipino medical transitions
    addition_connectors = [
        "Dagdag pa rito, ", "Bukod dito, ", "Gayundin, ", 
        "Karagdagan dito, ", "Bilang karagdagan, ", "Isa pa, "
    ]
    
    contrast_connectors = [
        "Gayunpaman, ", "Subalit, ", "Sa kabilang banda, ", 
        "Ngunit, ", "Sa kabila nito, ", "Bagama't ganito, "
    ]
    
    causal_connectors = [
        "Dahil dito, ", "Bunga nito, ", "Kaya naman, ", 
        "Bilang resulta, ", "Dulot nito, ", "Sa ganitong dahilan, "
    ]
    
    elaboration_connectors = [
        "Upang linawin, ", "Higit pa rito, ", "Sa partikular, ", 
        "Upang mas maunawaan, ", "Sa detalyadong pagtingin, "
    ]
    
    action_connectors = [
        "Para dito, ", "Upang matugunan ito, ", "Sa ganitong sitwasyon, ", 
        "Para sa mas mabuting pangangalaga, ", "Bilang hakbang, "
    ]
    
    # Check for keywords that suggest a specific relationship
    curr_lower = curr_sent.lower()
    prev_lower = prev_sent.lower()
    
    # Check if previous sentence mentions a condition and current mentions treatment
    condition_terms = ["kondisyon", "sakit", "sintomas", "karamdaman", "problema"]
    treatment_terms = ["gamutin", "lunas", "solusyon", "rekomendasyon", "treatment"]
    
    condition_then_treatment = any(term in prev_lower for term in condition_terms) and \
                               any(term in curr_lower for term in treatment_terms)
    
    # Check for contrast relationship
    if any(term in curr_lower for term in ["pero", "ngunit", "subalit", "gayunman", "datapwat"]):
        return random.choice(contrast_connectors)
    
    # Check for causation relationship
    elif any(term in curr_lower for term in ["dahil", "sanhi", "bunga", "resulta", "kaya"]):
        return random.choice(causal_connectors)
    
    # Check for condition-treatment relationship
    elif condition_then_treatment:
        return random.choice(action_connectors)
    
    # Check for elaboration (typically involving same topic with more detail)
    elif len(set(prev_lower.split()) & set(curr_lower.split())) > 3:
        return random.choice(elaboration_connectors)
    
    # Default to addition
    else:
        return random.choice(addition_connectors)

def has_connector(sentence):
    """Enhanced detection of Filipino connector phrases at start of sentences."""
    # Expanded list of Filipino connectors
    connectors = [
        # Simple connectors
        "dahil", "bunga", "kaya", "gayunpaman", "subalit", "ngunit", 
        "bukod", "karagdagan", "isa pa", "gayundin", "sa ganitong",
        "bilang", "upang", "para", "dulot", "dagdag", "higit",
        
        # Phrase connectors (these need special handling)
        "sa kabilang banda", "sa kabila nito", "bagama't ganito",
        "dahil dito", "bunga nito", "kaya naman", "bilang resulta",
        "bilang karagdagan", "dagdag pa rito", "bukod dito", 
        "upang linawin", "para sa", "sa partikular", "sa detalyadong"
    ]
    
    # First check whole phrases
    sentence_lower = sentence.lower()
    
    # Check for complete phrases first
    for phrase in [c for c in connectors if " " in c]:
        if sentence_lower.startswith(phrase):
            return True
    
    # Then check individual words
    words = sentence_lower.split()
    if words and any(words[0] == conn or words[0].startswith(conn) for conn in connectors):
        return True
        
    return False

def post_process_executive_summary(summary, doc_type=None):
    """Enhanced post-processing for executive summaries with better terminology and Filipino phrasing."""
    import re
    
    # Fix merged words (common issues from sample texts)
    word_fixes = {
        'arawmas': 'araw mas',
        'pagtulognagigising': 'pagtulog nagigising',
        'expressionslalo': 'expressions lalo',
        'pagsimangotay': 'pagsimangot ay',
        'anakparehong': 'anak—parehong',
        'patternsa': 'pattern—sa',
        'secret-monitor': 'monitor',
        'timing symptoms': 'timing ng symptoms',
        'dahil sakit': 'dahil sa sakit',
        'dahil lumalalang': 'dahil sa lumalalang',
        'Tataydati': 'Tatay dati',
        'naobserbahankong': 'naobserbahan kong',
        'ng ayon': 'ngayon',
        'ng symptoms': 'ng mga symptoms',
        'la,': 'sala,',
        'Paano monitor': 'paano i-monitor',
        'ma-document': 'i-document',
        'Samakatuwid': 'Dahil dito,',
        'may mga nutrition': 'may mga nutrition-based',
        'lugarisang': 'lugar. Isang',
        'rili': 'sarili',
        'para noia': 'paranoia',
        'unit ng ayon': 'ngunit ngayon',
        'sa an': 'saan',
        'sa kit': 'sakit'
    }
    
    # Apply all specific word fixes
    for wrong, correct in word_fixes.items():
        summary = summary.replace(wrong, correct)
    
    # Fix specific sentences with grammar issues found in previous outputs
    problematic_phrases = {
        'Higit pa rito, nag-rereklamo': 'Nag-rereklamo',
        'Bilang resulta, at nagkaroon': 'Bilang resulta, nagkaroon',
        'Dulot nito, nababawasan': 'Nababawasan',
        'si Tatay dati ay mahilig': 'si Tatay ay dating mahilig',
        'Napansin ko rin na nagbago ang social routines ni Tatay dati': 'Napansin ko rin na nagbago ang social routines ni Tatay na dati',
        'Tatay dati ay mahilig': 'Tatay ay dating mahilig',
        'kakaunti at malalaking meals': 'kakaunting at malalaking meals'
    }
    
    # Apply specific phrase fixes
    for wrong, correct in problematic_phrases.items():
        summary = re.sub(fr'\b{re.escape(wrong)}\b', correct, summary)
    
    # Fix common Filipino grammar issues
    summary = re.sub(r'\b(dahil) (sakit|lumalalang|problema)\b', r'\1 sa \2', summary)
    summary = re.sub(r'\b(timing) (symptoms|ng)\b', r'\1 ng \2', summary)
    summary = re.sub(r'ang ang', 'ang', summary)
    summary = re.sub(r'ng ng', 'ng', summary)
    summary = re.sub(r'sa sa', 'sa', summary)
    summary = re.sub(r'para ang', 'para sa', summary)
    summary = re.sub(r'upang ang', 'upang', summary)
    summary = re.sub(r'\b(dahil)\b(?!\s+(sa|ng))', r'\1 sa', summary)
    
    # Break extremely long sentences (more than 50 words)
    sentences = re.split(r'([.!?])', summary)
    processed_sentences = []
    
    for i in range(0, len(sentences), 2):
        if i+1 < len(sentences):
            sentence = sentences[i] + sentences[i+1]  # Rejoining the sentence with its punctuation
        else:
            sentence = sentences[i]
            
        words = sentence.split()
        if len(words) > 50:
            # Find a logical breaking point (after a comma or conjunction) around the middle
            middle = len(words) // 2
            break_point = middle
            
            # Look for a comma or conjunction near the middle to break at
            for j in range(middle, min(middle+10, len(words))):
                if words[j].endswith(',') or words[j] in ['at', 'pero', 'ngunit', 'dahil', 'upang']:
                    break_point = j + 1
                    break
                    
            # Create two sentences
            first_half = ' '.join(words[:break_point])
            second_half = ' '.join(words[break_point:])
            
            # Ensure the second half starts with a capital letter
            if second_half and len(second_half) > 0:
                second_half = second_half[0].upper() + second_half[1:]
                
            processed_sentences.append(first_half + '.')
            processed_sentences.append(second_half)
        else:
            processed_sentences.append(sentence)
    
    summary = ' '.join(processed_sentences)
    
    # Fix common linguistic issues in Filipino medical text
    summary = re.sub(r'inirerekomenda ko inirerekomenda ko', 'inirerekomenda ko', summary)
    summary = re.sub(r'iminumungkahi ko iminumungkahi ko', 'iminumungkahi ko', summary)
    summary = re.sub(r'inirerekomenda na inirerekomenda', 'inirerekomenda', summary)
    summary = re.sub(r'mahalagang mahalagang', 'mahalagang', summary)
    
    # Fix spacing issues
    summary = re.sub(r'\s+', ' ', summary)
    summary = re.sub(r'\s([,.;:])', r'\1', summary)
    summary = re.sub(r'([a-z])([A-Z])', r'\1 \2', summary)
    
    # Fix punctuation
    summary = re.sub(r'\.+', '.', summary)  # Multiple periods to single period
    summary = re.sub(r'\.([a-zA-Z])', r'. \1', summary)  # Ensure space after period
    summary = re.sub(r'[,;:]\s*\.', '.', summary)  # Fix ',.' sequences
    
    # Fix capitalization after periods
    summary = re.sub(r'(\.\s+)([a-z])', lambda m: f"{m.group(1)}{m.group(2).upper()}", summary)
    
    # Fix capitalization of Filipino connector words after periods
    for connector in ['bukod dito', 'gayundin', 'gayunpaman', 'subalit', 'ngunit', 'kaya naman', 
                      'isa pa', 'bilang karagdagan', 'dahil dito', 'upang', 'sa partikular',
                      'sa detalyadong', 'dahil sa', 'bukod pa', 'bilang resulta']:
        pattern = r'(\. )(' + re.escape(connector) + r')(\s+)([a-z])'
        replacement = lambda m: f"{m.group(1)}{m.group(2).capitalize()}{m.group(3)}{m.group(4)}"
        summary = re.sub(pattern, replacement, summary, flags=re.IGNORECASE)
    
    # Ensure proper capitalization at the start
    if summary and summary[0].islower():
        summary = summary[0].upper() + summary[1:]
        
    # Ensure ending with period
    if summary and not summary[-1] in ['.', '!', '?']:
        summary += '.'
    
    # Final cleanup of spaces
    summary = re.sub(r'\s{2,}', ' ', summary)
    
    return summary

def score_sentences(all_section_sentences, section_priorities, doc_type):
    """Score sentences based on content importance and section priority."""
    scored_sentences = []
    medical_terms = [
        # Critical medical terms to prioritize in summaries
        "referral", "doctor", "specialist", "physician", "konsulta",
        "emergency", "agaran", "urgent", "critical", "immediate", 
        "nutritional", "diet", "pagkain", "hydration", "dehydration",
        "monitoring", "observe", "subaybayan", "bantayan", "i-monitor",
        "medication", "gamot", "physical therapy", "exercise",
        "warning signs", "red flags", "alarming symptoms", "komplikasyon"
    ]
    
    # Score each sentence
    for sent, section_name in all_section_sentences:
        score = 0
        
        # Higher score for priority sections
        if section_name in section_priorities:
            score += 3 * (len(section_priorities) - section_priorities.index(section_name))
        
        # Score based on content features
        sent_doc = nlp(sent)
        
        # Points for important entities
        for ent in sent_doc.ents:
            if ent.label_ in ["RECOMMENDATION", "TREATMENT_METHOD", "HEALTHCARE_REFERRAL"]:
                score += 4  # Highest priority for recommendations
            elif ent.label_ in ["WARNING_SIGN", "SYMPTOM", "DISEASE"]:
                score += 3  # High priority for symptoms and warnings
            elif ent.label_ in ["DIET_RECOMMENDATION", "MONITORING"]:
                score += 3  # High priority for diet and monitoring
            elif ent.label_ in ["BODY_PART", "MEASUREMENT", "TIMEFRAME"]:
                score += 2  # Medium priority
            else:
                score += 1  # Low priority for other entities
        
        # Boost score for recommendation language
        if re.search(r'(inirerekomenda|iminungkahi|pinapayuhan|kailangan|dapat|mahalagang)', sent.lower()):
            score += 4
        
        # Boost for specific measurements or critical values
        if re.search(r'\d+\s*(?:mg|kg|cm|minuto|beses|oras|araw|°C)', sent.lower()):
            score += 2
            
        # Boost for medical terminology
        for term in medical_terms:
            if term.lower() in sent.lower():
                score += 2
                break
            
        # Boost for urgent language
        if any(term in sent.lower() for term in ["urgent", "agaran", "immediate", "kritikal"]):
            score += 3
            
        # Small boost for sentences with good length (not too short, not too long)
        words = len(sent.split())
        if 10 <= words <= 25:  # Ideal length range
            score += 1
            
        scored_sentences.append((sent, section_name, score))
    
    return scored_sentences

def replace_with_beneficiary_term(text):
    """Replace 'pasyente' with 'beneficiary' in Filipino contexts."""
    replacements = {
        'pasyente': 'beneficiary',
        'Pasyente': 'Beneficiary',
        'PASYENTE': 'BENEFICIARY',
        'ng pasyente': 'ng beneficiary',
        'sa pasyente': 'sa beneficiary',
        'para sa pasyente': 'para sa beneficiary',
        'ang pasyente': 'ang beneficiary',
        'ng Pasyente': 'ng Beneficiary',
        'sa Pasyente': 'sa Beneficiary'
    }
    
    for old, new in replacements.items():
        text = text.replace(old, new)
    
    return text

def create_simple_summary(doc, sections, doc_type="assessment"):
    """Create a simple summary as a fallback when enhanced summary generation fails."""
    # Extract main subject
    subject = extract_main_subject(doc)
    
    if doc_type.lower() == "assessment":
        if "mga_sintomas" in sections and sections["mga_sintomas"]:
            symptoms = nlp(sections["mga_sintomas"])
            symptom_entities = [ent.text for ent in symptoms.ents if ent.label_ in ["SYMPTOM", "DISEASE"]]
            if symptom_entities:
                return f"{subject} ay nagpapakita ng {symptom_entities[0]} at iba pang sintomas na nangangailangan ng pagsusuri."
            else:
                return f"{subject} ay nagpapakita ng mga sintomas na nangangailangan ng medikal na atensyon."
        else:
            return f"{subject} ay nangangailangan ng komprehensibong pagsusuri."
    else:  # Evaluation
        if "pangunahing_rekomendasyon" in sections and sections["pangunahing_rekomendasyon"]:
            recommendations = nlp(sections["pangunahing_rekomendasyon"])
            referral_entities = [ent.text for ent in recommendations.ents if ent.label_ == "HEALTHCARE_REFERRAL"]
            if referral_entities:
                return f"Inirerekomenda ang pagkonsulta sa {referral_entities[0]} para sa karagdagang pagsusuri at paggamot."
            else:
                return "Inirerekomenda ang pagkonsulta para sa karagdagang pagsusuri at paggamot."
        else:
            return "Kinakailangan ng karagdagang mga hakbang para sa optimal na pangangalaga."

def choose_context_aware_transition(prev_content, next_content, relationship):
    """Choose appropriate Filipino transitions with expanded cultural context awareness."""
    global _used_transitions
    
    # Get all possible transitions for this relationship
    if relationship == "causation":
        transitions = [
            "Dahil dito, ", "Bilang resulta, ", "Dulot nito, ", "Sa ganitong dahilan, ", "Bunga nito, ",
            "Dahil sa ganitong kondisyon, ", "Ito ang naging dahilan kung bakit ", "Sanhi nito, ",
            "Dala ng ganitong sitwasyon, ", "Sa kadahilanang ito, ", "Kung kaya't ", "Kaya naman ",
            "Dahil sa nasabing kalagayan, ", "Bilang epekto, ", "Mula rito, "
        ]
    elif relationship == "contrast":
        transitions = [
            "Sa kabilang banda, ", "Gayunpaman, ", "Subalit, ", "Ngunit, ", "Bagama't ganito, ", 
            "Samantala, ", "Sa kabila nito, ", "Kahit na ganito, ", "Datapwat, ", "Subali't, ", 
            "Sa kabila ng lahat, ", "Kahit ganoon, ", "Sa kabila ng mga ito, ", 
            "Ngunit kung titingnan mula sa ibang anggulo, ", "Magkagayunman, "
        ]
    
    # Elaboration transitions - when adding more details about the same topic
    elif relationship == "elaboration":
        transitions = [
            "Higit pa rito, ", "Partikular na, ", "Upang linawin, ", "Dagdag dito, ", 
            "Bilang karagdagan, ", "Lalo pa, ", "Para mas maunawaan, ", "Sa mas detalyadong paraan, ",
            "Upang idiin, ", "Sa karagdagang paliwanag, ", "Partikular na, ", "Halimbawa, ",
            "Bilang halimbawa, ", "Upang mas maunawaan ito, ", "Sa mga ganitong detalye, ",
            "Mahalagang banggitin na ", "Maaring magdagdag ng kaalaman na "
        ]
    
    # Action-oriented transitions - when moving to recommendations or steps
    elif relationship == "action":
        transitions = [
            "Dahil dito, ", "Samakatuwid, ", "Kaya naman, ", "Batay sa mga ito, ", 
            "Sa ganitong kadahilanan, ", "Upang matugunan ito, ", "Para masolusyonan ito, ",
            "Kailangang gawin ang sumusunod: ", "Iminumungkahi na ", "Upang maayos ito, ",
            "Para sa tamang pangangalaga, ", "Bilang nararapat na hakbang, ",
            "Ayon sa pangangailangan, ", "Batay sa nasabing kondisyon, ",
            "Para sa kabutihan ng pasyente, ", "Alinsunod sa nakitang pangangailangan, "
        ]
    
    # Context-providing transitions
    elif relationship == "context":
        transitions = [
            "Sa konteksto nito, ", "Para sa kabuuan, ", "Upang maintindihan nang lubos, ", 
            "Sa kalagayang ito, ", "Bilang bahagi ng buong sitwasyon, ", "Sa puntong ito, ",
            "Sa ganitong kalagayan, ", "Kung isasaalang-alang, ", "Kapag isinasama sa buong kwento, ",
            "Bilang bahagi ng kanyang sitwasyon, ", "Kung titingnan ang kaniyang kabuuang kalagayan, ",
            "Sa ganitong pananaw, ", "Ayon sa kanyang karanasan, ", "Sa ganitong perspektibo, "
        ]
    
    # Topic shift transitions
    elif relationship == "topical_shift":
        transitions = [
            "Tungkol sa ibang aspeto, ", "Samantala, ", "Bukod dito, ", "Sa isa pang paksa, ", 
            "Gayundin, ", "Sa kaugnay na paksa, ", "Patungkol naman sa ", "Kung pag-uusapan naman ang ",
            "Tungkol naman sa ", "Bilang karagdagan, ", "Sa ibang usapin, ", "Maliban dito, ",
            "Sa ibang bahagi ng assessment, ", "Kung titingnan naman ang ", "Dagdag pa sa mga nabanggit, "
        ]
    
    # NEW: Temporal sequence transitions 
    elif relationship == "temporal_sequence":
        transitions = [
            "Pagkatapos nito, ", "Kasunod, ", "Sumunod na, ", "Matapos ito, ", "Nang lumaon, ", 
            "Sa mga susunod na araw, ", "Kalaunan, ", "Nang magtagal, ", "Sa kasalukuyan, ", 
            "Ngayon naman, ", "Magmula noon, ", "Sa kasunod na panahon, ", "Mula noong pangyayaring iyon, ",
            "Sa mga nakalipas na linggo, ", "Sa paglipas ng panahon, ", "Kamakailan lamang, "
        ]
    
    # NEW: Assessment-to-recommendation transitions
    elif relationship == "assessment_to_recommendation":
        transitions = [
            "Batay sa assessment na ito, ", "Sa nakitang kalagayan, ", "Mula sa nasabing obserbasyon, ", 
            "Dahil sa ganitong pagtasa, ", "Alinsunod sa resulta ng assessment, ", 
            "Bilang tugon sa nakitang pangangailangan, ", "Sa pagsaalang-alang ng kondisyong ito, ",
            "Matapos suriin ang kalagayan, ", "Ayon sa evaluation, ", "Base sa nasusing pagsusuri, ",
            "Sa aming pagtatasa, ", "Dahil sa nakitang pangangailangan, "
        ]
    
    # NEW: Cultural reasoning transitions
    elif relationship == "cultural_reasoning":
        transitions = [
            "Ayon sa kaniyang kultura, ", "Batay sa kaniyang paniniwala, ", 
            "Dahil sa tradisyong kanilang sinusunod, ", "Sa kanilang nakagawian, ", 
            "Bunga ng kulturang kinagisnan, ", "Dahil sa kanilang pamahiin, ",
            "Bilang bahagi ng kanilang kultura, ", "Ayon sa nakaugaliang tradisyon, ",
            "Sa kaniyang pananampalataya, ", "Dahil sa ugaling Pilipino, "
        ]
    
    # NEW: Question-answer transitions
    elif relationship == "answer":
        transitions = [
            "Ang sagot, ", "Ayon sa pagsusuri, ", "Base sa aming nakita, ", 
            "Sa aming pagtataya, ", "Batay sa assessment, ", "Lumalabas na ", 
            "Makikita na ", "Napag-alaman na ", "Naitala na ", "Naobserbahan na "
        ]
    
    # NEW: Comparison transitions
    elif relationship == "comparison":
        transitions = [
            "Kung ihahambing, ", "Sa paghahambing, ", "Kapag kinompara, ", "Mas maayos na ", 
            "Hindi kasing-epektibo ng ", "Naiiba ito sapagkat ", "Higit na epektibo kaysa ", 
            "Kung titingnan kasabay ng ", "Hindi tulad ng dati, ", "Sa kaibahan sa dati, "
        ]
    
    # NEW: Holistic care transitions (mind-body connection)
    elif relationship == "holistic":
        transitions = [
            "Bukod sa pisikal na aspeto, ", "Kasama ng kalusugang pangkatawan, ", 
            "Hindi lamang pisikal kundi pati na rin emosyonal, ", 
            "Kaakibat ng mga sintomas na ito, ", "Kasabay ng pangangatawan, ",
            "Bahagi rin ng kabuuang kalusugan ang ", "Sa psychological na aspeto naman, ",
            "Kasama sa holistic na pagtingin, ", "Hindi lamang sa pisikal na kalusugan, "
        ]
    
    # NEW: Implementation transitions
    elif relationship == "implementation":
        transitions = [
            "Sa praktikal na aspeto, ", "Para maisakatuparan ito, ", "Sa araw-araw na pamumuhay, ", 
            "Para maisagawa ito, ", "Sa aktwal na pagsasabuhay, ", "Para maipatupad ang mga rekomendasyon, ",
            "Upang magkaroon ng pagbabago, ", "Sa pagsasagawa nito, ", "Para maipatupad ang plano, "
        ]
    
    # Addition transitions (default with expanded Filipino expressions)
    else:
        transitions = [
            "Bukod dito, ", "Gayundin, ", "Dagdag pa rito, ", "Karagdagan dito, ", 
            "Kasabay nito, ", "Kasama rito, ", "Kaakibat nito, ", "Pati na rin, ", 
            "Hindi lamang iyon, ", "Maliban dito, ", "Isa pa, ", "Bilang dagdag, ", 
            "Katulad din nito, ", "Karugtong nito, ", "Bukod pa sa nabanggit, ", 
            "Sa kabilang dako, ", "At higit pa rito, "
        ]
    
    # Avoid recently used transitions - prefer unused ones
    preferred_transitions = [t for t in transitions if t not in _used_transitions]
    if preferred_transitions:
        transition_options = preferred_transitions
    else:
        transition_options = transitions
    
    # Find transitions that don't appear in the content
    for transition in transition_options:
        if transition.lower() not in prev_content.lower() and transition.lower() not in next_content.lower():
            # Track this transition to avoid overuse
            _used_transitions.add(transition)
            # Limit tracked transitions to prevent memory growth
            if len(_used_transitions) > 7:  # Keep track of last 7 transitions
                _used_transitions.pop()  # Remove an arbitrary transition
            return transition
    
    # If all transitions appear in content or were recently used, pick one that's least problematic
    return transitions[0]  # Default to first transition as fallback



def choose_appropriate_transition(prev_content, next_content, section_relationship=None):
    """Choose the most appropriate transition phrase based on content relationship."""
    # Get relationship if not provided
    relationship = section_relationship or get_semantic_relationship(prev_content, next_content)
    
    # Causation transitions
    if relationship == "causation":
        transitions = ["Dahil dito, ", "Bilang resulta, ", "Dulot nito, ", 
                       "Sa ganitong dahilan, ", "Bunga nito, "]
    
    # Contrast transitions
    elif relationship == "contrast":
        transitions = ["Sa kabilang banda, ", "Gayunpaman, ", "Subalit, ", 
                       "Ngunit, ", "Bagama't ganito, ", "Samantala, "]
    
    # Addition transitions (default)
    else:
        transitions = ["Bukod dito, ", "Gayundin, ", "Dagdag pa rito, ", 
                       "Karagdagan dito, ", "Kasabay nito, ", "Kasama rito, "]
    
    # Return a randomly selected transition from the appropriate category
    import random
    return random.choice(transitions)

def post_process_summary(summary):
    """Apply comprehensive post-processing to ensure high-quality section summaries."""
    import re
    
    # Fix spacing issues
    summary = re.sub(r'\s+', ' ', summary)
    summary = re.sub(r'\s([,.;:])', r'\1', summary)
    
    # Fix specific Filipino grammar issues
    summary = re.sub(r'patternsa ', 'pattern—sa ', summary)  # Fix merged words
    summary = re.sub(r'secret-monitor', 'monitor', summary)  # Fix incorrect term
    
    # Fix common spacing errors
    summary = re.sub(r'(\w+)([,.;:])(\w+)', r'\1\2 \3', summary)  # Add space after punctuation if missing
    
    # Fix period spacing and double periods
    summary = re.sub(r'\.+', '.', summary)  # Multiple periods to single period
    summary = re.sub(r'\.([a-zA-Z])', r'. \1', summary)  # Ensure space after period
    
    # Fix parentheses spacing
    summary = re.sub(r'\(\s+', '(', summary)  # Remove space after opening parenthesis
    summary = re.sub(r'\s+\)', ')', summary)  # Remove space before closing parenthesis
    
    # Fix incorrect word combinations
    summary = re.sub(r'timing symptoms', 'timing ng symptoms', summary)
    
    # Fix common errors in Filipino medical text
    summary = re.sub(r'ang ang', 'ang', summary)
    summary = re.sub(r'ng ng', 'ng', summary)
    summary = re.sub(r'sa sa', 'sa', summary)
    summary = re.sub(r'para ang', 'para sa', summary)
    summary = re.sub(r'upang ang', 'upang', summary)
    summary = re.sub(r'upang ([a-zA-Z]+) sa', r'upang \1', summary)  # Fix upang Para sa pattern
    
    # Fix missing articles/linkers 
    summary = re.sub(r'timing (symptoms|ng)', 'timing ng', summary)
    
    # Fix capitalization after periods
    summary = re.sub(r'(\. )([a-z])', lambda m: f"{m.group(1)}{m.group(2).upper()}", summary)
    
    # Ensure proper capitalization at start
    if summary and summary[0].islower():
        summary = summary[0].upper() + summary[1:]
        
    # Ensure ending with period
    if summary and not summary[-1] in ['.', '!', '?']:
        summary += '.'
    
    # Fix incorrect punctuation sequences
    summary = re.sub(r'[,;:]\s*\.', '.', summary)
    
    return summary