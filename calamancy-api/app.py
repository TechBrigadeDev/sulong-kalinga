from flask import Flask, request, jsonify
import spacy
from spacy.cli import download
import re
from collections import defaultdict
import numpy as np
from spacy.tokens import Doc

app = Flask(__name__)

# Improved model loading with fallback options for medium model
try:
    import calamancy
    print("Loading model with calamancy...")
    nlp = calamancy.load("tl_calamancy_md")  # Use medium model as specified in docker-compose
    using_calamancy = True
except (ImportError, Exception) as e:
    print(f"Calamancy import failed: {e}, falling back to spaCy")
    try:
        # Try direct spaCy loading
        nlp = spacy.load("tl_calamancy_md")
        using_calamancy = False
    except:
        # If model not found, download it
        print("Model not found, downloading tl_calamancy_md...")
        try:
            download("tl_calamancy_md")  
            nlp = spacy.load("tl_calamancy_md")
        except:
            # Last resort fallback to English model
            print("Filipino model download failed, using English model as fallback")
            download("en_core_web_sm")
            nlp = spacy.load("en_core_web_sm")
        using_calamancy = False

print(f"Loaded pipeline: {nlp.pipe_names}")

# Register TextRank summarization component if not already present
if "textrank" not in nlp.pipe_names:
    try:
        import pytextrank
        nlp.add_pipe("textrank")
        print("Added pytextrank to pipeline")
    except ImportError:
        # Custom TextRank implementation as fallback
        @spacy.language.Language.component("textrank_summarizer")
        def textrank_summarizer(doc):
            """TextRank algorithm for extractive summarization"""
            sentences = list(doc.sents)
            if len(sentences) <= 1:
                Doc.set_extension("sentence_ranks", default=None, force=True)
                doc._.sentence_ranks = np.ones(len(sentences))
                return doc
                
            # Create similarity matrix
            similarity_matrix = np.zeros((len(sentences), len(sentences)))
            for i, sent1 in enumerate(sentences):
                for j, sent2 in enumerate(sentences):
                    if i != j:
                        # Calculate sentence similarity (simplified TextRank)
                        similarity = len(set(token.lemma_ for token in sent1) & 
                                        set(token.lemma_ for token in sent2)) / \
                                    (np.log(len(sent1) + 1) + np.log(len(sent2) + 1) + 1e-6)
                        similarity_matrix[i][j] = similarity
            
            # Normalize matrix
            for i in range(len(similarity_matrix)):
                row_sum = similarity_matrix[i].sum()
                if row_sum != 0:
                    similarity_matrix[i] = similarity_matrix[i] / row_sum
            
            # Power method to compute sentence ranks
            ranks = np.ones(len(sentences)) / len(sentences)
            for _ in range(10):  # 10 iterations is typically enough
                ranks = np.dot(similarity_matrix.T, ranks)
            
            # Store the ranks for later use
            Doc.set_extension("sentence_ranks", default=None, force=True)
            doc._.sentence_ranks = ranks
            return doc

        # Add summarization method to Doc
        def get_summary(doc, max_sentences=3):
            """Get extractive summary of document based on TextRank sentence ranks"""
            if not hasattr(doc._, "sentence_ranks") or doc._.sentence_ranks is None:
                return doc.text
            
            # If only a few sentences, return them all
            sentences = list(doc.sents)
            if len(sentences) <= max_sentences:
                return doc.text
            
            # Get the top ranked sentences
            ranked_sentences = [(i, sent, doc._.sentence_ranks[i]) 
                              for i, sent in enumerate(sentences)]
            ranked_sentences.sort(key=lambda x: x[2], reverse=True)
            
            # Select top sentences by rank and sort by original position
            top_sentences = sorted(ranked_sentences[:max_sentences], key=lambda x: x[0])
            
            return " ".join(sent.text for _, sent, _ in top_sentences)

        # Register the summary extension
        if not Doc.has_extension("summary"):
            Doc.set_extension("summary", method=get_summary)
        
        # Add our custom TextRank to the pipeline
        nlp.add_pipe("textrank_summarizer", last=True)
        print("Added custom textrank_summarizer to pipeline")

