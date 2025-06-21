import re
from nlp_loader import nlp

def extract_key_elements(sentences, topic):
    """
    Extract key elements from a group of related sentences with enhanced capabilities.
    Uses comprehensive extraction logic while maintaining the original interface.
    """
    # Initialize with original structure for backward compatibility
    elements = {
        "subject": "",
        "condition": "",
        "impact": "",
        "recommendations": [],
        "interventions": [],
        "monitoring": []
    }
    
    # Enhanced extraction - combine full text for better pattern matching
    full_text = " ".join(sentences)
    doc = nlp(full_text)
    
    # Extract subject (usually the patient)
    for ent in doc.ents:
        if ent.label_ == "PER":
            elements["subject"] = ent.text
            break
    
    # Enhanced condition extraction (symptoms, diseases)
    condition_entities = []
    for ent in doc.ents:
        if ent.label_ == "DISEASE" and ent.text not in condition_entities:
            condition_entities.append(ent.text)
        elif ent.label_ == "SYMPTOM" and ent.text not in condition_entities:
            condition_entities.append(ent.text)
    
    # Set primary condition if found
    if condition_entities:
        elements["condition"] = condition_entities[0]
        
    # Enhanced impact extraction with more pattern matching
    impact_patterns = [
        r"(nangangailangan ng|kailangan ng|dapat|kritikal na) ([^.,:;]+)",
        r"(nagiging sanhi ng|nakakaapekto sa|nagdudulot ng) ([^.,:;]+)",
        r"(dahilan ng|naglalagay sa risk ng) ([^.,:;]+)"
    ]
    
    for pattern in impact_patterns:
        matches = re.finditer(pattern, full_text.lower())
        for match in matches:
            if match and len(match.groups()) >= 2:
                elements["impact"] = match.group(0)
                break
        if elements["impact"]:
            break
    
    # Enhanced recommendation extraction
    recommendation_patterns = [
        r"(inirerekomenda|iminumungkahi|pinapayuhan) (ko|kong|namin|naming)? (na|ang) ([^.,:;]+)",
        r"(dapat|kailangan|kinakailangan|mahalagang) (na)? ([^.,:;]+)",
        r"(mainam|mas mainam|makabubuti) (na)? ([^.,:;]+)"
    ]
    
    for pattern in recommendation_patterns:
        matches = re.finditer(pattern, full_text.lower())
        for match in matches:
            if match and len(match.groups()) >= 1:
                rec = match.group(0)
                if rec and rec not in elements["recommendations"]:
                    elements["recommendations"].append(rec)
    
    # Enhanced intervention extraction
    for ent in doc.ents:
        if ent.label_ in ["TREATMENT_METHOD", "TREATMENT", "EQUIPMENT", "MEDICATION"]:
            if ent.text not in elements["interventions"]:
                elements["interventions"].append(ent.text)
    
    # Enhanced monitoring extraction with more patterns
    monitoring_phrases = ["i-monitor", "obserbahan", "bantayan", "subaybayan", 
                         "regular na tsek", "check regularly", "track", "i-record"]
    
    for phrase in monitoring_phrases:
        if phrase in full_text.lower():
            # Find context around the monitoring phrase
            phrase_pos = full_text.lower().find(phrase)
            if phrase_pos >= 0:
                context_start = max(0, phrase_pos - 10)
                context_end = min(len(full_text), phrase_pos + len(phrase) + 40)
                context = full_text[context_start:context_end].strip()
                
                # Find sentence boundary
                end_pos = context.find('.')
                if end_pos > 0:
                    context = context[:end_pos+1]
                
                if context and context not in elements["monitoring"]:
                    elements["monitoring"].append(context)
    
    # Fallback extraction for cases with no matches
    if not any([elements["recommendations"], elements["interventions"], elements["monitoring"]]):
        # Process each sentence individually for basic extraction
        for sent in sentences:
            sent_doc = nlp(sent)
            
            # Basic recommendation keywords
            if any(word in sent.lower() for word in ["inirerekomenda", "dapat", "kailangan", "mainam"]):
                elements["recommendations"].append(sent)
                
            # Basic intervention keywords
            elif any(word in sent.lower() for word in ["gawin", "isagawa", "therapy", "treatment"]):
                elements["interventions"].append(sent)
                
            # Basic monitoring keywords  
            elif any(word in sent.lower() for word in ["obserbahan", "bantayan", "check", "monitor"]):
                elements["monitoring"].append(sent)
                
            # Verb-based extraction as last resort
            else:
                for token in sent_doc:
                    if token.pos_ == "VERB" and token.dep_ == "ROOT":
                        verb_phrase = " ".join([t.text for t in token.subtree])
                        if verb_phrase and len(verb_phrase) > 3:
                            if topic == "recommendation":
                                elements["recommendations"].append(verb_phrase)
                            elif topic == "intervention":
                                elements["interventions"].append(verb_phrase)
                            elif topic == "monitoring": 
                                elements["monitoring"].append(verb_phrase)
                            break
    
    return elements

