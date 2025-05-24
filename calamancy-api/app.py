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
    Extract content into logical sections based on patterns in Tagalog medical text.
    """
    # Common sections in medical assessments/evaluations (with Tagalog equivalents)
    potential_sections = {
        "vital_signs": [],
        "symptoms": [],
        "observations": [],
        "findings": [],
        "recommendations": [],
        "treatment": [],
        "follow_up": []
    }
    
    # Updated patterns with Tagalog equivalents
    patterns = {
        # Both English and Tagalog terms (vital signs often kept in English)
        "vital_signs": r'(?i)(vital signs|mga vital sign|pulso|presyon ng dugo|temperatura)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        
        # Symptoms
        "symptoms": r'(?i)(symptoms|mga sintomas|sintomas|karamdaman)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        
        # Observations 
        "observations": r'(?i)(observations|mga obserbasyon|obserbasyon|napansin)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        
        # Findings
        "findings": r'(?i)(findings|mga natuklasan|natuklasan|mga nakita|assessment)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        
        # Recommendations
        "recommendations": r'(?i)(recommendations|mga rekomendasyon|rekomendasyon|payo)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        
        # Treatment 
        "treatment": r'(?i)(treatment|paggamot|lunas|gamot)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)',
        
        # Follow-up (often kept in English)
        "follow_up": r'(?i)(follow[- ]up|susunod na checkup|susunod na pagpapatingin)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|\Z)'
    }
    
    for section, pattern in patterns.items():
        matches = re.findall(pattern, text)
        if matches:
            # Extract the content part from the tuple (pattern, content)
            section_content = [m[1].strip() if isinstance(m, tuple) and len(m) > 1 else m.strip() for m in matches]
            potential_sections[section] = section_content
    
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
    
    # If no sections were found, create general ones with Tagalog-appropriate names
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
            sections = {"buod": " ".join([s.text for s in sentences]).strip()}  # "buod" means "summary" in Tagalog
    
    return sections

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)