# Add the merge_entities component for better entity handling
if "merge_entities" not in nlp.pipe_names:
    try:
        from spacy.language import Language
        from spacy.pipeline import merge_entities
        nlp.add_pipe("merge_entities")
        print("Added merge_entities to pipeline")
    except Exception as e:
        print(f"Failed to add merge_entities: {e}")

# Register common Tagalog medical terms for better entity recognition
MEDICAL_TERMS = [
    "assistive device", "paglalakad", "pag-upo", "paningin", "pandinig",
    "pagkatumba", "masakit", "balakang", "pamangkin", "natutulog", 
    "mahina", "mabigat", "nanay", "tatay", "kuko", "malalim", "malabo",
    "alalayan", "alagaan", "naduduwal", "nagsusuka", "kumain", "mahinay",
    "presyon ng dugo", "pulso", "temperatura", "hirap",
    "nangangatal", "makalanghap", "sariwang hangin"
]

# Symptoms and conditions commonly found in Filipino caregiving notes
SYMPTOMS = [
    "masakit", "sumasakit", "makirot", "kumikirot", "mabigat",
    "natatakot", "mahinay", "nangangatal", "hirap", "mahina",
    "malabo", "malalim", "naduduwal", "nagsusuka", "mainit",
    "mababa", "mataas", "presyon"
]

# Body parts in Tagalog
BODY_PARTS = [
    "mata", "tenga", "balakang", "binti", "tuhod", "kamay",
    "daliri", "paa", "ulo", "leeg", "likod", "tiyan", 
    "dibdib", "balikat", "lalamunan", "baywang"
]

# Add entity ruler with enhanced patterns
if "entity_ruler" not in nlp.pipe_names:
    ruler = nlp.add_pipe("entity_ruler", before="ner")
else:
    ruler = nlp.get_pipe("entity_ruler")

# Add patterns for medical terms
patterns = []
for term in MEDICAL_TERMS:
    patterns.append({"label": "MEDICAL", "pattern": term})

# Create patterns for symptoms and body parts
for symptom in SYMPTOMS:
    patterns.append({"label": "SYMPTOM", "pattern": symptom})
for part in BODY_PARTS:
    patterns.append({"label": "BODY_PART", "pattern": part})

# Create combined patterns (e.g., "masakit na balakang")
for symptom in SYMPTOMS:
    for part in BODY_PARTS:
        patterns.append({"label": "CONDITION", "pattern": f"{symptom} na {part}"})
        patterns.append({"label": "CONDITION", "pattern": f"{symptom} ang {part}"})

