from flask import Flask, request, jsonify
import spacy
import re
import time
import traceback
import calamancy
import difflib

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

    
medical_patterns = [
    # Persons/Roles
    {"label": "PER", "pattern": "Lolo"},
    {"label": "PER", "pattern": "Lola"},
    {"label": "PER", "pattern": "Nanay"},
    {"label": "PER", "pattern": "Tatay"},
    {"label": "PER", "pattern": "tagapag-alaga"},
    {"label": "PER", "pattern": "caregiver"},
    {"label": "PER", "pattern": "doktor"},
    {"label": "PER", "pattern": "nurse"},
    {"label": "PER", "pattern": "physical therapist"},
    
    # Body Parts
    {"label": "BODY_PART", "pattern": "dibdib"},
    {"label": "BODY_PART", "pattern": "puso"},
    {"label": "BODY_PART", "pattern": "tiyan"},
    {"label": "BODY_PART", "pattern": "kamay"},
    {"label": "BODY_PART", "pattern": "daliri"},
    {"label": "BODY_PART", "pattern": "kasukasuan"},
    {"label": "BODY_PART", "pattern": "tuhod"},
    {"label": "BODY_PART", "pattern": "balakang"},
    {"label": "BODY_PART", "pattern": "likod"},
    {"label": "BODY_PART", "pattern": "binti"},
    {"label": "BODY_PART", "pattern": "paa"},
    {"label": "BODY_PART", "pattern": "mata"},
    {"label": "BODY_PART", "pattern": "tenga"},
    {"label": "BODY_PART", "pattern": "bibig"},
    {"label": "BODY_PART", "pattern": "labi"},
    {"label": "BODY_PART", "pattern": "mukha"},
    {"label": "BODY_PART", "pattern": "ngipin"},
    {"label": "BODY_PART", "pattern": "batok"},
    {"label": "BODY_PART", "pattern": "ulo"},
    {"label": "BODY_PART", "pattern": "utak"},
    {"label": "BODY_PART", "pattern": "leeg"},
    {"label": "BODY_PART", "pattern": "braso"},
    {"label": "BODY_PART", "pattern": "balikat"},
    {"label": "BODY_PART", "pattern": "baywang"},
    {"label": "BODY_PART", "pattern": "lungs"},
    {"label": "BODY_PART", "pattern": "baga"},
    {"label": "BODY_PART", "pattern": "atay"},
    {"label": "BODY_PART", "pattern": "kidney"},
    {"label": "BODY_PART", "pattern": "bato"},
    {"label": "BODY_PART", "pattern": "lower back"},
    {"label": "BODY_PART", "pattern": "joints"},
    {"label": "BODY_PART", "pattern": "bladder"},
    {"label": "BODY_PART", "pattern": "pantog"},
    {"label": "BODY_PART", "pattern": "balat"},
    {"label": "BODY_PART", "pattern": "quadriceps"},
    {"label": "BODY_PART", "pattern": "kalamnan"},
    {"label": "BODY_PART", "pattern": "tissue"},
    {"label": "BODY_PART", "pattern": "arteries"},
    {"label": "BODY_PART", "pattern": "ugat"},
    {"label": "BODY_PART", "pattern": "spinal cord"},
    {"label": "BODY_PART", "pattern": "gulugod"},
    {"label": "BODY_PART", "pattern": "sacrum"},
    {"label": "BODY_PART", "pattern": "heels"},

    # Diseases and Conditions
    {"label": "DISEASE", "pattern": "diabetes"},
    {"label": "DISEASE", "pattern": "type 2 diabetes"},
    {"label": "DISEASE", "pattern": "diyabetis"},
    {"label": "DISEASE", "pattern": "high blood pressure"},
    {"label": "DISEASE", "pattern": "altapresyon"},
    {"label": "DISEASE", "pattern": "hypertension"},
    {"label": "DISEASE", "pattern": "hyperglycemia"},
    {"label": "DISEASE", "pattern": "dementia"},
    {"label": "DISEASE", "pattern": "Alzheimer"},
    {"label": "DISEASE", "pattern": "stroke"},
    {"label": "DISEASE", "pattern": "arthritis"},
    {"label": "DISEASE", "pattern": "depression"},
    {"label": "DISEASE", "pattern": "anxiety"},
    {"label": "DISEASE", "pattern": "pagkabalisa"},
    {"label": "DISEASE", "pattern": "kalungkutan"},
    {"label": "DISEASE", "pattern": "chronic pain"},
    {"label": "DISEASE", "pattern": "insomnia"},
    {"label": "DISEASE", "pattern": "sleep apnea"},
    {"label": "DISEASE", "pattern": "pneumonia"},
    {"label": "DISEASE", "pattern": "urinary tract infection"},
    {"label": "DISEASE", "pattern": "UTI"},
    {"label": "DISEASE", "pattern": "pressure ulcers"},
    {"label": "DISEASE", "pattern": "dehydration"},
    {"label": "DISEASE", "pattern": "malnutrition"},
    {"label": "DISEASE", "pattern": "dysphagia"},
    {"label": "DISEASE", "pattern": "cognitive decline"},
    {"label": "DISEASE", "pattern": "hearing loss"},
    {"label": "DISEASE", "pattern": "vision impairment"},
    {"label": "DISEASE", "pattern": "heart failure"},
    {"label": "DISEASE", "pattern": "COPD"},
    {"label": "DISEASE", "pattern": "osteoporosis"},
    {"label": "DISEASE", "pattern": "kidney disease"},
    {"label": "DISEASE", "pattern": "macular degeneration"},
    {"label": "DISEASE", "pattern": "Parkinson's disease"},
    {"label": "DISEASE", "pattern": "glaucoma"},
    {"label": "DISEASE", "pattern": "cataracts"},
    
    # Symptoms
    {"label": "SYMPTOM", "pattern": "sakit"},
    {"label": "SYMPTOM", "pattern": "kirot"},
    {"label": "SYMPTOM", "pattern": "nahihirapang huminga"},
    {"label": "SYMPTOM", "pattern": "hirap sa paghinga"},
    {"label": "SYMPTOM", "pattern": "kakapusan ng hininga"},
    {"label": "SYMPTOM", "pattern": "pagod"},
    {"label": "SYMPTOM", "pattern": "fatigue"},
    {"label": "SYMPTOM", "pattern": "pagkahilo"},
    {"label": "SYMPTOM", "pattern": "dizziness"},
    {"label": "SYMPTOM", "pattern": "pagsusuka"},
    {"label": "SYMPTOM", "pattern": "nausea"},
    {"label": "SYMPTOM", "pattern": "pananakit ng ulo"},
    {"label": "SYMPTOM", "pattern": "headache"},
    {"label": "SYMPTOM", "pattern": "pagkalito"},
    {"label": "SYMPTOM", "pattern": "confusion"},
    {"label": "SYMPTOM", "pattern": "pagkalimot"},
    {"label": "SYMPTOM", "pattern": "memory loss"},
    {"label": "SYMPTOM", "pattern": "pamamanhid"},
    {"label": "SYMPTOM", "pattern": "numbness"},
    {"label": "SYMPTOM", "pattern": "pamamaga"},
    {"label": "SYMPTOM", "pattern": "swelling"},
    {"label": "SYMPTOM", "pattern": "panananakit ng kasukasuan"},
    {"label": "SYMPTOM", "pattern": "joint pain"},
    {"label": "SYMPTOM", "pattern": "hirap sa paglalakad"},
    {"label": "SYMPTOM", "pattern": "difficulty walking"},
    {"label": "SYMPTOM", "pattern": "pagbagsak"},
    {"label": "SYMPTOM", "pattern": "falls"},
    {"label": "SYMPTOM", "pattern": "hirap sa pagtulog"},
    {"label": "SYMPTOM", "pattern": "sleep difficulty"},
    {"label": "SYMPTOM", "pattern": "walang gana sa pagkain"},
    {"label": "SYMPTOM", "pattern": "loss of appetite"},
    {"label": "SYMPTOM", "pattern": "pagbaba ng timbang"},
    {"label": "SYMPTOM", "pattern": "weight loss"},
    {"label": "SYMPTOM", "pattern": "tremors"},
    {"label": "SYMPTOM", "pattern": "panginginig"},
    {"label": "SYMPTOM", "pattern": "pagkabilaok"},
    {"label": "SYMPTOM", "pattern": "choking"},
    {"label": "SYMPTOM", "pattern": "ubo"},
    {"label": "SYMPTOM", "pattern": "coughing"},
    {"label": "SYMPTOM", "pattern": "hirap sa paglunok"},
    {"label": "SYMPTOM", "pattern": "difficulty swallowing"},
    {"label": "SYMPTOM", "pattern": "paghinga nang mabilis"},
    {"label": "SYMPTOM", "pattern": "rapid breathing"},
    {"label": "SYMPTOM", "pattern": "pagtatae"},
    {"label": "SYMPTOM", "pattern": "diarrhea"},
    {"label": "SYMPTOM", "pattern": "pagtitibi"},
    {"label": "SYMPTOM", "pattern": "constipation"},
    {"label": "SYMPTOM", "pattern": "paranoia"},
    {"label": "SYMPTOM", "pattern": "hallucinations"},
    {"label": "SYMPTOM", "pattern": "mood swings"},
    {"label": "SYMPTOM", "pattern": "agitation"},
    {"label": "SYMPTOM", "pattern": "matinding pag-aalala"},
    {"label": "SYMPTOM", "pattern": "severe worry"},
    {"label": "SYMPTOM", "pattern": "hopelessness"},
    {"label": "SYMPTOM", "pattern": "kawalan ng pag-asa"},
    {"label": "SYMPTOM", "pattern": "roller-coaster pattern"},
    {"label": "SYMPTOM", "pattern": "significant fluctuations"},
    {"label": "SYMPTOM", "pattern": "mataas na blood sugar"},
    {"label": "SYMPTOM", "pattern": "sundowning syndrome"},
    
    # Measurements
    {"label": "MEASUREMENT", "pattern": "mg/dL"},
    {"label": "MEASUREMENT", "pattern": "blood glucose"},
    {"label": "MEASUREMENT", "pattern": "blood sugar"},
    {"label": "MEASUREMENT", "pattern": "asukal sa dugo"},
    {"label": "MEASUREMENT", "pattern": "glucose readings"},
    {"label": "MEASUREMENT", "pattern": "presyon ng dugo"},
    {"label": "MEASUREMENT", "pattern": "blood pressure"},
    {"label": "MEASUREMENT", "pattern": "200 mg/dL"},
    {"label": "MEASUREMENT", "pattern": "respiratory rate"},
    {"label": "MEASUREMENT", "pattern": "24-26 breaths per minute"},
    {"label": "MEASUREMENT", "pattern": "32-34 breaths per minute"},
    {"label": "MEASUREMENT", "pattern": "37.8°C"},
    {"label": "MEASUREMENT", "pattern": "timbang"},
    {"label": "MEASUREMENT", "pattern": "weight"},
    {"label": "MEASUREMENT", "pattern": "7 kilos"},
    {"label": "MEASUREMENT", "pattern": "5 kilograms"},
    {"label": "MEASUREMENT", "pattern": "6.2 kilograms"},
    {"label": "MEASUREMENT", "pattern": "13.6 pounds"},
    {"label": "MEASUREMENT", "pattern": "850-950 calories"},
    {"label": "MEASUREMENT", "pattern": "1,500 calories"},
    {"label": "MEASUREMENT", "pattern": "25-30 grams"},
    {"label": "MEASUREMENT", "pattern": "60 grams"},
    {"label": "MEASUREMENT", "pattern": "oxygen saturation"},
    
    # Foods and Diet
    {"label": "FOOD", "pattern": "kanin"},
    {"label": "FOOD", "pattern": "white bread"},
    {"label": "FOOD", "pattern": "brown rice"},
    {"label": "FOOD", "pattern": "kakanin"},
    {"label": "FOOD", "pattern": "Filipino dishes"},
    {"label": "FOOD", "pattern": "carbohydrate-rich"},
    {"label": "FOOD", "pattern": "lugaw"},
    {"label": "FOOD", "pattern": "sopas"},
    {"label": "FOOD", "pattern": "gulay"},
    {"label": "FOOD", "pattern": "vegetables"},
    {"label": "FOOD", "pattern": "prutas"},
    {"label": "FOOD", "pattern": "fruits"},
    {"label": "FOOD", "pattern": "karne"},
    {"label": "FOOD", "pattern": "meat"},
    {"label": "FOOD", "pattern": "isda"},
    {"label": "FOOD", "pattern": "fish"},
    {"label": "FOOD", "pattern": "tinapay"},
    {"label": "FOOD", "pattern": "bread"},
    {"label": "FOOD", "pattern": "sawsawan"},
    {"label": "FOOD", "pattern": "ulam"},
    {"label": "FOOD", "pattern": "fried dishes"},
    {"label": "FOOD", "pattern": "instant noodles"},
    {"label": "FOOD", "pattern": "canned goods"},
    {"label": "FOOD", "pattern": "cookies"},
    {"label": "FOOD", "pattern": "protein"},
    {"label": "FOOD", "pattern": "lean proteins"},
    
    # Medical equipment and aids
    {"label": "MEDICAL", "pattern": "wheelchair"},
    {"label": "MEDICAL", "pattern": "walker"},
    {"label": "MEDICAL", "pattern": "cane"},
    {"label": "MEDICAL", "pattern": "tungkod"},
    {"label": "MEDICAL", "pattern": "grab bars"},
    {"label": "MEDICAL", "pattern": "hearing aid"},
    {"label": "MEDICAL", "pattern": "eyeglasses"},
    {"label": "MEDICAL", "pattern": "salamin"},
    {"label": "MEDICAL", "pattern": "dentures"},
    {"label": "MEDICAL", "pattern": "pustiso"},
    {"label": "MEDICAL", "pattern": "walkers"},
    {"label": "MEDICAL", "pattern": "quad cane"},
    {"label": "MEDICAL", "pattern": "hospital bed"},
    {"label": "MEDICAL", "pattern": "commode"},
    {"label": "MEDICAL", "pattern": "raised toilet seat"},
    {"label": "MEDICAL", "pattern": "shower chair"},
    {"label": "MEDICAL", "pattern": "blood glucose monitor"},
    {"label": "MEDICAL", "pattern": "insulin pen"},
    {"label": "MEDICAL", "pattern": "syringe"},
    {"label": "MEDICAL", "pattern": "pill organizer"},
    {"label": "MEDICAL", "pattern": "compression stockings"},
    {"label": "MEDICAL", "pattern": "hoyer lift"},
    
    # Treatments and interventions
    {"label": "TREATMENT", "pattern": "medication"},
    {"label": "TREATMENT", "pattern": "gamot"},
    {"label": "TREATMENT", "pattern": "physical therapy"},
    {"label": "TREATMENT", "pattern": "exercise"},
    {"label": "TREATMENT", "pattern": "ehersisyo"},
    {"label": "TREATMENT", "pattern": "therapy"},
    {"label": "TREATMENT", "pattern": "paggamot"},
    {"label": "TREATMENT", "pattern": "maintenance"},
    {"label": "TREATMENT", "pattern": "antibiotics"},
    {"label": "TREATMENT", "pattern": "insulin"},
    {"label": "TREATMENT", "pattern": "pain management"},
    {"label": "TREATMENT", "pattern": "wound care"},
    {"label": "TREATMENT", "pattern": "dialysis"},
    {"label": "TREATMENT", "pattern": "oxygen therapy"},
    {"label": "TREATMENT", "pattern": "rehabilitation"},
    {"label": "TREATMENT", "pattern": "occupational therapy"},
    {"label": "TREATMENT", "pattern": "speech therapy"},
    {"label": "TREATMENT", "pattern": "counseling"},
    {"label": "TREATMENT", "pattern": "diet modification"},
    {"label": "TREATMENT", "pattern": "fluid restriction"},
    {"label": "TREATMENT", "pattern": "bed rest"},
    {"label": "TREATMENT", "pattern": "palliative care"},
    {"label": "TREATMENT", "pattern": "hospice care"},
    {"label": "TREATMENT", "pattern": "swallowing exercises"},
    {"label": "TREATMENT", "pattern": "cognitive stimulation"},
    {"label": "TREATMENT", "pattern": "balance training"},
    
    # Medications
    {"label": "MEDICATION", "pattern": "diuretic"},
    {"label": "MEDICATION", "pattern": "blood thinner"},
    {"label": "MEDICATION", "pattern": "cholesterol medication"},
    {"label": "MEDICATION", "pattern": "anti-inflammatory"},
    {"label": "MEDICATION", "pattern": "pain reliever"},
    {"label": "MEDICATION", "pattern": "antihypertensive"},
    {"label": "MEDICATION", "pattern": "antidepressant"},
    {"label": "MEDICATION", "pattern": "anti-anxiety"},
    {"label": "MEDICATION", "pattern": "sleeping pill"},
    {"label": "MEDICATION", "pattern": "statin"},
    {"label": "MEDICATION", "pattern": "beta blocker"},
    {"label": "MEDICATION", "pattern": "ACE inhibitor"},
    {"label": "MEDICATION", "pattern": "ARB"},
    {"label": "MEDICATION", "pattern": "calcium channel blocker"},
    {"label": "MEDICATION", "pattern": "metformin"},
    {"label": "MEDICATION", "pattern": "sulfonylurea"},
    {"label": "MEDICATION", "pattern": "GLP-1 agonist"},
    {"label": "MEDICATION", "pattern": "insulin"},
    {"label": "MEDICATION", "pattern": "aspirin"},
    {"label": "MEDICATION", "pattern": "paracetamol"},
    {"label": "MEDICATION", "pattern": "antibiotic"},
    
    # Healthcare professionals
    {"label": "HEALTHCARE_PROF", "pattern": "doktor"},
    {"label": "HEALTHCARE_PROF", "pattern": "doctor"},
    {"label": "HEALTHCARE_PROF", "pattern": "nurse"},
    {"label": "HEALTHCARE_PROF", "pattern": "nars"},
    {"label": "HEALTHCARE_PROF", "pattern": "physical therapist"},
    {"label": "HEALTHCARE_PROF", "pattern": "occupational therapist"},
    {"label": "HEALTHCARE_PROF", "pattern": "speech therapist"},
    {"label": "HEALTHCARE_PROF", "pattern": "dietitian"},
    {"label": "HEALTHCARE_PROF", "pattern": "nutritionist"},
    {"label": "HEALTHCARE_PROF", "pattern": "audiologist"},
    {"label": "HEALTHCARE_PROF", "pattern": "optometrist"},
    {"label": "HEALTHCARE_PROF", "pattern": "psychologist"},
    {"label": "HEALTHCARE_PROF", "pattern": "psychiatrist"},
    {"label": "HEALTHCARE_PROF", "pattern": "neurologist"},
    {"label": "HEALTHCARE_PROF", "pattern": "cardiologist"},
    {"label": "HEALTHCARE_PROF", "pattern": "endocrinologist"},
    {"label": "HEALTHCARE_PROF", "pattern": "geriatrician"},
    {"label": "HEALTHCARE_PROF", "pattern": "pharmacist"},
    {"label": "HEALTHCARE_PROF", "pattern": "dentist"},
    {"label": "HEALTHCARE_PROF", "pattern": "podiatrist"},
    {"label": "HEALTHCARE_PROF", "pattern": "caregiver"},
    {"label": "HEALTHCARE_PROF", "pattern": "tagapag-alaga"}
]
additional_patterns = [
    # Social Relationships
    {"label": "SOCIAL_REL", "pattern": "asawa"},
    {"label": "SOCIAL_REL", "pattern": "spouse"},
    {"label": "SOCIAL_REL", "pattern": "anak"},
    {"label": "SOCIAL_REL", "pattern": "children"},
    {"label": "SOCIAL_REL", "pattern": "apo"},
    {"label": "SOCIAL_REL", "pattern": "grandchildren"},
    {"label": "SOCIAL_REL", "pattern": "pamangkin"},
    {"label": "SOCIAL_REL", "pattern": "nephew"},
    {"label": "SOCIAL_REL", "pattern": "niece"},
    {"label": "SOCIAL_REL", "pattern": "magulang"},
    {"label": "SOCIAL_REL", "pattern": "parent"},
    {"label": "SOCIAL_REL", "pattern": "kapitbahay"},
    {"label": "SOCIAL_REL", "pattern": "neighbor"},
    {"label": "SOCIAL_REL", "pattern": "kaibigan"},
    {"label": "SOCIAL_REL", "pattern": "friend"},
    {"label": "SOCIAL_REL", "pattern": "pamilya"},
    {"label": "SOCIAL_REL", "pattern": "family"},
    {"label": "SOCIAL_REL", "pattern": "kamag-anak"},
    {"label": "SOCIAL_REL", "pattern": "relative"},
    
    # Social Settings and Activities
    {"label": "SOCIAL_ACT", "pattern": "simbahan"},
    {"label": "SOCIAL_ACT", "pattern": "church"},
    {"label": "SOCIAL_ACT", "pattern": "senior center"},
    {"label": "SOCIAL_ACT", "pattern": "community gathering"},
    {"label": "SOCIAL_ACT", "pattern": "birthday celebration"},
    {"label": "SOCIAL_ACT", "pattern": "family reunion"},
    {"label": "SOCIAL_ACT", "pattern": "pagtitipon"},
    {"label": "SOCIAL_ACT", "pattern": "gathering"},
    {"label": "SOCIAL_ACT", "pattern": "bisita"},
    {"label": "SOCIAL_ACT", "pattern": "visit"},
    {"label": "SOCIAL_ACT", "pattern": "pakikipag-usap"},
    {"label": "SOCIAL_ACT", "pattern": "conversation"},
    {"label": "SOCIAL_ACT", "pattern": "social media"},
    {"label": "SOCIAL_ACT", "pattern": "video call"},
    {"label": "SOCIAL_ACT", "pattern": "tawag"},
    {"label": "SOCIAL_ACT", "pattern": "phone call"},
    
    # Environment
    {"label": "ENVIRONMENT", "pattern": "bahay"},
    {"label": "ENVIRONMENT", "pattern": "home"},
    {"label": "ENVIRONMENT", "pattern": "apartment"},
    {"label": "ENVIRONMENT", "pattern": "kitchen"},
    {"label": "ENVIRONMENT", "pattern": "kusina"},
    {"label": "ENVIRONMENT", "pattern": "banyo"},
    {"label": "ENVIRONMENT", "pattern": "bathroom"},
    {"label": "ENVIRONMENT", "pattern": "kwarto"},
    {"label": "ENVIRONMENT", "pattern": "bedroom"},
    {"label": "ENVIRONMENT", "pattern": "sala"},
    {"label": "ENVIRONMENT", "pattern": "living room"},
    {"label": "ENVIRONMENT", "pattern": "hagdan"},
    {"label": "ENVIRONMENT", "pattern": "stairs"},
    {"label": "ENVIRONMENT", "pattern": "garden"},
    {"label": "ENVIRONMENT", "pattern": "hardin"},
    {"label": "ENVIRONMENT", "pattern": "bakuran"},
    {"label": "ENVIRONMENT", "pattern": "yard"},
    {"label": "ENVIRONMENT", "pattern": "village"},
    {"label": "ENVIRONMENT", "pattern": "barangay"},
    {"label": "ENVIRONMENT", "pattern": "rural"},
    {"label": "ENVIRONMENT", "pattern": "urban"},
    {"label": "ENVIRONMENT", "pattern": "probinsya"},
    {"label": "ENVIRONMENT", "pattern": "province"},
    {"label": "ENVIRONMENT", "pattern": "lungsod"},
    {"label": "ENVIRONMENT", "pattern": "city"},
    {"label": "ENVIRONMENT", "pattern": "Maynila"},
    
    # Emotional States
    {"label": "EMOTION", "pattern": "kalungkutan"},
    {"label": "EMOTION", "pattern": "sadness"},
    {"label": "EMOTION", "pattern": "pagkabalisa"},
    {"label": "EMOTION", "pattern": "anxiety"},
    {"label": "EMOTION", "pattern": "kawalan ng pag-asa"},
    {"label": "EMOTION", "pattern": "hopelessness"},
    {"label": "EMOTION", "pattern": "stress"},
    {"label": "EMOTION", "pattern": "frustration"},
    {"label": "EMOTION", "pattern": "pagkabigo"},
    {"label": "EMOTION", "pattern": "takot"},
    {"label": "EMOTION", "pattern": "fear"},
    {"label": "EMOTION", "pattern": "galit"},
    {"label": "EMOTION", "pattern": "anger"},
    {"label": "EMOTION", "pattern": "irritable"},
    {"label": "EMOTION", "pattern": "iritable"},
    {"label": "EMOTION", "pattern": "kasiyahan"},
    {"label": "EMOTION", "pattern": "happiness"},
    {"label": "EMOTION", "pattern": "contentment"},
    {"label": "EMOTION", "pattern": "kapanatagan"},
    {"label": "EMOTION", "pattern": "pride"},
    {"label": "EMOTION", "pattern": "dignity"},
    {"label": "EMOTION", "pattern": "dignidad"},
    {"label": "EMOTION", "pattern": "self-worth"},
    {"label": "EMOTION", "pattern": "embarrassment"},
    {"label": "EMOTION", "pattern": "kahihiyan"},
    
    # Cognitive Concepts
    {"label": "COGNITIVE", "pattern": "memorya"},
    {"label": "COGNITIVE", "pattern": "memory"},
    {"label": "COGNITIVE", "pattern": "nakalimutan"},
    {"label": "COGNITIVE", "pattern": "forgotten"},
    {"label": "COGNITIVE", "pattern": "pagkalito"},
    {"label": "COGNITIVE", "pattern": "confusion"},
    {"label": "COGNITIVE", "pattern": "orientation"},
    {"label": "COGNITIVE", "pattern": "disorientation"},
    {"label": "COGNITIVE", "pattern": "pag-iisip"},
    {"label": "COGNITIVE", "pattern": "thinking"},
    {"label": "COGNITIVE", "pattern": "decision-making"},
    {"label": "COGNITIVE", "pattern": "pagdedesisyon"},
    {"label": "COGNITIVE", "pattern": "comprehension"},
    {"label": "COGNITIVE", "pattern": "pag-unawa"},
    {"label": "COGNITIVE", "pattern": "attention"},
    {"label": "COGNITIVE", "pattern": "atensyon"},
    {"label": "COGNITIVE", "pattern": "concentration"},
    {"label": "COGNITIVE", "pattern": "konsentrasyon"},
    
    # Communication
    {"label": "COMMUNICATION", "pattern": "komunikasyon"},
    {"label": "COMMUNICATION", "pattern": "communication"},
    {"label": "COMMUNICATION", "pattern": "pakikipag-usap"},
    {"label": "COMMUNICATION", "pattern": "conversation"},
    {"label": "COMMUNICATION", "pattern": "pakikinig"},
    {"label": "COMMUNICATION", "pattern": "listening"},
    {"label": "COMMUNICATION", "pattern": "pagsasalita"},
    {"label": "COMMUNICATION", "pattern": "speaking"},
    {"label": "COMMUNICATION", "pattern": "pagbabasa"},
    {"label": "COMMUNICATION", "pattern": "reading"},
    {"label": "COMMUNICATION", "pattern": "pagsusulat"},
    {"label": "COMMUNICATION", "pattern": "writing"},
    {"label": "COMMUNICATION", "pattern": "language"},
    {"label": "COMMUNICATION", "pattern": "wika"},
    {"label": "COMMUNICATION", "pattern": "dialect"},
    {"label": "COMMUNICATION", "pattern": "diyalekto"},
    
    # Cultural Factors
    {"label": "CULTURAL", "pattern": "tradisyon"},
    {"label": "CULTURAL", "pattern": "tradition"},
    {"label": "CULTURAL", "pattern": "kultura"},
    {"label": "CULTURAL", "pattern": "culture"},
    {"label": "CULTURAL", "pattern": "belief"},
    {"label": "CULTURAL", "pattern": "paniniwala"},
    {"label": "CULTURAL", "pattern": "values"},
    {"label": "CULTURAL", "pattern": "religious"},
    {"label": "CULTURAL", "pattern": "relihiyon"},
    {"label": "CULTURAL", "pattern": "spiritual"},
    {"label": "CULTURAL", "pattern": "espiritual"},
    {"label": "CULTURAL", "pattern": "customs"},
    {"label": "CULTURAL", "pattern": "kaugalian"},
    {"label": "CULTURAL", "pattern": "Filipino lifestyle"},
    
    # Financial/Economic Aspects
    {"label": "ECONOMIC", "pattern": "gastos"},
    {"label": "ECONOMIC", "pattern": "expenses"},
    {"label": "ECONOMIC", "pattern": "budget"},
    {"label": "ECONOMIC", "pattern": "badyet"},
    {"label": "ECONOMIC", "pattern": "income"},
    {"label": "ECONOMIC", "pattern": "kita"},
    {"label": "ECONOMIC", "pattern": "insurance"},
    {"label": "ECONOMIC", "pattern": "pension"},
    {"label": "ECONOMIC", "pattern": "pensiyon"},
    {"label": "ECONOMIC", "pattern": "savings"},
    {"label": "ECONOMIC", "pattern": "financial concern"},
    {"label": "ECONOMIC", "pattern": "PhilHealth"},
    {"label": "ECONOMIC", "pattern": "social security"},
    {"label": "ECONOMIC", "pattern": "afford"},
    {"label": "ECONOMIC", "pattern": "cost"},
    {"label": "ECONOMIC", "pattern": "mahal"},
    {"label": "ECONOMIC", "pattern": "expensive"},
    
    # Daily Living Activities
    {"label": "ADL", "pattern": "pagligo"},
    {"label": "ADL", "pattern": "bathing"},
    {"label": "ADL", "pattern": "pagbibihis"},
    {"label": "ADL", "pattern": "dressing"},
    {"label": "ADL", "pattern": "pagluluto"},
    {"label": "ADL", "pattern": "cooking"},
    {"label": "ADL", "pattern": "paglilinis"},
    {"label": "ADL", "pattern": "cleaning"},
    {"label": "ADL", "pattern": "pagkain"},
    {"label": "ADL", "pattern": "eating"},
    {"label": "ADL", "pattern": "paglalaba"},
    {"label": "ADL", "pattern": "laundry"},
    {"label": "ADL", "pattern": "grocery"},
    {"label": "ADL", "pattern": "shopping"},
    {"label": "ADL", "pattern": "pamimili"},
    {"label": "ADL", "pattern": "transportation"},
    {"label": "ADL", "pattern": "commute"},
    {"label": "ADL", "pattern": "biyahe"},
    {"label": "ADL", "pattern": "self-care"},
    {"label": "ADL", "pattern": "hygiene"},
    {"label": "ADL", "pattern": "kalinisan"},
    
    # Safety and Independence
    {"label": "SAFETY", "pattern": "kaligtasan"},
    {"label": "SAFETY", "pattern": "safety"},
    {"label": "SAFETY", "pattern": "risk"},
    {"label": "SAFETY", "pattern": "peligro"},
    {"label": "SAFETY", "pattern": "danger"},
    {"label": "SAFETY", "pattern": "fall"},
    {"label": "SAFETY", "pattern": "pagkahulog"},
    {"label": "SAFETY", "pattern": "independent"},
    {"label": "SAFETY", "pattern": "malaya"},
    {"label": "SAFETY", "pattern": "dependent"},
    {"label": "SAFETY", "pattern": "nangangailangan ng tulong"},
    {"label": "SAFETY", "pattern": "supervision"},
    {"label": "SAFETY", "pattern": "pangagalaga"},
    {"label": "SAFETY", "pattern": "emergency"},
    {"label": "SAFETY", "pattern": "accident"},
    {"label": "SAFETY", "pattern": "aksidente"}
]
evaluation_patterns = [
    # Recommendation phrases (common in evaluations)
    {"label": "RECOMMENDATION", "pattern": "inirerekomenda ko"},
    {"label": "RECOMMENDATION", "pattern": "iminumungkahi ko"},
    {"label": "RECOMMENDATION", "pattern": "pinapayuhan ko"},
    {"label": "RECOMMENDATION", "pattern": "ipinapayo ko"},
    {"label": "RECOMMENDATION", "pattern": "nirerekomenda ko"},
    {"label": "RECOMMENDATION", "pattern": "binigyang-diin ko"},
    {"label": "RECOMMENDATION", "pattern": "iminungkahi ko"},
    {"label": "RECOMMENDATION", "pattern": "mahalagang"},
    {"label": "RECOMMENDATION", "pattern": "kinausap ko"},
    {"label": "RECOMMENDATION", "pattern": "maaaring subukan ang"},
    {"label": "RECOMMENDATION", "pattern": "dapat na"},
    {"label": "RECOMMENDATION", "pattern": "hinihikayat ko"},
    {"label": "RECOMMENDATION", "pattern": "binigyan ko ng"},
    {"label": "RECOMMENDATION", "pattern": "tinuruan ko"},
    {"label": "RECOMMENDATION", "pattern": "kailangan ng"},
    
    # Healthcare professional consultations
    {"label": "HEALTHCARE_REFERRAL", "pattern": "konsultasyon sa"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "pagpapatingin sa"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "kausapin ang doktor"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "medical evaluation"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "assessment mula sa"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "referral sa"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "physical therapist"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "occupational therapist"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "geriatric psychiatrist"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "audiologist"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "ophthalmologist"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "dietitian"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "nutritionist"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "gastroenterologist"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "geriatrician"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "neurologist"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "urologist"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "speech therapist"},
    
    # Treatment methods and approaches
    {"label": "TREATMENT_METHOD", "pattern": "modified yoga poses"},
    {"label": "TREATMENT_METHOD", "pattern": "gentle stretching"},
    {"label": "TREATMENT_METHOD", "pattern": "progressive muscle relaxation"},
    {"label": "TREATMENT_METHOD", "pattern": "deep breathing exercises"},
    {"label": "TREATMENT_METHOD", "pattern": "light physical activity"},
    {"label": "TREATMENT_METHOD", "pattern": "short walks"},
    {"label": "TREATMENT_METHOD", "pattern": "gentle exercises"},
    {"label": "TREATMENT_METHOD", "pattern": "structured bedtime routine"},
    {"label": "TREATMENT_METHOD", "pattern": "energy conservation techniques"},
    {"label": "TREATMENT_METHOD", "pattern": "memory enhancement"},
    {"label": "TREATMENT_METHOD", "pattern": "cognitive stimulation"},
    {"label": "TREATMENT_METHOD", "pattern": "compression stockings"},
    {"label": "TREATMENT_METHOD", "pattern": "heat therapy"},
    {"label": "TREATMENT_METHOD", "pattern": "hot compress"},
    {"label": "TREATMENT_METHOD", "pattern": "cold compress"},
    {"label": "TREATMENT_METHOD", "pattern": "pain management"},
    {"label": "TREATMENT_METHOD", "pattern": "repositioning schedule"},
    {"label": "TREATMENT_METHOD", "pattern": "fluid management"},
    {"label": "TREATMENT_METHOD", "pattern": "medication review"},
    {"label": "TREATMENT_METHOD", "pattern": "palliative care"},
    {"label": "TREATMENT_METHOD", "pattern": "grief counseling"},
    {"label": "TREATMENT_METHOD", "pattern": "psychological counseling"},
    {"label": "TREATMENT_METHOD", "pattern": "validation therapy"},
    
    # Equipment and aids
    {"label": "EQUIPMENT", "pattern": "grab bars"},
    {"label": "EQUIPMENT", "pattern": "raised toilet seat"},
    {"label": "EQUIPMENT", "pattern": "shower chair"},
    {"label": "EQUIPMENT", "pattern": "non-slip mats"},
    {"label": "EQUIPMENT", "pattern": "walker"},
    {"label": "EQUIPMENT", "pattern": "cane"},
    {"label": "EQUIPMENT", "pattern": "quad cane"},
    {"label": "EQUIPMENT", "pattern": "pill organizer"},
    {"label": "EQUIPMENT", "pattern": "handheld showerhead"},
    {"label": "EQUIPMENT", "pattern": "assistive devices"},
    {"label": "EQUIPMENT", "pattern": "adaptive clothing"},
    {"label": "EQUIPMENT", "pattern": "long-handled tools"},
    {"label": "EQUIPMENT", "pattern": "medication chart"},
    {"label": "EQUIPMENT", "pattern": "hearing aid"},
    {"label": "EQUIPMENT", "pattern": "white noise machine"},
    {"label": "EQUIPMENT", "pattern": "mattress overlay"},
    {"label": "EQUIPMENT", "pattern": "motion-activated lights"},
    {"label": "EQUIPMENT", "pattern": "bedside commode"},
    {"label": "EQUIPMENT", "pattern": "adaptive utensils"},
    {"label": "EQUIPMENT", "pattern": "dressing stick"},
    
    # Home safety and modifications
    {"label": "HOME_MODIFICATION", "pattern": "reorganization ng kitchen"},
    {"label": "HOME_MODIFICATION", "pattern": "ergonomic environment"},
    {"label": "HOME_MODIFICATION", "pattern": "blackout curtains"},
    {"label": "HOME_MODIFICATION", "pattern": "installation ng grab bars"},
    {"label": "HOME_MODIFICATION", "pattern": "night lights"},
    {"label": "HOME_MODIFICATION", "pattern": "elevated bed"},
    {"label": "HOME_MODIFICATION", "pattern": "lever-type faucets"},
    {"label": "HOME_MODIFICATION", "pattern": "contrasting color strips"},
    {"label": "HOME_MODIFICATION", "pattern": "removal ng loose rugs"},
    {"label": "HOME_MODIFICATION", "pattern": "adequate lighting"},
    {"label": "HOME_MODIFICATION", "pattern": "rubberized flooring"},
    
    # Monitoring and documentation
    {"label": "MONITORING", "pattern": "regular monitoring"},
    {"label": "MONITORING", "pattern": "observation ng symptoms"},
    {"label": "MONITORING", "pattern": "pagmo-monitor sa"},
    {"label": "MONITORING", "pattern": "tracking ng"},
    {"label": "MONITORING", "pattern": "regular check-ups"},
    {"label": "MONITORING", "pattern": "weekly review"},
    {"label": "MONITORING", "pattern": "assessment ng progress"},
    {"label": "MONITORING", "pattern": "symptom diary"},
    {"label": "MONITORING", "pattern": "food diary"},
    {"label": "MONITORING", "pattern": "hydration chart"},
    {"label": "MONITORING", "pattern": "blood glucose readings"},
    {"label": "MONITORING", "pattern": "medication log"},
    {"label": "MONITORING", "pattern": "turning chart"},
    {"label": "MONITORING", "pattern": "weight monitoring"},
    
    # Dietary recommendations
    {"label": "DIET_RECOMMENDATION", "pattern": "frequent, small meals"},
    {"label": "DIET_RECOMMENDATION", "pattern": "balanced nutrition"},
    {"label": "DIET_RECOMMENDATION", "pattern": "adequate hydration"},
    {"label": "DIET_RECOMMENDATION", "pattern": "fluid intake"},
    {"label": "DIET_RECOMMENDATION", "pattern": "nutritionally dense"},
    {"label": "DIET_RECOMMENDATION", "pattern": "increased fiber"},
    {"label": "DIET_RECOMMENDATION", "pattern": "reduced salt"},
    {"label": "DIET_RECOMMENDATION", "pattern": "protein-rich"},
    {"label": "DIET_RECOMMENDATION", "pattern": "soft diet"},
    {"label": "DIET_RECOMMENDATION", "pattern": "thickened liquids"},
    {"label": "DIET_RECOMMENDATION", "pattern": "fortified foods"},
    {"label": "DIET_RECOMMENDATION", "pattern": "nutrient-dense foods"},
    {"label": "DIET_RECOMMENDATION", "pattern": "limiting caffeine"},
    {"label": "DIET_RECOMMENDATION", "pattern": "pag-iwas sa alcohol"},
    
    # Communication strategies
    {"label": "COMMUNICATION_STRATEGY", "pattern": "clear at simple instructions"},
    {"label": "COMMUNICATION_STRATEGY", "pattern": "picture-based communication"},
    {"label": "COMMUNICATION_STRATEGY", "pattern": "face-to-face communication"},
    {"label": "COMMUNICATION_STRATEGY", "pattern": "speaking slowly"},
    {"label": "COMMUNICATION_STRATEGY", "pattern": "simplified language"},
    {"label": "COMMUNICATION_STRATEGY", "pattern": "written reminders"},
    {"label": "COMMUNICATION_STRATEGY", "pattern": "visual cues"},
    {"label": "COMMUNICATION_STRATEGY", "pattern": "validation ng feelings"},
    {"label": "COMMUNICATION_STRATEGY", "pattern": "redirection technique"},
    {"label": "COMMUNICATION_STRATEGY", "pattern": "avoiding contradiction"},
    {"label": "COMMUNICATION_STRATEGY", "pattern": "gentle reassurance"},
    
    # Psychosocial interventions
    {"label": "PSYCHOSOCIAL", "pattern": "family meetings"},
    {"label": "PSYCHOSOCIAL", "pattern": "support groups"},
    {"label": "PSYCHOSOCIAL", "pattern": "social reintegration"},
    {"label": "PSYCHOSOCIAL", "pattern": "meaningful activities"},
    {"label": "PSYCHOSOCIAL", "pattern": "cognitive reframing"},
    {"label": "PSYCHOSOCIAL", "pattern": "memory book"},
    {"label": "PSYCHOSOCIAL", "pattern": "life review"},
    {"label": "PSYCHOSOCIAL", "pattern": "emotional support"},
    {"label": "PSYCHOSOCIAL", "pattern": "preserving dignity"},
    {"label": "PSYCHOSOCIAL", "pattern": "promoting independence"},
    {"label": "PSYCHOSOCIAL", "pattern": "caregiver training"},
    {"label": "PSYCHOSOCIAL", "pattern": "caregiver support"},
    {"label": "PSYCHOSOCIAL", "pattern": "family education"},
    
    # Timeframes and scheduling
    {"label": "TIMEFRAME", "pattern": "daily routine"},
    {"label": "TIMEFRAME", "pattern": "weekly schedule"},
    {"label": "TIMEFRAME", "pattern": "araw-araw"},
    {"label": "TIMEFRAME", "pattern": "immediate intervention"},
    {"label": "TIMEFRAME", "pattern": "short-term management"},
    {"label": "TIMEFRAME", "pattern": "long-term strategy"},
    {"label": "TIMEFRAME", "pattern": "habang hinihintay"},
    {"label": "TIMEFRAME", "pattern": "sa susunod na pagbisita"},
    {"label": "TIMEFRAME", "pattern": "regular na assessment"},
    {"label": "TIMEFRAME", "pattern": "gradually increasing"},
    {"label": "TIMEFRAME", "pattern": "unti-unting transition"},
    
    # Warning signs
    {"label": "WARNING_SIGN", "pattern": "warning signs"},
    {"label": "WARNING_SIGN", "pattern": "red flags"},
    {"label": "WARNING_SIGN", "pattern": "mga palatandaan ng"},
    {"label": "WARNING_SIGN", "pattern": "signs of infection"},
    {"label": "WARNING_SIGN", "pattern": "signs of dehydration"},
    {"label": "WARNING_SIGN", "pattern": "indications ng worsening"}
]
more_evaluation_patterns = [
    # General recommendation phrases
    {"label": "RECOMMENDATION", "pattern": "inirerekomenda"},
    {"label": "RECOMMENDATION", "pattern": "ipinapayo"},
    {"label": "RECOMMENDATION", "pattern": "maaaring"},
    {"label": "RECOMMENDATION", "pattern": "mainam"},
    {"label": "RECOMMENDATION", "pattern": "mas mainam"},
    {"label": "RECOMMENDATION", "pattern": "dapat"},
    {"label": "RECOMMENDATION", "pattern": "mahalaga"},
    {"label": "RECOMMENDATION", "pattern": "kritikal"},
    {"label": "RECOMMENDATION", "pattern": "napakahalaga"},
    {"label": "RECOMMENDATION", "pattern": "makakabuti"},
    {"label": "RECOMMENDATION", "pattern": "makatutulong"},
    
    # More specific evaluation entities
    {"label": "DIET_RECOMMENDATION", "pattern": "small meals"},
    {"label": "DIET_RECOMMENDATION", "pattern": "maliliit na meals"},
    {"label": "DIET_RECOMMENDATION", "pattern": "mababang sodium"},
    {"label": "DIET_RECOMMENDATION", "pattern": "mababang asukal"},
    {"label": "DIET_RECOMMENDATION", "pattern": "nutrient-dense"},
    {"label": "DIET_RECOMMENDATION", "pattern": "madaling matunaw"},
    {"label": "WARNING_SIGN", "pattern": "dry mouth"},
    {"label": "WARNING_SIGN", "pattern": "dehydration"},
    {"label": "WARNING_SIGN", "pattern": "increased dizziness"},
    {"label": "TREATMENT_METHOD", "pattern": "symptom diary"},
    {"label": "TREATMENT_METHOD", "pattern": "food diary"},
    {"label": "TREATMENT_METHOD", "pattern": "regular monitoring"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "registered dietitian"},
    {"label": "HEALTHCARE_REFERRAL", "pattern": "primary care physician"},
] 
additional_medical_patterns = [
    # Filipino-specific health descriptions
    {"label": "SYMPTOM", "pattern": "nanginginig"},
    {"label": "SYMPTOM", "pattern": "nananakit"},
    {"label": "SYMPTOM", "pattern": "namimitig"},
    {"label": "SYMPTOM", "pattern": "mabigat ang pakiramdam"},
    {"label": "SYMPTOM", "pattern": "maputla"},
    {"label": "SYMPTOM", "pattern": "maputla ang balat"},
    {"label": "SYMPTOM", "pattern": "malamig ang kamay"},
    {"label": "SYMPTOM", "pattern": "mabilis ang tibok ng puso"},
    {"label": "SYMPTOM", "pattern": "mababaw ang paghinga"},
    {"label": "SYMPTOM", "pattern": "masakit ang mga kasukasuan"},
    {"label": "SYMPTOM", "pattern": "nananakit ang likod"},
    {"label": "SYMPTOM", "pattern": "hinahabol ang paghinga"},
    {"label": "SYMPTOM", "pattern": "walang ganang kumain"},
    {"label": "SYMPTOM", "pattern": "nangangayayat"},
    {"label": "SYMPTOM", "pattern": "kawalan ng ganang kumain"},
    {"label": "SYMPTOM", "pattern": "nahihirapang matulog"},
    {"label": "SYMPTOM", "pattern": "nahihirapang huminga"},
    {"label": "SYMPTOM", "pattern": "hirap sa pagnguya"},
    {"label": "SYMPTOM", "pattern": "hirap sa paglunok"},
    {"label": "SYMPTOM", "pattern": "walang lakas"},
    {"label": "SYMPTOM", "pattern": "nawawalang ng balanse"},
    {"label": "SYMPTOM", "pattern": "madaling mapagod"},
    {"label": "SYMPTOM", "pattern": "pagpapawis sa gabi"},
    {"label": "SYMPTOM", "pattern": "mahapdi ang pag-ihi"},
    {"label": "SYMPTOM", "pattern": "madalas na pag-ihi"},
    {"label": "SYMPTOM", "pattern": "nahihirapang tumayo"},
    {"label": "SYMPTOM", "pattern": "pamamanas"},
    {"label": "SYMPTOM", "pattern": "pamamaga"},
    
    # Cognitive symptoms - more specific patterns
    {"label": "COGNITIVE", "pattern": "nakakalimot ng mga bagay"},
    {"label": "COGNITIVE", "pattern": "hindi matandaan"},
    {"label": "COGNITIVE", "pattern": "nakakalimutan ang pangalan"},
    {"label": "COGNITIVE", "pattern": "nalilito sa araw"},
    {"label": "COGNITIVE", "pattern": "nalilito sa lugar"},
    {"label": "COGNITIVE", "pattern": "nalilito sa mga mukha"},
    {"label": "COGNITIVE", "pattern": "nahihirapan magbigay ng desisyon"},
    {"label": "COGNITIVE", "pattern": "paulit-ulit na tanong"},
    {"label": "COGNITIVE", "pattern": "paulit-ulit na kuwento"},
    {"label": "COGNITIVE", "pattern": "naghahalo ang mga ideya"},
    {"label": "COGNITIVE", "pattern": "hindi makasunod sa usapan"},
    {"label": "COGNITIVE", "pattern": "nakakalimutan ang mga kaganapan"},
    {"label": "COGNITIVE", "pattern": "hindi matandaan kung saan inilagay"},
    {"label": "COGNITIVE", "pattern": "inaakala na ninakaw ang gamit"},
    {"label": "COGNITIVE", "pattern": "pagbabago ng pag-iisip"},
    {"label": "COGNITIVE", "pattern": "pagkawala ng kakayahang magdesisyon"},
    
    # Elder-specific medical conditions
    {"label": "DISEASE", "pattern": "mabagal na paggaling ng sugat"},
    {"label": "DISEASE", "pattern": "bed sore"},
    {"label": "DISEASE", "pattern": "pressure ulcer"},
    {"label": "DISEASE", "pattern": "presyon sa balat"},
    {"label": "DISEASE", "pattern": "pagbaba ng cognitive function"},
    {"label": "DISEASE", "pattern": "paghina ng pag-iisip"},
    {"label": "DISEASE", "pattern": "sundowning"},
    {"label": "DISEASE", "pattern": "pagkabalisa sa gabi"},
    {"label": "DISEASE", "pattern": "vascular dementia"},
    {"label": "DISEASE", "pattern": "Parkinson's"},
    {"label": "DISEASE", "pattern": "mild cognitive impairment"},
    {"label": "DISEASE", "pattern": "frontotemporal dementia"},
    {"label": "DISEASE", "pattern": "Lewy body dementia"},
    {"label": "DISEASE", "pattern": "polypharmacy"},
    {"label": "DISEASE", "pattern": "sarcopenia"},
    {"label": "DISEASE", "pattern": "frailty syndrome"},
    {"label": "DISEASE", "pattern": "presbycusis"},
    {"label": "DISEASE", "pattern": "pagkawala ng pandinig"},
    {"label": "DISEASE", "pattern": "pagbaba ng paningin"},
    {"label": "DISEASE", "pattern": "macular degeneration"},
    {"label": "DISEASE", "pattern": "pagkawala ng balanse"},
    
    # Functional assessment terms
    {"label": "ADL", "pattern": "pag-aalaga sa sarili"},
    {"label": "ADL", "pattern": "independiyenteng pamumuhay"},
    {"label": "ADL", "pattern": "pag-aayos ng sarili"},
    {"label": "ADL", "pattern": "paghuhugas ng kamay at mukha"},
    {"label": "ADL", "pattern": "pagsusuklay"},
    {"label": "ADL", "pattern": "pag-aahit"},
    {"label": "ADL", "pattern": "paglalagay ng makeup"},
    {"label": "ADL", "pattern": "pagpunta sa banyo"},
    {"label": "ADL", "pattern": "paggamit ng toilet"},
    {"label": "ADL", "pattern": "paghuhugas ng ngipin"},
    {"label": "ADL", "pattern": "paglalaba ng sariling damit"},
    {"label": "ADL", "pattern": "paglilinis ng bahay"},
    {"label": "ADL", "pattern": "paggawa ng grocery"},
    {"label": "ADL", "pattern": "pagluluto ng pagkain"},
    {"label": "ADL", "pattern": "pag-inom ng gamot"},
    {"label": "ADL", "pattern": "paggamit ng telepono"},
    {"label": "ADL", "pattern": "pamamahala ng pera"},
    {"label": "ADL", "pattern": "paglalakbay sa komunidad"},
    {"label": "ADL", "pattern": "pagmamaneho"},
]

