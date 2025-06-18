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
    return "Ang pasyente"