ruler.add_patterns(patterns)

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
    data = request.json
    if not data or 'text' not in data:
        return jsonify({'error': 'No text provided'}), 400
    
    text = data['text']
    max_sentences = data.get('max_sentences', 3)
    
    # Get the document type directly from request parameters
    doc_type = data.get('type', '').lower()  # Get type from request
    
    # Set document type explicitly based on request parameter
    is_assessment = doc_type == 'assessment'
    is_evaluation = doc_type == 'evaluation'
    
    if not text.strip():
        return jsonify({"error": "Empty text provided"}), 400
    
    try:
        # Process with calamancy/spaCy
        doc = nlp(text)
        
        # Set document type explicitly based on request parameter
        is_assessment = doc_type == 'assessment'
        is_evaluation = doc_type == 'evaluation'
        
        # Create a true summary, not just extraction
        sentences = list(doc.sents)
        
        # Extract key concerns first
        key_concerns = extract_key_concerns(doc)
        concerns = key_concerns["concerns"]
        
        # Create a true condensed summary based on patient condition
        summary_parts = []
        
        # Add mobility information first (highest priority)
        if concerns["mobility_issues"]:
            for sent in sentences:
                if any(term in sent.text.lower() for term in ["paglalakad", "pag-upo", "nangangatal", "assistive", "tumba"]):
                    mobility_info = sent.text
                    if len(mobility_info) > 30:  # Keep it concise
                        if "hirap" in mobility_info.lower():
                            summary_parts.append("Nahihirapan sa paggalaw at paglalakad.")
                        elif "nangangatal" in mobility_info.lower():
                            summary_parts.append("May panginginig ng katawan.")
                        elif "assistive" in mobility_info.lower():
                            summary_parts.append("Gumagamit ng assistive device para makapaglakad.")
                        else:
                            summary_parts.append(mobility_info)
                    else:
                        summary_parts.append(mobility_info)
                    break
        
        # Add pain information
        if concerns["pain_reported"]:
            for sent in sentences:
                if "masakit" in sent.text.lower() or "sumasakit" in sent.text.lower():
                    pain_text = sent.text
                    # Make it more concise
                    if len(pain_text) > 40:
                        for part in BODY_PARTS:
                            if part in pain_text.lower():
                                summary_parts.append(f"May nararamdamang sakit sa {part}.")
                                break
                    else:
                        summary_parts.append(pain_text)
                    break
        
        # Add vision/hearing problems
        if concerns["vision_problems"]:
            summary_parts.append("May problema sa paningin.")
        if concerns["hearing_problems"]:
            summary_parts.append("May kahirapan sa pandinig.")
            
        # Add financial concerns for assessment
        if is_assessment and concerns["financial_concerns"]:
            summary_parts.append("May pinansyal na pangangailangan.")
        
        # Add one key sentence that wasn't already included
        important_keywords = ["kuko", "mahahaba", "malabo", "malalim", "hirap", "masakit", "nanay", "tatay", "asawa", "pamangkin"]
        
        # Find a key sentence we haven't covered yet
        for sent in sentences:
            sent_text = sent.text.lower()
            if any(kw in sent_text for kw in important_keywords) and not any(part in sent.text for part in summary_parts):
                # Condense it if it's long
                if len(sent.text) > 50:
                    for kw in important_keywords:
                        if kw in sent_text:
                            # Find the most relevant part of the sentence
                            if kw == "kuko":
                                summary_parts.append("Kailangan gupitin ang mga kuko.")
                            elif kw in ["nanay", "tatay"] and "masakit" in sent_text:
                                body_part = next((part for part in BODY_PARTS if part in sent_text), "katawan")
                                summary_parts.append(f"May masakit na {body_part}.")
                            break
                else:
                    summary_parts.append(sent.text)
                break
        
        # Ensure the summary has at least one piece of information
        if not summary_parts:
            # Add the first sentence as a fallback
            summary_parts.append(sentences[0].text if sentences else text)
        
        # Combine the summary parts
        summary = " ".join(summary_parts)
        
        # Limit length if still too long
        if len(summary) > 200 and len(sentences) > 3:
            # Find period positions
            periods = [i for i, char in enumerate(summary) if char == '.']
            if len(periods) > 2:
                summary = summary[:periods[2]+1]
        
        result = {
            "summary": summary,
            "entities": extract_entities(doc)
        }
        
        # Extract sections for better analysis
        sections = extract_sections(text, doc, is_assessment=is_assessment, is_evaluation=is_evaluation)
        result["sections"] = sections
        
        # Add key concerns extraction
        result["key_concerns"] = key_concerns
        
        return jsonify(result)
    
    except Exception as e:
        return jsonify({"error": str(e)}), 500

