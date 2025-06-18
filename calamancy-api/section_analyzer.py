import re
from nlp_loader import nlp
from entity_extractor import extract_structured_elements
from text_processor import split_into_sentences

def extract_sections_improved(sentences, doc_type="assessment"):
    """Extract and categorize sections using advanced linguistic analysis and pattern matching."""
    print(f"Extracting sections for {doc_type}, {len(sentences)} sentences")
    
    # Process all sentences with Calamancy NLP first
    try:
        sentence_docs = [nlp(sent) for sent in sentences]
    except Exception as e:
        print(f"Error processing sentences: {e}")
        sentence_docs = [None] * len(sentences)
    
    # Define section keywords with expanded terms for better matching
    section_keywords = {
        "mga_sintomas": [
            "sintomas", "sakit", "nararamdaman", "sumasakit", "masakit", "kirot", 
            "nagpapakita", "kondisyon", "lumalala", "bumubuti", "symptoms",
            "nahihirapan", "dumaranas", "nakakaramdam", "condition", "naobserbahan",
            "napansin", "nakita", "issues", "problema", "nagdurusa", "mahina",
            "pagbabago", "change", "kakaiba", "abnormal", "unusual", "hindi normal",
            # Enhanced symptom keywords
            "pananakit", "lumalala", "pagbabago", "episode", "attack",
            "kombulsyon", "namamanhid", "numbness", "tusok-tusok",
            "difficulty", "hindi makagalaw", "hindi makatulog", "insomnia",
            "palaging", "persistent", "chronic", "paulit-ulit", "recurring",
            "paranoia", "agitation", "confusion", "hallucination"
        ],
        "kalagayan_pangkatawan": [
            "pisikal", "physical", "katawan", "body", "lakas", "strength", "bigat", "weight",
            "timbang", "tangkad", "height", "vital signs", "temperatura", "temperature",
            "pagkain", "eating", "paglunok", "swallowing", "paglakad", "walking",
            "balanse", "balance", "paggalaw", "movement", "koordinasyon", "coordination",
            "panginginig", "tremors", "nanghihina", "weakness", "pagod", "fatigue",
            "paglalakad", "mobility", "joints", "kasukasuan", "namamaga", "swelling",
            # Enhanced physical condition keywords
            "blood pressure", "presyon", "heart rate", "pulso", "respiratory",
            "paghinga", "oxygen", "sugar level", "glucose", "hydration", "dehydration",
            "nutrisyon", "pagbaba ng timbang", "pagtaba", "edema", "pamamaga",
            "kakayahang gumalaw", "stamina", "lakas ng katawan", "posture"
        ],
        "kalagayan_mental": [
            "mental", "isip", "cognitive", "cognition", "pag-iisip", "memorya", "memory",
            "nakalimutan", "forget", "pagkalito", "confusion", "disorientation",
            "hindi makapag-concentrate", "concentration", "hindi makafocus", "focus",
            "pagkataranta", "agitation", "irritable", "mairita", "emotional", "emosyonal",
            "kalungkutan", "depression", "lungkot", "sad", "malungkot", "mood", "estado ng isip",
            "paranoia", "pagdududa", "suspicion", "doubt", "pag-aalala", "worry", "anxiety",
            "stress", "pressure", "tension",
            # Enhanced mental state keywords
            "orientation", "oryentasyon", "awareness", "pagkakaalam", "alertness",
            "responsiveness", "pagtugon", "attention span", "atensyon", 
            "decision-making", "pagpapasya", "judgment", "paghatol", "reasoning",
            "pangangatwiran", "delusions", "pagkabaliw", "psychosis", 
            "mood swings", "pagbabago ng mood", "personality changes", 
            "behavior changes", "pagbabago ng ugali", "fears", "takot", 
            "dementia", "demensya", "Alzheimer's", "cognitive decline"
        ],
        "aktibidad": [
            "aktibidad", "activities", "gawain", "task", "daily living", "araw-araw",
            "routine", "gawing", "self-care", "personal care", "pangangalaga sa sarili",
            "hygiene", "kalinisan", "pagligo", "bathing", "pagbibihis", "dressing",
            "pagkain", "eating", "pagluluto", "cooking", "paglilinis", "cleaning",
            "exercise", "ehersisyo", "therapy", "therapiya", "hobbies", "libangan",
            "social activities", "pakikisalamuha", "pakikipag-usap", "communication",
            # Enhanced activity keywords
            "mobility", "paggalaw", "ambulation", "paglalakad", "transfers",
            "paglipat", "bed mobility", "paggalaw sa kama", "independence",
            "dependence", "pag-asa sa iba", "tungkod", "cane", "walker",
            "wheelchair", "silya de gulong", "crutches", "saklay", 
            "transportasyon", "lakad", "pamimili", "gawaing bahay"
        ],
        "kalagayan_social": [
            "relasyon", "relationship", "pamilya", "family", "social", "pakikisalamuha",
            "kaibigan", "friends", "komunidad", "community", "suporta", "support",
            "pakikipag-usap", "communication", "pakikipag-interact", "interaction",
            "asawa", "spouse", "anak", "children", "kamag-anak", "relatives",
            "kapitbahay", "neighbors", "kakilala", "acquaintances", "visitors",
            "bisita", "group", "organization", "samahan",
            # Enhanced social keywords
            "socialization", "pakikihalubilo", "isolation", "pagkakahiwalay",
            "loneliness", "kalungkutan", "withdrawal", "pag-iwas", "social network",
            "involvement", "participation", "pakikilahok", "church", "simbahan",
            "volunteer", "boluntaryo", "caregiver", "tagapag-alaga", 
            "tulong", "financial support", "sustento", "living situation",
            "tirahan", "kapangyarihan sa bahay", "household dynamics"
        ]
    }
    
    # Initialize scoring for each sentence-section pair with improved weights
    sentence_scores = {}
    for i, (sent, doc) in enumerate(zip(sentences, sentence_docs)):
        if not doc:
            continue
            
        sentence_scores[i] = {}
        
        # Get sentence length for normalization
        sent_length = len(sent.split())
        
        for section, keywords in section_keywords.items():
            # Calculate base score from keyword matches
            base_score = 0
            for keyword in keywords:
                if keyword.lower() in sent.lower():
                    # Give higher score to exact matches
                    if f" {keyword.lower()} " in f" {sent.lower()} ":
                        base_score += 1.5  # Full word match
                    else:
                        base_score += 1.0  # Partial match
            
            # Normalize by sentence length to avoid favoring long sentences
            if sent_length > 20:
                base_score = base_score * (20 / sent_length) * 1.5
            
            # Enhance score based on entity types present
            entity_boost = 0
            if doc.ents:
                for ent in doc.ents:
                    if section == "mga_sintomas" and ent.label_ in ["SYMPTOM", "DISEASE"]:
                        entity_boost += 3
                    elif section == "kalagayan_pangkatawan" and ent.label_ in ["BODY_PART", "MEASUREMENT"]:
                        entity_boost += 2.5
                    elif section == "kalagayan_mental" and ent.label_ in ["COGNITIVE", "EMOTION"]:
                        entity_boost += 2.5
                    elif section == "aktibidad" and ent.label_ in ["ADL"]:
                        entity_boost += 2.5
                    elif section == "kalagayan_social" and ent.label_ in ["SOCIAL_REL", "PER", "ENVIRONMENT"]:
                        entity_boost += 2
            
            # First few sentences often provide overview/symptoms
            if i < 2 and section == "mga_sintomas":
                base_score += 1
            
            # Save the combined score
            sentence_scores[i][section] = base_score + entity_boost
    
    # Assign sentences to sections based on scores
    result = {}
    assigned_sentences = set()
    
    # Initialize all sections to empty arrays
    for section in section_keywords.keys():
        result[section] = []
    
    # FIRST PASS: Assign sentences with clear high scores
    threshold = 2.5  # Higher threshold for clear assignment
    for section in section_keywords.keys():
        sorted_sentences = sorted(sentence_scores.items(), 
                                  key=lambda x: -x[1].get(section, 0))
        
        # Take up to 5 sentences with high scores
        count = 0
        max_sentences = 5
        
        for i, scores in sorted_sentences:
            if i in assigned_sentences:
                continue
                
            section_score = scores.get(section, 0)
            next_best_score = max([s for k, s in scores.items() if k != section], default=0)
            
            # Only assign if score is high and clearly better than other sections
            if (section_score >= threshold and 
                section_score > next_best_score * 1.25 and
                count < max_sentences):
                
                result[section].append(sentences[i])
                assigned_sentences.add(i)
                count += 1
    
    # SECOND PASS: Assign remaining sentences to their best-matching section
    section_counts = {s: len(sents) for s, sents in result.items()}
    max_sentences_per_section = 5
    
    for i, scores in sorted(sentence_scores.items(), 
                           key=lambda x: -max(x[1].values() if x[1] else [0])):
        if i not in assigned_sentences and any(scores.values()):
            best_section = max(scores.items(), key=lambda x: x[1])[0]
            
            # Only add if we haven't reached the max sentences for this section
            if section_counts.get(best_section, 0) < max_sentences_per_section:
                result[best_section].append(sentences[i])
                assigned_sentences.add(i)
                section_counts[best_section] = section_counts.get(best_section, 0) + 1
    
    # THIRD PASS: Ensure sentences are in logical order within each section
    for section in result:
        # Get the indices of assigned sentences and sort them
        indices = [i for i, sent in enumerate(sentences) if sent in result[section]]
        # Reorder sentences based on original order
        result[section] = [sentences[i] for i in sorted(indices)]
    
    # Ensure at least one section has content
    if all(not sents for sents in result.values()) and sentences:
        result["mga_sintomas"] = sentences[:5]  # Limit to 5 sentences
    
    # Convert lists to strings
    return {section: " ".join(sents) for section, sents in result.items() if sents}

