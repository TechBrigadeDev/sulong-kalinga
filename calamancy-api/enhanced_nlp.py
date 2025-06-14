import re  # Add this import at the top
import spacy
from collections import defaultdict
from typing import List, Dict, Any, Set, Tuple
from tagalog_medical_terms import (
    BODY_PARTS, MEDICAL_CONDITIONS, MOBILITY_TERMS,
    COGNITIVE_EMOTIONAL_TERMS, SPEECH_TERMS,
    ASSESSMENT_PHRASES, EVALUATION_PHRASES
)

class TagalogMedicalKG:
    """Enhanced knowledge graph with morphological awareness for Tagalog medical terms"""
    
    def __init__(self, nlp):
        self.nlp = nlp
        # Core symptom concepts with related word forms
        self.symptom_concepts = {
            "pain": {
                "lemmas": ["sakit", "kirot", "hapdi"],
                "forms": ["masakit", "sumasakit", "kirot", "kumulirot", "kumikirot", 
                         "hapdi", "mahapdi", "humahapdi"]
            },
            "weakness": {
                "lemmas": ["hina", "dala"],
                "forms": ["mahina", "nanghihina", "humihina", "nanghihina", "nanlulumo"]
            },
            "dizziness": {
                "lemmas": ["hilo", "lula"],
                "forms": ["nahilo", "nahihilo", "malula", "nalulula"]
            },
            "visual_problem": {
                "lemmas": ["malabo", "paningin"],
                "forms": ["malabo", "bulag", "hindi makakita", "nagdilim"]
            },
            "hearing_problem": {
                "lemmas": ["bingi", "pandinig"],
                "forms": ["bingi", "hindi makarinig", "mahirap makarinig"]
            },
            "mobility_issue": {
                "lemmas": ["lakad", "kilos"],
                "forms": ["hirap lumakad", "hindi makalakad", "nahihirapang gumalaw"]
            },
            "tremor": {
                "lemmas": ["nangangatal", "panginginig"],
                "forms": ["nangangatal", "nanginginig", "panginginig"]
            },
            "balance_problem": {
                "lemmas": ["balanse", "pagbagsak"],
                "forms": ["pagbagsak", "pabagsak", "bagsak", "natutumba"]
            },
            "nausea": {
                "lemmas": ["duwal"],
                "forms": ["naduduwal", "nasusuka", "pagsusuka"]
            },
            "fatigue": {
                "lemmas": ["pagod", "kapoy"],
                "forms": ["pagod", "napapagod", "pagkapagod", "kapoy"]
            }
        }
        
        # Extended body part relations specific to Tagalog texts from our examples
        self.body_relations = {
            "mata": ["paningin", "bulag", "malabo"],
            "tenga": ["pandinig", "bingi", "malalim"],
            "balakang": ["sakit", "kirot", "masakit"],
            "binti": ["sakit", "kirot", "mahina", "masakit"],
            "tuhod": ["sakit", "kirot", "masakit"],
            "kamay": ["panginginig", "namamanhid", "mahina", "nangangatal"],
            "ulo": ["sakit", "masakit", "sumasakit", "kirot"],
            "balikat": ["sakit", "kirot", "masakit"], # From example about "kanang balikat"
            "kuko": ["mahahaba", "madumi"], # From example about "kuko niya ay mahahaba at madumi"
            "katawan": ["mabigat", "nanghihina"] # From "mabigat ang kanyang katawan at nanghihina"
        }

    def analyze(self, doc):
        """Analyze document for medical concepts with morphological awareness"""
        results = {
            "symptoms": [],
            "body_parts": [],
            "relations": [],
            "needs": []
        }
        
        # Special needs and social patterns from our examples
        need_markers = ["kailangan", "pera", "gatas", "tinapay", "wala", "pension"]
        social_markers = ["anak", "pamangkin", "apo"]
        
        # Track mentions
        for sent in doc.sents:
            sent_text = sent.text.lower()
            
            # Check for needs
            for need in need_markers:
                if need in sent_text:
                    results["needs"].append({
                        "need": need,
                        "sentence": sent.text
                    })
            
            # Look for symptom concepts
            for symptom, data in self.symptom_concepts.items():
                for token in sent:
                    token_text = token.text.lower()
                    
                    # Check word forms or lemma
                    if token_text in data["forms"] or token.lemma_ in data["lemmas"]:
                        # Found symptom
                        results["symptoms"].append({
                            "type": symptom,
                            "term": token.text,
                            "sentence": sent.text
                        })
                        
                        # Check nearby tokens for body parts
                        for other in sent:
                            other_text = other.text.lower()
                            
                            # Look for body part mentions using our comprehensive list
                            body_part_found = False
                            matching_body_part = None
                            
                            for body_part, variants in BODY_PARTS.items():
                                if other_text in variants or other_text == body_part:
                                    body_part_found = True
                                    matching_body_part = body_part
                                    break
                            
                            if body_part_found:
                                results["body_parts"].append(matching_body_part)
                                # Create relation
                                results["relations"].append({
                                    "symptom": symptom,
                                    "body_part": matching_body_part,
                                    "sentence": sent.text
                                })
        
        # Remove duplicates while maintaining order
        results["body_parts"] = list(dict.fromkeys(results["body_parts"]))
        
        return results

