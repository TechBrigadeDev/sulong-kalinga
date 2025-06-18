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
    """Create a comprehensive summary with equal representation from all sections with improved context detection."""
    if not sections:
        return "Walang sapat na impormasyon para sa buod."
    
    # Extract main subject (patient)
    subject = extract_main_subject(doc)
    
    # NEW: Document-wide context analysis for cross-section understanding
    doc_context = analyze_document_context(sections, doc_type)
    
    # ENHANCEMENT 1: Handle long sections by splitting them
    processed_sections = {}
    for section_name, section_text in sections.items():
        # Skip empty sections
        if not section_text or len(section_text.strip()) < 10:
            continue
            
        # For very long sections (more than 500 chars), split into smaller parts
        if len(section_text) > 500:
            # Split into chunks for better processing
            section_sentences = split_into_sentences(section_text)
            
            # Create chunks of max 3 sentences
            chunks = []
            current_chunk = []
            current_length = 0
            
            for sent in section_sentences:
                if current_length + len(sent) > 300 and current_chunk:  # Start new chunk if this would make it too long
                    chunks.append(" ".join(current_chunk))
                    current_chunk = [sent]
                    current_length = len(sent)
                else:
                    current_chunk.append(sent)
                    current_length += len(sent)
                    
            # Add the last chunk if not empty
            if current_chunk:
                chunks.append(" ".join(current_chunk))
                
            # Store the chunks
            processed_sections[section_name] = chunks
        else:
            # For normal sections, keep as is
            processed_sections[section_name] = [section_text]
    
    # ENHANCEMENT 2: Calculate how many sentences to dedicate per section
    section_sentence_allocation = {}
    
    # NEW: Use document context to adjust allocation priority
    priority_sections = doc_context["priority_sections"]
    
    # Base allocation - start with one sentence per section
    for section_name, chunks in processed_sections.items():
        # Allocate one sentence per chunk, with a minimum of 1 and maximum of 3
        base_allocation = min(3, max(1, len(chunks)))
        
        # NEW: Boost allocation for high-priority sections based on document context
        if section_name in priority_sections:
            # Increase allocation for especially important sections
            section_sentence_allocation[section_name] = min(3, base_allocation + 1)
        else:
            section_sentence_allocation[section_name] = base_allocation
    
    # Ensure we have reasonable total length (aim for 3-7 sentences total)
    total_allocation = sum(section_sentence_allocation.values())
    max_total_sentences = 7
    
    if total_allocation > max_total_sentences:
        # We need to reduce allocation for some sections
        # Use priority sections from context analysis instead of hardcoding
        
        # First reduce non-priority sections to 1 sentence each
        for section_name in section_sentence_allocation:
            if section_name not in priority_sections and section_sentence_allocation[section_name] > 1:
                section_sentence_allocation[section_name] = 1
        
        # If still too many, reduce priority sections evenly
        total_allocation = sum(section_sentence_allocation.values())
        if total_allocation > max_total_sentences:
            # Calculate how many to remove
            to_remove = total_allocation - max_total_sentences
            
            # Convert to list for easier manipulation
            allocations = [(section, count) for section, count in section_sentence_allocation.items()]
            allocations.sort(key=lambda x: (x[0] not in priority_sections, -x[1]))  # Sort by priority, then by count
            
            # Remove from highest counts first
            for i in range(to_remove):
                if i < len(allocations) and allocations[i][1] > 1:
                    allocations[i] = (allocations[i][0], allocations[i][1] - 1)
            
            # Convert back to dict
            section_sentence_allocation = {section: count for section, count in allocations}
    
    # ENHANCEMENT 3: Generate sentences for each section with SPECIFIC details
    section_sentences = {}
    
    # Extract structured elements for all sections first
    section_elements = {}
    for section_name, chunks in processed_sections.items():
        # Process all chunks together to get more context
        combined_text = " ".join(chunks)
        section_elements[section_name] = extract_structured_elements(combined_text, section_name)
    
    # NEW: Add cross-section entity correlation 
    cross_section_entities = identify_cross_section_entities(section_elements)
    
    # Process document sections based on document type
    if doc_type.lower() == "assessment":
        # Process assessment sections with improved context awareness
        for section_name, chunks in processed_sections.items():
            # Skip processed sections
            if section_name in section_sentences:
                continue
                
            sentences = []
            elements = section_elements[section_name]
            
            # NEW: Use cross-section context to enhance summaries
            relevant_cross_entities = get_relevant_entities_for_section(
                section_name, cross_section_entities, doc_context
            )
            
            # Generate sentences based on section type with SPECIFIC DETAILS AND IMPROVED CONTEXT
            if section_name == "mga_sintomas" or "sintomas" in section_name:
                # SYMPTOMS & CONDITIONS SECTION
                conditions = elements["conditions"][:2]  # Take up to 2 conditions
                symptoms = elements["symptoms"][:3]      # Take up to 3 symptoms
                severity = elements["severity"][0] if elements["severity"] else ""
                frequency = elements["frequency"][0] if elements["frequency"] else ""
                duration = elements["duration"][0] if elements["duration"] else ""
                
                # Look for "roller-coaster" or similar patterns and provide context
                pattern_descriptions = {}
                for condition in conditions:
                    if "diabetes" in condition.lower() or "blood sugar" in condition.lower() or "glucose" in condition.lower():
                        pattern_context = extract_measurement_context(" ".join(chunks), "roller-coaster")
                        if pattern_context["pattern"]:
                            pattern_descriptions["diabetes"] = pattern_context["pattern"]
                            
                # Create a coherent sentence about symptoms/conditions with enhanced detail
                summary = f"{subject} ay nagpapakita ng "
                
                # Combine conditions and symptoms with enhanced pattern descriptions
                medical_issues = []
                if conditions:
                    for condition in conditions:
                        if "diabetes" in condition.lower() and "diabetes" in pattern_descriptions:
                            # Add meaningful context for mentioned patterns
                            enhanced_condition = f"{condition} na may {pattern_descriptions['diabetes']}"
                            medical_issues.append(enhanced_condition)
                        else:
                            medical_issues.append(condition)
                
                if symptoms and not any(s in " ".join(conditions) for s in symptoms):
                    medical_issues.extend(symptoms)
                
                if medical_issues:
                    # Format the medical issues with proper Tagalog conjunction
                    if len(medical_issues) == 1:
                        condition_text = medical_issues[0]
                    elif len(medical_issues) == 2:
                        condition_text = f"{medical_issues[0]} at {medical_issues[1]}"
                    else:
                        condition_text = f"{', '.join(medical_issues[:-1])}, at {medical_issues[-1]}"
                    
                    # Include specific severity or frequency if available
                    severity = elements["severity"][0] if elements["severity"] else ""
                    frequency = elements["frequency"][0] if elements["frequency"] else ""
                    
                    # NEW: Check document context for severity trends
                    if doc_context.get("severity_trend"):
                        severity = doc_context["severity_trend"]
                    
                    if severity and frequency:
                        sentences.append(f"{subject} ay nagpapakita ng {severity} at {frequency} na {condition_text}.")
                    elif severity:
                        sentences.append(f"{subject} ay nagpapakita ng {severity} na {condition_text}.")
                    elif frequency:
                        sentences.append(f"{subject} ay nagpapakita ng {condition_text} na nangyayari nang {frequency}.")
                    else:
                        sentences.append(f"{subject} ay nagpapakita ng {condition_text}.")
                
                # If allocated more than 1 sentence and we have severity info, add it
                if section_sentence_allocation[section_name] > 1 and (elements["severity"] or elements["frequency"] or elements["duration"]):
                    severity = elements["severity"][0] if elements["severity"] else ""
                    frequency = elements["frequency"][0] if elements["frequency"] else ""
                    duration = elements["duration"][0] if elements["duration"] else ""
                    
                    # NEW: Use document context for temporal information
                    if doc_context.get("duration_context"):
                        duration = doc_context["duration_context"]
                    
                    # Create more specific second sentence about symptom characteristics
                    if severity and frequency and duration:
                        sentences.append(f"Ang mga sintomas ay {severity}, nangyayari nang {frequency}, at nagsimula {duration}.")
                    elif severity and frequency:
                        sentences.append(f"Ang mga sintomas ay {severity} at {frequency}.")
                    elif severity and duration:
                        sentences.append(f"Ang mga sintomas ay {severity} at nagsimula {duration}.")
                    elif frequency and duration:
                        sentences.append(f"Ang mga sintomas ay nangyayari nang {frequency} at nagsimula {duration}.")
                    elif severity:
                        sentences.append(f"Ang mga sintomas ay {severity}.")
                    elif frequency:
                        sentences.append(f"Ang mga sintomas ay nangyayari nang {frequency}.")
                    elif duration:
                        sentences.append(f"Ang mga sintomas ay nagsimula {duration}.")
                
                # NEW: Add third sentence with impact information if available in context
                if section_sentence_allocation[section_name] > 2:
                    # Check if document context contains impact information
                    if doc_context.get("symptom_impacts"):
                        impact = doc_context["symptom_impacts"][0]
                        sentences.append(f"Ang mga sintomas na ito ay {impact}.")
                    # Otherwise use additional symptoms if available
                    elif len(symptoms) > 2:
                        additional_symptoms = symptoms[2:]
                        if additional_symptoms:
                            if len(additional_symptoms) == 1:
                                symptom_text = additional_symptoms[0]
                            else:
                                symptom_text = f"{additional_symptoms[0]} at {additional_symptoms[1]}"
                            sentences.append(f"Bukod dito, nararanasan din niya ang {symptom_text}.")
            
            elif section_name == "kalagayan_pangkatawan" or "pangkatawan" in section_name:
                # PHYSICAL CONDITION SECTION with context awareness
                measurements = elements["vital_signs"]
                limitations = elements["limitations"]
                body_parts = elements["body_parts"]
                
                # Check for cross-section entities
                if relevant_cross_entities.get("body_parts"):
                    cross_body_parts = relevant_cross_entities["body_parts"]
                    body_parts = cross_body_parts + [b for b in body_parts if b not in cross_body_parts]
                    body_parts = body_parts[:3]  # Limit to top 3
                
                # First sentence about physical measurements/vital signs
                if measurements:
                    measurement_text = measurements[0]
                    if len(measurements) > 1:
                        sentences.append(f"Ang mga sukat ay nagpapakita ng {measurement_text}, kasama ang {measurements[1]}.")
                    else:
                        sentences.append(f"Ang mga sukat ay nagpapakita ng {measurement_text}.")
                
                # Second sentence about physical limitations if available
                if section_sentence_allocation[section_name] > 1 and (limitations or body_parts):
                    if limitations and body_parts:
                        limitation = limitations[0]
                        body_part = body_parts[0]
                        sentences.append(f"{subject} ay may limitasyon sa {limitation}, partikular sa {body_part}.")
                    elif limitations:
                        sentences.append(f"{subject} ay nagpapakita ng {limitations[0]}.")
                    elif body_parts:
                        body_parts_text = ", ".join(body_parts[:2]) if len(body_parts) > 1 else body_parts[0]
                        sentences.append(f"May kondisyon na nakakaapekto sa {body_parts_text}.")
                
                # Third sentence with additional health context if available
                if section_sentence_allocation[section_name] > 2:
                    # Look for specific patterns related to physical condition in the text
                    if doc_context.get("key_entities") and "DISEASE" in doc_context["key_entities"]:
                        disease = doc_context["key_entities"]["DISEASE"][0]
                        sentences.append(f"Ang {disease} ay nakakadagdag sa pangkalahatang pisikal na limitasyon ni {subject}.")
                    elif elements["adjectives"]:
                        adjective = elements["adjectives"][0]
                        sentences.append(f"Ang kanyang pisikal na kalagayan ay maaaring ilarawan bilang {adjective}.")
            
            elif section_name == "kalagayan_mental" or "mental" in section_name:
                # MENTAL & COGNITIVE CONDITION SECTION WITH SPECIFIC DETAILS
                cognitive = elements["cognitive_status"]
                emotional = elements["emotional_state"]
                mental = elements["mental_state"]
                
                # Start with cognitive status
                if cognitive:
                    cognitive_desc = cognitive[0]
                    # Check for severity qualifier
                    severity_terms = ["mild", "moderate", "severe", "banayad", "katamtaman", "malubha"]
                    severity = next((term for term in severity_terms if term in " ".join(chunks).lower()), "")
                    
                    if severity:
                        sentences.append(f"Ang mental na kalagayan ni {subject} ay nagpapakita ng {cognitive_desc} na {severity}.")
                    else:
                        sentences.append(f"Ang mental na kalagayan ni {subject} ay nagpapakita ng {cognitive_desc}.")
                
                # Add emotional state if allocated more than 1 sentence
                if section_sentence_allocation[section_name] > 1 and emotional:
                    emotion_text = emotional[0]
                    sentences.append(f"Sa emosyonal na aspeto, {subject} ay nagpapakita ng {emotion_text}.")
                
                # Add impact on daily functioning if allocated more than 2 sentences
                if section_sentence_allocation[section_name] > 2:
                    # Look for impact patterns
                    impact_patterns = [
                        r'(nakakaapekto sa|nagdudulot ng|nagiging sanhi ng)[^.]{5,50}(araw-araw|daily|functioning)[^.]{5,100}'
                    ]
                    
                    impact_found = False
                    for pattern in impact_patterns:
                        match = re.search(pattern, " ".join(chunks), re.IGNORECASE)
                        if match:
                            impact = match.group(0).strip()
                            sentences.append(f"Ang kondisyong ito ay {impact}.")
                            impact_found = True
                            break
                    
                    # If no specific impact found
                    if not impact_found and mental:
                        sentences.append(f"Ang kanyang mental na estado ay maaaring ilarawan bilang {mental[0]}.")
                
                # Add these sentences to the section_sentences dictionary
                section_sentences[section_name] = sentences[:section_sentence_allocation[section_name]]
            
            elif section_name == "aktibidad" or "aktibidad" in section_name:
                # ACTIVITY & DAILY LIVING with context awareness
                activities = elements["activities"]
                activity_limitations = elements["activity_limitations"]
                
                # First sentence about general activity status
                if activity_limitations:
                    limitation = activity_limitations[0]
                    sentences.append(f"Sa mga pang-araw-araw na gawain, {subject} ay nahihirapan sa {limitation}.")
                elif activities:
                    activity = activities[0]
                    sentences.append(f"{subject} ay may kahirapan sa {activity} at iba pang pang-araw-araw na gawain.")
                else:
                    sentences.append(f"Ang kakayahan ni {subject} sa pang-araw-araw na gawain ay nangangailangan ng suporta.")
                
                # Second sentence with specific activity limitations
                if section_sentence_allocation[section_name] > 1 and activities:
                    if len(activities) > 1:
                        sentences.append(f"Partikular na nahihirapan siya sa {activities[0]} at {activities[1]}.")
                    else:
                        sentences.append(f"Partikular na nahihirapan siya sa {activities[0]}.")
                
                # Third sentence with context and impact
                if section_sentence_allocation[section_name] > 2:
                    # Check if there's family impact mentioned in context
                    if any("family" in theme or "pamilya" in theme for theme in doc_context.get("cross_section_themes", [])):
                        sentences.append(f"Ang limitasyon sa aktibidad na ito ay nakakaapekto rin sa routine ng kanyang pamilya.")
                    elif doc_context.get("key_entities") and "DISEASE" in doc_context["key_entities"]:
                        disease = doc_context["key_entities"]["DISEASE"][0]
                        sentences.append(f"Ang {disease} ay nagiging hadlang sa kanyang mga normal na gawain.")
            
            elif section_name == "kalagayan_social" or "social" in section_name:
                # SOCIAL CONDITION with context awareness
                relations = elements["social_support"]
                
                # First sentence about social relationships
                if relations:
                    relation = relations[0]
                    
                    # Check document context for resistance/support patterns
                    if any("tumanggi" in theme or "hindi sumusunod" in theme for theme in doc_context.get("cross_section_themes", [])):
                        sentences.append(f"May interaksyon {subject} sa kanyang {relation}, na nagrereport ng hindi pagsunod sa mga rekomendasyon.")
                    else:
                        sentences.append(f"May ugnayan {subject} sa kanyang {relation} tungkol sa kanyang kalusugan.")
                else:
                    sentences.append(f"Ang social na kalagayan ni {subject} ay mahalagang aspeto ng kanyang pangkalahatang kalusugan.")
                
                # Second sentence with additional relationships
                if section_sentence_allocation[section_name] > 1 and len(relations) > 1:
                    sentences.append(f"Bukod dito, may interaksyon din siya sa {relations[1]}.")
                elif section_sentence_allocation[section_name] > 1:
                    # Look for cultural factors in context
                    if any("cultural" in theme or "traditional" in theme for theme in doc_context.get("cross_section_themes", [])):
                        sentences.append(f"Ang cultural preferences ni {subject} ay nakakaapekto sa kanyang desisyon tungkol sa kanyang kalusugan.")
                
                # Third sentence about social impact and needs
                if section_sentence_allocation[section_name] > 2:
                    if doc_context.get("key_entities") and "SOCIAL_REL" in doc_context["key_entities"]:
                        support_system = doc_context["key_entities"]["SOCIAL_REL"][0]
                        sentences.append(f"Ang pakikipag-ugnayan sa {support_system} ay mahalaga para sa patuloy na pangangalaga.")
                    else:
                        sentences.append(f"Kinakailangan ng patuloy na social support para sa kanyang kondisyon.")
            
            # Make sure we store the sentences for this section
            if section_name not in section_sentences:
                section_sentences[section_name] = sentences[:section_sentence_allocation[section_name]]
            
    else:  # EVALUATION document
        # Similar context-enhanced processing for evaluation document sections
        for section_name, chunks in processed_sections.items():
            # Skip processed sections
            if section_name in section_sentences:
                continue
                
            sentences = []
            elements = section_elements[section_name]
            
            # Get cross-section context
            relevant_cross_entities = get_relevant_entities_for_section(
                section_name, cross_section_entities, doc_context
            )
            
            if section_name == "pangunahing_rekomendasyon" or "rekomendasyon" in section_name:
                # PRIMARY RECOMMENDATIONS section
                referrals = elements["healthcare_referrals"]
                recommendations = elements["recommendations"]
                
                # First sentence about main recommendation
                if referrals:
                    referral = referrals[0]
                    sentences.append(f"Inirerekomenda ang pagkonsulta sa {referral} para sa kumpletong pagsusuri at diagnosis.")
                elif recommendations:
                    recommendation = recommendations[0]
                    sentences.append(f"Inirerekomenda na {recommendation}.")
                else:
                    sentences.append("Inirerekomenda ang komprehensibong pagsusuri at plano ng pangangalaga.")
                
                # Second sentence with additional recommendations
                if section_sentence_allocation[section_name] > 1:
                    if len(recommendations) > 1:
                        sentences.append(f"Bukod dito, mahalagang {recommendations[1]}.")
                    elif referrals and recommendations:
                        sentences.append(f"Mahalagang {recommendations[0]}.")
                    elif doc_context.get("cross_section_entities", {}).get("recommendations"):
                        cross_rec = doc_context["cross_section_entities"]["recommendations"][0]
                        sentences.append(f"Mahalagang isaalang-alang din ang {cross_rec}.")
                
                # Third sentence with timing and importance
                if section_sentence_allocation[section_name] > 2:
                    if doc_context.get("severity_trend") == "malubha":
                        sentences.append("Ang mga rekomendasyon na ito ay dapat isagawa sa lalong madaling panahon.")
                    else:
                        sentences.append("Ang regular na pagsubaybay sa mga rekomendasyon na ito ay mahalaga para sa pangmatagalang kalusugan.")
            
            elif section_name == "mga_hakbang" or "hakbang" in section_name:
                # STEPS & INTERVENTIONS section
                treatments = elements["treatments"]
                methods = elements["intervention_methods"]
                
                # First sentence about specific steps/treatments
                if treatments:
                    treatment = treatments[0]
                    sentences.append(f"Ang mga partikular na hakbang na kailangan isagawa ay {treatment}.")
                elif methods:
                    method = methods[0]
                    sentences.append(f"Ang mga paraan ng interbensyon ay kinabibilangan ng {method}.")
                else:
                    sentences.append("Ang mga hakbang sa paggamot ay dapat isagawa nang maingat at regular.")
                
                # Second sentence with additional treatments/methods
                if section_sentence_allocation[section_name] > 1:
                    if len(treatments) > 1:
                        sentences.append(f"Dapat din isagawa ang {treatments[1]}.")
                    elif len(methods) > 1:
                        sentences.append(f"Kasama sa mga rekomendasyon ang {methods[1]}.")
                    elif treatments and methods:
                        sentences.append(f"Kasama sa mga rekomendasyon ang {methods[0]}.")
                
                # Third sentence with specific instructions or guidance
                if section_sentence_allocation[section_name] > 2:
                    if elements["verbs"]:
                        action_verb = elements["verbs"][0]
                        sentences.append(f"Mahalagang {action_verb} ang mga hakbang na ito nang regular at tama.")
                    else:
                        sentences.append("Ang consistent na pagsunod sa mga hakbang na ito ay mahalaga para sa positibong resulta.")
            
            elif section_name == "pangangalaga" or "alaga" in section_name:
                # CARE & MONITORING section
                monitoring = elements["monitoring_plans"]
                warnings = elements["warnings"]
                
                # First sentence about monitoring approach
                if monitoring:
                    monitor = monitoring[0]
                    sentences.append(f"Sa pangangalaga, mahalagang subaybayan ang {monitor}.")
                else:
                    sentences.append("Ang regular na pagsubaybay at pangangalaga ay mahalaga para sa progreso.")
                
                # Second sentence about warning signs
                if section_sentence_allocation[section_name] > 1 and warnings:
                    warning = warnings[0]
                    sentences.append(f"Maging alerto sa mga palatandaan ng {warning}.")
                elif section_sentence_allocation[section_name] > 1 and len(monitoring) > 1:
                    sentences.append(f"Dapat ding regular na i-monitor ang {monitoring[1]}.")
                
                # Third sentence with frequency and importance
                if section_sentence_allocation[section_name] > 2:
                    if doc_context.get("severity_trend") == "malubha":
                        sentences.append("Ang agarang pagkilos sa anumang pagbabago ay kritikal para sa pasyente.")
                    else:
                        sentences.append("Ang dokumentasyon ng mga pagbabago ay makakatulong sa mga healthcare provider.")
            
            elif section_name == "pagbabago_sa_pamumuhay" or "pamumuhay" in section_name:
                # LIFESTYLE CHANGES section
                diet = elements.get("diet_changes", [])
                lifestyle = elements.get("lifestyle_changes", [])
                
                # First sentence about diet recommendations
                if diet:
                    diet_rec = diet[0]
                    sentences.append(f"Para sa diet, inirerekomenda ang {diet_rec} upang mabawasan ang posibleng komplikasyon.")
                elif lifestyle:
                    lifestyle_change = lifestyle[0]
                    sentences.append(f"Inirerekomenda ang {lifestyle_change} bilang pagbabago sa pamumuhay.")
                else:
                    sentences.append("Ang mga pagbabago sa pamumuhay ay mahalaga para sa pangkalahatang kalusugan.")
                
                # Second sentence with additional diet/lifestyle recommendations
                if section_sentence_allocation[section_name] > 1:
                    if len(diet) > 1:
                        sentences.append(f"Maaari ring isama sa diet ang {diet[1]} para sa optimal na nutrition.")
                    elif len(lifestyle) > 1:
                        sentences.append(f"Bukod dito, mahalagang {lifestyle[1]}.")
                    elif diet and lifestyle:
                        sentences.append(f"Bukod dito, mahalagang {lifestyle[0]}.")
                
                # Third sentence with encouragement for adherence
                if section_sentence_allocation[section_name] > 2:
                    sentences.append("Consistent na pagsunod sa mga lifestyle modifications na ito ay mahalaga para makita ang mga positibong resulta.")
            
            section_sentences[section_name] = sentences[:section_sentence_allocation[section_name]]
    
    # ENHANCEMENT 4: Compile the final summary with improved context-aware transitions
    all_sentences = []

    # Define selected_sections from processed_sections
    selected_sections = list(processed_sections.keys())

    # NEW: Enhance each sentence with measurement details
    for i, section_name in enumerate(selected_sections):
        if section_name in section_sentences:
            # Get the original sentences
            original_sentences = section_sentences[section_name]
            
            # Enhance each sentence with measurement details
            enhanced_sentences = []
            for sentence in original_sentences:
                enhanced = enhance_measurement_references(sentence, sections[section_name])
                enhanced_sentences.append(enhanced)
            
            section_sentences[section_name] = enhanced_sentences
    
    # NEW: Create logical ordering of sections based on document context
    section_order = determine_optimal_section_order(doc_context, doc_type)
    
    # Add sentences from each section in the optimal order
    for section_name in section_order:
        if section_name in section_sentences:
            all_sentences.extend(section_sentences[section_name])
    
    # Apply transitions between sentences with improved context awareness and quality control
    final_summary = ""
    
    if all_sentences:
        # Start with first sentence
        final_summary = all_sentences[0]
        
        # Track used transitions to avoid repetition
        used_transitions = set()
        
        # NEW: Better sentence joining with context and quality control
        for i in range(1, len(all_sentences)):
            # Get the next sentence and ensure it's complete
            next_sentence = all_sentences[i]
            if not next_sentence or len(next_sentence.strip()) < 15:
                continue  # Skip very short sentences
                
            # Check that sentence ends with punctuation
            if not next_sentence.strip().endswith(('.', '!', '?')):
                next_sentence += '.'
            
            # Get contextual relationship
            relationship = get_contextual_relationship(
                all_sentences[i-1], next_sentence, doc_context, i-1, i
            )
            
            # Choose appropriate transition (that avoids repetition)
            transition = choose_context_aware_transition(final_summary, next_sentence, relationship)
            
            # Ensure proper capitalization after transition
            if next_sentence[0].isupper():
                next_sentence = next_sentence[0].lower() + next_sentence[1:]
            
            # Add to final summary
            final_summary += f" {transition}{next_sentence}"
    else:
        # Fallback
        if doc_type.lower() == "assessment":
            final_summary = f"{subject} ay nangangailangan ng karagdagang pagsusuri."
        else:
            final_summary = "Inirerekomenda ang komprehensibong medikal na pagsusuri para sa wastong diagnosis at paggamot."
    
    # Final formatting with consistency checks
    final_summary = re.sub(r'\s+', ' ', final_summary)  # Fix multiple spaces
    final_summary = re.sub(r'\s([,.;:])', r'\1', final_summary)  # Fix spacing before punctuation
    
    # Ensure first letter is capitalized
    if final_summary and final_summary[0].islower():
        final_summary = final_summary[0].upper() + final_summary[1:]
    
    # Ensure proper ending punctuation
    if final_summary and not final_summary[-1] in ['.', '!', '?']:
        final_summary += '.'
    
    return final_summary

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