# More comprehensive evaluation patterns
additional_evaluation_patterns = [
    {"label": "RECOMMENDATION", "pattern": "kinakailangang bantayan"},
    {"label": "RECOMMENDATION", "pattern": "dapat regular na subaybayan"},
    {"label": "RECOMMENDATION", "pattern": "mabisang paraan"},
    {"label": "RECOMMENDATION", "pattern": "epektibong lunas"},
    {"label": "RECOMMENDATION", "pattern": "nararapat isagawa"},
    {"label": "RECOMMENDATION", "pattern": "pinakamainam na gawin"},
    {"label": "RECOMMENDATION", "pattern": "maaaring makatulong"},
    {"label": "RECOMMENDATION", "pattern": "mainam na ipagpatuloy"},
    {"label": "RECOMMENDATION", "pattern": "lubos na inirerekomenda"},
    {"label": "RECOMMENDATION", "pattern": "pinakaangkop na hakbang"},
    {"label": "RECOMMENDATION", "pattern": "makabubuting gawin"},
    
    # Nuanced care recommendations
    {"label": "TREATMENT_METHOD", "pattern": "paunti-unting pag-eehersisyo"},
    {"label": "TREATMENT_METHOD", "pattern": "mahinang ehersisyo"},
    {"label": "TREATMENT_METHOD", "pattern": "balance training"},
    {"label": "TREATMENT_METHOD", "pattern": "pagsasanay sa balanse"},
    {"label": "TREATMENT_METHOD", "pattern": "kontrol ng kirot"},
    {"label": "TREATMENT_METHOD", "pattern": "pamamahala ng sakit"},
    {"label": "TREATMENT_METHOD", "pattern": "pagiwas sa pagkahulog"},
    {"label": "TREATMENT_METHOD", "pattern": "fall prevention"},
    {"label": "TREATMENT_METHOD", "pattern": "memory exercises"},
    {"label": "TREATMENT_METHOD", "pattern": "pagsasanay ng memorya"},
    {"label": "TREATMENT_METHOD", "pattern": "malnutrition screening"},
    {"label": "TREATMENT_METHOD", "pattern": "pagtatasa ng nutrisyon"},
    {"label": "TREATMENT_METHOD", "pattern": "pagpapalakas ng kalamnan"},
    {"label": "TREATMENT_METHOD", "pattern": "pagsasanay para sa kahinaan"},
    {"label": "TREATMENT_METHOD", "pattern": "adaptive strategies"},
    {"label": "TREATMENT_METHOD", "pattern": "cognitive stimulation"},
    {"label": "TREATMENT_METHOD", "pattern": "pagpapasigla ng isipan"},
    {"label": "TREATMENT_METHOD", "pattern": "suporta sa pagkain"},
    
    # More specific warnings and monitoring
    {"label": "WARNING_SIGN", "pattern": "mabilis na pagbaba ng timbang"},
    {"label": "WARNING_SIGN", "pattern": "madalas na pagkahulog"},
    {"label": "WARNING_SIGN", "pattern": "matinding pagkahilo"},
    {"label": "WARNING_SIGN", "pattern": "hindi maipaliwanag na pasa"},
    {"label": "WARNING_SIGN", "pattern": "madalas na impeksyon"},
    {"label": "WARNING_SIGN", "pattern": "pagbabago ng mental status"},
    {"label": "WARNING_SIGN", "pattern": "mabilis na pagsama ng kalagayan"},
    {"label": "WARNING_SIGN", "pattern": "kahirapan sa paglunok"},
    {"label": "WARNING_SIGN", "pattern": "pagbaba ng interaksyon"},
    {"label": "WARNING_SIGN", "pattern": "pagtanggi sa pagkain"},
    {"label": "WARNING_SIGN", "pattern": "matinding pananakit"},
    
    # Filipino culture-specific healthcare concepts
    {"label": "CULTURAL", "pattern": "hilot"},
    {"label": "CULTURAL", "pattern": "albularyo"},
    {"label": "CULTURAL", "pattern": "halamang gamot"},
    {"label": "CULTURAL", "pattern": "kulam"},
    {"label": "CULTURAL", "pattern": "usog"},
    {"label": "CULTURAL", "pattern": "pasma"},
    {"label": "CULTURAL", "pattern": "init sa katawan"},
    {"label": "CULTURAL", "pattern": "lamig sa katawan"},
    {"label": "CULTURAL", "pattern": "pilay"},
    {"label": "CULTURAL", "pattern": "bughat"},
    {"label": "CULTURAL", "pattern": "ba-i"},
    {"label": "CULTURAL", "pattern": "susto"},
    {"label": "CULTURAL", "pattern": "hilo sa hangin"},
    
    # Family care concepts
    {"label": "FAMILY_CARE", "pattern": "tagapag-alaga sa pamilya"},
    {"label": "FAMILY_CARE", "pattern": "pamilyang nag-aalaga"},
    {"label": "FAMILY_CARE", "pattern": "tulong ng pamilya"},
    {"label": "FAMILY_CARE", "pattern": "suporta ng mga anak"},
    {"label": "FAMILY_CARE", "pattern": "pamilyang sumusuporta"},
    {"label": "FAMILY_CARE", "pattern": "pagod sa pag-aalaga"},
    {"label": "FAMILY_CARE", "pattern": "caregiver stress"},
    {"label": "FAMILY_CARE", "pattern": "caregiver burden"},
    {"label": "FAMILY_CARE", "pattern": "pagpapahinga ng tagapag-alaga"},
    {"label": "FAMILY_CARE", "pattern": "respite care"},
    {"label": "FAMILY_CARE", "pattern": "multi-generation na pag-aalaga"},
    {"label": "FAMILY_CARE", "pattern": "shared caregiving"}
]

