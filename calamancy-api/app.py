from flask import Flask, request, jsonify
import spacy
import os
import pickle
from collections import defaultdict

# Import our preprocessing modules
from text_preprocessing import preprocess_tagalog_text, enhance_medical_assessment
from tagalog_medical_terms import get_all_medical_terms, BODY_PARTS, get_assessment_evaluation_examples
from section_classifier import build_section_classifier, classify_sentence, load_training_data, save_classifier, load_classifier
from topic_modeling import extract_sections_with_parser, enhance_medical_entities, integrate_enhanced_entities

# Import enhanced NLP functions
from enhanced_nlp import (
    extract_key_relations,
    analyze_symptoms_with_morphology,
    TagalogMedicalKG,
    generate_enhanced_summary,
    detect_document_type,  # Add this import
    extract_non_medical_aspects,  # Add this import
    extract_key_concerns_improved  # Ensure this is imported
)

app = Flask(__name__)

# Create models directory if it doesn't exist
os.makedirs('models', exist_ok=True)

# Improved model loading using calamancy properly
try:
    import calamancy
    print("Attempting to load model with calamancy...")
    models_list = calamancy.models()
    print(f"Available calamancy models: {models_list}")
    
    # Get latest version of the medium model
    try:
        latest_version = calamancy.get_latest_version("tl_calamancy_md")
        print(f"Latest version of tl_calamancy_md: {latest_version}")
    except:
        print("Could not get latest version information")
    
    # Load the model
    nlp = calamancy.load("tl_calamancy_md")
    print(f"Successfully loaded model with calamancy: {nlp.pipe_names}")

except Exception as e:
    print(f"Error loading with calamancy: {e}")
    # Fallback options
    try:
        # Try direct spaCy loading
        nlp = spacy.load("tl_calamancy_md")
        print("Loaded model with spaCy directly")
    except:
        try:
            # Download if needed
            from spacy.cli import download
            print("Attempting to download tl_calamancy_md...")
            download("tl_calamancy_md")
            nlp = spacy.load("tl_calamancy_md")
            print("Downloaded and loaded model")
        except Exception as e:
            print(f"All loading methods failed: {e}")
            print("Using blank Tagalog model as last resort")
            nlp = spacy.blank("tl")

# Load medical terms and add them to the NLP pipeline
medical_terms = get_all_medical_terms()
print(f"Loaded {len(medical_terms)} medical terms")

# Add entity ruler if supported
if "entity_ruler" in nlp.pipe_names or "entity_ruler" in nlp.factory_names:
    try:
        ruler = nlp.get_pipe("entity_ruler")
    except:
        ruler = nlp.add_pipe("entity_ruler", before="ner" if "ner" in nlp.pipe_names else None)
    
    patterns = []
    # Add medical terms
    for term in medical_terms[:100]:  # Limit to first 100 terms to avoid overloading
        patterns.append({"label": "MEDICAL", "pattern": term})
    
    # Add body parts as specific entities
    for key, variations in BODY_PARTS.items():
        for term in variations:
            patterns.append({"label": "BODY_PART", "pattern": term})
    
    ruler.add_patterns(patterns)
    print("Added entity patterns")

# Initialize or load classifier
try:
    classifier_path = 'models/section_classifier.pkl'
    classifier_model = load_classifier(classifier_path)
    
    if classifier_model is None:
        print("Training new classification model...")
        training_data = load_training_data()
        if training_data:
            classifier_model = build_section_classifier(training_data)
            save_classifier(classifier_model, classifier_path)
            print(f"Classification model trained and saved to {classifier_path}")
    else:
        print("Loaded pre-existing classification model")
except Exception as e:
    print(f"Warning: Could not initialize classifier: {e}")
    classifier_model = None

@app.route('/health', methods=['GET'])
def health_check():
    """Simple health check endpoint"""
    return jsonify({
        "status": "healthy",
        "model": nlp.meta.get("lang", "unknown"),
        "pipeline": nlp.pipe_names,
        "classifier": "loaded" if classifier_model is not None else "unavailable"
    })