def analyze_symptoms_with_morphology(doc):
    """
    Use morphological analysis to better understand Tagalog symptom descriptions
    
    Args:
        doc: spaCy Doc object processed with Tagalog model
        
    Returns:
        List of identified symptoms with details
    """
    # Root words with common affixes for symptoms in Tagalog
    symptom_markers = {
        # Root words that indicate symptoms when combined with certain affixes
        "sakit": ["masakit", "sumasakit", "nasaktan", "masaktan", "sumasakit"],
        "kirot": ["kumulirot", "kumirot", "kumikirot", "kinirot"],
        "hilo": ["nahilo", "hilo", "nahihilo", "hinihilo"],
        "duwal": ["naduwal", "naduduwal", "dumaduwal", "duwal"],
        "hina": ["nanghihina", "humihina", "mahina", "hinihina"],
        "hirap": ["nahihirapan", "mahirap", "hirap", "nahihirapang"],
        # Additional markers observed in example texts
        "bagsak": ["pabagsak", "pagbagsak", "bumagsak", "mahulog"],
        "kain": ["kumakain", "hindi kumakain", "hindi siya masyadong kumakain"],
        "timbang": ["ibinaba", "pagbaba", "timbang", "pagbabawas ng timbang"],
        "ngatal": ["nangangatal", "panginginig", "nanginginig"],
        "tumba": ["natumba", "pagkatumba", "natutumba"],
        "lakad": ["lumakad", "naglalakad", "paglalakad", "makalakad", "makapaglakad"]
    }
    
    # Extended list of intensity markers based on sample texts
    intensity_markers = [
        "sobra", "grabe", "masyado", "napaka", "lubha", "talagang",  # Severe
        "medyo", "bahagya", "konti",  # Mild
        "palagi", "lagi", "parati", "madalas"  # Frequent
    ]
    
    symptoms = []
    
    for sent in doc.sents:
        sent_text = sent.text.lower()
        
        # Look for symptom words
        for token in sent:
            token_text = token.text.lower()
            token_lemma = token.lemma_.lower()
            
            # Check if this is a symptom marker
            is_symptom = False
            symptom_type = None
            
            for symptom_root, variants in symptom_markers.items():
                if token_text in variants or token_lemma == symptom_root:
                    is_symptom = True
                    symptom_type = symptom_root
                    break
            
            # Check for compound symptom expressions (e.g., "hirap lumakad")
            if not is_symptom:
                for i in range(len(sent) - 1):
                    bigram = (sent[i].text + " " + sent[i+1].text).lower()
                    for symptom_root, variants in symptom_markers.items():
                        if any(variant in bigram for variant in variants):
                            is_symptom = True
                            symptom_type = symptom_root
                            break
            
            if is_symptom:
                # Check for intensity modifiers
                intensity = "moderate"  # default
                for i_token in sent:
                    i_text = i_token.text.lower()
                    if i_text in intensity_markers:
                        if i_text in ["sobra", "grabe", "napaka", "lubha", "talagang"]:
                            intensity = "severe"
                        elif i_text in ["medyo", "bahagya", "konti"]:
                            intensity = "mild"
                        elif i_text in ["palagi", "lagi", "parati", "madalas"]:
                            intensity = "frequent"
                
                # Look for associated body part
                body_part = None
                for bp_token in sent:
                    bp_text = bp_token.text.lower()
                    for part_name, variants in BODY_PARTS.items():
                        if bp_text in variants or bp_text == part_name:
                            body_part = part_name
                            break
                
                # In example texts, "katawan" is mentioned as feeling heavy
                if symptom_type == "hina" and "katawan" in sent_text and "mabigat" in sent_text:
                    body_part = "katawan"
                
                # Extract context span (up to 5 tokens around symptom)
                token_idx = token.i
                start_idx = max(0, token_idx - 5)
                end_idx = min(len(doc), token_idx + 6)
                context = doc[start_idx:end_idx].text
                
                symptoms.append({
                    "symptom": symptom_type,
                    "intensity": intensity,
                    "body_part": body_part,
                    "context": context,
                    "text": sent.text
                })
    
    return symptoms