def extract_important_terms(text, count=5, doc_type="assessment"):
    """Extract important medical and health terms without relying on noun_chunks."""
    if not text or len(text) < 10:
        return []
    
    # Process with Calamancy NLP
    doc = nlp(text)
    term_candidates = []
    term_scores = {}
    
    # 1. EXTRACT FROM NAMED ENTITIES - most reliable method
    entity_scores = {
        "DISEASE": 10,         # Medical conditions - highest priority
        "SYMPTOM": 9,          # Symptoms
        "TREATMENT_METHOD": 8, # Specific treatment approaches
        "HEALTHCARE_REFERRAL": 8, # Referrals to specialists
        "TREATMENT": 7,        # Treatments
        "RECOMMENDATION": 7,   # Medical recommendations
        "DIET_RECOMMENDATION": 7, # Dietary recommendations
        "WARNING_SIGN": 7,     # Warning signs for conditions
        "BODY_PART": 6,        # Body parts
        "MEASUREMENT": 6,      # Medical measurements
        "COGNITIVE": 6,        # Cognitive conditions
        "EMOTION": 5,          # Emotional states
        "MEDICATION": 6,       # Medications
        "EQUIPMENT": 5,        # Medical equipment
        "HOME_MODIFICATION": 5, # Environmental changes
        "ADL": 5,              # Activities of daily living
        "SAFETY": 5,           # Safety concerns
        "MONITORING": 5,       # Monitoring approaches
        "TIMEFRAME": 4,        # Timeframes
        "SOCIAL_REL": 3,       # Social relationships
        "PER": 2               # People mentioned
    }
    
    for ent in doc.ents:
        if ent.label_ in entity_scores:
            term = ent.text.lower().strip()
            term = re.sub(r'^[,\s.:;]+|[,\s.:;]+$', '', term)
            
            if term and len(term) > 2:
                score = entity_scores.get(ent.label_, 1)
                
                # Context and document-specific scoring
                if doc_type.lower() == "assessment" and ent.label_ in ["DISEASE", "SYMPTOM"]:
                    score += 1
                elif doc_type.lower() == "evaluation" and ent.label_ in ["RECOMMENDATION", "TREATMENT_METHOD"]:
                    score += 1
                    
                # Add or update score
                if term in term_scores:
                    term_scores[term] = max(term_scores[term], score)
                else:
                    term_scores[term] = score
                    term_candidates.append(term)
    
    # 2. USE REGEX PATTERNS INSTEAD OF NOUN CHUNKS
    # Identify common Filipino medical multi-word expressions
    medical_patterns = [
        # Symptom patterns
        r'(problema sa|hirap sa|sakit sa|pananakit ng) ([a-zA-Z\s]{3,25})',
        r'(nahihirapang|hindi makapag|limitadong) ([a-zA-Z\s]{3,25})',
        
        # Condition patterns
        r'(may|diagnosed na may|nagdurusa sa) ([a-zA-Z\s]{3,25})',
        r'(kondisyon ng|karamdaman sa) ([a-zA-Z\s]{3,25})',
        
        # Recommendation patterns
        r'(dapat|kailangan|inirerekomenda na|iminumungkahi na) ([a-zA-Z\s]{3,25})',
        r'(mahalagang|kinakailangang) ([a-zA-Z\s]{3,25})',
        
        # Treatment patterns
        r'(paggamot sa|lunas para sa) ([a-zA-Z\s]{3,25})',
        r'(therapy para sa|gamot para sa) ([a-zA-Z\s]{3,25})'
    ]
    
    for pattern in medical_patterns:
        matches = re.finditer(pattern, text.lower())
        for match in matches:
            if match and len(match.groups()) > 1:
                # Get the matched phrase
                phrase = match.group(0).strip()
                
                # Clean up the phrase
                phrase = re.sub(r'^[,\s.:;]+|[,\s.:;]+$', '', phrase)
                
                if phrase and len(phrase) > 5 and phrase not in term_candidates:
                    # Score based on pattern type
                    pattern_type = match.group(1).strip() if match.groups() else ""
                    
                    if any(rec in pattern_type for rec in ["dapat", "kailangan", "inirerekomenda"]):
                        score = 7 if doc_type.lower() == "evaluation" else 4
                    elif any(cond in pattern_type for cond in ["problema", "hirap", "sakit"]):  # FIXED
                        score = 7 if doc_type.lower() == "assessment" else 4
                    else:
                        score = 5
                    
                    term_scores[phrase] = score
                    term_candidates.append(phrase)
    
    # 3. EXTRACT FROM MEDICAL INDICATORS - comprehensive list
    medical_indicators = [
        # Disease and condition indicators
        "diabetes", "type 2 diabetes", "diyabetis", "hypertension", "altapresyon",
        "dementia", "alzheimer", "depression", "anxiety", "pagkabalisa",
        "chronic pain", "sakit", "kirot", "stroke", "arthritis",
        
        # Symptoms
        "pagkahilo", "dizziness", "pananakit", "hirap huminga", "shortness of breath",
        "pagduduwal", "nausea", "fatigue", "pagkapagod", "panginginig",
        
        # Treatments
        "physical therapy", "gamot", "medication", "therapy", "operasyon", "surgery",
        
        # Recommendations
        "inirerekomenda", "dapat", "kailangan", "mainam", "iminumungkahi"
    ]
    
    # Look for important medical terms and their surrounding context
    for term in medical_indicators:
        if term in text.lower():
            # Find the term in context (words before and after)
            term_positions = [m.start() for m in re.finditer(r'\b' + re.escape(term) + r'\b', text.lower())]
            
            for pos in term_positions:
                # Get surrounding context (15 chars before, 20 after)
                start = max(0, pos - 15)
                end = min(len(text), pos + len(term) + 20)
                context = text[start:end].lower()
                
                # Clean up the context
                context = re.sub(r'^[^a-zA-Z0-9\s]+', '', context)
                context = re.sub(r'[^a-zA-Z0-9\s]+$', '', context)
                context = re.sub(r'\s+', ' ', context).strip()
                
                if context and len(context) > len(term) and context not in term_candidates:
                    # Score based on term type
                    if term in ["inirerekomenda", "dapat", "kailangan", "mainam"]:
                        score = 6 if doc_type.lower() == "evaluation" else 3
                    elif term in ["diabetes", "hypertension", "dementia", "depression"]:
                        score = 6 if doc_type.lower() == "assessment" else 3
                    else:
                        score = 4
                        
                    term_scores[context] = score
                    term_candidates.append(context)
    
    # 4. FILTER AND RANK
    # Remove stopwords
    tagalog_stopwords = {
        "ang", "ng", "sa", "na", "ay", "mga", "ko", "ako", "ikaw", "ka", "siya", "kami", 
        "tayo", "sila", "ito", "iyon", "dito", "diyan", "doon", "ni", "si", "nang", "nga", 
        "po", "raw", "din", "rin", "pa", "lang", "pala", "daw", "man", "kasi", "dahil", 
        "pero", "ngunit", "at", "kung", "kapag", "hindi", "wala", "may", "mayroon"
    }
    
    filtered_terms = []
    for term in term_candidates:
        # Skip standalone stopwords
        if term.lower() in tagalog_stopwords:
            continue
        
        # Skip if all words are stopwords
        if all(word.lower() in tagalog_stopwords for word in term.split()):
            continue
        
        filtered_terms.append(term)
    
    # Sort by score
    sorted_terms = sorted(filtered_terms, key=lambda term: term_scores.get(term, 0), reverse=True)
    
    # 5. ENSURE DIVERSITY OF TERM TYPES
    final_terms = []
    categories_found = {
        "condition": 0,
        "symptom": 0,
        "treatment": 0,
        "recommendation": 0
    }
    
    for term in sorted_terms:
        # Categorize the term
        if any(c in term for c in ["kondisyon", "sakit", "disease", "diabetes"]):
            category = "condition"
        elif any(s in term for s in ["sintomas", "hirap", "pain", "sakit"]):
            category = "symptom"
        elif any(t in term for t in ["gamot", "therapy", "treatment"]):
            category = "treatment"
        elif any(r in term for r in ["inirerekomenda", "dapat", "kailangan"]):
            category = "recommendation"
        else:
            category = "other"
        
        # Maintain diversity by limiting categories
        max_per_category = 2
        if categories_found.get(category, 0) < max_per_category:
            final_terms.append(term)
            categories_found[category] = categories_found.get(category, 0) + 1
            
        # Stop once we have enough terms
        if len(final_terms) >= count:
            break
    
    # If we don't have enough diverse terms, add more from the sorted list
    if len(final_terms) < count:
        for term in sorted_terms:
            if term not in final_terms:
                final_terms.append(term)
            if len(final_terms) >= count:
                break
    
    return final_terms[:count]