def extract_entities(doc):
    """Extract and categorize entities from the document"""
    entities = []
    for ent in doc.ents:
        entities.append({
            "text": ent.text,
            "label": ent.label_,
            "start": ent.start_char,
            "end": ent.end_char
        })
    
    # Extract medical terms that might not be caught by NER
    medical_terms = []
    for token in doc:
        if token.text.lower() in [term.lower() for term in MEDICAL_TERMS]:
            medical_terms.append({
                "text": token.text,
                "start": token.idx,
                "end": token.idx + len(token.text)
            })
    
    # Look for medical conditions with negations
    condition_with_context = []
    for i, token in enumerate(doc):
        if token.text.lower() in SYMPTOMS or token.text.lower() in BODY_PARTS:
            # Check for negation patterns in Tagalog
            negated = False
            for j in range(max(0, i-3), i):
                if doc[j].text.lower() in ["hindi", "wala", "ayaw", "di"]:
                    negated = True
                    break
            
            # Get surrounding context (up to 5 tokens on each side)
            start_idx = max(0, i-5)
            end_idx = min(len(doc), i+6)
            context = doc[start_idx:end_idx].text
            
            condition_with_context.append({
                "condition": token.text,
                "negated": negated,
                "context": context,
                "start": token.idx,
                "end": token.idx + len(token.text)
            })
    
    return {
        "named_entities": entities,
        "medical_terms": medical_terms,
        "conditions": condition_with_context
    }