def extract_key_relations(doc):
    """
    Extract key subject-verb-object relations using dependency parser
    
    Args:
        doc: spaCy Doc object processed with Tagalog model
        
    Returns:
        Tuple of (summary sentences, key relations)
    """
    key_relations = []
    
    # Track sentences we've already processed
    processed_sentences = set()
    
    for sent in doc.sents:
        # Skip if already processed
        if sent.text in processed_sentences:
            continue
            
        # Find main verb of the sentence
        main_verb = None
        for token in sent:
            if token.pos_ == "VERB" and token.dep_ in ["ROOT", "ccomp"]:
                main_verb = token
                break
        
        if not main_verb:
            continue
            
        # Find subject and object
        subject = None
        obj = None
        
        # Collect all children of the main verb
        verb_children = list(main_verb.children)
        
        # First look for subject
        for child in verb_children:
            if child.dep_ in ["nsubj", "nsubjpass"]:
                # Expand to include the full noun phrase
                subject_span = extract_noun_phrase(child)
                subject = subject_span
                break
        
        # Then look for object
        for child in verb_children:
            if child.dep_ in ["dobj", "obj", "iobj", "pobj"]:
                # Expand to include the full noun phrase
                object_span = extract_noun_phrase(child)
                obj = object_span
                break
        
        # Special case for Tagalog sentences where object might be in a different position
        if not obj:
            for token in sent:
                if token.dep_ in ["dobj", "obj", "iobj", "pobj"] and token.head == main_verb:
                    object_span = extract_noun_phrase(token)
                    obj = object_span
                    break
        
        # Construct a simplified relation
        if subject:
            # Get span text
            subject_text = subject.text if hasattr(subject, "text") else subject
            verb_text = main_verb.text
            object_text = obj.text if obj and hasattr(obj, "text") else (obj or "")
            
            # Add relation data
            relation = {
                "subject": subject_text,
                "verb": verb_text,
                "object": object_text,
                "sentence": sent.text
            }
            
            key_relations.append(relation)
            processed_sentences.add(sent.text)
    
    # Filter relations to focus on key medical information
    filtered_relations = filter_medical_relations(key_relations)
    
    # Use these relations to generate a concise summary, prioritizing medical information
    summary_sentences = []
    taken_sentences = set()
    
    # First add sentences with medical terms
    for relation in filtered_relations:
        if relation["sentence"] not in taken_sentences:
            summary_form = construct_summary_sentence(relation)
            if summary_form:
                summary_sentences.append(summary_form)
                taken_sentences.add(relation["sentence"])
    
    # Then add other important relations if we haven't reached our limit
    if len(summary_sentences) < 3:
        for relation in key_relations:
            if relation["sentence"] not in taken_sentences:
                summary_form = construct_summary_sentence(relation)
                if summary_form and len(summary_sentences) < 3:
                    summary_sentences.append(summary_form)
                    taken_sentences.add(relation["sentence"])
    
    return summary_sentences, key_relations

