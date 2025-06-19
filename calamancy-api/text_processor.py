import re
from nlp_loader import nlp, split_into_sentences, clean_and_normalize_text

def enhance_measurement_references(sentence, section_text):
    """Ensure measurements referenced in a sentence include specific values"""
    from context_analyzer import extract_measurement_context  # Import here to avoid circular import
    
    measurement_terms = ["blood sugar", "glucose", "blood pressure", "presyon", 
                         "timbang", "weight", "oxygen", "temperature", "lagnat"]
    
    enhanced = sentence
    
    for term in measurement_terms:
        if term in enhanced.lower():
            # Only enhance if no specific value is already included
            has_value = any(char.isdigit() for char in enhanced)
            if not has_value:
                measure_context = extract_measurement_context(section_text, term)
                if measure_context["value"]:
                    # Replace generic term with term + value
                    pattern = re.compile(r'\b' + re.escape(term) + r'\b', re.IGNORECASE)
                    enhanced = pattern.sub(f"{term} ({measure_context['value']})", enhanced)
    
    return enhanced