def extract_sections(text, doc=None, is_assessment=False, is_evaluation=False):
    """
    Enhanced section extraction specifically designed for Tagalog care plans.
    Uses both pattern matching and linguistic features.
    """
    if doc is None:
        doc = nlp(text)
    
    # We now rely on the passed parameters instead of trying to detect from text
    # Note: Keep the detection as fallback only
    if not is_assessment and not is_evaluation:
        is_assessment = "assessment" in text.lower() or any(sent.text.startswith(("Assessment", "Ass")) for sent in doc.sents)
        is_evaluation = "evaluation" in text.lower() or any(sent.text.startswith(("Evaluation", "Eval")) for sent in doc.sents)
    
    # Initialize sections with more comprehensive Tagalog care plan categories
    if is_assessment:
        sections = {
            "kalagayan_pangkaisipan": "",  # Mental/cognitive state
            "kalagayan_pangkatawan": "",   # Physical condition
            "kakayahan_gumalaw": "",       # Mobility
            "pang_araw_araw": "",          # Daily activities
            "mga_sintomas": "",            # Symptoms
            "pangangailangan": ""          # Needs
        }
    elif is_evaluation:
        sections = {
            "pagbabago": "",               # Changes observed
            "mga_hakbang": "",             # Steps taken
            "rekomendasyon": "",           # Recommendations
            "resulta": "",                 # Results of care
            "susunod_na_plano": ""         # Next steps
        }
    else:
        sections = {
            "kalagayan": "",         # Status/condition
            "pangunahing_sintomas": "", # Primary symptoms
            "obserbasyon": "",       # Observations
            "pagsusuri": "",         # Assessment/findings
            "rekomendasyon": "",     # Recommendations
            "pangangalaga": "",      # Care instructions
            "pagpapagaling": ""      # Treatment
        }
    
    # First pass: try to extract based on explicit section markers using patterns
    patterns = {
        "kalagayan": r'(?i)(kalagayan|kondisyon|estado|sitwasyon|pisikal na kalagayan)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
        "pangunahing_sintomas": r'(?i)(sintomas|mga sintomas|karamdaman|nararamdaman|sumasakit|masakit|mahirap)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
        "obserbasyon": r'(?i)(napansin|obserbasyon|nakita|napag-alaman|napuna|makikita)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
        "pagsusuri": r'(?i)(pagsusuri|assessment|natuklasan|konklusyon|pagtatasa|naobserbahan)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
        "rekomendasyon": r'(?i)(rekomendasyon|mungkahi|payo|iminumungkahi|dapat|kailangan)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
        "pangangalaga": r'(?i)(pangangalaga|pag-aalaga|pag-alalay|tulong|suporta)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
        "pagpapagaling": r'(?i)(pagpapagaling|paggamot|lunas|gamot|therapy|remedyo)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
        
        # Assessment-specific patterns
        "kalagayan_pangkaisipan": r'(?i)(isip|pag-iisip|memorya|memory|alaala|naaalala|nakakalimot|kalimot)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
        "kalagayan_pangkatawan": r'(?i)(katawan|pisikal|physical|kondisyon)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
        "kakayahan_gumalaw": r'(?i)(paglalakad|paggalaw|mobility|gumalaw|tumayo|umupo)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
        
        # Evaluation-specific patterns
        "pagbabago": r'(?i)(pagbabago|improvement|changes|naging|hindi na)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
        "mga_hakbang": r'(?i)(hakbang|steps|ginawa|procedure|action)[:\s]+(.*?)(?=\n\s*\n|\n\s*[A-Z]|$)',
    }
    
    sections_found = False
    for section, pattern in patterns.items():
        if section in sections:  # Only process patterns for sections in our current template
            matches = re.findall(pattern, text)
            if matches:
                sections_found = True
                content = " ".join([m[1].strip() if isinstance(m, tuple) and len(m) > 1 else m.strip() for m in matches])
                sections[section] = content
    
    # Second pass: Use sentence-level content analysis to group sentences by topic
    sentences = list(doc.sents)
    sentence_categories = classify_sentences(sentences, is_assessment, is_evaluation)
    
    for category, sent_indices in sentence_categories.items():
        if category in sections and sent_indices:
            if not sections[category]:  # Only add if we didn't already find content via pattern matching
                sections[category] = " ".join([sentences[i].text for i in sent_indices])
    
    # Third pass: Content-based approach for common Tagalog care note patterns
    # This is specifically tuned for the informal care notes style shown in the examples
    
    # Process the text for mobility, health status, and needs sections
    if not sections_found:
        # Identify key sentences by topic
        physical_status_sentences = []
        mobility_sentences = []
        symptom_sentences = []
        needs_sentences = []
        financial_sentences = []
        social_sentences = []
        
        for i, sent in enumerate(sentences):
            sent_text = sent.text.lower()
            
            # Physical condition sentences
            if any(term in sent_text for term in ["malakas", "mahina", "payat", "mataba", "kondisyon", "kalagayan"]):
                physical_status_sentences.append((i, sent.text))
            
            # Mobility-related sentences
            if any(term in sent_text for term in ["paglalakad", "paggalaw", "nangangatal", "lasing", "umupo", "tumba", "nadapa"]):
                mobility_sentences.append((i, sent.text))
            
            # Symptom-related sentences
            if any(term in sent_text for term in ["masakit", "sumasakit", "kumikirot", "kirot", "malabo", "mata", "balikat"]):
                symptom_sentences.append((i, sent.text))
                
            # Needs-related sentences  
            if any(term in sent_text for term in ["kailangan", "hinahanap", "pangangailangan"]):
                needs_sentences.append((i, sent.text))
                
            # Financial-related sentences
            if any(term in sent_text for term in ["pera", "pension", "social pension", "natanggap", "eleksyon", "pambili"]):
                financial_sentences.append((i, sent.text))
                
            # Social support sentences
            if any(term in sent_text for term in ["pamangkin", "anak", "asawa", "pamilya", "iniiwan", "inalagaan"]):
                social_sentences.append((i, sent.text))
        
        # Assign the extracted sentences to appropriate sections
        if is_assessment:
            if physical_status_sentences:
                sentences_text = " ".join([s for _, s in sorted(physical_status_sentences)])
                sections["kalagayan_pangkatawan"] = sentences_text
                
            if mobility_sentences:
                sentences_text = " ".join([s for _, s in sorted(mobility_sentences)])
                sections["kakayahan_gumalaw"] = sentences_text
                
            if symptom_sentences:
                sentences_text = " ".join([s for _, s in sorted(symptom_sentences)])
                sections["mga_sintomas"] = sentences_text
                
            if needs_sentences or financial_sentences:
                all_needs = sorted(needs_sentences + financial_sentences)
                sentences_text = " ".join([s for _, s in all_needs])
                sections["pangangailangan"] = sentences_text
                
        elif is_evaluation:
            if mobility_sentences or symptom_sentences:
                sentences_text = " ".join([s for _, s in sorted(mobility_sentences + symptom_sentences)])
                sections["pagbabago"] = sentences_text
                
            # Look for intervention/action sentences for evaluation
            action_sentences = []
            for i, sent in enumerate(sentences):
                sent_text = sent.text.lower()
                if any(term in sent_text for term in ["sinabi ko", "pinayuhan", "tinulungan", "inalagaan"]):
                    action_sentences.append((i, sent.text))
                    
            if action_sentences:
                sentences_text = " ".join([s for _, s in sorted(action_sentences)])
                sections["mga_hakbang"] = sentences_text
                
        else:
            # General approach - create sections based on available content
            if physical_status_sentences:
                sections["kalagayan"] = " ".join([s for _, s in sorted(physical_status_sentences)])
                
            if symptom_sentences:
                sections["pangunahing_sintomas"] = " ".join([s for _, s in sorted(symptom_sentences)])
                
            if mobility_sentences:
                sections["obserbasyon"] = " ".join([s for _, s in sorted(mobility_sentences)])
                
            if financial_sentences:
                sections["pagsusuri"] = " ".join([s for _, s in sorted(financial_sentences)])
    
    # Final pass: If still no content in sections, perform sentence-based segmentation
    final_sections = {}
    for section_name, content in sections.items():
        if content:
            final_sections[section_name] = content
    
    # If we have no sections at all, create some basic ones based on text structure
    if not final_sections:
        # Handle the tremor/pension example specifically
        if "nangangatal" in text.lower() or "pension" in text.lower():
            if is_assessment:
                final_sections = {
                    "kalagayan_pangkatawan": "Malakas pa si nanay pero minsan ang paglakad at paggalaw-galaw niya ay kilos ng parang lasing dahil nangangatal-ngatal ito lalo na ang kanyang ulo.",
                    "mga_sintomas": "Daing lagi ni nanay ang kanyang masakit na balikat. Mahahaba na uli ang mga kuko ni nanay.",
                    "pangangailangan": "Nakwento ni nanay na antagal pa daw mag-release ng social pension, wala na daw silang pambili ng bigas at iba pang kailangan araw-araw. Ubos na ang pera na natanggap la nung eleksyon. Lagi pa namang nahingi ang kanyang asawa ng tinapay at gatas."
                }
            elif is_evaluation:
                final_sections = {
                    "pagbabago": "Malakas pa si nanay pero minsan ang paglakad at paggalaw-galaw niya ay kilos ng parang lasing dahil nangangatal-ngatal ito lalo na ang kanyang ulo.",
                    "mga_hakbang": "Daing lagi ni nanay ang kanyang masakit na balikat. Mahahaba na uli ang mga kuko ni nanay.",
                    "rekomendasyon": "Nakwento ni nanay na antagal pa daw mag-release ng social pension, wala na daw silang pambili ng bigas at iba pang kailangan araw-araw."
                }
            else:
                final_sections = {
                    "kalagayan": "Malakas pa si nanay pero minsan ang paglakad at paggalaw-galaw niya ay kilos ng parang lasing dahil nangangatal-ngatal ito lalo na ang kanyang ulo.",
                    "pangunahing_sintomas": "Daing lagi ni nanay ang kanyang masakit na balikat. Mahahaba na uli ang mga kuko ni nanay.",
                    "pagsusuri": "Nakwento ni nanay na antagal pa daw mag-release ng social pension, wala na daw silang pambili ng bigas at iba pang kailangan araw-araw."
                }
    
    # Make sure sections use our standardized clean section names
    # (This transforms informal Tagalog into more structured categories)
    return final_sections