def extract_structured_elements(text, section_type):
    """Extract detailed structured elements from text for better synthesis."""
    doc = nlp(text)
    elements = {
        # Subject-related
        "subject": None,      # Main person/patient
        
        # Symptom-related
        "symptoms": [],       # Symptoms described
        "conditions": [],     # Medical conditions
        "severity": [],       # Severity descriptors
        "frequency": [],      # Frequency terms
        "locations": [],      # Body parts/locations
        "duration": [],       # Duration terms
        
        # Physical status
        "vital_signs": [],    # Vital sign measurements
        "limitations": [],    # Physical limitations
        "body_parts": [],     # Body parts mentioned
        
        # Activity-related
        "activities": [],     # Activities mentioned
        "activity_limitations": [], # Limitations in activities
        
        # Mental/Emotional
        "cognitive_status": [], # Cognitive status descriptors
        "mental_state": [],     # Mental state descriptors
        "emotional_state": [],  # Emotional state terms
        
        # Care-related
        "treatments": [],     # Treatments mentioned
        "medications": [],    # Medications
        "dosages": [],        # Medication dosages
        "intervention_methods": [], # Intervention methods
        "recommendations": [], # Recommendations
        "monitoring_plans": [], # Monitoring approaches
        "healthcare_referrals": [], # Referrals to healthcare providers
        
        # Lifestyle/Diet - ADDED MISSING KEYS
        "diet_changes": [],   # Diet recommendations
        "exercise": [],       # Exercise recommendations
        "lifestyle_changes": [], # Other lifestyle changes
        
        # Social
        "social_support": [], # Social support systems
        "caregivers": [],     # Caregiver information
        "living_conditions": [], # Living conditions
        
        # General
        "needs": [],          # Identified needs
        "verbs": [],          # Key action verbs
        "adjectives": [],     # Important descriptive adjectives
        "warnings": [],       # Warning signs or precautions
    }
    
    # Extract subject (main person)
    for ent in doc.ents:
        if ent.label_ == "PER" and not elements["subject"]:
            elements["subject"] = ent.text
            break
    
    # Extract entities into appropriate categories
    for ent in doc.ents:
        if ent.label_ == "DISEASE" and ent.text not in elements["conditions"]:
            elements["conditions"].append(ent.text)
        elif ent.label_ == "SYMPTOM" and ent.text not in elements["symptoms"]:
            elements["symptoms"].append(ent.text)
        elif ent.label_ == "BODY_PART" and ent.text not in elements["body_parts"]:
            elements["body_parts"].append(ent.text)
        elif ent.label_ == "MEASUREMENT" and ent.text not in elements["vital_signs"]:
            elements["vital_signs"].append(ent.text)
        elif ent.label_ == "ADL" and ent.text not in elements["activities"]:
            elements["activities"].append(ent.text)
        elif ent.label_ == "COGNITIVE" and ent.text not in elements["cognitive_status"]:
            elements["cognitive_status"].append(ent.text)
        elif ent.label_ == "EMOTION" and ent.text not in elements["emotional_state"]:
            elements["emotional_state"].append(ent.text)
        elif ent.label_ == "TREATMENT" and ent.text not in elements["treatments"]:
            elements["treatments"].append(ent.text)
        elif ent.label_ == "TREATMENT_METHOD" and ent.text not in elements["intervention_methods"]:
            elements["intervention_methods"].append(ent.text)
        elif ent.label_ == "RECOMMENDATION" and ent.text not in elements["recommendations"]:
            elements["recommendations"].append(ent.text)
        elif ent.label_ == "HEALTHCARE_REFERRAL" and ent.text not in elements["healthcare_referrals"]:
            elements["healthcare_referrals"].append(ent.text)
        elif ent.label_ == "MEDICATION" and ent.text not in elements["medications"]:
            elements["medications"].append(ent.text)
        elif ent.label_ == "MONITORING" and ent.text not in elements["monitoring_plans"]:
            elements["monitoring_plans"].append(ent.text)
        elif ent.label_ == "SOCIAL_REL" and ent.text not in elements["social_support"]:
            elements["social_support"].append(ent.text)
        # ADD NEW DIET AND WARNING EXTRACTION
        elif ent.label_ == "DIET_RECOMMENDATION" and ent.text not in elements["diet_changes"]:
            elements["diet_changes"].append(ent.text)
        elif ent.label_ == "FOOD" and ent.text not in elements["diet_changes"]:
            elements["diet_changes"].append(ent.text)
        elif ent.label_ == "WARNING_SIGN" and ent.text not in elements["warnings"]:
            elements["warnings"].append(ent.text)
    
    # Extract severity descriptors
    severity_terms = ["matindi", "malubha", "severe", "moderate", "mild", "banayad", 
                    "grabeng", "lubhang", "significant", "napaka"]
    
    for term in severity_terms:
        pattern = r'\b' + re.escape(term) + r'\w*\b'
        for match in re.finditer(pattern, text.lower()):
            found_term = match.group(0)
            if found_term not in elements["severity"]:
                elements["severity"].append(found_term)
    
    # Extract frequency descriptors
    frequency_terms = ["araw-araw", "daily", "madalas", "often", "frequently", 
                      "paminsan-minsan", "sometimes", "occasionally", "regular",
                      "persistent", "paulit-ulit", "recurring", "constant"]
                      
    for term in frequency_terms:
        pattern = r'\b' + re.escape(term) + r'\w*\b'
        for match in re.finditer(pattern, text.lower()):
            found_term = match.group(0)
            if found_term not in elements["frequency"]:
                elements["frequency"].append(found_term)
    
    # Extract duration information
    duration_patterns = [
        r'sa loob ng (\d+\s+(?:araw|linggo|buwan|taon))',
        r'for (\d+\s+(?:day|week|month|year)s?)',
        r'(ilang|maraming) (araw|linggo|buwan|taon)',
        r'(several|many|few) (day|week|month|year)s',
        r'(sa nakalipas na|for the past) ([^.,:;]+)'
    ]
    
    for pattern in duration_patterns:
        for match in re.finditer(pattern, text.lower()):
            duration = match.group(0)
            if duration not in elements["duration"]:
                elements["duration"].append(duration)
    
    # Extract limitation patterns
    limitation_patterns = [
        r'(nahihirapan|hirap) sa ([^.,:;]+)',
        r'(limitado|limited) ang ([^.,:;]+)',
        r'(problema|issue) sa ([^.,:;]+)',
        r'(hindi|di) (makapag|magawang) ([^.,:;]+)',
        r'(struggles with|difficulty in) ([^.,:;]+)'
    ]
    
    for pattern in limitation_patterns:
        for match in re.finditer(pattern, text.lower()):
            if len(match.groups()) >= 2:
                limitation = match.group(0)
                if limitation not in elements["limitations"]:
                    elements["limitations"].append(limitation)
    
    # Extract mental state patterns specific to section type
    if section_type == "kalagayan_mental":
        mental_state_patterns = [
            r'(nagpapakita ng|shows|exhibits) ([^.,:;]+)',
            r'(may|has|with) ([^.,:;]+) (mental state|cognitive function|mood)',
            r'(nadidiagnose|diagnosed with) ([^.,:;]+)',
            r'(nararamdaman ang|feels) ([^.,:;]+)'
        ]
        
        for pattern in mental_state_patterns:
            for match in re.finditer(pattern, text.lower()):
                if len(match.groups()) >= 2:
                    state = match.group(0)
                    if state not in elements["mental_state"]:
                        elements["mental_state"].append(state)
    
    # Extract need patterns for any section
    need_patterns = [
        r'(nangangailangan ng|needs|requires) ([^.,:;]+)',
        r'(kailangan ang|requires the) ([^.,:;]+)',
        r'(dapat|should) (ay|be)? ([^.,:;]+)',
        r'(kinakailangan na|it is necessary to) ([^.,:;]+)'
    ]
    
    for pattern in need_patterns:
        for match in re.finditer(pattern, text.lower()):
            need = match.group(0)
            if need not in elements["needs"]:
                elements["needs"].append(need)
    
    # Extract important verbs and adjectives (useful for synthesis)
    for token in doc:
        if token.pos_ == "VERB" and token.is_alpha and len(token.text) > 2:
            if token.text.lower() not in elements["verbs"]:
                elements["verbs"].append(token.text)
        elif token.pos_ == "ADJ" and token.is_alpha and len(token.text) > 2:
            if token.text.lower() not in elements["adjectives"]:
                elements["adjectives"].append(token.text)
    
    return elements