def extract_sections_for_evaluation(sentences):
    """Extract sections specific to evaluation documents using pattern-based approach."""
    print(f"Extracting evaluation-specific sections from {len(sentences)} sentences")
    
    # Process sentences with NLP
    sentence_docs = [nlp(sent) for sent in sentences]
    
    # Initialize sections
    sections = {
        "pangunahing_rekomendasyon": [],
        "mga_hakbang": [],
        "pangangalaga": [],
        "pagbabago_sa_pamumuhay": []
    }
    
    # Strong signal patterns for each section with expanded patterns
    section_patterns = {
        "pangunahing_rekomendasyon": [
            r'inirerekomenda(ng)? (ko|kong|namin|naming) (na|ang)',
            r'iminumungkahi(ng)? (ko|kong|namin|naming) (na|ang)',
            r'pinapayuhan (ko|kong|namin|naming) (na|ang)',
            r'(una sa lahat|bilang pangunahing hakbang)',
            r'(dapat|kailangan|kinakailangan|mahalagang) (na )?',
            r'rekomendasyon',
            r'agarang pagkonsulta',
            r'immediate consultation',
            r'priority',
            r'most important',
            r'critical',
            r'crucial',
            r'essential',
            r'necessary',
            r'kailangang',
            r'kinakailangan',
            r'referral',
            r'irefer'
        ],
        
        "mga_hakbang": [
            r'(simulan|gawin|ipatupad|isagawa) ang',
            r'(susunod na hakbang|sa|mga|bilang) (hakbang|steps|interventions)',
            r'(dapat|kailangang) (din|rin) (na )?',
            r'(pangalawang|pangatlo|kasunod na) hakbang',
            r'procedure',
            r'process',
            r'method',
            r'technique',
            r'approach',
            r'implementation',
            r'implement',
            r'execute',
            r'perform',
            r'apply',
            r'administer',
            r'isagawa',
            r'gawin',
            r'therapy sessions',
            r'treatment course'
        ],
        
        "pangangalaga": [
            r'(para sa|upang|sa) (pangangalaga|pag-iwas|pag-aalaga)',
            r'(i-monitor|obserbahan|bantayan|subaybayan)',
            r'(sa pang-araw-araw na pangangalaga|daily care)',
            r'(sa bahay|home care|home management)',
            r'(kapag|kung|sa) (nagkaroon|nagkakaroon)',
            r'(palaging|regular na|always|consistently)',
            r'care',
            r'alaga',
            r'monitoring',
            r'pagbabantay',
            r'observation',
            r'pagmamasid',
            r'maintenance',
            r'management',
            r'hygiene',
            r'kalinisan',
            r'bathing',
            r'pagliligo',
            r'grooming',
            r'pag-aayos',
            r'positioning',
            r'pagpoposisyon'
        ],
        
        "pagbabago_sa_pamumuhay": [
            r'(pagbabago sa|baguhin ang|adjustment sa) (pamumuhay|lifestyle)',
            r'(diet|nutrisyon|nutrition|pagkain)',
            r'(exercise|ehersisyo|physical activity)',
            r'(normal na routine|daily habits|araw-araw)',
            r'(long-term|pangmatagalang|sa hinaharap|future)',
            r'lifestyle',
            r'pamumuhay',
            r'habits',
            r'ugali',
            r'practices',
            r'gawain',
            r'routines',
            r'modifications',
            r'adjustments',
            r'changes',
            r'pagbabago',
            r'diet plan',
            r'meal plan',
            r'exercise program',
            r'sleep',
            r'tulog',
            r'hydration',
            r'pag-inom ng tubig',
            r'stress management',
            r'relaxation',
            r'environment',
            r'kapaligiran'
        ]
    }
    
    # First pass: Match sentences to sections based on strong signals
    assigned_sentences = set()
    
    for section, patterns in section_patterns.items():
        # Limit to 5 sentences per section
        section_count = 0
        max_per_section = 5
        
        for i, sent in enumerate(sentences):
            if i in assigned_sentences or section_count >= max_per_section:
                continue
                
            # Check if sentence matches any pattern for this section
            for pattern in patterns:
                if re.search(pattern, sent.lower()):
                    sections[section].append(sent)
                    assigned_sentences.add(i)
                    section_count += 1
                    break
    
    # Second pass: Analyze entities for remaining sentences
    for i, (sent, doc) in enumerate(zip(sentences, sentence_docs)):
        if i in assigned_sentences:
            continue
            
        section_scores = {section: 0 for section in sections.keys()}
        
        # Score based on entities
        if doc.ents:
            for ent in doc.ents:
                if ent.label_ == "RECOMMENDATION":
                    section_scores["pangunahing_rekomendasyon"] += 2
                elif ent.label_ in ["TREATMENT_METHOD", "TREATMENT"]:
                    section_scores["mga_hakbang"] += 2
                elif ent.label_ in ["MONITORING", "HEALTHCARE_REFERRAL"]:
                    section_scores["pangangalaga"] += 2
                elif ent.label_ in ["DIET_RECOMMENDATION", "FOOD", "HOME_MODIFICATION"]:
                    section_scores["pagbabago_sa_pamumuhay"] += 2
        
        # Score based on keywords and context
        for section, patterns in section_patterns.items():
            for word in sent.split():
                if any(pattern.lower() in word.lower() for pattern in patterns):
                    section_scores[section] += 0.5
        
        # Assign to highest scoring section if score is significant
        best_section = max(section_scores.items(), key=lambda x: x[1])
        if best_section[1] >= 1.5:  # More stringent threshold
            if len(sections[best_section[0]]) < 5:  # Respect max 5 sentences per section
                sections[best_section[0]].append(sent)
                assigned_sentences.add(i)
    
    # Third pass: Default assignment of remaining important sentences
    remaining = [i for i in range(len(sentences)) if i not in assigned_sentences]
    
    # Prefer assigning introductory sentences to pangunahing_rekomendasyon
    for i in remaining:
        if i < 2:  # First two sentences
            if len(sections["pangunahing_rekomendasyon"]) < 5:
                sections["pangunahing_rekomendasyon"].append(sentences[i])
                assigned_sentences.add(i)
    
    # Sort sentences within each section to maintain original flow
    for section in sections:
        # Get the indices of assigned sentences and sort them
        indices = []
        for i, sent in enumerate(sentences):
            if sent in sections[section]:
                indices.append(i)
        
        # Reorder sentences based on original order
        sections[section] = [sentences[i] for i in sorted(indices)]
    
    # Convert lists to strings
    return {section: " ".join(sents) for section, sents in sections.items() if sents}