@app.route('/summarize', methods=['POST'])
def summarize_text():
    # Get data - support both JSON and form data
    if request.is_json:
        data = request.json
    else:
        data = request.form.to_dict()
        
    print(f"Request data received: {data}")  # Debug log
        
    if not data or 'text' not in data:
        return jsonify({'error': 'No text provided'}), 400
    
    text = data['text']
    doc_type = data.get('type', '').lower()
    
    # Define max_sentences from request or use default
    max_sentences = int(data.get('max_sentences', 3))
    
    # Use the provided type parameter from the request
    # Only fall back to auto-detection if type is not provided
    if not doc_type:
        from enhanced_nlp import detect_document_type
        doc_type = detect_document_type(text)
        print(f"Auto-detected document type: {doc_type}")
    else:
        print(f"Using provided document type: {doc_type}")
    
    is_assessment = doc_type == 'assessment'
    is_evaluation = doc_type == 'evaluation'
    
    if not text.strip():
        return jsonify({"error": "Empty text provided"}), 400
    
    try:
        # Use our preprocessing module to get detailed analysis
        analysis = preprocess_tagalog_text(text, detailed=True)
        
        # Process with NLP for additional insights
        doc = nlp(analysis["normalized_text"])
        
        # Generate enhanced summary using our new approach
        from enhanced_nlp import generate_enhanced_summary
        summary = generate_enhanced_summary(doc, analysis, max_sentences, is_assessment, is_evaluation)
        
        # Try the enhanced topic modeling approach with document type
        try:
            sections_via_parser = extract_sections_with_parser(doc, is_assessment, is_evaluation)
            print("Successfully extracted sections via parser")
        except Exception as e:
            print(f"Parser-based section extraction failed: {e}")
            sections_via_parser = {}
        
        # Extract sections using our classifier with document type
        sections = extract_distinct_sections_improved(analysis["sentences"], is_assessment, is_evaluation)
        
        # Merge the results, preferring parser-based sections if available
        for key, value in sections_via_parser.items():
            if value.strip():  # Only use non-empty sections
                sections[key] = value
        
        # Extract key relations using dependency parser
        from enhanced_nlp import extract_key_relations
        relation_sentences, key_relations = extract_key_relations(doc)
        
        # Get symptom information using morphological analysis
        from enhanced_nlp import analyze_symptoms_with_morphology
        symptoms = analyze_symptoms_with_morphology(doc)
        
        # Use our knowledge graph
        from enhanced_nlp import TagalogMedicalKG
        knowledge_graph = TagalogMedicalKG(None)  # NLP not needed here as we already have doc
        kg_results = knowledge_graph.analyze(doc)
        
        # Use our analysis for key concerns
        from enhanced_nlp import extract_non_medical_aspects
        concerns = extract_key_concerns_improved(analysis, doc)
        non_medical = extract_non_medical_aspects(doc)
        
        # Enhance medical entities
        try:
            enhanced_entities = enhance_medical_entities(doc)
            entity_info = integrate_enhanced_entities(enhanced_entities, text)
            print(f"Found {len(enhanced_entities)} enhanced medical entities")
        except Exception as e:
            print(f"Enhanced entity extraction failed: {e}")
            entity_info = {"body_parts": [], "medical_conditions": [], "entity_list": []}
        
        # Add new rich analysis to the response
        result = {
            "summary": summary,
            "sections": sections,
            "key_concerns": concerns,
            "sentence_count": len(analysis["sentences"]),
            "preprocessing_applied": True,
            "entities": entity_info,
            "relations": key_relations[:5],
            "symptoms": symptoms,
            "knowledge_graph": {
                "symptoms": kg_results["symptoms"][:5],
                "body_parts": kg_results["body_parts"],
                "relations": kg_results["relations"][:5],
                "needs": kg_results["needs"]
            },
            "document_type": doc_type,  # Return the document type used
            "non_medical_aspects": non_medical
        }
        
        return jsonify(result)
    
    except Exception as e:
        import traceback
        print(f"Error in summarize_text: {e}")
        print(traceback.format_exc())
        return jsonify({"error": str(e)}), 500

