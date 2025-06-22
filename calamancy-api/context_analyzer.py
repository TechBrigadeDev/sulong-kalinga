import re
from nlp_loader import nlp, split_into_sentences
from entity_extractor import extract_structured_elements

# NEW SUPPORTING FUNCTIONS FOR IMPROVED CONTEXT DETECTION

def analyze_document_context(sections, doc_type):
    """Build a comprehensive document-level context by analyzing all sections together."""
    context = {
        "priority_sections": [],
        "key_entities": {},
        "severity_trend": None,
        "duration_context": None,
        "symptom_impacts": [],
        "condition_relationships": {},
        "temporal_markers": [],
        "cross_section_themes": [],
        "care_domains": {},      
        "cultural_context": [],  
        "beneficiary_profile": {}
    }
    
    # ENHANCED: Expanded severity terms with Filipino expressions - MOVED UP HERE
    severity_terms = {
        "high": ["malubha", "matindi", "severe", "significant", "napaka", "lubhang", "grabeng", 
                "masyadong", "sobrang", "critical", "emergency", "urgent", "nagbabanta", 
                "nakamamatay", "life-threatening", "maaaring ikasama", "mapanganib", 
                "napakalala", "grabe", "extremely", "highly", "seriously", "gravely"],
        "medium": ["katamtaman", "moderate", "banayad", "medyo", "may kaunting", "noticeable", 
                  "concerning", "kapansin-pansin", "nakakaapekto", "hindi masyado", 
                  "moderately", "somewhat", "partly", "relatively"],
        "low": ["bahagya", "mild", "minimal", "slight", "kaunti", "hindi gaanong", "limitado", 
               "hindi masyadong", "minor", "light", "slightly", "marginally", "occasionally"]
    }
    
    # Combine all section text for full document analysis
    all_text = " ".join(section_text for section_text in sections.values())
    doc = nlp(all_text)
    
    # Fix error: Determine priority sections based on document type and content
    if doc_type.lower() == "assessment":
        # Assessment priority logic - focus on symptoms and physical condition first
        
        # Count entities by section to determine information density
        section_entity_counts = {}
        critical_entities = []
        
        # Find sections with highest information density and critical medical entities
        for section_name, section_text in sections.items():
            section_doc = nlp(section_text)
            
            # Count entities by section
            entity_count = len([e for e in section_doc.ents])
            section_entity_counts[section_name] = entity_count
            
            # Look for critical medical entities (severe conditions, acute symptoms)
            for ent in section_doc.ents:
                if ent.label_ in ["DISEASE", "SYMPTOM"]:
                    # Check nearby text for severity markers
                    ent_start = max(0, ent.start_char - 20)
                    ent_end = min(len(section_text), ent.end_char + 20)
                    context_text = section_text[ent_start:ent_end].lower()
                    
                    # Check for critical conditions and severe symptoms
                    if (ent.text.lower() in ["stroke", "heart attack", "diabetes", "hypertension", "dementia", "alzheimer", "cancer"] or
                        any(term in context_text for term in severity_terms["high"])):
                        critical_entities.append((section_name, ent.text))
        
        # Base priorities on medical information hierarchy
        base_priorities = ["mga_sintomas", "kalagayan_pangkatawan", "kalagayan_mental"]
        
        # Add sections with critical entities first
        for section_name, _ in critical_entities:
            if section_name not in context["priority_sections"]:
                context["priority_sections"].append(section_name)
        
        # Ensure we have the most important sections at minimum
        for section in base_priorities:
            if section in sections and section not in context["priority_sections"]:
                context["priority_sections"].append(section)
        
        # Add sections with high information density
        if section_entity_counts:
            # Get top 2 sections by entity count that aren't already included
            sorted_sections = sorted(section_entity_counts.items(), key=lambda x: x[1], reverse=True)
            for section_name, _ in sorted_sections:
                if len(context["priority_sections"]) >= 3:  # Limit to 3 priority sections
                    break
                if section_name not in context["priority_sections"]:
                    context["priority_sections"].append(section_name)
        
        # Ensure activity section is included if ADL limitations are present
        activity_section = next((s for s in sections if "aktibidad" in s), None)
        if activity_section and activity_section not in context["priority_sections"]:
            activity_terms = ["nahihirapan", "limitado", "assistance", "tulong", "hindi magawa"]
            activity_text = sections.get(activity_section, "")
            if any(term in activity_text.lower() for term in activity_terms):
                context["priority_sections"].append(activity_section)

    else:  # Evaluation document
        # Evaluation priority logic - focus on recommendations and interventions
        
        # Always prioritize primary recommendations
        recommendation_section = next((s for s in sections if "rekomendasyon" in s), None)
        if recommendation_section:
            context["priority_sections"].append(recommendation_section)
        
        # Check for urgent/critical recommendations
        for section_name, section_text in sections.items():
            section_doc = nlp(section_text)
            
            # Look for urgency markers
            urgency_terms = ["agaran", "urgent", "immediately", "kaagad", "critical", "crucial", 
                        "emergency", "hindi dapat palampasin", "sa lalong madaling panahon"]
            
            if any(term in section_text.lower() for term in urgency_terms):
                if section_name not in context["priority_sections"]:
                    context["priority_sections"].append(section_name)
            
            # Check for specific medical intervention requirements
            intervention_indicators = [
                "kailangan ng medical attention", "requires hospitalization",
                "specialist referral", "dapat magpatingin", "kinakailangan ng gamot",
                "medication adjustment", "therapy", "procedure"
            ]
            
            if any(indicator in section_text.lower() for indicator in intervention_indicators):
                if section_name not in context["priority_sections"] and len(context["priority_sections"]) < 3:
                    context["priority_sections"].append(section_name)
        
        # Include steps/interventions section if concrete actions are mentioned
        steps_section = next((s for s in sections if "hakbang" in s), None)
        if steps_section and steps_section not in context["priority_sections"]:
            steps_text = sections.get(steps_section, "")
            action_verbs = ["gawin", "isagawa", "simulan", "ipatupad", "follow", "implement"]
            
            if any(verb in steps_text.lower() for verb in action_verbs):
                context["priority_sections"].append(steps_section)
        
        # Include care section if monitoring is emphasized
        care_section = next((s for s in sections if "pangangalaga" in s or "alaga" in s), None)
        if care_section and care_section not in context["priority_sections"]:
            care_text = sections.get(care_section, "")
            monitoring_terms = ["i-monitor", "bantayan", "subaybayan", "obserbahan", "warning signs"]
            
            if any(term in care_text.lower() for term in monitoring_terms):
                context["priority_sections"].append(care_section)
    
    # ENHANCED: Expanded severity terms with Filipino expressions
    severity_terms = {
        "high": ["malubha", "matindi", "severe", "significant", "napaka", "lubhang", "grabeng", 
                "masyadong", "sobrang", "critical", "emergency", "urgent", "nagbabanta", 
                "nakamamatay", "life-threatening", "maaaring ikasama", "mapanganib", 
                "napakalala", "grabe", "extremely", "highly", "seriously", "gravely"],
        "medium": ["katamtaman", "moderate", "banayad", "medyo", "may kaunting", "noticeable", 
                  "concerning", "kapansin-pansin", "nakakaapekto", "hindi masyado", 
                  "moderately", "somewhat", "partly", "relatively"],
        "low": ["bahagya", "mild", "minimal", "slight", "kaunti", "hindi gaanong", "limitado", 
               "hindi masyadong", "minor", "light", "slightly", "marginally", "occasionally"]
    }
    
    # ENHANCED: More comprehensive temporal markers
    temporal_patterns = [
        # Duration patterns
        r'(sa loob ng|for|ilang|maraming|several|many|few) (\d+\s+(?:araw|linggo|buwan|taon|day|week|month|year)s?)',
        r'(sa nakalipas na|for the past|since|simula|noon|mula) ([^.,:;]+)',
        
        # Frequency patterns
        r'(daily|araw-araw|lingguhan|weekly|monthly|buwanan|every|tuwing) ([^.,:;]+)',
        r'(madalas|often|occasionally|paminsan-minsan|rarely|bihira|sometimes|kung minsan) ([^.,:;]+)',
        
        # Progression patterns
        r'(lumalala|worsening|improving|bumubuti|nagbabago|changing|unstable|stable) ([^.,:;]+)',
        r'(dati|before|previously|dating|noon|recently|kamakailan|lately|nitong mga nakaraan) ([^.,:;]+)',
        
        # Time-specific patterns
        r'(umaga|morning|tanghali|noon|hapon|afternoon|gabi|evening|madaling-araw|dawn) ([^.,:;]+)',
        r'(habang|kapag|tuwing|during|after|pagkatapos|bago|before|while|when|upon) ([^.,:;]+)'
    ]
    
    # ENHANCED: More detailed impact patterns to detect how symptoms affect daily life
    impact_patterns = [
        r'(nakakaapekto sa|nagdudulot ng|nagiging sanhi ng|resulting in|causing|leading to) ([^.,:;]+)',
        r'(nakapipigil sa|preventing|limiting|restricting|hindering|nakakahadlang sa|pumipigil sa) ([^.,:;]+)',
        r'(naglilikha ng|dahilan ng|dahil sa|because of|due to|nagbubunga ng|nagdudulot ng|humahantong sa) ([^.,:;]+)',
        r'(nahihirapan sa|struggles with|having difficulty with|challenged by|hindi magawa ang|unable to) ([^.,:;]+)',
        r'(kailangan ng tulong sa|requires assistance with|needs help with|dependent on others for) ([^.,:;]+)',
        r'(hindi na kayang|can no longer|has lost the ability to|nawalan ng kakayahang) ([^.,:;]+)'
    ]
    
    # NEW: Detect care domains mentioned in the document
    care_domains = {
        "physical": ["physical", "mobility", "pagkilos", "paglalakad", "walking", "standing", 
                    "pagtayo", "strength", "lakas", "movement", "galaw", "balance", "balanse", 
                    "coordination", "koordinasyon", "pain", "sakit", "kirot", "vital signs"],
        "cognitive": ["memory", "memorya", "nakalimutan", "forgets", "cognition", "pag-iisip", 
                     "mental", "isip", "concentration", "pokus", "attention", "pagkalito", 
                     "confusion", "orientation", "awareness", "decision-making", "judgment"],
        "emotional": ["mood", "emotional", "emosyon", "depression", "anxiety", "pagkabalisa", 
                     "fear", "takot", "loneliness", "kalungkutan", "hopelessness", "isolation", 
                     "irritability", "agitation", "restlessness", "damdamin", "feelings"],
        "social": ["social", "relationships", "pakikisalamuha", "family", "pamilya", "friends", 
                  "kaibigan", "community", "komunidad", "interaction", "engagement", "participation", 
                  "isolation", "withdrawal", "pag-iwas", "support", "suporta"],
        "self_care": ["ADL", "activities of daily living", "self-care", "bathing", "pagliligo", 
                     "dressing", "pagbibihis", "eating", "pagkain", "toileting", "grooming", 
                     "hygiene", "kalinisan", "personal care", "sariling pangangalaga"],
        "medical": ["medication", "gamot", "treatment", "lunas", "paggamot", "therapy", 
                   "diagnosis", "condition", "karamdaman", "disease", "sakit", "symptoms", 
                   "sintomas", "prognosis", "management", "pamamahala", "healthcare"],
        "spiritual": ["spiritual", "espirituwal", "faith", "pananampalataya", "religion", 
                     "relihiyon", "prayer", "dasal", "belief", "paniniwala", "meaning", 
                     "purpose", "layunin", "values", "pagpapahalaga", "rituals"]
    }
    
    # Detect primary care domains in the document
    for domain, terms in care_domains.items():
        count = sum(all_text.lower().count(term) for term in terms)
        if count > 0:
            context["care_domains"][domain] = count
    
    # NEW: Detect cultural context elements
    cultural_elements = [
        # Filipino family dynamics
        "extended family", "malawak na pamilya", "multi-generational", "filial piety", "utang na loob",
        "pagrespeto sa nakakatanda", "pakikisama", "bayanihan", "malasakit", "hiya",
        
        # Traditional healing/beliefs
        "hilot", "albularyo", "faith healing", "dasal", "prayer", "herbal", "halamang gamot",
        "galing sa lupa", "tradisyonal na lunas", "pasma", "init-lamig", "tawas", "suob",
        
        # Religious aspects
        "simbahan", "church", "misa", "mass", "santo", "saint", "rosaryo", "rosary",
        "panalangin", "novena", "bendisyon", "blessing", "spiritual healing",
        
        # Folk beliefs and practices
        "usog", "kulam", "malas", "swerte", "pamahiin", "superstition", "bawal", "taboo",
        "bagong taon", "pista", "fiesta", "tradisyon", "paniniwala"
    ]
    
    # Check for cultural elements
    for element in cultural_elements:
        if element.lower() in all_text.lower():
            context["cultural_context"].append(element)
    
    # NEW: Detect beneficiary profile information
    age_patterns = [
        r'(\d+)[- ](?:years old|taong gulang|years|taon)',
        r'edad(?: ay)? (\d+)',
        r'age(?: of)? (\d+)'
    ]
    
    gender_patterns = [
        r'\b(male|female|lalaki|babae)\b',
        r'\b(lolo|lola|tatay|nanay)\b'  # Infer from role terms
    ]
    
    # Extract age
    for pattern in age_patterns:
        matches = re.finditer(pattern, all_text.lower())
        for match in matches:
            try:
                age = int(match.group(1))
                context["beneficiary_profile"]["age"] = age
                break
            except (ValueError, IndexError):
                continue
    
    # Extract gender
    for pattern in gender_patterns:
        matches = re.finditer(pattern, all_text.lower())
        for match in matches:
            gender_term = match.group(1).lower()
            if gender_term in ["male", "lalaki", "lolo", "tatay"]:
                context["beneficiary_profile"]["gender"] = "male"
                break
            elif gender_term in ["female", "babae", "lola", "nanay"]:
                context["beneficiary_profile"]["gender"] = "female"
                break
    
    # ENHANCED: More detailed condition-symptom relationship detection
    condition_symptoms = {}
    disease_entities = [ent.text.lower() for ent in doc.ents if ent.label_ == "DISEASE"]
    
    for disease in disease_entities:
        # Find symptoms that appear in same sentence as the disease
        symptoms = []
        for sent in doc.sents:
            sent_text = sent.text.lower()
            if disease in sent_text:
                for ent in sent.ents:
                    if ent.label_ == "SYMPTOM" and ent.text.lower() not in symptoms:
                        symptoms.append(ent.text.lower())
        
        if symptoms:
            condition_symptoms[disease] = symptoms
    
    context["condition_relationships"] = condition_symptoms
    
    return context

