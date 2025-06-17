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
        
        # Extract sentences
        sentences = [sent.text for sent in doc.sents]
        
        # Extract entities
        entities = [{"text": ent.text, "label": ent.label_} for ent in doc.ents]
        
        # Create simple summary (first 3 sentences or fewer if text is shorter)
        summary_sentences = sentences[:min(3, len(sentences))]
        summary = " ".join(summary_sentences)
        
        # Create basic sections based on content
        sections = {}
        
        if doc_type == 'assessment':
            # Simple rule-based sectioning for assessment
            for sent in sentences:
                sent_lower = sent.lower()
                if any(term in sent_lower for term in ["kalagayan", "kondisyon", "malakas", "mahina"]):
                    if "kalagayan_pangkatawan" not in sections:
                        sections["kalagayan_pangkatawan"] = []
                    sections["kalagayan_pangkatawan"].append(sent)
                elif any(term in sent_lower for term in ["masakit", "kirot", "sakit"]):
                    if "mga_sintomas" not in sections:
                        sections["mga_sintomas"] = []
                    sections["mga_sintomas"].append(sent)
                else:
                    if "pangangailangan" not in sections:
                        sections["pangangailangan"] = []
                    sections["pangangailangan"].append(sent)
        
        elif doc_type == 'evaluation':
            # Simple rule-based sectioning for evaluation
            for sent in sentences:
                sent_lower = sent.lower()
                if any(term in sent_lower for term in ["pagbuti", "pagbabago", "naging"]):
                    if "pagbabago" not in sections:
                        sections["pagbabago"] = []
                    sections["pagbabago"].append(sent)
                elif any(term in sent_lower for term in ["ginawa", "isinagawa", "inayos"]):
                    if "mga_hakbang" not in sections:
                        sections["mga_hakbang"] = []
                    sections["mga_hakbang"].append(sent)
                else:
                    if "rekomendasyon" not in sections:
                        sections["rekomendasyon"] = []
                    sections["rekomendasyon"].append(sent)
        
        # Convert section arrays to text
        for key in sections:
            sections[key] = " ".join(sections[key])
        
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
    app.run(debug=True, host='0.0.0.0', port=5000)