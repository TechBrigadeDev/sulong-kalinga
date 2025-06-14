from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.pipeline import Pipeline
import os
import json
import pickle

def build_section_classifier(training_data):
    """
    Build a classifier for categorizing sentences into medical sections
    
    Args:
        training_data: list of (sentence, section) tuples
    
    Returns:
        Trained sklearn Pipeline
    """
    X = [text for text, _ in training_data]
    y = [label for _, label in training_data]
    
    # Use character n-grams for better handling of Tagalog
    pipeline = Pipeline([
        ('tfidf', TfidfVectorizer(
            ngram_range=(2, 5), 
            analyzer='char_wb',
            max_features=5000
        )),
        ('clf', LogisticRegression(C=10, max_iter=1000))
    ])
    
    pipeline.fit(X, y)
    return pipeline

def classify_sentence(sentence, classifier, is_assessment=False, is_evaluation=False):
    """
    Classify a single sentence into its appropriate section
    
    Args:
        sentence: The sentence to classify
        classifier: Trained classifier model
        is_assessment: Whether this is an assessment document
        is_evaluation: Whether this is an evaluation document
    
    Returns:
        Predicted category name
    """
    # Map document type to appropriate categories
    if is_assessment:
        categories = ["kalagayan_pangkatawan", "mga_sintomas", "pangangailangan"]
    elif is_evaluation:
        categories = ["pagbabago", "mga_hakbang", "rekomendasyon"]
    else:
        categories = ["kalagayan", "obserbasyon", "rekomendasyon"]
    
    # Make prediction
    prediction = classifier.predict([sentence])[0]
    
    # Make sure prediction is in valid categories, otherwise return default
    if prediction in categories:
        return prediction
    else:
        # Return default category based on document type
        if is_assessment:
            return "kalagayan_pangkatawan"
        elif is_evaluation:
            return "rekomendasyon"
        else:
            return "obserbasyon"

def load_training_data(filepath=None):
    """
    Load training data from file or generate from example assessments
    
    Args:
        filepath: Path to training data file
    
    Returns:
        List of (sentence, category) tuples
    """
    if filepath and os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as f:
            return json.load(f)
    
    # Generate training data from our assessment examples
    from tagalog_medical_terms import get_assessment_evaluation_examples
    from text_preprocessing import preprocess_tagalog_text
    
    training_data = []
    
    # Get examples 
    examples = get_assessment_evaluation_examples()
    
    # Process each example
    for example in examples:
        if "assessment" in example:
            # Preprocess text
            sentences = preprocess_tagalog_text(example["assessment"])
            
            # Manually assign categories based on keywords
            for sentence in sentences:
                sent_lower = sentence.lower()
                
                # Assign to appropriate category
                if any(term in sent_lower for term in ["malakas", "mahina", "hirap", "assistive", "paglalakad"]):
                    training_data.append((sentence, "kalagayan_pangkatawan"))
                elif any(term in sent_lower for term in ["masakit", "sumasakit", "kirot", "daing", "kuko"]):
                    training_data.append((sentence, "mga_sintomas"))
                elif any(term in sent_lower for term in ["kailangan", "pension", "pera", "tinapay"]):
                    training_data.append((sentence, "pangangailangan"))
        
        if "evaluation" in example:
            # Process evaluation sentences similarly
            sentences = preprocess_tagalog_text(example["evaluation"])
            
            for sentence in sentences:
                sent_lower = sentence.lower()
                
                if any(term in sent_lower for term in ["naging", "pagbuti", "ngayon", "bumuti"]):
                    training_data.append((sentence, "pagbabago"))
                elif any(term in sent_lower for term in ["ginawa", "tinulungan", "inilagay"]):
                    training_data.append((sentence, "mga_hakbang"))
                elif any(term in sent_lower for term in ["dapat", "kailangan", "inirerekumenda"]):
                    training_data.append((sentence, "rekomendasyon"))
    
    return training_data

def save_classifier(classifier, filepath):
    """Save the trained classifier to a file"""
    with open(filepath, 'wb') as f:
        pickle.dump(classifier, f)

def load_classifier(filepath):
    """Load a trained classifier from a file"""
    if os.path.exists(filepath):
        with open(filepath, 'rb') as f:
            return pickle.load(f)
    return None

# Training and initializing the classifier when module is imported
try:
    # Try to load pre-trained classifier
    classifier_model = load_classifier('models/section_classifier.pkl')
    
    # If not available, train a new one
    if classifier_model is None:
        training_data = load_training_data()
        if training_data:
            classifier_model = build_section_classifier(training_data)
            # Create directory if it doesn't exist
            os.makedirs('models', exist_ok=True)
            # Save for future use
            save_classifier(classifier_model, 'models/section_classifier.pkl')
except Exception as e:
    print(f"Warning: Could not load or train classifier: {e}")
    classifier_model = None