def generate_concise_summary(doc, analysis, max_sentences=3, is_assessment=False, is_evaluation=False):
    """Generate a truly concise summary focused on key health findings"""
    # Use our enhanced analysis to generate summary
    sentences = analysis["sentences"]
    
    # If text is already short, return as is
    if len(sentences) <= 2:
        return doc.text
    
    # Build health aspects based on our analysis
    health_aspects = []
    
    # Add mobility information if available
    if analysis["mobility_mentioned"] and analysis["mobility_sentences"]:
        if "assistive device" in " ".join(analysis["mobility_sentences"]).lower():
            health_aspects.append("Gumagamit ng assistive device para sa paglalakad.")
        elif "nangangatal" in " ".join(analysis["mobility_sentences"]).lower():
            health_aspects.append("May panginginig sa katawan.")
        elif any(term in " ".join(analysis["mobility_sentences"]).lower() for term in ["hirap", "mahinay"]):
            health_aspects.append("Nahihirapan sa paggalaw at paglalakad.")
        else:
            health_aspects.append(analysis["mobility_sentences"][0])
    
    # Add pain information if available
    if analysis["pain_mentioned"] and analysis["pain_sentences"]:
        # Try to identify body part with pain
        pain_text = " ".join(analysis["pain_sentences"]).lower()
        body_part_found = False
        
        for part_key, variations in BODY_PARTS.items():
            if any(term in pain_text.lower() for term in variations):
                health_aspects.append(f"May nararamdamang sakit sa {part_key}.")
                body_part_found = True
                break
                
        if not body_part_found:
            # If no specific body part found
            health_aspects.append("May nararamdamang sakit.")
    
    # Add sensory issues if found
    if "vision" in analysis["sensory_issues"]:
        health_aspects.append("May problema sa paningin.")
        
    if "hearing" in analysis["sensory_issues"]:
        health_aspects.append("May kahirapan sa pandinig.")
    
    # Add emotional state if found
    if "depressed" in analysis["emotional_state"]:
        health_aspects.append("Nakararanas ng kalungkutan o depresyon.")
        
    if "anxious" in analysis["emotional_state"]:
        health_aspects.append("May pagkabalisa o takot.")
    
    # Ensure we have at least one aspect
    if not health_aspects and sentences:
        health_aspects.append(sentences[0])
    
    # Limit to max_sentences
    if len(health_aspects) > max_sentences:
        health_aspects = health_aspects[:max_sentences]
    
    return " ".join(health_aspects)

def extract_distinct_sections_improved(sentences, is_assessment=False, is_evaluation=False):
    """Extract distinct sections using improved preprocessed sentences"""
    print(f"Processing {len(sentences)} preprocessed sentences")
    
    # Define output categories based on document type
    if is_assessment:
        categories = {
            "kalagayan_pangkatawan": [],  # Physical condition
            "mga_sintomas": [],           # Symptoms
            "pangangailangan": []         # Needs/requirements
        }
    elif is_evaluation:
        categories = {
            "pagbabago": [],              # Changes/progress
            "mga_hakbang": [],            # Steps taken
            "rekomendasyon": []           # Recommendations
        }
    else:
        categories = {
            "kalagayan": [],              # General condition
            "obserbasyon": [],            # Observations
            "rekomendasyon": []           # Recommendations
        }
    
    # Track which sentences have been assigned
    assigned = set()
    
    # Try to use classifier if it's available
    if classifier_model is not None:
        try:
            for i, sent in enumerate(sentences):
                if i in assigned:
                    continue
                
                # Predict category using our classifier
                category = classify_sentence(sent, classifier_model, is_assessment, is_evaluation)
                if category in categories:
                    categories[category].append(sent)
                    assigned.add(i)
                    print(f"Classifier assigned: '{sent[:30]}...' -> {category}")
        except Exception as e:
            print(f"Error using classifier: {e}")
    
    # Process sentences using rule-based method for those not classified by ML
    for i, sent in enumerate(sentences):
        if i in assigned:
            continue
            
        sent_lower = sent.lower()
        
        if is_assessment:
            # Physical condition indicators
            if any(term in sent_lower for term in ["malakas", "mahina", "hirap", "assistive", "paglalakad", "pag-upo", 
                                                 "nangangatal", "pabagsak", "pagkatumba"]):
                categories["kalagayan_pangkatawan"].append(sent)
                assigned.add(i)
                
            # Symptoms indicators  
            elif any(term in sent_lower for term in ["masakit", "sumasakit", "kirot", "daing", "malabo", "kuko", 
                                                   "naduduwal", "nagsusuka", "matalas", "panginginig"]):
                categories["mga_sintomas"].append(sent)
                assigned.add(i)
                
            # Needs indicators
            elif any(term in sent_lower for term in ["kailangan", "pangangailangan", "pension", "pera", "gatas", 
                                                   "tinapay", "mainit", "magpahangin", "araw", "isama", "apo"]):
                categories["pangangailangan"].append(sent)
                assigned.add(i)
                
        elif is_evaluation:
            # Changes/progress indicators
            if any(term in sent_lower for term in ["ngayon", "naging", "pagbuti", "pagkatapos", "matapos", 
                                                 "pagbabago", "bumuti", "lumala"]):
                categories["pagbabago"].append(sent)
                assigned.add(i)
                
            # Steps taken indicators
            elif any(term in sent_lower for term in ["ginawa", "isinagawa", "tinulungan", "inilagay", "binigyan", 
                                                   "pinakita", "nagturo", "nagamot"]):
                categories["mga_hakbang"].append(sent)
                assigned.add(i)
                
            # Recommendation indicators
            elif any(term in sent_lower for term in ["dapat", "kailangan", "inirerekumenda", "iminumungkahi", 
                                                   "makabubuting", "mabuting", "magsagawa"]):
                categories["rekomendasyon"].append(sent)
                assigned.add(i)
    
    # Distribute any remaining sentences to ensure coverage
    for i, sent in enumerate(sentences):
        if i not in assigned:
            if is_assessment:
                # Find section with fewest sentences
                min_category = min(categories.keys(), key=lambda k: len(categories[k]))
                categories[min_category].append(sent)
            elif is_evaluation:
                # Default to recommendations for unassigned evaluation sentences
                categories["rekomendasyon"].append(sent)
            else:
                # Default to observations for general text
                if "obserbasyon" in categories:
                    categories["obserbasyon"].append(sent) 
    
    # Combine sentences in each category
    result = {}
    for category, sents in categories.items():
        if sents:  # Only include non-empty categories
            result[category] = " ".join(sents)
    
    return result

