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
    """Create a comprehensive summary with equal representation from all sections."""
    if not sections:
        return "Walang sapat na impormasyon para sa buod."
    
    # Extract main subject (patient)
    subject = extract_main_subject(doc)
    
    # ENHANCEMENT 1: Handle long sections by splitting them
    processed_sections = {}
    for section_name, section_text in sections.items():
        # Skip empty sections
        if not section_text or len(section_text.strip()) < 10:
            continue
            
        # For very long sections (more than 500 chars), split into smaller parts
        if len(section_text) > 500:
            # Split into chunks for better processing
            section_sentences = split_into_sentences(section_text)
            
            # Create chunks of max 3 sentences
            chunks = []
            current_chunk = []
            current_length = 0
            
            for sent in section_sentences:
                if current_length + len(sent) > 300 and current_chunk:  # Start new chunk if this would make it too long
                    chunks.append(" ".join(current_chunk))
                    current_chunk = [sent]
                    current_length = len(sent)
                else:
                    current_chunk.append(sent)
                    current_length += len(sent)
                    
            # Add the last chunk if not empty
            if current_chunk:
                chunks.append(" ".join(current_chunk))
                
            # Store the chunks
            processed_sections[section_name] = chunks
        else:
            # For normal sections, keep as is
            processed_sections[section_name] = [section_text]
    
    # ENHANCEMENT 2: Calculate how many sentences to dedicate per section
    section_sentence_allocation = {}
    
    # Base allocation - start with one sentence per section
    for section_name, chunks in processed_sections.items():
        # Allocate one sentence per chunk, with a minimum of 1 and maximum of 3
        # This ensures long sections get more representation
        section_sentence_allocation[section_name] = min(3, max(1, len(chunks)))
    
    # Ensure we have reasonable total length (aim for 3-7 sentences total)
    total_allocation = sum(section_sentence_allocation.values())
    max_total_sentences = 7
    
    if total_allocation > max_total_sentences:
        # We need to reduce allocation for some sections
        # Prioritize by section importance
        priority_sections = []
        if doc_type.lower() == "assessment":
            priority_sections = ["mga_sintomas", "kalagayan_pangkatawan", "kalagayan_mental"]
        else:
            priority_sections = ["pangunahing_rekomendasyon", "mga_hakbang"]
            
        # First reduce non-priority sections to 1 sentence each
        for section_name in section_sentence_allocation:
            if section_name not in priority_sections and section_sentence_allocation[section_name] > 1:
                section_sentence_allocation[section_name] = 1
        
        # If still too many, reduce priority sections evenly
        total_allocation = sum(section_sentence_allocation.values())
        if total_allocation > max_total_sentences:
            # Calculate how many to remove
            to_remove = total_allocation - max_total_sentences
            
            # Convert to list for easier manipulation
            allocations = [(section, count) for section, count in section_sentence_allocation.items()]
            allocations.sort(key=lambda x: (x[0] not in priority_sections, -x[1]))  # Sort by priority, then by count
            
            # Remove from highest counts first
            for i in range(to_remove):
                if i < len(allocations) and allocations[i][1] > 1:
                    allocations[i] = (allocations[i][0], allocations[i][1] - 1)
            
            # Convert back to dict
            section_sentence_allocation = {section: count for section, count in allocations}
    
    # ENHANCEMENT 3: Generate sentences for each section with SPECIFIC details
    section_sentences = {}
    
    # Extract structured elements for all sections first
    section_elements = {}
    for section_name, chunks in processed_sections.items():
        # Process all chunks together to get more context
        combined_text = " ".join(chunks)
        section_elements[section_name] = extract_structured_elements(combined_text, section_name)
    
    # Process document sections based on document type
    if doc_type.lower() == "assessment":
        # Process assessment sections
        for section_name, chunks in processed_sections.items():
            # Skip processed sections
            if section_name in section_sentences:
                continue
                
            sentences = []
            elements = section_elements[section_name]
            
            # Generate sentences based on section type with SPECIFIC DETAILS
            if section_name == "mga_sintomas" or "sintomas" in section_name:
                # CONDITIONS AND SYMPTOMS
                conditions = elements["conditions"][:2]  # Take up to 2 conditions
                symptoms = elements["symptoms"][:3]      # Take up to 3 symptoms
                
                # Combine conditions and symptoms
                medical_issues = []
                if conditions:
                    medical_issues.extend(conditions)
                if symptoms and not any(s in " ".join(conditions) for s in symptoms):
                    medical_issues.extend(symptoms)
                
                if medical_issues:
                    # Format the medical issues with proper Tagalog conjunction
                    if len(medical_issues) == 1:
                        condition_text = medical_issues[0]
                    elif len(medical_issues) == 2:
                        condition_text = f"{medical_issues[0]} at {medical_issues[1]}"
                    else:
                        condition_text = f"{', '.join(medical_issues[:-1])}, at {medical_issues[-1]}"
                    
                    # NEW: Include specific severity or frequency if available
                    severity = elements["severity"][0] if elements["severity"] else ""
                    frequency = elements["frequency"][0] if elements["frequency"] else ""
                    
                    if severity and frequency:
                        sentences.append(f"{subject} ay nagpapakita ng {severity} at {frequency} na {condition_text}.")
                    elif severity:
                        sentences.append(f"{subject} ay nagpapakita ng {severity} na {condition_text}.")
                    elif frequency:
                        sentences.append(f"{subject} ay nagpapakita ng {condition_text} na nangyayari nang {frequency}.")
                    else:
                        sentences.append(f"{subject} ay nagpapakita ng {condition_text}.")
                
                # If allocated more than 1 sentence and we have severity info, add it
                if section_sentence_allocation[section_name] > 1 and (elements["severity"] or elements["frequency"] or elements["duration"]):
                    severity = elements["severity"][0] if elements["severity"] else ""
                    frequency = elements["frequency"][0] if elements["frequency"] else ""
                    duration = elements["duration"][0] if elements["duration"] else ""
                    
                    # NEW: Create more specific second sentence about symptom characteristics
                    if severity and frequency and duration:
                        sentences.append(f"Ang mga sintomas ay {severity}, nangyayari nang {frequency}, at nagsimula {duration}.")
                    elif severity and frequency:
                        sentences.append(f"Ang mga sintomas ay {severity} at {frequency}.")
                    elif severity and duration:
                        sentences.append(f"Ang mga sintomas ay {severity} at nagsimula {duration}.")
                    elif frequency and duration:
                        sentences.append(f"Ang mga sintomas ay nangyayari nang {frequency} at nagsimula {duration}.")
                    elif severity:
                        sentences.append(f"Ang mga sintomas ay {severity}.")
                    elif frequency:
                        sentences.append(f"Ang mga sintomas ay nangyayari nang {frequency}.")
                    elif duration:
                        sentences.append(f"Ang mga sintomas ay nagsimula {duration}.")
                
                # NEW: If allocated 3 sentences and we have additional symptoms, describe them
                if section_sentence_allocation[section_name] > 2 and len(symptoms) > 2:
                    additional_symptoms = symptoms[2:]
                    if additional_symptoms:
                        if len(additional_symptoms) == 1:
                            symptom_text = additional_symptoms[0]
                        else:
                            symptom_text = f"{additional_symptoms[0]} at {additional_symptoms[1]}"
                        sentences.append(f"Bukod dito, nararanasan din niya ang {symptom_text}.")
            
            elif section_name == "kalagayan_pangkatawan" or "pangkatawan" in section_name:
                # PHYSICAL STATUS
                measurements = elements["vital_signs"]
                limitations = elements["limitations"]
                body_parts = elements["body_parts"]
                
                # NEW: Create more specific physical status sentences
                if measurements:
                    measurement = measurements[0]
                    sentences.append(f"Ang mga sukat ay nagpapakita ng {measurement}.")
                
                # NEW: Combine limitations with specific body parts for more detail
                if limitations and body_parts and not sentences:
                    limitation = limitations[0]
                    body_part = body_parts[0]
                    sentences.append(f"May limitasyon {subject} sa {limitation}, partikular sa {body_part}.")
                elif limitations and not sentences:
                    limitation = limitations[0]
                    sentences.append(f"May limitasyon {subject} sa {limitation}.")
                elif body_parts and not sentences:
                    body_part = body_parts[0]
                    sentences.append(f"May nararamdaman {subject} sa {body_part}.")
                
                # Add a second sentence with additional physical details if allocated
                if section_sentence_allocation[section_name] > 1:
                    if measurements and len(measurements) > 1 and not any("sukat" in s for s in sentences):
                        specific_measurement = measurements[1]
                        sentences.append(f"Ang kanyang vital signs ay nagpapakita ng {specific_measurement}.")
                    elif body_parts and len(body_parts) > 1 and not any(body_parts[1] in s for s in sentences):
                        specific_body_part = body_parts[1]
                        sentences.append(f"May karamdaman din {subject} sa {specific_body_part}.")
                    elif limitations and len(limitations) > 1 and not any(limitations[1] in s for s in sentences):
                        specific_limitation = limitations[1]
                        sentences.append(f"Nakakaranas din siya ng {specific_limitation}.")
            
            elif section_name == "aktibidad" or "aktibidad" in section_name:
                # ACTIVITIES AND MOBILITY
                activities = elements["activities"]
                limitations = elements["activity_limitations"]
                
                # NEW: More specific activity limitation descriptions
                if limitations:
                    specific_limitation = limitations[0]
                    # Look for specific ADLs or mobility issues
                    if "paglalakad" in specific_limitation.lower() or "mobility" in specific_limitation.lower():
                        sentences.append(f"{subject} ay nahihirapan sa {specific_limitation}, na nakakaapekto sa kanyang paggalaw.")
                    elif "pagkain" in specific_limitation.lower() or "feeding" in specific_limitation.lower():
                        sentences.append(f"{subject} ay nahihirapan sa {specific_limitation}, na nakakaapekto sa kanyang nutrition.")
                    else:
                        sentences.append(f"{subject} ay nahihirapan sa {specific_limitation}.")
                elif activities:
                    activity = activities[0]
                    sentences.append(f"Sa mga pang-araw-araw na gawain, {subject} ay may kahirapan sa {activity}.")
                
                # Add second sentence for additional details
                if section_sentence_allocation[section_name] > 1:
                    if activities and len(activities) > 1 and not any(activities[1] in s for s in sentences):
                        specific_activity = activities[1]
                        sentences.append(f"Nahihirapan din siya sa {specific_activity}.")
                    elif limitations and len(limitations) > 1:
                        specific_limitation = limitations[1]
                        sentences.append(f"May kahirapan din siya sa {specific_limitation}.")
            
            elif section_name == "kalagayan_mental" or "mental" in section_name:
                # MENTAL STATUS
                cognitive = elements["cognitive_status"]
                emotional = elements["emotional_state"]
                mental = elements["mental_state"]
                
                # NEW: More specific mental status descriptions
                mental_states = cognitive + emotional + mental
                
                if mental_states:
                    # Get the most specific cognitive condition first
                    specific_cognitive_terms = [
                        "dementia", "Alzheimer", "cognitive decline", "memory loss", 
                        "confusion", "disorientation", "pagkalito", "nakalimutan",
                        "hindi matandaan", "nalilito", "hindi makilala"
                    ]
                    
                    # Look for specific cognitive conditions first
                    specific_state = None
                    for state in mental_states:
                        if any(term in state.lower() for term in specific_cognitive_terms):
                            specific_state = state
                            break
                    
                    # If no specific cognitive condition found, take first mental state
                    if not specific_state and mental_states:
                        specific_state = mental_states[0]
                    
                    if specific_state:
                        sentences.append(f"Ang mental na kalagayan ni {subject} ay nagpapakita ng {specific_state}.")
                    
                    # Add second sentence with emotional state if different
                    if section_sentence_allocation[section_name] > 1 and len(mental_states) > 1:
                        # Try to find a different type of mental state for variety
                        emotional_terms = ["lungkot", "depression", "anxiety", "takot", "galit", "irritable", "agitation"]
                        
                        second_state = None
                        for state in mental_states:
                            if state != specific_state and any(term in state.lower() for term in emotional_terms):
                                second_state = state
                                break
                        
                        if not second_state and len(mental_states) > 1:
                            second_state = [s for s in mental_states if s != specific_state][0]
                            
                        if second_state:
                            sentences.append(f"Mayroon din siyang {second_state}.")
                
                # NEW: Add third sentence with specific behavioral manifestation if allocated
                if section_sentence_allocation[section_name] > 2:
                    behavior_terms = [
                        "behavior", "pag-uugali", "kilos", "ginagawa", "routine",
                        "pinagkakaabalahan", "pagtulog", "sleeping", "nagagalit",
                        "nagsisigaw", "nagwawala", "nagiging agresibo"
                    ]
                    
                    # Extract sentences from section text that mention behavior
                    behavior_sentences = []
                    for chunk in processed_sections[section_name]:
                        chunk_sentences = split_into_sentences(chunk)
                        for sent in chunk_sentences:
                            if any(term in sent.lower() for term in behavior_terms):
                                behavior_sentences.append(sent)
                    
                    # Find the most relevant behavior sentence
                    if behavior_sentences:
                        selected_sent = min(behavior_sentences, key=len)  # Get shortest one
                        if len(selected_sent) > 100:  # Trim if too long
                            last_comma = selected_sent[:100].rfind(',')
                            if last_comma > 30:
                                selected_sent = selected_sent[:last_comma+1]
                        sentences.append(f"Kapansin-pansin din ang kanyang {selected_sent}")
            
            elif section_name == "kalagayan_social" or "social" in section_name:
                # SOCIAL STATUS
                relations = elements["social_support"]
                
                # NEW: More specific social support descriptions
                if relations:
                    relation = relations[0]
                    
                    # Check for specific relationship terms
                    family_terms = ["pamilya", "asawa", "anak", "apo", "magulang", "kapatid"]
                    if any(term in relation.lower() for term in family_terms):
                        sentences.append(f"May suporta {subject} mula sa kanyang {relation}, na tumutulong sa kanyang pangangailangan.")
                    else:
                        sentences.append(f"May suporta {subject} mula sa kanyang {relation}.")
                    
                    # Add second sentence if allocated
                    if section_sentence_allocation[section_name] > 1 and len(relations) > 1:
                        relation2 = relations[1]
                        sentences.append(f"Tumutulong din ang kanyang {relation2} sa pangangalaga.")
                
                # NEW: Add third sentence about social integration/isolation if allocated
                if section_sentence_allocation[section_name] > 2:
                    social_issue_terms = [
                        "isolation", "kalungkutan", "loneliness", "nag-iisa", 
                        "walang karamay", "walang kasama", "home-bound", "bedridden"
                    ]
                    
                    # Check if text mentions isolation
                    combined_section_text = " ".join(processed_sections[section_name])
                    if any(term in combined_section_text.lower() for term in social_issue_terms):
                        sentences.append(f"Gayunpaman, nararanasan niya ang social isolation na maaaring makaapekto sa kanyang pangkalahatang kalusugan.")
                    else:
                        sentences.append(f"Ang kanyang social support network ay mahalaga para sa kanyang recovery at pangkalahatang well-being.")
            
            # If we still don't have enough sentences for this section, add generic ones
            # but with more SPECIFIC CONTENT based on section type
            if len(sentences) < section_sentence_allocation[section_name]:
                needed = section_sentence_allocation[section_name] - len(sentences)
                
                # Get section-specific text elements to make generic sentences more specific
                combined_text = " ".join(processed_sections[section_name])
                doc_section = nlp(combined_text)
                
                # Extract entities from section text
                section_entities = []
                for ent in doc_section.ents:
                    if len(ent.text) > 3:  # Skip very short entities
                        section_entities.append(ent.text)
                
                if section_name == "mga_sintomas" and needed > 0:
                    if section_entities:
                        specific_entity = section_entities[0]
                        sentences.append(f"Ang mga sintomas ni {subject} tulad ng {specific_entity} ay nangangailangan ng karagdagang pagsusuri.")
                    else:
                        sentences.append(f"Ang mga sintomas ni {subject} ay nangangailangan ng karagdagang pagsusuri.")
                        
                elif section_name == "kalagayan_pangkatawan" and needed > 0:
                    if body_parts:
                        sentences.append(f"Ang pisikal na kondisyon ni {subject}, lalo na sa {body_parts[0]}, ay nangangailangan ng atensyon.")
                    else:
                        sentences.append(f"Ang pisikal na kondisyon ni {subject} ay nangangailangan ng atensyon.")
                        
                elif section_name == "aktibidad" and needed > 0:
                    if activities:
                        sentences.append(f"May mga limitasyon si {subject} sa kanyang {activities[0]}.")
                    else:
                        sentences.append(f"May mga limitasyon si {subject} sa kanyang pang-araw-araw na gawain.")
                        
                elif section_name == "kalagayan_mental" and needed > 0:
                    if section_entities:
                        cognitive_entity = next((e for e in section_entities if "memory" in e.lower() or "confusion" in e.lower() or "pagkalito" in e.lower() or "cognitive" in e.lower()), "")
                        if cognitive_entity:
                            sentences.append(f"Ang mental na kalagayan ni {subject}, partikular ang {cognitive_entity}, ay nangangailangan ng maingat na ebalwasyon.")
                        else:
                            sentences.append(f"Ang mental na kalagayan ni {subject} ay nagpapakita ng mga pagbabago na dapat subaybayan.")
                    else:
                        sentences.append(f"Ang mental na kalagayan ni {subject} ay nagpapakita ng mga pagbabago.")
                        
                elif section_name == "kalagayan_social" and needed > 0:
                    if relations:
                        sentences.append(f"Ang suporta mula sa {relations[0]} ay mahalaga para kay {subject}.")
                    else:
                        sentences.append(f"Ang suporta ng pamilya at komunidad ay mahalaga para kay {subject}.")
            
            section_sentences[section_name] = sentences[:section_sentence_allocation[section_name]]  # Limit to allocation
            
    else:  # EVALUATION document
        # Process evaluation sections
        for section_name, chunks in processed_sections.items():
            # Skip processed sections
            if section_name in section_sentences:
                continue
                
            sentences = []
            elements = section_elements[section_name]
            
            if section_name == "pangunahing_rekomendasyon" or "rekomendasyon" in section_name:
                # PRIMARY RECOMMENDATIONS
                referrals = elements["healthcare_referrals"]
                recommendations = elements["recommendations"]
                
                # NEW: More specific recommendation sentences
                if referrals:
                    specific_referral = referrals[0]
                    # Check for specialist type
                    specialist_terms = ["specialist", "doctor", "gastroenterologist", "neurologist", "cardiologist", "oncologist", "psychologist", "physical therapist"]
                    
                    if any(term in specific_referral.lower() for term in specialist_terms):
                        sentences.append(f"Inirerekomenda ang agarang pagkonsulta sa {specific_referral} para sa komprehensibong pagsusuri at diagnosis.")
                    else:
                        sentences.append(f"Inirerekomenda ang agarang pagkonsulta sa {specific_referral} para sa komprehensibong pagsusuri.")
                elif recommendations:
                    recommendation = next((r for r in recommendations if len(r) > 5), "komprehensibong pagsusuri")
                    sentences.append(f"Inirerekomenda ang {recommendation} para sa wastong pangangalaga.")
                
                # Second sentence for additional recommendations
                if section_sentence_allocation[section_name] > 1:
                    if referrals and len(referrals) > 1:
                        second_referral = referrals[1]
                        sentences.append(f"Maaari ring kumunsulta sa {second_referral} para sa mas detalyadong pagsusuri.")
                    elif recommendations and len(recommendations) > 1:
                        rec2 = next((r for r in recommendations[1:] if len(r) > 5 and r not in sentences[0]), None)
                        if rec2:
                            sentences.append(f"Iminumungkahi rin ang {rec2} para sa kabuuang manangement ng kondisyon.")
                
                # Third sentence for urgency or timeline if allocated
                if section_sentence_allocation[section_name] > 2:
                    urgency_terms = ["agaran", "urgent", "immediate", "priority", "critical", "crucial", "important"]
                    combined_section_text = " ".join(processed_sections[section_name])
                    
                    if any(term in combined_section_text.lower() for term in urgency_terms):
                        sentences.append("Ang mga rekomendasyon na ito ay dapat isagawa sa lalong madaling panahon upang maiwasan ang karagdagang komplikasyon.")
                    else:
                        sentences.append("Ang pagpapatupad ng mga rekomendasyon na ito ay mahalaga para sa epektibong management ng kanyang kondisyon.")
            
            elif section_name == "mga_hakbang" or "hakbang" in section_name:
                # ACTION STEPS
                treatments = elements["treatments"]
                methods = elements["intervention_methods"]
                
                all_treatments = treatments + methods
                
                # NEW: More specific treatment/intervention sentences
                if all_treatments:
                    specific_treatment = all_treatments[0]
                    
                    # Check treatment type
                    medication_terms = ["gamot", "medicine", "medication", "tablets", "pills", "antibiotic", "drug", "prescription"]
                    therapy_terms = ["therapy", "exercise", "rehabilitation", "physical therapy", "speech therapy", "occupational therapy"]
                    
                    if any(term in specific_treatment.lower() for term in medication_terms):
                        sentences.append(f"Dapat isagawa ang {specific_treatment} ayon sa prescribed dosage at schedule para sa epektibong paggamot.")
                    elif any(term in specific_treatment.lower() for term in therapy_terms):
                        sentences.append(f"Dapat isagawa ang {specific_treatment} nang regular at konsistent para sa optimal na recovery.")
                    else:
                        sentences.append(f"Dapat isagawa ang {specific_treatment} para sa epektibong paggamot.")
                    
                    # Second sentence for additional treatments
                    if section_sentence_allocation[section_name] > 1 and len(all_treatments) > 1:
                        second_treatment = all_treatments[1]
                        sentences.append(f"Makatutulong din ang {second_treatment} sa pangkalahatang paggaling.")
                
                # Third sentence for timing or duration if allocated
                if section_sentence_allocation[section_name] > 2 and all_treatments:
                    duration_terms = ["weeks", "months", "linggo", "buwan", "araw", "days", "regular", "daily", "weekly"]
                    combined_section_text = " ".join(processed_sections[section_name])
                    
                    if any(term in combined_section_text.lower() for term in duration_terms):
                        sentences.append(f"Ang {all_treatments[0]} ay dapat ipagpatuloy nang ilang linggo o hanggang sa makita ang pagbuti ng kondisyon.")
                    else:
                        sentences.append(f"Mahalagang sundin ang tamang technique sa pagsasagawa ng {all_treatments[0]} para sa maximum benefit.")
            
            elif section_name == "pangangalaga" or "alaga" in section_name:
                # CARE APPROACHES
                monitoring = elements["monitoring_plans"]
                warnings = elements["warnings"]
                
                # NEW: More specific monitoring and warning sentences
                if monitoring:
                    specific_monitoring = monitoring[0]
                    
                    vital_sign_terms = ["blood pressure", "temperature", "heart rate", "oxygen", "breathing", "presyon", "temperatura"]
                    if any(term in specific_monitoring.lower() for term in vital_sign_terms):
                        sentences.append(f"Mahalagang regular na subaybayan ang {specific_monitoring} at i-record ang mga reading para makita ang trend.")
                    else:
                        sentences.append(f"Mahalagang subaybayan ang {specific_monitoring} para sa kaligtasan.")
                elif warnings:
                    specific_warning = warnings[0]
                    sentences.append(f"Kailangang bantayan ang {specific_warning} para maiwasan ang kumplikasyon.")
                
                # Add second sentence if allocated
                if section_sentence_allocation[section_name] > 1:
                    if warnings and (not monitoring or not any(w in s for w, s in zip(warnings, sentences))):
                        warn = warnings[0] if not warnings[0] in " ".join(sentences) else warnings[1] if len(warnings) > 1 else ""
                        if warn:
                            sentences.append(f"Maging mapagmatyag sa mga palatandaan ng {warn} at kumonsulta agad kung lumala ang mga sintomas.")
                    elif monitoring and len(monitoring) > 1:
                        second_monitoring = monitoring[1]
                        sentences.append(f"Regular ding i-monitor ang {second_monitoring} upang masiguro ang kaligtasan.")
                
                # Add third sentence about documentation if allocated
                if section_sentence_allocation[section_name] > 2:
                    documentation_terms = ["record", "document", "diary", "log", "listahan", "talaan"]
                    combined_section_text = " ".join(processed_sections[section_name])
                    
                    if any(term in combined_section_text.lower() for term in documentation_terms):
                        sentences.append("Mahalagang i-document ang lahat ng pagbabago sa kondisyon para makatulong sa healthcare provider sa pag-assess ng progress.")
                    else:
                        sentences.append("Regular na pagbabahagi ng updates sa healthcare team ay makatutulong sa pag-adjust ng care plan kung kinakailangan.")
            
            elif section_name == "pagbabago_sa_pamumuhay" or "pamumuhay" in section_name:
                # LIFESTYLE CHANGES
                diet = elements.get("diet_changes", [])  # Use .get() with default empty list
                
                # NEW: More specific lifestyle recommendation sentences
                if diet:
                    specific_diet = diet[0]
                    
                    restriction_terms = ["bawas", "iwas", "avoid", "limit", "reduce", "cut", "hindi"]
                    addition_terms = ["dagdag", "increase", "more", "add", "include", "incorporate"]
                    
                    if any(term in specific_diet.lower() for term in restriction_terms):
                        sentences.append(f"Para sa diet, inirerekomenda ang {specific_diet} upang mabawasan ang posibleng komplikasyon.")
                    elif any(term in specific_diet.lower() for term in addition_terms):
                        sentences.append(f"Para sa diet, inirerekomenda ang {specific_diet} upang mapalakas ang immune system at mapabilis ang paggaling.")
                    else:
                        sentences.append(f"Para sa diet, inirerekomenda ang {specific_diet} upang matugunan ang mga pangangailangan.")
                else:
                    sentences.append("Inirerekomenda ang mga pagbabago sa istilo ng pamumuhay para sa paggaling at pag-iwas sa pagbalik ng kondisyon.")
                
                # Add second sentence if allocated
                if section_sentence_allocation[section_name] > 1:
                    if diet and len(diet) > 1:
                        second_diet = diet[1]
                        sentences.append(f"Bukod dito, maaaring isama sa diet ang {second_diet} para sa optimal na nutrition.")
                    else:
                        # Check for additional lifestyle recommendations
                        lifestyle_terms = {
                            "exercise": "Regular na physical activity na angkop sa kakayahan ng pasyente",
                            "ehersisyo": "Regular na ehersisyo na angkop sa kakayahan ng pasyente", 
                            "sleep": "Tamang schedule ng pagtulog at pagpapahinga",
                            "tulog": "Tamang schedule ng pagtulog at pagpapahinga",
                            "stress": "Pamamahala ng stress sa pamamagitan ng relaxation techniques",
                            "smoking": "Pag-iwas sa paninigarilyo",
                            "alcohol": "Pagbawas o pag-iwas sa alkohol"
                        }
                        
                        combined_section_text = " ".join(processed_sections[section_name])
                        
                        for term, recommendation in lifestyle_terms.items():
                            if term in combined_section_text.lower():
                                sentences.append(f"Iminumungkahi rin ang {recommendation}.")
                                break
                        else:
                            sentences.append("Ang mga pagbabago sa pang-araw-araw na gawain ay mahalaga para sa pangmatagalang kalusugan at quality of life.")
                
                # Add third sentence about consistency if allocated
                if section_sentence_allocation[section_name] > 2:
                    sentences.append("Consistent na pagsunod sa mga lifestyle modifications na ito ay mahalaga para makita ang mga positibong resulta sa kalusugan.")
            
            # Ensure we have enough sentences based on allocation
            if len(sentences) < section_sentence_allocation[section_name]:
                needed = section_sentence_allocation[section_name] - len(sentences)
                
                # Get section-specific text elements to make generic sentences more specific
                combined_text = " ".join(processed_sections[section_name])
                doc_section = nlp(combined_text)
                
                # Extract entities from section text
                section_entities = []
                for ent in doc_section.ents:
                    if len(ent.text) > 3:  # Skip very short entities
                        section_entities.append(ent.text)
                
                if section_name == "pangunahing_rekomendasyon" and needed > 0:
                    if section_entities:
                        specific_entity = next((e for e in section_entities if "specialist" in e.lower() or "doctor" in e.lower() or "physical" in e.lower()), section_entities[0])
                        sentences.append(f"Inirerekomenda ang pagkonsulta sa {specific_entity} para sa komprehensibong medikal na pagsusuri.")
                    else:
                        sentences.append("Inirerekomenda ang komprehensibong medikal na pagsusuri para sa wastong diagnosis.")
                        
                elif section_name == "mga_hakbang" and needed > 0:
                    if section_entities:
                        specific_entity = next((e for e in section_entities if "treatment" in e.lower() or "therapy" in e.lower() or "exercise" in e.lower()), section_entities[0])
                        sentences.append(f"Ang {specific_entity} ay makatutulong sa pagpapabuti ng kalagayan.")
                    else:
                        sentences.append("Ang mga partikular na hakbang na ito ay makatutulong sa pagpapabuti ng kalagayan.")
                        
                elif section_name == "pangangalaga" and needed > 0:
                    if section_entities:
                        specific_entity = next((e for e in section_entities if "monitor" in e.lower() or "track" in e.lower() or "observe" in e.lower()), section_entities[0])
                        sentences.append(f"Ang {specific_entity} ay mahalaga para sa epektibong pangangalaga.")
                    else:
                        sentences.append("Ang regular na pagsubaybay at pangangalaga ay mahalaga para sa paggaling.")
                        
                elif section_name == "pagbabago_sa_pamumuhay" and needed > 0:
                    if section_entities:
                        specific_entity = next((e for e in section_entities if "diet" in e.lower() or "nutrition" in e.lower() or "pagkain" in e.lower()), section_entities[0])
                        sentences.append(f"Ang tamang {specific_entity} ay mahalaga para sa pangmatagalang kalusugan.")
                    else:
                        sentences.append("Ang pagbabago sa istilo ng pamumuhay ay mahalaga para sa pangmatagalang kalusugan.")
            
            section_sentences[section_name] = sentences[:section_sentence_allocation[section_name]]
    
    # ENHANCEMENT 4: Compile the final summary
    all_sentences = []
    
    # Add sentences in a logical order based on document type
    if doc_type.lower() == "assessment":
        section_order = ["mga_sintomas", "kalagayan_pangkatawan", "kalagayan_mental", "aktibidad", "kalagayan_social"]
    else:
        section_order = ["pangunahing_rekomendasyon", "mga_hakbang", "pangangalaga", "pagbabago_sa_pamumuhay"]
    
    # Add sentences from each section in the defined order
    for section_name in section_order:
        if section_name in section_sentences:
            all_sentences.extend(section_sentences[section_name])
    
    # Apply transitions between sentences
    final_summary = ""
    
    if all_sentences:
        final_summary = all_sentences[0]
        
        # Transitions
        transitions = [
            "Bukod dito, ", 
            "Gayundin, ", 
            "Dagdag pa rito, ", 
            "Karagdagan sa nabanggit, ", 
            "Kasama rito, ", 
            "Sa kabilang banda, ",
            "Samantala, ", 
            "Higit pa, "
        ]
        
        for i in range(1, len(all_sentences)):
            # Choose transition and apply
            prev_content = all_sentences[i-1]
            next_content = all_sentences[i]
            
            relationship = get_semantic_relationship(prev_content, next_content)
            transition = choose_appropriate_transition(prev_content, next_content, relationship)
            
            # Ensure proper capitalization after transition
            next_sentence = next_content
            if next_sentence[0].isupper():
                next_sentence = next_sentence[0].lower() + next_sentence[1:]
            
            final_summary += f" {transition}{next_sentence}"
    else:
        # Fallback
        if doc_type.lower() == "assessment":
            final_summary = f"{subject} ay nangangailangan ng karagdagang pagsusuri."
        else:
            final_summary = "Inirerekomenda ang komprehensibong medikal na pagsusuri para sa wastong diagnosis at paggamot."
    
    # Final formatting
    final_summary = re.sub(r'\s+', ' ', final_summary)  # Fix multiple spaces
    final_summary = re.sub(r'\s([,.;:])', r'\1', final_summary)  # Fix spacing before punctuation
    
    # Ensure first letter is capitalized
    if final_summary and final_summary[0].islower():
        final_summary = final_summary[0].upper() + final_summary[1:]
    
    # Ensure proper ending punctuation
    if final_summary and not final_summary[-1] in ['.', '!', '?']:
        final_summary += '.'
    
    return final_summary

