from flask import Flask, request, jsonify
import calamancy
import spacy
import re

app = Flask(__name__)

# Load the calamanCy model (medium size for better balance of speed and performance)
nlp = calamancy.load("tl_calamancy_md-0.1.0")

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({"status": "healthy"})

@app.route('/summarize', methods=['POST'])
def summarize_text():
    data = request.json
    if not data or 'text' not in data:
        return jsonify({'error': 'No text provided'}), 400
    
    text = data['text']
    max_sentences = data.get('max_sentences', 3)
    sectioned = data.get('sectioned', False)
    
    if not text.strip():
        return jsonify({"error": "Empty text provided"}), 400
    
    try:
        # Basic summary
        doc = nlp(text)
        summary = doc._.summary(max_sentences=max_sentences)
        
        result = {
            "summary": summary,
        }
        
        # If sectioned is requested, try to extract sections
        if sectioned:
            sections = extract_sections(text)
            result["sections"] = sections
        
        return jsonify(result)
    
    except Exception as e:
        return jsonify({"error": str(e)}), 500

def extract_sections(text):
    """
    Extract content into logical sections based on patterns in the text.
    This is a basic implementation - improve with actual NLP categorization.
    """
    # Common sections in medical assessments/evaluations
    potential_sections = {
        "vital_signs": [],
        "symptoms": [],
        "observations": [],
        "findings": [],
        "recommendations": [],
        "treatment": [],
        "follow_up": []
    }
    
    # Simple pattern matching for demonstration
    # In real implementation, use a more sophisticated NLP approach
    patterns = {
        "vital_signs": r'(?i)vital signs[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        "symptoms": r'(?i)symptoms[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        "observations": r'(?i)observations[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        "findings": r'(?i)findings[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        "recommendations": r'(?i)recommendations[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        "treatment": r'(?i)treatment[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        "follow_up": r'(?i)follow[- ]up[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)'
    }
    
    for section, pattern in patterns.items():
        matches = re.findall(pattern, text)
        if matches:
            potential_sections[section] = [m.strip() for m in matches]
    
    # Generate summaries for sections
    sections = {}
    for section, content in potential_sections.items():
        if content:
            # Join multiple matches for the same section
            combined_content = " ".join(content)
            
            # Create a summary for longer sections
            if len(combined_content) > 200:
                doc = nlp(combined_content)
                sections[section] = doc._.summary(max_sentences=2).strip()
            else:
                sections[section] = combined_content.strip()
    
    # If no sections were found, create general ones
    if not sections:
        doc = nlp(text)
        sentences = list(doc.sents)
        
        # Simple distribution of sentences
        n = len(sentences)
        if n >= 3:
            sections = {
                "observations": " ".join([s.text for s in sentences[:n//3]]).strip(),
                "findings": " ".join([s.text for s in sentences[n//3:2*n//3]]).strip(),
                "recommendations": " ".join([s.text for s in sentences[2*n//3:]]).strip()
            }
        else:
            sections = {"summary": " ".join([s.text for s in sentences]).strip()}
    
    return sections

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)