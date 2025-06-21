import re
from nlp_loader import nlp
from entity_extractor import extract_structured_elements, get_entity_section_confidence
from text_processor import split_into_sentences
from context_analyzer import detect_oral_medication_context, get_contextual_relationship

def extract_sections_improved(sentences, doc_type="assessment"):
    """Extract and categorize sections with improved handling of complete sentences."""
    print(f"Extracting sections for {doc_type}, {len(sentences)} sentences")
    
    # Process all sentences with Calamancy NLP first
    try:
        sentence_docs = [nlp(sentence) for sentence in sentences]
    except Exception as e:
        print(f"Error processing sentences: {e}")
        sentence_docs = []
    
    # Define section patterns for more precise matching
    section_patterns = {
        "mga_sintomas": [
            r'(nagsimulang|nakakaranas|dumaranas|nakakaramdam) (ng|sa) (sakit|pananakit|sintomas)',
            r'(nagpapakita|nagkaroon|nakaranas) (ng|sa) (mga sintomas|kondisyon)',
            r'(nahihirapan|nahihirapang|hirap) (siyang|siya|na) (huminga|lumunok|matulog)',
            r'(dumaranas|nararamdaman|nakakaramdam) (niya|nila|nya|ko|ng) (pananakit|kirot)',
            r'sumasakit ang (kanyang|kaniyang|ulo|tiyan|dibdib|likod)',
            r'(binabangungot|pabalik-balik na panaginip)',
            r'madalas (siya|siyang|silang) (nahihilo|nasusuka|naduduwal)',
            r'(nagtatae|constipated|hirap dumumi)',
            r'may madalas na (ubo|sipon|lagnat)',
            r'nagkakaroon ng (acid reflux|heartburn)',
            r'bumaba ang (timbang|gana sa pagkain|appetito)',
            r'(hindi|di) makontrol ang (pag-ihi|pagdumi)',
            r'hirap (siya|siyang) mag-(lakad|pasok sa banyo|upo)',
            r'pabalik-balik na (sakit|kirot|pananakit)',
            r'nagpapahirap sa (kanya|kaniyang) (paglalakad|pag-upo)',
            r'mabilis (mapagod|mahapo)',
            r'nararamdaman (niyang|niya na) mahina ang (kanyang|kaniyang) (katawan)'
        ],
        
        "kalagayan_pangkatawan": [
            r'ang (kanyang|kaniyang|kanilang) pisikal na (kondisyon|kalagayan)',
            r'sa terms ng (pisikal na|physical) (lakas|kalagayan)',
            r'(nagpapakita|nagpapamalas) ng (kahinaan|kahinaang) (pisikal|sa katawan)',
            r'(hindi|di) stable ang (kanyang|kaniyang) (paglakad|balanse)',
            r'(mababa|mataas) ang (kanyang|kaniyang) (blood pressure|presyon|heart rate)',
            r'lumalala ang (kahinaan|panghihina) ng (kanyang|kaniyang) (muscles|kalamnan)',
            r'(nahihirapan|hirap|nahihirapang) (siya|siyang) (tumayo|bumangon|gumalaw)',
            r'(nangangailangan|kailangan) (niya|niyang) ng (tulong|suporta) sa paglalakad',
            r'limited ang (range of motion|movement|galaw) ng (kanyang|kaniyang) joints',
            r'(hirap|nahihirapan|nahihirapang) sa mga (hagdanan|uneven surfaces|hindi patag)',
            r'ang (kanyang|kaniyang) (strength|lakas) sa (itaas na|ibabang) parte ng katawan',
            r'(naaapektuhan|apektado) ang (kanyang|kaniyang) (balanse|coordination)',
            r'(vital signs|temperature|BP|heart rate|respiratory rate) (are|ay|is)',
            r'(mahina|malakas) ang (kanyang|kaniyang) (upper body|lower body)',
            r'(may|merong) (difficulty|kahirapan) sa (pagkilos|paggalaw)',
            r'(may|meron) siyang (edema|pamamaga) sa (kanyang|kaniyang)',
            r'ang (kanyang|kaniyang) (physical condition|weight|timbang) ay'
        ],
        
        "kalagayan_mental": [
            r'(nagpapakita|nagpapahiwatig) ng (signs|sintomas) ng (depression|anxiety|dementia)',
            r'(kalimutan|nakakalimutan|nalilimutan) (niya|nila|nya) (kung|ang)',
            r'(nalilito|confused|naguguluhan) (siya|sila) (tungkol sa|kapag|sa)',
            r'(nag-aalala|worried|concerned) (siya|siyang|sila) (tungkol sa|dahil sa) (kanyang|kaniyang) (emosyon|damdamin|mental health)',
            r'(bumaba|tumaas) ang (kanyang|kaniyang) (mood|disposition|estado ng isip)',
            r'(nagiging|naging) (iritable|mainitim ang ulo|short-tempered)',
            r'mental health (issues|concerns|problems)',
            r'hindi (niya|nya|nila) (matandaan|maalala) ang (kanyang|kaniyang)',
            r'(nagpapakita|nagpapamalas) ng (anxiety|depression|lungkot|kalungkutan)',
            r'(nagbabago-bago|unstable) ang (kanyang|kaniyang) (emosyon|damdamin)',
            r'(nahihirapan|hirap) (siyang|siyang) mag-(focus|concentrate)',
            r'(nababalisa|nag-aalalala|worried) (siya|siyang) lagi',
            r'(nagiging|naging) (defensive|agitated|irritable) (siya|siyang)',
            r'may cognitive (impairment|decline|deterioration)',
            r'(bumababa|lumalala) ang (kanyang|kaniyang) kakayahang (mag-isip|magdesisyon)',
            r'(may|nagpapakita ng|nagpapahiwatig ng) confusion (siya|sila)',
            r'(feeling|nakakaramdam ng) (hopeless|worthless|helpless|walang halaga)',
            # More specific mental health patterns
            r'(nagpapakita|nagpapahiwatig) ng (signs|sintomas) ng (depression|anxiety|dementia)',
            r'(kalimutan|nakakalimutan|nalilimutan) (niya|nila|nya) (kung|ang)',
            r'(nalilito|confused|naguguluhan) (siya|sila) (tungkol sa|kapag|sa)',
            # Make worried/concerned patterns more specific to mental health concerns
            r'(nag-aalala|worried|concerned) (siya|siyang|sila) (tungkol sa|dahil sa) (kanyang|kaniyang|sarili|pag-iisip|memorya|kalungkutan|takot)',
            r'(bumaba|tumaas) ang (kanyang|kaniyang) (mood|disposition|estado ng isip)',
            r'(nagiging|naging) (iritable|mainitim ang ulo|short-tempered)',
            # Make this more specific 
            r'mental health (issues|concerns|problems)',
            r'hindi (niya|nya|nila) (matandaan|maalala) ang (kanyang|kaniyang)',
            # Make pattern more specific to emotional states
            r'(nagpapakita|nagpapamalas) ng (anxiety|depression|lungkot|kalungkutan|mood swings|pagbabago ng emosyon)',
            r'(nagbabago-bago|unstable) ang (kanyang|kaniyang) (emosyon|damdamin)',
            r'(nahihirapan|hirap) (siyang|siyang) mag-(focus|concentrate)',
            r'(nababalisa|nag-aalalala|worried) (siya|siyang) lagi',
            r'(nagiging|naging) (defensive|agitated|irritable) (siya|siyang)',
            r'may cognitive (impairment|decline|deterioration)',
            r'(bumababa|lumalala) ang (kanyang|kaniyang) kakayahang (mag-isip|magdesisyon)',
            r'(may|nagpapakita ng|nagpapahiwatig ng) confusion (siya|sila)',
            r'(feeling|nakakaramdam ng) (hopeless|worthless|helpless|walang halaga)'
        ],
        
        "aktibidad": [
            r'(kailangan|nangangailangan) (niya|niyang|siya) ng (tulong|assistance) sa (pagligo|pagbibihis|pagkain)',
            r'(sa|tungkol sa) (kanyang|kaniyang) (activities of daily living|ADLs|pang-araw-araw na gawain)',
            r'(kaya|hindi) (niya|niyang) (gawin|isagawa) ang (normal|basic) (na )?(activities|gawain)',
            r'(hirap|nahihirapan|nahihirapang) (siya|siyang) mag-(ligo|bihis|kain|lakad|akyat)',
            r'(kailangan|nangangailangan) ng (supervision|bantay|gabay) sa (CR|bathroom|banyo)',
            r'(mahirap|challenging) para sa (kanya|kaniya) ang (paggamit|paggawa) ng',
            r'(hindi|di) na (makagamit|makaligo|makapunta|makabangon) nang mag-isa',
            r'(nangagailangan|kailangan) ng (assistive devices|mobility aids|tulong)',
            r'(iniiwasan|umiiwas) (na|siyang) gumamit ng (hagdanan|stairs)',
            r'(gumagamit|umaasa) (siya|siyang) ng (wheelchair|walker|cane|tungkod)',
            r'(kailangan|nangangailangan) ng (modified|assistive) (equipment|devices)',
            r'(mahirap|challenging) para sa (kanya|kaniya) ang (household tasks|gawaing-bahay)',
            r'(nawala|bumaba) ang (kanyang|kaniyang) (independence|kakayahang gumalaw)',
            r'(may|meron siyang) limitations sa (pagbibiyahe|transportation|travel)',
            r'(natigil|huminto) (siya|na siya|siyang) sa (pagpunta|pagsali) sa',
            r'hindi na (siya|siyang) (nag|nakaka|nakakasali) sa (church|social activities)'
        ],
        
        "kalagayan_social": [
            r'(tungkol sa|about) (kanyang|kaniyang) (social support|social network)',
            r'(nakikitungo|nakikisalamuha) (siya|siyang) sa (kaniyang|kanyang) (pamilya|friends)',
            r'(bihira|madalang|regular) (siyang|siya|silang) (bumibisita|dumalaw|makipagkita)',
            r'(umaasa|dependent|nakadepende) (siya|siyang) sa (kanyang|kaniyang) (pamilya|asawa|anak)',
            r'(naninirahan|nakatira) (siya|siyang|sila) kasama ang (kanyang|kaniyang)',
            r'(nag-iisa|mag-isa|isolated) (siya|siyang) (nakatira|namumuhay)',
            r'(nahihirapan|hirap) (siyang|siya) makisalamuha sa (ibang tao|kapwa|komunidad)',
            r'(may tension|may alitan|strained relationship) sa (kanyang|kaniyang) (pamilya|anak)',
            r'(nararamdaman|feeling) (niya|niyang) (hiniwalayan|inabandona|iniwanan)',
            r'financial (concerns|issues|problems) sa (kanyang|kaniyang) (pamilya)',
            r'(nawala|nabawasan) ang (kanyang|kaniyang) (social contacts|pakikisalamuha)',
            r'(nababawasan|humihina) ang (supportive|suportang) (network|komunidad)',
            r'(hirap|nahihirapan) (siyang|siya) mag-adjust sa (bagong|new) environment',
            r'(generational gap|pagkakaiba ng edad) sa (kanyang|kaniyang) (pamilya|household)',
            r'(nakikilala|nakikita) bilang (burden|pabigat) sa (kanyang|kaniyang) (pamilya)',
            r'(feelings of|nararamdamang) (isolation|pagkakalayo|disconnection)',
            r'(nawala|nabawasan) ang (kanyang|kaniyang) (social role|papel sa lipunan)'
        ],
        # NEW SECTION: Medical History
        "medical_history": [
            # Specific medical history patterns
            r'(medical history|history ng sakit|medical background|nakaraang karamdaman)',
            r'(diagnosed|na-diagnose|diagnosed with|na-diagnose na may) (.*)',
            r'(may sakit s[ai]|may|nakakaranas ng|diagnosed with) (diabetes|hypertension|cancer|stroke|heart disease|altapresyon)',
            r'(history|kasaysayan) (ng|ni|niya|nila) (heart attack|stroke|surgery|operation)',
            r'((siya|siyang|sila) ay may|mayroong|meron (siyang|siya|silang)) (diabetes|hypertension|asthma)',
            r'(chronic|long-term|pangmatagalan) (na )?(condition|illness|sakit)',
            r'(controlled|hindi kontrolado|controlled with|managed with) (gamot|medication)',
            r'(previous|dati|nakaraan|dating) (surgery|operasyon|hospital admission|pagkakaospital)',
            r'(family history|history sa pamilya|genetic|hereditary)',
            r'(allerg(y|ies)|allergy sa|allergic reactions|reaksyon sa)',
            r'(tinanong|kinuha|inalam) (ko|namin) (ang|tungkol sa) (kanyang|kaniyang) (medical history|kasaysayan ng kalusugan)',
            r'(ayon sa|according to|base sa) (kanyang|kaniyang|kanilang) (medical records|record|medical charts?)',
            r'(previous|dati|dating) (stroke|heart attack|cardiovascular event|myocardial infarction)',
            r'(comorbid condition|comorbidity|multiple conditions|diagnosed din with)'
        ],
        # PAIN & DISCOMFORT
        "pain_discomfort": [
            r'(sumasakit|masakit|nakakaramdam ng sakit) (sa|ang) (kanyang|kaniyang)',
            r'(nararamdaman|nakakaramdam|dumaranas) (niya|nila|nya|ko|ng) (pananakit|kirot)',
            r'(chronic|acute|matinding|tuloy-tuloy) (pain|sakit|pananakit)',
            r'(pain|sakit) (scale|intensity|level)',
            r'(rated|inireyt|sinabi|described) (niya|niyang|nila) (ang|na ang) pain',
            r'(10|sampung) point scale',
            r'(intermittent|pabalik-balik|periodic|occasional|paulit-ulit) (na)? pain',
            r'(radiating|referred|lumalipat|kumakalat) (na)? pain',
            r'(discomfort|kawalan ng ginhawa|hindi kumportable|uncomfortable)',
            r'(throbbing|burning|shooting|stabbing|sharp|dull) pain',
            r'(matigas|sensitive|tender|namamaga) (kapag|when) (pressed|pinipindot|hinihipo)',
            r'(hindi|di) (makatulog|makagalaw|makalakad|makapaghinga) dahil sa sakit',
            r'(affected|nakakaapekto|naaapektuhan) (ang|sa) (kanyang|kaniyang) (quality of life|pamumuhay)',
            r'(lumalala|lumalalang|sumisingkit) (ang|yung) (pananakit|kirot|sakit)',
            r'(gumiginhawa|lumilinaw|bumubuti) (ang|yung) (pananakit|kirot|sakit)',
            r'(pain medications?|pain relievers?|pampatanggal ng sakit)',
            r'(joint pain|muscle pain|sakit ng kalamnan|sakit ng kasukasuan)',
            r'(cramping|paninigas ng kalamnan|pulikat)',
            r'(fibromyalgia|neuropathic pain|nerve pain)',
            r'(headache|migraine|sakit ng ulo)',
            r'(stomach pain|abdominal pain|sakit ng tiyan)',
            r'(back pain|lower back pain|sakit ng likod)',
            r'(stretching|massage|hot compress|ice pack) (for|para sa) (pain|sakit)'
        ],

        # HYGIENE & SELF-CARE
        "hygiene": [
            r'(personal hygiene|personal care|kalinisan ng sarili)',
            r'(bathing|pagliligo|paliligo|naliligo|maligo) (routines?|habits?|practices?)',
            r'(nahihirapan|hirap|nahihirapang) (siya|siyang) (maligo|maglinis|mag-ayos)',
            r'(brushing teeth|pagsesepilyo|oral hygiene|kalinisan ng bibig)',
            r'(washing|paglilinis|paghuhugas) (ng|sa) (kamay|mukha|face|hands)',
            r'(independent|needs assistance|kailangan ng tulong) (sa|in) (grooming|hygiene|self-care)',
            r'(maintenance|pagpapanatili) (ng|sa) (personal|sariling) cleanliness',
            r'(incontinence care|pangangalaga sa incontinence)',
            r'(changing clothes|pagpapalit ng damit|bihis)',
            r'(toileting|paggamit ng banyo|pag-CR)',
            r'(shaving|ahit|pag-aahit|nail care|hair care|pangangalaga ng buhok)',
            r'(regular|araw-araw|daily|weekly|lingguhan|monthly|buwanan) (routine|gawain)',
            r'(self-neglect|pagpapabaya sa sarili|hindi nag-aalaga ng sarili)',
            r'(hygiene problems|issues sa kalinisan|poor hygiene|mahinang kalinisan)',
            r'(assistance|tulong|supervision) (with|sa) (sa|sa mga|sa kanyang) (bathing|toileting)',
            r'(body odor|amoy|hindi magandang amoy|malansang amoy)',
            r'(clean clothes|malinis na damit|papalitan ng damit)',
            r'(ability to|kakayahang) (maintain|panatilihin) (cleanliness|kalinisan)',
            r'(adaptive equipment|shower chair|grab bars) (for|para sa) (bathing|pagliligo)',
            r'(proper|improper) (hygiene|kalinisan) (practices|gawi|habits|nakagawian)'
        ],
    }
    
    # Define section keywords with expanded terms for better matching
    section_keywords = {
        "mga_sintomas": [
            # Existing keywords
            "sintomas", "sakit", "nararamdaman", "sumasakit", "masakit", "kirot", 
            "nagpapakita", "kondisyon", "lumalala", "bumubuti", "symptoms",
            "nahihirapan", "dumaranas", "nakakaramdam", "condition", "naobserbahan",
            "napansin", "nakita", "issues", "problema", "nagdurusa", "mahina",
            "pagbabago", "change", "kakaiba", "abnormal", "unusual", "hindi normal",
            "pananakit", "lumalala", "pagbabago", "episode", "attack",
            "kombulsyon", "namamanhid", "numbness", "tusok-tusok",
            "difficulty", "hindi makagalaw", "hindi makatulog", "insomnia",
            "palaging", "persistent", "chronic", "paulit-ulit", "recurring",
            "paranoia", "agitation", "confusion", "hallucination",
            
            # Additional symptoms from sample text
            "peripheral edema", "pamamaga", "chronic pain", "matinding sakit",
            "hirap huminga", "respiratory distress", "cyanosis", "colored sputum",
            "fever", "lagnat", "panginginig", "night vision problem", "blurry vision",
            "malabo ang paningin", "eye strain", "headaches", "sakit ng ulo", 
            "digestive", "acid reflux", "heartburn", "stomach pain", "nausea", 
            "nasusuka", "pagtatae", "constipation", "hirap dumumi", "weight loss",
            "significant weight loss", "pagbaba ng timbang", "reduced appetite",
            "walang ganang kumain", "labored breathing", "hingal", "shortness of breath",
            "dizziness", "vertigo", "hilo", "pagkahilo", "pagsusuka", "vomiting",
            "pananakit ng dibdib", "chest pain", "palpitations", "mabilis na tibok ng puso",
            "joint pain", "arthritis", "stiffness", "paninigas", "incontinence",
            "hindi mapigilan ang pag-ihi", "bowel problems", "urinary issues"
        ],
        
        "kalagayan_pangkatawan": [
            # Existing keywords
            "pisikal", "physical", "katawan", "body", "lakas", "strength", "bigat", "weight",
            "timbang", "tangkad", "height", "vital signs", "temperatura", "temperature",
            "pagkain", "eating", "paglunok", "swallowing", "paglakad", "walking",
            "balanse", "balance", "paggalaw", "movement", "koordinasyon", "coordination",
            "panginginig", "tremors", "nanghihina", "weakness", "pagod", "fatigue",
            "paglalakad", "mobility", "joints", "kasukasuan", "namamaga", "swelling",
            "blood pressure", "presyon", "heart rate", "pulso", "respiratory",
            "paghinga", "oxygen", "sugar level", "glucose", "hydration", "dehydration",
            "nutrisyon", "pagbaba ng timbang", "pagtaba", "edema", "pamamaga",
            "kakayahang gumalaw", "stamina", "lakas ng katawan", "posture",
            
            # Additional physical condition terms from sample text
            "increasing assistance needs", "limited mobility", "paghawak sa hagdanan",
            "hindi pantay na paglalakad", "uneven surfaces", "unsteady gait", 
            "hindi stable na paglakad", "muscle weakness", "kahinaan ng kalamnan",
            "upper body strength", "lower body strength", "extremities", "falls risk",
            "risk ng pagkahulog", "circulation", "circulatory issues", "sirkulasyon",
            "fine motor skills", "gross motor skills", "range of motion", "saklaw ng paggalaw", 
            "flexibility", "flexibility ng joints", "muscle tone", "tone ng kalamnan", 
            "hand strength", "lakas ng kamay", "grip strength", "hawak", "lakas ng hawak",
            "weight-bearing", "endurance", "stamina", "tagal ng paggalaw", "chronic dehydration",
            "madalas na dehydrated", "postural stability", "stability sa pagtayo",
            "gait pattern", "pattern ng paglakad", "transfer ability", "ability na maglipat",
            "sit-to-stand", "pagbangon mula sa pagkakaupo", "physical frailty"
        ],
        
        "kalagayan_mental": [
            # Existing keywords
            "mental", "isip", "cognitive", "cognition", "pag-iisip", "memorya", "memory",
            "nakalimutan", "forget", "pagkalito", "confusion", "disorientation",
            "hindi makapag-concentrate", "concentration", "hindi makafocus", "focus",
            "pagkataranta", "agitation", "irritable", "mairita", "emotional", "emosyonal",
            "kalungkutan", "depression", "lungkot", "sad", "malungkot", "mood", "estado ng isip",
            "paranoia", "pagdududa", "suspicion", "doubt", "pag-aalala", "worry", "anxiety",
            "stress", "pressure", "tension",
            "orientation", "oryentasyon", "awareness", "pagkakaalam", "alertness",
            "responsiveness", "pagtugon", "attention span", "atensyon", 
            "decision-making", "pagpapasya", "judgment", "paghatol", "reasoning",
            "pangangatwiran", "delusions", "pagkabaliw", "psychosis", 
            "mood swings", "pagbabago ng mood", "personality changes", 
            "behavior changes", "pagbabago ng ugali", "fears", "takot", 
            "dementia", "demensya", "Alzheimer's", "cognitive decline",
            
            # Additional mental state terms from sample text
            "pagbabago sa memorya", "memory loss", "forgetfulness", "end-of-life anxiety",
            "death anxiety", "takot sa kamatayan", "grief", "grieving", "pagluluksa",
            "complicated grief", "major depressive disorder", "sundowning syndrome",
            "psychotic symptoms", "hallucinations", "false beliefs", "fears of abandonment",
            "abandonment anxiety", "takot na iwanan", "hopelessness", "kawalan ng pag-asa",
            "worthlessness", "feeling na walang kwenta", "suicidal ideation", "thoughts of death",
            "passive suicidal thoughts", "mental confusion", "aggressiveness", "aggression",
            "pagka-iritable", "irritability", "emotional outbursts", "biglaang pagbabago ng emosyon",
            "emotional lability", "excessive worry", "labis na pag-aalala", "generalized anxiety",
            "disorientation to time", "disorientation to place", "temporal confusion",
            "hindi alam ang araw/oras", "personality disorder", "pagkabaliw", "delirium",
            "obsessive thoughts", "compulsive behaviors", "executive dysfunction",
            "loss of identity", "pagkawala ng identidad", "sense of self", "defensive behavior"
        ],
        
        "aktibidad": [
            # Existing keywords
            "aktibidad", "activities", "gawain", "task", "daily living", "araw-araw",
            "routine", "gawing", "self-care", "personal care", "pangangalaga sa sarili",
            "hygiene", "kalinisan", "pagligo", "bathing", "pagbibihis", "dressing",
            "pagkain", "eating", "pagluluto", "cooking", "paglilinis", "cleaning",
            "exercise", "ehersisyo", "therapy", "therapiya", "hobbies", "libangan",
            "social activities", "pakikisalamuha", "pakikipag-usap", "communication",
            "mobility", "paggalaw", "ambulation", "paglalakad", "transfers",
            "paglipat", "bed mobility", "paggalaw sa kama", "independence",
            "dependence", "pag-asa sa iba", "tungkod", "cane", "walker",
            "wheelchair", "silya de gulong", "crutches", "saklay", 
            "transportasyon", "lakad", "pamimili", "gawaing bahay",
            
            # Additional activity terms from sample text
            "shower safety", "ligtas na pagligo", "toilet safety", "ligtas na paggamit ng banyo",
            "medication management", "pangangasiwa ng gamot", "medication adherence",
            "pagsunod sa inireseta", "meal preparation", "paghahanda ng pagkain",
            "nutritional challenges", "hair and nail care", "home management",
            "pangangasiwa ng tahanan", "household tasks", "gawaing bahay",
            "ability to manage steps", "kakayahang umakyat ng hagdan",
            "assistive devices", "tulong sa paggalaw", "adaptive equipment",
            "modified utensils", "access to transportation", "pagpunta sa appointments",
            "shopping", "bill payment", "pagbabayad ng bills", "financial management",
            "phone use", "paggamit ng telepono", "technology use", "paggamit ng gadgets",
            "community participation", "pakikilahok sa komunidad", "leisure activities",
            "recreational activities", "libangan", "religious activities", "spiritual practice",
            "volunteer work", "household safety", "bathroom modifications"
        ],
        
        "kalagayan_social": [
            # Existing keywords
            "relasyon", "relationship", "pamilya", "family", "social", "pakikisalamuha",
            "kaibigan", "friends", "komunidad", "community", "suporta", "support",
            "pakikipag-usap", "communication", "pakikipag-interact", "interaction",
            "asawa", "spouse", "anak", "children", "kamag-anak", "relatives",
            "kapitbahay", "neighbors", "kakilala", "acquaintances", "visitors",
            "bisita", "group", "organization", "samahan",
            "socialization", "pakikihalubilo", "isolation", "pagkakahiwalay",
            "loneliness", "kalungkutan", "withdrawal", "pag-iwas", "social network",
            "involvement", "participation", "pakikilahok", "church", "simbahan",
            "volunteer", "boluntaryo", "caregiver", "tagapag-alaga", 
            "tulong", "financial support", "sustento", "living situation",
            "tirahan", "kapangyarihan sa bahay", "household dynamics",
            
            # Additional social condition terms from sample text
            "intergenerational communication gap", "communication barriers",
            "cultural dislocation", "cultural adjustment", "adjustment sa bagong environment",
            "feeling invisible", "pakiramdam na hindi nakikita", "role changes",
            "pagbabago ng papel sa pamilya", "loss of authority", "pagkawala ng awtoridad",
            "social disconnection", "withdrawal from activities", "hindi na sumasali",
            "social anxiety", "takot sa social situations", "loss of role", "pagkawala ng papel",
            "care dependency", "dependence on others", "pag-asa sa iba", "social isolation",
            "elder abuse potential", "potential mistreatment", "pagmamaltratro",
            "strained family relationships", "tensyong pampamilya", "conflict with caregivers",
            "multi-generational household", "adjustment to living with family",
            "boundaries", "personal space issues", "financial dependence",
            "rural to urban transition", "adapting to new community",
            "generational differences", "pagkakaiba ng henerasyon", "respect issues",
            "acceptance by others", "acceptance ng kondisyon", "stigma", "shame", "hiya",
            "social identity loss", "loss of social standing", "peer relationships"
        ],
        # NEW SECTION: Medical History Keywords
        "medical_history": [
            "medical history", "history", "kasaysayan", "diagnosis", "diagnosed", "diyagnosis", 
            "na-diagnose", "condition", "kondisyon", "chronic", "chronic condition", "matagal na sakit", 
            "pangmatagalang sakit", "hypertension", "altapresyon", "high blood", "mataas na presyon",
            "diabetes", "dyabetis", "heart attack", "atake sa puso", "myocardial infarction", "MI", 
            "stroke", "cardiovascular", "heart disease", "sakit sa puso", "coronary artery disease", 
            "coronary", "pulmonary", "respiratory", "respiratory disease", "sakit sa baga", "COPD", 
            "emphysema", "chronic bronchitis", "bronchitis", "asthma", "hika", "cancer", "kanser", 
            "tumor", "bukol", "arthritis", "rayuma", "osteoporosis", "osteoarthritis", "joint disease",
            "sakit sa kasukasuan", "kidney disease", "sakit sa kidney", "renal", "liver", "atay", 
            "hepatic", "thyroid", "thyroid disorder", "goiter", "allergies", "alerhiya", "medications", 
            "gamot", "maintenance", "maintenance medication", "iniinom na gamot", "surgical history", 
            "operasyon", "surgery", "surgeries", "previous hospitalizations", "naospital", "hospitalization",
            "dating sakit", "previous illness", "family history", "history sa pamilya", "hereditary",
            "genetic", "genetically", "pre-existing condition", "dati nang kondisyon", 
            "controlled with medication", "controlled by medication", "controlled using",
            "side effects", "adverse reactions", "medical records", "laboratory results",
            "lab test", "laboratory test", "xray", "CT scan", "MRI", "imaging", "diagnostic test",
            "timeline ng sakit", "onset", "duration", "tagal", "complications", "komplikasyon",
            "underlying condition", "existing condition"
        ],
        "pain_discomfort": [
            "pain", "sakit", "pananakit", "kirot", "masakit", "sumasakit", "discomfort", 
            "kawalan ng ginhawa", "uncomfortable", "hindi komportable", "ache", "aray", 
            "masakit", "makirot", "chronic pain", "acute pain", "matinding sakit", 
            "tuloy-tuloy na sakit", "throbbing", "burning", "shooting", "stabbing", 
            "sharp", "dull", "matigas", "malambot", "masama", "nakakaapekto", "nakakahadlang", 
            "lumalala", "lumalalang", "gumiginhawa", "bumubuti", "nasobrahan", "matindi", 
            "mild", "moderate", "severe", "bahagya", "katamtaman", "malala", "nakakalimitasyon", 
            "hindi makatulog", "hindi makagalaw", "joint pain", "muscle pain", "sakit ng kalamnan", 
            "sakit ng kasukasuan", "fibromyalgia", "neuropathic pain", "nerve pain", 
            "headache", "migraine", "sakit ng ulo", "stomach pain", "abdominal pain", 
            "sakit ng tiyan", "back pain", "sakit ng likod", "referred pain", 
            "radiating pain", "intermittent", "pabalik-balik", "persistent", "paulit-ulit", 
            "pahirap ng pahirap", "pain management", "pain relief", "pain medication", 
            "pain scale", "pain level", "intensity", "sintomas", "trigger points", "cramping", 
            "pulikat", "paninigas ng kalamnan", "ngalay", "pagod", "hingal", "pressure", "presyon", 
            "tender", "sensitive", "namamaga", "swelling", "pamamaga"
        ],
        "hygiene": [
            "hygiene", "kalinisan", "cleanliness", "malinis", "personal care", "pangangalaga sa sarili", 
            "bathing", "pagliligo", "paliligo", "washing", "paglilinis", "paghuhugas", 
            "brushing teeth", "pagsesepilyo", "oral hygiene", "kalinisan ng bibig", 
            "grooming", "pag-aayos", "dressing", "pagbibihis", "toileting", "pag-CR", 
            "incontinence", "pagpigil sa pag-ihi", "pagpigil sa pagdumi", "nail care", 
            "hair care", "pag-aahit", "shaving", "skincare", "routine", "gawain", 
            "assistance", "tulong", "supervision", "pagbabantay", "araw-araw", "daily", 
            "weekly", "lingguhan", "monthly", "buwanan", "body odor", "amoy", 
            "clean clothes", "malinis na damit", "dirty clothes", "maruming damit", 
            "self-care", "self-neglect", "pagpapabaya sa sarili", "pangangalaga sa sarili", 
            "adaptive equipment", "shower chair", "bath bench", "grab bars", "hawakan", 
            "soap", "sabon", "shampoo", "shampoo", "toothpaste", "toothbrush", "sipilyo", 
            "towel", "tuwalya", "washcloth", "basin", "palanggana", "sponge", "lotion", 
            "deodorant", "cologne", "pabango", "presentable", "malinis na kasuotan", 
            "independent", "nakakaya", "needs help", "nangangailangan ng tulong"
        ]
    }
    
     # Initialize scoring for each sentence-section pair with improved weights
    sentence_scores = {}
    for i, (sent, doc) in enumerate(zip(sentences, sentence_docs)):
        if not doc:
            continue
            
        sentence_scores[i] = {}
        
        # Get sentence length for normalization
        sent_length = len(sent.split())
        
        for section, keywords in section_keywords.items():
            # Calculate base score from keyword matches
            base_score = 0
            for keyword in keywords:
                if keyword.lower() in sent.lower():
                    # Give higher score to exact matches
                    if f" {keyword.lower()} " in f" {sent.lower()} ":
                        base_score += 1.5  # Full word match
                    else:
                        base_score += 1.0  # Partial match
            
            # Check for pattern matches
            pattern_score = 0
            if section in section_patterns:
                for pattern in section_patterns[section]:
                    if re.search(pattern, sent.lower()):
                        pattern_score += 4.0  # Higher score for pattern matches
                        break  # One strong pattern match is enough
            
            # Normalize by sentence length
            if sent_length > 20:
                base_score = base_score * (20 / sent_length) * 1.5
            
            # REPLACE OLD ENTITY BOOST WITH NEW CONTEXT-AWARE ENTITY SCORING
            entity_boost = get_entity_context_score(doc, section)
            
            # First few sentences often provide overview/symptoms
            if i < 2 and section == "mga_sintomas":
                base_score += 1
            
            # Save the combined score
            sentence_scores[i][section] = base_score + entity_boost + pattern_score
    
    # Assign sentences to sections based on scores
    result = {}
    assigned_sentences = set()
    
    # Initialize all sections to empty arrays
    for section in section_keywords.keys():
        result[section] = []
    
    # FIRST PASS: Assign sentences with clear high scores
    threshold = 2.5  # Higher threshold for clear assignment
    for section in section_keywords.keys():
        sorted_sentences = sorted(sentence_scores.items(), 
                                  key=lambda x: -x[1].get(section, 0))
        
        # Take up to 5 sentences with high scores
        count = 0
        max_sentences = 7
        
        for i, scores in sorted_sentences:
            if i in assigned_sentences:
                continue
                
            section_score = scores.get(section, 0)
            next_best_score = max([s for k, s in scores.items() if k != section], default=0)
            
            # Only assign if score is high and clearly better than other sections
            if (section_score >= threshold and 
                section_score > next_best_score * 1.25 and
                count < max_sentences):
                
                result[section].append(sentences[i])
                assigned_sentences.add(i)
                count += 1
    
    # SECOND PASS: Assign remaining sentences to their best-matching section
    section_counts = {s: len(sents) for s, sents in result.items()}
    max_sentences_per_section = 7  # Increase to 10 sentences or more
    
    for i, scores in sorted(sentence_scores.items(), 
                           key=lambda x: -max(x[1].values() if x[1] else [0])):
        if i not in assigned_sentences and any(scores.values()):
            best_section = max(scores.items(), key=lambda x: x[1])[0]
            
            # Only add if we haven't reached the max sentences for this section
            if section_counts.get(best_section, 0) < max_sentences_per_section:
                result[best_section].append(sentences[i])
                assigned_sentences.add(i)
                section_counts[best_section] = section_counts.get(best_section, 0) + 1
    
    # THIRD PASS: Ensure sentences are in logical order within each section
    for section in result:
        # Get the indices of assigned sentences and sort them
        indices = [i for i, sent in enumerate(sentences) if sent in result[section]]
        # Reorder sentences based on original order
        result[section] = [sentences[i] for i in sorted(indices)]
    
    # Fourth pass: Assign any remaining important sentences to most relevant sections
    for i, sent in enumerate(sentences):
        if i not in assigned_sentences:
            if i in sentence_scores:
                scores = sentence_scores[i]
                if scores:
                    best_section = max(scores.items(), key=lambda x: x[1])[0]
                    # Use "result" instead of "sections"
                    result[best_section].append(sent)
                    assigned_sentences.add(i)
    
    # Ensure at least one section has content
    if all(not sents for sents in result.values()) and sentences:
        result["mga_sintomas"] = sentences[:5]  # Limit to 5 sentences
    
    sections = handle_section_overflow(result, sentence_scores, sentences, assigned_sentences, max_sentences_per_section)
    
    # Convert lists to strings and apply post-processing
    processed_sections = {}
    for section, sents in sections.items():  # <-- CORRECTED
        if sents:
            # Join complete sentences
            section_text = " ".join(sents)
            # Apply post-processing to fix formatting issues
            section_text = post_process_summary(section_text)
            processed_sections[section] = section_text
    
    return processed_sections