def extract_main_subject(doc):
    """Extract the main subject (person) from the document."""
    # Look for person entities
    for ent in doc.ents:
        if ent.label_ == "PER":
            return ent.text
    
    # Default subjects if no person entity found
    return "Ang beneficiary"

def enhanced_main_subject_extraction(doc):
    """Extract the main subject (person) with improved Filipino name detection."""
    # Honorific patterns specific to Filipino context
    filipino_honorifics = [
        "si", "kay", "lolo", "lola", "tatay", "nanay", "kuya", "ate", 
        "tito", "tita", "ginoong", "ginang", "binibining", "doktor", "dr."
    ]
    
    # First try to find standard named entities
    for ent in doc.ents:
        if ent.label_ == "PER":
            # Extract properly - include title if present
            start_idx = max(0, ent.start - 2)
            preceding_tokens = doc[start_idx:ent.start]
            
            # Check if a Filipino honorific precedes the name
            has_honorific = any(token.text.lower() in filipino_honorifics for token in preceding_tokens)
            
            if has_honorific:
                # Include the honorific in the subject
                subject = doc[start_idx:ent.end].text
                return subject
            else:
                # Just return the entity text
                return ent.text
                
    # Fallback approach - look for honorific + proper noun patterns
    for i, token in enumerate(doc):
        if token.text.lower() in filipino_honorifics and i+1 < len(doc):
            next_token = doc[i+1]
            # Check if next token is a proper noun or has capital first letter
            if next_token.pos_ == "PROPN" or (next_token.text[0].isupper() if next_token.text else False):
                # Simple honorific + name
                if i+2 < len(doc) and doc[i+2].pos_ == "PROPN":
                    # Likely first and last name
                    return doc[i:i+3].text
                else:
                    # Just honorific and single name
                    return doc[i:i+2].text
    
    # Default fallbacks
    return "Ang beneficiary"

