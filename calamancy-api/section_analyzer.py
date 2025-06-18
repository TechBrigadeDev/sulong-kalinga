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
        sentence_docs = []
    
    # Define section keywords with expanded terms for better matching
    if doc_type.lower() == "assessment":
        section_keywords = {
            "mga_sintomas": [
                # General symptom terms
                "sakit", "sintomas", "hirap", "dumaranas", "nararamdaman", "nakakaranas", 
                "nagpapakita", "kondisyon", "diagnosed", "masakit", "sumasakit", "kirot",
                "nahihirapan", "problema sa", "nagkaroon ng", "nagdurusa sa",
                
                # Specific symptoms and manifestations
                "pain", "ache", "soreness", "tenderness", "discomfort", "burning",
                "stabbing", "throbbing", "radiating", "chronic pain", "acute pain",
                "neuropathic", "muscle pain", "joint pain", "abdominal pain",
                "chest pain", "back pain", "headache", "sakit ng ulo",
                
                # Neurological symptoms
                "dizziness", "vertigo", "lightheadedness", "fainting", "syncope",
                "seizure", "tremors", "shaking", "panginginig", "numbness", "tingling",
                "pamamanhid", "weakness", "paralysis", "incoordination",
                
                # Respiratory symptoms
                "cough", "ubo", "shortness of breath", "hirap huminga", "wheezing",
                "huni", "sputum", "plema", "hemoptysis", "dugo sa ubo", 
                "chest tightness", "kapos sa hininga"
            ],
            
            "kalagayan_pangkatawan": [
                # Basic physical terms
                "pisikal", "katawan", "lakas", "kahinaan", "balance", "koordinasyon",
                "physical", "stance", "posture", "tindig", "galaw", "coordination",
                
                # Body systems & functions
                "cardiovascular", "respiratory", "musculoskeletal", "heart", "puso", 
                "baga", "lungs", "atay", "liver", "bato", "kidney", "presyon", 
                "blood pressure", "weight", "timbang", "pulse", "rhythm", "pulso",
                "breathing", "paghinga", "oxygen", "muscle", "kalamnan", "joints",
                "neuromuscular", "respiratory", "digestion", "excretion", "circulation",
                
                # Physical measurements
                "BMI", "pulse ox", "vital signs", "blood pressure", "temperature",
                "height", "weight", "range of motion", "muscle strength", "endurance"
            ],
            
            "kalagayan_mental": [
                # Cognitive aspects
                "memorya", "nakalimutan", "nalilito", "naguguluhan", "pagkalito",
                "cognitive", "mental", "isip", "memory", "forgetful", "disorientation",
                "concentration", "disoriented", "confused", "hindi matandaan",
                "attention span", "awareness", "alertness", "orientation",
                "decision-making", "judgment", "reasoning", "comprehension",
                
                # Emotional aspects
                "emosyon", "emotion", "kalungkutan", "pagkabalisa", "depression", 
                "anxiety", "mood", "affect", "irritability", "agitation",
                "sadness", "hopelessness", "fear", "takot", "worry", "stress",
                "coping", "emotional state", "feelings", "damdamin"
            ],
            
            "aktibidad": [
                # Daily activities
                "gawain", "aktibidad", "activity", "araw-araw", "daily", "self-care",
                "routine", "schedule", "regular", "tasks", "chores", "responsibilities",
                
                # ADLs
                "pagligo", "bathing", "pagbibihis", "dressing", "pagkain", "feeding",
                "toileting", "hygiene", "grooming", "eating", "sleeping", "pagtulog",
                
                # Mobility
                "mobility", "paggalaw", "walking", "paglalakad", "trabaho", "work",
                "standing", "pagtayo", "bed mobility", "transfers", "wheelchair",
                "walker", "cane", "tungkod", "assistive device", "mobility aid",
                
                # IADLs
                "cooking", "cleaning", "shopping", "finances", "transportation",
                "medication management", "communication", "phone", "computer"
            ],
            
            "kalagayan_social": [
                # Relationships
                "pamilya", "asawa", "anak", "social", "pakikisalamuha", "kaibigan", 
                "friend", "spouse", "family", "relatives", "kamag-anak", "apo",
                
                # Social environment
                "kapitbahay", "komunidad", "simbahan", "church", "pakikitungo", 
                "ugnayan", "community", "neighborhood", "support system", "network",
                "social engagement", "participation", "interaction", "isolation",
                "loneliness", "withdrawal"
            ]
        }
    else:  # Evaluation document sections
        section_keywords = {
            "pangunahing_rekomendasyon": [
                # Direct recommendation terms
                "inirerekomenda", "iminumungkahi", "pinapayuhan", "dapat", "kailangan", 
                "mahalagang", "ipinapayo", "nirerekomenda", "binigyang-diin", "iminungkahi", 
                "kinakailangan", "mabuting", "mainam", "mas mainam", "sulit",
                
                # Priority language
                "kritikal", "mahalaga", "essential", "urgent", "agarang",
                "high priority", "immediate", "necessary", "importante",
                "crucial", "vital", "indispensable", "non-negotiable",
                
                # Professional advice
                "ayon sa eksperto", "batay sa research", "evidence shows", 
                "clinical guidelines", "standard practice", "best practice"
            ],
            
            "pangangalaga": [
                # Care management
                "pag-iwas", "monitor", "obserbahan", "bantayan", "management", 
                "symptom management", "preventive care", "care techniques", 
                "maintenance", "prevention", "alaga", "supervision", 
                "pagsubaybay", "monitoring", "observation",
                
                # Specific care procedures
                "wound care", "skin care", "pressure relief", "pain management",
                "pangangalaga", "pagbabantay", "pagbibigay ng gamot", "positioning",
                "comfort measures", "palliative care", "supportive care"
            ],
            
            "mga_hakbang": [
                # Action verbs
                "simulan", "gawin", "ipatupad", "isagawa", "sundin", "interventions", 
                "measures", "implement", "execute", "perform", "conduct", "carry out",
                "undertake", "administer", "provide", "apply", "deliver", "follow",
                
                # Treatment terms
                "treatment", "therapy", "program", "regimen", "protocol", "procedure",
                "exercises", "techniques", "methods", "approaches", "strategies",
                "interventions", "rehabilitation", "restoration", "recovery"
            ],
            
            "pagbabago_sa_pamumuhay": [
                # Lifestyle components
                "diet", "nutrition", "pagkain", "ehersisyo", "physical activity", 
                "lifestyle", "routine", "habits", "daily schedule", "araw-araw",
                "sleep", "stress management", "work-life balance", "recreation",
                
                # Modification terms
                "adjustment", "modification", "change", "shift", "transition",
                "adaptation", "pagbabago", "pag-adjust", "pagsasaayos", "improvement",
                "enhancement", "reduction", "increase", "moderation"
            ]
        }
    
    # Initialize scoring for each sentence-section pair
    sentence_scores = {}
    for i, (sent, doc) in enumerate(zip(sentences, sentence_docs)):
        sent_lower = sent.lower()
        scores = {}
        
        # Score each section for this sentence
        for section, keywords in section_keywords.items():
            # Base score from keyword matches
            keyword_matches = sum(1 for keyword in keywords if keyword in sent_lower)
            
            # Entity-based matching with stronger weighting
            entity_score = 0
            for ent in doc.ents:
                # Map entity types to relevant sections
                if section == "mga_sintomas" and ent.label_ in ["SYMPTOM", "DISEASE"]:
                    entity_score += 2
                elif section == "kalagayan_pangkatawan" and ent.label_ in ["BODY_PART", "MEASUREMENT"]:
                    entity_score += 2
                elif section == "kalagayan_mental" and ent.label_ in ["COGNITIVE", "EMOTION"]:
                    entity_score += 2
                elif section == "aktibidad" and ent.label_ in ["ADL", "SAFETY"]:
                    entity_score += 2
                elif section == "kalagayan_social" and ent.label_ in ["SOCIAL_REL", "SOCIAL_ACT"]:
                    entity_score += 2
                elif section == "pangunahing_rekomendasyon" and ent.label_ in ["RECOMMENDATION", "HEALTHCARE_REFERRAL"]:
                    entity_score += 2
                elif section == "pangangalaga" and ent.label_ in ["TREATMENT", "MONITORING"]:
                    entity_score += 2
                elif section == "mga_hakbang" and ent.label_ in ["TREATMENT_METHOD", "EQUIPMENT"]:
                    entity_score += 2
                elif section == "pagbabago_sa_pamumuhay" and ent.label_ in ["DIET_RECOMMENDATION", "FOOD"]:
                    entity_score += 2
            
            # Position-based scoring - first sentences often indicate topic
            position_score = 1.5 if i == 0 else (0.5 if i == len(sentences)-1 else 0)
            
            # Calculate total score
            total_score = keyword_matches + entity_score + position_score
            if total_score > 0:
                scores[section] = total_score
                
        sentence_scores[i] = (sent, scores)
    
    # Assign sentences to sections based on scores
    result = {}
    assigned_sentences = set()
    
    # Initialize all sections to empty arrays
    for section in section_keywords.keys():
        result[section] = []
    
    # First pass: Assign sentences with clear high scores
    for section in section_keywords.keys():
        section_candidates = []
        
        for i, (sent, scores) in sentence_scores.items():
            if i in assigned_sentences:
                continue
                
            if section in scores and scores[section] >= 2:  # Strong signal
                section_candidates.append((i, sent, scores[section]))
        
        # Sort by score and add top sentences
        for i, sent, score in sorted(section_candidates, key=lambda x: x[2], reverse=True):
            result[section].append(sent)
            assigned_sentences.add(i)
    
    # Second pass: Assign remaining sentences to their best-matching section
    for i, (sent, scores) in sentence_scores.items():
        if i in assigned_sentences:
            continue
            
        # Find best section for this sentence
        if scores:
            best_section = max(scores.items(), key=lambda x: x[1])[0]
            result[best_section].append(sent)
            assigned_sentences.add(i)
        else:
            # Default assignment for sentences with no matches
            default_section = "mga_sintomas" if doc_type.lower() == "assessment" else "pangunahing_rekomendasyon"
            result[default_section].append(sent)
            assigned_sentences.add(i)
    
    # Ensure at least one section has content
    if all(not sents for sents in result.values()) and sentences:
        # Create fallback section with first sentence
        if doc_type.lower() == "assessment":
            result["mga_sintomas"] = [sentences[0]]
        else:
            result["pangunahing_rekomendasyon"] = [sentences[0]]
    
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
    
    # Strong signal patterns for each section
    section_patterns = {
        "pangunahing_rekomendasyon": [
            r'inirerekomenda(ng)? (ko|kong|namin|naming) (na|ang)',
            r'iminumungkahi(ng)? (ko|kong|namin|naming) (na|ang)',
            r'pinapayuhan (ko|kong|namin|naming) (na|ang)',
            r'(una sa lahat|bilang pangunahing hakbang)',
            r'(dapat|kailangan|kinakailangan|mahalagang) (na )?'
        ],
        
        "mga_hakbang": [
            r'(simulan|gawin|ipatupad|isagawa) ang',
            r'(susunod na hakbang|sa|mga|bilang) (hakbang|steps|interventions)',
            r'(dapat|kailangang) (din|rin) (na )?',
            r'(pangalawang|pangatlo|kasunod na) hakbang'
        ],
        
        "pangangalaga": [
            r'(para sa|upang|sa) (pangangalaga|pag-iwas|pag-aalaga)',
            r'(i-monitor|obserbahan|bantayan|subaybayan)',
            r'(sa pang-araw-araw na pangangalaga|daily care)',
            r'(sa bahay|home care|home management)',
            r'(kapag|kung|sa) (nagkaroon|nagkakaroon)',
            r'(palaging|regular na|always|consistently)'
        ],
        
        "pagbabago_sa_pamumuhay": [
            r'(pagbabago sa|baguhin ang|adjustment sa) (pamumuhay|lifestyle)',
            r'(diet|nutrisyon|nutrition|pagkain)',
            r'(exercise|ehersisyo|physical activity)',
            r'(normal na routine|daily habits|araw-araw)',
            r'(long-term|pangmatagalang|sa hinaharap|future)'
        ]
    }
    
    # First pass: Match sentences to sections based on strong signals
    assigned_sentences = set()
    
    for section, patterns in section_patterns.items():
        for i, sent in enumerate(sentences):
            if i in assigned_sentences:
                continue
                
            sent_lower = sent.lower()
            for pattern in patterns:
                if re.search(pattern, sent_lower):
                    sections[section].append(sent)
                    assigned_sentences.add(i)
                    break
    
    # Second pass: Analyze entities for remaining sentences
    for i, (sent, doc) in enumerate(zip(sentences, sentence_docs)):
        if i in assigned_sentences:
            continue
            
        # Analyze entities
        recommendation_count = 0
        treatment_count = 0
        monitoring_count = 0
        lifestyle_count = 0
        
        for ent in doc.ents:
            if ent.label_ in ["RECOMMENDATION", "HEALTHCARE_REFERRAL"]:
                recommendation_count += 1
            elif ent.label_ in ["TREATMENT_METHOD", "TREATMENT", "EQUIPMENT"]:
                treatment_count += 1
            elif ent.label_ in ["MONITORING", "WARNING_SIGN"]:
                monitoring_count += 1
            elif ent.label_ in ["DIET_RECOMMENDATION", "FOOD"]:
                lifestyle_count += 1
        
        # Assign based on entity counts
        max_count = max(recommendation_count, treatment_count, monitoring_count, lifestyle_count)
        
        if max_count > 0:
            if max_count == recommendation_count:
                sections["pangunahing_rekomendasyon"].append(sent)
            elif max_count == treatment_count:
                sections["mga_hakbang"].append(sent)
            elif max_count == monitoring_count:
                sections["pangangalaga"].append(sent)
            elif max_count == lifestyle_count:
                sections["pagbabago_sa_pamumuhay"].append(sent)
            
            assigned_sentences.add(i)
    
    # Third pass: Default assignment of remaining sentences
    for i, sent in enumerate(sentences):
        if i in assigned_sentences:
            continue
            
        # Default to "pangunahing_rekomendasyon" for first sentence
        if i == 0:
            sections["pangunahing_rekomendasyon"].append(sent)
        # For remaining sentences, distribute evenly across sections that need content
        else:
            # Find the section with the least content
            empty_sections = [s for s, sentences in sections.items() if not sentences]
            if empty_sections:
                sections[empty_sections[0]].append(sent)
            else:
                min_section = min(sections.items(), key=lambda x: len(x[1]))[0]
                sections[min_section].append(sent)
    
    # Convert lists to strings
    return {section: " ".join(sents) for section, sents in sections.items() if sents}