def extract_key_concerns_improved(analysis, doc=None):
    """
    Extract key concerns using our enhanced analysis with non-medical aspects
    
    Args:
        analysis: Analysis data from text preprocessing
        doc: Optional spaCy Doc object for deeper analysis
        
    Returns:
        Dictionary of concerns
    """
    concerns = {}
    
    # Use our enhanced analysis to populate medical concerns
    if analysis["mobility_mentioned"]:
        concerns["mobility_issues"] = True
        if analysis["mobility_sentences"]:
            concerns["mobility_details"] = analysis["mobility_sentences"][0]
    
    if analysis["pain_mentioned"]:
        concerns["pain_reported"] = True
        if analysis["pain_sentences"]:
            concerns["pain_details"] = analysis["pain_sentences"][0]
    
    if "vision" in analysis["sensory_issues"]:
        concerns["vision_problems"] = True
    
    if "hearing" in analysis["sensory_issues"]:
        concerns["hearing_problems"] = True
    
    if analysis["emotional_state"]:
        concerns["emotional_concerns"] = analysis["emotional_state"]
    
    # Additional specific checks from original text
    text = analysis["normalized_text"].lower()
    
    # Check for fall risk
    if any(term in text for term in ["tumba", "natumba", "nahulog", "nadapa"]):
        concerns["fall_risk"] = True
    
    # Non-medical concerns from doc content
    if doc is not None:
        # Financial concerns
        if any(term in text for term in ["pension", "pera", "wala", "ubos", "gastos", "mahal"]):
            concerns["financial_concerns"] = True
            # Extract financial details
            for sent in doc.sents:
                if any(term in sent.text.lower() for term in ["pension", "pera", "wala", "ubos", "gastos", "mahal"]):
                    concerns["financial_details"] = sent.text
                    break
        
        # Social support assessment
        if "pamangkin" in text or "anak" in text or "apo" in text:
            # Assess quality of social support
            negative_indicators = ["iniwan", "wala", "hindi", "ayaw", "busy"]
            positive_indicators = ["kasama", "tulong", "suporta", "mahal", "malapit"]
            
            neg_count = sum(1 for term in negative_indicators if term in text)
            pos_count = sum(1 for term in positive_indicators if term in text)
            
            if neg_count > pos_count:
                concerns["social_support"] = "Poor"
            elif pos_count > 0:
                concerns["social_support"] = "Good"
            else:
                concerns["social_support"] = "Present but quality unclear"
    
    # Nutrition concerns
    if any(term in text for term in ["hindi kumakain", "pagbaba ng timbang", "payat", 
                                    "mataba", "naduduwal", "nasusuka", "timbang"]):
        concerns["nutrition_concerns"] = True
    
    # Environmental concerns
    if any(term in text for term in ["mainit", "malamig", "init", "lamig"]):
        concerns["environmental_concerns"] = True
    
    # Daily activity concerns
    if any(term in text for term in ["hirap gawin", "hindi na nagagawa", "nahihirapan", 
                                    "tulong", "tulungan", "gawain"]):
        concerns["daily_activity_concerns"] = True
    
    return {
        "concerns": concerns
    }

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)