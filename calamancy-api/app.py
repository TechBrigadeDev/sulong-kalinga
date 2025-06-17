import os
import sys
from flask import Flask, request, jsonify
import traceback

app = Flask(__name__)

try:
    import numpy
    print(f"NumPy version: {numpy.__version__}")
    import spacy
    print(f"spaCy version: {spacy.__version__}")
    import calamancy
    print(f"Loading calamancy model...")
    
    # Use the versioned model name
    nlp = calamancy.load("tl_calamancy_md-0.2.0")
    print("Model loaded successfully")
except Exception as e:
    print(f"Error loading dependencies: {e}")
    traceback.print_exc()
    sys.exit(1)

# Add medical entities to the pipeline
if "entity_ruler" not in nlp.pipe_names:
    ruler = nlp.add_pipe("entity_ruler", before="ner")
    patterns = [
        # Diseases and conditions 
        {"label": "DISEASE", "pattern": "diabetes"},
        {"label": "DISEASE", "pattern": "type 2 diabetes"},
        {"label": "DISEASE", "pattern": "diyabetis"},
        {"label": "DISEASE", "pattern": "high blood pressure"},
        {"label": "DISEASE", "pattern": "altapresyon"},
        
        # Body parts
        {"label": "BODY_PART", "pattern": "dibdib"},
        {"label": "BODY_PART", "pattern": "puso"},
        {"label": "BODY_PART", "pattern": "tiyan"},
        
        # Symptoms
        {"label": "SYMPTOM", "pattern": "sakit"},
        {"label": "SYMPTOM", "pattern": "kirot"},
        {"label": "SYMPTOM", "pattern": "nahihirapang huminga"},
        
        # Food and nutrition terms
        {"label": "FOOD", "pattern": "kanin"},
        {"label": "FOOD", "pattern": "white bread"},
        {"label": "FOOD", "pattern": "kakanin"},
        
        # Medical measurements
        {"label": "MEASUREMENT", "pattern": "200 mg/dL"},
        {"label": "MEASUREMENT", "pattern": "blood glucose"},
        {"label": "MEASUREMENT", "pattern": "blood sugar"},
    ]
    # Add these patterns to the entity ruler
    if "entity_ruler" not in nlp.pipe_names:
        ruler = nlp.add_pipe("entity_ruler", before="ner")
        ruler.add_patterns(patterns)
    
    print(f"Added {len(patterns)} medical entity patterns")

def create_better_summary(doc, max_sentences=3):
    """Create a true summary by selecting important sentences."""
    # Get sentences
    sentences = [sent.text for sent in doc.sents]
    
    # If text is already short enough, return it all
    if len(sentences) <= max_sentences:
        return doc.text
    
    # Score sentences based on various factors
    scores = {}
    
    # 1. Score by position - first and last sentences are often important
    for i, sent in enumerate(sentences):
        position_score = 1.0
        if i == 0:  # First sentence
            position_score = 3.0
        elif i == len(sentences) - 1:  # Last sentence
            position_score = 2.0
        scores[sent] = position_score
    
    # 2. Score by presence of key terms
    key_medical_terms = ["diabetes", "blood", "glucose", "diet", "kalusugan", "sakit", 
                         "pagkain", "problema", "sintomas", "Type 2"]
    
    for sent in sentences:
        term_score = sum(2.0 for term in key_medical_terms if term.lower() in sent.lower())
        if sent in scores:
            scores[sent] += term_score
        else:
            scores[sent] = term_score
    
    # 3. Score by presence of named entities
    for sent in doc.sents:
        if any(ent.label_ in ["DISEASE", "MEDICAL", "BODY_PART", "PER"] for ent in sent.ents):
            if sent.text in scores:
                scores[sent.text] += 2.0
            else:
                scores[sent.text] = 2.0
    
    # 4. Select top sentences by score
    ranked_sentences = sorted([(score, sent) for sent, score in scores.items()], 
                             reverse=True)
    
    top_sentences = [sent for _, sent in ranked_sentences[:max_sentences]]
    
    # Sort sentences by their original order to maintain flow
    ordered_top_sentences = [sent for sent in sentences if sent in top_sentences]
    
    # Join sentences into a summary
    return " ".join(ordered_top_sentences)

def get_sentences(text):
    """Split text into sentences more accurately"""
    # First try spaCy's splitter
    doc = nlp(text)
    sentences = [sent.text.strip() for sent in doc.sents]
    
    # If that fails or returns just one sentence, try regex-based splitting
    if len(sentences) <= 1 and len(text) > 100:
        pattern = r'(?<=[.!?])\s+'
        sentences = [s.strip() for s in re.split(pattern, text) if s.strip()]
    
    # Ensure we have meaningful sentences
    sentences = [s for s in sentences if len(s) > 10]
    
    return sentences