def summarize_section_text(section_text, section_name, max_length=350):
    """Create a concise summary of a potentially long section with proper sentence selection."""
    # Skip if the section is already short enough
    if len(section_text) <= max_length:
        return section_text
    
    # First try to generate a synthesized summary
    try:
        synthesized_summary = synthesize_section_summary(section_text, section_name, max_length)
        if synthesized_summary and len(synthesized_summary) <= max_length and len(synthesized_summary) > 50:
            return synthesized_summary
    except Exception as e:
        print(f"Error synthesizing summary for {section_name}: {e}")
        # If synthesis fails, continue with the original method
    
    # If synthesis didn't work or produced poor results, use the original selection-based method
    # Process the section text
    doc = nlp(section_text)
    
    # Extract key sentences based on section type
    section_sentences = split_into_sentences(section_text)
    
    # Fix incomplete sentences - common issue in extracted sections
    fixed_sentences = []
    for i, sent in enumerate(section_sentences):
        # Skip empty sentences
        if not sent.strip():
            continue
            
        # Check if sentence is a fragment or starts with lowercase (likely fragment)
        if (len(sent.strip()) < 20 or sent.strip()[0].islower()) and i > 0:
            # This might be a sentence fragment - combine with previous
            if fixed_sentences:
                fixed_sentences[-1] = fixed_sentences[-1] + " " + sent
            else:
                # If no previous sentence, try to make it standalone
                if not sent.strip()[0].isupper():
                    capitalized = sent[0].upper() + sent[1:]
                    fixed_sentences.append(capitalized)
                else:
                    fixed_sentences.append(sent)
        else:
            # Ensure sentence ends with punctuation
            if not sent.strip()[-1] in ['.', '!', '?']:
                sent = sent + "."
            fixed_sentences.append(sent)
    
    # Use fixed sentences if we have any, otherwise use originals
    if fixed_sentences:
        section_sentences = fixed_sentences
    
    if len(section_sentences) <= 3:
        # If only 1-3 sentences, keep them all but truncate if needed
        summary = " ".join(section_sentences)
        if len(summary) > max_length:
            # Find natural break points for better truncation
            last_period = summary[:max_length-3].rfind('.')
            last_comma = summary[:max_length-3].rfind(',')
            last_break = max(last_period, last_comma)
            
            if last_break > max_length/2:  # Only use break point if it's reasonably far in
                summary = summary[:last_break+1] + "..."
            else:
                summary = summary[:max_length-3] + "..."
        return summary
    
    # Score sentences based on information content and relevance
    scored_sentences = []
    
    for i, sent in enumerate(section_sentences):
        score = 0
        sent_doc = nlp(sent)
        
        # Position score - prioritize first sentences for context
        if i == 0:
            score += 5  # First sentence is crucial for context
        elif i == 1:
            score += 3  # Second sentence often contains important details
        elif i == len(section_sentences) - 1:
            score += 2  # Last sentence may have conclusions/recommendations
        
        # Length preference - avoid very short or very long sentences
        sent_length = len(sent)
        if 40 < sent_length < 120:
            score += 2  # Ideal length
        elif 20 < sent_length <= 40:
            score += 1  # Short but acceptable
        elif sent_length <= 20:
            score -= 1  # Too short
        elif sent_length > 200:
            score -= 2  # Too long
        
        # Check if sentence begins with a capital letter (better formed)
        if sent.strip() and sent.strip()[0].isupper():
            score += 2
        
        # Entity and information density scoring
        entity_count = len([ent for ent in sent_doc.ents])
        score += min(4, entity_count)  # Up to 4 points for entities
        
        # Check for section-specific key terms
        key_terms = section_key_terms.get(section_name, [])
        for term in key_terms:
            if term.lower() in sent.lower():
                score += 2
        
        # Section-specific scoring
        if section_name == "mga_sintomas" or "sintomas" in section_name:
            # Prioritize sentences with clear symptom descriptions
            for ent in sent_doc.ents:
                if ent.label_ in ["DISEASE", "SYMPTOM"]:
                    score += 5  # Strong boost for symptom mentions
                elif ent.label_ == "BODY_PART":
                    score += 2
            
            # Check for severity and duration words
            severity_terms = ["matindi", "malubha", "banayad", "moderate", "mild", "severe"]
            duration_terms = ["araw-araw", "linggo", "buwan", "daily", "weekly", "years"]
            
            if any(term in sent.lower() for term in severity_terms):
                score += 3
            if any(term in sent.lower() for term in duration_terms):
                score += 2
                
        elif section_name == "kalagayan_mental" or "mental" in section_name:
            # Prioritize sentences with cognitive status descriptions
            for ent in sent_doc.ents:
                if ent.label_ == "COGNITIVE":
                    score += 5  # Strong boost for cognitive mentions
                elif ent.label_ == "EMOTION":
                    score += 4
            
            # Specific cognitive terms that are high value
            cognitive_terms = ["memorya", "kalituhan", "confusion", "nakalimutan", 
                             "hindi matandaan", "disorientation", "pagkalito"]
            
            if any(term in sent.lower() for term in cognitive_terms):
                score += 4
                
        elif section_name == "aktibidad" or "aktibidad" in section_name:
            # Make sure we have complete sentences about activities
            # Avoid sentence fragments
            if len(sent) < 30 or sent.strip()[0].islower():
                score -= 5  # Significant penalty for likely fragments
                
            activity_terms = ["gawain", "activity", "araw-araw", "limitasyon", 
                             "nahihirapan", "tulong", "assistance"]
            
            if any(term in sent.lower() for term in activity_terms):
                score += 3
                
        elif section_name == "kalagayan_pangkatawan" or "pangkatawan" in section_name:
            # Physical status terms
            for ent in sent_doc.ents:
                if ent.label_ in ["BODY_PART", "MEASUREMENT", "VITAL_SIGNS"]:
                    score += 3
                    
            physical_terms = ["pisikal", "katawan", "vital signs", "presyon", "weight"]
            if any(term in sent.lower() for term in physical_terms):
                score += 2
                
        elif section_name == "kalagayan_social" or "social" in section_name:
            # Social relationships and support
            for ent in sent_doc.ents:
                if ent.label_ in ["SOCIAL_REL", "PER"]:
                    score += 4
                    
            support_terms = ["suporta", "tulong", "pamilya", "asawa", "anak", "apo"]
            if any(term in sent.lower() for term in support_terms):
                score += 3
                
        elif section_name == "pangunahing_rekomendasyon" or "rekomendasyon" in section_name:
            for ent in sent_doc.ents:
                if ent.label_ in ["RECOMMENDATION", "HEALTHCARE_REFERRAL"]:
                    score += 5
                    
            recommendation_terms = ["inirerekomenda", "iminumungkahi", "dapat", "kailangan"]
            if any(term in sent.lower() for term in recommendation_terms):
                score += 4
                
        elif section_name == "mga_hakbang" or "hakbang" in section_name:
            for ent in sent_doc.ents:
                if ent.label_ in ["TREATMENT_METHOD", "TREATMENT", "EQUIPMENT"]:
                    score += 4
                    
            action_terms = ["gawin", "isagawa", "ipatupad", "sundin", "simulan"]
            if any(term in sent.lower() for term in action_terms):
                score += 3
                
        elif section_name == "pangangalaga" or "alaga" in section_name:
            for ent in sent_doc.ents:
                if ent.label_ in ["MONITORING", "WARNING_SIGN"]:
                    score += 4
                    
            care_terms = ["bantayan", "subaybayan", "obserbahan", "i-monitor", "ingatan"]
            if any(term in sent.lower() for term in care_terms):
                score += 3
                
        elif section_name == "pagbabago_sa_pamumuhay" or "pamumuhay" in section_name:
            for ent in sent_doc.ents:
                if ent.label_ in ["DIET_RECOMMENDATION", "FOOD"]:
                    score += 4
                    
            lifestyle_terms = ["diet", "ehersisyo", "exercise", "pagkain", "nutrition"]
            if any(term in sent.lower() for term in lifestyle_terms):
                score += 3
        
        # Store the scored sentence with its original position
        scored_sentences.append((sent, score, i))
    
    # Sort by score (highest first)
    sorted_sentences = sorted(scored_sentences, key=lambda x: x[1], reverse=True)
    
    # Select top sentences, aiming for 3 if possible
    selected_indices = []
    current_length = 0
    target_sentences = min(3, len(section_sentences))
    
    # First pass: get highest scoring sentences
    for sent, score, idx in sorted_sentences[:5]:  # Consider top 5 candidates
        if len(selected_indices) < target_sentences and current_length + len(sent) <= max_length:
            selected_indices.append(idx)
            current_length += len(sent) + 1  # +1 for space
            
    # If we don't have enough yet, try to fill with other sentences
    if len(selected_indices) < target_sentences and current_length < max_length:
        remaining = [(i, sent) for i, sent in enumerate(section_sentences) 
                    if i not in selected_indices]
        
        # Add sentences in order to maintain narrative flow
        for i, sent in sorted(remaining, key=lambda x: x[0]):
            if current_length + len(sent) <= max_length:
                selected_indices.append(i)
                current_length += len(sent) + 1
                
                if len(selected_indices) >= target_sentences:
                    break
    
    # Always include at least one sentence
    if not selected_indices and section_sentences:
        # Try first sentence (usually has context)
        first_sent = section_sentences[0]
        if len(first_sent) <= max_length:
            selected_indices.append(0)
        else:
            # Truncate if too long
            last_period = first_sent[:max_length-3].rfind('.')
            if last_period > 30:
                first_sent = first_sent[:last_period+1] + "..."
            else:
                first_sent = first_sent[:max_length-3] + "..."
            section_sentences[0] = first_sent
            selected_indices.append(0)
    
    # Sort indices to maintain original order (better narrative flow)
    selected_indices.sort()
    
    # Combine sentences in original order
    selected_sentences = [section_sentences[i] for i in selected_indices]
    
    # Final check - ensure the first sentence provides context
    if selected_sentences and not (selected_sentences[0].strip()[0].isupper()):
        # First sentence seems to be a fragment - try to fix
        if section_name == "mga_sintomas":
            selected_sentences[0] = f"Ang pasyente ay nagpapakita ng {selected_sentences[0]}"
        elif section_name == "kalagayan_pangkatawan":
            selected_sentences[0] = f"Pisikal na kalagayan: {selected_sentences[0]}"
        elif section_name == "kalagayan_mental":
            selected_sentences[0] = f"Mental na kalagayan: {selected_sentences[0]}"
        elif section_name == "aktibidad":
            selected_sentences[0] = f"Sa mga pang-araw-araw na gawain, {selected_sentences[0]}"
        elif section_name == "kalagayan_social":
            selected_sentences[0] = f"Sa social na aspeto, {selected_sentences[0]}"
    
    summary = " ".join(selected_sentences)
    
    # Final length check
    if len(summary) > max_length:
        last_period = summary[:max_length-3].rfind('.')
        if last_period > max_length/2:
            summary = summary[:last_period+1] + "..."
        else:
            summary = summary[:max_length-3] + "..."
    
    return summary