all_patterns = []
all_patterns.extend(medical_patterns)
all_patterns.extend(additional_patterns)
all_patterns.extend(evaluation_patterns)
all_patterns.extend(more_evaluation_patterns)
all_patterns.extend(additional_medical_patterns)
all_patterns.extend(additional_evaluation_patterns)

# Now check if entity_ruler exists and add it if needed
if "entity_ruler" not in nlp.pipe_names:
    print("Creating new entity_ruler")
    ruler = nlp.add_pipe("entity_ruler", before="ner")
else:
    print("Using existing entity_ruler")
    ruler = nlp.get_pipe("entity_ruler")

# Add patterns just once (after ruler is guaranteed to exist)
ruler.add_patterns(all_patterns)
print(f"Added {len(all_patterns)} patterns to entity ruler")

# Verify the patterns were added
if hasattr(ruler, "patterns") and len(ruler.patterns) > 0:
    print(f"Entity ruler has {len(ruler.patterns)} patterns")
else:
    print("WARNING: Entity ruler has no patterns")

def extract_key_elements(sentences, topic):
    """
    Extract key elements from a group of related sentences with enhanced capabilities.
    Uses comprehensive extraction logic while maintaining the original interface.
    """
    # Initialize with original structure for backward compatibility
    elements = {
        "subject": "",
        "condition": "",
        "impact": "",
        "recommendations": [],
        "interventions": [],
        "monitoring": []
    }
    
    # Enhanced extraction - combine full text for better pattern matching
    full_text = " ".join(sentences)
    doc = nlp(full_text)
    
    # Extract subject (usually the patient)
    for ent in doc.ents:
        if ent.label_ == "PER":
            elements["subject"] = ent.text
            break
    
    # Enhanced condition extraction (symptoms, diseases)
    condition_entities = []
    for ent in doc.ents:
        if ent.label_ == "DISEASE" and ent.text not in condition_entities:
            condition_entities.append(ent.text)
        elif ent.label_ == "SYMPTOM" and ent.text not in condition_entities:
            condition_entities.append(ent.text)
    
    # Set primary condition if found
    if condition_entities:
        elements["condition"] = condition_entities[0]
        
    # Enhanced impact extraction with more pattern matching
    impact_patterns = [
        r"(nangangailangan ng|kailangan ng|dapat|kritikal na) ([^.,:;]+)",
        r"(nagiging sanhi ng|nakakaapekto sa|nagdudulot ng) ([^.,:;]+)",
        r"(dahilan ng|naglalagay sa risk ng) ([^.,:;]+)"
    ]
    
    for pattern in impact_patterns:
        matches = re.finditer(pattern, full_text.lower())
        for match in matches:
            if match and len(match.groups()) >= 2:
                elements["impact"] = match.group(0)
                break
        if elements["impact"]:
            break
    
    # Enhanced recommendation extraction
    recommendation_patterns = [
        r"(inirerekomenda|iminumungkahi|pinapayuhan) (ko|kong|namin|naming)? (na|ang) ([^.,:;]+)",
        r"(dapat|kailangan|kinakailangan|mahalagang) (na)? ([^.,:;]+)",
        r"(mainam|mas mainam|makabubuti) (na)? ([^.,:;]+)"
    ]
    
    for pattern in recommendation_patterns:
        matches = re.finditer(pattern, full_text.lower())
        for match in matches:
            if match and len(match.groups()) >= 1:
                rec = match.group(0)
                if rec and rec not in elements["recommendations"]:
                    elements["recommendations"].append(rec)
    
    # Enhanced intervention extraction
    for ent in doc.ents:
        if ent.label_ in ["TREATMENT_METHOD", "TREATMENT", "EQUIPMENT", "MEDICATION"]:
            if ent.text not in elements["interventions"]:
                elements["interventions"].append(ent.text)
    
    # Enhanced monitoring extraction with more patterns
    monitoring_phrases = ["i-monitor", "obserbahan", "bantayan", "subaybayan", 
                         "regular na tsek", "check regularly", "track", "i-record"]
    
    for phrase in monitoring_phrases:
        if phrase in full_text.lower():
            # Find context around the monitoring phrase
            phrase_pos = full_text.lower().find(phrase)
            if phrase_pos >= 0:
                context_start = max(0, phrase_pos - 10)
                context_end = min(len(full_text), phrase_pos + len(phrase) + 40)
                context = full_text[context_start:context_end].strip()
                
                # Find sentence boundary
                end_pos = context.find('.')
                if end_pos > 0:
                    context = context[:end_pos+1]
                
                if context and context not in elements["monitoring"]:
                    elements["monitoring"].append(context)
    
    # Fallback extraction for cases with no matches
    if not any([elements["recommendations"], elements["interventions"], elements["monitoring"]]):
        # Process each sentence individually for basic extraction
        for sent in sentences:
            sent_doc = nlp(sent)
            
            # Basic recommendation keywords
            if any(word in sent.lower() for word in ["inirerekomenda", "dapat", "kailangan", "mainam"]):
                elements["recommendations"].append(sent)
                
            # Basic intervention keywords
            elif any(word in sent.lower() for word in ["gawin", "isagawa", "therapy", "treatment"]):
                elements["interventions"].append(sent)
                
            # Basic monitoring keywords  
            elif any(word in sent.lower() for word in ["obserbahan", "bantayan", "check", "monitor"]):
                elements["monitoring"].append(sent)
                
            # Verb-based extraction as last resort
            else:
                for token in sent_doc:
                    if token.pos_ == "VERB" and token.dep_ == "ROOT":
                        verb_phrase = " ".join([t.text for t in token.subtree])
                        if verb_phrase and len(verb_phrase) > 3:
                            if topic == "recommendation":
                                elements["recommendations"].append(verb_phrase)
                            elif topic == "intervention":
                                elements["interventions"].append(verb_phrase)
                            elif topic == "monitoring": 
                                elements["monitoring"].append(verb_phrase)
                            break
    
    return elements

