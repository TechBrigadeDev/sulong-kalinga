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
        # First try standard loading
        nlp = calamancy.load("tl_calamancy_md")
        print("Model loaded successfully using standard method")
    except Exception as e1:
        errors.append(f"Standard loading failed: {e1}")
        try:
            # Try with version
            nlp = calamancy.load("tl_calamancy_md-0.2.0")
            print("Model loaded successfully using version-specific method")
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

# Add sentence splitting function here to avoid circular imports
def split_into_sentences(text):
    """Split text into sentences with improved handling for Tagalog text"""
    if not text:
        return []
        
    # Clean and normalize the text first
    text = re.sub(r'([.!?,:;])([A-Za-z])', r'\1 \2', text)
    text = re.sub(r'(\w)\s+-\s+(\w)', r'\1-\2', text)  # Fix over-spaced hyphens first
    text = re.sub(r'(\s)-(\s)', r'\1 - \2', text)  # Ensure spaces around standalone dashes
    text = re.sub(r'\s+', ' ', text).strip()
    
    # Try spaCy's sentence splitter first
    doc = nlp(text)
    sentences = [sent.text.strip() for sent in doc.sents]
    
    # If that returns just one sentence for a long text, use regex-based splitting
    if len(sentences) <= 1 and len(text) > 100:
        # Tagalog/English end-of-sentence patterns
        sentence_endings = r'(?<=[.!?])\s+'
        sentences = [s.strip() for s in re.split(sentence_endings, text) if s.strip()]
        
        # Further split by common conjunctions if sentences are still very long
        refined_sentences = []
        for sent in sentences:
            if len(sent) > 150:  # Only split long sentences
                # Split by Tagalog conjunctions but not in the middle of dates or numbers
                conjunction_splits = re.split(r'(?<!\d)\s+(?:ngunit|subalit|datapwat|gayunman|kapag|kung)\s+', sent)
                refined_sentences.extend([s.strip() for s in conjunction_splits if s.strip()])
            else:
                refined_sentences.append(sent)
        sentences = refined_sentences
    
    # Filter out very short or empty sentences
    return [sent for sent in sentences if len(sent) > 10]

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
    
    # Fix spaces around parentheses
    text = re.sub(r'\s*\(\s*', ' (', text)
    text = re.sub(r'\s*\)\s*', ') ', text)
    
    # Normalize whitespace
    text = re.sub(r'\s+', ' ', text)
    
    return text.strip()