def extract_sections_for_evaluation(sentences):
    """Extract sections for evaluation with improved handling of complete sentences."""
    print(f"Extracting evaluation-specific sections from {len(sentences)} sentences")
    
    # Process sentences with NLP
    sentence_docs = [nlp(sent) for sent in sentences]
    
    # Initialize sections
    sections = {
        "pangunahing_rekomendasyon": [],
        "mga_hakbang": [],
        "pangangalaga": [],
        "pagbabago_sa_pamumuhay": [],
        # New sections
        "safety_risk_factors": [],         # Safety risks and mitigation
        "nutrisyon_at_pagkain": [],        # Nutrition and diet
        "kalusugan_ng_bibig": [],          # Oral/dental health
        "mobility_function": [],           # Mobility and functional ability
        "kalagayan_ng_tulog": [],          # Sleep management
        "pamamahala_ng_gamot": [],         # Medication management
        "suporta_ng_pamilya": [],          # Family support recommendations
        "kalagayan_mental": [],            # Mental/emotional support
        "preventive_health": [],           # Preventive measures
        "vital_signs_measurements": []     # Vital signs monitoring
    }
    
    # Strong signal patterns for each section with expanded patterns
    section_patterns = {
        "pangunahing_rekomendasyon": [
            # Existing patterns
            r'inirerekomenda(ng)? (ko|kong|namin|naming) (na|ang)',
            r'iminumungkahi(ng)? (ko|kong|namin|naming) (na|ang)',
            r'pinapayuhan (ko|kong|namin|naming) (na|ang)',
            r'(una sa lahat|bilang pangunahing hakbang)',
            r'(dapat|kailangan|kinakailangan|mahalagang) (na )?',
            r'rekomendasyon',
            r'agarang pagkonsulta',
            r'immediate consultation',
            r'priority',
            r'most important',
            r'critical',
            r'crucial',
            r'essential',
            r'necessary',
            r'kailangang',
            r'kinakailangan',
            r'referral',
            r'irefer',
            
            # Additional patterns from sample text
            r'unang-una, inirerekomenda ko',
            r'una sa lahat, inirerekomenda ko',
            r'inirerekumenda kong magpatingin',
            r'agarang pagpapatingin sa',
            r'immediate medical evaluation',
            r'immediate intervention',
            r'komprehensibong assessment',
            r'comprehensive evaluation',
            r'referral sa (specialist|doctor|physical therapist|occupational therapist)',
            r'nirerekomenda ko ang consultation sa',
            r'kailangang magpa-(konsulta|evaluate|check|assess)',
            r'binibigyang-diin ko ang kahalagahan ng',
            r'binigyang-diin ko sa pamilya',
            r'strong(ly)? (urge|recommend|advised)',
            r'highest priority',
            r'urgent need for',
            r'primary recommendation',
            r'medical attention',
            r'emergency (care|evaluation|assessment)',
            r'kinakailangang ma-address agad',
            r'nangangailangan ng agarang',
            r'requires immediate',
            r'professional help',
            r'specialist evaluation',
            r'specialized care'
        ],
        
        "mga_hakbang": [
            # Existing patterns
            r'(simulan|gawin|ipatupad|isagawa) ang',
            r'(susunod na hakbang|sa|mga|bilang) (hakbang|steps|interventions)',
            r'(dapat|kailangang) (din|rin) (na )?',
            r'(pangalawang|pangatlo|kasunod na) hakbang',
            r'procedure',
            r'process',
            r'method',
            r'technique',
            r'approach',
            r'implementation',
            r'implement',
            r'execute',
            r'perform',
            r'apply',
            r'administer',
            r'isagawa',
            r'gawin',
            r'therapy sessions',
            r'treatment course',
            
            # Additional patterns from sample text
            r'tinuruan ko si(ya|la|lo|na)? ng',
            r'nagbigay ako ng demonstration',
            r'ipinakita ko kung paano',
            r'gumawa ako ng personalized',
            r'binuo ko ang isang',
            r'specific techniques',
            r'proper technique',
            r'step-by-step (approach|process|method)',
            r'systematic approach',
            r'structured method',
            r'training sa proper',
            r'exercises na dapat gawin',
            r'specific strategies',
            r'practical solutions',
            r'intervention plan',
            r'demonstration sa pamilya',
            r'family education',
            r'caregiver training',
            r'tinuruan ang pamilya',
            r'pagbibigay ng gabay',
            r'specific adaptations',
            r'gumawa ng visual aids',
            r'energy conservation techniques',
            r'taught proper positioning',
            r'nagturo ng proper use',
            r'exercises for strengthening',
            r'binigyan ng mga kopya',
            r'developed system',
            r'created schedule',
            r'training session',
            r'tinuruan sa proper'
        ],
        
        "pangangalaga": [
            # Existing patterns
            r'(para sa|upang|sa) (pangangalaga|pag-iwas|pag-aalaga)',
            r'(i-monitor|obserbahan|bantayan|subaybayan)',
            r'(sa pang-araw-araw na pangangalaga|daily care)',
            r'(sa bahay|home care|home management)',
            r'(kapag|kung|sa) (nagkaroon|nagkakaroon)',
            r'(palaging|regular na|always|consistently)',
            r'care',
            r'alaga',
            r'monitoring',
            r'pagbabantay',
            r'observation',
            r'pagmamasid',
            r'maintenance',
            r'management',
            r'hygiene',
            r'kalinisan',
            r'bathing',
            r'pagliligo',
            r'grooming',
            r'pag-aayos',
            r'positioning',
            r'pagpoposisyon',
            
            # Additional patterns from sample text
            r'regular na pag-monitor',
            r'regular monitoring',
            r'daily observation',
            r'araw-araw na pagsusuri',
            r'weekly medication review',
            r'routine check',
            r'follow-up assessment',
            r'continuity of care',
            r'care routine',
            r'skin care',
            r'skin integrity',
            r'wound care',
            r'proper hygiene',
            r'oral care',
            r'oral hygiene',
            r'dental care',
            r'medication supervision',
            r'supervised medication',
            r'supervised intake',
            r'consistent supervision',
            r'safety supervision',
            r'continence care',
            r'incontinence management',
            r'hair and nail care',
            r'skin protection',
            r'repositioning schedule',
            r'scheduled turning',
            r'pressure relief',
            r'family supervision',
            r'assisted care',
            r'bathing assistance',
            r'washing assistance',
            r'dressing support',
            r'regular assessment',
            r'care schedule',
            r'caregiver responsibilities',
            r'daily charting',
            r'documentation ng symptoms',
            r'regular reporting'
        ],
        
        "pagbabago_sa_pamumuhay": [
            # Existing patterns
            r'(pagbabago sa|baguhin ang|adjustment sa) (pamumuhay|lifestyle)',
            r'(diet|nutrisyon|nutrition|pagkain)',
            r'(exercise|ehersisyo|physical activity)',
            r'(normal na routine|daily habits|araw-araw)',
            r'(long-term|pangmatagalang|sa hinaharap|future)',
            r'lifestyle',
            r'pamumuhay',
            r'habits',
            r'ugali',
            r'practices',
            r'gawain',
            r'routines',
            r'modifications',
            r'adjustments',
            r'changes',
            r'pagbabago',
            r'diet plan',
            r'meal plan',
            r'exercise program',
            r'sleep',
            r'tulog',
            r'hydration',
            r'pag-inom ng tubig',
            r'stress management',
            r'relaxation',
            r'environment',
            r'kapaligiran',
            
            # Additional patterns from sample text
            r'balanced diet',
            r'dietary adjustments',
            r'dietary modifications',
            r'nutrition plan',
            r'nutritional changes',
            r'healthy eating',
            r'food choices',
            r'pagbabago ng diyeta',
            r'hydration routine',
            r'increased fluid intake',
            r'limited sodium',
            r'reduced sugar',
            r'pagbawas ng (asukal|asin|alak)',
            r'sleep hygiene',
            r'sleep routine',
            r'tulog schedule',
            r'regular sleep pattern',
            r'consistent bedtime',
            r'avoid screens before bed',
            r'relaxation techniques',
            r'stress reduction',
            r'pagbabawas ng stress',
            r'home modifications',
            r'environmental changes',
            r'safety modifications',
            r'assistive devices',
            r'adaptive equipment',
            r'accessible home',
            r'grab bars',
            r'better lighting',
            r'removing hazards',
            r'physical activity',
            r'regular exercise',
            r'gentle movement',
            r'strengthening exercises',
            r'balance exercises',
            r'daily walking',
            r'social engagement',
            r'social activities',
            r'mental stimulation',
            r'cognitive activities',
            r'hobbies at leisure',
            r'limit alcohol',
            r'smoking cessation',
            r'pag-iwas sa paninigarilyo'
        ],
        # NEW SECTION PATTERNS
        "safety_risk_factors": [
            # Safety risk patterns
            r'(falls? risk|risk (ng|of) (pagkahulog|pagkatumba))',
            r'(safety hazards?|panganib sa kaligtasan|mapanganib na (bagay|sitwasyon))',
            r'(home safety|kaligtasan sa (bahay|tahanan))',
            r'(alisin|tanggalin) (ang|mga) (clutter|kalat|nakaharang)',
            r'(anti-slip|non-slip|hindi madulas|rubber|mats)',
            r'(grab bars?|handrails?|hawakan|suporta sa dingding)',
            r'(adequate|sapat na) (lighting|liwanag|ilaw)',
            r'(pangalagaan|siguraduhin|i-secure|secure) ang (hagdanan|stairs)',
            r'(injury prevention|pag-iwas sa aksidente)',
            r'(loose|maluwag na) (rugs|cables|wires|basahan|kable)',
            r'(makaiwas|upang maiwasan) (ang|sa) (pagkahulog|aksidente|pinsala)',
            r'(nighttime|sa gabi|madaling araw) (safety|kaligtasan)',
            r'(paglalagay|installation) ng (safety devices|safety equipment)',
            r'(trip hazards?|madulas na sahig|mahuhulog)',
            r'(warning signs|alarm system|emergency response)'
        ],
        
        "nutrisyon_at_pagkain": [
            # Nutrition and diet patterns
            r'(balanced diet|balanseng pagkain|nutrisyon|nutrition)',
            r'(dietary|food|pagkain|diyeta) (recommendations|changes|adjustments)',
            r'(food|meals?|pagkain) (preparation|paghahanda)',
            r'(specific nutritional|partikular na sustansiya)',
            r'(healthy eating|malusog na pagkain)',
            r'(adequate|sapat na) (protein|protina|carbohydrates|karbohidrato)',
            r'(vitamins?|minerals?|bitamina|mineral)',
            r'(high|mataas sa) (fiber|dietary fiber|fiber content)',
            r'(small|frequent|maliit|maliliit|madalas) (meals|pagkain)',
            r'(soft|malambot) (diet|foods|pagkain)',
            r'(fluid intake|pag-inom ng tubig|hydration)',
            r'(nutritional supplements|food supplements|bitamina)',
            r'(bawasan|limitahan|iwasan) ang (asukal|asin|sodium|fats|taba)',
            r'(whole grains|fruits|vegetables|gulay|prutas)',
            r'(swallowing techniques|paglunok|pagkain nang dahan-dahan)'
        ],
        
        "kalusugan_ng_bibig": [
            # More specific oral health patterns that won't capture general medication info
            r'(oral health|oral care|dental care|dental health) ((?!medication).)*$',
            r'(kalinisan ng bibig|pangangalaga ng ngipin|dental hygiene)',
            r'(pagsesepilyo|toothbrushing|brushing (of )?teeth) ((?!medication).)*$',
            r'(dental checkups?|dental visits?|pagpapa-dentista)',
            r'(flossing|paggamit ng dental floss)',
            r'(dry mouth|tuyong bibig|xerostomia) ((?!medication).)*$',  # Exclude when related to medication
            r'(dentures?|pustiso|false teeth)',
            r'(gums?|gilagid|periodontal)',
            r'(teeth cleaning|paglilinis ng ngipin)',
            r'(mouthwash|mouth rinse|oral rinse) ((?!medication).)*$',
            r'(saliva|laway|lubrication)',
            r'(oral problems(?! with medication)|problema sa bibig(?! dahil sa gamot))',
            r'(kalinisan ng dila|tongue cleaning)',
            r'(tooth decay|ngipin na may sira|cavities)',
            r'(oral sores(?! from medication)|sugat sa bibig(?! dahil sa gamot))',
            # More specific oral health patterns that won't capture general medication info
            r'(oral health|oral care|dental care|dental health) ((?!medication|gamot|drug|prescription).)*$',
            r'(kalinisan ng bibig|pangangalaga ng ngipin|dental hygiene)',
            r'(pagsesepilyo|toothbrushing|brushing (of )?teeth) ((?!medication|gamot|drug).)*$',
        ],
        
        "mobility_function": [
            # Mobility patterns
            r'(assistive devices?|mobility aids?|tulong sa paggalaw)',
            r'(walker|wheelchair|silya de gulong|tungkod|cane)',
            r'(gait pattern|pattern ng paglalakad|paglalakad)',
            r'(strengthening exercises|pagpapalakas|ehersisyo)',
            r'(balanse|balance exercises|pagpapanatili ng balanse)',
            r'(sit-to-stand|pagtayo mula sa pagkakaupo)',
            r'(range of motion|flexibility|saklaw ng paggalaw)',
            r'(transfer techniques|paglipat|paglilipat)',
            r'(proper posture|tamang postura|tamang pag-upo)',
            r'(mobility limitations|limitasyon sa paggalaw)',
            r'(coordination exercises|training sa coordination)',
            r'(joint (mobility|protection)|pangangalaga ng kasukasuan)',
            r'(adapted|modified) (movements|techniques|galaw)',
            r'(steps?|hakbang|hagdanan|stairs) (nagagawang|ability)',
            r'(physiotherapy|physical therapy|PT exercises)'
        ],
        
        "kalagayan_ng_tulog": [
            # Sleep management patterns
            r'(sleep hygiene|kalinisan ng tulog|sleep habits)',
            r'(sleep routine|routine sa pagtulog)',
            r'(sleep schedule|regular na oras ng pagtulog)',
            r'(comfortable|komportableng) (bed|kama|bedding|sleeping)',
            r'(sleep environment|sleep setting|kwarto para sa tulog)',
            r'(before bedtime|bago matulog|bedtime routine)',
            r'(avoid|iwasan) (screens?|electronic devices?|TV|cellphone)',
            r'(relaxation techniques?|relaxation method|pagrerelaks)',
            r'(mattress|unan|pillow|sleeping position)',
            r'(noise|ilaw|light|temperature|ingay) (sa|sa oras ng) (sleep|tulog)',
            r'(nap|idlip|pahinga) (schedule|routine)',
            r'(sleep promoting|nakakatulong sa pagtulog)',
            r'(insomnia|hirap makatulog|hirap sa pagtulog)',
            r'(deep breathing|meditation|breathing exercise)',
            r'(regular na paggising|consistent waking time)'
        ],
        
        "pamamahala_ng_gamot": [
            # Medication management patterns
            r'(medication|gamot) (schedule|routine)',
            r'(medication adherence|pagsunod sa reseta|pagsunod sa gamot)',
            r'(pill organizer|pill box|lalagyan ng gamot)',
            r'(medication reminder|paalala sa pag-inom ng gamot)',
            r'(monitoring|regularly check|bantayan) (ang|the) (side effects|epekto)',
            r'(prescription|reseta) (refill|renewal)',
            r'(medication list|listahan ng gamot)',
            r'(dosage|dosis) (adjustment|pagbabago|timing)',
            r'(regular medication review|regular na pagsusuri ng gamot)',
            r'(drug interactions?|kontra-indikasyon|harmful combinations?)',
            r'(administering|pag-administer|pagbigay) (ng|of) (medications?|gamot)',
            r'(coordination with|pakikipag-ugnayan sa|consultation with) (physicians?|doctors?|doktor)',
            r'(injectable medications?|iv medications?|specialty medications?)',
            r'(adverse reactions?|allergic reactions?)',
            r'(as-needed medications?|prn medications?)',
            # Add patterns for medication explanations, education, and fears
            r'(explanation|paliwanag|information) (sheet|handout)s? (tungkol sa|about) (medication|gamot)',
            r'(simplified|simplified na) (explanation|paliwanag) (tungkol sa|about) (medication|gamot)',
            r'(fears?|takot|misconceptions?|maling paniniwala) (tungkol sa|about|sa|regarding) (medications?|gamot)',
            r'(benefits?|advantages?|kabutihan) (over|versus|vs|kaysa) (risks?|side effects?|panganib)',
            r'(package inserts?|leaflets?|information sheets?)',
            r'(potential|possible|rare) (side effects?|adverse reactions?|epekto)',
            r'(medication education|edukasyon tungkol sa gamot)',
            r'(teaching|pagtuturo|explaining|pagpapaliwanag) (about|tungkol sa) (medications?|gamot)',
            r'(understanding|pag-unawa sa) (medications?|prescriptions?|gamot)'
        ],
        
        "suporta_ng_pamilya": [
            # Family support patterns
            r'(family involvement|family participation|pakikilahok ng pamilya)',
            r'(caregiver|tagapag-alaga) (education|training|pagsasanay)',
            r'(family members?|kapamilya|kamag-anak) (dapat|kailangan|need to)',
            r'(support system|family support|suportang pampamilya)',
            r'(regular communication|pakikipag-usap sa pamilya)',
            r'(family meetings?|family conference|pamilya)',
            r'(shared responsibility|hatian ng responsibilidad)',
            r'(respite care|pahinga para sa caregiver)',
            r'(balancing care|pagsasaayos ng pangangalaga)',
            r'(family dynamics|ugnayang pampamilya)',
            r'(pinayuhan|tinuruan|binigyang-kaalaman) ang pamilya',
            r'(collaboration with|pakikipagtulungan sa) (family|pamilya)',
            r'(sensitibo|mahalagang|importante) para sa (family members?|kapamilya)',
            r'(multi-generational|buong pamilya)',
            r'(kasama ang|involvement of|role of) (children|grandchildren|anak|apo)'
        ],
        
        "kalagayan_mental": [
            # Mental/emotional support patterns
            r'(emotional support|suportang emosyonal|psychological support)',
            r'(mental health needs|pangangailangang mental)',
            r'(anxiety reduction|stress management|pamamahala ng stress)',
            r'(coping strategies|coping mechanisms|paraan ng pag-cope)',
            r'(socialization|interaction|pakikisalamuha)',
            r'(cognitive stimulation|mental exercises|ehersisyo para sa isipan)',
            r'(addressing fears|pagtugon sa takot|pagharap sa pangamba)',
            r'(mood improvement|pagpapabuti ng mood)',
            r'(relaxation techniques|relaxation therapy)',
            r'(validation|empathy|understanding|pag-unawa)',
            r'(recreational activities|gawaing libangan|libangan)',
            r'(depression|loneliness|kalungkutan) (management|therapy)',
            r'(self-esteem|self-worth|pagpapahalaga sa sarili)',
            r'(therapeutic communication|pakikipag-usap nang maayos)',
            r'(grief counseling|counseling|psychological support)'
        ],
        
        "preventive_health": [
            # Preventive measures patterns
            r'(preventive measures|pag-iwas sa sakit|makaiwas)',
            r'(regular check-ups|regular na pagpapatingin)',
            r'(screening tests?|routine screening)',
            r'(vaccinations?|bakuna|immunizations?)',
            r'(early detection|maagang pagtuklas)',
            r'(monitoring for|signs of|bantayan ang palatandaan)',
            r'(preventable complications?|maiiwasang komplikasyon)',
            r'(early intervention|maagang interbensyon)',
            r'(fall prevention|pag-iwas sa pagkatumba)',
            r'(pressure ulcers?|bed sores?|pressure injury prevention)',
            r'(infection control|infection prevention|pag-iwas sa impeksyon)',
            r'(health promotion|pagpapaunlad ng kalusugan)',
            r'(disease prevention|pag-iwas sa sakit)',
            r'(managing risk factors|pangangasiwa ng risk factors)',
            r'(lifestyle modifications?|pagbabago ng pamumuhay)'
        ],
        
        "vital_signs_measurements": [
            # Vital signs monitoring patterns
            r'(vital signs?|vital measurements?|mahahalagang sukatan)',
            r'(blood pressure|presyon|BP)',
            r'(temperature|temperatura)',
            r'(pulse rate|heart rate|rate ng pulso|bilis ng tibok)',
            r'(respiratory rate|breathing rate|bilis ng paghinga)',
            r'(oxygen saturation|oxygen levels?|SpO2)',
            r'(monitoring|pagsusukat|pagsubaybay) (ng|sa) (BP|presyon|temperature)',
            r'(regular monitoring|regular na pagsusukat)',
            r'(abnormal readings?|changes in vitals?)',
            r'(log|record|documentation) (ng|sa|of) (vital signs?)',
            r'(high|low|elevated|mataas|mababa) (readings?|values?)',
            r'(blood glucose|blood sugar|asukal sa dugo)',
            r'(weight monitoring|pagsukat ng timbang|weight changes?)',
            r'(vital signs equipment|aparato para sa pagsukat)',
            r'(home monitoring|pagsusukat sa bahay)'
        ]
    }

    # ADD THIS: Keywords for evaluation sections to complement the patterns
    section_keywords = {
        "pangunahing_rekomendasyon": [
            "inirerekomenda", "iminumungkahi", "pinapayuhan", "nirerekomenda", 
            "rekomendasyon", "recommendation", "priority", "una", "first", "kailangan",
            "necessary", "crucial", "critical", "essential", "importante", "mahalagang",
            "referral", "irefer", "prioritize", "agaran", "immediate", "urgent",
            "binibigyang-diin", "emphasize", "highlight", "nangangailangan", "requires",
            "kinakailangan", "comprehensive", "komprehensibo", "professional help",
            "konsulta", "specialist", "doctor", "medical attention", "emergency",
            "key recommendation", "pangunahing rekomendasyon", "pangangailangang"
        ],
        
        "mga_hakbang": [
            "hakbang", "step", "approach", "method", "technique", "procedure", "process",
            "implementation", "execute", "administer", "perform", "apply", "implement",
            "isagawa", "gawin", "simulan", "ipatupad", "pangalawa", "pangatlo",
            "demonstration", "demonstration sa", "ipakita", "turuan", "binuo", 
            "pagbubuo", "specific technique", "specific strategy", "structured approach",
            "step-by-step", "systematic", "proper technique", "tamang technique",
            "training", "pagsasanay", "intervention plan", "gabay", "guidance",
            "visual aids", "training session", "practical solution", "personalized",
            "therapy session", "treatment course", "physical therapy", "kognitibong"
        ],
        
        "pangangalaga": [
            "care", "alaga", "pangangalaga", "pag-aalaga", "monitoring", "pagbabantay",
            "observation", "regular", "palagi", "consistently", "daily care", "home care",
            "pang-araw-araw", "maintenance", "management", "hygiene", "kalinisan",
            "bathing", "pagliligo", "grooming", "pag-aayos", "positioning", "pagpoposisyon",
            "observation", "pagmamasid", "subaybayan", "bantayan", "i-monitor", "obserbahan",
            "regular monitoring", "araw-araw na pagsusuri", "follow-up", "routine check",
            "continuity of care", "care routine", "skin care", "wound care", "oral care",
            "supervision", "supervised", "regular assessment", "scheduled", "documentation",
            "documentation ng symptoms", "report", "consistent", "assistance", "suporta"
        ],
        
        "pagbabago_sa_pamumuhay": [
            "pagbabago", "baguhin", "change", "adjust", "lifestyle", "pamumuhay", 
            "modifications", "adjustments", "habits", "ugali", "practices", "gawain",
            "routines", "diet", "nutrition", "nutrisyon", "exercise", "ehersisyo",
            "physical activity", "sleep", "tulog", "hydration", "pag-inom ng tubig",
            "stress management", "relaxation", "environment", "kapaligiran",
            "diet plan", "meal plan", "exercise program", "balanced diet",
            "dietary adjustment", "healthy eating", "food choice", "nutritional change",
            "hydration routine", "fluid intake", "reduced sugar", "reduced salt",
            "sleep hygiene", "sleep routine", "consistent bedtime", "relaxation technique",
            "stress reduction", "home modification", "safety modification", "daily habit"
        ],
        
        "safety_risk_factors": [
            "safety", "kaligtasan", "risk", "panganib", "hazard", "falls", "pagkahulog", 
            "pagkatumba", "prevention", "iwasan", "accident", "aksidente", "injury", 
            "pinsala", "secure", "anti-slip", "non-slip", "rubber mats", "grab bars", 
            "handrails", "hawakan", "lighting", "ilaw", "liwanag", "clear pathways", 
            "madulas", "trip hazards", "obstacles", "nakaharang", "nightlight", 
            "emergency", "fire safety", "alarma", "motion sensors", "smoke detectors", 
            "stairs safety", "bathroom safety", "bathing safety", "kitchen safety", 
            "sharp objects", "mapanganib", "security", "emergency response", "warning signs",
            "fall prevention", "pag-iwas sa pagkadapa", "preventative", "emergency plan"
        ],
        
        "nutrisyon_at_pagkain": [
            "diet", "pagkain", "nutrition", "nutrisyon", "hydration", "tubig", "fluids", 
            "food", "meals", "protein", "protina", "fiber", "dietary fiber", "vitamins", 
            "bitamina", "minerals", "mineral", "carbohydrates", "karbohidrato", "healthy eating", 
            "balanced diet", "soft diet", "thickened liquids", "supplements", "food preparation", 
            "meal planning", "small meals", "frequent meals", "sodium", "asin", "salt", "sugar", 
            "asukal", "fats", "taba", "fruits", "vegetables", "gulay", "prutas", "whole grains", 
            "nutritional needs", "swallowing", "paglunok", "appetite", "gana sa pagkain",
            "malnutrition", "nutritional status", "high protein", "low sodium", "low sugar",
            "modified diet", "meal schedule", "eating assistance", "texture modified"
        ],
        
        "kalusugan_ng_bibig": [
            "oral health", "dental care", "kalusugan ng bibig", "ngipin", "teeth", "gums", 
            "gilagid", "brushing", "pagsesepilyo", "flossing", "dental floss", "mouthwash", 
            "dental checkup", "dentist", "dentista", "dentures", "pustiso", "false teeth", 
            "dry mouth", "xerostomia", "saliva", "laway", "oral hygiene", "kalinisan ng bibig", 
            "oral pain", "sakit ng ngipin", "tooth decay", "cavities", "teeth cleaning", 
            "paglilinis ng ngipin", "oral sores", "mouth ulcers", "sugat sa bibig", "tongue", 
            "dila", "dental health", "oral care", "mouth care", "dental problems",
            "denture care", "paglilinis ng pustiso", "oral assessment", "gum disease",
            # More specific oral/dental terms
            "dental", "ngipin", "teeth", "tooth", "gums", "gilagid", "pagsesepilyo", 
            "toothbrush", "toothpaste", "floss", "mouthwash", "dental checkup", "dentista",
            "pustiso", "dentures", "dental prosthesis", "braces", "dental bridges", "crowns",
            "oral lesions", "oral cavity", "mouth sores", "mouth ulcers", "tongue", "dila",
            "palate", "ngala-ngala", "jaw", "panga", "TMJ", "dental pain", "gum disease",
            "gingivitis", "periodontitis", "tartar", "plaque", "cavities", "dental caries",
            "tooth decay", "dental fillings", "root canal", "extraction", "dental surgery", 
            "dental x-ray", "oral cancer screening", "oral hygiene", "kalinisan ng bibig"
        ],
        
        "mobility_function": [
            "mobility", "paggalaw", "gait", "paglalakad", "assistive device", "walker", "cane", 
            "tungkod", "wheelchair", "silya de gulong", "balance", "balanse", "transfers", 
            "paglipat", "strength", "lakas", "exercises", "ehersisyo", "range of motion", 
            "flexibility", "coordination", "koordinasyon", "posture", "postura", "ergonomics", 
            "physical therapy", "therapist", "rehabilitation", "positioning", "pagtayo", 
            "pagbangon", "stability", "joint protection", "protection sa mga joints", 
            "kasukasuan", "movement", "galaw", "ambulation", "walking aid", "tulong sa paglalakad", 
            "steps", "hagdanan", "stairs", "functional ability", "functional training",
            "mobility aid", "strengthening", "transferring", "bed mobility", "sit to stand"
        ],
        
        "kalagayan_ng_tulog": [
            "sleep", "tulog", "insomnia", "hirap matulog", "sleep pattern", "sleep hygiene", 
            "kalinisan ng tulog", "bedtime routine", "sleep schedule", "consistent sleep", 
            "sleep environment", "comfortable bed", "kama", "mattress", "pillow", "unan", 
            "noise", "ingay", "light", "ilaw", "temperature", "temperatura", "relaxation", 
            "pagrerelaks", "deep breathing", "breathing exercises", "meditation", "before bed", 
            "bago matulog", "avoid screens", "electronic devices", "gadgets", "television", 
            "nap", "idlip", "daytime sleepiness", "sleep quality", "sleep duration", 
            "sleep disturbance", "nightmare", "bangungot", "sleep position", "sleep apnea",
            "excessive daytime sleeping", "sleep-wake cycle", "sleeping pills", "pagkakatulog"
        ],
        
        "pamamahala_ng_gamot": [
            "medication", "gamot", "pills", "tableta", "medicine", "prescription", "reseta", 
            "dosage", "dosis", "schedule", "iskedyul", "adherence", "pagsunod", "side effects", 
            "epekto", "adverse reactions", "drug interactions", "interaction", "pill organizer", 
            "pill box", "lalagyan ng gamot", "reminder", "paalala", "refill", "renewal", 
            "regular review", "monitoring", "pharmacist", "parmasiyutiko", "doctor", "doktor", 
            "instructions", "administering", "pagbibigay", "timing", "names of drugs", 
            "generic name", "brand name", "as needed", "PRN", "maintenance", "injections", 
            "contraindications", "kontra-indikasyon", "drug allergies", "medication list",
            "drug-food interaction", "pamahala sa gamot", "medication safety", "prescriptions",
            # Additional medication management terms
            "medication guide", "drug information", "paliwanag ng gamot", "medication teaching",
            "drug education", "patient information", "medication fears", "fear of side effects",
            "misconceptions", "medication benefits", "risk vs benefit", "package insert", 
            "drug leaflet", "medication instructions", "patient education materials",
            "medication adherence education", "drug information sheet", "simplified explanation",
            "medication counseling", "drug counseling", "medication literacy", "patient understanding",
            "medication schedule explanation", "drug administration teaching"
        ],
        
        "suporta_ng_pamilya": [
            "family", "pamilya", "caregiver", "tagapag-alaga", "spouse", "asawa", "children", 
            "anak", "relatives", "kamag-anak", "support", "suporta", "involvement", "pakikilahok", 
            "education", "pagtuturo", "training", "pagsasanay", "communication", "komunikasyon", 
            "dynamics", "roles", "tungkulin", "responsibilities", "responsibilidad", "meetings", 
            "pag-uusap", "conferences", "respite", "pahinga", "shared care", "coordination", 
            "cooperation", "understanding", "pag-unawa", "emotional support", "empathy", 
            "compassion", "assistance", "tulong", "relationship", "relasyon", "multi-generational", 
            "extended family", "pamilyang Filipino", "family training", "caregiver stress",
            "caregiver burden", "family coping", "family education", "family roles"
        ],
        
        "kalagayan_mental": [
            "emotional", "emosyonal", "mental", "psychological", "sikolohikal", "anxiety", 
            "pagkabalisa", "depression", "kalungkutan", "stress", "stress management", 
            "pamamahala ng stress", "coping", "coping strategy", "adjustment", "socialization", 
            "pakikisalamuha", "interaction", "pakikipag-ugnayan", "memory", "memorya", "cognitive", 
            "cognition", "fears", "takot", "worry", "agam-agam", "self-esteem", "confidence", 
            "tiwala sa sarili", "mood", "disposition", "kalagayan ng isip", "recreation", 
            "libangan", "activity", "stimulation", "counseling", "therapy", "therapist", 
            "validation", "reassurance", "sense of purpose", "meaning", "social connections", 
            "support group", "mental health", "psychological support", "cognitive stimulation",
            "emotional well-being", "motivation", "engagement", "dementia care", "confusion"
        ],
        
        "preventive_health": [
            "prevention", "pag-iwas", "preventive", "screening", "early detection", 
            "maagang pagtuklas", "check-up", "regular check-up", "vaccination", "bakuna", 
            "immunization", "monitoring", "pagsubaybay", "signs", "symptoms", "palatandaan", 
            "complications", "komplikasyon", "risk reduction", "health promotion", "wellness", 
            "kalusugan", "wellness plan", "health plan", "follow-up", "maintenance", 
            "routine care", "fall prevention", "infection prevention", "disease prevention", 
            "pressure ulcer prevention", "pressure injury", "bed sores", "risk factors", 
            "early intervention", "preventive strategies", "annual check-up", "taong eksaminasyon",
            "health screening", "prophylactic", "preventative care", "lifestyle modification"
        ],
        
        "vital_signs_measurements": [
            "vital signs", "vitals", "blood pressure", "presyon", "BP", "temperature", 
            "temperatura", "pulse", "pulso", "heart rate", "pulse rate", "respiratory rate", 
            "breathing rate", "paghinga", "oxygen", "oxygen saturation", "SpO2", "measurements", 
            "sukatan", "monitoring", "pagsukat", "regular monitoring", "documentation", "log", 
            "record", "talaan", "readings", "values", "abnormal", "changes", "pagbabago", 
            "elevated", "high", "mataas", "low", "mababa", "blood glucose", "blood sugar", 
            "asukal sa dugo", "weight", "timbang", "changes in weight", "equipment", "apparatus", 
            "device", "monitor", "chart", "tsart", "trends", "pattern", "home monitoring",
            "vital signs monitoring", "tracking changes", "recording", "baseline measurements"
        ]
    }

    # Initialize scoring for each sentence-section pair
    sentence_scores = {}
    for i, (sent, doc) in enumerate(zip(sentences, sentence_docs)):
        if not doc:
            continue
            
        sentence_scores[i] = {}
        
        # Get sentence length for normalization
        sent_length = len(sent.split())
        
        for section, keywords in section_keywords.items():
            # Calculate base score from keyword matches
            base_score = 0
            for keyword in keywords:
                if keyword.lower() in sent.lower():
                    # Give higher score to exact matches
                    if f" {keyword.lower()} " in f" {sent.lower()} ":
                        base_score += 1.5  # Full word match
                    else:
                        base_score += 1.0  # Partial match
            
            # Check for pattern matches
            pattern_score = 0
            if section in section_patterns:
                for pattern in section_patterns[section]:
                    if re.search(pattern, sent.lower()):
                        pattern_score += 4.0  # Higher score for pattern matches
                        break  # One strong pattern match is enough
            
            # Normalize by sentence length
            if sent_length > 20:
                base_score = base_score * (20 / sent_length) * 1.5

            # Replace with enhanced entity context scoring:
            entity_boost = get_entity_context_score(doc, section)

            # Save the combined score
            sentence_scores[i][section] = base_score + entity_boost + pattern_score
    
    # Build partial document context with sentence-section mapping
    doc_context = {"sentence_section_map": {}}

    # First determine best section for each sentence (for context mapping)
    for i, scores in sentence_scores.items():
        if scores:
            best_section = max(scores.items(), key=lambda x: x[1])[0]
            doc_context["sentence_section_map"][i] = best_section

    # NEW: Add context-aware relationship detection for contiguous sentences
    contextual_connections = {}
    for i in range(len(sentences)):
        # Initialize empty set for each sentence
        contextual_connections[i] = set()
        
        # Skip if this is the first sentence
        if i == 0:
            continue
        
        current_sent = sentences[i].lower()
        prev_sent = sentences[i-1].lower()
        
        # Then use this context in relationship detection
        relationship = get_contextual_relationship(prev_sent, current_sent, doc_context, i-1, i)
        
        # Create contextual connections based on relationship type
        if relationship in ["elaboration", "causation", "addition", "sequential_steps", 
                        "solution", "implementation", "holistic", "action"]:
            contextual_connections[i].add(i-1)
            
        # Check for thematic continuity across evaluation sections
        # Medication theme
        medication_terms = ["gamot", "medication", "pills", "tablets", "prescription", "side effects", "regimen"]
        safety_terms = ["safety", "kaligtasan", "falls", "pagkahulog", "prevention", "risk", "hazard"]
        mobility_terms = ["mobility", "paggalaw", "assistive device", "walker", "cane", "wheelchair"]
        
        same_medication_theme = any(term in prev_sent for term in medication_terms) and any(term in current_sent for term in medication_terms)
        same_safety_theme = any(term in prev_sent for term in safety_terms) and any(term in current_sent for term in safety_terms)
        same_mobility_theme = any(term in prev_sent for term in mobility_terms) and any(term in current_sent for term in mobility_terms)
        
        # Boost thematic connections
        if same_medication_theme or same_safety_theme or same_mobility_theme:
            contextual_connections[i].add(i-1)

    # Apply contextual boost to section scores
    for i in range(len(sentences)):
        if i in contextual_connections and contextual_connections[i]:
            # Apply boost to connected sentences
            for connected_idx in contextual_connections[i]:
                if connected_idx in sentence_scores:
                    connected_scores = sentence_scores[connected_idx]
                    if connected_scores:
                        best_section, best_score = max(connected_scores.items(), key=lambda x: x[1])
                        # Apply boost for context connection
                        sentence_scores[i][best_section] = sentence_scores[i].get(best_section, 0) + 3.0


    # FIRST PASS: Assign sentences with clear high scores - now uses sentence_scores
    threshold = 2.5  # Higher threshold for clear assignment
    assigned_sentences = set()
    
    for section in sections.keys():
        sorted_sentences = sorted(sentence_scores.items(), 
                                  key=lambda x: -x[1].get(section, 0))
        
        # Take up to 5 sentences with high scores
        count = 0
        max_sentences = 7  # Increase to 10 sentences or more
        
        for i, scores in sorted_sentences:
            if i in assigned_sentences:
                continue
                
            section_score = scores.get(section, 0)
            next_best_score = max([s for k, s in scores.items() if k != section], default=0)
            
            # Only assign if score is high and clearly better than other sections
            if (section_score >= threshold and 
                section_score > next_best_score * 1.25 and
                count < max_sentences):
                
                sections[section].append(sentences[i])
                assigned_sentences.add(i)
                count += 1
    
    # Second pass: Analyze entities for remaining sentences
    for i, (sent, doc) in enumerate(zip(sentences, sentence_docs)):
        if i in assigned_sentences:
            continue
            
        # Initialize section scores for this sentence
        section_scores = {section: 0 for section in sections.keys()}
        
        # Score based on entities
        if doc.ents:
            for ent in doc.ents:
                # Check entity types against each section
                if ent.label_ in ["RISK_FACTOR", "SAFETY_DEVICE", "PREVENTION"]:
                    section_scores["safety_risk_factors"] += 2.5
                    
                elif ent.label_ in ["FOOD", "NUTRITION", "DIET"]:
                    section_scores["nutrisyon_at_pagkain"] += 2.5
                    
                elif ent.label_ in ["BODY_PART", "HYGIENE", "DENTAL"]:
                    section_scores["kalusugan_ng_bibig"] += 2.0
                    
                elif ent.label_ in ["EQUIPMENT", "MOVEMENT", "EXERCISE"]:
                    section_scores["mobility_function"] += 2.5
                    
                elif ent.label_ in ["ROUTINE", "SLEEP", "REST"]:
                    section_scores["kalagayan_ng_tulog"] += 2.0
                    
                elif ent.label_ in ["MEDICATION", "PRESCRIPTION", "TREATMENT"]:
                    section_scores["pamamahala_ng_gamot"] += 3.0
                    
                elif ent.label_ in ["PERSON", "SOCIAL_REL", "SUPPORT"]:
                    section_scores["suporta_ng_pamilya"] += 2.0
                    
                elif ent.label_ in ["EMOTION", "MENTAL", "PSYCHOLOGICAL"]:
                    section_scores["kalagayan_mental"] += 2.5
                    
                elif ent.label_ in ["PREVENTION", "SCREENING", "RISK_FACTOR"]:
                    section_scores["preventive_health"] += 2.0
                    
                elif ent.label_ in ["MEASUREMENT", "VITAL_SIGN", "MONITORING"]:
                    section_scores["vital_signs_measurements"] += 2.5
        
        # Score based on keywords and patterns
        for section in sections.keys():
            # Check for pattern matches
            for pattern in section_patterns.get(section, []):
                if re.search(pattern, sent.lower()):
                    section_scores[section] += 2.0
                    break  # Only count one pattern match per section
                    
            # Check for keyword matches
            for keyword in section_keywords.get(section, []):
                if keyword.lower() in sent.lower():
                    section_scores[section] += 0.5
        
        # Also consider contextual relationships
        if i in contextual_connections and contextual_connections[i]:
            for connected_idx in contextual_connections[i]:
                if connected_idx in sentence_scores:
                    connected_best_section = max(sentence_scores[connected_idx].items(), key=lambda x: x[1])[0]
                    section_scores[connected_best_section] += 1.5  # Boost for contextual relationship
        
        # Assign to highest scoring section if score is significant
        if section_scores:
            best_section, best_score = max(section_scores.items(), key=lambda x: x[1])
            if best_score >= 1.5 and len(sections[best_section]) < 5:  # Enforce max 5 sentences per section
                sections[best_section].append(sent)
                assigned_sentences.add(i)
    
    # Third pass: Default assignment of remaining important sentences
    remaining = [i for i in range(len(sentences)) if i not in assigned_sentences]
        
    # Prefer assigning introductory sentences to pangunahing_rekomendasyon
    for i in remaining:
        if i < 2:  # First two sentences
            if len(sections["pangunahing_rekomendasyon"]) < 5:
                sections["pangunahing_rekomendasyon"].append(sentences[i])
                assigned_sentences.add(i)

    # Special handling for potentially ambiguous dental vs medication content
    for i in remaining:
        if i in sentence_scores:
            dental_score = sentence_scores[i].get("kalusugan_ng_bibig", 0)
            med_score = sentence_scores[i].get("pamamahala_ng_gamot", 0)
            
            # If the scores are close and could be ambiguous
            if abs(dental_score - med_score) < 1.5 and dental_score > 0 and med_score > 0:
                # Use specialized function to determine correct context
                context_type = detect_oral_medication_context(sentences[i])
                if context_type == "dental" and len(sections["kalusugan_ng_bibig"]) < 5:
                    sections["kalusugan_ng_bibig"].append(sentences[i])
                    assigned_sentences.add(i)
                elif context_type == "medication" and len(sections["pamamahala_ng_gamot"]) < 5:
                    sections["pamamahala_ng_gamot"].append(sentences[i])
                    assigned_sentences.add(i)

    # Sort sentences within each section to maintain original flow
    for section in sections:
        # Get the indices of assigned sentences and sort them
        indices = []
        for i, sent in enumerate(sentences):
            if sent in sections[section]:
                indices.append(i)
        
        # Reorder sentences based on original order
        sections[section] = [sentences[i] for i in sorted(indices)]
    
    # Fourth pass: Assign any remaining important sentences to most relevant sections
    for i, sent in enumerate(sentences):
        if i not in assigned_sentences:
            # Find best section based on scores
            if i in sentence_scores:
                scores = sentence_scores[i]
                if scores:
                    best_section = max(scores.items(), key=lambda x: x[1])[0]
                    # Force assign even if we exceed max sentences
                    sections[best_section].append(sent)
                    assigned_sentences.add(i)
    
    sections = handle_section_overflow(sections, sentence_scores, sentences, assigned_sentences, max_sentences)
    
    # Convert lists to strings and apply post-processing
    processed_sections = {}
    for section, sents in sections.items():
        if sents:
            # Join complete sentences with proper spacing
            section_text = " ".join(sents)
            # Apply post-processing to fix formatting issues
            section_text = post_process_summary(section_text)
            processed_sections[section] = section_text
    
    return processed_sections