def extract_noun_phrase(token):
    """
    Extract the full noun phrase starting at this token
    
    Args:
        token: The token to start from (usually a subject or object)
        
    Returns:
        The full noun phrase span
    """
    # Start with this token
    min_i = token.i
    max_i = token.i
    
    # Go through children recursively
    for child in token.children:
        if child.dep_ in ["det", "amod", "compound", "nummod", "poss", "advmod"]:
            min_i = min(min_i, child.i)
            max_i = max(max_i, child.i)
            
            # Also check children of this child
            for grandchild in child.children:
                if grandchild.dep_ in ["det", "amod", "compound", "nummod"]:
                    min_i = min(min_i, grandchild.i)
                    max_i = max(max_i, grandchild.i)
    
    # Get the full span
    return token.doc[min_i:max_i+1]

def filter_medical_relations(relations):
    """
    Filter relations to prioritize those with medical content
    
    Args:
        relations: List of relation dictionaries
        
    Returns:
        Filtered list of relations
    """
    # Collect all medical terms from our gazetteer
    medical_terms = []
    for category_dict in [BODY_PARTS, MEDICAL_CONDITIONS, MOBILITY_TERMS, COGNITIVE_EMOTIONAL_TERMS]:
        for key, variants in category_dict.items():
            medical_terms.extend(variants)
            medical_terms.append(key)
    
    # Filter relations by medical relevance
    medical_relations = []
    other_relations = []
    
    for relation in relations:
        # Check if any part of the relation contains a medical term
        is_medical = False
        rel_text = f"{relation['subject']} {relation['verb']} {relation['object']}".lower()
        
        for term in medical_terms:
            if term.lower() in rel_text:
                is_medical = True
                break
        
        if is_medical:
            medical_relations.append(relation)
        else:
            other_relations.append(relation)
    
    # Return medical relations first, then others
    return medical_relations + other_relations

def construct_summary_sentence(relation):
    """
    Construct a summary sentence from a relation
    
    Args:
        relation: Relation dictionary
        
    Returns:
        Summary sentence or None
    """
    subject = relation["subject"].strip()
    verb = relation["verb"].strip()
    obj = relation["object"].strip()
    
    # Skip incomplete relations
    if not subject or not verb:
        return None
    
    # Construct appropriate sentence based on components
    if obj:
        return f"{subject} {verb} {obj}."
    else:
        return f"{subject} {verb}."

def generate_enhanced_summary(doc, analysis, max_sentences=3, is_assessment=False, is_evaluation=False):
    """
    Generate an enhanced summary that preserves critical details
    based on document type (assessment or evaluation)
    
    Args:
        doc: spaCy Doc object
        analysis: Analysis data from preprocess_tagalog_text
        max_sentences: Maximum number of sentences in the summary
        is_assessment: Whether this is an assessment document
        is_evaluation: Whether this is an evaluation document
        
    Returns:
        Enhanced summary string
    """
    # If document type flags are already passed, use them
    if is_assessment:
        return generate_assessment_summary(doc, analysis, max_sentences)
    elif is_evaluation:
        return generate_evaluation_summary(doc, analysis, max_sentences)
    else:
        # Fall back to auto-detection only if flags aren't provided
        doc_type = detect_document_type(doc.text)
        print(f"Auto-detected document type in enhanced summary: {doc_type}")
        
        if doc_type == "evaluation":
            return generate_evaluation_summary(doc, analysis, max_sentences)
        else:
            # Default to assessment type processing
            return generate_assessment_summary(doc, analysis, max_sentences)