def extract_measurement_context(text, measurement_term, window_size=80):
    """Extract measurement values, patterns and interpretations around a measurement term."""
    context_data = {"value": None, "pattern": None, "interpretation": None}
    
    # Find position of the measurement term
    term_pos = text.lower().find(measurement_term.lower())
    if term_pos < 0:
        return context_data
    
    # Extract larger window around the term to capture more context
    start = max(0, term_pos - window_size)
    end = min(len(text), term_pos + len(measurement_term) + window_size)
    context = text[start:end]
    
    # Look for pattern descriptions - specifically for roller-coaster pattern
    if "roller-coaster" in text.lower():
        # Look for the complete description with the value
        pattern_match = re.search(r'roller-coaster pattern\s+([^.]+)', text, re.IGNORECASE)
        if pattern_match:
            full_context = pattern_match.group(0)
            # Check if there's a value mentioned
            value_match = re.search(r'(\d+\s*mg/dL)', full_context)
            if value_match:
                context_data["value"] = value_match.group(1)
                context_data["pattern"] = full_context
            else:
                context_data["pattern"] = full_context
    
    # Look for numeric values with units - expanded set
    value_patterns = [
        # Complete range patterns
        r'(\d+(?:\.\d+)?\s*-\s*\d+(?:\.\d+)?\s*(?:mg/dL|mmHg|kg|cm|lbs|%|bpm))',
        
        # Specific values with units
        r'(\d+(?:\.\d+)?\s*(?:mg/dL|mmHg|kg|cm|lbs|%|bpm|mcg|mL|L|g|mg|meq\/L))',
        
        # Blood pressure patterns
        r'(\d+/\d+\s*(?:mmHg)?)',
        
        # Percentage patterns
        r'(\d+(?:\.\d+)?%)',
        
        # Basic numeric followed by unit word
        r'(\d+(?:\.\d+)?\s*(?:percent|porsyento|degrees|units))',
        
        # Simple numeric values likely to be measurements
        r'(\d+(?:\.\d+)?)\s*(?=\s|$|\)|\.|,)',
        
        # Degree values (for temperature)
        r'(\d+(?:\.\d+)?)\s*(?:°C|°F|degrees?)',
        
        # Fractions and mixed numbers
        r'(\d+\s+\d+/\d+|\d+/\d+)',
        
        # Values with x format (e.g., "2x normal")
        r'(\d+(?:\.\d+)?x)',
        
        # Complex multi-part values
        r'(\d+(?:\.\d+)?/\d+(?:\.\d+)?/\d+(?:\.\d+)?)'  # e.g., lab value ratios
    ]
    
    # Try each pattern in order of specificity
    for pattern in value_patterns:
        match = re.search(pattern, context)
        if match:
            context_data["value"] = match.group(1)
            break
    
    # Look for pattern descriptions with expanded terms
    pattern_terms = [
        "lumampas sa", "exceeding", "mataas sa", "mababa sa", "below", 
        "fluctuating", "nagbabago", "hindi stable", "unstable", "irregular",
        "consistent", "regular", "stable", "roller-coaster", "spike", "significant",
        "paiba-iba", "palaging mataas", "bumababa", "tumataas", "variable",
        "tumaas ng", "bumaba ng", "elevated by", "decreased by", "increased to",
        "nasa normal range", "within normal limits", "labas sa normal"
    ]
    
    pattern_context = None
    for term in pattern_terms:
        if term in context.lower():
            # Get surrounding phrase containing pattern description
            pattern_pos = context.lower().find(term)
            pattern_start = max(0, pattern_pos - 15)
            pattern_end = min(len(context), pattern_pos + len(term) + 30)
            pattern_context = context[pattern_start:pattern_end].strip()
            
            # Clean up pattern context
            pattern_context = re.sub(r'^[^a-zA-Z0-9]+', '', pattern_context)
            pattern_context = re.sub(r'[^a-zA-Z0-9.]+$', '', pattern_context)
            
            context_data["pattern"] = pattern_context
            break
    
    # Look for interpretation with comprehensive terms
    interpretation_terms = [
        # Basic status terms
        "normal", "elevated", "abnormal", "critical", "stable", "unstable",
        "mataas", "mababa", "delikado", "mapanganib", "malubha", "kritikal",
        
        # Clinical interpretations
        "out of range", "borderline", "pre-diabetic", "hypertensive", "hypotensive", 
        "tachycardic", "bradycardic", "deteriorating", "improving", "controlled",
        "uncontrolled", "within target", "outside target", "bumubuti", "lumalala",
        
        # Filipino specific terms
        "nasa tamang antas", "hindi nasa tamang antas", "concerning", "alarming",
        "requires attention", "nangangailangan ng pansin", "hyperglycemic", "hypoglycemic",
        
        # General status descriptors
        "excessive", "insufficient", "optimal", "suboptimal", "therapeutic",
        "toxic", "deficient", "irregular", "unstable", "fluctuating",
        
        # Severity terms
        "mild", "moderate", "severe", "extreme", "banayad", "katamtaman", 
        "malala", "matindi", "napakataas", "napakababa"
    ]
    
    for term in interpretation_terms:
        if term in context.lower():
            near_term_pos = context.lower().find(term)
            near_term_start = max(0, near_term_pos - 10)
            near_term_end = min(len(context), near_term_pos + len(term) + 15)
            interpretation = context[near_term_start:near_term_end].strip()
            context_data["interpretation"] = interpretation
            break
    
    return context_data

