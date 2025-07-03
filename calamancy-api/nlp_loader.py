import calamancy
import spacy
import numpy
import traceback
import sys
import re
import os

# Add model installer
from model_installer import download_and_install_calamancy_model

print("Initializing NLP Loader...")

# Initialize NLP model with better error handling
try:
    print(f"NumPy version: {numpy.__version__}")
    print(f"spaCy version: {spacy.__version__}")
    print(f"Loading calamancy model...")
    
    # First ensure the model is installed with proper versioning
    if not os.path.exists("models/tl_calamancy_md-0.2.0-py3-none-any.whl"):
        download_and_install_calamancy_model()
    
    # Try multiple loading approaches
    nlp = None
    errors = []
    
    try:
        # Load specific version
        nlp = calamancy.load("tl_calamancy_md-0.2.0")
        print("Model loaded successfully using version-specific method")
    except Exception as e1:
        errors.append(f"Standard loading failed: {e1}")
        try:
            # Try with standard version
            nlp = calamancy.load("tl_calamancy_md")
            print("Model loaded successfully using standard method")
        except Exception as e2:
            errors.append(f"Version-specific loading failed: {e2}")
            try:
                # Last resort: direct spaCy loading
                nlp = spacy.load("tl_calamancy_md")
                print("Model loaded successfully using direct spaCy method")
            except Exception as e3:
                errors.append(f"Direct spaCy loading failed: {e3}")
                # Comprehensive failure summary
                print("All loading attempts failed:")
                for i, error in enumerate(errors):
                    print(f"  Attempt {i+1}: {error}")
                raise RuntimeError("Unable to load the NLP model through any method")
    
    print("Model loaded successfully")
    
except Exception as e:
    print(f"Error loading dependencies: {e}")
    traceback.print_exc()
    sys.exit(1)

def split_into_sentences(text):
    """Split text into sentences with improved handling for Tagalog text"""
    if not text:
        return []

    # Clean and normalize the text first
    # Only add space after punctuation if not already present
    text = re.sub(r'([.!?,:;])([A-Za-z])', r'\1 \2', text)
    # Do NOT split on 'sa ', hyphens, or inside words!
    # Only fix over-spaced hyphens (e.g., "sabay - sabay" -> "sabay-sabay")
    text = re.sub(r'(\w)\s*-\s*(\w)', r'\1-\2', text)
    text = re.sub(r'\s+', ' ', text).strip()

    # List of common abbreviations to avoid incorrect sentence splitting
    abbreviations = [r'Dr\.', r'St\.', r'Mr\.', r'Mrs\.', r'Ms\.', r'Prof\.', r'etc\.', 
                     r'e\.g\.', r'i\.e\.', r'vs\.', r'Fig\.', r'No\.', r'Atty\.', r'Gov\.']

    # Temporarily replace periods in abbreviations to prevent incorrect splitting
    for abbr in abbreviations:
        text = re.sub(abbr, abbr.replace('.', '@@'), text)

    # Use spaCy/calamancy's sentence splitter
    doc = nlp(text)
    sentences = [sent.text.strip() for sent in doc.sents]

    # If that returns just one sentence for a long text, use regex-based splitting on punctuation only
    if len(sentences) <= 1 and len(text) > 100:
        # Only split at end of sentence punctuation followed by a space and a capital letter or number
        sentence_endings = r'(?<=[.!?])\s+(?=[A-Z0-9])'
        sentences = [s.strip() for s in re.split(sentence_endings, text) if s.strip()]

    # Restore the original abbreviation periods
    sentences = [re.sub(r'@@', '.', sent) for sent in sentences]

    # Filter out very short or empty sentences, but be more lenient (min 5 chars)
    return [sent for sent in sentences if len(sent) > 5]

