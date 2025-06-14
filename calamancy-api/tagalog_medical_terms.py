"""
Comprehensive Tagalog medical terms, body parts, and their variations
for preprocessing Tagalog medical texts.
"""

# Body parts with common variations
BODY_PARTS = {
    "ulo": ["head", "ulo", "bungo", "kokote"],
    "mata": ["eye", "mata", "paningin", "panginain"],
    "ilong": ["nose", "ilong", "lung"],
    "bibig": ["mouth", "bibig", "bunganga"],
    "tenga": ["ear", "tenga", "tainga", "pandinig"],
    "leeg": ["neck", "leeg", "liig"],
    "balikat": ["shoulder", "balikat"],
    "braso": ["arm", "braso", "bisig"],
    "siko": ["elbow", "siko"],
    "kamay": ["hand", "kamay", "palad"],
    "daliri": ["finger", "daliri", "tudlo"],
    "dibdib": ["chest", "dibdib", "suso"],
    "tiyan": ["abdomen", "stomach", "tiyan", "puson", "sikmura"],
    "likod": ["back", "likod", "likuran"],
    "balakang": ["hip", "balakang"],
    "tuhod": ["knee", "tuhod", "luhod"],
    "binti": ["leg", "binti", "paa"],
    "bukung-bukong": ["ankle", "bukung-bukong"],
    "paa": ["foot", "paa", "talampakan"],
    "ngipin": ["teeth", "tooth", "ngipin"],
    "labi": ["lips", "labi"],
    "dila": ["tongue", "dila"],
    "lalamunan": ["throat", "lalamunan", "nguya"],
    "baga": ["lungs", "baga"],
    "puso": ["heart", "puso"],
    "atay": ["liver", "atay"],
    "bituka": ["intestine", "bituka"],
    "bato": ["kidney", "bato"],
    "pantog": ["bladder", "pantog"],
    # Additional terms observed in texts
    "pustiso": ["denture", "pustiso", "artipisyal na ngipin"],
    "kasukasuan": ["joints", "kasukasuan", "rayuma"],
    "balat": ["skin", "balat"]
}

# Medical conditions and symptoms with extended keys
MEDICAL_CONDITIONS = {
    "alta presyon": ["hypertension", "alta presyon", "high blood", "mataas na presyon"],
    "diyabetis": ["diabetes", "diyabetis", "matamis na dugo"],
    "angina": ["angina", "chest pain", "pananakit ng dibdib"],
    "atake sa puso": ["heart attack", "atake sa puso", "myocardial infarction"],
    "atake sa utak": ["stroke", "atake sa utak", "brain attack"],
    "rayuma": ["arthritis", "rayuma", "pamamaga ng kasukasuan"],
    "paghinga": ["breathing", "paghinga", "huminga", "nahihirapan huminga"],
    "pamamaga": ["swelling", "pamamaga", "namamaga", "mamaga"],
    "kirot": ["pain", "kirot", "masakit", "sumasakit", "sakit"],
    # New condition keys observed in sample texts:
    "insomnia": ["insomnia", "hindi makatulog", "hirap matulog", "paghihirap sa tulog"],
    "dysphagia": ["dysphagia", "hirap lumunok", "mahirap lunukin"],
    "dehydration": ["dehydration", "kakulangan sa tubig", "tuyot", "nauuhaw"],
    "malnutrition": ["malnutrition", "pagkawala ng timbang", "pagbabawas ng timbang", "underweight"],
    "aspiration pneumonia": ["aspiration pneumonia", "panghihigop ng laman", "nalunok", "pneumonia"],
    "urinary tract infection": ["urinary tract infection", "UTI", "impeksyon sa pantog", "infection sa pantog"]
}

# Cognitive and emotional terms with additional variations
COGNITIVE_EMOTIONAL_TERMS = {
    "nakalilito": ["confused", "nakalilito", "nalilito", "pagkalito"],
    "malungkot": ["depressed", "sad", "malungkot", "kalungkutan", "lungkot"],
    "nag-aalala": ["worried", "nag-aalala", "alalahanin", "nababahala"],
    "pagkabalisa": ["anxiety", "pagkabalisa", "balisa", "takot"],
    "pagod": ["tired", "fatigue", "pagod", "napapagod", "kapaguran"],
    "nangangatal": ["tremor", "shaking", "nangangatal", "nanginginig", "panginginig"],
    # Additional terms from the samples:
    "pagkalimot": ["memory loss", "pagkawala ng memorya", "pagkalimot", "limot"],
    "frustration": ["frustration", "nainis", "galit", "pagkairita"]
}

# Mobility and functional terms with added variations
MOBILITY_TERMS = {
    "paglalakad": ["walking", "paglalakad", "lumakad", "naglalakad"],
    "pag-upo": ["sitting", "pag-upo", "umupo", "nakaupo"],
    "pagtayo": ["standing", "pagtayo", "tumayo", "nakatayo"],
    "pagakyat": ["climbing", "pagakyat", "umakyat", "pag-akyat ng hagdan"],
    "assistive device": ["assistive device", "walker", "wheelchair", "tungkod", "saklay"],
    "pagkahulog": ["fall", "pagkahulog", "nabagsak", "natumba"]
}

# Additional communication or speech terms (optional, based on sample texts)
SPEECH_TERMS = {
    "pananalita": ["speech", "pananalita", "pagbigkas", "mga salita"],
    "pagkakaroon ng problema sa pananalita": ["speech difficulties", "hirap magsalita", "pagkakahirapan sa pagsasalita"]
}

# Disease progression terms remain the same, possibly add 'stable'
DISEASE_PROGRESSION = {
    "bumuti": ["improved", "getting better", "bumuti", "gumaling", "umunlad"],
    "lumala": ["worsened", "getting worse", "lumala", "sumama", "bumagsak"],
    "stable": ["stable", "walang pagbabago", "steady", "habang-tenga"]
}