def clean_and_normalize_text(text):
    """Clean and normalize text to improve processing quality"""
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

def split_into_sentences(text):
    """Split text into sentences with improved handling for Tagalog text"""
    # Clean and normalize the text first
    text = clean_and_normalize_text(text)
    
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

def extract_sections_improved(sentences, doc_type="assessment"):
    """Extract and categorize sections using advanced linguistic analysis and pattern matching."""
    print(f"Extracting sections for {doc_type}, {len(sentences)} sentences")
    
    # Process all sentences with Calamancy NLP first
    sentence_docs = [nlp(sent) for sent in sentences]
    
    # Define section keywords with expanded terms for better matching
    if doc_type.lower() == "assessment":
        section_keywords = {
            "mga_sintomas": [
                # General symptom terms
                "sakit", "sintomas", "hirap", "dumaranas", "nararamdaman", "nakakaranas", 
                "nagpapakita", "kondisyon", "diagnosed", "masakit", "sumasakit", "kirot",
                "nahihirapan", "problema sa", "nagkaroon ng", "nagdurusa sa",
                
                # Pain and discomfort
                "pain", "ache", "soreness", "tenderness", "discomfort", "burning",
                "stabbing", "throbbing", "radiating", "chronic pain", "acute pain",
                "neuropathic", "muscle pain", "joint pain", "abdominal pain",
                "chest pain", "back pain", "headache", "sakit ng ulo",
                
                # Neurological symptoms
                "dizziness", "vertigo", "lightheadedness", "fainting", "syncope",
                "seizure", "tremors", "shaking", "panginginig", "numbness", "tingling",
                "pamamanhid", "weakness", "paralysis", "incoordination",
                
                # Respiratory symptoms
                "cough", "ubo", "shortness of breath", "hirap huminga", "wheezing",
                "huni", "sputum", "plema", "hemoptysis", "dugo sa ubo", 
                "chest tightness", "kapos sa hininga"
            ],
            
            "kalagayan_pangkatawan": [
                # Basic physical terms
                "pisikal", "katawan", "lakas", "kahinaan", "balance", "koordinasyon",
                "physical", "stance", "posture", "tindig", "galaw", "coordination",
                
                # Body systems & functions
                "cardiovascular", "respiratory", "musculoskeletal", "heart", "puso", 
                "baga", "lungs", "atay", "liver", "bato", "kidney", "presyon", 
                "blood pressure", "weight", "timbang", "pulse", "rhythm", "pulso",
                "breathing", "paghinga", "oxygen", "muscle", "kalamnan", "joints",
                "neuromuscular", "respiratory", "digestion", "excretion", "circulation",
                
                # Physical measurements
                "BMI", "pulse ox", "vital signs", "blood pressure", "temperature",
                "height", "weight", "range of motion", "muscle strength", "endurance"
            ],
            
            "kalagayan_mental": [
                # Cognitive aspects
                "memorya", "nakalimutan", "nalilito", "naguguluhan", "pagkalito",
                "cognitive", "mental", "isip", "memory", "forgetful", "disorientation",
                "concentration", "disoriented", "confused", "hindi matandaan",
                "attention span", "awareness", "alertness", "orientation",
                "decision-making", "judgment", "reasoning", "comprehension",
                
                # Emotional aspects
                "emosyon", "emotion", "kalungkutan", "pagkabalisa", "depression", 
                "anxiety", "mood", "affect", "irritability", "agitation",
                "sadness", "hopelessness", "fear", "takot", "worry", "stress",
                "coping", "emotional state", "feelings", "damdamin"
            ],
            
            "aktibidad": [
                # Daily activities
                "gawain", "aktibidad", "activity", "araw-araw", "daily", "self-care",
                "routine", "schedule", "regular", "tasks", "chores", "responsibilities",
                
                # ADLs
                "pagligo", "bathing", "pagbibihis", "dressing", "pagkain", "feeding",
                "toileting", "hygiene", "grooming", "eating", "sleeping", "pagtulog",
                
                # Mobility
                "mobility", "paggalaw", "walking", "paglalakad", "trabaho", "work",
                "standing", "pagtayo", "bed mobility", "transfers", "wheelchair",
                "walker", "cane", "tungkod", "assistive device", "mobility aid",
                
                # IADLs
                "cooking", "cleaning", "shopping", "finances", "transportation",
                "medication management", "communication", "phone", "computer"
            ],
            
            "kalagayan_social": [
                # Relationships
                "pamilya", "asawa", "anak", "social", "pakikisalamuha", "kaibigan", 
                "friend", "spouse", "family", "relatives", "kamag-anak", "apo",
                
                # Social environment
                "kapitbahay", "komunidad", "simbahan", "church", "pakikitungo", 
                "ugnayan", "community", "neighborhood", "support system", "network",
                "social engagement", "participation", "interaction", "isolation",
                "loneliness", "withdrawal"
            ]
        }
    else:  # Evaluation document sections
        section_keywords = {
            "pangunahing_rekomendasyon": [
                # Direct recommendation terms
                "inirerekomenda", "iminumungkahi", "pinapayuhan", "dapat", "kailangan", 
                "mahalagang", "ipinapayo", "nirerekomenda", "binigyang-diin", "iminungkahi", 
                "kinakailangan", "mabuting", "mainam", "mas mainam", "sulit",
                
                # Priority language
                "kritikal", "mahalaga", "essential", "urgent", "agarang",
                "high priority", "immediate", "necessary", "importante",
                "crucial", "vital", "indispensable", "non-negotiable",
                
                # Professional advice
                "ayon sa eksperto", "batay sa research", "evidence shows", 
                "clinical guidelines", "standard practice", "best practice"
            ],
            
            "pangangalaga": [
                # Care management
                "pag-iwas", "monitor", "obserbahan", "bantayan", "management", 
                "symptom management", "preventive care", "care techniques", 
                "maintenance", "prevention", "alaga", "supervision", 
                "pagsubaybay", "monitoring", "observation",
                
                # Specific care procedures
                "wound care", "skin care", "pressure relief", "pain management",
                "pangangalaga", "pagbabantay", "pagbibigay ng gamot", "positioning",
                "comfort measures", "palliative care", "supportive care"
            ],
            
            "mga_hakbang": [
                # Action verbs
                "simulan", "gawin", "ipatupad", "isagawa", "sundin", "interventions", 
                "measures", "implement", "execute", "perform", "conduct", "carry out",
                "undertake", "administer", "provide", "apply", "deliver", "follow",
                
                # Treatment terms
                "treatment", "therapy", "program", "regimen", "protocol", "procedure",
                "exercises", "techniques", "methods", "approaches", "strategies",
                "interventions", "rehabilitation", "restoration", "recovery"
            ],
            
            "pagbabago_sa_pamumuhay": [
                # Lifestyle components
                "diet", "nutrition", "pagkain", "ehersisyo", "physical activity", 
                "lifestyle", "routine", "habits", "daily schedule", "araw-araw",
                "sleep", "stress management", "work-life balance", "recreation",
                
                # Modification terms
                "adjustment", "modification", "change", "shift", "transition",
                "adaptation", "pagbabago", "pag-adjust", "pagsasaayos", "improvement",
                "enhancement", "reduction", "increase", "moderation"
            ]
        }
    
    # Initialize scoring for each sentence-section pair
    sentence_scores = {}
    for i, (sent, doc) in enumerate(zip(sentences, sentence_docs)):
        sent_lower = sent.lower()
        scores = {}
        
        # Score each section for this sentence
        for section, keywords in section_keywords.items():
            # Base score from keyword matches
            keyword_matches = sum(1 for keyword in keywords if keyword in sent_lower)
            
            # Entity-based matching with stronger weighting
            entity_score = 0
            for ent in doc.ents:
                # Map entity types to relevant sections
                if section == "mga_sintomas" and ent.label_ in ["SYMPTOM", "DISEASE"]:
                    entity_score += 2
                elif section == "kalagayan_pangkatawan" and ent.label_ in ["BODY_PART", "MEASUREMENT"]:
                    entity_score += 2
                elif section == "kalagayan_mental" and ent.label_ in ["COGNITIVE", "EMOTION"]:
                    entity_score += 2
                elif section == "aktibidad" and ent.label_ in ["ADL", "SAFETY"]:
                    entity_score += 2
                elif section == "kalagayan_social" and ent.label_ in ["SOCIAL_REL", "SOCIAL_ACT"]:
                    entity_score += 2
                elif section == "pangunahing_rekomendasyon" and ent.label_ in ["RECOMMENDATION", "HEALTHCARE_REFERRAL"]:
                    entity_score += 2
                elif section == "pangangalaga" and ent.label_ in ["TREATMENT", "MONITORING"]:
                    entity_score += 2
                elif section == "mga_hakbang" and ent.label_ in ["TREATMENT_METHOD", "EQUIPMENT"]:
                    entity_score += 2
                elif section == "pagbabago_sa_pamumuhay" and ent.label_ in ["DIET_RECOMMENDATION", "FOOD"]:
                    entity_score += 2
            
            # Position-based scoring - first sentences often indicate topic
            position_score = 1.5 if i == 0 else (0.5 if i == len(sentences)-1 else 0)
            
            # Calculate total score
            total_score = keyword_matches + entity_score + position_score
            if total_score > 0:
                scores[section] = total_score
                
        sentence_scores[i] = (sent, scores)
    
    # Assign sentences to sections based on scores
    result = {}
    assigned_sentences = set()
    
    # First pass: Assign sentences with clear high scores
    for section in section_keywords.keys():
        result[section] = []
        
        # Find sentences with high scores for this section
        section_candidates = []
        for i, (sent, scores) in sentence_scores.items():
            if i in assigned_sentences:
                continue
                
            if section in scores and scores[section] >= 2:  # Strong signal
                section_candidates.append((i, sent, scores[section]))
        
        # Sort by score and add top sentences
        for i, sent, score in sorted(section_candidates, key=lambda x: x[2], reverse=True):
            result[section].append(sent)
            assigned_sentences.add(i)
    
    # Second pass: Assign remaining sentences to their best-matching section
    for i, (sent, scores) in sentence_scores.items():
        if i in assigned_sentences:
            continue
            
        # Find best section for this sentence
        if scores:
            best_section = max(scores.items(), key=lambda x: x[1])[0]
            result[best_section].append(sent)
            assigned_sentences.add(i)
        else:
            # Default assignment for sentences with no matches
            default_section = "mga_sintomas" if doc_type.lower() == "assessment" else "pangunahing_rekomendasyon"
            result[default_section].append(sent)
            assigned_sentences.add(i)
    
    # Ensure at least one section exists with content
    if all(not sents for sents in result.values()) and sentences:
        # Create fallback section with first sentence
        if doc_type.lower() == "assessment":
            result["mga_sintomas"] = [sentences[0]]
        else:
            result["pangunahing_rekomendasyon"] = [sentences[0]]
    
    # Ensure we have the key sections for assessment documents
    if doc_type.lower() == "assessment":
        required_sections = ["mga_sintomas", "kalagayan_pangkatawan", "aktibidad"]
        for section in required_sections:
            if section not in result or not result[section]:
                # Find a sentence to repurpose if possible, or use first sentence
                for other_section, sents in result.items():
                    if len(sents) > 1:  # Can spare one sentence
                        result[section] = [sents[0]]
                        break
                else:
                    # No spare sentences, use first sentence
                    result[section] = [sentences[0] if sentences else ""]
    
    # Convert lists to strings
    return {section: " ".join(sents) for section, sents in result.items() if sents}