def identify_cross_section_entities(section_elements):
    """Identify entities that appear across multiple sections with enhanced categorization."""
    cross_section = {
        # Medical categories
        "conditions": [],
        "symptoms": [],
        "body_parts": [],
        "treatments": [],
        "medications": [],
        "vital_signs": [],
        
        # Care-related categories
        "recommendations": [],
        "interventions": [],
        "monitoring_actions": [],
        "assistive_devices": [],
        
        # NEW: More detailed categories for comprehensive tracking
        "risk_factors": [],
        "functional_limitations": [],
        "emotional_states": [],
        "care_techniques": [],
        "social_supports": [],
        "environmental_factors": [],
        "dietary_elements": [],
        "safety_concerns": [],
        "prognosis_indicators": [],
        "cultural_factors": [],
        
        # NEW: Track relationships between entities
        "entity_relationships": {}
    }
    
    # Collect all entities of each type with expanded categories
    all_entities = {
        "conditions": {},
        "symptoms": {},
        "body_parts": {},
        "treatments": {},
        "medications": {},
        "vital_signs": {},
        "recommendations": {},
        "interventions": {},
        "monitoring_actions": {},
        "assistive_devices": {},
        "risk_factors": {},
        "functional_limitations": {},
        "emotional_states": {},
        "care_techniques": {},
        "social_supports": {},
        "environmental_factors": {},
        "dietary_elements": {},
        "safety_concerns": {},
        "prognosis_indicators": {},
        "cultural_factors": {}
    }
    
    # Count occurrences across sections with expanded mapping of entity types
    for section_name, elements in section_elements.items():
        # Traditional entity types
        for condition in elements.get("conditions", []):
            all_entities["conditions"][condition] = all_entities["conditions"].get(condition, 0) + 1
            
        for symptom in elements.get("symptoms", []):
            all_entities["symptoms"][symptom] = all_entities["symptoms"].get(symptom, 0) + 1
            
        for body_part in elements.get("body_parts", []):
            all_entities["body_parts"][body_part] = all_entities["body_parts"].get(body_part, 0) + 1
            
        for treatment in elements.get("treatments", []):
            all_entities["treatments"][treatment] = all_entities["treatments"].get(treatment, 0) + 1
            
        for medication in elements.get("medications", []):
            all_entities["medications"][medication] = all_entities["medications"].get(medication, 0) + 1
            
        for recommendation in elements.get("recommendations", []):
            all_entities["recommendations"][recommendation] = all_entities["recommendations"].get(recommendation, 0) + 1
            
        for vital_sign in elements.get("vital_signs", []):
            all_entities["vital_signs"][vital_sign] = all_entities["vital_signs"].get(vital_sign, 0) + 1
        
        # NEW: Expanded entity tracking
        for intervention in elements.get("intervention_methods", []):
            all_entities["interventions"][intervention] = all_entities["interventions"].get(intervention, 0) + 1
            
        for monitor in elements.get("monitoring_plans", []):
            all_entities["monitoring_actions"][monitor] = all_entities["monitoring_actions"].get(monitor, 0) + 1
            
        # Get assistive devices from the text context
        for device in elements.get("equipment", []):
            all_entities["assistive_devices"][device] = all_entities["assistive_devices"].get(device, 0) + 1
            
        # Track emotional states
        for emotion in elements.get("emotional_state", []):
            all_entities["emotional_states"][emotion] = all_entities["emotional_states"].get(emotion, 0) + 1
            
        # Track functional limitations
        for limitation in elements.get("limitations", []) + elements.get("activity_limitations", []):
            all_entities["functional_limitations"][limitation] = all_entities["functional_limitations"].get(limitation, 0) + 1
            
        # Track social supports
        for support in elements.get("social_support", []) + elements.get("caregivers", []):
            all_entities["social_supports"][support] = all_entities["social_supports"].get(support, 0) + 1
            
        # Track dietary elements
        for diet in elements.get("diet_changes", []):
            all_entities["dietary_elements"][diet] = all_entities["dietary_elements"].get(diet, 0) + 1
            
        # Track safety concerns
        for warning in elements.get("warnings", []):
            all_entities["safety_concerns"][warning] = all_entities["safety_concerns"].get(warning, 0) + 1
    
    # Find entities appearing in multiple sections with more comprehensive detection
    for category, entities in all_entities.items():
        cross_section[category] = [entity for entity, count in entities.items() if count > 1]
    
    # NEW: Identify relationships between entities across sections
    # For example, find symptoms related to specific conditions
    condition_symptom_map = {}
    body_part_symptom_map = {}
    treatment_condition_map = {}
    
    # Process each section's elements to find relationships
    for section_name, elements in section_elements.items():
        conditions = elements.get("conditions", [])
        symptoms = elements.get("symptoms", [])
        body_parts = elements.get("body_parts", [])
        treatments = elements.get("treatments", [])
        
        # Map conditions to symptoms that appear in same section
        for condition in conditions:
            if condition not in condition_symptom_map:
                condition_symptom_map[condition] = []
            for symptom in symptoms:
                if symptom not in condition_symptom_map[condition]:
                    condition_symptom_map[condition].append(symptom)
                    
        # Map body parts to symptoms
        for body_part in body_parts:
            if body_part not in body_part_symptom_map:
                body_part_symptom_map[body_part] = []
            for symptom in symptoms:
                if symptom not in body_part_symptom_map[body_part]:
                    body_part_symptom_map[body_part].append(symptom)
                    
        # Map treatments to conditions
        for treatment in treatments:
            if treatment not in treatment_condition_map:
                treatment_condition_map[treatment] = []
            for condition in conditions:
                if condition not in treatment_condition_map[treatment]:
                    treatment_condition_map[treatment].append(condition)
    
    # Store relationships in the cross_section object
    cross_section["entity_relationships"]["condition_symptoms"] = condition_symptom_map
    cross_section["entity_relationships"]["body_part_symptoms"] = body_part_symptom_map
    cross_section["entity_relationships"]["treatment_conditions"] = treatment_condition_map
    
    return cross_section