def create_simple_summary(doc, sections, doc_type="assessment"):
    """Create a simple summary as a fallback when enhanced summary generation fails."""
    # Extract main subject
    subject = extract_main_subject(doc)
    
    if doc_type.lower() == "assessment":
        if "mga_sintomas" in sections and sections["mga_sintomas"]:
            symptoms = nlp(sections["mga_sintomas"])
            symptom_entities = [ent.text for ent in symptoms.ents if ent.label_ in ["SYMPTOM", "DISEASE"]]
            if symptom_entities:
                return f"{subject} ay nagpapakita ng {symptom_entities[0]} at iba pang sintomas na nangangailangan ng pagsusuri."
            else:
                return f"{subject} ay nagpapakita ng mga sintomas na nangangailangan ng medikal na atensyon."
        else:
            return f"{subject} ay nangangailangan ng komprehensibong pagsusuri."
    else:  # Evaluation
        if "pangunahing_rekomendasyon" in sections and sections["pangunahing_rekomendasyon"]:
            recommendations = nlp(sections["pangunahing_rekomendasyon"])
            referral_entities = [ent.text for ent in recommendations.ents if ent.label_ == "HEALTHCARE_REFERRAL"]
            if referral_entities:
                return f"Inirerekomenda ang pagkonsulta sa {referral_entities[0]} para sa karagdagang pagsusuri at paggamot."
            else:
                return "Inirerekomenda ang pagkonsulta para sa karagdagang pagsusuri at paggamot."
        else:
            return "Kinakailangan ng karagdagang mga hakbang para sa optimal na pangangalaga."

