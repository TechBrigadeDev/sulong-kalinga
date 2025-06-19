import re
from nlp_loader import nlp
from entity_extractor import extract_structured_elements
from text_processor import split_into_sentences

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
            r'(nag-aalala|worried|concerned) (siya|siyang|sila) (tungkol sa|dahil sa)',
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
        ]
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
            
            # Enhance score based on entity types present
            entity_boost = 0
            if doc.ents:
                for ent in doc.ents:
                    if section == "mga_sintomas" and ent.label_ in ["SYMPTOM", "DISEASE"]:
                        entity_boost += 3
                    elif section == "kalagayan_pangkatawan" and ent.label_ in ["BODY_PART", "MEASUREMENT"]:
                        entity_boost += 2.5
                    elif section == "kalagayan_mental" and ent.label_ in ["COGNITIVE", "EMOTION"]:
                        entity_boost += 2.5
                    elif section == "aktibidad" and ent.label_ in ["ADL"]:
                        entity_boost += 2.5
                    elif section == "kalagayan_social" and ent.label_ in ["SOCIAL_REL", "PER", "ENVIRONMENT"]:
                        entity_boost += 2
            
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
        max_sentences = 5
        
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
    max_sentences_per_section = 5
    
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
    
    # Ensure at least one section has content
    if all(not sents for sents in result.values()) and sentences:
        result["mga_sintomas"] = sentences[:5]  # Limit to 5 sentences
    
    # Convert lists to strings and apply post-processing
    processed_sections = {}
    for section, sents in result.items():
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
        "pagbabago_sa_pamumuhay": []
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
        ]
    }
    
    # First pass: Match sentences to sections based on strong signals
    assigned_sentences = set()
    
    for section, patterns in section_patterns.items():
        # Limit to 5 sentences per section (changed from 3)
        section_count = 0
        max_per_section = 5
        
        for i, sent in enumerate(sentences):
            if i in assigned_sentences or section_count >= max_per_section:
                continue
                
            # Check if sentence matches any pattern for this section
            for pattern in patterns:
                if re.search(pattern, sent.lower()):
                    sections[section].append(sent)
                    assigned_sentences.add(i)
                    section_count += 1
                    break
    
    # Second pass: Analyze entities for remaining sentences
    for i, (sent, doc) in enumerate(zip(sentences, sentence_docs)):
        if i in assigned_sentences:
            continue
            
        section_scores = {section: 0 for section in sections.keys()}
        
        # Score based on entities
        if doc.ents:
            for ent in doc.ents:
                if ent.label_ == "RECOMMENDATION":
                    section_scores["pangunahing_rekomendasyon"] += 2
                elif ent.label_ in ["TREATMENT_METHOD", "TREATMENT"]:
                    section_scores["mga_hakbang"] += 2
                elif ent.label_ in ["MONITORING", "HEALTHCARE_REFERRAL"]:
                    section_scores["pangangalaga"] += 2
                elif ent.label_ in ["DIET_RECOMMENDATION", "FOOD", "HOME_MODIFICATION"]:
                    section_scores["pagbabago_sa_pamumuhay"] += 2
        
        # Score based on keywords and context
        for section, patterns in section_patterns.items():
            for word in sent.split():
                if any(pattern.lower() in word.lower() for pattern in patterns):
                    section_scores[section] += 0.5
        
        # Assign to highest scoring section if score is significant
        best_section = max(section_scores.items(), key=lambda x: x[1])
        if best_section[1] >= 1.5:  # More stringent threshold
            if len(sections[best_section[0]]) < 5:  # Respect max 5 sentences per section
                sections[best_section[0]].append(sent)
                assigned_sentences.add(i)
    
    # Third pass: Default assignment of remaining important sentences
    remaining = [i for i in range(len(sentences)) if i not in assigned_sentences]
    
    # Prefer assigning introductory sentences to pangunahing_rekomendasyon
    for i in remaining:
        if i < 2:  # First two sentences
            if len(sections["pangunahing_rekomendasyon"]) < 5:  # Changed from 3 to 5
                sections["pangunahing_rekomendasyon"].append(sentences[i])
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