def summarize_section_text(section_text, section_name, max_length=350):
    """Create a concise summary of a potentially long section with proper sentence selection."""
    # Skip if the section is already short enough
    if len(section_text) <= max_length:
        return section_text
    
    # Extract sentences
    section_sentences = split_into_sentences(section_text)
    
    # If few sentences, just return them all (up to 5)
    if len(section_sentences) <= 5:
        return section_text
        
    # Fix incomplete sentences - common issue in extracted sections
    fixed_sentences = []
    for i, sent in enumerate(section_sentences):
        if len(sent) < 15 and i > 0:  # Very short sentence might be a fragment
            fixed_sentences[-1] = fixed_sentences[-1] + " " + sent
        else:
            fixed_sentences.append(sent)
    
    # If we have a reasonable number of fixed sentences, use them
    if 1 <= len(fixed_sentences) <= 5:
        return " ".join(fixed_sentences)
    
    # For longer sections, select most representative sentences
    # Always include first sentence (provides context)
    selected_sentences = [fixed_sentences[0]]
    
    # Get key terms for this section
    section_terms = []
    if section_name in section_key_terms:
        section_terms = section_key_terms[section_name]
    
    # Score the remaining sentences based on key terms and entities
    scored_sentences = []
    for i, sent in enumerate(fixed_sentences[1:], 1):
        if len(sent) < 15:  # Skip very short sentences
            continue
            
        score = 0
        sent_lower = sent.lower()
        
        # Check for key terms
        for term in section_terms:
            if term.lower() in sent_lower:
                score += 1
        
        # Check for named entities
        doc = nlp(sent)
        for ent in doc.ents:
            score += 1
            
        # Prefer sentences with numbers (often contain specific measurements)
        if any(c.isdigit() for c in sent):
            score += 0.5
            
        # Adjust score based on sentence position (prefer earlier sentences)
        position_weight = 1.0 - (i / len(fixed_sentences))
        score = score + (position_weight * 0.5)
        
        scored_sentences.append((i, sent, score))
    
    # Sort by score (highest first) and select top sentences
    scored_sentences.sort(key=lambda x: -x[2])
    
    # Take up to 4 more sentences (for a total of 5 with the first one)
    for _, sent, _ in scored_sentences[:4]:
        selected_sentences.append(sent)
    
    # Reorder selected sentences to maintain original flow
    selected_sentences = sorted(selected_sentences, 
                               key=lambda s: fixed_sentences.index(s) if s in fixed_sentences else 0)
    
    return " ".join(selected_sentences)