def get_entity_section_confidence(entity_text, entity_label, section_name):
    """Calculate confidence score for entity belonging to a specific section."""
    base_score = 1.0
    
    # Section-specific entity boosting with detailed categories
    # Section-specific entity boosting with detailed categories
    section_entity_affinities = {
        # KEY RECOMMENDATIONS - Enhanced based on samples
        "pangunahing_rekomendasyon": {
            "RECOMMENDATION": [
                "immediate", "urgent", "primary", "key recommendation", "critical", 
                "priority", "essential", "important", "necessary", "agarang", 
                "kritikal", "pangunahing rekomendasyon", "mahalagang",
                # New from samples
                "kinakailangang ma-address agad", "comprehensive assessment", 
                "konsultasyon sa neurologist", "geriatrician", "ophthalmologist", 
                "psychiatric evaluation", "ENT specialist", "physical therapist"
            ],
            "HEALTHCARE_REFERRAL": [
                "refer to", "consultation with", "evaluation by", "specialist", 
                "doctor", "physical therapist", "occupational therapist", 
                "medical professional",
                # New from samples
                "geriatric psychiatrist", "psychologist", "formal evaluation",
                "komprehensibong assessment", "agarang psychiatric evaluation",
                "sleep specialist", "audiologist", "proper assessment"
            ],
            "WARNING_SIGN": [
                "red flag", "warning sign", "serious symptom", "concerning finding", 
                "dangerous condition",
                # New from samples
                "suicidal ideation", "thoughts of death", "kawalang-saysay ng buhay",
                "floating spots", "flashes of light", "chest pain", "labored breathing",
                "significant weight loss", "pagkawala ng balanse"
            ]
        },
        "pain_discomfort": {
            "SYMPTOM": [
                "pain", "sakit", "ache", "throbbing", "burning", "pananakit", "kirot", "masakit",
                "sumasakit", "discomfort", "uncomfortable", "hindi komportable", "matindi",
                "chronic pain", "acute pain", "sharp pain", "dull pain", "shooting pain",
                "referred pain", "radiating pain", "persistent pain", "intermittent pain"
            ],
            "BODY_PART": [
                "head", "ulo", "neck", "leeg", "shoulder", "balikat", "arm", "braso", "elbow",
                "siko", "wrist", "pulso", "hand", "kamay", "back", "likod", "spine", "chest",
                "dibdib", "abdomen", "tiyan", "hip", "balakang", "leg", "binti", "knee",
                "tuhod", "ankle", "foot", "paa", "joints", "kasukasuan", "muscles", "kalamnan"
            ],
            "TREATMENT": [
                "pain relief", "pain management", "pain medication", "analgesic", "painkillers",
                "gamot sa sakit", "hot compress", "cold pack", "ice", "yelo", "massage",
                "masahe", "physical therapy", "transcutaneous electrical nerve stimulation",
                "TENS", "acupuncture", "acupressure", "therapy", "relaxation techniques"
            ],
            "MEASUREMENT": [
                "pain scale", "pain level", "intensity", "severity", "mild", "moderate", "severe",
                "bahagya", "katamtaman", "malala", "1-10", "numeric rating scale", "visual analog scale"
            ]
        },

        "hygiene": {
            "TREATMENT": [
                "bathing", "pagliligo", "shower", "bath", "washing", "paglilinis", "brushing teeth",
                "pagsesepilyo", "oral care", "oral hygiene", "grooming", "pag-aayos",
                "toileting", "nail care", "hair care", "pag-aahit", "shaving", "skincare"
            ],
            "EQUIPMENT": [
                "soap", "sabon", "shampoo", "toothpaste", "toothbrush", "sipilyo", "towel",
                "tuwalya", "washcloth", "basin", "palanggana", "sponge", "lotion", "deodorant",
                "shower chair", "bath bench", "grab bars", "hawakan", "shower hose", "hand-held shower",
                "long-handled sponge", "adaptive equipment", "bathroom aids", "toilet riser"
            ],
            "LIMITATION": [
                "dependence", "independence", "assistance", "tulong", "supervision", "pagbabantay",
                "needs help", "nangangailangan ng tulong", "unable to", "hindi kayang", "difficulty",
                "kahirapan", "challenge", "assistance needed", "unable to maintain", "reliant on others"
            ],
            "FREQUENCY": [
                "daily", "araw-araw", "weekly", "lingguhan", "monthly", "buwanan", "regular",
                "occasionally", "rarely", "bihira", "intermittently", "routinely", "scheduled"
            ]
        },
        
        # MOBILITY FUNCTION - Enhanced from mobility samples
        "mobility_function": {
            "EQUIPMENT": [
                "walker", "cane", "wheelchair", "mobility aid", "assistive device",
                "tungkod", "silya de gulong", "ambulatory aid",
                # New from samples
                "quad cane", "handrail", "grab bars", "raised toilet seat", 
                "handles", "knee braces", "non-slip mats", "walking poles",
                "specialized canes", "wider base", "electronic lift chair"
            ],
            "BODY_PART": [
                "joints", "muscles", "extremities", "legs", "arms", "back",
                "kasukasuan", "kalamnan", "binti", "braso", "likod",
                # New from samples
                "tuhod", "knee", "hip", "balakang", "lower back", "quadriceps", 
                "gluteal muscles", "core strength", "ankles", "joints", "feet", "paa"
            ],
            "TREATMENT_METHOD": [
                "strengthening exercises", "balance training", "gait training",
                "transfer training", "range of motion", "flexibility exercises",
                # New from samples
                "leg lifts", "gentle squats", "thigh strengthening", "physical therapy",
                "occupational therapy assessment", "body mechanics", "safe handling techniques",
                "proper positioning", "leg exercises", "heat application", "gentle stretching"
            ],
            "LIMITATION": [
                "limited mobility", "difficulty walking", "unsteady gait",
                "fall risk", "transfer difficulty", "balance problems",
                # New from samples
                "hirap sa pagtayo", "difficulty in sit-to-stand", "uneven surfaces",
                "steps navigation", "hirap sa hagdanan", "freezing episodes",
                "gait instability", "hirap sa pagbabalanse"
            ]
        },
        
        # SLEEP MANAGEMENT - Enhanced from insomnia sample
        "kalagayan_ng_tulog": {
            "ROUTINE": [
                "sleep routine", "sleep schedule", "sleep hygiene", "bedtime ritual",
                "sleep pattern", "sleep habit", "gawing pagtulog",
                # New from samples
                "structured bedtime routine", "consistent sleep-wake schedule", 
                "regular sleep pattern", "pagtigil sa panonood ng TV", "no cellphone use",
                "no screens", "bedtime", "warm bath", "warm shower"
            ],
            "ENVIRONMENT": [
                "bedroom", "sleep environment", "mattress", "pillow", "bedding",
                "kwarto", "kama", "unan", "kumot", "sapin",
                # New from samples
                "dim light", "quiet room", "nakakagambalang ingay", "room temperature",
                "temperatura ng kwarto", "relaxing scents", "lavender", "comfortable bed",
                "comfortable temperature", "dark room"
            ],
            "SYMPTOM": [
                "insomnia", "sleep disturbance", "difficulty sleeping", "early waking",
                "sleep apnea", "hirap matulog", "pagkagising nang maaga",
                # New from samples
                "nightmares", "bangungot", "night sweats", "nagigising sa kalagitnaan ng gabi",
                "daytime drowsiness", "pagtulog sa hapon", "disturbing dreams",
                "insomnia", "malalang insomnia", "poor sleep quality"
            ],
            "RECOMMENDATION": [
                "consistent bedtime", "avoid caffeine", "relaxation technique",
                "sleep position", "comfortable environment",
                # New from samples
                "deep breathing exercises", "guided meditation", "journaling bago matulog",
                "no caffeine after noon", "no caffeine after tanghali", "light snack",
                "avoid heavy meals", "cognitive restructuring", "avoid stimulation"
            ]
        },
        
        # MENTAL HEALTH - Enhanced from depression/anxiety samples
        "kalagayan_mental": {
            "EMOTION": [
                "anxiety", "depression", "worry", "stress", "fear", "confusion",
                "pagkabalisa", "kalungkutan", "pag-aalala", "takot", "pagkalito",
                # New from samples
                "grief", "prolonged grief", "complicated grief", "profound loss",
                "feeling worthless", "kawalang-saysay", "fear of abandonment",
                "embarassment", "frustration", "irritability", "hopelessness"
            ],
            "COGNITIVE": [
                "memory", "cognition", "orientation", "comprehension", "awareness",
                "memorya", "pag-unawa", "awareness", "cognitive function",
                # New from samples
                "sundowning syndrome", "confusion at night", "agitation sa gabi",
                "false beliefs", "disorientation", "cognitive decline", 
                "pagbabago sa memorya", "negatibong kaisipan", "overthinking"
            ],
            "TREATMENT": [
                "counseling", "therapy", "emotional support", "psychological support",
                "cognitive exercises", "mental stimulation",
                # New from samples
                "psychiatric evaluation", "grief counseling", "support group", 
                "grief support", "gentle routine", "structured activities",
                "cognitive behavioral therapy", "relaxation techniques"
            ],
            "SOCIAL_REL": [
                "socialization", "interaction", "engagement", "participation",
                "pakikisalamuha", "pakikipag-ugnayan",
                # New from samples
                "small manageable interactions", "social circle", "social connections",
                "support group", "small, quiet gatherings", "family interaction",
                "spiritual needs", "prayer", "meditation", "church visit"
            ]
        },
        
        # MEDICATION MANAGEMENT - Enhanced from medication samples
        "pamamahala_ng_gamot": {
            "MEDICATION": [
                "tablet", "capsule", "pill", "tableta", "kapsula", "gamot",
                "prescription", "reseta", "dose", "dosis",
                # New from samples
                "pain medication", "sleeping pills", "anti-anxiety", "maintenance",
                "prescription medications", "over-the-counter", "supplements",
                "anti-depressants", "blood pressure medication", "anti-inflammatory"
            ],
            "EDUCATION": [
                "medication education", "drug information", "side effect",
                "drug interaction", "contraindication", "instruction",
                # New from samples
                "simplified explanation sheet", "package insert", "pag-unawa sa gamot",
                "fears tungkol sa medications", "misconceptions", "simplified visual guide",
                "potential side effects", "rare complications", "benefits over risks",
                "medication literacy", "medication guide"
            ],
            "RECOMMENDATION": [
                "medication adherence", "compliance", "pill organizer",
                "reminder system", "pagsunod sa gamot", "medication safety",
                # New from samples
                "take medication as prescribed", "hindi lang kapag may sakit", 
                "regular schedule", "consistent timing", "medication diary",
                "tracking log", "pill box", "medication dispenser"
            ]
        },
        
        # SAFETY RISK FACTORS - Enhanced from falls prevention samples
        "safety_risk_factors": {
            "RISK_FACTOR": [
                "fall risk", "fall hazard", "panganib ng pagkahulog", "trip hazard", 
                "safety hazard", "slippery", "madulas", "uneven surface",
                # New from samples
                "clutter", "walang suporta sa banyo", "loose mats", "madilim na lugar",
                "poor lighting", "cords", "cables", "high steps", "unstable furniture",
                "dizziness", "pagkawala ng balanse", "medication side effects",
                "poor vision", "night vision problems", "poor depth perception"
            ],
            "PREVENTION": [
                "grab bars", "handrails", "safety rails", "non-slip", "rubber mat",
                "lighting", "clear pathways", "proper footwear",
                # New from samples
                "motion-activated lights", "nightlights", "bedside commode",
                "removal of tripping hazards", "removal of loose rugs",
                "rearrange furniture", "declutter", "stable furniture",
                "slip-resistant shoes", "cushioned soles", "wide base support"
            ],
            "ENVIRONMENT": [
                "stairs", "bathroom", "shower", "kitchen", "hagdanan", 
                "paliguan", "banyo", "kusina", "walkway", "daanan",
                # New from samples
                "hallway", "living room", "kitchen area", "bedroom", "entrance",
                "outdoor paths", "garden", "pasilyo", "salas", "hagdan",
                "sidewalk", "patio", "balcony", "threshold", "uneven terrain"
            ]
        },
        
        # ORAL HEALTH - Enhanced from dental health samples
        "kalusugan_ng_bibig": {
            "BODY_PART": [
                "tooth", "teeth", "gums", "tongue", "mouth", "oral cavity", 
                "ngipin", "dila", "gilagid", "bibig", "ngala-ngala", "jaw", "panga",
                # New from samples
                "palate", "cheeks", "pisngi", "lips", "labi", "mucous membrane",
                "saliva glands", "buccal tissue", "sublingual area", "periodontal tissue"
            ],
            "TREATMENT": [
                "brushing", "flossing", "dental checkup", "tooth extraction", 
                "pagsesepilyo", "dental cleaning", "oral examination",
                # New from samples
                "paraffin wax treatment", "mouthwash", "warm salt water rinses",
                "oral lubricants", "artificial saliva", "lip balm", "topical fluoride",
                "professional cleaning", "deep cleaning", "scaling", "root planing"
            ],
            "DISEASE": [
                "cavity", "gingivitis", "periodontitis", "dental caries", 
                "tooth decay", "oral cancer", "sira ng ngipin",
                # New from samples
                "dry mouth", "xerostomia", "halitosis", "bad breath", "mabahong hininga",
                "oral lesions", "mouth sores", "canker sores", "angular cheilitis", 
                "oral thrush", "leukoplakia", "lichen planus", "erythema multiforme"
            ]
        },

        # SYMPTOMS - For assessment document type
        "mga_sintomas": {
            "SYMPTOM": [
                "pain", "ache", "sakit", "pananakit", "kirot", "discomfort", 
                "hirap", "difficulty", "problema", "nahihirapan", "nahihirapang",
                # From samples
                "dizziness", "pagkahilo", "nausea", "pagduduwal", "headache", "sakit ng ulo",
                "fatigue", "pagod", "shortness of breath", "hirap huminga", "insomnia",
                "constipation", "diarrhea", "pagtatae", "loss of appetite", "pamamaga",
                "edema", "night sweats", "fever", "lagnat", "chills", "panlalamig", 
                "blurry vision", "hearing loss", "ringing", "balance problems"
            ],
            "DISEASE": [
                "arthritis", "diabetes", "hypertension", "altapresyon", "pneumonia", 
                "stroke", "heart disease", "sakit sa puso", "UTI", "infection",
                # From samples
                "COPD", "dementia", "Alzheimer's", "Parkinson's", "depression",
                "anxiety", "pagkabalisa", "osteoporosis", "chronic pain", "neuropathy",
                "peripheral neuropathy", "peripheral edema", "sleep apnea", "insomnia"
            ],
            "BODY_PART": [
                "head", "ulo", "chest", "dibdib", "abdomen", "tiyan", "joints", 
                "kasukasuan", "back", "likod", "leg", "binti", "arm", "braso",
                # From samples
                "knee", "tuhod", "hip", "balakang", "ankle", "wrist", "pulso",
                "shoulder", "balikat", "neck", "leeg", "eyes", "mata", "ears", "tainga",
                "feet", "paa", "hands", "kamay", "toes", "fingers", "daliri"
            ]
        },
        
        # PHYSICAL CONDITION - For assessment document type
        "kalagayan_pangkatawan": {
            "MEASUREMENT": [
                "blood pressure", "presyon", "heart rate", "pulse", "temperatura", 
                "temperature", "oxygen level", "oxygen saturation", "SpO2", 
                # From samples
                "vital signs", "respiratory rate", "breathing rate", "glucose level", 
                "blood sugar", "weight", "timbang", "height", "tangkad", "BMI",
                "body mass index", "waist circumference", "hip measurement"
            ],
            "PHYSICAL_STATE": [
                "weak", "mahina", "strong", "malakas", "stable", "unstable", 
                "frail", "mahina", "muscle tone", "skin texture", "skin color", 
                # From samples
                "pale", "maputla", "cyanotic", "edematous", "mamamaga", "underweight",
                "overweight", "obese", "malnourished", "dehydrated", "fatigued",
                "well-built", "thin", "payat", "mataba", "cachexic", "wasted"
            ],
            "LIMITATION": [
                "limited range", "stiffness", "paninigas", "weakness", "kahinaan", 
                "imbalance", "poor balance", "poor coordination", "limited mobility",
                # From samples
                "difficulty walking", "hirap maglakad", "tremors", "panginginig",
                "paralysis", "decreased strength", "limited endurance", "mabilis mapagod",
                "poor grip strength", "mahina ang hawak", "poor fine motor skills",
                "inability to stand", "hindi makatayo", "bed-bound", "nakahiga"
            ]
        },
        
        # ACTIVITIES - For assessment document type
        "aktibidad": {
            "ADL": [
                "bathing", "pagligo", "dressing", "pagbibihis", "eating", "pagkain", 
                "toileting", "pag-CR", "grooming", "pag-aayos", "mobility", "paggalaw",
                # From samples
                "cooking", "pagluluto", "cleaning", "paglilinis", "shopping", "pamimili",
                "using phone", "paggamit ng telepono", "medication management",
                "money management", "transportation", "pagbibiyahe", "using stairs"
            ],
            "EQUIPMENT": [
                "cane", "tungkod", "walker", "wheelchair", "silya de gulong", "bedside commode", 
                "hospital bed", "trapeze", "grab bars", "shower chair", "bath bench",
                # From samples
                "toilet riser", "elevated toilet seat", "handrails", "adaptive utensils",
                "dressing aids", "reacher", "long-handled sponge", "button hook",
                "elastic shoelaces", "sock aid", "transfer board", "lift equipment"
            ],
            "ASSISTANCE": [
                "independent", "independent sa", "requires assistance", "nangangailangan ng tulong", 
                "dependent sa", "partial assistance", "minimal assistance", "moderate assistance", 
                # From samples
                "maksimal na tulong", "maximal assistance", "standby assistance",
                "verbal cues", "verbal reminders", "physical assistance", "supervision",
                "pagbabantay", "complete dependence", "lubusang pag-asa sa iba"
            ]
        },
        
        # SOCIAL CONDITION - For assessment document type
        "kalagayan_social": {
            "SOCIAL_REL": [
                "family", "pamilya", "spouse", "asawa", "children", "anak", "friends", 
                "kaibigan", "caregiver", "tagapag-alaga", "support", "suporta", 
                # From samples
                "lives with", "kasama sa bahay", "grandchildren", "apo", "neighbors",
                "kapitbahay", "relatives", "kamag-anak", "church members", "community",
                "social group", "senior center", "day care", "support group", "companions"
            ],
            "SOCIAL_STATE": [
                "isolated", "nag-iisa", "lonely", "nalulungkot", "engaged", "active", 
                "withdrawn", "umiiwas", "social", "interactive", "nakikisalamuha",
                # From samples  
                "sociable", "enjoys company", "prefers solitude", "recently widowed",
                "newly separated", "living alone", "fear of abandonment", "neglected", 
                "abused", "biktima ng pang-aabuso", "elder abuse", "social anxiety",
                "avoidant", "dependent on others", "socially active"
            ],
            "LIVING_CONDITION": [
                "living situation", "tirahan", "home environment", "kapaligiran sa bahay", 
                "living alone", "nag-iisang nakatira", "assisted living", "care facility", 
                # From samples
                "nursing home", "skilled nursing facility", "senior housing", "multi-generational home",
                "retirement community", "house", "apartment", "second floor", "stairs",
                "accessiblity issues", "rural area", "urban setting", "far from services"
            ]
        },
        "medical_history": {
            "DISEASE": [
                "diabetes", "hypertension", "heart attack", "stroke", "cancer", "arthritis",
                "COPD", "asthma", "chronic condition", "diyabetis", "altapresyon", 
                "atake sa puso", "kanser", "rayuma", "hika", "heart disease", "sakit sa puso",
                "coronary artery disease", "emphysema", "chronic bronchitis", "osteoporosis",
                "kidney disease", "renal disease", "liver disease", "cirrhosis", "thyroid disorder"
            ],
            "TREATMENT": [
                "surgery", "operation", "procedure", "hospitalization", "admission",
                "operasyon", "pagkakaospital", "naospital", "paggamot", "therapeutic",
                "surgical history", "previous surgeries", "past procedures", "intervention"
            ],
            "TIME": [
                "history", "previous", "past", "chronic", "longtime", "years ago", "months ago",
                "dati", "noon", "nakaraan", "dating", "matagal na", "diagnosed", "na-diagnose",
                "onset", "duration", "tagal", "simula", "diagnosed since", "existing"
            ],
            "MEDICATION": [
                "maintenance medication", "long-term medication", "controlled with", "managed with", 
                "prescribed", "treatment regimen", "iniinom na gamot", "gamot", "reseta", 
                "inirereseta", "medications", "current medications", "medication history"
            ],
            "FAMILY_HISTORY": [
                "family history", "genetic", "hereditary", "runs in the family", "family medical history",
                "kasaysayan ng pamilya", "namana", "history sa pamilya", "family condition",
                "parent had", "magulang", "genetics", "inherited", "predisposition"
            ]
        }
    }
    
    # Check if this entity has special affinity with the section
    if section_name in section_entity_affinities:
        section_affinities = section_entity_affinities[section_name]
        
        # Check if entity type has specific affinities
        if entity_label in section_affinities:
            # Check if entity text matches any high-affinity terms
            entity_lower = entity_text.lower()
            
            # Full match - highest boost
            if any(term.lower() == entity_lower for term in section_affinities[entity_label]):
                return base_score * 4.0
                
            # Partial match - good boost
            if any(term.lower() in entity_lower or entity_lower in term.lower() 
                  for term in section_affinities[entity_label]):
                return base_score * 2.5
    
    # Default scores by entity type and section - comprehensive mapping
    entity_section_scores = {
        # KEY RECOMMENDATIONS
        "pangunahing_rekomendasyon": {
            "RECOMMENDATION": 4.0, "HEALTHCARE_REFERRAL": 3.5, 
            "WARNING_SIGN": 2.5, "TREATMENT_METHOD": 2.5
        },
        
        # ACTION STEPS
        "mga_hakbang": {
            "TREATMENT_METHOD": 3.5, "TREATMENT": 3.0,
            "EQUIPMENT": 2.5, "RECOMMENDATION": 2.5
        },
        
        # CARE NEEDS  
        "pangangalaga": {
            "MONITORING": 3.5, "TREATMENT": 3.0,
            "TIME": 2.0, "DOCUMENTATION": 2.5
        },
        
        # LIFESTYLE CHANGES
        "pagbabago_sa_pamumuhay": {
            "RECOMMENDATION": 3.0, "DIET_RECOMMENDATION": 2.5,
            "ACTIVITY": 2.5, "ENVIRONMENT": 2.0
        },
        
        # SAFETY RISK FACTORS
        "safety_risk_factors": {
            "RISK_FACTOR": 4.0, "PREVENTION": 3.0,
            "ENVIRONMENT": 2.5, "EQUIPMENT": 2.5,
            "WARNING_SIGN": 2.5
        },
        
        # NUTRITION AND DIET
        "nutrisyon_at_pagkain": {
            "DIET_RECOMMENDATION": 4.0, "FOOD": 3.5,
            "NUTRITION": 3.5, "FOOD_PREPARATION": 3.0
        },
        
        # ORAL/DENTAL HEALTH
        "kalusugan_ng_bibig": {
            "BODY_PART": 1.8, "HYGIENE": 2.0, "DENTAL": 3.5, 
            "SYMPTOM": 1.0, "TREATMENT": 2.5, "DISEASE": 2.0
        },
        
        # MOBILITY AND FUNCTION
        "mobility_function": {
            "EQUIPMENT": 3.5, "BODY_PART": 2.0,
            "TREATMENT_METHOD": 3.0, "LIMITATION": 2.5
        },
        
        # SLEEP MANAGEMENT
        "kalagayan_ng_tulog": {
            "ROUTINE": 3.5, "ENVIRONMENT": 2.0,
            "SYMPTOM": 2.0, "RECOMMENDATION": 2.5
        },
        
        # MEDICATION MANAGEMENT
        "pamamahala_ng_gamot": {
            "MEDICATION": 4.0, "PRESCRIPTION": 3.5, "TREATMENT": 2.5, 
            "TIME": 2.0, "RECOMMENDATION": 2.5, "EDUCATION": 3.0
        },
        
        # FAMILY SUPPORT
        "suporta_ng_pamilya": {
            "PERSON": 3.0, "SOCIAL_REL": 3.5,
            "RECOMMENDATION": 2.5, "ENVIRONMENT": 2.0
        },
        
        # MENTAL/EMOTIONAL HEALTH
        "kalagayan_mental": {
            "EMOTION": 3.5, "COGNITIVE": 3.5,
            "TREATMENT": 2.5, "SOCIAL_REL": 2.0
        },
        
        # PREVENTIVE HEALTH
        "preventive_health": {
            "PREVENTION": 3.5, "HEALTHCARE_REFERRAL": 3.0,
            "WARNING_SIGN": 2.5, "RISK_FACTOR": 3.0
        },
        
        # VITAL SIGNS MEASUREMENTS
        "vital_signs_measurements": {
            "MEASUREMENT": 4.0, "MONITORING": 3.5,
            "EQUIPMENT": 2.5, "RECOMMENDATION": 2.0
        },
         # Assessment section scores
        "mga_sintomas": {
            "SYMPTOM": 4.0, "DISEASE": 3.5, "BODY_PART": 2.0,
            "TIME": 1.5, "FREQUENCY": 2.0
        },
        "kalagayan_pangkatawan": {
            "MEASUREMENT": 3.5, "PHYSICAL_STATE": 3.0, 
            "LIMITATION": 2.5, "BODY_PART": 2.0
        },
        "aktibidad": {
            "ADL": 3.5, "EQUIPMENT": 2.5,
            "ASSISTANCE": 3.0, "LIMITATION": 2.0
        },
        "kalagayan_social": {
            "SOCIAL_REL": 3.5, "SOCIAL_STATE": 3.0,
            "LIVING_CONDITION": 2.5, "PERSON": 2.0
        },
        "medical_history": {
            "DISEASE": 4.0, "TREATMENT": 3.0, "TIME": 3.0, 
            "MEDICATION": 2.5, "FAMILY_HISTORY": 3.5, "PERSON": 1.5,
            "SYMPTOM": 1.8, "RISK_FACTOR": 2.0, "HEALTHCARE_REFERRAL": 2.0
        },
        "pain_discomfort": {
            "SYMPTOM": 4.0, "BODY_PART": 3.0, "TREATMENT": 2.5,
            "MEASUREMENT": 3.0, "TIME": 1.5, "FREQUENCY": 2.0
        },

        "hygiene": {
            "TREATMENT": 3.5, "EQUIPMENT": 3.0, "LIMITATION": 3.0,
            "FREQUENCY": 2.5, "BODY_PART": 1.5, "PERSON": 1.0
        }
    }
    
    # Return appropriate boost if available
    if section_name in entity_section_scores and entity_label in entity_section_scores[section_name]:
        return entity_section_scores[section_name][entity_label]
        
    # Base score for non-specific matches
    return base_score