def get_relevant_entities_for_section(section_name, cross_section_entities, doc_context):
    """Get cross-section entities relevant to a specific section."""
    relevant = {}
    
    # Add all cross-section entities that are relevant to this section type
    if section_name == "mga_sintomas" or "sintomas" in section_name:
        relevant["conditions"] = cross_section_entities.get("conditions", [])
        relevant["symptoms"] = cross_section_entities.get("symptoms", [])
        
    elif section_name == "kalagayan_pangkatawan" or "pangkatawan" in section_name:
        relevant["body_parts"] = cross_section_entities.get("body_parts", [])
        relevant["vital_signs"] = cross_section_entities.get("vital_signs", [])
        
    elif section_name == "pangunahing_rekomendasyon" or "rekomendasyon" in section_name:
        relevant["recommendations"] = cross_section_entities.get("recommendations", [])
        
    elif section_name == "mga_hakbang" or "hakbang" in section_name:
        relevant["treatments"] = cross_section_entities.get("treatments", [])
    
    # Add document-level context elements if available
    if doc_context.get("key_entities"):
        key_entities = doc_context["key_entities"]
        
        # Add disease entities for symptom sections
        if (section_name == "mga_sintomas" or "sintomas" in section_name) and key_entities.get("DISEASE"):
            if "conditions" not in relevant:
                relevant["conditions"] = []
            for disease in key_entities["DISEASE"]:
                if disease not in relevant["conditions"]:
                    relevant["conditions"].append(disease)
    
    return relevant