# Define key terms for each section type - EXPANDED SIGNIFICANTLY
section_key_terms = {
    "mga_sintomas": [
        # General symptom terms
        "sintomas", "sakit", "kondisyon", "nararamdaman", "nagpapakita", 
        "dumaranas", "nakakaranas", "hirap", "problema",
        "symptoms", "condition", "experiencing", "suffering from", "presenting with",
        
        # Specific symptoms and manifestations
        "nananakit", "kirot", "pamamanhid", "pamumula", "pangangati", "panginginig",
        "pananakit", "panghihina", "pamamaga", "pamamanas", "pagkapagod", "pagod",
        "pagkahilo", "hirap huminga", "pag-ubo", "ubo", "lagnat", "sipon",
        "pagsusuka", "panunuyo", "kombulsyon", "walang gana kumain",
        
        # Pain descriptions
        "masakit", "sumasakit", "mabigat ang pakiramdam", "matigas",
        "naiirita", "malamig", "mainit", "burning", "stabbing", "throbbing",
        
        # Physical manifestations
        "pasa", "sugat", "paltos", "galos", "hiwa", "bukol", "cyst", "lumaking parte",
        "bloating", "swelling", "tenderness", "inflammation", "bleeding", "discharge",
        
        # Severity indicators
        "matinding", "malubhang", "banayad na", "katamtamang", 
        "severe", "moderate", "mild", "persistent", "chronic", "acute", "intermittent",
        
        # Frequency terms
        "paulit-ulit", "madalas", "paminsan-minsan", "regular", "occasional",
        "frequent", "daily", "nightly", "weekly", "constant", "recurring",
        
        # Duration indicators
        "matagal na", "ilang araw na", "ilang linggo na", "simula pa", 
        "ongoing", "recent onset", "long-standing", "new", "sudden"
    ],
    
    "kalagayan_pangkatawan": [
        # Basic physical terms
        "pisikal", "katawan", "lakas", "pangangatawan", "physical", "body",
        "pangkalahatan", "general", "overall", "strength", "condition",
        
        # Physical state descriptions
        "kalusugan", "kundisyon", "pakiramdam", "estado", "state", "profile",
        "malusog", "mabuti", "mahina", "maayos", "mahirap", "fragile", "frail",
        
        # Body systems
        "cardiovascular", "respiratory", "pulmonary", "musculoskeletal", 
        "digestive", "circulatory", "nervous system", "neurological",
        "immune", "endocrine", "integumentary", "skeletal", "sistema",
        
        # Vital signs and measurements
        "vital signs", "sukat", "timbang", "height", "weight", "temperature", 
        "blood pressure", "pulse", "heart rate", "respiratory rate", "presyon", 
        "oxygen saturation", "oxygen level", "BMI", "lab results",
        
        # Body parts
        "ulo", "dibdib", "puso", "baga", "tiyan", "balakang", "braso", "kamay",
        "hita", "tuhod", "binti", "paa", "likod", "spinal", "vertebral", 
        "atay", "bato", "pancreas", "bituka", "sikmura", "utak", "joints",
        
        # Physical abilities
        "balance", "koordinasyon", "coordination", "lakas", "strength", 
        "flexibility", "range of motion", "dexterity", "mobility", "stability",
        "stamina", "endurance", "pagkilos", "paggalaw", "movement", "restriction",
        
        # Physical conditions
        "overweight", "underweight", "obese", "malnourished", "dehydrated",
        "hypertensive", "hypotensive", "febrile", "afebrile", "cachectic"
    ],
    
    "kalagayan_mental": [
        # Cognitive terms
        "pag-iisip", "memorya", "cognitive", "mental", "isip", "memory",
        "pag-unawa", "comprehension", "understanding", "awareness", "orientation",
        "concentration", "attention", "focus", "decision-making", "judgment",
        "reasoning", "perception", "cognition", "processing", "thinking",
        "mentality", "consciousness", "alertness", "coherence", "confusion",
        
        # Mental state descriptions
        "pagkalito", "disoriented", "forgetful", "nakakalimot", "nalilito",
        "hindi matandaan", "alert", "oriented", "malinaw ang pag-iisip",
        "lucid", "confused", "disoriented", "aware", "unaware",
        
        # Emotional terms
        "kalungkutan", "depression", "lungkot", "pagkabalisa", "anxiety",
        "worry", "stress", "galit", "anger", "takot", "fear", "irritable",
        "malungkot", "nag-aalala", "balisa", "masaya", "happy", "hopeful",
        "hopeless", "kawalan ng pag-asa", "frustration", "disappointment",
        
        # Psychological conditions
        "dementia", "Alzheimer's", "cognitive decline", "psychiatric",
        "psychological", "mental health", "mood disorder", "anxiety disorder",
        "depressive disorder", "trauma", "PTSD", "schizophrenia", "bipolar",
        
        # Behavioral manifestations
        "pag-uugali", "behavior", "agitation", "agitated", "withdrawal",
        "pag-iwas", "isolation", "restlessness", "pagod ang isip",
        "irritability", "emotional lability", "mood swings", "aggression",
        "apathy", "detachment", "disinterest", "pagkawala ng interes"
    ],
    
    "aktibidad": [
        # Daily activities
        "gawain", "aktibidad", "activity", "daily", "routine", "schedule",
        "araw-araw", "pang-araw-araw", "tasks", "chores", "regular",
        "obligations", "responsibilidad", "responsibilities", "function",
        
        # Mobility & movement
        "paglalakad", "walking", "paggalaw", "mobility", "pagkilos", "movement",
        "travel", "commuting", "transferring", "standing", "sitting", "lying down",
        "pag-akyat", "climbing", "pagbaba", "descending", "stairs", "steps",
        
        # ADLs (Activities of Daily Living)
        "ADL", "self-care", "pangangalaga sa sarili", "hygiene", "kalinisan",
        "bathing", "pagliligo", "dressing", "pagbibihis", "pagkain", "feeding", 
        "toileting", "grooming", "pag-aayos", "sleeping", "pagtulog",
        
        # IADLs (Instrumental Activities of Daily Living)
        "IADL", "pagmamaneho", "driving", "paggamit ng telepono", "phone use",
        "pagluluto", "cooking", "paglilinis", "cleaning", "pagbabayad", "finances",
        "pamimili", "shopping", "pamamahala ng gamot", "medication management",
        
        # Assistive devices
        "tungkod", "cane", "walker", "wheelchair", "upuan de gulong", "andador",
        "assistive device", "tulong na kasangkapan", "mobility aid", "crutches",
        "saklay", "hospital bed", "ambulatory aid", "supportive device",
        
        # Activity limitations
        "limitado", "limited", "restrictions", "hindi magawa", "unable to",
        "nahihirapan", "difficulty with", "dependence", "dependent", "need assistance",
        "nangangailangan ng tulong", "supervision", "bantay", "difficulty performing"
    ],
    
    "kalagayan_social": [
        # Relationships
        "pamilya", "family", "asawa", "spouse", "anak", "children", "apo", "grandchildren",
        "magulang", "parents", "kaibigan", "friends", "kamag-anak", "relatives", 
        "kapit-bahay", "neighbors", "katrabaho", "co-workers", "relationship",
        "relasyon", "suporta", "support", "support system", "network",
        
        # Social environment
        "pamayanan", "community", "kapitbahayan", "neighborhood", "church", "simbahan", 
        "social circle", "social network", "social activities", "social groups",
        "senior center", "church group", "community center", "volunteer group",
        
        # Social interaction patterns
        "pakikisalamuha", "interaction", "pakikipag-usap", "communication",
        "pakikilahok", "participation", "engagement", "involvement", 
        "socialization", "pakikisama", "getting along", "collaborative",
        
        # Social issues
        "isolation", "pagkakahiwalay", "social isolation", "loneliness", "kalungkutan",
        "withdrawal", "pag-iwas", "social stigma", "discrimination", "abandonment",
        "neglect", "abuse", "pang-aabuso", "household dynamics", "family conflict",
        
        # Social support descriptions
        "assistance", "tulong", "supportive", "matulungin", "caregiver", "tagapag-alaga",
        "family support", "emotional support", "financial support", "sustento",
        "help", "aid", "resource", "provider", "social service", "government aid",
        
        # Living arrangements
        "living situation", "living arrangement", "household composition",
        "lives with", "resides with", "kasama sa bahay", "independent living",
        "assisted living", "nursing home", "living alone", "nag-iisa sa bahay",
        "multi-generational household", "extended family", "malawak na pamilya"
    ],
    
    "pangunahing_rekomendasyon": [
        # Recommendation phrases
        "inirerekomenda", "rekomendasyon", "iminumungkahi", "mungkahi", 
        "pinapayuhan", "payo", "ipinapayo", "nirerekomenda", "recommend", 
        "recommendation", "suggest", "advise", "advice", "proposed", "indicated",
        
        # Priority indicators
        "pangunahin", "primary", "main", "key", "essential", "important",
        "critical", "crucial", "vital", "necessary", "urgent", "immediate",
        "priority", "highest priority", "most important", "first step",
        
        # Action terms
        "kailangan", "need", "require", "must", "should", "dapat", "kinakailangan",
        "importante", "mahalagang", "necessary", "crucial", "critical", "essential",
        
        # Healthcare directives
        "referral", "konsulta", "consultation", "medical evaluation", "assessment",
        "comprehensive evaluation", "professional assessment", "specialist", 
        "dalubhasa", "eksperto", "expert opinion", "second opinion",
        
        # Conditional terms
        "kung", "if", "when", "kapag", "in case", "should", "would", "as needed",
        "as required", "as appropriate", "kung kinakailangan", "kung naaangkop",
        
        # Treatment recommendations
        "treatment", "paggamot", "therapy", "intervention", "procedure", 
        "operation", "operasyon", "surgical", "non-surgical", "medical management",
        "therapeutic", "rehabilitative", "palliative", "preventative"
    ],
    
    "mga_hakbang": [
        # Action words
        "gawin", "simulan", "isagawa", "ipatupad", "implement", "execute", "perform",
        "conduct", "undertake", "carry out", "initiate", "begin", "start", "proceed",
        "follow", "adhere to", "sundin", "sumunod", "tuparin", "execute",
        
        # Step terminology
        "hakbang", "step", "measure", "action", "procedure", "protocol", "process",
        "approach", "method", "technique", "strategy", "intervention", "tactic",
        "activity", "operation", "task", "procedure", "regimen", "course",
        
        # Treatment terms
        "treatment", "therapy", "therapeutic", "intervention", "management", 
        "administration", "application", "delivery", "regimen", "program", 
        "protocol", "procedure", "course", "plan", "schedule", "therapeutic approach",
        
        # Specific interventions
        "exercise", "ehersisyo", "physical therapy", "occupational therapy",
        "speech therapy", "rehabilitation", "pain management", "stress management",
        "cognitive therapy", "behavioral therapy", "paggamot", "therapy",
        
        # Medical procedures
        "surgery", "operasyon", "injection", "turok", "medication administration",
        "pagbibigay ng gamot", "wound care", "pangangalaga ng sugat", "dressing change",
        "assessment", "evaluation", "monitoring", "pagsubaybay", "laboratory test",
        
        # Timing indicators
        "immediate", "agaran", "promptly", "quickly", "urgent", "as soon as possible",
        "daily", "araw-araw", "weekly", "linggu-linggo", "monthly", "regular",
        "scheduled", "periodic", "intermittent", "continuous", "ongoing"
    ],
    
    "pangangalaga": [
        # Care terms
        "pangangalaga", "care", "alaga", "alalay", "assist", "support", "help",
        "aid", "pagtulong", "pagkalinga", "pag-aaruga", "alagaan", "ingatan",
        "assistance", "helping", "supporting", "maintaining", "preserving",
        
        # Care types
        "medical care", "nursing care", "supportive care", "palliative care",
        "preventive care", "rehabilitative care", "long-term care",
        "home care", "pangangalaga sa bahay", "self-care", "pangangalaga sa sarili",
        
        # Monitoring terms
        "monitor", "subaybayan", "observe", "obserbahan", "bantayan", "check",
        "assess", "watch", "pagmamasid", "observation", "assessment", "evaluation",
        "tracking", "measuring", "recording", "documentation", "reporting",
        
        # Care activities
        "feeding", "pagpapakain", "bathing", "pagliligo", "toileting", 
        "hygiene", "kalinisan", "dressing", "pagbibihis", "grooming", "pag-aayos",
        "positioning", "pagpoposisyon", "transfer", "paglilipat", "turning",
        "wound care", "pangangalaga ng sugat", "medication administration",
        
        # Caregiver references
        "caregiver", "tagapag-alaga", "caretaker", "nurse", "nars", "attendant",
        "family caregiver", "pamilyang tagapag-alaga", "professional caregiver",
        "home health aide", "nursing assistant", "healthcare provider",
        
        # Warning and safety terms
        "bantay", "watch", "track", "signs", "symptoms", "complications",
        "adverse effects", "side effects", "red flags", "warning signs",
        "deterioration", "changes", "pagbabago", "improvement", "pagbuti"
    ],
    
    "pagbabago_sa_pamumuhay": [
        # Change terminology
        "pagbabago", "change", "modification", "adjustment", "adaptation", 
        "transition", "shift", "alteration", "transformation", "conversion",
        "reforming", "restructuring", "revising", "adapting", "modifying",
        
        # Lifestyle terms
        "pamumuhay", "lifestyle", "daily life", "araw-araw na pamumuhay", 
        "way of life", "living condition", "daily routine", "karaniwang gawain",
        "habits", "ugali", "practices", "patterns", "behaviors", "pag-uugali",
        
        # Diet and nutrition
        "diet", "nutrition", "pagkain", "nutrisyon", "eating habits", "food intake",
        "nutritional needs", "dietary restriction", "meal planning", "hydration",
        "low sodium", "high protein", "diabetic diet", "heart healthy", "balanced diet",
        
        # Physical activity
        "physical activity", "exercise", "ehersisyo", "activity level", 
        "movement", "galaw", "active lifestyle", "fitness", "low impact exercise",
        "strength training", "stretching", "balance exercises", "walking program",
        
        # Sleep patterns
        "sleep", "tulog", "sleeping pattern", "sleep hygiene", "rest", "pahinga",
        "bedtime routine", "sleep schedule", "sleep quality", "insomnia management",
        
        # Stress management
        "stress management", "relaxation", "coping strategies", "meditation",
        "mindfulness", "breathing techniques", "anxiety reduction", "mental health care",
        
        # Health behaviors
        "smoking cessation", "alcohol reduction", "substance management",
        "medication adherence", "pagsunod sa gamot", "preventive care",
        "health monitoring", "self-management", "self-care", "risk reduction"
    ]
}