# Common phrases in assessments and evaluations
ASSESSMENT_PHRASES = [
    "ayon sa pasyente", "nakipanayam", "naobserbahan", 
    "sinabi niya", "ayon sa kaniya", "nagsabi na", "nagsasabing",
    "napansin ko", "sinusuri ko", "pagmamasid ko", "pagsusuri ko"
]

EVALUATION_PHRASES = [
    "inirerekomenda ko", "iminumungkahi ko", "gumawa ako ng", 
    "tinuruan ko", "pinayuhan ko", "ipinaliwanag ko", 
    "ipinapayo ko", "dapat", "kailangan", "inaayos ko"
]

# Common connectors in Tagalog that might indicate sentence breaks in long sentences
CONNECTING_PHRASES = [
    'pero', 'subalit', 'ngunit', 'datapwat',
    'dahil', 'sapagkat', 'dahilan sa',
    'kung kaya’t', 'kaya naman', 'kaya',
    'bukod dito', 'dagdag pa', 'bilang dagdag'
]

# Define a list structure for all terms to be used with various algorithms
def get_all_medical_terms():
    all_terms = []
    
    # Add all variations from all categories
    for category in [BODY_PARTS, MEDICAL_CONDITIONS, COGNITIVE_EMOTIONAL_TERMS, MOBILITY_TERMS, DISEASE_PROGRESSION, SPEECH_TERMS]:
        for term_list in category.values():
            all_terms.extend(term_list)
    
    # Return unique terms
    return list(set(all_terms))

def get_assessment_evaluation_examples():
    """Return assessment-evaluation pairs for training and testing"""
    return [
        {
            "assessment": "Si Tatay ay isang 78-anyos na lalaki na nakatira sa kanyang anak. Napansin kong nangangatal ang kanyang mga kamay habang kumakain. Akala niya daw ay dahil sa pagtanda, pero napansin din ng anak na lumalala ito. Pabagsak din siya maglakad at nahihirapan sa balanse. Ang pinakahuli niyang daing ay masakit ang kanyang kanang balikat. Tapos, ang mga kuko niya ay mahahaba at madumi na dapat palalim at linisin. Sinabi niya na handa pa siyang magtrabaho pero wala na raw siyang pension at pera pambili ng gamot. Kailangan din niya ng gatas at tinapay."
        },
        {
            "assessment": "Si Nanay ay 80-anyos na babae na hirap sa paglalakad at pag-upo. Palagi niyang sinasabi na mabigat ang kanyang katawan at nanghihina siya. Gumagamit siya ng assistive device para makapaglakad. Napansin ko rin na naduduwal siya pagkatapos kumain at hindi siya masyadong kumakain. Malaki ang ibinaba ng kanyang timbang. Sabi niya, mainit daw sa bahay at gusto niyang magpahangin sa labas araw-araw. Mas gusto niyang kasama ang kanyang apo kapag lumalabas."
        },
        {
            "evaluation": "Dahil sa lamig at init na nararanasan ni Nanay, inirekomenda ko ang pagsusuot ng mga komportableng damit depende sa klima. Para naman sa kanyang problema sa paningin, kailangan niyang magpatingin sa opthalmologist para sa tamang reseta ng salamin. Siniguro ko rin na may sapat siyang pag-unawa sa tamang pag-inom ng kanyang gamot na pampababa ng dugo. Inayos ko rin ang mga kuko niyang mahahaba na binawasan ko. Nirekomendasyon ko rin na patuloy na gawin ang mga pang-araw-araw na gawain nang may tulong ng kanyang anak."
        },
        {
            "evaluation": "Dahil sa pagkahina ni Lolo, inirekomenda ko ang pagkakaroon ng regular na aktibidad katulad ng paglalakad sa umaga. Ipinaliwanag ko rin sa kanyang anak na dapat siyang paiinumin ng sapat na tubig sa buong araw. Para naman sa nahihirapang pagtulog, inirerekomenda kong iwasan ang pag-inom ng kape sa gabi at magkaroon ng routine sa pagtulog. Binigyan ko siya ng observation para sa pagmamanman ng pagbaba ng kanyang timbang, at sinabihan ko siyang bawasan ang pagkain ng mga maalat upang maiwasan ang pagtaas ng blood pressure."
        }
    ]

def get_section_keywords():
    """Return keywords for different document sections"""
    return {
        "kalagayan_pangkatawan": [
            "malakas", "mahina", "hirap", "assistive", "paglalakad", "pag-upo", 
            "nangangatal", "pabagsak", "pagkatumba", "balanse", "lumakad", "naglalakad"
        ],
        "mga_sintomas": [
            "masakit", "sumasakit", "kirot", "daing", "malabo", "kuko", 
            "naduduwal", "nagsusuka", "matalas", "panginginig", "sakit"
        ],
        "pangangailangan": [
            "kailangan", "pangangailangan", "pension", "pera", "gatas", 
            "tinapay", "mainit", "magpahangin", "araw", "isama", "apo"
        ],
        "pagbabago": [
            "ngayon", "naging", "pagbuti", "pagkatapos", "matapos", 
            "pagbabago", "bumuti", "lumala", "napabuti"
        ],
        "mga_hakbang": [
            "ginawa", "isinagawa", "tinulungan", "inilagay", "binigyan", 
            "pinakita", "nagturo", "nagamot", "nilinisan"
        ],
        "rekomendasyon": [
            "dapat", "kailangan", "inirerekumenda", "iminumungkahi", 
            "makabubuting", "mabuting", "magsagawa", "iwasan"
        ]
    }