def get_semantic_relationship(content1, content2):
    """Determine the semantic relationship between two pieces of content."""
    # Simple keyword-based relationship detection
    causation_terms = ["dahil", "sanhi", "resulting in", "caused by", "leads to", "bunga ng"]
    contrast_terms = ["subalit", "ngunit", "however", "on the other hand", "in contrast"]
    addition_terms = ["bukod", "dagdag", "din", "rin", "additionally", "moreover"]
    
    content = content1.lower() + " " + content2.lower()
    
    if any(term in content for term in causation_terms):
        return "causation"
    elif any(term in content for term in contrast_terms):
        return "contrast"
    elif any(term in content for term in addition_terms):
        return "addition"
    else:
        return "neutral"
    
def detect_health_narrative_patterns(prev_content, next_content):
    """Detect specific health narrative patterns in adjacent content pieces with enhanced evaluation patterns."""
    combined_text = f"{prev_content} {next_content}".lower()
    
    # Symptom-to-condition pattern
    symptom_condition_pattern = any(term in prev_content.lower() for term in [
        "sintomas", "symptoms", "nakakaramdam", "sumasakit", "masakit", "nagpapakita"
    ]) and any(term in next_content.lower() for term in [
        "diagnosis", "kondisyon", "condition", "disease", "disorder", "karamdaman", "sakit"
    ])
    if symptom_condition_pattern:
        return "symptom_to_condition"
    
    # Condition-to-treatment pattern
    condition_treatment_pattern = any(term in prev_content.lower() for term in [
        "kondisyon", "condition", "diagnosis", "disease", "disorder", "sakit", "karamdaman"
    ]) and any(term in next_content.lower() for term in [
        "treatment", "therapy", "intervention", "medication", "gamot", "lunas", "paggamot"
    ])
    if condition_treatment_pattern:
        return "condition_to_treatment"
    
    # Pain-to-medication pattern
    pain_medication_pattern = any(term in prev_content.lower() for term in [
        "sakit", "pain", "masakit", "pananakit", "kirot", "nararamdaman", "sumasakit"
    ]) and any(term in next_content.lower() for term in [
        "medication", "gamot", "pain relief", "pain management", "analgesic", "pampakalma"
    ])
    if pain_medication_pattern:
        return "pain_to_medication"
    
    # Cause-to-effect pattern in health context
    cause_effect_pattern = any(term in combined_text for term in [
        "sanhi", "caused by", "nagdudulot", "resulting in", "dahil sa", "dahilan ng", 
        "because of", "kaya", "kung kaya't", "leads to", "humahantong sa"
    ])
    if cause_effect_pattern:
        return "causation"
    
    # Mental-physical connection pattern
    mental_physical_pattern = any(term in prev_content.lower() for term in [
        "anxiety", "depression", "stress", "worry", "mental", "emotional", "kabalisahan",
        "pagkabalisa", "kalungkutan", "pag-aalala", "kaisipan", "emosyon", "damdamin"
    ]) and any(term in next_content.lower() for term in [
        "physical symptoms", "stomach", "headache", "tension", "sipon", "sakit ng ulo", 
        "pananakit ng tiyan", "pagkahilo", "hirap huminga", "palpitations", "insomnia"
    ])
    if mental_physical_pattern:
        return "mental_physical_connection"
    
    # Check for recommendation pattern following assessment
    if any(term in prev_content.lower() for term in ["observed", "assessed", "found", "nakita", "napansin"]) and \
       any(term in next_content.lower() for term in ["recommended", "advised", "suggested", "inirerekomenda", "iminumungkahi"]):
        return "assessment_to_recommendation"
    
    # Check for implementation following recommendation
    if any(term in prev_content.lower() for term in ["recommended", "advised", "inirerekomenda", "iminumungkahi"]) and \
       any(term in next_content.lower() for term in ["implement", "apply", "administer", "isagawa", "ipatupad"]):
        return "recommendation_to_implementation"
    
    # Check for safety precaution pattern
    safety_precaution_pattern = any(term in combined_text for term in [
        "upang maiwasan ang", "to prevent", "para makaiwas sa", "to avoid", 
        "risk reduction", "pagbawas ng panganib", "safety measure", "hakbang pangkaligtasan"
    ])
    if safety_precaution_pattern:
        return "safety_precaution"
    
    # Check for evaluation outcome pattern
    evaluation_outcome_pattern = any(term in combined_text for term in [
        "as a result", "bilang resulta", "outcome of", "resulta ng", 
        "improved", "bumuti", "declined", "lumala", "no change", "walang pagbabago"
    ])
    if evaluation_outcome_pattern:
        return "evaluation_outcome"
    
    # Check for education-response pattern (common in evaluations)
    education_pattern = any(term in prev_content.lower() for term in [
        "tinuruan", "taught", "explained", "ipinaliwanag", "educated", "showed", "ipinakita"
    ]) and any(term in next_content.lower() for term in [
        "understood", "naintindihan", "learned", "natuto", "demonstrated", "ipinakita", 
        "was able to", "nagawa niya", "can now", "kaya na niyang"
    ])
    if education_pattern:
        return "education_response"
    
    # Return None if no patterns match
    return None