def generate_assessment_summary(doc, analysis, max_sentences=3):
    """
    Generate summary specifically for assessment texts
    """
    # Identify key information categories
    demographics = {}
    symptoms = []
    physical_conditions = []
    living_situation = []
    financial_concerns = []
    needs = []
    
    # Extract demographic information (age, gender)
    for sent in doc.sents:
        sent_text = sent.text.lower()
        # Look for age information
        age_match = re.search(r'(\d+)[\s-]*(?:anyos|taon)', sent_text)
        if age_match:
            demographics['age'] = age_match.group(1)
        
        # Look for gender indicators
        if "lalaki" in sent_text or "tatay" in sent_text:
            demographics['gender'] = "lalaki"
        elif "babae" in sent_text or "nanay" in sent_text:
            demographics['gender'] = "babae"
            
        # Check for living situation
        if "nakatira" in sent_text or "kasama" in sent_text:
            living_situation.append(sent.text)
            
        # Check for financial concerns
        if any(term in sent_text for term in ["pension", "pera", "wala", "gastos", "ubos", "pamili"]):
            financial_concerns.append(sent.text)
            
        # Check for specific needs
        if any(term in sent_text for term in ["kailangan", "gatas", "tinapay", "pagkain", "tubig", "gamot"]):
            needs.append(sent.text)
        
        # Extract physical conditions
        if any(term in sent_text for term in ["paglalakad", "hirap", "pag-upo", "nangangatal", "mahina", "mabigat"]):
            physical_conditions.append(sent.text)
    
    # Get symptom information using morphological analysis
    analyzed_symptoms = analyze_symptoms_with_morphology(doc)
    for symptom in analyzed_symptoms:
        symptoms.append(symptom["text"])
    
    # Build a comprehensive summary with key elements
    summary_parts = []
    
    # 1. Start with demographic information if available
    if demographics:
        demo_text = ""
        if 'gender' in demographics and 'age' in demographics:
            demo_text = f"{'Si Tatay' if demographics['gender'] == 'lalaki' else 'Si Nanay'} ay isang {demographics['age']}-anyos na {demographics['gender']}"
            if living_situation:
                # Add living situation to demographics sentence
                living_info = next((s for s in living_situation if "nakatira" in s.lower() or "kasama" in s.lower()), None)
                if living_info:
                    living_snippet = living_info.split("na ", 1)[1] if "na " in living_info else living_info
                    demo_text += " na " + living_snippet
            summary_parts.append(demo_text)
    
    # 2. Add physical conditions and mobility issues
    if physical_conditions:
        # Pick the most informative physical condition
        best_condition = max(physical_conditions, key=lambda x: sum(term in x.lower() for term in 
                                                                  ["hirap", "paglalakad", "nahihirapan", "nangangatal"]))
        summary_parts.append(best_condition)
    
    # 3. Add key symptoms and medical conditions
    if symptoms:
        # Prioritize pain and visible symptoms
        prioritized_symptoms = [s for s in symptoms if any(term in s.lower() for term in 
                                                         ["masakit", "sakit", "kirot", "nangangatal"])]
        if prioritized_symptoms:
            summary_parts.append(prioritized_symptoms[0])
    
    # 4. Add financial concerns and needs - combine if possible for compactness
    financial_need_text = ""
    if financial_concerns:
        financial_need_text = financial_concerns[0]
    if needs and not any(need in financial_need_text.lower() for need in ["kailangan", "gatas", "tinapay"]):
        if financial_need_text:
            financial_need_text += " " + needs[0]
        else:
            financial_need_text = needs[0]
    
    if financial_need_text:
        summary_parts.append(financial_need_text)
    
    # Ensure we have enough sentences but stay within limit
    if len(summary_parts) < max_sentences and analysis["sentences"]:
        # Find important sentences we haven't included yet
        remaining_slots = max_sentences - len(summary_parts)
        for sent in analysis["sentences"]:
            # Skip if this sentence is already covered
            if any(sent in part or part in sent for part in summary_parts):
                continue
                
            # Add this sentence if it contains important information
            sent_lower = sent.lower()
            if any(term in sent_lower for term in ["kuko", "maduduwal", "timbang", "nahihirapan sa balanse", "apo"]):
                summary_parts.append(sent)
                remaining_slots -= 1
                if remaining_slots <= 0:
                    break
    
    # Truncate to max_sentences if needed
    return " ".join(summary_parts[:max_sentences])