def normalize_medical_entity(entity_text, entity_type=None):
    """Normalize medical entity text to handle variations and improve matching."""
    if not entity_text:
        return entity_text
        
    normalized = entity_text.lower().strip()
    
    # Common abbreviations and their expansions
    abbreviations = {
        "bp": "blood pressure", "hr": "heart rate", "rr": "respiratory rate",
        "t": "temperature", "temp": "temperature", "spo2": "oxygen saturation",
        "bs": "blood sugar", "dm": "diabetes mellitus", "htn": "hypertension",
        "cva": "cerebrovascular accident", "mi": "myocardial infarction",
        "copd": "chronic obstructive pulmonary disease", "uti": "urinary tract infection",
        "adl": "activities of daily living", "cabg": "coronary artery bypass graft",
        "chf": "congestive heart failure"
    }
    
    # Check for exact abbreviation match
    if normalized in abbreviations:
        normalized = abbreviations[normalized]
    
    # Remove common prefixes/suffixes that don't change meaning
    normalized = re.sub(r'^(ang|mga|sa|ng)\s+', '', normalized)
    normalized = re.sub(r'\s+(niya|nila|ko|mo|nya|namin|niyang|nang)', '', normalized)
    
    # Standardize units of measurement
    normalized = re.sub(r'(\d+)\s*(/)\s*(\d+)', r'\1\2\3', normalized)  # Fix spaces in fractions
    
    # Standardize Filipino health terms
    filipino_term_mapping = {
        "presyon": "blood pressure",
        "altapresyon": "hypertension",
        "diyabetis": "diabetes",
        "atake sa puso": "heart attack",
        "sakit sa puso": "heart disease",
        "stroke": "stroke",
        "demensya": "dementia",
        "sipon": "common cold",
        "ubo": "cough",
        "lagnat": "fever",
        "rayuma": "arthritis"
    }
    
    # Apply Filipino term standardization
    for term, standard in filipino_term_mapping.items():
        if term in normalized:
            normalized = normalized.replace(term, standard)
    
    # Handle specific entity types
    if entity_type == "MEDICATION":
        # Remove dose information for medication matching
        normalized = re.sub(r'\d+\s*mg|\d+\s*ml|\d+\s*mcg', '', normalized).strip()
        
    elif entity_type == "SYMPTOM":
        # Standardize symptom descriptions
        pain_terms = ["sakit", "pananakit", "sumasakit", "masakit", "pain"]
        if any(term in normalized for term in pain_terms):
            if "chest" in normalized or "dibdib" in normalized:
                normalized = "chest pain"
            elif "head" in normalized or "ulo" in normalized:
                normalized = "headache"
            elif "stomach" in normalized or "tiyan" in normalized:
                normalized = "abdominal pain"
    
    return normalized