def get_contextual_relationship(prev_content, next_content, doc_context, prev_idx, next_idx):
    """Determine the contextual relationship between sentences with improved Filipino context awareness."""
    # Start with basic semantic relationship
    basic_relationship = get_semantic_relationship(prev_content, next_content)
    
    # Check if sentences are from same or different sections
    if prev_idx in doc_context.get("sentence_section_map", {}) and next_idx in doc_context.get("sentence_section_map", {}):
        prev_section = doc_context["sentence_section_map"].get(prev_idx)
        next_section = doc_context["sentence_section_map"].get(next_idx)
        
        # If moving to a new section, relationship is often topical shift
        if prev_section != next_section:
            # UPDATED WITH NEW EVALUATION SECTIONS
            
            # Call the already defined health narrative patterns function
            health_pattern = detect_health_narrative_patterns(prev_content, next_content)
            if health_pattern:
                return health_pattern
            
            # Action-oriented content transitions
            if next_section in ["mga_hakbang", "pangangalaga", "pangunahing_rekomendasyon", 
                              "pamamahala_ng_gamot", "safety_risk_factors"]:
                return "action"  # Moving to action-oriented content
                
            # Contextual information transitions  
            elif next_section in ["kalagayan_social", "suporta_ng_pamilya"]:
                return "context"  # Social and family details provide important context
                
            # Implementation/practical transitions
            elif next_section in ["pagbabago_sa_pamumuhay", "nutrisyon_at_pagkain", 
                                "mobility_function", "preventive_health"]:
                return "implementation"  # Moving to practical implementation
                
            # Physical manifestation transitions
            elif next_section in ["kalagayan_pangkatawan", "kalusugan_ng_bibig", 
                                "pain_discomfort", "vital_signs_measurements"] and prev_section == "mga_sintomas":
                return "elaboration"  # Physical details elaborate on symptoms
                
            # Mind-body connection transitions
            elif next_section == "kalagayan_mental" and prev_section in ["mga_sintomas", "kalagayan_pangkatawan"]:
                return "holistic"  # Mental aspects complement physical in holistic care
                
            # Sleep and rest transitions  
            elif next_section == "kalagayan_ng_tulog" and prev_section in ["kalagayan_mental", "pain_discomfort"]:
                return "consequence"  # Sleep issues often result from pain/mental state
                
            # Medical history transitions
            elif next_section == "medical_history":
                return "background"  # Medical history provides background context
                
            # NEW: Medication to oral health relationship
            elif next_section == "kalusugan_ng_bibig" and prev_section == "pamamahala_ng_gamot":
                return "specific_application"  # Oral health as specific application of medication management
                
            # NEW: Safety to mobility relationship
            elif (next_section == "mobility_function" and prev_section == "safety_risk_factors") or \
                 (next_section == "safety_risk_factors" and prev_section == "mobility_function"):
                return "related_concern"  # Safety and mobility are closely related concerns
                
            # NEW: Sleep to mental health relationship
            elif (next_section == "kalagayan_mental" and prev_section == "kalagayan_ng_tulog") or \
                 (next_section == "kalagayan_ng_tulog" and prev_section == "kalagayan_mental"):
                return "interdependent"  # Sleep and mental health are interdependent
                
            # NEW: Nutrition to preventive health
            elif (next_section == "preventive_health" and prev_section == "nutrisyon_at_pagkain"):
                return "preventative_measure"  # Nutrition as preventive health measure
                
            # NEW: Vital signs to medication management
            elif (next_section == "pamamahala_ng_gamot" and prev_section == "vital_signs_measurements"):
                return "monitoring_adjustment"  # Vital signs monitoring for medication adjustment

            # Medical history transitions
            elif next_section == "medical_history" and prev_section in ["mga_sintomas", "kalagayan_pangkatawan"]:
                return "background"  # Medical history provides background context

            # Add a new relationship type
            elif prev_section == "medical_history" and next_section in ["pamamahala_ng_gamot", "preventive_health"]:
                return "risk_basis"  # Medical history as basis for treatment/prevention decisions

            else:
                return "topical_shift"  # General shift in topic
    
    # NEW: Check for health narrative patterns that might span multiple sections
    if detect_health_narrative_patterns(prev_content, next_content):
        return detect_health_narrative_patterns(prev_content, next_content)

    # NEW: Check for question-answer pattern (common in assessment narratives)
    question_indicators = ["?", "kumusta", "paano", "bakit", "kailan", "saan", "sino", "ano", 
                           "how", "why", "when", "where", "who", "what"]
    if any(indicator in prev_content.lower() for indicator in question_indicators):
        if not any(indicator in next_content.lower() for indicator in question_indicators):
            return "answer"  # Next content answers the previous question
    
    # Check for causal relationship in document context
    if doc_context.get("condition_relationships"):
        # Look for causal chains captured in document context
        for condition, effects in doc_context["condition_relationships"].items():
            if condition.lower() in prev_content.lower() and any(effect.lower() in next_content.lower() for effect in effects):
                return "causation"
    
    # NEW: Check for temporal relationship (sequence/chronology)
    time_indicators_prev = ["una", "first", "initially", "simula", "dati", "before", "previously", "noon"]
    time_indicators_next = ["pagkatapos", "after", "then", "later", "eventually", "sunod", "kasunod", "ngayon", "now", "currently"]
    
    if any(indicator in prev_content.lower() for indicator in time_indicators_prev) and \
       any(indicator in next_content.lower() for indicator in time_indicators_next):
        return "temporal_sequence"
    
    # NEW: Check for comparison relationship
    comparison_indicators = ["mas", "higit", "kulang", "more", "less", "better", "worse", 
                            "improved", "declined", "kaysa", "compared to", "relative to", 
                            "mataas", "mababa", "higher", "lower"]
                            
    if any(indicator in next_content.lower() for indicator in comparison_indicators):
        return "comparison"
    
    # NEW: Check for recommendation following assessment pattern
    assessment_terms = ["nakita", "naobserbahan", "observed", "assessed", "evaluated", "found", 
                       "napansin", "noticed", "documented", "recorded", "measured"]
    recommendation_terms = ["inirerekomenda", "ipinapayo", "kailangan", "dapat", "recommended", 
                           "advised", "needed", "required", "suggested", "necessary", "important"]
                           
    if any(term in prev_content.lower() for term in assessment_terms) and \
       any(term in next_content.lower() for term in recommendation_terms):
        return "assessment_to_recommendation"
    
    # NEW: Check for Filipino cultural context-specific relationships
    cultural_terms_prev = ["paniniwala", "belief", "tradisyon", "tradition", "kultura", "culture", 
                         "pamahiin", "superstition", "kagawian", "practice", "values", "pagpapahalaga"]
                        
    cultural_impact_terms = ["kaya", "so", "therefore", "thus", "hence", "dahil dito", "as a result", 
                           "because of this", "dahilan", "reason"]
                           
    if any(term in prev_content.lower() for term in cultural_terms_prev) and \
       any(term in next_content.lower() for term in cultural_impact_terms):
        return "cultural_reasoning"
    
    # NEW: Check for clarification/elaboration relationship
    clarification_terms = ["ibig sabihin", "meaning", "that is", "in other words", "specifically", 
                         "particularly", "halimbawa", "example", "illustrates", "clarifies", 
                         "elaborates", "explains", "demonstrates", "shows"]
                         
    if any(term in next_content.lower() for term in clarification_terms):
        return "elaboration"
    
    # Look for contrasting elements with expanded list
    contrast_indicators = ["ngunit", "pero", "however", "subalit", "on the other hand", "sa kabilang banda", 
                         "gayunpaman", "nonetheless", "still", "yet", "bagama't", "although", 
                         "kahit na", "despite", "in spite of", "sabagay", "datapwat"]
                         
    if any(indicator in next_content.lower() for indicator in contrast_indicators):
        return "contrast"
    
    # Check for elaboration pattern with cross-section themes
    if any(term in prev_content.lower() for term in doc_context.get("cross_section_themes", [])) and \
       any(term in next_content.lower() for term in doc_context.get("cross_section_themes", [])):
        return "elaboration"
    
    # Default to basic semantic relationship if no specific pattern is found
    return basic_relationship