def post_process_summary(summary):
    """Comprehensive post-processing for section text to fix common issues."""
    import re
    
    if not summary:
        return summary
        
    # Fix merged words (common issues from sample text)
    # Format: problematic_pattern -> correct_form
    word_fixes = {
        'arawmas': 'araw mas',
        'pagtulognagigising': 'pagtulog nagigising',
        'expressionslalo': 'expressions lalo',
        'pagsimangotay': 'pagsimangot ay',
        'anakparehong': 'anak—parehong',  # Use em dash for clarity
        'patternsa': 'pattern—sa',
        'secret-monitor': 'monitor',
        'tugtugin.Madalas': 'tugtugin. Madalas',
        'tulogSa': 'tulog. Sa'
    }
    
    # Apply all specific word fixes
    for wrong, correct in word_fixes.items():
        summary = summary.replace(wrong, correct)
    
    # Fix spacing issues
    summary = re.sub(r'\s+', ' ', summary)
    summary = re.sub(r'\s([,.;:])', r'\1', summary)
    
    # Fix missing spaces (more comprehensive)
    summary = re.sub(r'([a-z])([A-Z])', r'\1 \2', summary)  # Space between lowercase and uppercase
    
    # Fix common spacing errors
    summary = re.sub(r'(\w+)([,.;:])(\w+)', r'\1\2 \3', summary)  # Add space after punctuation if missing
    
    # Fix period spacing and double periods
    summary = re.sub(r'\.+', '.', summary)  # Multiple periods to single period
    summary = re.sub(r'\.([a-zA-Z])', r'. \1', summary)  # Ensure space after period
    
    # Fix common Filipino prepositions and conjunctions that run together
    prepositions = ['sa', 'ng', 'para', 'dahil', 'tungkol']
    for prep in prepositions:
        # Make sure preposition has spaces around it
        summary = re.sub(fr'\b{prep}([a-zA-Z])', fr'{prep} \1', summary)
    
    # Fix missing "sa" and "ng" in common phrases
    summary = re.sub(r'dahil (sakit|lumalalang)', r'dahil sa \1', summary)
    summary = re.sub(r'timing (symptoms|ng)', r'timing ng \1', summary)
    
    # Fix specific patterns with missing words
    summary = re.sub(r'\b(dahil)\b(?!\s+(sa|ng))', r'\1 sa', summary)
    summary = re.sub(r'nakikisalamuha\s+sa', r'Siya ay nakikisalamuha sa', summary)
    
    # Fix dangling sentences ending with conjunctions or prepositions
    for prep in ['at', 'ng', 'sa', 'para', 'dahil', 'tungkol']:
        summary = re.sub(fr'\.\s*{prep}\s*$', f'.', summary)
        summary = re.sub(fr'{prep}\s*\.$', '.', summary)
    
    # Fix common errors in Filipino medical text
    summary = re.sub(r'ang ang', 'ang', summary)
    summary = re.sub(r'ng ng', 'ng', summary)
    summary = re.sub(r'sa sa', 'sa', summary)
    summary = re.sub(r'para ang', 'para sa', summary)
    summary = re.sub(r'upang ang', 'upang', summary)
    
    # Fix capitalization after periods
    summary = re.sub(r'(\. )([a-z])', lambda m: f"{m.group(1)}{m.group(2).upper()}", summary)
    
    # Ensure proper capitalization at start
    if summary and summary[0].islower():
        summary = summary[0].upper() + summary[1:]
        
    # Ensure ending with period
    if summary and not summary[-1] in ['.', '!', '?']:
        summary += '.'
    
    return summary