def summarize_section_text(section_text, section_name, max_length=350):
    """Create a concise summary of a potentially long section with proper sentence selection."""
    # Skip if the section is already short enough
    if len(section_text) <= max_length:
        return section_text
        
    # Process the section text
    doc = nlp(section_text)
    
    # Extract key sentences based on section type
    section_sentences = split_into_sentences(section_text)
    
    # Fix incomplete sentences - common issue in extracted sections
    fixed_sentences = []
    for i, sent in enumerate(section_sentences):
        # Skip empty sentences
        if not sent.strip():
            continue
            
        # Check if sentence is a fragment or starts with lowercase (likely fragment)
        if (len(sent.strip()) < 20 or sent.strip()[0].islower()) and i > 0:
            # This might be a sentence fragment - combine with previous
            if fixed_sentences:
                fixed_sentences[-1] = fixed_sentences[-1] + " " + sent
            else:
                # If no previous sentence, try to make it standalone
                if not sent.strip()[0].isupper():
                    capitalized = sent[0].upper() + sent[1:]
                    fixed_sentences.append(capitalized)
                else:
                    fixed_sentences.append(sent)
        else:
            # Ensure sentence ends with punctuation
            if not sent.strip()[-1] in ['.', '!', '?']:
                sent = sent + "."
            fixed_sentences.append(sent)
    
    # Use fixed sentences if we have any, otherwise use originals
    if fixed_sentences:
        section_sentences = fixed_sentences
    
    if len(section_sentences) <= 3:
        # If only 1-3 sentences, keep them all but truncate if needed
        summary = " ".join(section_sentences)
        if len(summary) > max_length:
            # Find natural break points for better truncation
            last_period = summary[:max_length-3].rfind('.')
            last_comma = summary[:max_length-3].rfind(',')
            last_break = max(last_period, last_comma)
            
            if last_break > max_length/2:  # Only use break point if it's reasonably far in
                summary = summary[:last_break+1] + "..."
            else:
                summary = summary[:max_length-3] + "..."
        return summary
    
    # For sections with many sentences, select the most informative ones
    
    # Extract important elements to help with sentence selection
    elements = extract_structured_elements(section_text, section_name)
    
    # Score sentences based on information content and relevance
    scored_sentences = []
    
    for i, sent in enumerate(section_sentences):
        score = 0
        sent_doc = nlp(sent)
        
        # Position score - prioritize first sentences for context
        if i == 0:
            score += 5  # First sentence is crucial for context
        elif i == 1:
            score += 3  # Second sentence often contains important details
        elif i == len(section_sentences) - 1:
            score += 2  # Last sentence may have conclusions/recommendations
        
        # Length preference - avoid very short or very long sentences
        sent_length = len(sent)
        if 40 < sent_length < 120:
            score += 2  # Ideal length
        elif 20 < sent_length <= 40:
            score += 1  # Short but acceptable
        elif sent_length <= 20:
            score -= 1  # Too short
        elif sent_length > 200:
            score -= 2  # Too long
        
        # Check if sentence begins with a capital letter (better formed)
        if sent.strip() and sent.strip()[0].isupper():
            score += 2
        
        # Entity and information density scoring
        entity_count = len([ent for ent in sent_doc.ents])
        score += min(4, entity_count)  # Up to 4 points for entities
        
        # Check for section-specific key terms
        key_terms = section_key_terms.get(section_name, [])  # Using the existing dictionary
        for term in key_terms:
            if term.lower() in sent.lower():
                score += 2
        
        # Section-specific scoring - more detailed than before
        if section_name == "mga_sintomas" or "sintomas" in section_name:
            # Prioritize sentences with clear symptom descriptions
            for ent in sent_doc.ents:
                if ent.label_ in ["DISEASE", "SYMPTOM"]:
                    score += 5  # Strong boost for symptom mentions
                elif ent.label_ == "BODY_PART":
                    score += 2
            
            # Check for severity and duration words
            severity_terms = ["matindi", "malubha", "banayad", "moderate", "mild", "severe"]
            duration_terms = ["araw-araw", "linggo", "buwan", "daily", "weekly", "years"]
            
            if any(term in sent.lower() for term in severity_terms):
                score += 3
            if any(term in sent.lower() for term in duration_terms):
                score += 2
                
        elif section_name == "kalagayan_mental" or "mental" in section_name:
            # Prioritize sentences with cognitive status descriptions
            for ent in sent_doc.ents:
                if ent.label_ == "COGNITIVE":
                    score += 5  # Strong boost for cognitive mentions
                elif ent.label_ == "EMOTION":
                    score += 4
            
            # Specific cognitive terms that are high value
            cognitive_terms = ["memorya", "kalituhan", "confusion", "nakalimutan", 
                             "hindi matandaan", "disorientation", "pagkalito"]
            
            if any(term in sent.lower() for term in cognitive_terms):
                score += 4
                
        elif section_name == "aktibidad" or "aktibidad" in section_name:
            # Make sure we have complete sentences about activities
            # Avoid sentence fragments
            if len(sent) < 30 or sent.strip()[0].islower():
                score -= 5  # Significant penalty for likely fragments
                
            activity_terms = ["gawain", "activity", "araw-araw", "limitasyon", 
                             "nahihirapan", "tulong", "assistance"]
            
            if any(term in sent.lower() for term in activity_terms):
                score += 3
                
        elif section_name == "kalagayan_pangkatawan" or "pangkatawan" in section_name:
            # Physical status terms
            for ent in sent_doc.ents:
                if ent.label_ in ["BODY_PART", "MEASUREMENT", "VITAL_SIGNS"]:
                    score += 3
                    
            physical_terms = ["pisikal", "katawan", "vital signs", "presyon", "weight"]
            if any(term in sent.lower() for term in physical_terms):
                score += 2
                
        elif section_name == "kalagayan_social" or "social" in section_name:
            # Social relationships and support
            for ent in sent_doc.ents:
                if ent.label_ in ["SOCIAL_REL", "PER"]:
                    score += 4
                    
            support_terms = ["suporta", "tulong", "pamilya", "asawa", "anak", "apo"]
            if any(term in sent.lower() for term in support_terms):
                score += 3
                
        elif section_name == "pangunahing_rekomendasyon" or "rekomendasyon" in section_name:
            for ent in sent_doc.ents:
                if ent.label_ in ["RECOMMENDATION", "HEALTHCARE_REFERRAL"]:
                    score += 5
                    
            recommendation_terms = ["inirerekomenda", "iminumungkahi", "dapat", "kailangan"]
            if any(term in sent.lower() for term in recommendation_terms):
                score += 4
                
        elif section_name == "mga_hakbang" or "hakbang" in section_name:
            for ent in sent_doc.ents:
                if ent.label_ in ["TREATMENT_METHOD", "TREATMENT", "EQUIPMENT"]:
                    score += 4
                    
            action_terms = ["gawin", "isagawa", "ipatupad", "sundin", "simulan"]
            if any(term in sent.lower() for term in action_terms):
                score += 3
                
        elif section_name == "pangangalaga" or "alaga" in section_name:
            for ent in sent_doc.ents:
                if ent.label_ in ["MONITORING", "WARNING_SIGN"]:
                    score += 4
                    
            care_terms = ["bantayan", "subaybayan", "obserbahan", "i-monitor", "ingatan"]
            if any(term in sent.lower() for term in care_terms):
                score += 3
                
        elif section_name == "pagbabago_sa_pamumuhay" or "pamumuhay" in section_name:
            for ent in sent_doc.ents:
                if ent.label_ in ["DIET_RECOMMENDATION", "FOOD"]:
                    score += 4
                    
            lifestyle_terms = ["diet", "ehersisyo", "exercise", "pagkain", "nutrition"]
            if any(term in sent.lower() for term in lifestyle_terms):
                score += 3
        
        # Store the scored sentence with its original position
        scored_sentences.append((sent, score, i))
    
    # Sort by score (highest first)
    sorted_sentences = sorted(scored_sentences, key=lambda x: x[1], reverse=True)
    
    # Select top sentences, aiming for 3 if possible
    selected_indices = []
    current_length = 0
    target_sentences = min(3, len(section_sentences))
    
    # First pass: get highest scoring sentences
    for sent, score, idx in sorted_sentences[:5]:  # Consider top 5 candidates
        if len(selected_indices) < target_sentences and current_length + len(sent) <= max_length:
            selected_indices.append(idx)
            current_length += len(sent) + 1  # +1 for space
            
    # If we don't have enough yet, try to fill with other sentences
    if len(selected_indices) < target_sentences and current_length < max_length:
        remaining = [(i, sent) for i, sent in enumerate(section_sentences) 
                    if i not in selected_indices]
        
        # Add sentences in order to maintain narrative flow
        for i, sent in sorted(remaining, key=lambda x: x[0]):
            if current_length + len(sent) <= max_length:
                selected_indices.append(i)
                current_length += len(sent) + 1
                
                if len(selected_indices) >= target_sentences:
                    break
    
    # Always include at least one sentence
    if not selected_indices and section_sentences:
        # Try first sentence (usually has context)
        first_sent = section_sentences[0]
        if len(first_sent) <= max_length:
            selected_indices.append(0)
        else:
            # Truncate if too long
            last_period = first_sent[:max_length-3].rfind('.')
            if last_period > 30:
                first_sent = first_sent[:last_period+1] + "..."
            else:
                first_sent = first_sent[:max_length-3] + "..."
            section_sentences[0] = first_sent
            selected_indices.append(0)
    
    # Sort indices to maintain original order (better narrative flow)
    selected_indices.sort()
    
    # Combine sentences in original order
    selected_sentences = [section_sentences[i] for i in selected_indices]
    
    # Final check - ensure the first sentence provides context
    if selected_sentences and not (selected_sentences[0].strip()[0].isupper()):
        # First sentence seems to be a fragment - try to fix
        if section_name == "mga_sintomas":
            selected_sentences[0] = f"Ang pasyente ay nagpapakita ng {selected_sentences[0]}"
        elif section_name == "kalagayan_pangkatawan":
            selected_sentences[0] = f"Pisikal na kalagayan: {selected_sentences[0]}"
        elif section_name == "kalagayan_mental":
            selected_sentences[0] = f"Mental na kalagayan: {selected_sentences[0]}"
        elif section_name == "aktibidad":
            selected_sentences[0] = f"Sa mga pang-araw-araw na gawain, {selected_sentences[0]}"
        elif section_name == "kalagayan_social":
            selected_sentences[0] = f"Sa social na aspeto, {selected_sentences[0]}"
    
    summary = " ".join(selected_sentences)
    
    # Final length check
    if len(summary) > max_length:
        last_period = summary[:max_length-3].rfind('.')
        if last_period > max_length/2:
            summary = summary[:last_period+1] + "..."
        else:
            summary = summary[:max_length-3] + "..."
    
    return summary

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
        
        # Lifestyle/Diet - ADDED MISSING KEYS
        "diet_changes": [],   # Diet recommendations
        "exercise": [],       # Exercise recommendations
        "lifestyle_changes": [], # Other lifestyle changes
        
        # Social
        "social_support": [], # Social support systems
        "caregivers": [],     # Caregiver information
        "living_conditions": [], # Living conditions
        
        # General
        "needs": [],          # Identified needs
        "verbs": [],          # Key action verbs
        "adjectives": [],     # Important descriptive adjectives
        "warnings": [],       # Warning signs or precautions
    }
    
    # Extract subject (main person)
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
        # ADD NEW DIET AND WARNING EXTRACTION
        elif ent.label_ == "DIET_RECOMMENDATION" and ent.text not in elements["diet_changes"]:
            elements["diet_changes"].append(ent.text)
        elif ent.label_ == "FOOD" and ent.text not in elements["diet_changes"]:
            elements["diet_changes"].append(ent.text)
        elif ent.label_ == "WARNING_SIGN" and ent.text not in elements["warnings"]:
            elements["warnings"].append(ent.text)
    
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
                    elif any(cond in pattern_type for cond in ["problema", "hirap", "sakit"]):  # FIXED
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
                
            # Create summarized versions of sections using our enhanced function
            summarized_sections = {}
            for section_name, section_content in sections.items():
                summarized_sections[section_name] = summarize_section_text(
                    section_content, 
                    section_name,
                    max_length=350  # INCREASED from 250 to allow for 3 sentences
                )
                
        except Exception as e:
            print(f"Section extraction error: {e}")
            sections = {}
            summarized_sections = {}
        
        # Generate enhanced summary that captures important details from ALL sections
        try:
            # Extract high-importance medical terms and symptoms from the text
            important_medical_info = []
            for ent in doc.ents:
                if ent.label_ in ["DISEASE", "SYMPTOM", "COGNITIVE"] and len(ent.text) > 3:
                    important_medical_info.append(ent.text)
            
            # Use our enhanced multi-section summary generation
            summary = create_enhanced_multi_section_summary(doc, sections, doc_type)
            
            # Check for important information that might be missing from the summary
            # This helps ensure specific key details are included
            for term in important_medical_info[:3]:  # Check top 3 important terms
                if term not in summary.lower() and len(term) > 3:
                    # Find which section this term belongs to
                    for section_name, section_content in sections.items():
                        if term.lower() in section_content.lower():
                            # Extract a relevant sentence containing this term
                            section_sents = split_into_sentences(section_content)
                            for sent in section_sents:
                                if term.lower() in sent.lower():
                                    # Find a good transition phrase
                                    transition = "Mahalaga ring banggitin na "
                                    # Add the information with proper context
                                    additional_detail = sent
                                    if len(additional_detail) > 150:  # Trim if too long
                                        # Find a good break point
                                        break_point = additional_detail.find(",", 100)
                                        if break_point > 0:
                                            additional_detail = additional_detail[:break_point+1]
                                    
                                    # Add to summary if not already too long
                                    if len(summary) < 400:  # Keep summary reasonable
                                        summary = summary + " " + transition + additional_detail
                                    break
            
            # Final sanity check - if summary is still too short, use fallback
            if len(summary) < 50:
                main_subject = extract_main_subject(doc)
                if doc_type.lower() == "assessment":
                    summary = f"{main_subject} ay nangangailangan ng komprehensibong pagsusuri."
                else:
                    summary = "Inirerekomenda ang pagkonsulta para sa karagdagang pagsusuri at paggamot."
                    
        except Exception as e:
            print(f"Summary generation error: {e}")
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
            "sections": sections,  # Full sections
            "summarized_sections": summarized_sections,  # Shorter versions for display
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