def extract_sections_for_evaluation(sentences):
    """Extract sections specific to evaluation documents using pattern-based approach."""
    print(f"Extracting evaluation-specific sections from {len(sentences)} sentences")
    
    # Process sentences with NLP
    sentence_docs = [nlp(sent) for sent in sentences]
    
    # Initialize sections
    sections = {
        "pangunahing_rekomendasyon": [],
        "mga_hakbang": [],
        "pangangalaga": [],
        "pagbabago_sa_pamumuhay": []
    }
    
    # Strong signal patterns for each section
    section_patterns = {
        "pangunahing_rekomendasyon": [
            r'inirerekomenda(ng)? (ko|kong|namin|naming) (na|ang)',
            r'iminumungkahi(ng)? (ko|kong|namin|naming) (na|ang)',
            r'pinapayuhan (ko|kong|namin|naming) (na|ang)',
            r'(una sa lahat|bilang pangunahing hakbang)',
            r'(dapat|kailangan|kinakailangan|mahalagang) (na )?'
        ],
        
        "mga_hakbang": [
            r'(simulan|gawin|ipatupad|isagawa) ang',
            r'(susunod na hakbang|sa|mga|bilang) (hakbang|steps|interventions)',
            r'(dapat|kailangang) (din|rin) (na )?',
            r'(pangalawang|pangatlo|kasunod na) hakbang'
        ],
        
        "pangangalaga": [
            r'(para sa|upang|sa) (pangangalaga|pag-iwas|pag-aalaga)',
            r'(i-monitor|obserbahan|bantayan|subaybayan)',
            r'(sa pang-araw-araw na pangangalaga|daily care)',
            r'(sa bahay|home care|home management)',
            r'(kapag|kung|sa) (nagkaroon|nagkakaroon)',
            r'(palaging|regular na|always|consistently)'
        ],
        
        "pagbabago_sa_pamumuhay": [
            r'(pagbabago sa|baguhin ang|adjustment sa) (pamumuhay|lifestyle)',
            r'(diet|nutrisyon|nutrition|pagkain)',
            r'(exercise|ehersisyo|physical activity)',
            r'(normal na routine|daily habits|araw-araw)',
            r'(long-term|pangmatagalang|sa hinaharap|future)'
        ]
    }
    
    # First pass: Match sentences to sections based on strong signals
    assigned_sentences = set()
    
    for section, patterns in section_patterns.items():
        for i, sent in enumerate(sentences):
            if i in assigned_sentences:
                continue
                
            sent_lower = sent.lower()
            for pattern in patterns:
                if re.search(pattern, sent_lower):
                    sections[section].append(sent)
                    assigned_sentences.add(i)
                    break
    
    # Second pass: Analyze entities for remaining sentences
    for i, (sent, doc) in enumerate(zip(sentences, sentence_docs)):
        if i in assigned_sentences:
            continue
            
        # Analyze entities
        recommendation_count = 0
        treatment_count = 0
        monitoring_count = 0
        lifestyle_count = 0
        
        for ent in doc.ents:
            if ent.label_ in ["RECOMMENDATION", "HEALTHCARE_REFERRAL"]:
                recommendation_count += 1
            elif ent.label_ in ["TREATMENT_METHOD", "TREATMENT", "EQUIPMENT"]:
                treatment_count += 1
            elif ent.label_ in ["MONITORING", "WARNING_SIGN"]:
                monitoring_count += 1
            elif ent.label_ in ["DIET_RECOMMENDATION", "FOOD"]:
                lifestyle_count += 1
        
        # Assign based on entity counts
        max_count = max(recommendation_count, treatment_count, monitoring_count, lifestyle_count)
        
        if max_count > 0:
            if max_count == recommendation_count:
                sections["pangunahing_rekomendasyon"].append(sent)
            elif max_count == treatment_count:
                sections["mga_hakbang"].append(sent)
            elif max_count == monitoring_count:
                sections["pangangalaga"].append(sent)
            elif max_count == lifestyle_count:
                sections["pagbabago_sa_pamumuhay"].append(sent)
            
            assigned_sentences.add(i)
    
    # Third pass: Default assignment of remaining sentences
    for i, sent in enumerate(sentences):
        if i in assigned_sentences:
            continue
            
        # Default to "pangunahing_rekomendasyon" for first sentence
        if i == 0:
            sections["pangunahing_rekomendasyon"].append(sent)
        # For remaining sentences, distribute evenly across sections that need content
        else:
            # Find the section with the least content
            empty_sections = [s for s, sentences in sections.items() if not sentences]
            if empty_sections:
                sections[empty_sections[0]].append(sent)
            else:
                min_section = min(sections.items(), key=lambda x: len(x[1]))[0]
                sections[min_section].append(sent)
    
    # Convert lists to strings
    return {section: " ".join(sents) for section, sents in sections.items() if sents}