# Add this at global scope
_used_transitions = set()  # Track recently used transitions

def determine_optimal_section_order(doc_context, doc_type):
    """Determine the optimal order of sections based on enhanced document context analysis."""
    # Default orders based on document type
    if doc_type.lower() == "assessment":
        # Assessment order (already updated)
        default_order = [
            "mga_sintomas",                # Symptoms - high priority
            "kalagayan_pangkatawan",       # Physical condition,
            "medical_history",             # Medical history background - moved up for logical flow
            "pain_discomfort",             # Pain/discomfort
            "vital_signs_measurements",    # Vital signs
            "kalagayan_mental",            # Mental/cognitive state
            "kalagayan_ng_tulog",          # Sleep pattern
            "mobility_function",           # Mobility capabilities
            "aktibidad",                   # Activities/functional status
            "kalusugan_ng_bibig",          # Oral/dental health
            "kalagayan_social",            # Social situation
            "suporta_ng_pamilya",          # Family support
            "pamamahala_ng_gamot",         # Medication management
            "medical_history",             # Medical history background
            "hygiene",                      # Hygiene habits
            "preventive_health"            # Preventive health measures
        ]
    else:  # Evaluation document - improved order based on clinical logic
        default_order = [
            "pangunahing_rekomendasyon",   # Key recommendations - always first
            "safety_risk_factors",         # Safety concerns - high priority 
            "mga_hakbang",                 # Action steps
            "mobility_function",           # Mobility and function
            "pamamahala_ng_gamot",         # Medication management
            "nutrisyon_at_pagkain",        # Nutrition and diet
            "kalusugan_ng_bibig",          # Oral health
            "kalagayan_ng_tulog",          # Sleep management
            "kalagayan_mental",            # Mental/emotional support
            "suporta_ng_pamilya",          # Family support
            "pangangalaga",                # Care needs
            "preventive_health",           # Preventive measures
            "vital_signs_measurements",    # Vital signs monitoring
            "pagbabago_sa_pamumuhay"       # Lifestyle changes
        ]
    
    # NEW: Consider severity when ordering assessment sections
    severity_trend = doc_context.get("severity_trend")
    if severity_trend == "malubha" and doc_type.lower() == "assessment":
        # For severe cases, prioritize physical and symptom information first
        critical_order = ["mga_sintomas", "kalagayan_pangkatawan"]
        non_critical = [s for s in default_order if s not in critical_order]
        default_order = critical_order + non_critical
    
    # Use priority sections from context to adjust order
    priority_sections = doc_context.get("priority_sections", [])
    
    # NEW: Consider care domains for ordering sections
    care_domains = doc_context.get("care_domains", {})
    if care_domains:
        # Find the dominant care domains (top 2)
        sorted_domains = sorted(care_domains.items(), key=lambda x: x[1], reverse=True)
        dominant_domains = [domain for domain, count in sorted_domains[:2]]
        
        # Map domains to sections
        domain_section_map = {
            "physical": ["kalagayan_pangkatawan", "mga_sintomas"],
            "cognitive": ["kalagayan_mental"],
            "emotional": ["kalagayan_mental"],
            "social": ["kalagayan_social"],
            "self_care": ["aktibidad"],
            "medical": ["mga_sintomas", "kalagayan_pangkatawan"],
            "spiritual": ["kalagayan_social"]
        }
        
        # Add sections related to dominant domains to priorities
        for domain in dominant_domains:
            related_sections = domain_section_map.get(domain, [])
            for section in related_sections:
                if section not in priority_sections and section in default_order:
                    priority_sections.append(section)
    
    # NEW: Consider beneficiary profile when ordering
    beneficiary = doc_context.get("beneficiary_profile", {})
    if beneficiary:
        age = beneficiary.get("age")
        # For very elderly (80+), prioritize physical status and activities
        if age and age >= 80 and doc_type.lower() == "assessment":
            age_priority = ["kalagayan_pangkatawan", "aktibidad"]
            for section in age_priority:
                if section not in priority_sections and section in default_order:
                    priority_sections.append(section)
    
    # NEW: For evaluation documents, consider condition relationships
    if doc_type.lower() == "evaluation" and "condition_relationships" in doc_context:
        # If there are clear condition-symptom relationships, ensure recommendations address them
        if doc_context["condition_relationships"]:
            if "pangunahing_rekomendasyon" not in priority_sections:
                priority_sections.insert(0, "pangunahing_rekomendasyon")
    
    # If we have priority sections, move them to the front while maintaining relative order
    if priority_sections:
        # Start with non-priority sections in default order
        non_priority = [s for s in default_order if s not in priority_sections]
        
        # Maintain default order for priority sections
        ordered_priorities = [s for s in default_order if s in priority_sections]
        
        # Combine priorities first, then non-priorities
        return ordered_priorities + non_priority
    
    # NEW: Consider cultural context for section ordering
    cultural_elements = doc_context.get("cultural_context", [])
    if any(term in ["family", "pamilya", "social support", "extended family"] for term in cultural_elements):
        # In Filipino culture, family/social context is often emphasized
        if "kalagayan_social" in default_order:
            # Move social context earlier in assessment
            default_order.remove("kalagayan_social")
            # Place after physical but before other sections
            default_order.insert(2, "kalagayan_social")
    
    return default_order