def generate_evaluation_summary(doc, analysis, max_sentences=3):
    """
    Generate summary specifically for evaluation texts
    """
    # Identify key information categories
    recommendations = []
    actions_taken = []
    follow_ups = []
    
    # Look for specific elements in each sentence
    for sent in doc.sents:
        sent_text = sent.text.lower()
        
        # Look for recommendations
        if any(term in sent_text for term in ["inirekomenda", "kailangan", "dapat", "iminumungkahi", "ipinapayo"]):
            recommendations.append(sent.text)
            
        # Look for actions already taken
        elif any(term in sent_text for term in ["ginawa", "tinulungan", "binigyan", "siniguro", "inayos", "ipinaliwanag"]):
            actions_taken.append(sent.text)
        
        # Look for follow-up plans or monitoring
        elif any(term in sent_text for term in ["obserbahan", "pagsubaybay", "pagmanman", "sundin", "bantayan"]):
            follow_ups.append(sent.text)
    
    # Extract key relations to find subject-verb-object patterns
    relation_sentences, relations = extract_key_relations(doc)
    
    # Build summary with priority on recommendations and actions
    summary_parts = []
    
    # 1. Start with the most important recommendation
    if recommendations:
        # Find recommendation about exercise/mobility if present (common in our samples)
        mobility_rec = next((r for r in recommendations if any(term in r.lower() for term in 
                                                          ["paglalakad", "aktibidad", "ehersisyo", "galaw"])), None)
        if mobility_rec:
            summary_parts.append(mobility_rec)
        else:
            # Otherwise, use the first recommendation
            summary_parts.append(recommendations[0])
    
    # 2. Add an important action that was taken
    if actions_taken and len(summary_parts) < max_sentences:
        # Prioritize medication/treatment actions
        med_action = next((a for a in actions_taken if any(term in a.lower() for term in 
                                                         ["gamot", "inayos", "binigyan", "niresetahan"])), None)
        if med_action:
            summary_parts.append(med_action)
        else:
            # Otherwise, use the first action
            summary_parts.append(actions_taken[0])
    
    # 3. Add another important recommendation if space allows
    if len(recommendations) > 1 and len(summary_parts) < max_sentences:
        # Skip recommendation similar to what we already included
        for rec in recommendations[1:]:
            # Check if this is significantly different from what we already have
            if not any(similar_sentences(rec, part) for part in summary_parts):
                summary_parts.append(rec)
                break
    
    # Fill remaining slots with actions or follow-ups
    remaining_candidates = []
    if actions_taken:
        remaining_candidates.extend([a for a in actions_taken if a not in summary_parts])
    if follow_ups:
        remaining_candidates.extend(follow_ups)
    if recommendations:
        remaining_candidates.extend([r for r in recommendations if r not in summary_parts])
        
    # Add from remaining candidates
    remaining_slots = max_sentences - len(summary_parts)
    for i in range(min(remaining_slots, len(remaining_candidates))):
        candidate = remaining_candidates[i]
        if not any(similar_sentences(candidate, part) for part in summary_parts):
            summary_parts.append(candidate)
    
    return " ".join(summary_parts[:max_sentences])

def similar_sentences(sent1, sent2, threshold=0.5):
    """Check if two sentences are similar based on word overlap"""
    words1 = set(sent1.lower().split())
    words2 = set(sent2.lower().split())
    
    # Calculate Jaccard similarity
    intersection = len(words1.intersection(words2))
    union = len(words1.union(words2))
    
    return intersection / union > threshold if union > 0 else False