def handle_section_overflow(sections, section_scores, sentences, assigned_sentences, max_sentences_per_section):
    """Handle overflow sentences by reassigning to alternative sections or overflow container."""
    
    # Initialize overflow section
    if "additional_information" not in sections:
        sections["additional_information"] = []
    
    # Find sentences that would exceed section limits
    overflow_sentences = []
    for section_name, section_sentences in sections.items():
        if section_name == "additional_information":
            continue
            
        # Check if section exceeds max length
        if len(section_sentences) > max_sentences_per_section:
            # Get the overflow sentences (those beyond the max)
            overflow = section_sentences[max_sentences_per_section:]
            # Remove them from original section
            sections[section_name] = section_sentences[:max_sentences_per_section]
            
            # Add to overflow with metadata
            for sent in overflow:
                sent_idx = next((i for i, s in enumerate(sentences) if s == sent), None)
                if sent_idx is not None:
                    overflow_sentences.append((sent_idx, sent, section_name))
    
    # Try to reassign overflow sentences to their next best section
    for sent_idx, sent, original_section in overflow_sentences:
        if sent_idx in section_scores:
            # Get all section scores for this sentence
            scores = section_scores[sent_idx]
            
            # Find next best section
            alternative_sections = sorted(
                [(section, score) for section, score in scores.items() 
                 if section != original_section and len(sections[section]) < max_sentences_per_section],
                key=lambda x: -x[1]  # Sort by descending score
            )
            
            # If we found an alternative section with room, use it
            if alternative_sections and alternative_sections[0][1] > 0:
                alt_section = alternative_sections[0][0]
                sections[alt_section].append(sent)
            else:
                # Otherwise put in overflow section
                sections["additional_information"].append(sent)
    
    # Remove the overflow section if empty
    if not sections["additional_information"]:
        del sections["additional_information"]
        
    return sections

def get_entity_context_score(sent_doc, section_name):
    """Get score based on entity context for section classification."""
    context_score = 0
    
    # Skip if no entities found
    if not sent_doc.ents:
        return context_score
    
    # Calculate context score from entities and their relationships
    seen_entity_types = set()
    
    for ent in sent_doc.ents:
        # Get confidence score for this entity in this section
        confidence = get_entity_section_confidence(ent.text, ent.label_, section_name)
        
        # Apply score based on confidence
        context_score += confidence
        
        # Small bonus for entity type diversity to favor rich content
        if ent.label_ not in seen_entity_types:
            context_score += 0.5
            seen_entity_types.add(ent.label_)
    
    return context_score