def detect_entity_relationships(doc, entity_spans, max_distance=10):
    """Detect relationships between medical entities in text."""
    relationships = []
    
    # Skip if we have fewer than 2 entities
    if len(entity_spans) < 2:
        return relationships
    
    # Relationship patterns to look for between entities
    relationship_patterns = [
        (["SYMPTOM", "SYMPTOM"], ["worsened by", "triggered by", "associated with", "along with", 
                                "kasabay ng", "kasama ang", "at"]),
        (["SYMPTOM", "DISEASE"], ["caused by", "due to", "dahil sa", "associated with", 
                                "indication of", "sign of"]),
        (["SYMPTOM", "MEDICATION"], ["relieved by", "treated with", "responds to", 
                                   "ginagamot gamit ang"]),
        (["SYMPTOM", "BODY_PART"], ["in the", "on the", "around the", "of the", "sa", "sa may"]),
        (["DISEASE", "TREATMENT"], ["treated with", "managed with", "requires", 
                                  "needs", "ginagamot gamit ang"]),
        (["MEDICATION", "DISEASE"], ["for", "prescribed for", "treats", "para sa", "gamot sa"]),
        (["BODY_PART", "RISK_FACTOR"], ["at risk for", "vulnerable to", "prone to", 
                                      "nanganganib sa"]),
    ]
    
    # Sort entities by document position
    sorted_entities = sorted(entity_spans, key=lambda e: e.start)
    
    # Check pairs of entities
    for i, entity1 in enumerate(sorted_entities[:-1]):
        for j in range(i+1, min(i+max_distance, len(sorted_entities))):
            entity2 = sorted_entities[j]
            
            # Calculate token distance between entities
            distance = entity2.start - entity1.end
            
            # Skip if too far apart
            if distance > 15:
                continue
            
            # Extract text between entities to check for relationship indicators
            if entity1.end < entity2.start:  # Entities don't overlap
                between_text = doc.text[entity1.end:entity2.start].lower().strip()
                
                # Check if entity types and between text match relationship patterns
                for (type_pair, indicators) in relationship_patterns:
                    if [entity1.label_, entity2.label_] == type_pair:
                        # Check for relationship indicators in between text
                        if any(indicator in between_text for indicator in indicators):
                            relationship_type = f"{type_pair[0]}_TO_{type_pair[1]}"
                            relationships.append({
                                "entity1": entity1.text,
                                "entity1_type": entity1.label_,
                                "entity2": entity2.text,
                                "entity2_type": entity2.label_,
                                "relationship": relationship_type,
                                "confidence": 0.85 if len(between_text) < 10 else 0.7
                            })
    
    return relationships