def synthesize_section_summary(section_text, section_name, max_length=350):
    """Generate a coherent summary for a section with specific details and context."""
    import re
    
    if not section_text or len(section_text) < 50:
        return section_text
        
    # Process section with Calamancy NLP
    doc = nlp(section_text)
    
    # Extract structured elements with enhanced Calamancy-specific processing
    elements = extract_structured_elements(section_text, section_name)
    
    # Enhanced subject detection using Calamancy's entity recognition
    subject = None
    
    # First try to find named individuals
    for ent in doc.ents:
        if ent.label_ == "PER":
            subject = ent.text
            break
            
    # Look for kinship terms which are common in Filipino medical narratives
    if not subject:
        kinship_terms = ["Lolo", "Lola", "Tatay", "Nanay", "Ate", "Kuya", "Tito", "Tita"]
        for term in kinship_terms:
            if term in section_text:
                subject = term
                break
                
    # Default subject if none found
    if not subject:
        subject = "Ang pasyente"
    
    # Get medical measurements with units for precise reporting
    measurements = {}
    for ent in doc.ents:
        if ent.label_ == "MEASUREMENT":
            # Try to extract the numerical value and unit
            value_match = re.search(r'(\d+\.?\d*)\s*(mg/dL|mmHg|kg|cm|°C|%)', ent.text)
            if value_match:
                measurements[ent.text] = {
                    'value': value_match.group(1),
                    'unit': value_match.group(2)
                }
    
    # Extract Filipino cultural references especially for dietary preferences
    cultural_foods = []
    for ent in doc.ents:
        if ent.label_ == "FOOD":
            cultural_foods.append(ent.text)
    
    # Use Calamancy's NER to identify specific Filipino medical terms
    filipino_medical_terms = []
    for ent in doc.ents:
        if ent.label_ in ["DISEASE", "SYMPTOM", "TREATMENT"] and any(c in "ñabcdefghijklmnopqrstuvwxyzÑABCDEFGHIJKLMNOPQRSTUVWXYZ" for c in ent.text):
            filipino_medical_terms.append(ent.text)
    
    # Generate tailored summary based on section type
    if section_name == "mga_sintomas" or "sintomas" in section_name:
        # SYMPTOMS SECTION - Enhanced with Calamancy's medical understanding
        conditions = elements.get("conditions", [])
        symptoms = elements.get("symptoms", [])
        severity = elements.get("severity", [""])[0] if elements.get("severity") else ""
        frequency = elements.get("frequency", [""])[0] if elements.get("frequency") else ""
        
        # Create symptom summary with specific details
        # First sentence: Main condition and symptoms
        if "diabetes" in section_text.lower():
            # Extract specific glucose values leveraging Calamancy's measurement recognition
            glucose_match = re.search(r'(\d+)\s*mg/dL', section_text)
            glucose_value = glucose_match.group(1) if glucose_match else ""
            
            if "roller-coaster pattern" in section_text.lower() or "fluctuations" in section_text.lower():
                summary = f"{subject} ay nagpapakita ng Type 2 diabetes na may roller-coaster pattern sa blood glucose readings, kung saan madalas na lumampas sa {glucose_value} mg/dL ang kanyang post-meal readings pagkatapos kumain ng traditional Filipino foods."
            else:
                summary = f"{subject} ay diagnosed na may Type 2 diabetes at nagpapakita ng hindi stable na glucose levels na nakaka-apekto sa kanyang araw-araw na gawain."
        
        elif "mobility" in section_text.lower() or "paggalaw" in section_text.lower():
            # Detailed mobility challenges using ADL entities from Calamancy
            adl_entities = [ent.text for ent in doc.ents if ent.label_ == "ADL"]
            if adl_entities:
                summary = f"{subject} ay nagpapakita ng matinding limitasyon sa mobility, partikular sa {', '.join(adl_entities[:3])}."
            else:
                summary = f"{subject} ay nagpapakita ng matinding limitasyon sa mobility, partikular sa pang-araw-araw na gawain tulad ng pag-shower, pag-toilet, at pagbibihis."
        
        elif "cognitive" in section_text.lower() or "isip" in section_text.lower() or "memorya" in section_text.lower():
            # Use cognitive-specific terms from Calamancy
            cognitive_issues = [ent.text for ent in doc.ents if ent.label_ == "COGNITIVE"]
            
            if cognitive_issues:
                summary = f"{subject} ay nagpapakita ng cognitive issues tulad ng {', '.join(cognitive_issues[:2])}."
            else:
                summary = f"{subject} ay nagpapakita ng cognitive difficulties, partikular sa memorya at orientation."
        
        elif conditions or symptoms:
            if conditions and symptoms:
                summary = f"{subject} ay nagpapakita ng {', '.join(conditions[:1])} kasama ang mga sintomas na {', '.join(symptoms[:2])}."
            elif conditions:
                summary = f"{subject} ay diagnosed na may {', '.join(conditions[:2])}."
            elif symptoms:
                summary = f"{subject} ay nagpapakita ng mga sintomas tulad ng {', '.join(symptoms[:2])}."
        else:
            summary = f"{subject} ay nagpapakita ng mga sintomas na nangangailangan ng medikal na atensyon."
        
        # Second sentence: Specific symptom details with cultural context
        if "falls" in section_text.lower() or "madapa" in section_text.lower():
            summary += f" May risk ng falls o pagkahulog dahil sa kahinaan ng kanyang balance at leg strength."
        
        if cultural_foods and ("diet" in section_text.lower() or "pagkain" in section_text.lower()):
            summary += f" Nahihirapan siyang i-manage ang kanyang kondisyon dahil sa preference para sa traditional Filipino foods na high in carbohydrates tulad ng {', '.join(cultural_foods[:3])}."
            
        if filipino_medical_terms:
            summary += f" Nabanggit din ang {', '.join(filipino_medical_terms[:2])} bilang bahagi ng kanyang mga sintomas."
        
        # Third sentence: Impact and frequency with specific measurements
        if any(term in section_text.lower() for term in ["napapaupo", "pagkapagod", "hingal", "fatigue"]):
            summary += f" Madalas siyang nakakaranas ng fatigue o pagkapagod sa mga pang-araw-araw na aktibidad, na naglalagay sa kanya sa risk para sa komplikasyon."
        
        if measurements:
            measurement_phrases = []
            for name, details in measurements.items():
                if "blood" in name.lower() or "glucose" in name.lower():
                    measurement_phrases.append(f"blood glucose na {details['value']} {details['unit']}")
                elif "pressure" in name.lower():
                    measurement_phrases.append(f"blood pressure na {details['value']} {details['unit']}")
                elif "weight" in name.lower() or "timbang" in name.lower():
                    measurement_phrases.append(f"timbang na {details['value']} {details['unit']}")
            
            if measurement_phrases:
                summary += f" Base sa mga sukat, nakita natin ang {', '.join(measurement_phrases)}."
        
        return summary.strip()
        
    elif section_name == "kalagayan_pangkatawan" or "pangkatawan" in section_name:
        # PHYSICAL CONDITION SECTION - Enhanced with body parts and specifics
        body_parts = elements.get("body_parts", [])
        limitations = elements.get("limitations", [])
        weakness_areas = []
        key_traits = []
        
        # Better extraction of body parts using Calamancy entities
        body_part_entities = [ent.text for ent in doc.ents if ent.label_ == "BODY_PART"]
        
        # Extract key physical indicators
        for sentence in doc.sents:
            sentence_text = sentence.text.lower()
            if "weakness" in sentence_text and any(bp in sentence_text for bp in ["lower extremities", "legs", "binti"]):
                weakness_areas.append("lower extremities")
            if any(term in sentence_text for term in ["upper body strength", "lakas ng upper body"]):
                key_traits.append("significant upper body strength")
            if any(term in sentence_text for term in ["poor balance", "walang balanse", "mahirap mag-balance"]):
                key_traits.append("poor balance")
            if any(term in sentence_text for term in ["arthritic fingers", "arthritis sa daliri"]):
                weakness_areas.append("arthritic fingers")
        
        # First sentence: Physical condition overview with specific body parts
        if weakness_areas:
            summary = f"{subject} ay nagpapakita ng physical limitations dahil sa {' at '.join(weakness_areas)}."
        elif "meal timing" in section_text.lower() or "glucose" in section_text.lower():
            # Special case for metabolic/nutritional physical status
            timing_match = re.search(r'(\d+)-(\d+)\s*hours', section_text)
            meal_timing = f"{timing_match.group(1)}-{timing_match.group(2)} oras" if timing_match else "hindi regular"
            
            summary = f"{subject} ay may inappropriate meal compositions at inconsistent meal timing (pagitan ng {meal_timing}) na nagdudulot ng glucose fluctuations."
        elif body_part_entities:
            summary = f"{subject} ay may limitasyon sa {', '.join(body_part_entities[:3])}, na nakakaapekto sa kanyang pang-araw-araw na gawain."
        else:
            summary = f"Ang pisikal na kalagayan ni {subject} ay nagpapakita ng mga limitasyon sa mobility at pangangatawan."
            
        # Second sentence: Specific physical challenges with rich detail
        if key_traits:
            summary += f" Kailangan niyang umasa sa {' at '.join(key_traits)} para makatulong sa mga pang-araw-araw na gawain."
        
        specific_challenges = []
        if "towel rack" in section_text.lower() or "humahawak" in section_text.lower():
            specific_challenges.append("paggamit ng hindi angkop na suporta tulad ng towel rack habang naliligo")
        
        if "humiga sa kama" in section_text.lower() or "pagbibihis" in section_text.lower():
            specific_challenges.append("pangangailangang humiga sa kama para maisuot ang pantalon")
            
        if specific_challenges:
            summary += f" May mga partikular na kahirapan tulad ng {' at '.join(specific_challenges)}."
        
        # Third sentence: Impact on safety and function with medical context
        risk_factors = []
        if "hingal" in section_text.lower() or "pagkapagod" in section_text.lower():
            risk_factors.append("mabilis na pagkapagod")
        
        if "risk para sa falls" in section_text.lower() or "madapa" in section_text.lower():
            risk_factors.append("mataas na risk para sa falls")
            
        if "limited range of motion" in section_text.lower() or "limited flexibility" in section_text.lower():
            risk_factors.append("limitadong range of motion")
            
        if risk_factors:
            summary += f" Ang mga kondisyong ito ay nagdudulot ng {', '.join(risk_factors)}, na nangangailangan ng safety precautions at angkop na interventions."
        else:
            summary += f" Ang kanyang pisikal na limitasyon ay malinaw na nakakaapekto sa kanyang kalidad ng buhay at pang-araw-araw na gawain."
            
        return summary.strip()
        
    elif section_name == "kalagayan_mental" or "mental" in section_name:
        # MENTAL/EMOTIONAL CONDITION SECTION
        emotional_states = elements.get("emotional_state", [])
        cognitive_status = elements.get("cognitive_status", [])
        
        # Extract emotional states from Calamancy entities
        emotion_entities = [ent.text for ent in doc.ents if ent.label_ == "EMOTION"]
        cognitive_entities = [ent.text for ent in doc.ents if ent.label_ == "COGNITIVE"]
        
        # First sentence: Overall mental/emotional state with specific terminology
        if emotion_entities or "mairita" in section_text.lower():
            emotion_terms = emotion_entities if emotion_entities else ["pagkairita", "frustration"]
            summary = f"{subject} ay nagpapakita ng {', '.join(emotion_terms[:2])}, lalo na kapag nakakaranas ng kahirapan sa personal care activities."
        elif cognitive_entities:
            summary = f"{subject} ay nagpapakita ng cognitive issues tulad ng {', '.join(cognitive_entities[:2])}."
        elif emotional_states:
            summary = f"{subject} ay nagpapakita ng {', '.join(emotional_states[:2])} na emosyonal na kalagayan."
        else:
            summary = f"Ang mental na kalagayan ni {subject} ay nangangailangan ng pagsusuri at atensyon."
        
        # Second sentence: Specific emotional reactions with contextual triggers
        if "'Hindi ako baby'" in section_text or "Hindi ako baby" in section_text:
            summary += f" Kapag tinutulungan siya, nagiging defensive at sinasabi ang 'Hindi ako baby!' o 'Kaya ko pa ito!' na nagpapahiwatig ng kanyang pagpapahalaga sa independence."
        
        if any(term in section_text.lower() for term in ["confused", "nalilito", "disoriented", "pagkalito"]):
            # Use specific examples from the text
            confusion_example = ""
            for sent in doc.sents:
                if any(term in sent.text.lower() for term in ["confused", "nalilito", "disoriented", "pagkalito"]):
                    confusion_example = sent.text
                    break
                    
            if confusion_example:
                summary += f" Nagpapakita siya ng pagkalito, halimbawa: '{confusion_example}'."
            else:
                summary += f" Regular na nagpapakita ng pagkalito at disorientation."
        
        # Third sentence: Impact on relationships with cultural context
        relationship_mentions = [ent.text for ent in doc.ents if ent.label_ == "SOCIAL_REL"]
        if relationship_mentions:
            summary += f" Ang kanyang kondisyon ay nakakaapekto sa relasyon niya sa {', '.join(relationship_mentions[:2])}."
        elif "kanin" in section_text.lower() and "hindi ko mabubuhay" in section_text.lower():
            summary += f" May matibay siyang cultural attachment sa Filipino diet, sinasabing 'hindi ko mabubuhay nang walang kanin' na nagpapahirap sa pag-adapt ng mga diet modifications."
        else:
            summary += f" Ang kanyang mental state ay nakakaapekto sa overall quality of life at daily functioning."
        
        return summary.strip()
        
    elif section_name == "aktibidad" or "aktibidad" in section_name:
        # ACTIVITY/ADL SECTION
        activities = elements.get("activities", [])
        limitations = elements.get("activity_limitations", [])
        
        # Use Calamancy's entity recognition for ADLs
        adl_entities = [ent.text for ent in doc.ents if ent.label_ == "ADL"]
        
        # First sentence: Overall activity status with specific ADLs
        specific_activities = []
        if adl_entities:
            specific_activities = adl_entities[:3]
        else:
            if "pag-shower" in section_text.lower() or "bathing" in section_text.lower():
                specific_activities.append("pag-shower")
            if "pag-toilet" in section_text.lower() or "toilet" in section_text.lower():
                specific_activities.append("paggamit ng toilet")
            if "pagbibihis" in section_text.lower() or "dressing" in section_text.lower():
                specific_activities.append("pagbibihis")
        
        if specific_activities:
            summary = f"{subject} ay dumaranas ng matinding kahirapan sa mga pang-araw-araw na gawain, partikular sa {', '.join(specific_activities)}."
        elif activities:
            summary = f"{subject} ay nahihirapan sa {', '.join(activities[:3])}."
        elif "mobility-related" in section_text.lower():
            summary = f"{subject} ay dumaranas ng matinding kahirapan sa mobility-related self-care activities, na nakakaapekto sa kanyang kalidad ng buhay."
        else:
            summary = f"Ang kakayahan ni {subject} sa mga pang-araw-araw na aktibidad ay nakompromiso."
        
        # Second sentence: Specific challenges with detailed context
        adl_challenges = []
        
        # Extract specific challenges from the text
        if "madapa" in section_text.lower() or "falls" in section_text.lower():
            adl_challenges.append("risk ng pagkahulog")
            
            # Add specific details about falls if available
            fall_details = ""
            for sent in doc.sents:
                if "madapa" in sent.text.lower() or "falls" in sent.text.lower():
                    fall_details = sent.text
                    break
            
            if fall_details:
                falls_match = re.search(r'may ([^\.]*)pagkakataon([^\.]*)(madapa|bumagsak|mahulog)', section_text, re.IGNORECASE)
                if falls_match:
                    adl_challenges[-1] = f"risk ng pagkahulog ({falls_match.group(0)})"
        
        if "nagbabawas" in section_text.lower() and "frequency" in section_text.lower() and "bathing" in section_text.lower():
            adl_challenges.append("pagbabawas ng frequency ng bathing")
            
        if "paulit-ulit" in section_text.lower() and "damit" in section_text.lower():
            adl_challenges.append("paulit-ulit na pagsuot ng parehong damit")
            
        if adl_challenges:
            summary += f" Dahil sa mga challenges na ito, nagpapakita siya ng {', '.join(adl_challenges)} bilang coping mechanism."
        elif "feast-and-fast" in section_text.lower():
            summary += f" Sinusubukan niya ang 'feast-and-fast' approach kung saan kumakain siya ng regular Filipino meals nang walang restrictions, tapos nagsa-skip ng meals kapag mataas ang kanyang blood sugar readings."
        
        # Third sentence: Impact on independence and quality of life
        if "'Hindi ako baby'" in section_text or "Hindi ako baby" in section_text:
            summary += f" Mahalaga sa kanya ang kanyang independence, sinasabi niyang 'Hindi ako baby!' o 'Kaya ko pa ito!' kahit na malinaw na nahihirapan siya sa mga aktibidad."
        elif "tinutulungan" in section_text.lower() and ("bathing" in section_text.lower() or "dressing" in section_text.lower()):
            summary += f" Bagamat nangangailangan ng tulong, nagpapakita siya ng frustration kapag tinutulungan sa mga personal activities, na nagpapakita ng kanyang pakikibaka para sa independence."
        else:
            summary += f" Ang mga limitations na ito ay may significant impact sa kanyang independence at overall quality of life."
        
        return summary.strip()
        
    elif section_name == "kalagayan_social" or "social" in section_name:
        # SOCIAL CONDITION SECTION
        social_supports = elements.get("social_support", [])
        relationship_entities = [ent.text for ent in doc.ents if ent.label_ == "SOCIAL_REL"]
        
        # First sentence: Social relationships overview
        if "tumanggi" in section_text.lower() and ("meal plan" in section_text.lower() or "diet" in section_text.lower()):
            summary = f"{subject} ay tumanggi na sundin ang meal plan na binigay ng healthcare professional dahil ito'y salungat sa kanyang cultural food preferences."
        elif relationship_entities:
            mentioned_relationships = relationship_entities[:2]
            if "family" in section_text.lower() or "pamilya" in section_text.lower():
                mentioned_relationships.append("pamilya")
            summary = f"{subject} ay may social interactions na kinabibilangan ng {', '.join(set(mentioned_relationships))}."
        elif social_supports:
            summary = f"{subject} ay may suporta mula sa {', '.join(social_supports[:2])}."
        else:
            summary = f"Ang social na kalagayan ni {subject} ay may mahalagang papel sa kanyang pangkalahatang well-being."
        
        # Second sentence: Cultural or family dynamics with specific details
        if "hindi ko mabubuhay nang walang kanin" in section_text.lower():
            summary += f" Paulit-ulit niyang binabanggit na 'hindi ko mabubuhay nang walang kanin' at 'masyadong bland ang diet na ibinibigay sa akin,' na nagpapakita ng matinding cultural attachment sa tradisyonal na Filipino diet."
        elif "masyadong malayo ito sa kanyang usual diet" in section_text.lower():
            summary += f" Ang mga pang-medical na rekomendasyon ay masyadong malayo sa kanyang usual diet na deeply rooted sa Filipino culture, kaya nahihirapan siyang sumunod dito."
        elif "Filipino dishes" in section_text.lower() or "Filipino food" in section_text.lower():
            summary += f" May matibay siyang preference para sa traditional Filipino dishes na high in refined carbohydrates at sweets, na nagiging hadlang sa kanyang diabetes management."
        
        # Third sentence: Impact on family/social dynamics with cultural context
        if "strong resistance" in section_text.lower():
            summary += f" Ang mga attempt sa pag-introduce ng mas healthy options tulad ng {', '.join(cultural_foods[:2]) if cultural_foods else 'brown rice at vegetables'} ay nakatagpo ng strong resistance mula sa kanya."
        elif "naging mahirap ang grocery shopping" in section_text.lower():
            summary += f" Ang pamilya ay nahihirapan sa grocery shopping at meal preparation dahil kailangan ng separate meals o radical changes sa family recipes para sa kanyang dietary needs."
        elif "sinasalungat niya" in section_text.lower():
            summary += f" Sinasalungat niya ang mga pagbabago sa kanyang diet na nagpapahirap sa pagsunod sa mga medical recommendations at nagdudulot ng tension sa family dynamics."
        else:
            summary += f" Ang kanyang social context ay mahalagang isaalang-alang sa pagbubuo ng effective na care plan."
        
        return summary.strip()
        
    elif section_name == "pangunahing_rekomendasyon" or "rekomendasyon" in section_name:
        # PRIMARY RECOMMENDATIONS SECTION
        recommendations = elements.get("recommendations", [])
        healthcare_referrals = elements.get("healthcare_referrals", [])
        
        # Extract healthcare referrals from Calamancy entities
        referral_entities = [ent.text for ent in doc.ents if ent.label_ == "HEALTHCARE_REFERRAL"]
        recommendation_entities = [ent.text for ent in doc.ents if ent.label_ == "RECOMMENDATION"]
        
        # First sentence: Primary recommendation with specific healthcare providers
        if referral_entities:
            summary = f"Inirerekomenda ang pagkonsulta sa {', '.join(referral_entities[:1])} para sa comprehensive na assessment at management ng kondisyon."
        elif "gastroenterologist" in section_text.lower():
            summary = f"Inirerekomenda ang agarang pagkonsulta sa gastroenterologist para sa proper diagnostic work-up at evaluasyon ng gastrointestinal symptoms."
        elif "nutritionist" in section_text.lower() or "dietitian" in section_text.lower():
            summary = f"Iminumungkahi ang regular na konsultasyon sa nutrition specialist para sa personalized na dietary plan na angkop sa medical at cultural needs."
        elif recommendations:
            summary = f"Ang pangunahing rekomendasyon ay {recommendations[0]}."
        elif recommendation_entities:
            summary = f"Iminumungkahi ang {recommendation_entities[0]} bilang pangunahing hakbang para sa pangangalaga."
        else:
            summary = f"Iminumungkahi na mag-monitor ng signs ng dehydration tulad ng dry mouth, decreased urination, at increased dizziness."
        
        # Second sentence: Specific action recommendations with detailed guidance
        if "high-fat" in section_text.lower() and "acidic" in section_text.lower():
            summary += f" Para sa meal composition, iminumungkahi ang pagbawas ng high-fat, acidic, at spicy foods na maaaring mag-trigger ng reflux symptoms."
        elif "comprehensive" in section_text.lower() and "evaluation" in section_text.lower():
            summary += f" Kinakailangang bantayan ang monitor ng mga vital signs at suriin ang mga underlying conditions tulad ng gastrointestinal ulcers, malabsorption syndromes, o posibleng malignancies."
        elif "low" in section_text.lower() and "carb" in section_text.lower():
            summary += f" Mahalagang i-modify ang diet patungo sa mas mababang carbohydrate content habang isinasaalang-alang ang cultural preferences para mapanatili ang adherence."
        elif "over-the-counter" in section_text.lower() or "antacids" in section_text.lower():
            summary += f" Para sa interim symptom management, maaaring gamitin ang over-the-counter antacids o acid reducers, pero dapat temporary lang ito habang hinihintay ang komprehensibong medical assessment."
        
        # Third sentence: Timeframe or importance emphasis with follow-up plan
        if "agarang" in section_text.lower() or "immediate" in section_text.lower():
            summary += f" Ang mga rekomendasyon na ito ay nangangailangan ng agarang aksyon para maiwasan ang mga komplikasyon at mapabuti ang quality of life."
        elif "temporary" in section_text.lower() or "pansamantala" in section_text.lower():
            summary += f" Binibigyang-diin na dapat temporary lang ang mga self-management strategies habang hinihintay ang komprehensibong medical assessment."
        else:
            summary += f" Regular na follow-up at monitoring ay mahalaga para sa tuloy-tuloy na progreso at adjustment ng treatment plan kung kinakailangan."
        
        return summary.strip()
        
    elif section_name == "mga_hakbang" or "hakbang" in section_name:
        # STEPS/ACTIONS SECTION
        treatments = elements.get("treatments", [])
        intervention_methods = elements.get("intervention_methods", [])
        
        # Extract treatment entities from Calamancy
        treatment_entities = [ent.text for ent in doc.ents if ent.label_ == "TREATMENT_METHOD" or ent.label_ == "TREATMENT"]
        
        # First sentence: Primary action steps with specific interventions
        if "pag-iwas sa caffeine" in section_text.lower():
            summary = f"Pinapayuhan ang pag-iwas sa caffeine, alcohol, at carbonated beverages na maaaring magpalala ng gastric acid production at makaapekto sa digestive health."
        elif treatment_entities:
            summary = f"Ang mga rekomendasyon ay kinabibilangan ng {', '.join(treatment_entities[:2])} bilang pangunahing hakbang sa management ng kondisyon."
        elif treatments:
            summary = f"Ang mga hakbang sa paggamot ay nakatuon sa {', '.join(treatments[:2])} para sa pag-improve ng kanyang kondisyon."
        elif intervention_methods:
            summary = f"Ang mga hakbang sa paggamot ay nakatuon sa {', '.join(intervention_methods[:2])}."
        else:
            summary = f"May mga ispesipikong hakbang na dapat isagawa para sa pangangalaga at management ng kondisyon."
            
        # Second sentence: Implementation guidance with cultural sensitivity
        if "Bukod dito, tinuruan" in section_text.lower():
            summary += f" Bukod dito, kinakailangang turuan ang pamilya tungkol sa wastong pangangalaga, monitoring ng symptoms, at paggamit ng food and symptom diary para sa mas mahusay na tracking."
        elif "family" in section_text.lower() or "pamilya" in section_text.lower():
            summary += f" Mahalagang kasangkutin ang pamilya sa implementation ng mga hakbang na ito para sa dagdag na suporta at consistency."
        elif cultural_foods:
            summary += f" Ang mga hakbang na ito ay dapat iakma sa kanyang cultural preferences, lalo na sa aspeto ng pagkain at lifestyle, para matiyak ang adherence at sustainability."
        else:
            summary += f" Ang mga hakbang na ito ay dapat isagawa nang regular at susundin nang mahigpit para sa optimal na resulta at pag-iwas sa komplikasyon."
            
        # Third sentence: Expected outcomes with follow-up plan
        if filipino_medical_terms:
            summary += f" Ang pagsunod sa mga hakbang na ito ay makakatulong sa pag-manage ng {', '.join(filipino_medical_terms[:2])} at pagpapabuti ng overall quality of life."
        else:
            summary += f" Ang pagsunod sa mga hakbang na ito ay makakatulong sa pagpapabuti ng kalagayan, pagbawas ng symptoms, at pagtaas ng functional independence sa araw-araw."
            
        return summary.strip()
        
    elif section_name == "pangangalaga" or "alaga" in section_name:
        # CARE SECTION
        monitoring_plans = elements.get("monitoring_plans", [])
        warnings = elements.get("warnings", [])
        
        # Extract safety and monitoring entities
        safety_entities = [ent.text for ent in doc.ents if ent.label_ == "SAFETY"]
        warning_entities = [ent.text for ent in doc.ents if ent.label_ == "WARNING_SIGN"]
        
        # First sentence: Care approach with specific techniques
        if "reflux symptoms" in section_text.lower() and "management" in section_text.lower():
            summary = f"Para sa management ng reflux symptoms, inirerekomenda na manatiling nakaupo nang tuwid sa loob ng 30 minutos pagkatapos kumain at ang pag-elevate ng upper body habang natutulog."
        elif "dizziness" in section_text.lower() and ("pagtayo" in section_text.lower() or "blood pressure" in section_text.lower()):
            summary = f"Hinggil sa postural dizziness, nirerekomenda ang paunti-unting pagtayo mula sa pagkakaupo o pagkakahiga para mabigyan ng pagkakataon ang blood pressure na ma-adjust."
        elif "falls" in section_text.lower() or "madapa" in section_text.lower():
            summary = f"Para sa pangangalaga at pag-iwas sa falls, kinakailangang maglagay ng safety measures sa bahay, lalo na sa banyo at mga lugar na may transition surfaces."
        elif monitoring_plans:
            summary = f"Ang pangangalaga ay nakatuon sa {', '.join(monitoring_plans[:2])} para sa optimal na recovery at pag-iwas sa komplikasyon."
        else:
            summary = f"Mahalagang magkaroon ng comprehensive na pangangalaga strategy na nakaayon sa kanyang specific needs at kondisyon."
            
        # Second sentence: Specific care techniques with detailed implementation
        if "pag-elevate ng upper body" in section_text.lower():
            summary += f" Inirerekomenda ang pag-elevate ng upper body habang natutulog sa pamamagitan ng paglalagay ng mga unan o pag-adjust ng kama para mabawasan ang reflux symptoms sa gabi."
        elif "proper positioning" in section_text.lower() or "positioning" in section_text.lower():
            summary += f" Ang proper positioning ay mahalaga para sa comfort at pag-iwas sa pressure sores, na nangangailangan ng regular na pagpapalit ng posisyon every 2-3 hours."
        elif safety_entities:
            safety_items = []
            
            if "towel rack" in section_text.lower():
                safety_items.append("pag-install ng grab bars sa halip na paggamit ng towel rack")
            if "shower" in section_text.lower() or "bathing" in section_text.lower():
                safety_items.append("paggamit ng non-slip mats sa shower area")
            if "toilet" in section_text.lower():
                safety_items.append("paggamit ng toilet seat risers")
                
            if safety_items:
                summary += f" Inirerekomenda ang mga safety modifications tulad ng {', '.join(safety_items)} para mabawasan ang risk ng aksidente."
            else:
                summary += f" Kinakailangang i-implement ang mga safety measures sa buong bahay para maprotektahan siya mula sa potential hazards."
            
        # Third sentence: Monitoring guidance with specific warning signs
        if warning_entities:
            summary += f" Mahalagang bantayan ang mga warning signs tulad ng {', '.join(warning_entities[:3])} at agad na humingi ng medical attention kung nakita ang mga ito."
        elif "dehydration" in section_text.lower():
            summary += f" Mahalaga ring mag-monitor ng signs ng dehydration tulad ng dry mouth, decreased urination, at increased dizziness, lalo na dahil may risk ng inadequate fluid intake."
        else:
            summary += f" Regular na monitoring ng kanyang kondisyon at pag-document ng mga pagbabago ay mahalaga para sa early detection ng potential complications at adjustment ng care plan kung kinakailangan."
            
        return summary.strip()
        
    elif section_name == "pagbabago_sa_pamumuhay" or "pamumuhay" in section_name:
        # LIFESTYLE CHANGES SECTION
        diet_changes = elements.get("diet_changes", [])
        lifestyle_changes = elements.get("lifestyle_changes", [])
        
        # Extract diet recommendations from Calamancy entities
        diet_entities = [ent.text for ent in doc.ents if ent.label_ == "DIET_RECOMMENDATION"]
        food_entities = [ent.text for ent in doc.ents if ent.label_ == "FOOD"]
        
        # First sentence: Lifestyle modifications overview with specific focus
        if "nutrition-focused interventions" in section_text.lower() or "frequent, small meals" in section_text.lower():
            summary = f"Inirerekomenda ang pagbago sa eating pattern tungo sa frequent, small meals (5-6 na beses sa isang araw) sa halip na malalaki at mabibigat na meals para mabawasan ang burden sa digestive system."
        elif diet_entities:
            summary = f"Ang mga inirerekomendang pagbabago sa diet ay kinabibilangan ng {', '.join(diet_entities[:2])} para sa pag-improve ng kalusugan."
        elif food_entities and "brown rice" in ' '.join(food_entities).lower():
            food_list = [food for food in food_entities if "brown rice" in food.lower() or "vegetables" in food.lower() or "lean" in food.lower()]
            summary = f"Iminumungkahi ang gradual transition sa mga healthier options tulad ng {', '.join(food_list[:3])} habang isinasaalang-alang ang cultural preferences."
        elif diet_changes:
            summary = f"Ang mga pagbabago sa diet ay kinabibilangan ng {', '.join(diet_changes[:2])} para sa better management ng kondisyon."
        elif lifestyle_changes:
            summary = f"Ang mga inirerekomendang pagbabago sa pamumuhay ay kinabibilangan ng {', '.join(lifestyle_changes[:2])} para sa pangkalahatang pagpapabuti ng kalusugan."
        else:
            summary = f"Inirerekomenda ang mga pagbabago sa diet at physical activity na naaayon sa kanyang kondisyon at kakayahan."
            
        # Second sentence: Specific modifications with detailed implementation
        if "dietary fiber" in section_text.lower() or "soluble fiber" in section_text.lower():
            summary += f" Mahalagang isama ang gradual increase ng dietary fiber (unang soluble fiber para hindi ma-irritate ang tiyan), adequate hydration, at regular physical activity ayon sa kanyang tolerance."
        elif "hydration" in section_text.lower():
            summary += f" Mahalagang maisama ang sapat na hydration (8-10 glasses ng tubig araw-araw) at regular na pagkilos ayon sa kanyang tolerance para sa overall health at digestive function."
        elif "meal composition" in section_text.lower():
            if food_entities:
                avoid_foods = [food for food in food_entities if "white" in food.lower() or "fried" in food.lower() or "sweets" in food.lower()]
                if avoid_foods:
                    summary += f" Inirerekomenda ang pagbawas ng consumption ng {', '.join(avoid_foods[:3])} at ang pagtaas ng intake ng nutrient-dense na pagkain na madaling ma-digest."
                else:
                    summary += f" Inirerekomenda ang pagbabago ng meal composition para mabawasan ang refined carbohydrates at saturated fats, at madagdagan ang lean proteins at complex carbohydrates."
            else:
                summary += f" Inirerekomenda ang balanseng diet na may tamang ratio ng carbohydrates, proteins, at healthy fats, kasama ang mataas na fiber content para mapanatili ang stable blood sugar levels."
        elif "physical activity" in section_text.lower() or "exercise" in section_text.lower():
            summary += f" Ang moderate physical activity ng hindi bababa sa 30 minutes, 5 days a week ay inirerekomenda, kasama ang mga balance exercises para mapabuti ang stability at maiwasan ang falls."
        
        # Third sentence: Expected benefits and implementation support
        if "dietitian" in section_text.lower() or "nutritionist" in section_text.lower():
            summary += f" Inirerekomenda rin ang konsultasyon sa registered dietitian para sa personalized nutrition plan na sasagot sa kanyang specific nutritional needs habang ina-address ang kanyang medical at cultural considerations."
        elif "monitoring" in section_text.lower() or "food diary" in section_text.lower() or "symptom diary" in section_text.lower():
            summary += f" Ang paggamit ng food at symptom diary ay makakatulong sa pag-monitor ng kanyang response sa mga pagbabagong ito at mag-guide sa further refinement ng lifestyle interventions."
        else:
            summary += f" Consistent na pagsunod sa mga lifestyle modifications na ito ay mahalaga para makita ang mga positibong resulta sa kanyang kalusugan at quality of life."
        
        return summary.strip()
    
    # For any other section type, extract key sentences
    return summarize_section_text(section_text, section_name, max_length)

