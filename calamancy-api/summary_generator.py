import re
import random
from typing import Dict, List, Tuple, Any, Optional, Set
from nlp_loader import nlp
from entity_extractor import extract_main_subject, extract_structured_elements
from text_processor import split_into_sentences, enhance_measurement_references
from context_analyzer import analyze_document_context, extract_measurement_context, identify_cross_section_entities, get_relevant_entities_for_section, get_semantic_relationship, get_contextual_relationship, determine_optimal_section_order

# Track used transitions to avoid repetition
_used_transitions: Set[str] = set()

def create_multi_section_summary(doc, sections, doc_type="assessment", max_sentences=8):
    """Create a concise multi-section executive summary with improved extraction and clustering."""
    print(f"Generating extractive summary for {doc_type} document...")
    
    if not sections:
        return {
            "summary": "Walang sapat na impormasyon para sa buod.",
            "sections": {}
        }
        
    # Generate extractive summary with improved clustering and sentence selection
    extractive_summary = generate_extractive_summary(sections, doc_type, max_sentences)
    
    # Return the structured result (summary text and sections)
    return {
        "summary": extractive_summary["text"],
        "sections": extractive_summary["sections"]
    }

def generate_extractive_summary(sections, doc_type, max_sentences=8):
    """Generate extractive summary by selecting important sentences from each section with improved clustering."""
    print(f"Processing {len(sections)} sentences across all sections")
    
    # Import necessary functions
    from context_analyzer import analyze_document_context
    from entity_extractor import extract_structured_elements

    # Process and clean input text to create full document for context
    all_text = " ".join([text for text in sections.values() if text])
    doc = nlp(all_text)
    
    # Analyze document context for better sentence selection
    doc_context = analyze_document_context(sections, doc_type)
    
    # Define priority order for sections based on document type
    section_priorities = {
        "assessment": ["mga_sintomas", "kalagayan_pangkatawan", "aktibidad", "kalagayan_mental", "kalagayan_social"],
        "evaluation": ["pangunahing_rekomendasyon", "pagbabago_sa_pamumuhay", "pangangalaga", "mga_hakbang"]
    }.get(doc_type.lower(), [])
    
    # Extract structured elements from each section for better context
    section_elements = {}
    for section_name, section_text in sections.items():
        section_elements[section_name] = extract_structured_elements(section_text, section_name)
    
    # Process sentences from all sections
    all_section_sentences = []
    for section_name, section_text in sections.items():
        if not section_text:
            continue
            
        # Weight based on section priority
        priority_weight = 1.0
        if section_name in section_priorities:
            priority_index = section_priorities.index(section_name)
            priority_weight = 1.5 - (priority_index * 0.1)  # Higher weight for higher priority
            
        sentences = split_into_sentences(section_text)
        for sent in sentences:
            if len(sent) < 10:  # Skip very short sentences
                continue
            all_section_sentences.append((sent, section_name, 0))  # Initial score is 0
    
    # Score sentences with improved criteria
    scored_sentences = score_sentences(all_section_sentences, section_priorities, doc_type)
    
    # Group related sentences using clustering for better coherence
    sentence_groups = group_related_sentences(scored_sentences, doc, doc_type)
    
    # Select most important sentences from each group with a max limit
    selected_sentences = []
    selected_section_map = {}
    
    # Get highest-scoring sentence from each group, up to max_sentences/2
    # to reserve space for section-specific important sentences
    groups_to_use = min(len(sentence_groups), max(1, max_sentences // 2))
    
    for group in sentence_groups[:groups_to_use]:
        if not group:
            continue
            
        # Get highest scoring sentence from group
        best_sentence = max(group, key=lambda x: x[2])
        selected_sentences.append(best_sentence)
        
        section_name = best_sentence[1]
        if section_name not in selected_section_map:
            selected_section_map[section_name] = []
        selected_section_map[section_name].append(best_sentence[0])
    
    # Fill remaining slots with high-scoring sentences from priority sections
    # that haven't been selected yet
    remaining_slots = max_sentences - len(selected_sentences)
    
    if remaining_slots > 0:
        # Get sentences from priority sections that aren't already selected
        remaining_sentences = []
        for sent, section, score in scored_sentences:
            if not any(s[0] == sent for s in selected_sentences):
                remaining_sentences.append((sent, section, score))
        
        # Sort by score and add up to remaining slots
        remaining_sentences.sort(key=lambda x: x[2], reverse=True)
        for sent, section, score in remaining_sentences[:remaining_slots]:
            selected_sentences.append((sent, section, score))
            if section not in selected_section_map:
                selected_section_map[section] = []
            selected_section_map[section].append(sent)
    
    # Sort selected sentences to maintain original document order
    # This creates a more coherent summary
    selected_sentences.sort(key=lambda x: all_section_sentences.index((x[0], x[1], 0)))
    
    # Build final summary text
    summary_text = ""
    for sent, _, _ in selected_sentences:
        if summary_text and not has_connector(sent):
            connector = select_appropriate_connector(summary_text, sent)
            summary_text += " " + connector + sent
        else:
            # First sentence or has connector
            if summary_text:
                summary_text += " " + sent
            else:
                summary_text = sent
    
    # Do post-processing for readability
    summary_text = post_process_summary(summary_text)
    
    return {
        "text": summary_text,
        "sections": selected_section_map
    }

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
        # Merged words
        'arawmas': 'araw mas',
        'pagtulognagigising': 'pagtulog nagigising',
        'expressionslalo': 'expressions lalo',
        'pagsimangotay': 'pagsimangot ay',
        'anakparehong': 'anak—parehong',
        'patternsa': 'pattern sa',
        'lugarisang': 'lugar. Isang',
        'naobserbahankong': 'naobserbahan kong',
        'hindimakontrol': 'hindi makontrol',
        'nagiisang': 'nag-iisang',
        'hindigumagana': 'hindi gumagana',
        'hindisiya': 'hindi siya',
        'hindinaliligo': 'hindi naliligo',
        'hindikakain': 'hindi kakain',
        'hindimabilis': 'hindi mabilis',
        'hindimakausap': 'hindi makausap',
        'nakaririndi': 'nakaririnig',
        'pagkakasakit': 'pagkakasakit',
        'hindimagandang': 'hindi magandang',
        'akoayparating': 'ako ay parating',
        'mayroongpabalikbalik': 'mayroong pabalik-balik',
        'laginakakalimutan': 'lagi nakakalimutan',
        
        # Incorrect spacing/hyphenation
        'secret-monitor': 'monitor',
        'self-care': 'self care',
        'ma-document': 'i-document',
        'ma-monitor': 'i-monitor',
        'Paano monitor': 'paano i-monitor',
        'i monitor': 'i-monitor',
        'I monitor': 'I-monitor',
        'self assess': 'self-assess',
        'pang araw araw': 'pang-araw-araw',
        'araw araw': 'araw-araw',
        'tuloy tuloy': 'tuloy-tuloy',
        'regular check up': 'regular check-up',
        'follow up': 'follow-up',
        'check up': 'check-up',
        'post operative': 'post-operative',
        'pre operative': 'pre-operative',
        
        # Missing prepositions
        'dahil sakit': 'dahil sa sakit',
        'dahil lumalalang': 'dahil sa lumalalang',
        'dahil problema': 'dahil sa problema',
        'timing symptoms': 'timing ng symptoms',
        'dahil pagod': 'dahil sa pagod',
        'dahil stress': 'dahil sa stress',
        'para treatment': 'para sa treatment',
        'para assessment': 'para sa assessment',
        'para referral': 'para sa referral',
        'para therapy': 'para sa therapy',
        'para pag inom': 'para sa pag-inom',
        'para pagkain': 'para sa pagkain',
        
        # Person references
        'Tataydati': 'Tatay dati',
        'Nanayko': 'Nanay ko',
        'Tatayko': 'Tatay ko',
        'Ating patient': 'Ating beneficiary',
        'Ang patient': 'Ang beneficiary',
        'Sa patient': 'Sa beneficiary',
        'Si patient': 'Si beneficiary',
        'Para sa patient': 'Para sa beneficiary',
        
        # Articles and determiners
        'ng symptoms': 'ng mga symptoms',
        'ng gamot': 'ng mga gamot',
        'ng beses': 'ng mga beses',
        'ng araw': 'ng mga araw',
        'ng hakbang': 'ng mga hakbang',
        'sa symptoms': 'sa mga symptoms',
        'sa gamot': 'sa mga gamot',
        'may symptoms': 'may mga symptoms',
        'may gamot': 'may mga gamot',
        'may nutrition': 'may mga nutrition-based',
        
        # Grammar/spelling fixes
        'ng ayon': 'ngayon',
        'la,': 'sala,',
        'Samakatuwid': 'Dahil dito,',
        'rili': 'sarili',
        'para noia': 'paranoia',
        'unit ng ayon': 'ngunit ngayon',
        'sa an': 'saan',
        'sa kit': 'sakit',
        'sa fety': 'safety',
        'sa rili': 'sarili',
        'isa-i': 'isa-isa',
        'para sa rili': 'para sa sarili',
        'kanyanger': 'kanyang anger',
        'nagsusul': 'nagsusulat',
        'sa bay-sa bay': 'sabay-sabay',
        'lapig': 'sa lapig',
        'tulungan problema': 'tulungan sa problema',
        'higit napaka': 'higit na napaka',
        'pag ka': 'pagka',
        'pang araw': 'pang-araw',
        'di makatulog': 'hindi makatulog',
        'di kumakain': 'hindi kumakain',
        'di regular': 'hindi regular',
        'di nagbabago': 'hindi nagbabago',
        'walangpagbabago': 'walang pagbabago',
        'healthcommunity': 'health community',
        'healthprovider': 'health provider',
        'dietaryneeds': 'dietary needs',
        'medicationschedule': 'medication schedule',
        'mentalstate': 'mental state',
        'discomfortlevel': 'discomfort level',
        'socialneeds': 'social needs',
        
        # Medical terms
        'bloodpressure': 'blood pressure',
        'heartrate': 'heart rate',
        'bodytemp': 'body temperature',
        'glucoselevel': 'glucose level',
        'hyper tension': 'hypertension',
        'insom nia': 'insomnia',
        'demen tia': 'dementia',
        'alz heimer': 'alzheimer',
        'depre ssion': 'depression',
        
        # Common contractions and abbreviations
        'accdg': 'ayon',
        'asap': 'kaagad',
        'atbp': 'at iba pa',
        'wla': 'wala',
        'mron': 'mayroon',
        'pra': 'para',
        'dhl': 'dahil',
        'dpat': 'dapat',
        'sknya': 'sa kanya',
        'lng': 'lang',
        'mgdamag': 'magdamag',
        'sitti': 'sitting',
        
        # Mixed language issues
        'nag-eexercise': 'nag-eexercise',
        'nag-aalaga': 'nag-aalaga',
        'nagtake': 'nag-take',
        'nagcheckup': 'nag-checkup',
        'nagfollow': 'nag-follow',
        'icheck': 'i-check',
        'imonitor': 'i-monitor',
        'idocument': 'i-document',
        'irefer': 'i-refer',
        'iensure': 'i-ensure',
        'ifollow': 'i-follow',
        
        # Function words
        'mgat': 'mga at',
        'atatbp': 'at at iba pa',
        'npra': 'na para',
        'dats': 'at sa',
        'atyung': 'at yung',
        'peroang': 'pero ang',
        'atng': 'at ng',
        'sapag': 'sa pag',
        'sa pat': 'sapat',
        'nasi': 'na si'
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
    """Score sentences based on multiple factors for better extraction."""
    scored_sentences = []
    
    for sent, section, _ in all_section_sentences:
        score = 0.0
        
        # 1. Section priority (0-5 points)
        if section in section_priorities:
            priority_idx = section_priorities.index(section)
            score += max(0, 5 - priority_idx)  # Higher score for higher priority
        
        # 2. Length penalty (-3 to 0 points)
        # Penalize very long sentences but don't excessively penalize medium-length ones
        words = len(sent.split())
        if words > 45:
            score -= 3
        elif words > 35:
            score -= 2
        elif words > 30:
            score -= 1
        
        # 3. Information density (0-4 points)
        sent_doc = nlp(sent)
        
        # Count entities, numbers, and medical terms
        info_elements = 0
        for ent in sent_doc.ents:
            info_elements += 1
        
        # Count numerical information
        if any(char.isdigit() for char in sent):
            info_elements += 1
        
        # Add score based on information density
        score += min(4, info_elements * 0.5)
        
        # 4. Position bonus (0-2 points)
        # First sentences in sections often contain important information
        section_sentences = [s for s, sect, _ in all_section_sentences if sect == section]
        if section_sentences and section_sentences[0] == sent:
            score += 2
        
        # 5. Key term presence (0-4 points)
        key_terms = {
            "assessment": ["sintomas", "kondisyon", "sakit", "problema", "nahihirapan", 
                          "hindi", "limitado", "restricted", "hirap"],
            "evaluation": ["rekomendasyon", "inirerekomenda", "dapat", "kailangan", 
                          "continuation", "tuloy", "bawas", "dagdag", "pagbabago"]
        }
        
        term_count = 0
        for term in key_terms.get(doc_type.lower(), []):
            if term.lower() in sent.lower():
                term_count += 1
        
        score += min(4, term_count)
        
        # Add to scored list
        scored_sentences.append((sent, section, score))
    
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

def post_process_summary(summary):
    """Apply comprehensive post-processing to ensure high-quality section summaries."""
    import re
    
    # Fix spacing issues
    summary = re.sub(r'\s+', ' ', summary)
    summary = re.sub(r'\s([,.;:])', r'\1', summary)
    
    # Fix specific Filipino grammar issues
    summary = re.sub(r'patternsa ', 'pattern sa ', summary)  # Fix merged words
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

    # Fix common split/merged Tagalog and English word errors
    word_fixes = {
        # Merged words
        'arawmas': 'araw mas',
        'pagtulognagigising': 'pagtulog nagigising',
        'expressionslalo': 'expressions lalo',
        'pagsimangotay': 'pagsimangot ay',
        'anakparehong': 'anak—parehong',
        'patternsa': 'pattern sa',
        'lugarisang': 'lugar. Isang',
        'naobserbahankong': 'naobserbahan kong',
        'hindimakontrol': 'hindi makontrol',
        'nagiisang': 'nag-iisang',
        'hindigumagana': 'hindi gumagana',
        'hindisiya': 'hindi siya',
        'hindinaliligo': 'hindi naliligo',
        'hindikakain': 'hindi kakain',
        'hindimabilis': 'hindi mabilis',
        'hindimakausap': 'hindi makausap',
        'nakaririndi': 'nakaririnig',
        'pagkakasakit': 'pagkakasakit',
        'hindimagandang': 'hindi magandang',
        'akoayparating': 'ako ay parating',
        'mayroongpabalikbalik': 'mayroong pabalik-balik',
        'laginakakalimutan': 'lagi nakakalimutan',
        
        # Incorrect spacing/hyphenation
        'secret-monitor': 'monitor',
        'self-care': 'self care',
        'ma-document': 'i-document',
        'ma-monitor': 'i-monitor',
        'Paano monitor': 'paano i-monitor',
        'i monitor': 'i-monitor',
        'I monitor': 'I-monitor',
        'self assess': 'self-assess',
        'pang araw araw': 'pang-araw-araw',
        'araw araw': 'araw-araw',
        'tuloy tuloy': 'tuloy-tuloy',
        'regular check up': 'regular check-up',
        'follow up': 'follow-up',
        'check up': 'check-up',
        'post operative': 'post-operative',
        'pre operative': 'pre-operative',
        
        # Missing prepositions
        'dahil sakit': 'dahil sa sakit',
        'dahil lumalalang': 'dahil sa lumalalang',
        'dahil problema': 'dahil sa problema',
        'timing symptoms': 'timing ng symptoms',
        'dahil pagod': 'dahil sa pagod',
        'dahil stress': 'dahil sa stress',
        'para treatment': 'para sa treatment',
        'para assessment': 'para sa assessment',
        'para referral': 'para sa referral',
        'para therapy': 'para sa therapy',
        'para pag inom': 'para sa pag-inom',
        'para pagkain': 'para sa pagkain',
        
        # Person references
        'Tataydati': 'Tatay dati',
        'Nanayko': 'Nanay ko',
        'Tatayko': 'Tatay ko',
        'Ating patient': 'Ating beneficiary',
        'Ang patient': 'Ang beneficiary',
        'Sa patient': 'Sa beneficiary',
        'Si patient': 'Si beneficiary',
        'Para sa patient': 'Para sa beneficiary',
        
        # Articles and determiners
        'ng symptoms': 'ng mga symptoms',
        'ng gamot': 'ng mga gamot',
        'ng beses': 'ng mga beses',
        'ng araw': 'ng mga araw',
        'ng hakbang': 'ng mga hakbang',
        'sa symptoms': 'sa mga symptoms',
        'sa gamot': 'sa mga gamot',
        'may symptoms': 'may mga symptoms',
        'may gamot': 'may mga gamot',
        'may nutrition': 'may mga nutrition-based',
        
        # Grammar/spelling fixes
        'ng ayon': 'ngayon',
        'la,': 'sala,',
        'Samakatuwid': 'Dahil dito,',
        'rili': 'sarili',
        'para noia': 'paranoia',
        'unit ng ayon': 'ngunit ngayon',
        'sa an': 'saan',
        'sa kit': 'sakit',
        'sa fety': 'safety',
        'sa rili': 'sarili',
        'isa-i': 'isa-isa',
        'para sa rili': 'para sa sarili',
        'kanyanger': 'kanyang anger',
        'nagsusul': 'nagsusulat',
        'sa bay-sa bay': 'sabay-sabay',
        'lapig': 'sa lapig',
        'tulungan problema': 'tulungan sa problema',
        'higit napaka': 'higit na napaka',
        'pag ka': 'pagka',
        'pang araw': 'pang-araw',
        'di makatulog': 'hindi makatulog',
        'di kumakain': 'hindi kumakain',
        'di regular': 'hindi regular',
        'di nagbabago': 'hindi nagbabago',
        'walangpagbabago': 'walang pagbabago',
        'healthcommunity': 'health community',
        'healthprovider': 'health provider',
        'dietaryneeds': 'dietary needs',
        'medicationschedule': 'medication schedule',
        'mentalstate': 'mental state',
        'discomfortlevel': 'discomfort level',
        'socialneeds': 'social needs',
        
        # Medical terms
        'bloodpressure': 'blood pressure',
        'heartrate': 'heart rate',
        'bodytemp': 'body temperature',
        'glucoselevel': 'glucose level',
        'hyper tension': 'hypertension',
        'insom nia': 'insomnia',
        'demen tia': 'dementia',
        'alz heimer': 'alzheimer',
        'depre ssion': 'depression',
        
        # Common contractions and abbreviations
        'accdg': 'ayon',
        'asap': 'kaagad',
        'atbp': 'at iba pa',
        'wla': 'wala',
        'mron': 'mayroon',
        'pra': 'para',
        'dhl': 'dahil',
        'dpat': 'dapat',
        'sknya': 'sa kanya',
        'lng': 'lang',
        'mgdamag': 'magdamag',
        'sitti': 'sitting',
        
        # Mixed language issues
        'nag-eexercise': 'nag-eexercise',
        'nag-aalaga': 'nag-aalaga',
        'nagtake': 'nag-take',
        'nagcheckup': 'nag-checkup',
        'nagfollow': 'nag-follow',
        'icheck': 'i-check',
        'imonitor': 'i-monitor',
        'idocument': 'i-document',
        'irefer': 'i-refer',
        'iensure': 'i-ensure',
        'ifollow': 'i-follow',
        
        # Function words
        'mgat': 'mga at',
        'atatbp': 'at at iba pa',
        'npra': 'na para',
        'dats': 'at sa',
        'atyung': 'at yung',
        'peroang': 'pero ang',
        'atng': 'at ng',
        'sapag': 'sa pag',
        'sa pat': 'sapat',
        'nasi': 'na si'
    }

    for wrong, correct in word_fixes.items():
        summary = summary.replace(wrong, correct)
    
    return summary