def detect_oral_medication_context(text):
    """Determine if text is about oral medication vs oral/dental health."""
    # Dental/oral health specific terms
    dental_terms = [
        "ngipin", "teeth", "tooth", "gums", "gilagid", "dentist", "dentista", 
        "pagsesepilyo", "toothbrush", "floss", "pustiso", "dentures", 
        "dental checkup", "cavity", "cavities", "filling", "root canal"
    ]
    
    # Medication specific terms that might include "oral"
    medication_terms = [
        "oral medication", "oral tablet", "oral capsule", "oral solution",
        "oral suspension", "oral drug", "oral administration", "oral dosage",
        "oral route", "oral formulation"
    ]
    
    # Count dental vs medication terms
    dental_count = sum(1 for term in dental_terms if term.lower() in text.lower())
    medication_count = sum(1 for term in medication_terms if term.lower() in text.lower())
    
    # Check for medication education context
    medication_education = any(phrase in text.lower() for phrase in [
        "explanation sheet", "medication guide", "drug information",
        "patient education", "side effects", "benefits versus risks",
        "package insert", "medication fears", "misconceptions"
    ])
    
    # Determine the predominant context
    if medication_count > dental_count or medication_education:
        return "medication"
    elif dental_count > 0:
        return "dental"
    else:
        return "neutral"