def extract_main_subject(doc):
    """Extract the main subject (person) from the document."""
    # Look for person entities
    for ent in doc.ents:
        if ent.label_ == "PER":
            return ent.text
    
    # Default subjects if no person entity found
    return "Ang pasyente"

def create_enhanced_multi_section_summary(doc, sections, doc_type="assessment"):
    """Create a comprehensive and cohesive summary by synthesizing information across sections."""
    if not sections:
        return "Walang sapat na impormasyon para sa buod."
    
    # Extract main subject (patient)
    subject = extract_main_subject(doc)
    
    # Apply proper Tagalog name formatting with "si" for names
    if subject and subject not in ["Ang pasyente", "Ang kliyente"]:
        if not subject.lower().startswith("si "):
            subject = f"Si {subject}"
    
    # Initialize collection of extracted information for synthesis
    info = {
        # Medical condition/symptoms
        "conditions": [],
        "symptoms": [],
        "severity_terms": [],
        "frequency_terms": [],
        "measurements": [],
        
        # Physical status
        "physical_limitations": [],
        "body_parts": [],
        
        # Activities
        "activities": [],
        "activity_limitations": [],
        
        # Mental state
        "cognitive_states": [],
        "emotional_states": [],
        
        # Social aspects
        "social_relations": [],
        "support_systems": [],
        
        # Recommendations (for evaluation type)
        "recommendations": [],
        "treatments": [],
        "diet_changes": [],
        "monitoring_needs": [],
        "healthcare_referrals": [],
        
        # Other important elements
        "timeframes": [],
        "warnings": []
    }
    
    # Process each section with improved extraction using extract_structured_elements
    for section_name, section_text in sections.items():
        if not section_text.strip():
            continue
        
        # Use the structured elements extraction for more comprehensive data
        elements = extract_structured_elements(section_text, section_name)
        
        # Map extracted elements to our summary info structure
        if elements["conditions"]:
            info["conditions"].extend([c for c in elements["conditions"] if c not in info["conditions"]])
        
        if elements["symptoms"]:
            info["symptoms"].extend([s for s in elements["symptoms"] if s not in info["symptoms"]])
            
        if elements["severity"]:
            info["severity_terms"].extend([s for s in elements["severity"] if s not in info["severity_terms"]])
            
        if elements["vital_signs"]:
            info["measurements"].extend([m for m in elements["vital_signs"] if m not in info["measurements"]])
            
        if elements["limitations"]:
            info["physical_limitations"].extend([l for l in elements["limitations"] if l not in info["physical_limitations"]])
            
        if elements["body_parts"]:
            info["body_parts"].extend([b for b in elements["body_parts"] if b not in info["body_parts"]])
            
        if elements["activities"]:
            info["activities"].extend([a for a in elements["activities"] if a not in info["activities"]])
            
        if elements["activity_limitations"]:
            info["activity_limitations"].extend([a for a in elements["activity_limitations"] if a not in info["activity_limitations"]])
            
        if elements["cognitive_status"]:
            info["cognitive_states"].extend([c for c in elements["cognitive_status"] if c not in info["cognitive_states"]])
            
        if elements["emotional_state"]:
            info["emotional_states"].extend([e for e in elements["emotional_state"] if e not in info["emotional_states"]])
            
        if elements["social_support"]:
            info["social_relations"].extend([s for s in elements["social_support"] if s not in info["social_relations"]])
            
        if elements["recommendations"]:
            info["recommendations"].extend([r for r in elements["recommendations"] if r not in info["recommendations"]])
            
        if elements["treatments"]:
            info["treatments"].extend([t for t in elements["treatments"] if t not in info["treatments"]])
            
        if elements["monitoring_plans"]:
            info["monitoring_needs"].extend([m for m in elements["monitoring_plans"] if m not in info["monitoring_needs"]])
            
        if elements["healthcare_referrals"]:
            info["healthcare_referrals"].extend([h for h in elements["healthcare_referrals"] if h not in info["healthcare_referrals"]])
        
        # Extract severity and frequency terms (for symptoms)
        severity_terms = ["matindi", "malubha", "severe", "moderate", "mild", "banayad", 
                        "grabeng", "lubhang", "napakasidhi", "katamtamang", "bahagyang"]
        
        for term in severity_terms:
            if term in section_text.lower() and term not in info["severity_terms"]:
                info["severity_terms"].append(term)
                
        frequency_terms = ["araw-araw", "madalas", "paminsan-minsan", "linggu-linggo", 
                        "paulit-ulit", "regular", "palaging", "bihira", "significant",
                        "parati", "pana-panahon", "persistent", "occasional"]
                
        for term in frequency_terms:
            if term in section_text.lower() and term not in info["frequency_terms"]:
                info["frequency_terms"].append(term)
        
        # Extract additional patterns not captured by entity recognition
        
        # Limitation patterns
        limitation_patterns = [
            r"(nahihirapan|hirap) sa ([^.;,]+)",
            r"(limitado|limited) ang ([^.;,]+)",
            r"(problema|issue) sa ([^.;,]+)",
            r"(hindi|di) (makapag|magawang) ([^.;,]+)"
        ]
        
        for pattern in limitation_patterns:
            matches = re.finditer(pattern, section_text.lower())
            for match in matches:
                if len(match.groups()) >= 2:
                    limitation = match.group(2).strip()
                    if limitation and limitation not in info["physical_limitations"] and len(limitation) > 3:
                        info["physical_limitations"].append(limitation)
        
        # Support system patterns
        support_patterns = [
            r"(support|tulong|suporta) (mula sa|ng|galing) ([^.;,]+)",
            r"(tinutulungan|sinusuportahan|inaalagaan) (ng|ni) ([^.;,]+)",
            r"kasama ang (kanyang|kaniyang) ([^.;,]+)"
        ]
        
        for pattern in support_patterns:
            matches = re.finditer(pattern, section_text.lower())
            for match in matches:
                if len(match.groups()) >= 2:
                    supporter = match.groupdict().get(match.lastindex, "").strip()
                    if supporter and supporter not in info["support_systems"] and len(supporter) > 3:
                        info["support_systems"].append(supporter)
    
    # Now build synthesized sentences based on document type
    summary_sentences = []
    
    # For assessment documents
    if doc_type.lower() == "assessment":
        # 1. First sentence: Main condition and symptoms
        if info["conditions"] or info["symptoms"]:
            conditions = info["conditions"][:2]  # Limit to top 2 conditions
            symptoms = info["symptoms"][:2]      # Limit to top 2 symptoms
            
            # Combine conditions and symptoms with proper formatting
            medical_issues = []
            if conditions:
                medical_issues.extend(conditions)
            if symptoms and not any(s in " ".join(conditions) for s in symptoms):
                medical_issues.extend(symptoms)
                
            # Add severity/frequency if available
            descriptor = ""
            if info["severity_terms"]:
                descriptor = info["severity_terms"][0] + " "
            elif info["frequency_terms"]:
                descriptor = info["frequency_terms"][0] + " "
                
            # Create primary condition sentence
            if medical_issues:
                # Format nicely with proper conjunctions for Tagalog
                if len(medical_issues) == 1:
                    condition_text = f"{descriptor}{medical_issues[0]}"
                elif len(medical_issues) == 2:
                    condition_text = f"{descriptor}{medical_issues[0]} at {medical_issues[1]}"
                else:
                    condition_text = f"{descriptor}{', '.join(medical_issues[:-1])}, at {medical_issues[-1]}"
                    
                summary_sentences.append(f"{subject} ay nagpapakita ng {condition_text}.")
        
        # 2. Second sentence: Physical measurements or status
        if info["measurements"] or info["physical_limitations"]:
            if info["measurements"]:
                measurement = info["measurements"][0]
                # Check if it contains numbers already
                if re.search(r'\d', measurement):
                    summary_sentences.append(f"Ang mga sukat ay nagpapakita ng {measurement}.")
                else:
                    summary_sentences.append(f"Ang mga sukat ay nagpapakita ng mataas na {measurement}.")
            elif info["physical_limitations"]:
                limitation = info["physical_limitations"][0]
                summary_sentences.append(f"Mayroon siyang limitasyon sa {limitation}.")
        
        # 3. Third sentence: Activity limitations or challenges
        if info["activities"] or info["activity_limitations"]:
            activities = info["activities"][:3]
            
            if activities:
                # Format with proper Tagalog conjunctions
                if len(activities) == 1:
                    act_text = activities[0]
                elif len(activities) == 2:
                    act_text = f"{activities[0]} at {activities[1]}"
                else:
                    act_text = f"{', '.join(activities[:-1])}, at {activities[-1]}"
                    
                summary_sentences.append(f"{subject} ay nahihirapan sa mga aktibidad na may kaugnayan sa {act_text}.")
        
        # 4. Fourth sentence: Social support or mental state
        if info["social_relations"]:
            relation = info["social_relations"][0]
            summary_sentences.append(f"May suporta {subject} mula sa kanyang {relation}.")
        elif info["cognitive_states"] or info["emotional_states"]:
            states = info["cognitive_states"] + info["emotional_states"]
            if states:
                summary_sentences.append(f"Ang kanyang mental na kalagayan ay nagpapakita ng {states[0]}.")
    
    # For evaluation documents
    else:
        # 1. First sentence: Primary recommendation or referral
        if info["healthcare_referrals"]:
            referral = info["healthcare_referrals"][0]
            summary_sentences.append(f"Inirerekomenda ang agarang pagkonsulta sa {referral} para sa komprehensibong pagsusuri.")
        elif info["recommendations"]:
            recommendation = next((r for r in info["recommendations"] if len(r) > 5), None)
            if recommendation:
                # Ensure we're not copying a full sentence but creating a synthesized one
                summary_sentences.append(f"Inirerekomenda ang pagkakaroon ng {recommendation}.")
        
        # 2. Second sentence: Treatment approach
        if info["treatments"]:
            treatments = info["treatments"][:2]
            if treatments:
                if len(treatments) == 1:
                    treatment_text = treatments[0]
                else:
                    treatment_text = f"{treatments[0]} at {treatments[1]}"
                
                summary_sentences.append(f"Dapat isagawa ang {treatment_text} para sa epektibong paggamot.")
        
        # 3. Third sentence: Diet or lifestyle changes
        if info["diet_changes"]:
            diet_changes = info["diet_changes"][:2]
            if diet_changes:
                summary_sentences.append(f"Para sa diet, inirerekomenda ang {diet_changes[0]}.")
        
        # 4. Fourth sentence: Monitoring needs
        if info["monitoring_needs"] or info["warnings"]:
            elements = info["monitoring_needs"] + info["warnings"]
            if elements:
                summary_sentences.append(f"Mahalagang subaybayan ang {elements[0]} para matiyak ang kaligtasan.")
    
    # Handle case where we couldn't generate enough sentences
    if len(summary_sentences) < 2:
        # Add fallback sentences based on document type
        if doc_type.lower() == "assessment":
            if not any("nagpapakita" in s for s in summary_sentences):
                primary_issue = next((c for c in info["conditions"]), "kondisyon")
                summary_sentences.append(f"{subject} ay nagpapakita ng {primary_issue}.")
            
            if len(summary_sentences) < 2:
                summary_sentences.append("Kailangan ng karagdagang pagsusuri para sa komprehensibong assessment.")
        else:
            if not any("inirerekomenda" in s for s in summary_sentences):
                summary_sentences.append("Inirerekomenda ang komprehensibong medikal na pagsusuri para sa wastong diagnosis at paggamot.")
            
            if len(summary_sentences) < 2:
                summary_sentences.append("Mahalagang sundin ang mga gabay sa pangangalaga para sa mas mabilis na paggaling.")
    
    # Apply transitions between sentences for better flow
    final_summary = summary_sentences[0]
    
    # More varied Tagalog transitions
    transitions = [
        "Bukod dito, ", "Gayundin, ", "Dagdag pa rito, ", 
        "Karagdagan sa nabanggit, ", "Kasama rito, ", "Sa kabilang banda, ",
        "Samantala, ", "Higit pa, "
    ]
    
    for i in range(1, len(summary_sentences)):
        transition = transitions[min(i-1, len(transitions)-1)]
        next_sentence = summary_sentences[i]
        
        # Ensure proper capitalization after transition
        if next_sentence[0].isupper():
            next_sentence = next_sentence[0].lower() + next_sentence[1:]
        
        final_summary += f" {transition}{next_sentence}"
    
    return final_summary