def synthesize_section_summary(section_text, section_name, max_length=350):
    """Generate a coherent summary for a section with specific details and context."""
    # Skip if section is too short
    if len(section_text) <= max_length:
        return section_text
        
    # Process section with NLP
    doc = nlp(section_text)
    
    # Extract structured elements for better synthesis
    elements = extract_structured_elements(section_text, section_name)
    
    # Extract subject (person) from the text
    subject = elements.get("subject")
    if not subject:
        for ent in doc.ents:
            if ent.label_ == "PER":
                subject = ent.text
                break
                
    # Default subject if none found
    if not subject:
        if "Lolo" in section_text:
            subject = "Lolo"
        elif "Lola" in section_text:
            subject = "Lola"
        elif "Nanay" in section_text:
            subject = "Nanay"
        elif "Tatay" in section_text:
            subject = "Tatay"
        else:
            subject = "Ang pasyente"
    
    # Generate tailored summary based on section type
    if section_name == "mga_sintomas" or "sintomas" in section_name:
        # SYMPTOMS SECTION WITH HIGHLY SPECIFIC DETAILS
        conditions = elements["conditions"][:2]  # Take up to 2 conditions
        symptoms = elements["symptoms"][:3]      # Take up to 3 symptoms
        severity = elements["severity"][0] if elements["severity"] else ""
        frequency = elements["frequency"][0] if elements["frequency"] else ""
        duration = elements["duration"][0] if elements["duration"] else ""
        
        # Extract specific problem descriptions instead of generic mentions
        problem_patterns = [
            r'(nagdurusa sa|nakakaranas ng|nahihirapan sa) ([^.]+)',
            r'(specifically|partikular na|lalo na) ([^.]+)'
        ]
        
        specific_problems = []
        for pattern in problem_patterns:
            matches = re.finditer(pattern, section_text)
            for match in matches:
                if len(match.groups()) > 1:
                    specific_detail = match.group(2).strip()
                    if len(specific_detail) > 10 and len(specific_detail) < 100:
                        specific_problems.append(specific_detail)
        
        # Create a coherent sentence about symptoms WITH SPECIFIC DETAILS
        summary = f"{subject} ay "
        
        # Get specific symptom CONTEXT rather than just listing symptoms
        if specific_problems:
            summary += f"nagpapakita ng {specific_problems[0]}"
            if len(specific_problems) > 1:
                summary += f", at {specific_problems[1]}"
        elif conditions or symptoms:
            # ENHANCED: Include specific descriptors with symptoms
            medical_issues = []
            
            # Look for specific descriptions of conditions
            for condition in conditions:
                condition_pattern = re.escape(condition) + r' na ([^.]+)'
                match = re.search(condition_pattern, section_text)
                if match:
                    # Include the description with the condition
                    enhanced_condition = f"{condition} na {match.group(1)}"
                    medical_issues.append(enhanced_condition)
                else:
                    medical_issues.append(condition)
            
            # Look for specific descriptions of symptoms
            for symptom in symptoms:
                if not any(symptom in condition for condition in medical_issues):
                    symptom_pattern = re.escape(symptom) + r' na ([^.]+)'
                    match = re.search(symptom_pattern, section_text)
                    if match:
                        enhanced_symptom = f"{symptom} na {match.group(1)}"
                        medical_issues.append(enhanced_symptom)
                    else:
                        medical_issues.append(symptom)
            
            if medical_issues:
                if len(medical_issues) == 1:
                    summary += f"nagpapakita ng {medical_issues[0]}"
                elif len(medical_issues) == 2:
                    summary += f"nagpapakita ng {medical_issues[0]} at {medical_issues[1]}"
                else:
                    summary += f"nagpapakita ng {', '.join(medical_issues[:-1])}, at {medical_issues[-1]}"
            else:
                summary += "nagpapakita ng mga sintomas na nangangailangan ng atensyon"
        else:
            summary += "nagpapakita ng mga sintomas na nangangailangan ng atensyon"
        
        # Add DETAILED severity and frequency specifics
        if severity and frequency:
            # Look for more detailed description of the severity
            severity_context = None
            severity_pos = section_text.lower().find(severity.lower())
            if severity_pos >= 0:
                start = max(0, severity_pos - 20)
                end = min(len(section_text), severity_pos + len(severity) + 40)
                severity_context = section_text[start:end]
                
            if severity_context and len(severity_context) < 80:
                summary += f", na {severity_context}"
            else:
                summary += f" na {severity} at nangyayari nang {frequency}"
        elif severity:
            summary += f" na {severity}"
        elif frequency:
            summary += f" na nangyayari nang {frequency}"
        
        # Add SPECIFIC duration details
        if duration:
            # Extract more context for duration if possible
            duration_pos = section_text.lower().find(duration.lower())
            if duration_pos >= 0:
                start = max(0, duration_pos - 10)
                end = min(len(section_text), duration_pos + len(duration) + 30)
                duration_context = section_text[start:end]
                
                # Clean up the context for better readability
                if '.' in duration_context:
                    duration_context = duration_context.split('.')[0]
                
                if len(duration_context) < 70:
                    summary += f". Ito ay nagsimula {duration_context}"
                else:
                    summary += f" mula {duration}"
            else:
                summary += f" mula {duration}"
        
        # Ensure the summary ends with proper punctuation
        if not summary.endswith(('.', '!', '?')):
            summary += "."
        
        # Add SPECIFIC second sentence about impact on daily life
        impact_patterns = [
            r'(nakakaapekto sa|nagdudulot ng|nagiging sanhi ng|nakakahadlang sa) ([^.]+)',
            r'(dahil dito|dahil sa kondisyong ito|bunga nito) ([^.]+)'
        ]
        
        for pattern in impact_patterns:
            match = re.search(pattern, section_text)
            if match and len(match.groups()) > 1:
                impact = match.group(0).strip()
                if len(impact) > 10:
                    summary += f" {impact.capitalize()}."
                    break
        
        # Add roller-coaster pattern enhancement with SPECIFIC VALUES
        if "roller-coaster pattern" in section_text.lower() or "blood sugar" in section_text.lower():
            pattern_match = re.search(r'roller-coaster pattern\s+([^.]+)', section_text, re.IGNORECASE)
            if pattern_match:
                pattern_desc = pattern_match.group(1).strip()
                blood_sugar_values = re.search(r'(\d+\s*mg\/dL)', section_text)
                value_text = blood_sugar_values.group(1) if blood_sugar_values else ""
                
                if "diabetes" in summary.lower() and "pattern" in summary.lower() and pattern_desc:
                    # Replace generic pattern with specific description
                    summary = re.sub(r'diabetes [^.]*pattern', 
                                   f"diabetes na may roller-coaster pattern {pattern_desc} {value_text}", 
                                   summary)
                elif "blood sugar" in summary.lower() and value_text:
                    # Add specific values to blood sugar mentions
                    summary = summary.replace("blood sugar", f"blood sugar ({value_text})")
        
        return summary.strip()
        
    elif section_name == "kalagayan_pangkatawan" or "pangkatawan" in section_name:
        # PHYSICAL CONDITION SECTION WITH SPECIFIC MEASUREMENTS AND ASSESSMENTS
        measurements = elements["vital_signs"]
        limitations = elements["limitations"]
        body_parts = elements["body_parts"]
        
        summary = ""
        
        # Extract SPECIFIC vital signs with EXACT VALUES
        if measurements:
            # Look for actual measurement values in text
            measurement_values = []
            value_patterns = [
                r'\b\d+(?:\.\d+)?\s*(?:kg|cm|lbs|mg/dL|mmHg|bpm|°C|°F)\b',  # Values with units
                r'\b\d+/\d+\s*(?:mmHg)?\b',  # Blood pressure format
                r'\b\d+(?:\.\d+)?%\b'  # Percentage values
            ]
            
            for pattern in value_patterns:
                for match in re.finditer(pattern, section_text):
                    value = match.group(0)
                    # Look for context around this value
                    pos = match.start()
                    start = max(0, pos - 30)
                    end = min(len(section_text), pos + len(value) + 30)
                    context = section_text[start:end]
                    
                    # Try to extract what this measurement is
                    measure_types = ["blood sugar", "glucose", "blood pressure", "presyon", "timbang", "oxygen", "temperatura"]
                    for measure in measure_types:
                        if measure in context.lower():
                            measurement_values.append(f"{measure} na {value}")
                            break
                    else:
                        # If no specific type found but in measurements list
                        for measure in measurements:
                            if measure in context:
                                measurement_values.append(f"{measure} ({value})")
                                break
            
            # Start with specific measurements and their actual values
            if measurement_values:
                if len(measurement_values) == 1:
                    summary += f"Ang sukat ay nagpapakita ng {measurement_values[0]}. "
                else:
                    summary += f"Ang mga sukat ay nagpapakita ng {measurement_values[0]} at {measurement_values[1]}. "
            else:
                # Fall back to generic measurements if no specific values found
                measurement_text = measurements[0]
                if len(measurements) > 1:
                    summary += f"Ang mga sukat ay nagpapakita ng {measurement_text}, kasama ang {measurements[1]}. "
                else:
                    summary += f"Ang mga sukat ay nagpapakita ng {measurement_text}. "
        
        # Add SPECIFIC physical limitations with affected body parts and detailed descriptions
        if limitations:
            # Look for more detailed explanations of limitations
            detailed_limitations = []
            
            for limitation in limitations:
                # Search for extended descriptions of this limitation
                limitation_pattern = re.escape(limitation) + r'[,.;]?\s+([^.]+)'
                match = re.search(limitation_pattern, section_text)
                if match:
                    detailed_limitations.append(f"{limitation} ({match.group(1).strip()})")
                else:
                    detailed_limitations.append(limitation)
            
            limitation_text = detailed_limitations[0] if detailed_limitations else limitations[0]
            
            if body_parts:
                # Look for specific impacts on these body parts
                body_part_details = {}
                for body_part in body_parts:
                    bp_pattern = re.escape(body_part) + r'[,.;]?\s+([^.]+)'
                    match = re.search(bp_pattern, section_text)
                    if match:
                        body_part_details[body_part] = match.group(1).strip()
                
                # Construct detailed body part text
                if body_part_details:
                    body_parts_text = ""
                    for i, (part, detail) in enumerate(list(body_part_details.items())[:2]):
                        if i == 0:
                            body_parts_text = f"{part} ({detail})"
                        else:
                            body_parts_text += f" at {part} ({detail})"
                    
                    summary += f"{subject} ay may limitasyon sa {limitation_text}, partikular sa {body_parts_text}. "
                else:
                    # Fall back to basic body parts
                    summary += f"{subject} ay may limitasyon sa {limitation_text}, partikular sa "
                    if len(body_parts) == 1:
                        summary += f"{body_parts[0]}. "
                    else:
                        summary += f"{body_parts[0]} at {body_parts[1]}. "
            else:
                summary += f"{subject} ay nagpapakita ng {limitation_text}. "
        
        # If no measurements or limitations yet, add specific physical condition information
        if not summary and body_parts:
            # Look for specific issues with these body parts
            for body_part in body_parts:
                body_part_pattern = re.escape(body_part) + r'[,.;]?\s+([^.]+)'
                match = re.search(body_part_pattern, section_text)
                if match:
                    condition = match.group(1).strip()
                    summary += f"{subject} ay may kondisyon na nakakaapekto sa {body_part} na {condition}. "
                    break
            
            # If no specific condition found
            if not summary:
                summary += f"{subject} ay may kondisyon na nakakaapekto sa {', '.join(body_parts[:2])}. "
            
        # If still no summary, create a specific one based on key phrases in text
        if not summary:
            # Check for specific physical status descriptions
            status_patterns = [
                r'(pisikal na kalagayan|kondisyon) (?:ni|ng) [^.]+ (ay|na) ([^.]+)',
                r'(nagpapakita|nakakaranas) ng ([^.]+) (sa|ang) [^.]+ (katawan|pisikal)'
            ]
            
            for pattern in status_patterns:
                match = re.search(pattern, section_text)
                if match:
                    description = match.group(0)
                    summary += f"{description}. "
                    break
            
            if not summary:
                # Extract glucose/diabetes details if present
                if "blood sugar" in section_text.lower() or "glucose" in section_text.lower():
                    # Look for specific patterns in blood sugar
                    bs_patterns = [
                        r'(blood sugar|glucose) [^.]+ (\d+[^.]+)',
                        r'(mataas|mababa|unstable|hindi stable)[^.]+ (blood sugar|glucose)'
                    ]
                    
                    for pattern in bs_patterns:
                        match = re.search(pattern, section_text, re.IGNORECASE)
                        if match:
                            summary += f"Sa pisikal na kalagayan ni {subject}, may {match.group(0)}. "
                            break
                    
                    if not summary:
                        summary += f"Sa pisikal na kalagayan ni {subject}, mapapansin ang hindi stabilized na blood sugar levels. "
                else:
                    summary += f"Ang pisikal na kalagayan ni {subject} ay nangangailangan ng atensyon. "
        
        # Add specific details about meal patterns and their effects if mentioned
        meal_pattern_match = re.search(r'(meal|pagkain)[^.]+pattern[^.]+', section_text, re.IGNORECASE)
        if meal_pattern_match and len(summary) < max_length - 100:
            meal_desc = meal_pattern_match.group(0)
            if len(meal_desc) < 80:
                summary += f"Nakikita ang {meal_desc} na nakakaapekto sa kanyang kalusugan. "
        
        # Add specific resistance details if present
        resistance_match = re.search(r'(resistance|pagtutol|ayaw)[^.]+', section_text, re.IGNORECASE)
        if resistance_match and len(summary) < max_length - 80:
            resistance_desc = resistance_match.group(0)
            if len(resistance_desc) < 70:
                summary += f"May {resistance_desc}. "
                
        # Add roller-coaster pattern details if present
        if "roller-coaster pattern" in section_text.lower() or "blood sugar" in section_text.lower():
            pattern_match = re.search(r'roller-coaster pattern\s+([^.]+)', section_text, re.IGNORECASE)
            if pattern_match:
                pattern_desc = pattern_match.group(1).strip()
                blood_sugar_values = re.search(r'(\d+\s*mg\/dL)', section_text)
                value_text = blood_sugar_values.group(1) if blood_sugar_values else ""
                
                if "diabetes" in summary.lower() and "pattern" in summary.lower() and pattern_desc:
                    # Replace generic pattern with specific description
                    summary = re.sub(r'diabetes [^.]*pattern', 
                                   f"diabetes na may roller-coaster pattern {pattern_desc} {value_text}", 
                                   summary)
                elif "blood sugar" in summary.lower() and value_text:
                    # Add specific values to blood sugar mentions
                    summary = summary.replace("blood sugar", f"blood sugar ({value_text})")

        return summary.strip()
        
    elif section_name == "kalagayan_pangkatawan" or "pangkatawan" in section_name:
        # PHYSICAL CONDITION SECTION WITH SPECIFIC MEASUREMENTS AND ASSESSMENTS
        measurements = elements["vital_signs"]
        limitations = elements["limitations"]
        body_parts = elements["body_parts"]
        
        summary = ""
        
        # Extract SPECIFIC vital signs with EXACT VALUES
        if measurements:
            # Look for actual measurement values in text
            measurement_values = []
            value_patterns = [
                r'\b\d+(?:\.\d+)?\s*(?:kg|cm|lbs|mg/dL|mmHg|bpm|°C|°F)\b',  # Values with units
                r'\b\d+/\d+\s*(?:mmHg)?\b',  # Blood pressure format
                r'\b\d+(?:\.\d+)?%\b'  # Percentage values
            ]
            
            for pattern in value_patterns:
                for match in re.finditer(pattern, section_text):
                    value = match.group(0)
                    # Look for context around this value
                    pos = match.start()
                    start = max(0, pos - 30)
                    end = min(len(section_text), pos + len(value) + 30)
                    context = section_text[start:end]
                    
                    # Try to extract what this measurement is
                    measure_types = ["blood sugar", "glucose", "blood pressure", "presyon", "timbang", "oxygen", "temperatura"]
                    for measure in measure_types:
                        if measure in context.lower():
                            measurement_values.append(f"{measure} na {value}")
                            break
                    else:
                        # If no specific type found but in measurements list
                        for measure in measurements:
                            if measure in context:
                                measurement_values.append(f"{measure} ({value})")
                                break
            
            # Start with specific measurements and their actual values
            if measurement_values:
                if len(measurement_values) == 1:
                    summary += f"Ang sukat ay nagpapakita ng {measurement_values[0]}. "
                else:
                    summary += f"Ang mga sukat ay nagpapakita ng {measurement_values[0]} at {measurement_values[1]}. "
            else:
                # Fall back to generic measurements if no specific values found
                measurement_text = measurements[0]
                if len(measurements) > 1:
                    summary += f"Ang mga sukat ay nagpapakita ng {measurement_text}, kasama ang {measurements[1]}. "
                else:
                    summary += f"Ang mga sukat ay nagpapakita ng {measurement_text}. "
        
        # Add SPECIFIC physical limitations with affected body parts and detailed descriptions
        if limitations:
            # Look for more detailed explanations of limitations
            detailed_limitations = []
            
            for limitation in limitations:
                # Search for extended descriptions of this limitation
                limitation_pattern = re.escape(limitation) + r'[,.;]?\s+([^.]+)'
                match = re.search(limitation_pattern, section_text)
                if match:
                    detailed_limitations.append(f"{limitation} ({match.group(1).strip()})")
                else:
                    detailed_limitations.append(limitation)
            
            limitation_text = detailed_limitations[0] if detailed_limitations else limitations[0]
            
            if body_parts:
                # Look for specific impacts on these body parts
                body_part_details = {}
                for body_part in body_parts:
                    bp_pattern = re.escape(body_part) + r'[,.;]?\s+([^.]+)'
                    match = re.search(bp_pattern, section_text)
                    if match:
                        body_part_details[body_part] = match.group(1).strip()
                
                # Construct detailed body part text
                if body_part_details:
                    body_parts_text = ""
                    for i, (part, detail) in enumerate(list(body_part_details.items())[:2]):
                        if i == 0:
                            body_parts_text = f"{part} ({detail})"
                        else:
                            body_parts_text += f" at {part} ({detail})"
                    
                    summary += f"{subject} ay may limitasyon sa {limitation_text}, partikular sa {body_parts_text}. "
                else:
                    # Fall back to basic body parts
                    summary += f"{subject} ay may limitasyon sa {limitation_text}, partikular sa "
                    if len(body_parts) == 1:
                        summary += f"{body_parts[0]}. "
                    else:
                        summary += f"{body_parts[0]} at {body_parts[1]}. "
            else:
                summary += f"{subject} ay nagpapakita ng {limitation_text}. "
        
        # If no measurements or limitations yet, add specific physical condition information
        if not summary and body_parts:
            # Look for specific issues with these body parts
            for body_part in body_parts:
                body_part_pattern = re.escape(body_part) + r'[,.;]?\s+([^.]+)'
                match = re.search(body_part_pattern, section_text)
                if match:
                    condition = match.group(1).strip()
                    summary += f"{subject} ay may kondisyon na nakakaapekto sa {body_part} na {condition}. "
                    break
            
            # If no specific condition found
            if not summary:
                summary += f"{subject} ay may kondisyon na nakakaapekto sa {', '.join(body_parts[:2])}. "
            
        # If still no summary, create a specific one based on key phrases in text
        if not summary:
            # Check for specific physical status descriptions
            status_patterns = [
                r'(pisikal na kalagayan|kondisyon) (?:ni|ng) [^.]+ (ay|na) ([^.]+)',
                r'(nagpapakita|nakakaranas) ng ([^.]+) (sa|ang) [^.]+ (katawan|pisikal)'
            ]
            
            for pattern in status_patterns:
                match = re.search(pattern, section_text)
                if match:
                    description = match.group(0)
                    summary += f"{description}. "
                    break
            
            if not summary:
                # Extract glucose/diabetes details if present
                if "blood sugar" in section_text.lower() or "glucose" in section_text.lower():
                    # Look for specific patterns in blood sugar
                    bs_patterns = [
                        r'(blood sugar|glucose) [^.]+ (\d+[^.]+)',
                        r'(mataas|mababa|unstable|hindi stable)[^.]+ (blood sugar|glucose)'
                    ]
                    
                    for pattern in bs_patterns:
                        match = re.search(pattern, section_text, re.IGNORECASE)
                        if match:
                            summary += f"Sa pisikal na kalagayan ni {subject}, may {match.group(0)}. "
                            break
                    
                    if not summary:
                        summary += f"Sa pisikal na kalagayan ni {subject}, mapapansin ang hindi stabilized na blood sugar levels. "
                else:
                    summary += f"Ang pisikal na kalagayan ni {subject} ay nangangailangan ng atensyon. "
        
        # Add specific details about meal patterns and their effects if mentioned
        meal_pattern_match = re.search(r'(meal|pagkain)[^.]+pattern[^.]+', section_text, re.IGNORECASE)
        if meal_pattern_match and len(summary) < max_length - 100:
            meal_desc = meal_pattern_match.group(0)
            if len(meal_desc) < 80:
                summary += f"Nakikita ang {meal_desc} na nakakaapekto sa kanyang kalusugan. "
        
        # Add specific resistance details if present
        resistance_match = re.search(r'(resistance|pagtutol|ayaw)[^.]+', section_text, re.IGNORECASE)
        if resistance_match and len(summary) < max_length - 80:
            resistance_desc = resistance_match.group(0)
            if len(resistance_desc) < 70:
                summary += f"May {resistance_desc}. "
                
        # Add roller-coaster pattern details if present
        if "roller-coaster pattern" in section_text.lower() or "blood sugar" in section_text.lower():
            pattern_match = re.search(r'roller-coaster pattern\s+([^.]+)', section_text, re.IGNORECASE)
            if pattern_match:
                pattern_desc = pattern_match.group(1).strip()
                blood_sugar_values = re.search(r'(\d+\s*mg\/dL)', section_text)
                value_text = blood_sugar_values.group(1) if blood_sugar_values else ""
                
                if "diabetes" in summary.lower() and "pattern" in summary.lower() and pattern_desc:
                    # Replace generic pattern with specific description
                    summary = re.sub(r'diabetes [^.]*pattern', 
                                   f"diabetes na may roller-coaster pattern {pattern_desc} {value_text}", 
                                   summary)
                elif "blood sugar" in summary.lower() and value_text:
                    # Add specific values to blood sugar mentions
                    summary = summary.replace("blood sugar", f"blood sugar ({value_text})")

        return summary.strip() 
        
    elif section_name == "kalagayan_mental" or "mental" in section_name:
        # MENTAL & COGNITIVE CONDITION SECTION WITH SPECIFIC DETAILS
        cognitive = elements["cognitive_status"]
        emotional = elements["emotional_state"]
        mental = elements["mental_state"]
        
        summary = ""
        
        # Extract SPECIFIC cognitive status details with qualifiers
        cognitive_patterns = [
            r'(memorya|memory|cognition|pag-iisip|cognitive function)([^.]{10,100})',
            r'(nakakalimot|forgets|hindi matandaan|can\'t remember|confused|nalilito)([^.]{10,100})',
            r'(orientation|disorientation|pagkalito|confusion|awareness)[^.]{5,100}'
        ]
        
        specific_cognitive = None
        for pattern in cognitive_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                groups = match.groups()
                if len(groups) >= 2:
                    cognitive_desc = match.group(0)
                    if len(cognitive_desc) < 120:
                        specific_cognitive = cognitive_desc
                        break
        
        # Start with specific cognitive status
        if specific_cognitive:
            if specific_cognitive[0].islower():
                summary += f"Ang mental na kalagayan ni {subject} ay nagpapakita ng {specific_cognitive}. "
            else:
                summary += f"{specific_cognitive}. "
        elif cognitive:
            cognitive_desc = cognitive[0]
            # Check if there's a severity qualifier available
            severity_terms = ["mild", "moderate", "severe", "banayad", "katamtaman", "malubha", 
                            "slight", "significant", "pronounced", "marked"]
            
            severity_qualifier = None
            for term in severity_terms:
                if term in section_text.lower():
                    term_pos = section_text.lower().find(term)
                    if term_pos >= 0:
                        start = max(0, term_pos - 10)
                        end = min(len(section_text), term_pos + len(term) + 30)
                        context = section_text[start:end]
                        if len(context) < 50:
                            severity_qualifier = context
                            break
            
            if severity_qualifier:
                summary += f"Ang mental na kalagayan ni {subject} ay nagpapakita ng {cognitive_desc} na {severity_qualifier}. "
            else:
                summary += f"Ang mental na kalagayan ni {subject} ay nagpapakita ng {cognitive_desc}. "
        
        # Extract SPECIFIC emotional state details
        emotional_patterns = [
            r'(kalungkutan|depression|anxiety|pagkabalisa|stress|worry|emotional state)[^.]{10,100}',
            r'(nag-aalala|nakakaramdam ng|feeling|fearful|natatakot|expressing)[^.]{5,50}(emotion|damdamin|concerns|worries|fears)[^.]{5,50}',
            r'(mood|emosyon|damdamin)[^.]{5,100}'
        ]
        
        specific_emotional = None
        for pattern in emotional_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                emotional_desc = match.group(0)
                if len(emotional_desc) < 100:
                    specific_emotional = emotional_desc
                    break
        
        # Add emotional state details
        if specific_emotional:
            if specific_emotional[0].islower():
                summary += f"Sa emosyonal na aspeto, {subject} ay nagpapakita ng {specific_emotional}. "
            else:
                summary += f"Sa emosyonal na aspeto, {specific_emotional}. "
        elif emotional:
            emotion_text = emotional[0]
            
            # Look for specific manifestations of this emotion
            manifestation_pattern = f"({re.escape(emotion_text)})[^.]*?(ipinapakita|manifested by|expressed through|makikita sa)([^.]+)"
            manifest_match = re.search(manifestation_pattern, section_text, re.IGNORECASE)
            
            if manifest_match and len(manifest_match.groups()) > 2:
                manifestation = manifest_match.group(3).strip()
                summary += f"Sa emosyonal na aspeto, {subject} ay nagpapakita ng {emotion_text} na makikita sa {manifestation}. "
            else:
                summary += f"Sa emosyonal na aspeto, {subject} ay nagpapakita ng {emotion_text}. "
        
        # Add SPECIFIC impact on daily functioning
        impact_patterns = [
            r'(nakakaapekto sa|nagdudulot ng|nagiging sanhi ng|nakakahadlang sa)[^.]{5,50}(araw-araw|daily|functioning|pang-araw-araw)[^.]{5,100}',
            r'(dahil sa|as a result of|because of)[^.]{5,30}(mental|cognitive|emotional)[^.]{5,50}(hindi|can\'t|unable to|nahihirapang)[^.]{5,100}'
        ]
        
        for pattern in impact_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                impact_desc = match.group(0).strip()
                if len(impact_desc) < 120 and len(summary) < max_length - 120:
                    if impact_desc[0].islower():
                        summary += f"Ang kondisyong ito ay {impact_desc}. "
                    else:
                        summary += f"{impact_desc}. "
                    break
        
        # Add SPECIFIC information about situational factors if available
        situation_patterns = [
            r'(kapag|when|during|sa panahon ng|tuwing)[^.]{5,50}(mas|more|gets|becomes|nagiging)[^.]{5,100}',
            r'(worse|better|lumalala|bumubuti)[^.]{5,30}(when|kapag|habang|during)[^.]{5,100}'
        ]
        
        for pattern in situation_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match and len(summary) < max_length - 100:
                situation_desc = match.group(0).strip()
                if len(situation_desc) < 100:
                    summary += f"Napansin na {situation_desc}. "
                    break
        
        # Add SPECIFIC memory details or cognitive test results if mentioned
        memory_patterns = [
            r'(memory test|cognitive assessment|mental status exam|evaluation)[^.]{5,100}',
            r'(nakakaalala|remembers|recalls|nakakalimutan|forgets)[^.]{5,100}'
        ]
        
        for pattern in memory_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match and len(summary) < max_length - 100:
                memory_desc = match.group(0).strip()
                if len(memory_desc) < 100:
                    summary += f"Sa aspeto ng memorya, {memory_desc}. "
                    break
        
        # If not enough content yet, add general mental state description from structured elements
        if not summary and mental:
            summary += f"Ang mental na kalagayan ni {subject} ay maaaring ilarawan bilang {mental[0]}. "
        
        # If still no specific details found, create general mental state description with key observations
        if not summary:
            # Look for mental health terms in text
            mental_terms = ["kalungkutan", "pagkabalisa", "memorya", "nalilito", "nakalimutan", 
                        "nahihirapan", "pag-iisip", "concentration", "orientation", "mood"]
                        
            for term in mental_terms:
                if term in section_text.lower():
                    term_pos = section_text.lower().find(term)
                    if term_pos >= 0:
                        start = max(0, term_pos - 15)
                        end = min(len(section_text), term_pos + len(term) + 40)
                        context = section_text[start:end]
                        
                        if "." in context:
                            context = context.split(".")[0] + "."
                        
                        if len(context) > 15 and len(context) < 100:
                            summary += f"{subject} ay nagpapakita ng {context} "
                            break
            
            if not summary:
                summary += f"Ang mental na kalagayan ni {subject} ay nangangailangan ng maingat na pagsusuri. "
        
        return summary.strip()
        
    elif section_name == "aktibidad" or "aktibidad" in section_name:
        # ACTIVITIES & DAILY LIVING SECTION WITH SPECIFIC FUNCTIONAL DETAILS
        activities = elements["activities"]
        limitations = elements["activity_limitations"]
        
        summary = ""
        
        # Extract SPECIFIC activity limitation patterns with detailed examples
        limitation_patterns = [
            r'(nahihirapan|hirap|hindi magawa|hindi kayang|limitado)[^.]{5,50}(sa|ang|na)[^.]{5,100}',
            r'(needs assistance|nangangailangan ng tulong)[^.]{5,100}',
            r'(dependent|umaasa|kailangan ng tulong)[^.]{5,50}(sa|para sa|with|in)[^.]{5,100}'
        ]
        
        specific_limitation = None
        for pattern in limitation_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                limitation_desc = match.group(0)
                if len(limitation_desc) < 120:
                    specific_limitation = limitation_desc
                    break
        
        # Start with specific activity limitations
        if specific_limitation:
            if specific_limitation[0].islower():
                summary += f"Sa mga pang-araw-araw na gawain, {subject} ay {specific_limitation}. "
            else:
                summary += f"Sa mga pang-araw-araw na gawain, {specific_limitation}. "
        elif limitations:
            limitation = limitations[0]
            
            # Try to find how this limitation manifests in daily life
            manifestation_pattern = f"({re.escape(limitation)})[^.]*?(makikita sa|napapansin sa|evident in|manifested by)[^.]+"
            manifest_match = re.search(manifestation_pattern, section_text, re.IGNORECASE)
            
            if manifest_match and len(manifest_match.groups()) > 1:
                manifestation = manifest_match.group(0).strip()
                summary += f"Sa mga pang-araw-araw na gawain, {subject} ay {manifestation}. "
            else:
                summary += f"Sa mga pang-araw-araw na gawain, {subject} ay nahihirapan sa {limitation}. "
        
        # Add SPECIFIC activity details with quantifiable metrics when available
        metric_patterns = [
            r'(kakayahang|able to|can)[^.]{5,30}(maglakad|walk|tumayo|stand|umakyat|climb)[^.]{5,30}(\d+[^.]+)',
            r'(tulungan ng|with assistance of|tulong ng|help from)[^.]{5,100}',
            r'(self-care|personal care|pag-aalaga sa sarili)[^.]{5,100}'
        ]
        
        for pattern in metric_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                metric_desc = match.group(0).strip()
                if len(metric_desc) < 100:
                    if metric_desc[0].islower():
                        summary += f"May {metric_desc}. "
                    else:
                        summary += f"{metric_desc}. "
                    break
        
        # Add SPECIFIC mobility/assistive device details
        device_patterns = [
            r'(gumagamit ng|uses|needs|requires|kailangan ng)[^.]{5,30}(wheelchair|tungkod|walker|cane|mobility aid|assistive device)[^.]{5,100}',
            r'(wheelchair|tungkod|walker|cane|mobility aid|assistive device)[^.]{5,100}(gumagamit|uses|needs)'
        ]
        
        for pattern in device_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match and len(summary) < max_length - 100:
                device_desc = match.group(0).strip()
                if len(device_desc) < 100:
                    summary += f"{device_desc}. "
                    break
        
        # Add SPECIFIC details on activity pattern changes
        change_patterns = [
            r'(recently|kamakailan|lately|for the past|sa nakalipas)[^.]{5,30}(hindi na|stopped|can\'t anymore|hindi na kayang)[^.]{5,100}',
            r'(dati|previously|dating|used to)[^.]{5,30}(kaya|able to|capable of)[^.]{5,100}(ngayon|now|pero ngayon|but now)[^.]{5,100}',
            r'(pagbabago sa|changes in|deterioration of)[^.]{5,30}(activity|gawain|function|kakayahan)[^.]{5,100}'
        ]
        
        for pattern in change_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match and len(summary) < max_length - 120:
                change_desc = match.group(0).strip()
                if len(change_desc) < 120:
                    summary += f"Napansin ang {change_desc}. "
                    break
        
        # Add specific independence level if available
        if len(summary) < max_length - 100:
            independence_patterns = [
                r'(independence level|antas ng pagsasarili|level of dependence)[^.]{5,100}',
                r'(completely|fully|partially|minimal|maximum|moderate)[^.]{5,30}(dependent|independent|assistance|tulong)[^.]{5,100}'
            ]
            
            for pattern in independence_patterns:
                match = re.search(pattern, section_text, re.IGNORECASE)
                if match:
                    independence_desc = match.group(0).strip()
                    if len(independence_desc) < 100:
                        summary += f"Sa aspeto ng independence, {independence_desc}. "
                        break
        
        # If we still don't have specific activity details, extract from text
        if not summary or len(summary) < 100:
            # Extract detailed information about specific ADLs
            adl_patterns = [
                r'(pagligo|bathing|pagbibihis|dressing|pagkain|eating|toileting|pag-aayos|grooming)[^.]{5,100}',
                r'(IADL|instrumental activities)[^.]{5,100}',
                r'(pagluluto|cooking|paglilinis|cleaning|pamimili|shopping|pamamahala ng gamot|medication management)[^.]{5,100}'
            ]
            
            for pattern in adl_patterns:
                match = re.search(pattern, section_text, re.IGNORECASE)
                if match and len(summary) < max_length - 100:
                    adl_desc = match.group(0).strip()
                    if len(adl_desc) < 100:
                        if not summary:
                            summary += f"{subject} ay may sumusunod na limitasyon sa pang-araw-araw na gawain: {adl_desc}. "
                        else:
                            summary += f"Kabilang sa mga apektadong gawain ang {adl_desc}. "
                        break
                        
        # If still no substantial content, add general activity description
        if not summary:
            if activities:
                if len(activities) > 1:
                    summary += f"{subject} ay may kahirapan sa {activities[0]} at {activities[1]} at iba pang pang-araw-araw na gawain. "
                else:
                    summary += f"{subject} ay may kahirapan sa {activities[0]} at iba pang pang-araw-araw na gawain. "
            else:
                summary += f"Ang kakayahan ni {subject} sa pang-araw-araw na gawain ay nangangailangan ng suporta. "
        
        # Add impact on family caregivers if mentioned
        if "family" in section_text.lower() or "pamilya" in section_text.lower() or "caregiver" in section_text.lower():
            caregiver_patterns = [
                r'(pamilya|family|caregiver|tagapag-alaga)[^.]{5,50}(nahihirapan|struggling|needs|kailangan|provides|nagbibigay)[^.]{5,100}',
                r'(burden|hirap|challenges|difficulties)[^.]{5,30}(sa|for|ng|of)[^.]{5,30}(pamilya|family|caregiver)[^.]{5,100}'
            ]
            
            for pattern in caregiver_patterns:
                match = re.search(pattern, section_text, re.IGNORECASE)
                if match and len(summary) < max_length - 100:
                    caregiver_desc = match.group(0).strip()
                    if len(caregiver_desc) < 100:
                        summary += f"Ang kalagayang ito ay {caregiver_desc}. "
                        break
        
        return summary.strip()
        
    elif section_name == "kalagayan_social" or "social" in section_name:
        # SOCIAL CONDITION SECTION WITH SPECIFIC RELATIONSHIP DYNAMICS
        relations = elements["social_support"]
        
        summary = ""
        
        # Extract SPECIFIC relationship dynamics with details
        relation_patterns = [
            r'(relationship|relasyon|ugnayan|pakikitungo)[^.]{5,50}(sa|with|between)[^.]{5,100}',
            r'(asawa|spouse|anak|children|pamilya|family|caregiver)[^.]{5,50}(nagbibigay|provides|helps|tumutulong|supports|sinusuportahan)[^.]{5,100}',
            r'(interaction|komunikasyon|communication|interaction)[^.]{5,50}(sa|with|ng)[^.]{5,50}(asawa|spouse|anak|children|pamilya|family)[^.]{5,100}'
        ]
        
        specific_relation = None
        for pattern in relation_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                relation_desc = match.group(0)
                if len(relation_desc) < 120:
                    specific_relation = relation_desc
                    break
        
        # Start with specific relationship dynamics
        if specific_relation:
            if specific_relation[0].islower():
                summary += f"Sa social na aspeto, {specific_relation}. "
            else:
                summary += f"Sa social na aspeto, {specific_relation}. "
        elif relations:
            relation = relations[0]
            
            # Check if the relationship indicates support or resistance
            support_terms = ["tumutulong", "sumusuporta", "supportive", "caring", "nagbibigay-suporta"]
            opposition_terms = ["tumanggi", "rejected", "opposed", "hindi sumusunod", "conflict", "tension"]
            
            # Check if there are signals of support or resistance
            has_support = any(term in section_text.lower() for term in support_terms)
            has_resistance = any(term in section_text.lower() for term in opposition_terms)
            
            if has_resistance:
                # Find specific resistance context
                for term in opposition_terms:
                    if term in section_text.lower():
                        term_pos = section_text.lower().find(term)
                        if term_pos >= 0:
                            start = max(0, term_pos - 20)
                            end = min(len(section_text), term_pos + len(term) + 60)
                            context = section_text[start:end]
                            if "." in context:
                                context = context.split(".")[0] + "."
                            if len(context) < 100:
                                summary += f"May {context} sa pagitan ni {subject} at kanyang {relation}. "
                                break
                
                # If no specific context found
                if not summary:
                    summary += f"May tensyon sa relasyon ni {subject} sa kanyang {relation}, partikular sa mga desisyon tungkol sa kalusugan. "
            elif has_support:
                # Find specific support context
                for term in support_terms:
                    if term in section_text.lower():
                        term_pos = section_text.lower().find(term)
                        if term_pos >= 0:
                            start = max(0, term_pos - 20)
                            end = min(len(section_text), term_pos + len(term) + 60)
                            context = section_text[start:end]
                            if "." in context:
                                context = context.split(".")[0] + "."
                            if len(context) < 100:
                                summary += f"Ang kanyang {relation} ay {context}. "
                                break
                
                # If no specific context found
                if not summary:
                    summary += f"Nakakatanggap {subject} ng suporta mula sa kanyang {relation}. "
            else:
                summary += f"May ugnayan {subject} sa kanyang {relation} tungkol sa kanyang kalusugan. "
            
            # Add additional relationships if available
            if len(relations) > 1 and len(summary) < max_length - 80:
                additional_relation = relations[1]
                summary += f"Bukod dito, may interaksyon din siya sa kanyang {additional_relation}. "
        
        # Add SPECIFIC social network details
        network_patterns = [
            r'(social network|social support system|support network|network of|grupo ng)[^.]{5,100}',
            r'(active|involved|participates|kasali|engaged)[^.]{5,30}(sa|in|with)[^.]{5,30}(community|komunidad|church|simbahan|senior|elderly)[^.]{5,100}',
            r'(friends|kaibigan|kamag-anak|relatives|extended family|malawak na pamilya)[^.]{5,100}'
        ]
        
        for pattern in network_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match and len(summary) < max_length - 100:
                network_desc = match.group(0).strip()
                if len(network_desc) < 100:
                    summary += f"Sa aspeto ng social network, {network_desc}. "
                    break
        
        # Add SPECIFIC cultural factors affecting social relationships
        cultural_patterns = [
            r'(cultural|kultura|traditional|tradisyonal|values|pagpapahalaga)[^.]{5,50}(factor|influence|impact|dahilan|epekto)[^.]{5,100}',
            r'(preferences|kagustuhan|cultural factors|cultural background)[^.]{5,50}(nakakaapekto|affects|influences|impact)[^.]{5,100}',
            r'(dahil sa|because of)[^.]{5,30}(culture|tradition|belief|paniniwala|kultura|tradisyon)[^.]{5,100}'
        ]
        
        for pattern in cultural_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match and len(summary) < max_length - 120:
                cultural_desc = match.group(0).strip()
                if len(cultural_desc) < 120:
                    summary += f"{cultural_desc}. "
                    break
        
        # Add SPECIFIC information about social isolation or engagement
        isolation_patterns = [
            r'(social isolation|isolation|isolation from|hiwalay sa|hindi nakikisalamuha sa)[^.]{5,100}',
            r'(feels|nakakaramdam ng|experiencing)[^.]{5,30}(lonely|loneliness|kalungkutan|nag-iisa|alone)[^.]{5,100}',
            r'(limited|decreased|reduced|nawalan ng|nawala ang)[^.]{5,30}(social interaction|pakikisalamuha|social engagement)[^.]{5,100}'
        ]
        
        engagement_patterns = [
            r'(actively|regularly|consistently)[^.]{5,30}(participates|engages|kasali|involved|lumahok)[^.]{5,100}',
            r'(enjoys|nagagalak|maintains|nananatili)[^.]{5,30}(social connection|interaction|pakikipag-ugnayan)[^.]{5,100}'
        ]
        
        # Check for isolation first
        isolation_found = False
        for pattern in isolation_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match and len(summary) < max_length - 100:
                isolation_desc = match.group(0).strip()
                if len(isolation_desc) < 100:
                    summary += f"Sa aspeto ng social interaction, {isolation_desc}. "
                    isolation_found = True
                    break
        
        # If no isolation found, check for engagement
        if not isolation_found:
            for pattern in engagement_patterns:
                match = re.search(pattern, section_text, re.IGNORECASE)
                if match and len(summary) < max_length - 100:
                    engagement_desc = match.group(0).strip()
                    if len(engagement_desc) < 100:
                        summary += f"Sa pakikisalamuha sa iba, {engagement_desc}. "
                        break
        
        # Add SPECIFIC communication barriers or abilities if mentioned
        communication_patterns = [
            r'(communication|komunikasyon|pakikipag-usap)[^.]{5,30}(barrier|hadlang|limitation|problema|issue)[^.]{5,100}',
            r'(mahirap|difficult|challenging)[^.]{5,30}(makipag-usap|communicate|express|ipahayag)[^.]{5,100}',
            r'(able to|capable of|kayang)[^.]{5,30}(communicate|makipag-usap|express|ipahayag)[^.]{5,100}'
        ]
        
        for pattern in communication_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match and len(summary) < max_length - 100:
                comm_desc = match.group(0).strip()
                if len(comm_desc) < 100:
                    summary += f"Sa larangan ng komunikasyon, {comm_desc}. "
                    break
        
        # If no specific details yet, create general social status summary
        if not summary:
            # Look for any social relationships in text
            if "asawa" in section_text.lower() or "spouse" in section_text.lower():
                if "tumanggi" in section_text.lower() or "hindi sumunod" in section_text.lower():
                    summary += f"Ayon sa asawa ni {subject}, hindi siya sumusunod sa mga rekomendasyon para sa kanyang kalusugan. "
                else:
                    summary += f"May komunikasyon {subject} sa kanyang asawa tungkol sa kanyang kondisyon. "
            
            # Look for cultural factors
            elif "cultural" in section_text.lower() or "traditional" in section_text.lower():
                summary += f"Ang cultural food preferences ni {subject} ay nakakaapekto sa kanyang pagsunod sa mga medical recommendations. "
            
            # Look for communication patterns
            elif "nakikipag-usap" in section_text.lower() or "binabanggit" in section_text.lower():
                summary += f"Sa kanyang pakikipag-usap, ipinahahayag ni {subject} ang kanyang mga kagustuhan at pangangailangan. "
                
            else:
                summary += f"Ang social na kalagayan ni {subject} ay mahalagang aspeto ng kanyang pangkalahatang kalusugan. "
        
        return summary.strip()
        
    elif section_name == "pangunahing_rekomendasyon" or "rekomendasyon" in section_name:
        # PRIMARY RECOMMENDATIONS SECTION WITH SPECIFIC MEDICAL ADVICE
        referrals = elements["healthcare_referrals"]
        recommendations = elements["recommendations"]
        
        summary = ""
        
        # Start with specific healthcare referrals and reasons
        if referrals:
            referral = referrals[0]
            
            # Look for specific reason for this referral
            reason_patterns = [
                f'(konsulta sa|pagpapatingin sa) {re.escape(referral)}[^.]+para sa ([^.]+)',
                f'{re.escape(referral)}[^.]+para ([^.]+)',
                r'(dahil sa|upang|para|to) ([^.]+)'
            ]
            
            for pattern in reason_patterns:
                match = re.search(pattern, section_text)
                if match and len(match.groups()) > 1:
                    reason = match.group(2).strip()
                    summary += f"Inirerekomenda ang pagkonsulta sa {referral} para sa {reason}. "
                    break
            
            # If no specific reason found
            if not summary:
                summary += f"Inirerekomenda ang pagkonsulta sa {referral} para sa kumpletong pagsusuri at diagnosis. "
        
        # Extract specific recommendation details from text
        specific_recommendations = []
        
        # Look for detailed diet recommendations
        diet_patterns = [
            r'(pagbawas ng|pag-iwas sa|pagtaas ng|pagdagdag ng)([^.,;:]{10,100})',
            r'(iminumungkahi|inirerekomenda|pinapayuhan)[^.]{5,30}(diet|pagkain|nutrition)[^.]{10,100}',
            r'(dapat|kailangan|mahalagang)[^.]{5,30}(diet|pagkain|nutrition)[^.]{10,100}'
        ]
        
        for pattern in diet_patterns:
            matches = re.finditer(pattern, section_text, re.IGNORECASE)
            for match in matches:
                recommendation = match.group(0).strip()
                if recommendation and len(recommendation) > 15 and len(recommendation) < 120:
                    specific_recommendations.append(recommendation)
        
        # Look for specific monitoring recommendations
        monitor_patterns = [
            r'(mag-monitor ng|subaybayan ang|bantayan ang|obserbahan ang)([^.,;:]{10,100})',
            r'(monitoring|pagsubaybay)[^.]{5,50}',
            r'(dapat|kailangan|mahalagang)[^.]{5,30}(bantayan|subaybayan|i-monitor)[^.]{10,100}'
        ]
        
        for pattern in monitor_patterns:
            matches = re.finditer(pattern, section_text, re.IGNORECASE)
            for match in matches:
                recommendation = match.group(0).strip()
                if recommendation and len(recommendation) > 15 and len(recommendation) < 120:
                    specific_recommendations.append(recommendation)
        
        # Look for specific medication or treatment recommendations
        treatment_patterns = [
            r'(gamot|medication|treatment)[^.]{10,100}',
            r'(dapat|kailangan|mahalagang)[^.]{5,30}(uminom ng|take|gamitin)[^.]{10,100}',
            r'(inirerekomenda|iminumungkahi|pinapayuhan)[^.]{5,30}(gamot|lunas|medication|treatment)[^.]{10,100}'
        ]
        
        for pattern in treatment_patterns:
            matches = re.finditer(pattern, section_text, re.IGNORECASE)
            for match in matches:
                recommendation = match.group(0).strip()
                if recommendation and len(recommendation) > 15 and len(recommendation) < 120:
                    specific_recommendations.append(recommendation)
        
        # Add specific recommendations with proper formatting
        if specific_recommendations:
            # Choose the most detailed recommendations (avoid duplicates)
            unique_recommendations = []
            for rec in specific_recommendations:
                # Check if this is significantly different from existing recommendations
                if not any(similar(rec, existing) > 0.7 for existing in unique_recommendations):
                    unique_recommendations.append(rec)
            
            # Add top recommendations
            if unique_recommendations:
                if not summary:
                    # First recommendation with proper introduction
                    first_rec = unique_recommendations[0]
                    # Ensure it starts with a capital letter
                    if first_rec[0].islower():
                        if "iminumungkahi" in first_rec.lower() or "inirerekomenda" in first_rec.lower():
                            summary += first_rec.capitalize() + ". "
                        else:
                            summary += "Iminumungkahi na " + first_rec + ". "
                    else:
                        summary += first_rec + ". "
                
                # Add second recommendation if available and different
                if len(unique_recommendations) > 1 and len(summary) < max_length - 80:
                    second_rec = unique_recommendations[1]
                    if second_rec[0].islower():
                        if "mahalagang" in second_rec.lower() or "dapat" in second_rec.lower():
                            summary += second_rec.capitalize() + ". "
                        else:
                            summary += "Mahalagang " + second_rec + ". "
                    else:
                        summary += second_rec + ". "
        
        # Use extracted recommendations if no specific ones were found
        elif recommendations:
            # Start with main recommendation
            rec = recommendations[0]
            summary += f"Mahalagang {rec}. "
            
            # Add secondary recommendation if available
            if len(recommendations) > 1:
                summary += f"Kinakailangan din na {recommendations[1]}. "
        
        # Add specific warning signs to watch for
        warning_patterns = [
            r'(bantayan|watch for|look for|observe for|be alert for)[^.]{5,50}(signs|symptoms|palatandaan)[^.]{10,100}',
            r'(warning signs|red flags|danger signals|palatandaan ng panganib)[^.]{10,100}'
        ]
        
        for pattern in warning_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                warning = match.group(0).strip()
                if warning and len(warning) < 100:
                    summary += f"Kinakailangang {warning}. "
                    break
        
        # If still no warnings but monitoring is mentioned, add generic warning
        if "monitor" in section_text.lower() and "warning" not in summary.lower() and "bantayan" not in summary.lower():
            # Look for specific symptoms/signs to monitor
            warning_terms = ["dehydration", "dry mouth", "decreased urination", "dizziness", 
                          "pagkahilo", "tuyot na bibig", "blood sugar", "blood pressure"]
            
            for term in warning_terms:
                if term in section_text.lower():
                    # Look for context around this term
                    term_pos = section_text.lower().find(term)
                    if term_pos >= 0:
                        start = max(0, term_pos - 20)
                        end = min(len(section_text), term_pos + len(term) + 40)
                        context = section_text[start:end]
                        
                        # Extract a readable warning that includes the term
                        if len(context) < 90:
                            summary += f"Kinakailangang bantayan ang {context}. "
                            break
                    
                    # If context extraction fails
                    summary += f"Kinakailangang bantayan ang mga palatandaan ng {term}. "
                    break
        
        return summary.strip()
        
    elif section_name == "pangangalaga" or "alaga" in section_name:
        # CARE & MONITORING SECTION WITH DETAILED SPECIFICS
        monitoring = elements["monitoring_plans"]
        warnings = elements["warnings"]
        treatments = elements["treatments"]  # Additional context
        recommendations = elements.get("recommendations", [])
        
        summary = ""
        
        # Extract SPECIFIC monitoring instructions with details
        monitoring_patterns = [
            r'(i-monitor|obserbahan|bantayan|subaybayan)([^.]{10,100})',
            r'(regular na|araw-araw na|weekly|monthly) (pagsubaybay|monitoring|pag-check)([^.]{5,100})',
            r'(dapat|kailangang|mahalagang) (subaybayan|bantayan|obserbahan)([^.]{10,100})'
        ]
        
        specific_monitoring = None
        for pattern in monitoring_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                groups = match.groups()
                if len(groups) >= 2:
                    monitoring_rec = match.group(0)
                    if len(monitoring_rec) < 120:
                        specific_monitoring = monitoring_rec
                        break
        
        # Start with specific monitoring instructions
        if specific_monitoring:
            if specific_monitoring[0].islower():
                summary += f"Sa pangangalaga, mahalagang {specific_monitoring}. "
            else:
                summary += f"Sa pangangalaga, {specific_monitoring}. "
        elif monitoring:
            monitor = monitoring[0]
            summary += f"Sa pangangalaga, mahalagang subaybayan ang {monitor}. "
            
            if len(monitoring) > 1:
                summary += f"Dapat ding regular na i-monitor ang {monitoring[1]}. "
        
        # Extract SPECIFIC warning signs with context
        warning_patterns = [
            r'(maging alerto sa|bantayan ang|mag-ingat sa)([^.]{10,100}(signs|symptoms|palatandaan|senyales))[^.]{10,100}',
            r'(warning signs|red flags|palatandaan ng panganib|danger signals)([^.]{10,100})',
            r'(kung|kapag|if|when) ([^.]{5,50})(kontakin|tawagan|i-contact|seek)[^.]{10,100}'
        ]
        
        specific_warning = None
        for pattern in warning_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                warning_rec = match.group(0)
                if len(warning_rec) < 120:
                    specific_warning = warning_rec
                    break
        
        # Add detailed warning signs
        if specific_warning:
            if specific_warning[0].islower():
                summary += f"Kinakailangang {specific_warning}. "
            else: 
                summary += f"{specific_warning}. "
        elif warnings:
            warning = warnings[0]
            summary += f"Maging alerto sa mga palatandaan ng {warning}. "
            
            # Add specific symptoms to watch for if available
            symptom_match = re.search(r'(symptoms|sintomas|signs|palatandaan) (tulad ng|such as|like|including)([^.]{5,100})', section_text, re.IGNORECASE)
            if symptom_match:
                specific_symptoms = symptom_match.group(3).strip()
                if len(specific_symptoms) < 80:
                    summary += f"Partikular na bantayan ang {specific_symptoms}. "
        
        # Extract SPECIFIC care techniques or approaches
        care_patterns = [
            r'(gawin ang|isagawa ang|apply|i-provide) ([^.]{10,100})',
            r'(technique|approach|pamamaraan|paraan) (para sa|for|sa) ([^.]{10,100})',
            r'(proper|tamang|wastong) (pangangalaga|care|positioning|pagpoposisyon)([^.]{10,100})'
        ]
        
        specific_care = None
        for pattern in care_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                care_rec = match.group(0)
                if len(care_rec) < 100:
                    specific_care = care_rec
                    break
        
        # Add care techniques if found
        if specific_care and len(summary) < 300:
            if specific_care[0].islower():
                summary += f"Iminumungkahi ang {specific_care}. "
            else:
                summary += f"{specific_care}. "
        elif treatments and len(summary) < 300:
            treatment = treatments[0]
            summary += f"Isagawa ang tamang {treatment} bilang bahagi ng pangangalaga. "
        
        # Extract SPECIFIC documentation instructions
        documentation_patterns = [
            r'(i-record|idokumento|isulat|i-log|i-track|i-document)([^.]{10,100})',
            r'(documentation|recording|tracking) (ng|of|para sa)([^.]{10,100})',
            r'(symptom diary|food diary|journal|log)([^.]{10,100})'
        ]
        
        specific_documentation = None
        for pattern in documentation_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                doc_rec = match.group(0)
                if len(doc_rec) < 100:
                    specific_documentation = doc_rec
                    break
        
        # Add documentation instructions if found
        if specific_documentation and len(summary) < 300:
            if specific_documentation[0].islower():
                summary += f"Mahalagang {specific_documentation}. "
            else:
                summary += f"{specific_documentation}. "
        
        # Extract SPECIFIC communication instructions with healthcare providers
        communication_patterns = [
            r'(kontakin|tawagan|makipag-ugnayan sa|consult with) ([^.]{10,100})',
            r'(inform|ipaalam|sabihan) (ang|the) (doktor|doctor|nurse|healthcare provider)([^.]{10,100})',
            r'(report|i-report|iulat) (ang|the|any) ([^.]{10,100})'
        ]
        
        specific_communication = None
        for pattern in communication_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                comm_rec = match.group(0)
                if len(comm_rec) < 100:
                    specific_communication = comm_rec
                    break
        
        # Add communication instructions if found
        if specific_communication and len(summary) < 300:
            if specific_communication[0].islower():
                summary += f"Kung may pagbabago sa kalagayan, {specific_communication}. "
            else:
                summary += f"{specific_communication}. "
        
        # If no specific details found yet, search for key terms
        if not summary:
            # Look for monitoring terms with context
            for monitor_term in ["subaybayan", "bantayan", "i-monitor", "obserbahan", "sundin"]:
                if monitor_term in section_text.lower():
                    # Extract context around the term
                    term_pos = section_text.lower().find(monitor_term)
                    if term_pos >= 0:
                        start = max(0, term_pos - 15)
                        end = min(len(section_text), term_pos + len(monitor_term) + 60)
                        context = section_text[start:end]
                        
                        # Find sentence boundary
                        period_pos = context.find('.')
                        if period_pos > 0:
                            context = context[:period_pos+1]
                        
                        if len(context) > 20 and len(context) < 120:
                            summary += context + " "
                            break
            
            # Look for caregiver instructions
            caregiver_patterns = [
                r'(caregiver|tagapag-alaga|family member) (dapat|should|can|may|pwedeng)([^.]{10,100})',
                r'(turuan|train|educate) (ang|the) (caregiver|tagapag-alaga|family)([^.]{10,100})'
            ]
            
            for pattern in caregiver_patterns:
                match = re.search(pattern, section_text, re.IGNORECASE)
                if match:
                    caregiver_rec = match.group(0)
                    if caregiver_rec and len(caregiver_rec) < 100:
                        summary += f"{caregiver_rec.capitalize()}. "
                        break
        
        # Add a general conclusion about the importance of consistent care
        if not summary:
            summary = "Ang regular na pagsubaybay at pangangalaga ay mahalaga para sa pagtukoy ng anumang pagbabago sa kalagayan. "
        elif len(summary) < 300:
            summary += "Ang maingat na pangangalaga at regular na monitoring ay mahalaga para sa mas mabilis na paggaling. "
        
        return summary.strip()
        
    elif section_name == "pagbabago_sa_pamumuhay" or "pamumuhay" in section_name:
        # LIFESTYLE CHANGES SECTION WITH CONCRETE SPECIFICS
        diet_changes = elements.get("diet_changes", [])
        lifestyle_changes = elements.get("lifestyle_changes", [])
        
        summary = ""
        
        # Extract SPECIFIC meal frequency details
        meal_patterns = [
            r'(frequent|small meals|maliit na pagkain|madalas na pagkain)[^.]{10,100}',
            r'(\d+-\d+)[^.]+(meal|pagkain)[^.]{10,100}'
        ]
        
        specific_meal_rec = None
        for pattern in meal_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                meal_rec = match.group(0)
                if len(meal_rec) < 100:
                    specific_meal_rec = meal_rec
                    break
        
        # Start with specific meal frequency recommendations
        if specific_meal_rec:
            if "sa halip na" in specific_meal_rec.lower() or "instead of" in specific_meal_rec.lower():
                summary += f"Para sa diet, inirerekomenda ang {specific_meal_rec}. "
            else:
                summary += f"Para sa diet, inirerekomenda ang {specific_meal_rec} upang mabawasan ang digestive burden. "
        elif diet_changes:
            diet_rec = diet_changes[0]
            # Extract more context for this diet recommendation
            diet_terms = [diet_rec.lower(), "diet", "pagkain"]
            for term in diet_terms:
                term_pos = section_text.lower().find(term)
                if term_pos >= 0:
                    start = max(0, term_pos - 20)
                    end = min(len(section_text), term_pos + len(term) + 60)
                    context = section_text[start:end]
                    if "." in context:
                        context = context.split('.')[0] + "."
                    if len(context) < 100:
                        summary += f"Para sa diet, {context} "
                        break
            
            # If no specific context found
            if not summary:
                summary += f"Para sa diet, inirerekomenda ang {diet_rec} upang mabawasan ang posibleng komplikasyon. "
        
        # Add SPECIFIC nutrition recommendations with details on what foods to include
        nutrition_patterns = [
            r'(nutrient-dense|nutritional|nutrisyon)[^.]{10,100}',
            r'(dapat|kailangan|mahalagang)[^.]{5,30}(pagkain|foods|nutrition)[^.]{10,100}'
        ]
        
        specific_nutrition = None
        for pattern in nutrition_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                nutrition_rec = match.group(0)
                if len(nutrition_rec) < 100:
                    specific_nutrition = nutrition_rec
                    break
        
        # Include specific nutritional recommendations
        if specific_nutrition:
            # Enhance with examples of specific foods if present
            food_examples = []
            food_terms = ["smoothies", "soup", "cereals", "protein", "gulay", "prutas"]
            for term in food_terms:
                if term in section_text.lower():
                    # Look for examples containing this term
                    example_pattern = f"(tulad ng|such as|like|including)[^.]*{term}[^.]+"
                    ex_match = re.search(example_pattern, section_text, re.IGNORECASE)
                    if ex_match:
                        example = ex_match.group(0)
                        if len(example) < 70:
                            food_examples.append(example)
                    else:
                        # Simple pattern to find food lists
                        simple_pattern = f"[^.]*{term}[^.,]*(?:,|at|and)[^.]+"
                        s_match = re.search(simple_pattern, section_text, re.IGNORECASE)
                        if s_match:
                            example = s_match.group(0)
                            if len(example) < 70:
                                food_examples.append(example)
            
            # Add nutrition recommendation with examples if found
            if food_examples:
                summary += f"Maaari ring isama sa diet ang {specific_nutrition}, {food_examples[0]}. "
            else:
                summary += f"Maaari ring isama sa diet ang {specific_nutrition} para sa optimal na nutrition. "
                
        elif len(diet_changes) > 1:
            # Look for specific examples of the second diet recommendation
            diet_rec = diet_changes[1]
            example_pattern = f"(tulad ng|such as|like|including)[^.]*{re.escape(diet_rec)}[^.]+"
            ex_match = re.search(example_pattern, section_text, re.IGNORECASE)
            if ex_match:
                example = ex_match.group(0)
                if len(example) < 70:
                    summary += f"Maaari ring isama sa diet ang {diet_rec}, {example}. "
                else:
                    summary += f"Maaari ring isama sa diet ang {diet_rec} para sa optimal na nutrition. "
            else:
                summary += f"Maaari ring isama sa diet ang {diet_rec} para sa optimal na nutrition. "
        
        # Add SPECIFIC physical activity recommendations
        activity_patterns = [
            r'(physical activity|ehersisyo|exercise|galaw)[^.]{10,100}',
            r'(regular|moderate|light|gentle)[^.]{1,10}(activity|exercise|ehersisyo)[^.]{10,100}'
        ]
        
        specific_activity = None
        for pattern in activity_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                activity_rec = match.group(0)
                if len(activity_rec) < 100 and "ayon sa" in activity_rec.lower():
                    # This contains personalized activity level mention
                    specific_activity = activity_rec
                    break
                elif len(activity_rec) < 100:
                    specific_activity = activity_rec
        
        # Add activity recommendation if found
        if specific_activity and len(summary) < max_length - 100:
            summary += f"Inirerekomenda rin ang {specific_activity}. "
        
        # Add SPECIFIC advice on hydration if mentioned
        if "hydration" in section_text.lower() or "tubig" in section_text.lower() or "fluid" in section_text.lower():
            hydration_pattern = r'(hydration|tubig|fluid|pag-inom)[^.]{10,100}'
            h_match = re.search(hydration_pattern, section_text, re.IGNORECASE)
            if h_match:
                hydration_rec = h_match.group(0)
                if len(hydration_rec) < 80:
                    summary += f"Mahalagang maisama ang {hydration_rec}. "
        
        # Add the adherence conclusion with specific benefits if possible
        benefit_patterns = [
            r'(para|upang|to|in order to)[^.]+(makita|makamit|achieve|attain)[^.]+resulta',
            r'(makakatulong|will help|helps)[^.]{10,100}'
        ]
        
        specific_benefit = None
        for pattern in benefit_patterns:
            match = re.search(pattern, section_text, re.IGNORECASE)
            if match:
                benefit = match.group(0)
                if len(benefit) < 80:
                    specific_benefit = benefit
                    break
        
        # Add conclusion with specific benefits if found
        if specific_benefit:
            summary += f"Consistent na pagsunod sa mga lifestyle modifications na ito {specific_benefit}."
        else:
            summary += "Consistent na pagsunod sa mga lifestyle modifications na ito ay mahalaga para makita ang mga positibong resulta."
        
        return summary.strip()
    
    # For any other section type, create a general summary
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