def extract_non_medical_aspects(doc):
    """
    Extract non-medical aspects important for care workers
    
    Args:
        doc: spaCy Doc object
        
    Returns:
        Dictionary of non-medical aspects
    """
    aspects = {
        "environmental_preferences": [],
        "family_relationships": [],
        "social_support": [],
        "financial_aspects": [],
        "daily_preferences": [],
        "cultural_aspects": []
    }
    
    # Process each sentence
    for sent in doc.sents:
        sent_text = sent.text.lower()
        
        # Environmental preferences
        if any(term in sent_text for term in ["mainit", "malamig", "init", "lamig", "bahay", 
                                             "kwarto", "labas", "loob", "paligid", "hangin"]):
            aspects["environmental_preferences"].append(sent.text)
        
        # Family relationships
        if any(term in sent_text for term in ["anak", "apo", "pamangkin", "asawa", "pamilya",
                                             "kapatid", "magulang", "kamag-anak"]):
            aspects["family_relationships"].append(sent.text)
        
        # Social support
        if any(term in sent_text for term in ["tulong", "tulungan", "suporta", "kasama", 
                                             "mag-alaga", "bantayan", "tingnan", "bisita"]):
            aspects["social_support"].append(sent.text)
        
        # Financial aspects
        if any(term in sent_text for term in ["pera", "gastos", "pension", "binibigyan", 
                                             "bayad", "mahal", "mura", "wala", "hindi kaya"]):
            aspects["financial_aspects"].append(sent.text)
        
        # Daily preferences
        if any(term in sent_text for term in ["gusto", "ayaw", "hilig", "gawain", "araw-araw",
                                             "kinagawian", "nakasanayan", "rutina", "routine"]):
            aspects["daily_preferences"].append(sent.text)
        
        # Cultural aspects
        if any(term in sent_text for term in ["simbahan", "dasal", "paniniwala", "kultura", 
                                             "tradisyon", "kaugalian", "relihiyon", "santo"]):
            aspects["cultural_aspects"].append(sent.text)
    
    # Convert lists to text and remove empty categories
    result = {}
    for category, sentences in aspects.items():
        if sentences:
            result[category] = " ".join(sentences)
    
    return result

# Add this function to detect document type
def detect_document_type(text):
    """
    Automatically detect whether a text is an assessment or evaluation
    
    Args:
        text: The input text
        
    Returns:
        String: "assessment", "evaluation", or "unknown"
    """
    import re
    text_lower = text.lower()
    
    # Count assessment and evaluation indicators
    assessment_count = sum(1 for phrase in ASSESSMENT_PHRASES if phrase in text_lower)
    evaluation_count = sum(1 for phrase in EVALUATION_PHRASES if phrase in text_lower)
    
    # Check for strong evaluation markers
    evaluation_markers = ["inirekomenda", "iminumungkahi", "kailangan", "dapat", "pinayuhan", 
                         "ipinapayo", "ipinaliwanag", "siniguro", "binigyan", "sinabihan"]
    has_strong_evaluation = any(marker in text_lower for marker in evaluation_markers)
    
    # Check for strong assessment markers
    assessment_markers = ["napansin", "nakita", "sinabi niya", "naobserbahan", "ayon sa", 
                         "sabi niya", "hinaing", "daing", "gusto niya", "gumagamit siya"]
    has_strong_assessment = any(marker in text_lower for marker in assessment_markers)
    
    # Check for presence of demographics which strongly indicates assessment
    has_demographics = re.search(r'(\d+)[\s-]*(?:anyos|taon)', text_lower) is not None
    has_elderly_terms = any(term in text_lower for term in ["tatay", "nanay", "lolo", "lola"])
    
    # Make the determination with weighted factors
    if (evaluation_count > assessment_count) or has_strong_evaluation:
        return "evaluation"
    elif (assessment_count > evaluation_count) or has_strong_assessment or (has_demographics and has_elderly_terms):
        return "assessment"
    else:
        # Default to assessment if mentions age
        return "assessment" if has_demographics else "unknown"