def extract_sections_improved(sentences, doc_type="assessment"):
    """Extract sections with enhanced keyword matching"""
    # Define more comprehensive section keywords
    if doc_type == "assessment":
        section_keywords = {
            "kalagayan_pangkatawan": ["kondisyon", "katawan", "malakas", "mahina", 
                                      "pandinig", "paningin", "motor", "physical"],
            "mga_sintomas": ["sakit", "kirot", "masakit", "sumasakit", "hirap", 
                            "nahihirapan", "dugo", "presyon", "blood", "sugar"],
            "pangangailangan": ["kailangan", "tulong", "assistance", "gamot", 
                               "medikasyon", "bilhin", "therapy", "suporta"],
            "kalagayan_mental": ["kalungkutan", "depression", "anxiety", "nalilito", 
                                "nakalimutan", "memorya", "nakakalimot", "pagkabalisa"],
            "kalagayan_social": ["pamilya", "asawa", "anak", "pamangkin", "apo", 
                                "kapitbahay", "social", "samahan"]
        }
    else:  # evaluation
        section_keywords = {
            "pagbabago": ["pagbuti", "improvement", "naging", "nagbago", "lumala", 
                         "bumuti", "pagbabago", "progreso"],
            "mga_hakbang": ["ginawa", "isinagawa", "inayos", "inalis", "measures", 
                          "steps", "intervention", "trinatmento", "binigyan"],
            "rekomendasyon": ["dapat", "kailangan", "kinakailangan", "inirerekumenda", 
                            "iminumungkahi", "ipagpatuloy", "iwasan"]
        }
    
    result = {}
    
    # Process each sentence for classification
    for sent in sentences:
        sent_lower = sent.lower()
        assigned = False
        
        # Check for keywords in each section
        for section, keywords in section_keywords.items():
            if any(keyword in sent_lower for keyword in keywords):
                if section not in result:
                    result[section] = []
                result[section].append(sent)
                assigned = True
                break
        
        # Special handling for diabetes/food-related content
        if not assigned and any(term in sent_lower for term in ["diabetes", "blood sugar", "pagkain", "diet"]):
            if "mga_sintomas" not in result:
                result["mga_sintomas"] = []
            result["mga_sintomas"].append(sent)
            assigned = True
        
        # Default section for unmatched sentences
        if not assigned:
            default_section = "kalagayan_pangkatawan" if doc_type == "assessment" else "rekomendasyon"
            if default_section not in result:
                result[default_section] = []
            result[default_section].append(sent)
    
    # Convert lists to strings
    return {section: " ".join(sents) for section, sents in result.items() if sents}

@app.route('/health', methods=['GET'])
def health_check():
    """Simple health check endpoint"""
    return jsonify({
        "status": "healthy",
        "model": nlp.meta.get("lang", "unknown"),
        "pipeline": nlp.pipe_names
    })

@app.route('/summarize', methods=['POST'])
def summarize_text():
    """Summarize Tagalog text"""
    if not request.is_json:
        return jsonify({"error": "Request must be JSON"}), 400
        
    data = request.json
    text = data.get('text', '')
    doc_type = data.get('type', '')
    
    if not text:
        return jsonify({"error": "No text provided"}), 400
    
    try:
        # Process text with calamancy
        doc = nlp(text)
        
        # Extract sentences more carefully
        sentences = [sent.text for sent in doc.sents]
        if len(sentences) <= 1 and len(text) > 100:
            # Fallback to regex if spaCy didn't split sentences well
            import re
            sentences = [s.strip() for s in re.split(r'(?<=[.!?])\s+', text) if s.strip()]
        
        # Extract entities
        entities = [{"text": ent.text, "label": ent.label_} for ent in doc.ents]
        
        # Generate a true summary
        summary = create_better_summary(doc, max_sentences=3)
        
        # Classify content into sections
        sections = extract_sections_improved(sentences, doc_type)
        
        return jsonify({
            "summary": summary,
            "sections": sections,
            "sentence_count": len(sentences),
            "entities": entities,
            "document_type": doc_type
        })
        
    except Exception as e:
        print(f"Error processing text: {e}")
        traceback.print_exc()
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    print("Starting Flask server...")
    app.run(debug=True, host='0.0.0.0', port=5000)