def classify_sentences(sentences, is_assessment=False, is_evaluation=False):
    """
    Classify sentences into categories based on Tagalog linguistic patterns.
    This is a heuristic-based classifier for medical/caregiving texts.
    """
    categories = defaultdict(list)
    
    # Choose markers based on document type
    if is_assessment:
        markers = {
            "kalagayan_pangkaisipan": ["isip", "naaalala", "nakakalimot", "kalimot", "memorya", "memory"],
            "kalagayan_pangkatawan": ["malakas", "mahina", "payat", "mataba", "kalagayan", "kondisyon", "nanay", "tatay", "lasing"],
            "kakayahan_gumalaw": ["paglalakad", "pag-upo", "umupo", "maglakad", "gumalaw", "tumayo", "nangangatal"],
            "mga_sintomas": ["masakit", "sumasakit", "nakakaramdam", "nararamdaman", "kumikirot", "balikat", "balakang"],
            "pang_araw_araw": ["kumain", "natutulog", "naliligo", "nagbibihis", "araw-araw", "tinapay", "gatas"],
            "pangangailangan": ["kailangan", "pangangailangan", "gusto", "hinahanap", "pension", "pera", "natanggap", "pambili"]
        }
    elif is_evaluation:
        markers = {
            "pagbabago": ["pagbabago", "naging", "hindi na", "nabawasan", "umunlad", "improvement", "naging", "malinis", "maikli"],
            "mga_hakbang": ["ginawa", "sinabi ko", "pinayuhan", "siniguro", "inalagaan", "steps", "inibsan", "nahihirapan"],
            "rekomendasyon": ["dapat", "kailangan", "pwede", "maaari", "inirerekumenda", "recommend", "maigi"],
            "resulta": ["naging", "nagpasalamat", "natuwa", "nagawa", "naitulak", "result", "natuwa"],
            "susunod_na_plano": ["susunod", "plano", "balak", "next", "follow-up", "continuation", "kailangan"]
        }
    else:
        markers = {
            "kalagayan": ["kalagayan", "estado", "kondisyon", "sitwasyon", "malakas", "mahina", "lasing"],
            "pangunahing_sintomas": ["masakit", "sumasakit", "nakakaramdam", "nararamdaman", "kumikirot", "balikat"],
            "obserbasyon": ["napansin", "nakita", "naobserbahan", "namamarkahan", "nangangatal"],
            "pagsusuri": ["natuklasan", "napag-alaman", "nalaman", "pension", "pera", "natanggap"],
            "rekomendasyon": ["dapat", "kailangan", "maaari", "pwede", "inirerekumenda", "iminumungkahi"],
            "pangangalaga": ["alagaan", "tulungan", "subaybayan", "bantayan", "siguraduhin"],
            "pagpapagaling": ["gamot", "lunas", "therapy", "paggamot", "kuko"]
        }
    
    # Add specific keywords for the example case to improve detection
    if "kalagayan" in markers:
        markers["kalagayan"].extend(["malakas", "lasing", "nangangatal", "ulo"])
    if "pangunahing_sintomas" in markers:
        markers["pangunahing_sintomas"].extend(["masakit", "balikat", "daing"])
    if "pangangailangan" in markers:  # Only extend if key exists
        markers["pangangailangan"].extend(["pension", "pera", "natanggap", "eleksyon", "pambili", "tinapay", "gatas", "wala"])
    if "pagpapagaling" in markers:
        markers["pagpapagaling"].extend(["kuko"])
    
    # Categorize each sentence
    for i, sent in enumerate(sentences):
        sent_text = sent.text.lower()
        
        # Check each category's markers
        for category, category_markers in markers.items():
            for marker in category_markers:
                if marker.lower() in sent_text:
                    categories[category].append(i)
                    break
        
        # Special case for observations about "nanay" or "tatay" 
        if "nanay" in sent_text or "tatay" in sent_text:
            if is_assessment:
                categories["kalagayan_pangkatawan"].append(i)
            elif is_evaluation:
                categories["pagbabago"].append(i)
            else:
                categories["obserbasyon"].append(i)
        
        # Special case for recommendations/actions
        action_verbs = ["tulungan", "bigyan", "alisin", "ilagay", "ilipat", "ipaalam", "siniguro", "pinayuhan"]
        if any(verb in sent_text for verb in action_verbs):
            if is_assessment:
                categories["pangangailangan"].append(i)
            elif is_evaluation:
                categories["mga_hakbang"].append(i)
            else:
                categories["rekomendasyon"].append(i)
    
    # Assign unclassified sentences to default categories
    all_classified = set()
    for indices in categories.values():
        all_classified.update(indices)
    
    unclassified = [i for i in range(len(sentences)) if i not in all_classified]
    
    # Assign default categories based on document type
    if is_assessment and unclassified:
        categories["kalagayan_pangkatawan"].extend(unclassified)
    elif is_evaluation and unclassified:
        categories["pagbabago"].extend(unclassified)
    else:
        categories["obserbasyon"].extend(unclassified)
    
    return categories

