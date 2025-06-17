import re
import unicodedata
from typing import List, Dict, Any, Union

# Common Tagalog medical terms and their variations
MEDICAL_TERM_MAPPING = {
    # Body parts
    r'\blang\b': 'ilong',
    r'\brnata\b': 'mata',
    r'\bteet[h]?\b': 'ngipin',
    r'\bbrain\b': 'utak',
    r'\bheart\b': 'puso',
    
    # Conditions
    r'\bhipertensyon\b': 'alta presyon',
    r'\bhypertension\b': 'alta presyon',
    r'\bdiabetes\b': 'diyabetis',
    r'\bstroke\b': 'atake sa utak',
    
    # Symptoms
    r'\bnasakit\b': 'masakit',
    r'\bsumakit\b': 'sumasakit',
    r'\bpanghihina\b': 'mahina',
    r'\bpangangatog\b': 'nangangatal',
    
    # Common contractions
    r'\bdi\b': 'hindi',
    r'\bwag\b': 'huwag',
    r'\bd\'yan\b': 'diyan',
}

# Common connecting phrases that could be significant for sentence breaks
CONNECTING_PHRASES = [
    'pero', 'subalit', 'ngunit', 'datapwat',  # But
    'dahil', 'sapagkat', 'dahilan sa',         # Because
    'kung kaya\'t', 'kaya naman', 'kaya',      # Therefore
    'bukod dito', 'dagdag pa', 'bilang dagdag' # Additionally
]

def normalize_text(text: str) -> str:
    """
    Normalizes text by standardizing spaces, punctuation, and case.
    
    Args:
        text: Raw input text
        
    Returns:
        Normalized text
    """
    # Convert to NFC form (canonical composition)
    text = unicodedata.normalize('NFC', text)
    
    # Standardize quotation marks
    text = re.sub(r'[""]', '"', text)
    # FIX: Use Unicode escape sequences for quote characters
    text = re.sub(r'[\u2018\u2019]', "'", text)
    
    # Standardize whitespace
    text = re.sub(r'\s+', ' ', text)
    text = re.sub(r'^\s+|\s+$', '', text)
    
    # Standardize dashes and hyphens
    text = re.sub(r'[–—−]', '-', text)
    
    # Make sure there's space after punctuation
    text = re.sub(r'([.!?,:;])([^\s\d])', r'\1 \2', text)
    
    return text

def standardize_medical_terms(text: str) -> str:
    """
    Standardizes medical terminology in Tagalog text.
    
    Args:
        text: Input text
        
    Returns:
        Text with standardized medical terms
    """
    # Replace common variations with standard terms
    for pattern, replacement in MEDICAL_TERM_MAPPING.items():
        text = re.sub(pattern, replacement, text, flags=re.IGNORECASE)
    
    return text

def split_sentences(text: str) -> List[str]:
    """
    Split text into sentences with special handling for Tagalog.
    
    Args:
        text: Input text
        
    Returns:
        List of sentences
    """
    try:
        # Use a simpler, more reliable pattern for initial splitting
        initial_sentences = []
        # Use a simple pattern that's less likely to fail
        for sent in re.split(r'([.!?])\s+', text):
            if sent.strip():
                initial_sentences.append(sent)
        
        # If the above fails, fall back to an even simpler approach
        if not initial_sentences:
            initial_sentences = [s.strip() + "." for s in text.split(".") if s.strip()]
            if not initial_sentences:
                return [text]
        
        # Filter out empty strings and clean up
        return [s.strip() for s in initial_sentences if s.strip()]
        
    except Exception as e:
        print(f"Error splitting sentences: {e}")
        # Return text as a single sentence as fallback
        return [text] if text else []

def enhance_medical_assessment(text: str) -> Dict[str, Any]:
    """
    Identifies medical assessment components in Tagalog text.
    
    Args:
        text: Medical assessment text
        
    Returns:
        Dictionary with medical assessment features
    """
    # Normalize and split the text
    text = normalize_text(text)
    text = standardize_medical_terms(text)
    sentences = split_sentences(text)
    
    # Look for mobility mentions
    mobility_sentences = [s for s in sentences if any(term in s.lower() for term in 
                        ["paglalakad", "hirap", "assistive", "nangangatal", "pabagsak", "pagkatumba"])]
    
    # Look for pain mentions
    pain_sentences = [s for s in sentences if any(term in s.lower() for term in 
                     ["masakit", "sumasakit", "kirot", "daing"])]
    
    # Look for sensory issues
    sensory_issues = []
    if any("mata" in s.lower() or "paningin" in s.lower() for s in sentences):
        sensory_issues.append("vision")
    if any("tenga" in s.lower() or "pandinig" in s.lower() for s in sentences):
        sensory_issues.append("hearing")
    
    # Look for emotional/psychological state
    emotional_state = []
    if any("lungkot" in s.lower() or "kalungkutan" in s.lower() for s in sentences):
        emotional_state.append("depressed")
    if any("takot" in s.lower() or "pagkabalisa" in s.lower() for s in sentences):
        emotional_state.append("anxious")
    
    return {
        "normalized_text": text,
        "sentences": sentences,
        "mobility_mentioned": len(mobility_sentences) > 0,
        "mobility_sentences": mobility_sentences,
        "pain_mentioned": len(pain_sentences) > 0,
        "pain_sentences": pain_sentences,
        "sensory_issues": sensory_issues,
        "emotional_state": emotional_state
    }

def preprocess_tagalog_text(text: str, detailed: bool = False) -> Union[List[str], Dict[str, Any]]:
    """
    Main preprocessing function for Tagalog medical texts.
    
    Args:
        text: Raw input text
        detailed: If True, returns detailed analysis, otherwise just sentences
        
    Returns:
        Either list of preprocessed sentences or detailed analysis dictionary
    """
    # Basic text normalization
    text = normalize_text(text)
    
    # Standardize medical terminology
    text = standardize_medical_terms(text)
    
    # Split into sentences
    sentences = split_sentences(text)
    
    # For simple preprocessing, just return the sentences
    if not detailed:
        return sentences
    
    # For detailed analysis, do more processing
    return enhance_medical_assessment(text)