# Text cleaning function - also moved here for convenience
def clean_and_normalize_text(text):
    """Clean and normalize text to improve processing quality"""
    if not text:
        return ""
        
    # Fix common spacing issues around punctuation
    text = re.sub(r'([.!?,:;])([A-Za-z])', r'\1 \2', text)
    
    # IMPROVED: Only add spaces around standalone dashes, not within hyphenated words
    text = re.sub(r'(\w)\s+-\s+(\w)', r'\1-\2', text)  # Fix over-spaced hyphens first
    text = re.sub(r'(\s)-(\s)', r'\1 - \2', text)  # Ensure spaces around standalone dashes

    # Add specific fixes for problematic Tagalog word splits and common errors
    fixes = [
        (r'\bsa\s+kit\b', 'sakit'),
        (r'\bpag\s+ka\b', 'pagka'),
        (r'\bhin\s+di\b', 'hindi'),
        (r'\bdi\s+makatulog\b', 'hindi makatulog'),
        (r'\bdi\s+kumakain\b', 'hindi kumakain'),
        (r'\bdi\s+regular\b', 'hindi regular'),
        (r'\bdi\s+nagbabago\b', 'hindi nagbabago'),
        (r'\baraw\s+araw\b', 'araw-araw'),
        (r'\bpang\s+araw\s+araw\b', 'pang-araw-araw'),
        (r'\btuloy\s+tuloy\b', 'tuloy-tuloy'),
        (r'\bcheck\s+up\b', 'check-up'),
        (r'\bfollow\s+up\b', 'follow-up'),
        (r'\bpost\s+operative\b', 'post-operative'),
        (r'\bpre\s+operative\b', 'pre-operative'),
        (r'\bng\s+ayon\b', 'ngayon'),
        (r'\bisa-i\b', 'isa-isa'),
        (r'\bmag-isa\s+siya\b', 'mag-isa siya'),
        (r'\bmag-isa\s+bumangon\b', 'mag-isa bumangon'),
        (r'\bmag-isa\s+kumain\b', 'mag-isa kumain'),
        (r'\bmag-isa\s+maglakad\b', 'mag-isa maglakad'),
        (r'\bmag-isa\s+maligo\b', 'mag-isa maligo'),
        (r'\bnag-iisa\s+siya\b', 'nag-iisa siya'),
        (r'\bnag-iisang\b', 'nag-iisang'),
        (r'\bhindi\s+gumagana\b', 'hindi gumagana'),
        (r'\bhindi\s+siya\b', 'hindi siya'),
        (r'\bhindi\s+naliligo\b', 'hindi naliligo'),
        (r'\bhindi\s+kakain\b', 'hindi kakain'),
        (r'\bhindi\s+mabilis\b', 'hindi mabilis'),
        (r'\bhindi\s+makausap\b', 'hindi makausap'),
        (r'\bself\s+care\b', 'self-care'),
        (r'\bi\s+monitor\b', 'i-monitor'),
        (r'\bi\s+document\b', 'i-document'),
        (r'\bi\s+refer\b', 'i-refer'),
        (r'\bi\s+ensure\b', 'i-ensure'),
        (r'\bi\s+follow\b', 'i-follow'),
        (r'\bself\s+assess\b', 'self-assess'),
        (r'\bmay\s+symptoms\b', 'may mga symptoms'),
        (r'\bmay\s+gamot\b', 'may mga gamot'),
        (r'\bmay\s+nutrition\b', 'may mga nutrition-based'),
        (r'\bng\s+symptoms\b', 'ng mga symptoms'),
        (r'\bng\s+gamot\b', 'ng mga gamot'),
        (r'\bng\s+beses\b', 'ng mga beses'),
        (r'\bng\s+araw\b', 'ng mga araw'),
        (r'\bng\s+hakbang\b', 'ng mga hakbang'),
        (r'\bsa\s+symptoms\b', 'sa mga symptoms'),
        (r'\bsa\s+gamot\b', 'sa mga gamot'),
        # Additional grammar/spelling fixes
        (r'\bng\s+ayon\b', 'ngayon'),
        (r'\bla,', 'sala,'),
        (r'\bSamakatuwid\b', 'Dahil dito,'),
        (r'\brili\b', 'sarili'),
        (r'\bpara\s+noia\b', 'paranoia'),
        (r'\bunit\s+ng\s+ayon\b', 'ngunit ngayon'),
        (r'\bsa\s+an\b', 'saan'),
        (r'\bsa\s+kit\b', 'sakit'),
        (r'\bsa\s+fety\b', 'safety'),
        (r'\bsa\s+rili\b', 'sarili'),
        (r'\bisa-i\b', 'isa-isa'),
        (r'\bpara\s+sa\s+rili\b', 'para sa sarili'),
        (r'\bkanyanger\b', 'kanyang anger'),
        (r'\bnagsusul\b', 'nagsusulat'),
        (r'\bsa\s+bay-sa\s+bay\b', 'sabay-sabay'),
        (r'\bsa\s+pat\b', 'sapat'),
    ]
    for pattern, replacement in fixes:
        text = re.sub(pattern, replacement, text, flags=re.IGNORECASE)

    # Fix spaces around parentheses
    text = re.sub(r'\s*\(\s*', ' (', text)
    text = re.sub(r'\s*\)\s*', ') ', text)
    
    # Normalize whitespace
    text = re.sub(r'\s+', ' ', text)
    
    return text.strip()