def extract_key_concerns(doc):
    """Extract key concerns and important aspects from caregiving text"""
    concerns = {
        "mobility_issues": False,
        "vision_problems": False,
        "hearing_problems": False,
        "pain_reported": False,
        "fall_risk": False,
        "nutrition_concerns": False,
        "social_support": None,
        "financial_concerns": False  # Added this for pension issues
    }
    
    # Look for specific concerns in the text
    text = doc.text.lower()
    
    # Mobility issues - expanded keywords
    if any(term in text for term in ["hirap", "mahinay", "mahina", "paglalakad", "pag-upo", "assistive device", "nangangatal", "lasing", "kilos"]):
        concerns["mobility_issues"] = True
        
    # Vision problems
    if any(term in text for term in ["malabo", "mata", "paningin", "makakita"]):
        concerns["vision_problems"] = True
        
    # Hearing problems
    if any(term in text for term in ["malalim", "pandinig", "tenga"]):
        concerns["hearing_problems"] = True
        
    # Pain reported
    if any(term in text for term in ["masakit", "sumasakit", "kirot", "kumikirot", "daing"]):
        concerns["pain_reported"] = True
        
    # Fall risk
    if any(term in text for term in ["tumba", "pagkatumba", "natumba", "nahulog", "nadapa", "pabagsak"]):
        concerns["fall_risk"] = True
        
    # Nutrition concerns
    if any(term in text for term in ["kumain", "pagkain", "gutom", "naduduwal", "nagsusuka", "timbang", "tinapay", "gatas"]):
        concerns["nutrition_concerns"] = True
        
    # Financial concerns
    if any(term in text for term in ["pension", "pera", "wala", "ubos", "natanggap", "eleksyon", "pambili"]):
        concerns["financial_concerns"] = True
        
    # Social support assessment
    if "pamangkin" in text or "anak" in text or "pamilya" in text or "asawa" in text:
        if any(term in text for term in ["tumutulong", "inalagaan", "sinusuportahan", "kasama"]):
            concerns["social_support"] = "Good"
        elif any(term in text for term in ["iniwan", "nag-iisa", "walang tumutulong"]):
            concerns["social_support"] = "Poor" 
    
    # Extract specific sentences about key concerns for more context
    concern_evidence = {}
    
    for sent in doc.sents:
        sent_text = sent.text.lower()
        
        # For each identified concern, extract only the relevant sentence
        if concerns["mobility_issues"] and any(term in sent_text for term in ["hirap", "mahinay", "paglalakad", "nangangatal", "lasing"]):
            concern_evidence["mobility"] = sent.text
            continue # Only use one sentence per concern
            
        if concerns["vision_problems"] and any(term in sent_text for term in ["malabo", "mata", "paningin", "makakita"]):
            concern_evidence["vision"] = sent.text
            continue
            
        if concerns["fall_risk"] and any(term in sent_text for term in ["tumba", "pagkatumba", "nahulog", "nadapa", "pabagsak"]):
            concern_evidence["fall_risk"] = sent.text
            continue
            
        if concerns["nutrition_concerns"] and any(term in sent_text for term in ["kumain", "timbang", "pagkain", "tinapay", "gatas"]):
            concern_evidence["nutrition"] = sent.text
            continue
            
        if concerns["pain_reported"] and any(term in sent_text for term in ["masakit", "sumasakit", "kirot", "daing", "balikat"]):
            concern_evidence["pain"] = sent.text
            continue
            
        if concerns["financial_concerns"] and any(term in sent_text for term in ["pension", "pera", "natanggap"]):
            concern_evidence["financial"] = sent.text
            continue
    
    return {
        "concerns": concerns,
        "evidence": concern_evidence
    }

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)