def is_sentence_similar_to_original(new_sentence, original_sentences, threshold=0.7):
    """
    Check if a generated sentence is too similar to any sentence in the original text.
    Returns True if similar, False if not.
    """
    if not new_sentence or not original_sentences:
        return False
        
    # Clean and normalize for comparison
    new_clean = ' '.join(new_sentence.lower().split())
    
    for orig in original_sentences:
        orig_clean = ' '.join(orig.lower().split())
        
        # Skip very short sentences
        if len(orig_clean) < 15 or len(new_clean) < 15:
            continue
            
        # Calculate similarity ratio
        similarity = difflib.SequenceMatcher(None, new_clean, orig_clean).ratio()
        
        # Check if exceeds threshold
        if similarity > threshold:
            return True
            
        # Also check if sentence is substantially contained within original
        if (new_clean in orig_clean and len(new_clean) > len(orig_clean) * 0.6) or \
           (orig_clean in new_clean and len(orig_clean) > len(new_clean) * 0.6):
            return True
    
    return False

def extract_structured_elements(text, section_type):
    """Extract detailed structured elements from text for better synthesis."""
    doc = nlp(text)
    elements = {
        # Subject-related
        "subject": None,      # Main person/patient
        
        # Symptom-related
        "symptoms": [],       # Symptoms described
        "conditions": [],     # Medical conditions
        "severity": [],       # Severity descriptors
        "frequency": [],      # Frequency terms
        "locations": [],      # Body parts/locations
        "duration": [],       # Duration terms
        
        # Physical status
        "vital_signs": [],    # Vital sign measurements
        "limitations": [],    # Physical limitations
        "body_parts": [],     # Body parts mentioned
        
        # Activity-related
        "activities": [],     # Activities mentioned
        "activity_limitations": [], # Limitations in activities
        
        # Mental/Emotional
        "cognitive_status": [], # Cognitive status descriptors
        "mental_state": [],     # Mental state descriptors
        "emotional_state": [],  # Emotional state terms
        
        # Care-related
        "treatments": [],     # Treatments mentioned
        "medications": [],    # Medications
        "dosages": [],        # Medication dosages
        "intervention_methods": [], # Intervention methods
        "recommendations": [], # Recommendations
        "monitoring_plans": [], # Monitoring approaches
        "healthcare_referrals": [], # Referrals to healthcare providers
        
        # Social
        "social_support": [], # Social support systems
        "caregivers": [],     # Caregiver information
        "living_conditions": [], # Living conditions
        
        # General
        "needs": [],          # Identified needs
        "verbs": [],          # Key action verbs
        "adjectives": [],     # Important descriptive adjectives
    }
    
    # First extract subject (main person)
    for ent in doc.ents:
        if ent.label_ == "PER" and not elements["subject"]:
            elements["subject"] = ent.text
            break
    
    # Extract entities into appropriate categories
    for ent in doc.ents:
        if ent.label_ == "DISEASE" and ent.text not in elements["conditions"]:
            elements["conditions"].append(ent.text)
        elif ent.label_ == "SYMPTOM" and ent.text not in elements["symptoms"]:
            elements["symptoms"].append(ent.text)
        elif ent.label_ == "BODY_PART" and ent.text not in elements["body_parts"]:
            elements["body_parts"].append(ent.text)
        elif ent.label_ == "MEASUREMENT" and ent.text not in elements["vital_signs"]:
            elements["vital_signs"].append(ent.text)
        elif ent.label_ == "ADL" and ent.text not in elements["activities"]:
            elements["activities"].append(ent.text)
        elif ent.label_ == "COGNITIVE" and ent.text not in elements["cognitive_status"]:
            elements["cognitive_status"].append(ent.text)
        elif ent.label_ == "EMOTION" and ent.text not in elements["emotional_state"]:
            elements["emotional_state"].append(ent.text)
        elif ent.label_ == "TREATMENT" and ent.text not in elements["treatments"]:
            elements["treatments"].append(ent.text)
        elif ent.label_ == "TREATMENT_METHOD" and ent.text not in elements["intervention_methods"]:
            elements["intervention_methods"].append(ent.text)
        elif ent.label_ == "RECOMMENDATION" and ent.text not in elements["recommendations"]:
            elements["recommendations"].append(ent.text)
        elif ent.label_ == "HEALTHCARE_REFERRAL" and ent.text not in elements["healthcare_referrals"]:
            elements["healthcare_referrals"].append(ent.text)
        elif ent.label_ == "MEDICATION" and ent.text not in elements["medications"]:
            elements["medications"].append(ent.text)
        elif ent.label_ == "MONITORING" and ent.text not in elements["monitoring_plans"]:
            elements["monitoring_plans"].append(ent.text)
        elif ent.label_ == "SOCIAL_REL" and ent.text not in elements["social_support"]:
            elements["social_support"].append(ent.text)
    
    # Extract severity descriptors
    severity_terms = ["matindi", "malubha", "severe", "moderate", "mild", "banayad", 
                    "grabeng", "lubhang", "significant", "napaka"]
    
    for term in severity_terms:
        pattern = r'\b' + re.escape(term) + r'\w*\b'
        for match in re.finditer(pattern, text.lower()):
            found_term = match.group(0)
            if found_term not in elements["severity"]:
                elements["severity"].append(found_term)
    
    # Extract frequency descriptors
    frequency_terms = ["araw-araw", "daily", "madalas", "often", "frequently", 
                      "paminsan-minsan", "sometimes", "occasionally", "regular",
                      "persistent", "paulit-ulit", "recurring", "constant"]
                      
    for term in frequency_terms:
        pattern = r'\b' + re.escape(term) + r'\w*\b'
        for match in re.finditer(pattern, text.lower()):
            found_term = match.group(0)
            if found_term not in elements["frequency"]:
                elements["frequency"].append(found_term)
    
    # Extract duration information
    duration_patterns = [
        r'sa loob ng (\d+\s+(?:araw|linggo|buwan|taon))',
        r'for (\d+\s+(?:day|week|month|year)s?)',
        r'(ilang|maraming) (araw|linggo|buwan|taon)',
        r'(several|many|few) (day|week|month|year)s',
        r'(sa nakalipas na|for the past) ([^.,:;]+)'
    ]
    
    for pattern in duration_patterns:
        for match in re.finditer(pattern, text.lower()):
            duration = match.group(0)
            if duration not in elements["duration"]:
                elements["duration"].append(duration)
    
    # Extract limitation patterns
    limitation_patterns = [
        r'(nahihirapan|hirap) sa ([^.,:;]+)',
        r'(limitado|limited) ang ([^.,:;]+)',
        r'(problema|issue) sa ([^.,:;]+)',
        r'(hindi|di) (makapag|magawang) ([^.,:;]+)',
        r'(struggles with|difficulty in) ([^.,:;]+)'
    ]
    
    for pattern in limitation_patterns:
        for match in re.finditer(pattern, text.lower()):
            if len(match.groups()) >= 2:
                limitation = match.group(0)
                if limitation not in elements["limitations"]:
                    elements["limitations"].append(limitation)
    
    # Extract mental state patterns specific to section type
    if section_type == "kalagayan_mental":
        mental_state_patterns = [
            r'(nagpapakita ng|shows|exhibits) ([^.,:;]+)',
            r'(may|has|with) ([^.,:;]+) (mental state|cognitive function|mood)',
            r'(nadidiagnose|diagnosed with) ([^.,:;]+)',
            r'(nararamdaman ang|feels) ([^.,:;]+)'
        ]
        
        for pattern in mental_state_patterns:
            for match in re.finditer(pattern, text.lower()):
                if len(match.groups()) >= 2:
                    state = match.group(0)
                    if state not in elements["mental_state"]:
                        elements["mental_state"].append(state)
    
    # Extract need patterns for any section
    need_patterns = [
        r'(nangangailangan ng|needs|requires) ([^.,:;]+)',
        r'(kailangan ang|requires the) ([^.,:;]+)',
        r'(dapat|should) (ay|be)? ([^.,:;]+)',
        r'(kinakailangan na|it is necessary to) ([^.,:;]+)'
    ]
    
    for pattern in need_patterns:
        for match in re.finditer(pattern, text.lower()):
            need = match.group(0)
            if need not in elements["needs"]:
                elements["needs"].append(need)
    
    # Extract important verbs and adjectives (useful for synthesis)
    for token in doc:
        if token.pos_ == "VERB" and token.is_alpha and len(token.text) > 2:
            if token.text.lower() not in elements["verbs"]:
                elements["verbs"].append(token.text)
        elif token.pos_ == "ADJ" and token.is_alpha and len(token.text) > 2:
            if token.text.lower() not in elements["adjectives"]:
                elements["adjectives"].append(token.text)
    
    return elements