# Define key terms for each section type - EXPANDED SIGNIFICANTLY
section_key_terms = {
    "mga_sintomas": [
        # General symptom terms
        "sintomas", "sakit", "kondisyon", "nararamdaman", "nagpapakita", 
        "dumaranas", "nakakaranas", "hirap", "problema",
        "symptoms", "condition", "experiencing", "suffering from", "presenting with",
        
        # Specific symptoms and manifestations
        "nananakit", "kirot", "pamamanhid", "pamumula", "pangangati", "panginginig",
        "pananakit", "panghihina", "pamamaga", "pamamanas", "pagkapagod", "pagod",
        "pagkahilo", "hirap huminga", "pag-ubo", "ubo", "lagnat", "sipon",
        "pagsusuka", "panunuyo", "kombulsyon", "walang gana kumain",
        
        # Pain descriptions
        "masakit", "sumasakit", "mabigat ang pakiramdam", "matigas",
        "naiirita", "malamig", "mainit", "burning", "stabbing", "throbbing",
        
        # Physical manifestations
        "pasa", "sugat", "paltos", "galos", "hiwa", "bukol", "cyst", "lumaking parte",
        "bloating", "swelling", "tenderness", "inflammation", "bleeding", "discharge",
        
        # Severity indicators
        "matinding", "malubhang", "banayad na", "katamtamang", 
        "severe", "moderate", "mild", "persistent", "chronic", "acute", "intermittent",
        
        # Frequency terms
        "paulit-ulit", "madalas", "paminsan-minsan", "regular", "occasional",
        "frequent", "daily", "nightly", "weekly", "constant", "recurring",
        
        # Duration indicators
        "matagal na", "ilang araw na", "ilang linggo na", "simula pa", 
        "ongoing", "recent onset", "long-standing", "new", "sudden"
    ],
    
    "kalagayan_pangkatawan": [
        # Basic physical terms
        "pisikal", "katawan", "lakas", "pangangatawan", "physical", "body",
        "pangkalahatan", "general", "overall", "strength", "condition",
        
        # Physical state descriptions
        "kalusugan", "kundisyon", "pakiramdam", "estado", "state", "profile",
        "malusog", "mabuti", "mahina", "maayos", "mahirap", "fragile", "frail",
        
        # Body systems
        "cardiovascular", "respiratory", "pulmonary", "musculoskeletal", 
        "digestive", "circulatory", "nervous system", "neurological",
        "immune", "endocrine", "integumentary", "skeletal", "sistema",
        
        # Vital signs and measurements
        "vital signs", "sukat", "timbang", "height", "weight", "temperature", 
        "blood pressure", "pulse", "heart rate", "respiratory rate", "presyon", 
        "oxygen saturation", "oxygen level", "BMI", "lab results",
        
        # Body parts
        "ulo", "dibdib", "puso", "baga", "tiyan", "balakang", "braso", "kamay",
        "hita", "tuhod", "binti", "paa", "likod", "spinal", "vertebral", 
        "atay", "bato", "pancreas", "bituka", "sikmura", "utak", "joints",
        
        # Physical abilities
        "balance", "koordinasyon", "coordination", "lakas", "strength", 
        "flexibility", "range of motion", "dexterity", "mobility", "stability",
        "stamina", "endurance", "pagkilos", "paggalaw", "movement", "restriction",
        
        # Physical conditions
        "overweight", "underweight", "obese", "malnourished", "dehydrated",
        "hypertensive", "hypotensive", "febrile", "afebrile", "cachectic"
    ],
    
    "kalagayan_mental": [
        # Cognitive terms
        "pag-iisip", "memorya", "cognitive", "mental", "isip", "memory",
        "pag-unawa", "comprehension", "understanding", "awareness", "orientation",
        "concentration", "attention", "focus", "decision-making", "judgment",
        "reasoning", "perception", "cognition", "processing", "thinking",
        "mentality", "consciousness", "alertness", "coherence", "confusion",
        
        # Mental state descriptions
        "pagkalito", "disoriented", "forgetful", "nakakalimot", "nalilito",
        "hindi matandaan", "alert", "oriented", "malinaw ang pag-iisip",
        "lucid", "confused", "disoriented", "aware", "unaware",
        
        # Emotional terms
        "kalungkutan", "depression", "lungkot", "pagkabalisa", "anxiety",
        "worry", "stress", "galit", "anger", "takot", "fear", "irritable",
        "malungkot", "nag-aalala", "balisa", "masaya", "happy", "hopeful",
        "hopeless", "kawalan ng pag-asa", "frustration", "disappointment",
        
        # Psychological conditions
        "dementia", "Alzheimer's", "cognitive decline", "psychiatric",
        "psychological", "mental health", "mood disorder", "anxiety disorder",
        "depressive disorder", "trauma", "PTSD", "schizophrenia", "bipolar",
        
        # Behavioral manifestations
        "pag-uugali", "behavior", "agitation", "agitated", "withdrawal",
        "pag-iwas", "isolation", "restlessness", "pagod ang isip",
        "irritability", "emotional lability", "mood swings", "aggression",
        "apathy", "detachment", "disinterest", "pagkawala ng interes"
    ],
    
    "aktibidad": [
        # Daily activities
        "gawain", "aktibidad", "activity", "daily", "routine", "schedule",
        "araw-araw", "pang-araw-araw", "tasks", "chores", "regular",
        "obligations", "responsibilidad", "responsibilities", "function",
        
        # Mobility & movement
        "paglalakad", "walking", "paggalaw", "mobility", "pagkilos", "movement",
        "travel", "commuting", "transferring", "standing", "sitting", "lying down",
        "pag-akyat", "climbing", "pagbaba", "descending", "stairs", "steps",
        
        # ADLs (Activities of Daily Living)
        "ADL", "self-care", "pangangalaga sa sarili", "hygiene", "kalinisan",
        "bathing", "pagliligo", "dressing", "pagbibihis", "pagkain", "feeding", 
        "toileting", "grooming", "pag-aayos", "sleeping", "pagtulog",
        
        # IADLs (Instrumental Activities of Daily Living)
        "IADL", "pagmamaneho", "driving", "paggamit ng telepono", "phone use",
        "pagluluto", "cooking", "paglilinis", "cleaning", "pagbabayad", "finances",
        "pamimili", "shopping", "pamamahala ng gamot", "medication management",
        
        # Assistive devices
        "tungkod", "cane", "walker", "wheelchair", "upuan de gulong", "andador",
        "assistive device", "tulong na kasangkapan", "mobility aid", "crutches",
        "saklay", "hospital bed", "ambulatory aid", "supportive device",
        
        # Activity limitations
        "limitado", "limited", "restrictions", "hindi magawa", "unable to",
        "nahihirapan", "difficulty with", "dependence", "dependent", "need assistance",
        "nangangailangan ng tulong", "supervision", "bantay", "difficulty performing"
    ],
    
    "kalagayan_social": [
        # Relationships
        "pamilya", "family", "asawa", "spouse", "anak", "children", "apo", "grandchildren",
        "magulang", "parents", "kaibigan", "friends", "kamag-anak", "relatives", 
        "kapit-bahay", "neighbors", "katrabaho", "co-workers", "relationship",
        "relasyon", "suporta", "support", "support system", "network",
        
        # Social environment
        "pamayanan", "community", "kapitbahayan", "neighborhood", "church", "simbahan", 
        "social circle", "social network", "social activities", "social groups",
        "senior center", "church group", "community center", "volunteer group",
        
        # Social interaction patterns
        "pakikisalamuha", "interaction", "pakikipag-usap", "communication",
        "pakikilahok", "participation", "engagement", "involvement", 
        "socialization", "pakikisama", "getting along", "collaborative",
        
        # Social issues
        "isolation", "pagkakahiwalay", "social isolation", "loneliness", "kalungkutan",
        "withdrawal", "pag-iwas", "social stigma", "discrimination", "abandonment",
        "neglect", "abuse", "pang-aabuso", "household dynamics", "family conflict",
        
        # Social support descriptions
        "assistance", "tulong", "supportive", "matulungin", "caregiver", "tagapag-alaga",
        "family support", "emotional support", "financial support", "sustento",
        "help", "aid", "resource", "provider", "social service", "government aid",
        
        # Living arrangements
        "living situation", "living arrangement", "household composition",
        "lives with", "resides with", "kasama sa bahay", "independent living",
        "assisted living", "nursing home", "living alone", "nag-iisa sa bahay",
        "multi-generational household", "extended family", "malawak na pamilya"
    ],
    
    "pangunahing_rekomendasyon": [
        # Recommendation phrases
        "inirerekomenda", "rekomendasyon", "iminumungkahi", "mungkahi", 
        "pinapayuhan", "payo", "ipinapayo", "nirerekomenda", "recommend", 
        "recommendation", "suggest", "advise", "advice", "proposed", "indicated",
        
        # Priority indicators
        "pangunahin", "primary", "main", "key", "essential", "important",
        "critical", "crucial", "vital", "necessary", "urgent", "immediate",
        "priority", "highest priority", "most important", "first step",
        
        # Action terms
        "kailangan", "need", "require", "must", "should", "dapat", "kinakailangan",
        "importante", "mahalagang", "necessary", "crucial", "critical", "essential",
        
        # Healthcare directives
        "referral", "konsulta", "consultation", "medical evaluation", "assessment",
        "comprehensive evaluation", "professional assessment", "specialist", 
        "dalubhasa", "eksperto", "expert opinion", "second opinion",
        
        # Conditional terms
        "kung", "if", "when", "kapag", "in case", "should", "would", "as needed",
        "as required", "as appropriate", "kung kinakailangan", "kung naaangkop",
        
        # Treatment recommendations
        "treatment", "paggamot", "therapy", "intervention", "procedure", 
        "operation", "operasyon", "surgical", "non-surgical", "medical management",
        "therapeutic", "rehabilitative", "palliative", "preventative"
    ],
    
    "mga_hakbang": [
        # Action words
        "gawin", "simulan", "isagawa", "ipatupad", "implement", "execute", "perform",
        "conduct", "undertake", "carry out", "initiate", "begin", "start", "proceed",
        "follow", "adhere to", "sundin", "sumunod", "tuparin", "execute",
        
        # Step terminology
        "hakbang", "step", "measure", "action", "procedure", "protocol", "process",
        "approach", "method", "technique", "strategy", "intervention", "tactic",
        "activity", "operation", "task", "procedure", "regimen", "course",
        
        # Treatment terms
        "treatment", "therapy", "therapeutic", "intervention", "management", 
        "administration", "application", "delivery", "regimen", "program", 
        "protocol", "procedure", "course", "plan", "schedule", "therapeutic approach",
        
        # Specific interventions
        "exercise", "ehersisyo", "physical therapy", "occupational therapy",
        "speech therapy", "rehabilitation", "pain management", "stress management",
        "cognitive therapy", "behavioral therapy", "paggamot", "therapy",
        
        # Medical procedures
        "surgery", "operasyon", "injection", "turok", "medication administration",
        "pagbibigay ng gamot", "wound care", "pangangalaga ng sugat", "dressing change",
        "assessment", "evaluation", "monitoring", "pagsubaybay", "laboratory test",
        
        # Timing indicators
        "immediate", "agaran", "promptly", "quickly", "urgent", "as soon as possible",
        "daily", "araw-araw", "weekly", "linggu-linggo", "monthly", "regular",
        "scheduled", "periodic", "intermittent", "continuous", "ongoing"
    ],
    
    "pangangalaga": [
        # Care terms
        "pangangalaga", "care", "alaga", "alalay", "assist", "support", "help",
        "aid", "pagtulong", "pagkalinga", "pag-aaruga", "alagaan", "ingatan",
        "assistance", "helping", "supporting", "maintaining", "preserving",
        
        # Care types
        "medical care", "nursing care", "supportive care", "palliative care",
        "preventive care", "rehabilitative care", "long-term care",
        "home care", "pangangalaga sa bahay", "self-care", "pangangalaga sa sarili",
        
        # Monitoring terms
        "monitor", "subaybayan", "observe", "obserbahan", "bantayan", "check",
        "assess", "watch", "pagmamasid", "observation", "assessment", "evaluation",
        "tracking", "measuring", "recording", "documentation", "reporting",
        
        # Care activities
        "feeding", "pagpapakain", "bathing", "pagliligo", "toileting", 
        "hygiene", "kalinisan", "dressing", "pagbibihis", "grooming", "pag-aayos",
        "positioning", "pagpoposisyon", "transfer", "paglilipat", "turning",
        "wound care", "pangangalaga ng sugat", "medication administration",
        
        # Caregiver references
        "caregiver", "tagapag-alaga", "caretaker", "nurse", "nars", "attendant",
        "family caregiver", "pamilyang tagapag-alaga", "professional caregiver",
        "home health aide", "nursing assistant", "healthcare provider",
        
        # Warning and safety terms
        "bantay", "watch", "track", "signs", "symptoms", "complications",
        "adverse effects", "side effects", "red flags", "warning signs",
        "deterioration", "changes", "pagbabago", "improvement", "pagbuti"
    ],
    
    "pagbabago_sa_pamumuhay": [
        # Change terminology
        "pagbabago", "change", "modification", "adjustment", "adaptation", 
        "transition", "shift", "alteration", "transformation", "conversion",
        "reforming", "restructuring", "revising", "adapting", "modifying",
        
        # Lifestyle terms
        "pamumuhay", "lifestyle", "daily life", "araw-araw na pamumuhay", 
        "way of life", "living condition", "daily routine", "karaniwang gawain",
        "habits", "ugali", "practices", "patterns", "behaviors", "pag-uugali",
        
        # Diet and nutrition
        "diet", "nutrition", "pagkain", "nutrisyon", "eating habits", "food intake",
        "nutritional needs", "dietary restriction", "meal planning", "hydration",
        "low sodium", "high protein", "diabetic diet", "heart healthy", "balanced diet",
        
        # Physical activity
        "physical activity", "exercise", "ehersisyo", "activity level", 
        "movement", "galaw", "active lifestyle", "fitness", "low impact exercise",
        "strength training", "stretching", "balance exercises", "walking program",
        
        # Sleep patterns
        "sleep", "tulog", "sleeping pattern", "sleep hygiene", "rest", "pahinga",
        "bedtime routine", "sleep schedule", "sleep quality", "insomnia management",
        
        # Stress management
        "stress management", "relaxation", "coping strategies", "meditation",
        "mindfulness", "breathing techniques", "anxiety reduction", "mental health care",
        
        # Health behaviors
        "smoking cessation", "alcohol reduction", "substance management",
        "medication adherence", "pagsunod sa gamot", "preventive care",
        "health monitoring", "self-management", "self-care", "risk reduction"
    ]
}