def extract_important_terms(text, count=5, doc_type="assessment"):
    """Extract important medical and health terms without relying on noun_chunks."""
    if not text or len(text) < 10:
        return []
    
    # Process with Calamancy NLP
    doc = nlp(text)
    term_candidates = []
    term_scores = {}
    
    # 1. EXTRACT FROM NAMED ENTITIES - most reliable method
    entity_scores = {
        "DISEASE": 10,         # Medical conditions - highest priority
        "SYMPTOM": 9,          # Symptoms
        "TREATMENT_METHOD": 8, # Specific treatment approaches
        "HEALTHCARE_REFERRAL": 8, # Referrals to specialists
        "TREATMENT": 7,        # Treatments
        "RECOMMENDATION": 7,   # Medical recommendations
        "DIET_RECOMMENDATION": 7, # Dietary recommendations
        "WARNING_SIGN": 7,     # Warning signs for conditions
        "BODY_PART": 6,        # Body parts
        "MEASUREMENT": 6,      # Medical measurements
        "COGNITIVE": 6,        # Cognitive conditions
        "EMOTION": 5,          # Emotional states
        "MEDICATION": 6,       # Medications
        "EQUIPMENT": 5,        # Medical equipment
        "HOME_MODIFICATION": 5, # Environmental changes
        "ADL": 5,              # Activities of daily living
        "SAFETY": 5,           # Safety concerns
        "MONITORING": 5,       # Monitoring approaches
        "TIMEFRAME": 4,        # Timeframes
        "SOCIAL_REL": 3,       # Social relationships
        "PER": 2               # People mentioned
    }
    
    for ent in doc.ents:
        if ent.label_ in entity_scores:
            term = ent.text.lower().strip()
            term = re.sub(r'^[,\s.:;]+|[,\s.:;]+$', '', term)
            
            if term and len(term) > 2:
                score = entity_scores.get(ent.label_, 1)
                
                # Context and document-specific scoring
                if doc_type.lower() == "assessment" and ent.label_ in ["DISEASE", "SYMPTOM"]:
                    score += 1
                elif doc_type.lower() == "evaluation" and ent.label_ in ["RECOMMENDATION", "TREATMENT_METHOD"]:
                    score += 1
                    
                # Add or update score
                if term in term_scores:
                    term_scores[term] = max(term_scores[term], score)
                else:
                    term_scores[term] = score
                    term_candidates.append(term)
    
    # 2. USE REGEX PATTERNS INSTEAD OF NOUN CHUNKS
    # Identify common Filipino medical multi-word expressions
    medical_patterns = [
        # Symptom patterns
        r'(problema sa|hirap sa|sakit sa|pananakit ng) ([a-zA-Z\s]{3,25})',
        r'(nahihirapang|hindi makapag|limitadong) ([a-zA-Z\s]{3,25})',
        
        # Condition patterns
        r'(may|diagnosed na may|nagdurusa sa) ([a-zA-Z\s]{3,25})',
        r'(kondisyon ng|karamdaman sa) ([a-zA-Z\s]{3,25})',
        
        # Recommendation patterns
        r'(dapat|kailangan|inirerekomenda na|iminumungkahi na) ([a-zA-Z\s]{3,25})',
        r'(mahalagang|kinakailangang) ([a-zA-Z\s]{3,25})',
        
        # Treatment patterns
        r'(paggamot sa|lunas para sa) ([a-zA-Z\s]{3,25})',
        r'(therapy para sa|gamot para sa) ([a-zA-Z\s]{3,25})'
    ]
    
    for pattern in medical_patterns:
        matches = re.finditer(pattern, text.lower())
        for match in matches:
            if match and len(match.groups()) > 1:
                # Get the matched phrase
                phrase = match.group(0).strip()
                
                # Clean up the phrase
                phrase = re.sub(r'^[,\s.:;]+|[,\s.:;]+$', '', phrase)
                
                if phrase and len(phrase) > 5 and phrase not in term_candidates:
                    # Score based on pattern type
                    pattern_type = match.group(1).strip() if match.groups() else ""
                    
                    if any(rec in pattern_type for rec in ["dapat", "kailangan", "inirerekomenda"]):
                        score = 7 if doc_type.lower() == "evaluation" else 4
                    elif any(cond in pattern_type for rec in ["problema", "hirap", "sakit"]):
                        score = 7 if doc_type.lower() == "assessment" else 4
                    else:
                        score = 5
                    
                    term_scores[phrase] = score
                    term_candidates.append(phrase)
    
    # 3. EXTRACT FROM MEDICAL INDICATORS - comprehensive list
    medical_indicators = [
        # Disease and condition indicators
        "diabetes", "type 2 diabetes", "diyabetis", "hypertension", "altapresyon",
        "dementia", "alzheimer", "depression", "anxiety", "pagkabalisa",
        "chronic pain", "sakit", "kirot", "stroke", "arthritis",
        
        # Symptoms
        "pagkahilo", "dizziness", "pananakit", "hirap huminga", "shortness of breath",
        "pagduduwal", "nausea", "fatigue", "pagkapagod", "panginginig",
        
        # Treatments
        "physical therapy", "gamot", "medication", "therapy", "operasyon", "surgery",
        
        # Recommendations
        "inirerekomenda", "dapat", "kailangan", "mainam", "iminumungkahi"
    ]
    
    # Look for important medical terms and their surrounding context
    for term in medical_indicators:
        if term in text.lower():
            # Find the term in context (words before and after)
            term_positions = [m.start() for m in re.finditer(r'\b' + re.escape(term) + r'\b', text.lower())]
            
            for pos in term_positions:
                # Get surrounding context (15 chars before, 20 after)
                start = max(0, pos - 15)
                end = min(len(text), pos + len(term) + 20)
                context = text[start:end].lower()
                
                # Clean up the context
                context = re.sub(r'^[^a-zA-Z0-9\s]+', '', context)
                context = re.sub(r'[^a-zA-Z0-9\s]+$', '', context)
                context = re.sub(r'\s+', ' ', context).strip()
                
                if context and len(context) > len(term) and context not in term_candidates:
                    # Score based on term type
                    if term in ["inirerekomenda", "dapat", "kailangan", "mainam"]:
                        score = 6 if doc_type.lower() == "evaluation" else 3
                    elif term in ["diabetes", "hypertension", "dementia", "depression"]:
                        score = 6 if doc_type.lower() == "assessment" else 3
                    else:
                        score = 4
                        
                    term_scores[context] = score
                    term_candidates.append(context)
    
    # 4. FILTER AND RANK
    # Remove stopwords
    tagalog_stopwords = {
        "ang", "ng", "sa", "na", "ay", "mga", "ko", "ako", "ikaw", "ka", "siya", "kami", 
        "tayo", "sila", "ito", "iyon", "dito", "diyan", "doon", "ni", "si", "nang", "nga", 
        "po", "raw", "din", "rin", "pa", "lang", "pala", "daw", "man", "kasi", "dahil", 
        "pero", "ngunit", "at", "kung", "kapag", "hindi", "wala", "may", "mayroon"
    }
    
    filtered_terms = []
    for term in term_candidates:
        # Skip standalone stopwords
        if term.lower() in tagalog_stopwords:
            continue
        
        # Skip if all words are stopwords
        if all(word.lower() in tagalog_stopwords for word in term.split()):
            continue
        
        filtered_terms.append(term)
    
    # Sort by score
    sorted_terms = sorted(filtered_terms, key=lambda term: term_scores.get(term, 0), reverse=True)
    
    # 5. ENSURE DIVERSITY OF TERM TYPES
    final_terms = []
    categories_found = {
        "condition": 0,
        "symptom": 0,
        "treatment": 0,
        "recommendation": 0
    }
    
    for term in sorted_terms:
        # Categorize the term
        if any(c in term for c in ["kondisyon", "sakit", "disease", "diabetes"]):
            category = "condition"
        elif any(s in term for s in ["sintomas", "hirap", "pain", "sakit"]):
            category = "symptom"
        elif any(t in term for t in ["gamot", "therapy", "treatment"]):
            category = "treatment"
        elif any(r in term for r in ["inirerekomenda", "dapat", "kailangan"]):
            category = "recommendation"
        else:
            category = "other"
        
        # Maintain diversity by limiting categories
        max_per_category = 2
        if categories_found.get(category, 0) < max_per_category:
            final_terms.append(term)
            categories_found[category] = categories_found.get(category, 0) + 1
            
        # Stop once we have enough terms
        if len(final_terms) >= count:
            break
    
    # If we don't have enough diverse terms, add more from the sorted list
    if len(final_terms) < count:
        for term in sorted_terms:
            if term not in final_terms:
                final_terms.append(term)
            if len(final_terms) >= count:
                break
    
    return final_terms[:count]

def get_semantic_relationship(content1, content2):
    """Determine the semantic relationship between two pieces of content."""
    # Simple keyword-based relationship detection
    causation_terms = ["dahil", "sanhi", "resulting in", "caused by", "leads to", "bunga ng"]
    contrast_terms = ["subalit", "ngunit", "however", "on the other hand", "in contrast"]
    addition_terms = ["bukod", "dagdag", "din", "rin", "additionally", "moreover"]
    
    content = content1.lower() + " " + content2.lower()
    
    if any(term in content for term in causation_terms):
        return "causation"
    elif any(term in content for term in contrast_terms):
        return "contrast"
    elif any(term in content for term in addition_terms):
        return "addition"
    else:
        return "neutral"

def choose_appropriate_transition(prev_content, next_content, section_relationship=None):
    """Choose the most appropriate transition phrase based on content relationship."""
    # Get relationship if not provided
    relationship = section_relationship or get_semantic_relationship(prev_content, next_content)
    
    # Causation transitions
    if relationship == "causation":
        transitions = ["Dahil dito, ", "Bilang resulta, ", "Dulot nito, ", 
                       "Sa ganitong dahilan, ", "Bunga nito, "]
    
    # Contrast transitions
    elif relationship == "contrast":
        transitions = ["Sa kabilang banda, ", "Gayunpaman, ", "Subalit, ", 
                       "Ngunit, ", "Bagama't ganito, ", "Samantala, "]
    
    # Addition transitions (default)
    else:
        transitions = ["Bukod dito, ", "Gayundin, ", "Dagdag pa rito, ", 
                       "Karagdagan dito, ", "Kasabay nito, ", "Kasama rito, "]
    
    # Return a randomly selected transition from the appropriate category
    import random
    return random.choice(transitions)

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
    """Summarize Tagalog text using methods that don't rely on noun_chunks"""
    if not request.is_json:
        return jsonify({"error": "Request must be JSON"}), 400
        
    data = request.json
    text = data.get('text', '')
    doc_type = data.get('type', '')
    
    if not text:
        return jsonify({"error": "Empty text provided"}), 400
    
    try:
        start_time = time.time()
        
        # Clean and normalize the text
        cleaned_text = clean_and_normalize_text(text)
        
        # Process text with calamancy
        doc = nlp(cleaned_text)
        
        # Extract original sentences
        sentences = split_into_sentences(cleaned_text)
        print(f"Found {len(sentences)} sentences in text")
        
        # Extract entities
        try:
            entities = []
            for ent in doc.ents:
                entities.append({
                    "text": ent.text,
                    "label": ent.label_,
                    "start_char": ent.start_char,
                    "end_char": ent.end_char
                })
        except Exception as e:
            print(f"Entity extraction error: {e}")
            entities = []
        
        # Extract key medical terms using our FIXED function
        try:
            key_terms = extract_important_terms(cleaned_text, count=5, doc_type=doc_type)
        except Exception as e:
            print(f"Term extraction error: {e}")
            key_terms = []
        
        # Extract sections using methods that don't rely on noun_chunks
        try:
            if doc_type.lower() == "evaluation":
                # Use specialized evaluation section extraction
                sections = extract_sections_for_evaluation(sentences)
            else:
                # Use general section extraction for assessments
                sections = extract_sections_improved(sentences, doc_type)
        except Exception as e:
            print(f"Section extraction error: {e}")
            sections = {}
            
        # Generate simplified summary that doesn't use noun_chunks
        try:
            # Try the enhanced multi-section approach first
            summary = create_enhanced_multi_section_summary(doc, sections, doc_type)
            
            # If it's too short, fall back to simple summary as backup
            if len(summary) < 100:
                backup_summary = create_simple_summary(doc, sections, doc_type)
                
                # Use whichever is better (longer and more comprehensive)
                if len(backup_summary) > len(summary) * 1.5:
                    summary = backup_summary
                    
            # Final sanity check - if summary is still too short, use fallback
            if len(summary) < 50:
                main_subject = extract_main_subject(doc)
                if not main_subject.startswith("Ang ") and not main_subject.startswith("Si "):
                    main_subject = f"Si {main_subject}"
                    
                if doc_type.lower() == "assessment":
                    summary = f"{main_subject} ay nangangailangan ng komprehensibong pagsusuri."
                else:
                    summary = "Inirerekomenda ang pagkonsulta para sa karagdagang pagsusuri at paggamot."
        except Exception as e:
            print(f"Enhanced summary generation error: {e}")
            traceback.print_exc()
            
            # Fall back to simple summary if enhanced fails
            try:
                summary = create_simple_summary(doc, sections, doc_type)
            except Exception as e:
                print(f"Simple summary generation error: {e}")
                
                # Last resort emergency fallback
                if doc_type.lower() == "assessment":
                    summary = "Ang pasyente ay nangangailangan ng karagdagang pagsusuri para sa kanyang kondisyon."
                else:
                    summary = "Inirerekomenda ang pagkonsulta para sa karagdagang pagsusuri at paggamot."
        
        # Final summary refinement
        if summary and len(summary) > 20:
            # Ensure proper punctuation and spacing
            summary = re.sub(r'\s+', ' ', summary)  # Fix multiple spaces
            summary = re.sub(r'\s([,.;:])', r'\1', summary)  # Fix spacing before punctuation
            
            # Ensure first letter is capitalized
            if summary[0].islower():
                summary = summary[0].upper() + summary[1:]
                
            # Ensure sentence ends with proper punctuation
            if not summary[-1] in ['.', '!', '?']:
                summary += '.'
        
        # Calculate processing statistics
        processing_time = round((time.time() - start_time) * 1000)
        compression_ratio = round(len(text) / len(summary)) if len(summary) > 0 else 0
        
        return jsonify({
            "summary": summary,
            "sections": sections,
            "sentence_count": len(sentences),
            "entities": entities,
            "key_medical_terms": key_terms,
            "document_type": doc_type,
            "processing_stats": {
                "original_text_length": len(text),
                "summary_length": len(summary),
                "compression_ratio": compression_ratio,
                "processing_time_ms": processing_time,
                "entities_found": len(entities),
                "sections_count": len(sections)
            }
        })
    except Exception as e:
        print(f"Error processing text: {e}")
        traceback.print_exc()
        return jsonify({
            "summary": "Hindi matagumpay ang pagsusuri. Mangyaring subukan muli.",
            "error": str(e),
            "sections": {},
            "entities": [],
            "sentence_count": 0
        })

if __name__ == '__main__':
    print("Starting Flask server...")
    app.run(debug=True, host='0.0.0.0', port=5000)