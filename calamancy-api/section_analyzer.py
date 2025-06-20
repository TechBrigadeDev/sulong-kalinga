import re
from nlp_loader import nlp
from entity_extractor import extract_structured_elements
from text_processor import split_into_sentences

def extract_sections_improved(sentences, doc_type="assessment"):
    """Extract and categorize sections with improved handling of complete sentences."""
    print(f"Extracting sections for {doc_type}, {len(sentences)} sentences")
    
    print(f"Input text split into {len(sentences)} sentences")
    if len(sentences) > 0:
        print(f"First sentence: {sentences[0][:50]}...")
        print(f"Last sentence: {sentences[-1][:50]}...")
        
    # Process all sentences with Calamancy NLP first
    try:
        sentence_docs = [nlp(sentence) for sentence in sentences]
    except Exception as e:
        print(f"Error processing sentences: {e}")
        sentence_docs = []
    
    # Initialize result dictionary with expanded sections
    result = {
        # Original sections
        "mga_sintomas": [],
        "kalagayan_pangkatawan": [],
        "kalagayan_mental": [],
        "kalagayan_social": [],
        "aktibidad": [],
        
        # Newly added sections
        "kalusugan_ng_bibig": [],        # Oral/dental health
        "mobility_function": [],         # Mobility aids and assistive devices
        "kalagayan_ng_tulog": [],        # Sleep patterns
        "pain_discomfort": [],           # Pain management
        "nutrisyon_at_pagkain": [],      # Nutrition and dietary needs
        "suporta_ng_pamilya": [],        # Family support
        "pamamahala_ng_gamot": [],       # Medication management
        "safety_risk_factors": [],       # Safety and risk factors
        "vital_signs_measurements": [],  # Vital signs
        "medical_history": [],           # Medical history
        "preventive_health": []          # Preventive health
    }
    
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
        ],

        "kalusugan_ng_bibig": [
            r'(oral|dental|ngipin|pustiso|dentures|teeth|tooth|bibig|dila|gums|gilagid)',
            r'(plaque|cavity|cavities|tooth decay|dental caries|halitosis|bad breath|mabahong hininga)',
            r'(pagtoothbrush|brushing|toothbrush|toothpaste|floss|mouthwash)',
            r'(dentist|dental check|dental appointment|dental care)',
            r'(tumatanggal|natanggal|tanggalin) (ang|na|ang mga) (ngipin|pustiso|dentures)',
            r'(sumasakit|masakit) (ang|na|ang mga) (ngipin|gilagid|bibig|dila)',
            r'(inflammation|swollen|namaga) (ang|na|ang mga) (gums|gilagid)',
            r'(may|nagkaroon ng) (sores|sugat|lesions) sa (bibig|dila|gilagid)',
            r'(nahihirapang|hirap|nahihirapan) (ngumuya|kumain|tumikim) (ng|dahil)',
            r'(oral hygiene|dental hygiene|pangangalaga ng bibig|oral care)',
            r'(nagtatanggal|pagtatanggal) (ng|sa) (pustiso|dentures)'
        ],
        
        "hygiene_habits": [
            r'(personal hygiene|kalinisan|naliligo|paliligo|nalilinis|pag-aayos)',
            r'(routine|gawain|nakagawian) (sa|ng|para sa) (paglilinis|kalinisan)',
            r'(frequency|dalas|beses|daily|araw-araw|lingguhan|monthly) (ng|sa) (pagligo|paghuhugas)',
            r'(self-care|pangangalaga sa sarili|personal care)',
            r'(hindi|ayaw|tumanggi|tumatanggi) (maligo|maghugas|maglinis|mag-ayos)',
            r'(nakalilimutan|nakakalimutan|nalilimutan) (maligo|maghugas|maglinis)',
            r'(dumumi|mag-CR|gamitin ang banyo|gumamit ng toilet)',
            r'(nahihiyang|nahiya|embarrassed) (maligo|maghugas|maglinis)',
            r'(bawas|bumaba|tumaas|lalong) (ang|na|ang mga) (pangangalaga sa sarili|self-care|personal hygiene)',
            r'(nangangailangan|kailangan) (ng|sa) (tulong|gabay) sa (pag-aayos|paglilinis|paghuhugas)',
            r'(shower|bath|bathing|washing) (practices|habits|routine)',
            r'(grooming|nail care|paggugupit|pagputol ng kuko)',
            r'(pagbabago|nagbago) (sa|ng) (personal hygiene|pangangalaga sa katawan)'
        ],
        
        "nutrisyon_at_pagkain": [
            r'(pagkain|diet|nutrition|sustansya|nutritional|pagkakain)',
            r'(hindi|ayaw|tumanggi|tumatanggi) (kumain|uminom|tumikim)',
            r'(problema|issue|concern) sa (pagkain|pag-inom|paglunok|panunaw)',
            r'(nawalan|nawalan ng gana|mababa) (ang|na|ang mga) (appetite|ganang kumain)',
            r'(kumakain|kinakain) (lang|lamang|ng) (konti|very little|minimal)',
            r'(pagbabago|nagbago) (sa|ng) (diet|eating habits|pagkain|eating patterns)',
            r'(matigas|maasim|matamis|maalat) na (pagkain|inumin)',
            r'(food preferences|gustong pagkain|preferred meals|food choices)',
            r'(nutritional deficiency|malnutrition|dehydration|kulang sa sustansya)',
            r'(bumaba|nabawasan|tumaas) (ang|na|ang mga) (timbang|weight)',
            r'(selective|picky|choosy) (sa|kapag) (pagkain|kumakain|food)',
            r'(meal preparation|paghahanda ng pagkain|planning meals)',
            r'(difficulty|kahirapan|hirap) (sa|kapag) (paglunok|pagnguya|swallowing|chewing)',
            r'(special diet|dietary restrictions|bawal na pagkain)',
            r'(tube feeding|nasogastric tube|feeding tube)'
        ],
        
        "mobility_function": [
            r'(mobility|paggalaw|paglalakad|pagkilos|kakayahang gumalaw)',
            r'(nahihirapang|hirap|nahihirapan) (tumayo|umupo|gumapang|gumalaw|maglakad|umakyat)',
            r'(nangangailangan|kailangan) (ng|sa) (wheelchair|walker|cane|tungkod|andador)',
            r'(balance|balanse|steady|matatag) (kapag|habang|tuwing|sa) (nakatayo|naglalakad)',
            r'(natutumba|bumabagsak|falls|falling|nadudulas) (kapag|habang|tuwing|sa)',
            r'(limited|limitado) (ang|na|ang mga) (movement|galaw|range of motion)',
            r'(transfer|paglipat) (mula sa|papunta sa|sa) (kama|upuan|banyo)',
            r'(independent|nakapag-iisa|assisted|may tulong) (sa|kapag) (paglalakad|pagkilos)',
            r'(posture|posisyon ng katawan|pagkakaupo|pagkakatayo)',
            r'(bed mobility|mobility sa kama|pagbabago ng posisyon)',
            r'(joint stiffness|paninigas ng kasukasuan|muscle rigidity)',
            r'(gait|pattern ng paglalakad|lakad|hakbang) (hindi normal|abnormal|unstable)',
            r'(coordination|koordinasyon|fine motor skills|gross motor)',
            r'(nangangawit|pagod|nanghihina) (kapag|habang|tuwing|sa) (gumagalaw|naglalakad)'
        ],
        
        "pain_discomfort": [
            r'(sakit|pananakit|masakit|sumasakit|kirot|painful|pain)',
            r'(nararamdaman|nakakaramdam|feels) (ng|sa) (sakit|pananakit|kirot)',
            r'(chronic|acute|persistent|paulit-ulit|pabalik-balik) (pain|sakit|pananakit)',
            r'(level|intensity|antas) (ng|sa|na) (sakit|pananakit|kirot)',
            r'(scale of|sa scale na|pain scale) (1-10|sampu|one to ten)',
            r'(aray|ouch|ay|aaray) (kapag|habang|tuwing|sa)',
            r'(grimace|facial expressions|mukha) (kapag|habang|tuwing|sa) (may sakit|masakit)',
            r'(pain medication|pain reliever|gamot para sa sakit)',
            r'(radiating|lumalipat|kumakalat) (ang|na|ang mga) (sakit|pananakit|kirot)',
            r'(burning|sensation|parang nasusunog|ngawit|tingling|maga)',
            r'(neuropathic pain|nerve pain|sakit dulot ng nerve damage)',
            r'(joint pain|sakit sa kasukasuan|arthritis pain)',
            r'(back pain|sakit sa likod|low back pain|upper back pain)',
            r'(chest pain|sakit sa dibdib|heart-related pain)',
            r'(abdominal pain|sakit ng tiyan|stomach pain)',
            r'(headache|sakit ng ulo|migraine)',
            r'(breakthrough pain|flare-up|biglaang paglala ng sakit)'
        ],
        
        "pamamahala_ng_gamot": [
            r'(gamot|medikasyon|medication|tablets|pills|capsule)',
            r'(prescription|reseta|prescribed|inireseta) (drugs|medication|gamot)',
            r'(dosage|dose|dosis|timing|schedule) (ng|sa|na) (gamot|medication)',
            r'(compliance|adherence|sumusunod) (sa|ng) (pag-inom|paggamit) (ng|sa) (gamot)',
            r'(nakalilimutan|nakakalimutan|nalilimutan) (uminom|inumin|gamitin) (ang|na|ang mga) (gamot)',
            r'(misses|missed|skips|skipped|hindi naiinom) (doses|dose|gamot)',
            r'(self-medicating|sariling pagreseta|taking unprescribed)',
            r'(side effects|adverse reactions|epekto) (ng|sa|na) (gamot)',
            r'(allergies|allergic reaction|adverse effects) (sa|ng|na) (gamot)',
            r'(binabago|iniiba|changing) (ang|na|ang mga) (dosage|instructed dose)',
            r'(nagtatanong|questionning|concerned about) (sa|ng|na) (gamot)',
            r'(instructions|gabay|tagubilin) (ng|sa) (pag-inom|paggamit) (ng|sa) (gamot)',
            r'(medication review|pagsusuri ng gamot|drug review)',
            r'(polypharmacy|multiple medications|maraming gamot)',
            r'(inhaler|nebulizer|insulin|injection) (technique|paggamit|administration)'
        ],
        
        "kalagayan_ng_tulog": [
            r'(tulog|pagtulog|natutulog|sleep|sleeping)',
            r'(insomnia|hirap matulog|can\'t sleep|hindi makatulog)',
            r'(quality|quality ng|kalidad ng) (tulog|pagtulog|sleep)',
            r'(hours|oras|duration|tagal) (ng|sa|na) (tulog|pagtulog|sleep)',
            r'(pahinga|nagpapahinga|rest|resting)',
            r'(disrupted|interrupted|putol-putol) (sleep|tulog)',
            r'(nagigising|gumigising|wakes up) (sa|tuwing|kapag) (gabi|madaling-araw)',
            r'(napapagod|pagod|tired|exhausted) (kahit|despite|even with|after) (matulog|natutulog)',
            r'(sleep medication|sleeping pills|gamot pampatulog)',
            r'(sleep apnea|sleep disorder|obstructive sleep)',
            r'(snoring|snores|malakas ang hilik)',
            r'(excessive|sobrang|over) (daytime|pang-araw) (sleepiness|antok)',
            r'(nightmare|bad dreams|bangungot|panaginip)',
            r'(bedtime routine|ritwal bago matulog|sleep schedule)',
            r'(circadian rhythm|sleep cycle|pattern ng pagtulog)',
            r'(nakakatulog|falling asleep) (sa|during|habang) (araw|umaga|hapon)',
            r'(restless|hindi mapakali|malikot) (kapag|habang|tuwing) (natutulog|tulog)'
        ],
        
        "cognitive_function": [
            r'(cognition|cognitive|pag-iisip|mental faculties)',
            r'(memory|memorya|naaalala|recall|natatandaan)',
            r'(forgetfulness|pagkalimot|nakakalimutan|nakalilimutan)',
            r'(confusion|kalituhan|nalilito|confused)',
            r'(orientation|oryentasyon) (sa|ng|sa) (time|lugar|place|person)',
            r'(hindi alam|doesn\'t know) (ang|na|ang mga) (araw|petsa|pangalan|lugar)',
            r'(executive function|decision making|pagdedesisyon)',
            r'(attention span|concentration|focus|pokus|atensyon)',
            r'(cognitive decline|dementia|Alzheimer\'s)',
            r'(mental status|mental capacity|kakayahang mag-isip)',
            r'(nagri-repeat|paulit-ulit|repeating) (ang|na|ang mga) (tanong|sagot|kwento)',
            r'(disoriented|disoryentado|hindi alam ang lugar)',
            r'(processing|proseso|pag-proseso) (ng|sa|na) (information|impormasyon)',
            r'(learning|pagkatuto|learning new skills)',
            r'(abstract thinking|capacity for abstraction)',
            r'(judgment|pagpapasya|discernment)',
            r'(reasoning|pangangatwiran|logic|lohika)',
            r'(nagtatanong|questioning|asks about) (paulit-ulit|repeatedly)'
        ],
        
        "emotional_state": [
            r'(mood|emosyon|damdamin|feelings|emotion)',
            r'(depressed|depression|malungkot|lungkot|sad|sadness)',
            r'(anxiety|takot|kaba|nerbyos|kinakabahan|nervous|anxious)',
            r'(irritable|mairita|madaling magalit|short tempered)',
            r'(frustrated|frustration|nagagalit|galit|angry|anger)',
            r'(apathetic|walang pakialam|detached|disinterested)',
            r'(mood swings|emotional lability|biglaang pagbabago ng mood)',
            r'(hopeless|hopelessness|nawalan ng pag-asa|helpless)',
            r'(worthless|feeling of worthlessness|pakiramdam na walang halaga)',
            r'(crying|iyak|tearful|lumuluha|napapaiyak)',
            r'(overwhelmed|sobrang stressed|nabibigatan)',
            r'(grief|grieving|nagdadalamhati|nagluluksa)',
            r'(fearful|kinatatakutan|frightened|scared)',
            r'(happy|masaya|content|pleased|satisfied)',
            r'(irritability|pagkamairita|bad mood)',
            r'(monotonous|flat|walang expression|bland) (ang|na) (mood|facial expression)',
            r'(suicidal|self-harm|thoughts of hurting self)',
            r'(coping mechanism|paraan ng pag-cope|ways of dealing)'
        ],
        
        "communication_abilities": [
            r'(communication|pakikipag-communicate|pakikipag-usap)',
            r'(verbal|non-verbal|written) (communication|expression)',
            r'(speech|pagsasalita|artikulasyon|pananalita)',
            r'(slurred|malabo|hindi malinaw) (ang|na|ang mga) (speech|salita|pananalita)',
            r'(aphasia|dysarthria|speech impediment)',
            r'(comprehension|pag-intindi|understanding)',
            r'(nahihirapang|hirap|nahihirapan) (magsalita|magsabi|sabihin|express)',
            r'(responding|pagsagot|answering) (sa|ng|na) (tanong|question)',
            r'(communication devices|assistive technology para sa communication)',
            r'(sigurado|unclear|unclear speech|hindi klaro) (ang|na|ang mga) (sagot|speech)',
            r'(receptive language|expressive language)',
            r'(language barrier|communication breakdown)',
            r'(gestures|hand signals|body language|sign language)',
            r'(naming|word finding|paghahanap ng salita) (problems|difficulties|issues)',
            r'(repeating|palaging sinasabi|saying over and over)',
            r'(nakikinig|listening|paying attention) (kapag|habang|tuwing) (kinakausap)'
        ],
        
        "suporta_ng_pamilya": [
            r'(support system|supporta|tulong|family support)',
            r'(caregiver|nagaalaga|tagapag-alaga|taga-alaga)',
            r'(family members|mga kapamilya|relatives|kamag-anak)',
            r'(home aid|home assist|helper|katulong)',
            r'(spouse|asawa|partner|significant other)',
            r'(children|anak|mga anak)',
            r'(live|nakatira|stay) (with|sa|kasama) (family|pamilya)',
            r'(alone|mag-isa|nakatira mag-isa|lives alone)',
            r'(social network|social connections|friends|kaibigan)',
            r'(neighbors|kapitbahay|komunidad|community support)',
            r'(church|simbahan|religious group) (support|activities)',
            r'(social isolation|socialization|pakikisalamuha sa iba)',
            r'(visits|bumibisita|dalaw) (regularly|regularly|madalas)',
            r'(dependency|dependent|umaasa) (sa|kay|kina) (family|pamilya|anak)',
            r'(emotional support|social support|financial support)',
            r'(strained relationships|tension|alitan) sa (family|pamilya|anak)',
            r'(caregiving burden|burnout|pagod sa pag-aalaga)'
        ],
        
        "safety_risk_factors": [
            r'(safety|kaligtasan|safety concern|hazards)',
            r'(risk|peligro|panganib|banta) (ng|sa|sa) (fall|pagkahulog|aksidente)',
            r'(falls history|dating pagkahulog|nahuhulog|natutumba)',
            r'(wandering|paglayas|paglalakad-lakad|pagkalat)',
            r'(fire hazard|panganib ng sunog|electrical hazards)',
            r'(medication errors|mali sa pag-inom|wrong dosage)',
            r'(driving|nagmamaneho|nagda-drive) (safety|concerns|issues)',
            r'(cannot manage|hindi kaya) (daily tasks|routine|basic needs)',
            r'(self-neglect|not taking care|hindi nag-aalaga sa sarili)',
            r'(environmental hazards|obstacles|hadlang|kaguluhan) (sa|ng|sa) (bahay)',
            r'(safety precautions|pangingat|preventive measures)',
            r'(supervision|pangangailangan ng bantay|needs watching)',
            r'(vulnerable|madaling maabuso|madaling masaktan)',
            r'(abuse|pang-aabuso|neglect|napapabayaan)',
            r'(stairs|hagdanan|steps) (safety|issues|problem)',
            r'(bathroom safety|ligtas na pagligo|bathing hazards)',
            r'(ambulatory aids|tulong sa paglakad) (improper use|wrong usage)'
        ],
        
        "vital_signs_measurements": [
            r'(vital signs|vital|senyales)',
            r'(blood pressure|presyon|BP|hypertension|hypotension)',
            r'(pulse|pulso|heart rate|rate ng puso)',
            r'(temperature|temperatura|lagnat|fever)',
            r'(respiratory rate|respiratory|breathing rate|rate ng paghinga)',
            r'(oxygen|O2|oxygen saturation|saturation)',
            r'(weight|timbang|weighs|nagwe-weigh)',
            r'(height|tangkad|taas)',
            r'(BMI|body mass index)',
            r'(within normal|normal range|abnormal) (ang|na|ang mga) (vital signs|vitals)',
            r'(elevated|mataas|increased) (ang|na|ang mga) (presyon|blood pressure)',
            r'(low|mababa|decreased) (ang|na|ang mga) (heart rate|pulso)',
            r'(monitoring|checking|sinusukat) (ang|na|ang mga) (BP|vitals)',
            r'(blood sugar|glucose|sugar levels)',
            r'(rhythm|ritmo) (ng|sa|na) (puso|pulso|heart)',
            r'(tachycardia|bradycardia|irregular heartbeat)',
            r'(fluid status|hydration status|edema|pamamaga)'
        ],
        
        "medical_history": [
            r'(medical history|kasaysayang medikal|health history)',
            r'(diagnosis|diagnosed|diyagnosis|na-diagnose)',
            r'(chronic condition|chronic illness|malubhang karamdaman)',
            r'(co-morbidity|co-morbidities|multiple conditions)',
            r'(surgery|operasyon|inoperahan) (history|in the past|dati)',
            r'(hospitalization|na-ospital|confined) (recently|kamakailaan|dati)',
            r'(trauma|aksidente|injury|insidente) (in the past|dati)',
            r'(medical procedures|tests|laboratory work)',
            r'(cardiologist|neurologist|specialist|doctor) (visit|check-up)',
            r'(medical devices|pacemaker|implanted devices)',
            r'(immunization|vaccination|bakuna) (history|record)',
            r'(allergies|allergic reaction|hypersensitivity)',
            r'(family history|genetic conditions|hereditary)',
            r'(test results|findings|laboratory result)',
            r'(progression|disease course|worsening of)',
            r'(prognosis|outlook|expected course)'
        ],
        
        "preventive_health": [
            r'(screening|check-up|regular na pagsusuri)',
            r'(vaccination|bakuna|immunization)',
            r'(preventive|pangingat|preventative) (care|hakbang|measures)',
            r'(health promotion|pampalusog|wellness activities)',
            r'(risk reduction|pagbabawas ng risk|prevention of)',
            r'(monitoring|pag-monitor|regular checking)',
            r'(health goals|targets sa kalusugan|health targets)',
            r'(follow-up|susunod na appointment|follow-up care)',
            r'(annual exams|taunang pagsusuri|yearly check-up)',
            r'(cancer screening|screening for|early detection)',
            r'(cardiovascular|diabetes|osteoporosis) (prevention|screening)',
            r'(infection control|infection prevention|infection precaution)',
            r'(exposure to|exposed to|nalantad sa) (risk factors|harmful)',
            r'(vaccines|vaccination|mga bakuna) (schedule|required)'
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
        ],

        "kalusugan_ng_bibig": [
            # Dental terms
            "ngipin", "teeth", "tooth", "dental", "dentist", "dentition", 
            "pustiso", "dentures", "braces", "oral", "bibig", "mouth",
            "dila", "tongue", "gums", "gilagid", "palate", "ngala-ngala",
            
            # Dental conditions
            "plaque", "tartar", "cavity", "cavities", "dental caries", "bungang-araw",
            "halitosis", "bad breath", "mabahong hininga", "gingivitis",
            "periodontitis", "tooth decay", "worn teeth", "gasgas na ngipin",
            "loose teeth", "mababang ngipin", "missing teeth", "kulang na ngipin",
            "tooth sensitivity", "masakit na ngipin sa malamig",
            
            # Dental care
            "toothbrush", "pagtoothbrush", "toothpaste", "dental floss", "mouthwash",
            "oral hygiene", "dental check-up", "dental cleaning", "linis ng ngipin",
            "oral care", "pangangalaga ng bibig", "dental procedures",
            "brushing", "flossing", "gargle", "pagmumumog",
            
            # Dental symptoms
            "toothache", "sakit ng ngipin", "gum bleeding", "nagdurugo ang gilagid",
            "sores", "lesions", "ulcer", "abscess", "impacted", "inflammation",
            "swollen gums", "maga ang gilagid", "receding gums",
            "difficulty chewing", "hirap ngumuya", "jaw pain", "sakit ng panga",
            
            # Dental procedures
            "extraction", "bunot", "filling", "pasta", "root canal", "dental surgery",
            "cleaning", "prophylaxis", "adjustment", "dental implant", "bridge"
        ],
        
        "hygiene_habits": [
            # General hygiene
            "hygiene", "kalinisan", "cleanliness", "malinis", "makalinis",
            "dirty", "marumi", "dumi", "personal care", "grooming", "pag-aayos",
            "self-care", "pangangalaga sa sarili", 
            
            # Activities
            "bathing", "pagliligo", "naliligo", "shower", "paliligo", "bath",
            "washing", "paghuhugas", "handwashing", "paghuhugas ng kamay",
            "changing clothes", "pagpapalit ng damit", "laundering", "paglalaba",
            "combing hair", "pagsusuklay", "brushing hair", "grooming",
            
            # Patterns/frequency
            "routine", "gawain", "nakagawian", "habit", "kaugalian", 
            "frequency", "dalas", "daily", "araw-araw", "weekly", "lingguhan",
            "monthly", "buwanan", "regular", "palagi", "occasional", "minsan",
            "rarely", "bihira", "never", "hindi kailanman",
            
            # Assistance
            "needs help", "nangangailangan ng tulong", "independent", "nakakapag-isa",
            "dependent", "dependent sa iba", "assistance", "tulong", "supervised",
            "unsupervised", "walang nagbabantay", "reminders", "paalala",
            
            # Challenges
            "refuses", "ayaw", "tumanggi", "declines", "hesitant", "reluctant",
            "forgetting", "nakakalimutan", "neglecting", "napapabayaan",
            "struggling with", "nahihirapan sa", "unable to", "hindi kayang",
            "embarrassed about", "nahihiya sa", "uncomfortable", "hindi komportable",
            
            # Products/equipment
            "soap", "sabon", "shampoo", "toiletries", "toilet paper", "towel",
            "tuwalya", "deodorant", "perfume", "talcum powder", "lotion",
            "assistive devices", "modified bathroom", "grab bars", "shower chair"
        ],
        
        "nutrisyon_at_pagkain": [
            # Food and diet
            "food", "pagkain", "diet", "nutrisyon", "nutrition", "meals", "meal",
            "breakfast", "almusal", "lunch", "tanghalian", "dinner", "hapunan",
            "snacks", "meryenda", "eating", "kumain", "food intake", "food consumption",
            
            # Appetite and eating patterns
            "appetite", "ganang kumain", "hunger", "gutom", "thirst", "uhaw",
            "decreased appetite", "walang ganang kumain", "increased appetite",
            "masiba", "picky eater", "mapili sa pagkain", "selective eating",
            "food refusal", "ayaw kumain", "food aversion", "binge eating",
            
            # Special diets/needs
            "soft diet", "malambot na pagkain", "pureed food", "blended food",
            "diabetic diet", "low sodium", "mababang asin", "low cholesterol",
            "low fat", "mababang taba", "high protein", "mataas na protina",
            "special diet", "dyeta", "dietitian", "nutritionist",
            "food allergies", "allergy sa pagkain", "food intolerance",
            
            # Nutritional concerns
            "weight loss", "pagbaba ng timbang", "weight gain", "pagtaba",
            "malnutrition", "underweight", "payat", "overweight", "sobrang taba",
            "obesity", "obese", "cachexia", "nutrient deficiency", "kulang sa sustansya",
            "dehydration", "dehydrated", "overhydration", "water intoxication",
            
            # Feeding issues
            "feeding", "pagpapakain", "feeding difficulties", "swallowing difficulty",
            "dysphagia", "hirap lumunok", "choking hazard", "risk of choking",
            "aspiration risk", "nasogastric tube", "feeding tube", "PEG tube",
            "assistance with feeding", "tulong sa pagkain", "spoon feeding",
            
            # Food preferences
            "preferences", "gusto", "preferred foods", "favorite food", "paborito",
            "dislikes", "ayaw", "refused foods", "cultural food preferences",
            "traditional diet", "lutong bahay", "comfort food", "cravings", "pagnanasa"
        ],
        
        "mobility_function": [
            # General mobility
            "mobility", "paggalaw", "movement", "paglalakad", "walking",
            "transfers", "paglipat", "ambulation", "range of motion", "galaw",
            "motility", "activity", "aktibidad", "function", "pagkilos",
            
            # Mobility aids
            "wheelchair", "silya de gulong", "walker", "andador", "cane", "tungkod",
            "crutches", "saklay", "scooter", "gait belt", "grab bars", "hand rails",
            "handrails", "hawakan", "assistive device", "mobility aid",
            
            # Mobility status
            "independent", "nag-iisa", "nakakapag-isa", "dependent", "umaasa",
            "requires assistance", "nangangailangan ng tulong", "supervision needed",
            "standby assist", "one-person assist", "two-person assist", "total assist",
            "bed bound", "nakaratay", "chair bound", "confined sa silya",
            
            # Mobility issues
            "trouble walking", "hirap maglakad", "difficulty standing", "hirap tumayo",
            "cannot walk", "hindi makalakad", "unsteady gait", "hindi matatag",
            "shuffling", "kakaladkad ang paa", "limping", "pilay", "staggering",
            "staggers", "falls", "nahuhulog", "stumbling", "natitisod",
            "paralysis", "paralisis", "hemiplegia", "weakness", "panghihina",
            
            # Physical abilities
            "strength", "lakas", "endurance", "stamina", "resistance", "flexibility",
            "kabilisan", "agility", "coordination", "koordinasyon", "balance",
            "balanse", "stability", "stroke", "muscle weakness", "mahina ang kalamnan",
            "joint stiffness", "matigas ang kasukasuan", "limited motion", "limitadong galaw",
            
            # Activities
            "climbing stairs", "pag-akyat ng hagdan", "standing up", "pagtayo",
            "sitting down", "pag-upo", "bending", "pagbaluktot", "reaching", "pag-abot",
            "turning", "pagpihit", "repositioning", "shifting position", "pagbabago ng posisyon",
            "bed mobility", "rolling over", "getting out of bed", "pagbangon",
            "transferring", "paglipat", "transitioning", "changing positions"
        ],
        
        "pain_discomfort": [
            # General pain terms
            "pain", "sakit", "pananakit", "kirot", "aches", "sore", "masakit",
            "discomfort", "uncomfortable", "hindi komportable", "tenderness",
            "distress", "matinding sakit", "suffering", "pagdurusa", "hurt",
            "nasasaktan", "nasasaktan", "aching", "sumasakit", "painful", "masakit",
            
            # Pain qualities
            "sharp", "matulis", "dull", "malabo", "burning", "nasusunog", "shooting",
            "parang kuryente", "stabbing", "parang sinasaksak", "throbbing",
            "kumikislot", "pounding", "pumupukpok", "radiating", "lumalawak",
            "constant", "palaging", "intermittent", "paminsan-minsan",
            
            # Pain locations
            "headache", "sakit ng ulo", "migraine", "back pain", "sakit ng likod",
            "joint pain", "sakit ng kasukasuan", "arthritis pain", "muscle pain",
            "sakit ng kalamnan", "abdominal pain", "sakit ng tiyan", "chest pain",
            "sakit ng dibdib", "neck pain", "sakit ng leeg", "shoulder pain",
            "sakit ng balikat", "hip pain", "sakit ng balakang", "leg pain",
            "sakit ng binti", "foot pain", "sakit ng paa",
            
            # Pain intensity
            "severe", "matindi", "moderate", "katamtaman", "mild", "banayad",
            "excruciating", "sobrang sakit", "unbearable", "hindi matiis",
            "tolerable", "matitiis", "scale", "pain scale", "intensity",
            "10/10", "worst pain", "pinakamasakit", "5/10", "medium", "medyo",
            "1/10", "slight", "bahagya", "very painful", "napaksakit",
            
            # Pain patterns
            "chronic pain", "paulit-ulit", "chronic", "matagal na", "acute pain",
            "bagong labas", "acute", "biglaan", "recurrent", "palaging bumabalik",
            "worsening", "lumalala", "improving", "bumubuti", "fluctuating",
            "nagbabago-bago", "comes and goes", "dumarating at nawawala",
            "breakthrough pain", "timing of pain", "kailan sumasakit",
            
            # Pain responses
            "grimace", "cries out", "sumisigaw", "groaning", "ungol",
            "facial expressions", "facial grimacing", "frowning", "nakasimangot",
            "wincing", "napapangiwi", "guarding", "protective of", "protective posturing",
            "clutching", "hawak nang mahigpit", "restlessness", "hindi mapakali",
            
            # Pain management
            "pain medication", "gamot sa sakit", "analgesics", "pain killers",
            "pain relief", "pampatanggal ng sakit", "management", "pain control",
            "pain management", "interventions", "ice pack", "yelo", "heat", "warm compress",
            "mainit na compress", "massage", "masahe", "positioning", "pagpoposisyon"
        ],
        
        "pamamahala_ng_gamot": [
            # Medication terms
            "medication", "gamot", "medicine", "pills", "tabletas", "tablet",
            "capsule", "kapsula", "drugs", "droga", "injection", "iniksyon",
            "inhaler", "inhaler", "patch", "patches", "drops", "eyedrops",
            "patak sa mata", "syrup", "syrup", "topical", "pampahid",
            "ointment", "cream", "cream", "prescription", "reseta", "non-prescription",
            
            # Medication administration
            "taking", "pag-inom", "intake", "administration", "pagbibigay",
            "dosage", "dosis", "dose", "route", "daanan", "frequency", "dalas",
            "timing", "oras", "schedule", "iskedyul", "morning", "umaga",
            "noon", "tanghali", "evening", "gabi", "before meals", "bago kumain",
            "after meals", "pagkatapos kumain", "as needed", "kung kailangan",
            
            # Medication issues
            "side effects", "side effect", "adverse effects", "reaction",
            "interactions", "drug interactions", "allergic reaction", "allergy",
            "alerhiya", "overdose", "sobrang dosis", "underdose", "kulang na dosis",
            "toxicity", "contraindications", "bawal isabay", "precautions",
            "warnings", "babala", "missed dose", "nakalimutang dosis", 
            
            # Medication compliance
            "compliance", "adherence", "pagsunod", "non-compliance", "hindi sinusunod",
            "refusing", "ayaw uminom", "declines medication", "skipping doses",
            "nilalaktawan", "self-medicating", "sariling pag-inom", "adjusting dose",
            "binabago ang dosis", "taking as prescribed", "iniinom ayon sa reseta",
            "reminders", "paalala", "medication errors", "pagkakamali sa gamot",
            
            # Medication management
            "pill box", "pill organizer", "medication list", "listahan ng gamot",
            "medication history", "medication reconciliation", "medication review",
            "refills", "pagpuno ulit", "prescribing", "pagreseta", "dispensing",
            "organizing", "pag-oorganisa", "monitoring", "pag-monitor", "pharmacy",
            "botika", "pharmacist", "parmasyutiko", "doctor's orders", "utos ng doktor",
            
            # Specific medications
            "antibiotics", "antibiyotiko", "pain medication", "painkillers",
            "blood pressure medicine", "gamot sa presyon", "hypertension medicine",
            "diabetes medicine", "insulin", "insulina", "anti-inflammatory",
            "steroids", "steroid", "diuretic", "pampaihi", "laxative", "pampadumi",
            "antidepressant", "sleeping pill", "pampatulog", "thyroid medicine"
        ],
        
        "kalagayan_ng_tulog": [
            # General sleep terms
            "sleep", "tulog", "pagtulog", "sleeping", "natutulog", "bedtime",
            "oras ng pagtulog", "nap", "idlip", "rest", "pahinga", "bedtime routine",
            "gawain bago matulog", "sleep ritual", "ritwal bago matulog",
            "drowsiness", "antok", "sleepiness", "pagka-antok",
            
            # Sleep quality
            "quality", "kalidad", "sleep quality", "kalidad ng tulog", "deep sleep",
            "malalim na tulog", "light sleep", "mababaw na tulog", "REM sleep",
            "restful", "maayos na tulog", "unrefreshing", "hindi nakaka-refresh",
            "restorative", "nakakapagpalakas", "sound sleep", "mahimbing na tulog",
            
            # Sleep duration
            "duration", "tagal", "hours", "oras", "hours of sleep", "oras ng tulog",
            "short sleep", "maikling tulog", "long sleep", "mahabang tulog",
            "oversleeping", "sobrang tulog", "undersleeping", "kulang sa tulog",
            "adequate sleep", "sapat na tulog", "sleep debt", "utang na tulog",
            
            # Sleep problems
            "insomnia", "insomniya", "difficulty falling asleep", "hirap matulog",
            "trouble staying asleep", "hirap manatiling tulog", "waking up",
            "nagigising", "early waking", "agapay na gumigising", "interrupted sleep",
            "putol-putol na tulog", "disrupted sleep", "disturbed sleep", "nightmares",
            "bangungot", "bad dreams", "masamang panaginip", "night terrors",
            "sleep talking", "nagsasalita habang tulog", "sleep walking", "nagwawalk habang tulog",
            "snoring", "hilik", "sleep apnea", "heavy snoring", "malakas na hilik",
            "grinding teeth", "pagngangalit ng ngipin", "bedwetting", "pag-ihi sa kama",
            
            # Sleep-related behaviors
            "bedtime routine", "nightly routine", "pre-sleep activities",
            "morning routine", "waking up routine", "tuwing umaga", "getting out of bed",
            "pagbangon sa kama", "staying in bed", "pagtambay sa kama", "lying down",
            "nakahiga", "position during sleep", "posisyon habang tulog", "side sleeper",
            "nakatagilid", "back sleeper", "nakahiga nang tuwid", "stomach sleeper",
            "nakadapa", "tossing and turning", "pihit nang pihit", "restless sleeper",
            
            # Sleep medications/aids
            "sleeping pills", "pampatulog", "sleep medications", "gamot pampatulog",
            "melatonin", "sedatives", "tranquilizers", "hypnotics", "sleep aids",
            "tulong sa pagtulog", "relaxation techniques", "paraan ng pagpaparelaks",
            "white noise", "sleep mask", "takip sa mata", "earplugs", "takip sa tainga",
            
            # Environmental factors
            "bedroom", "kwarto", "sleeping environment", "kapaligiran ng tulog",
            "noise", "ingay", "light", "liwanag", "temperature", "temperatura",
            "comfortable bed", "komportableng kama", "comfortable pillow",
            "komportableng unan", "mattress", "kolchon", "sleep surface",
            "dark room", "madilim na kwarto", "quiet", "tahimik", "disturbances",
            "istorbo", "pet in bed", "alagang hayop sa kama"
        ],
        
        "cognitive_function": [
            # General cognitive terms
            "cognition", "cognitive", "pag-iisip", "thinking", "mental function",
            "brain function", "cognitive abilities", "cognitive skills",
            "mental capacity", "kakayahang mag-isip", "intellectual function",
            "mental processes", "brain health", "kalusugan ng utak",
            
            # Memory
            "memory", "memorya", "alaala", "recall", "paggunita", "remembering",
            "pag-alala", "forgets", "nakakalimutan", "forgetting", "pagkalimot",
            "short-term memory", "maikling memorya", "long-term memory", "matagal na memorya",
            "recent memory", "memorya ng mga kamakailan", "remote memory", 
            "memorya ng matagal na", "procedural memory", "working memory",
            
            # Orientation
            "orientation", "oryentasyon", "oriented", "nakakaalam", "disoriented",
            "hindi nakakaalam", "confused", "naguluhan", "confusion", "kalituhan",
            "knows", "alam", "doesn't know", "hindi alam", "aware", "aware",
            "unaware", "hindi aware", "time orientation", "oryentasyon sa oras",
            "place orientation", "oryentasyon sa lugar", "person orientation",
            "oryentasyon sa tao", "situation awareness", "kamalayan sa sitwasyon",
            
            # Specific cognitive functions
            "attention", "atensyon", "concentration", "konsentrasyon", "focus", 
            "pokus", "distractible", "madaling maistorbo", "executive function",
            "decision making", "paggawa ng desisyon", "judgment", "paghatol", 
            "reasoning", "pangangatwiran", "problem solving", "paglutas ng problema", 
            "abstract thinking", "abstraktong pag-iisip", "sequencing", "pagkakasunod-sunod",
            "processing", "pagproseso", "processing speed", "bilis ng pag-iisip",
            "learning", "pagkatuto", "comprehension", "pag-unawa", "understanding",
            
            # Cognitive conditions
            "dementia", "demensya", "Alzheimer's", "mild cognitive impairment",
            "bahagyang kapansanan sa pag-iisip", "cognitive decline", "pagbaba ng kakayahang",
            "mental deterioration", "cognitive disorder", "sakit sa pag-iisip",
            "sundowning", "sundown syndrome", "delirium", "deliryo", "confusion",
            "kalituhan", "disorientation", "kawalan ng oryentasyon",
            
            # Cognitive symptoms
            "forgetful", "malilimutin", "easily confused", "madaling malito",
            "repeating", "paulit-ulit", "repetitive", "questioning repeatedly",
            "paulit-ulit na nagtatanong", "losing items", "nawawalan ng gamit",
            "misplacing", "naglalagay sa maling lugar", "poor concentration",
            "hirap mag-focus", "difficulty following", "hirap sumunod",
            "unable to learn", "hindi makatuto", "can't remember", "hindi matandaan",
            "doesn't recognize", "hindi namumukhaan", "confusing names",
            "naliligaw", "getting lost", "disoriented to time", "disoriented to place"
        ],
        
        "emotional_state": [
            # General emotional terms
            "emotions", "emosyon", "emotional", "emosyonal", "feelings", "pakiramdam",
            "mood", "mood", "affect", "disposition", "disposisyon", "state of mind",
            "kalagayan ng isip", "emotional health", "kalusugang emosyonal",
            
            # Positive emotions
            "happy", "masaya", "happiness", "kaligayahan", "joy", "galak", "content",
            "kontento", "satisfied", "nasisiyahan", "pleased", "pleased", "glad", "natutuwa",
            "cheerful", "masayahin", "positive", "positibo", "good spirits", "magandang disposisyon",
            "upbeat", "energetic", "malakas ang loob", "hopeful", "may pag-asa",
            "calm", "kalmado", "peaceful", "mapayapa", "relaxed", "relaxed",
            
            # Negative emotions
            "sad", "malungkot", "unhappy", "hindi masaya", "depressed", "depressed",
            "depression", "depresyon", "down", "low", "mababa ang mood", "blue", "nalulumbay",
            "melancholy", "melancholy", "grief", "lungkot", "grieving", "nagluluksa", 
            "angry", "galit", "anger", "poot", "irritable", "iritable", "irritated",
            "naiinis", "frustrated", "bwisit", "agitated", "nagagalit",
            "anxious", "balisa", "anxiety", "pagkabalisa", "worried", "nag-aalala",
            "concern", "pag-aalala", "fear", "takot", "scared", "natatakot", "afraid",
            "nervous", "kinakabahan", "tension", "stress", "pagod sa isip",
            "apathetic", "walang pakialam", "indifferent", "hindi pinapansin",
            "numb", "walang maramdaman", "detached", "disconnected", "walang koneksyon",
            
            # Emotional distress
            "distressed", "nagdurusa", "suffering", "paghihirap", "emotional pain",
            "emosyonal na sakit", "anguish", "hinagpis", "torment", "pagdurusa",
            "despair", "kawalan ng pag-asa", "hopelessness", "desperado", "desperate",
            "agonizing", "napakahirap", "overwhelmed", "sobra-sobra", "restless", "hindi mapakali", 
            "agitated", "balisa", "upset", "naguguluhan", "emotional outburst", "biglaang paglabas ng emosyon",
            
            # Mood disorders
            "mood swings", "pabago-bagong emosyon", "lability", "mood changes", 
            "pagbabagong-damdamin", "emotional liability", "erratic", "hindi maaasahan",
            "unpredictable", "hindi mahulaan", "fluctuating", "pabago-bago", "major depression", 
            "anxiety disorder", "adjustment disorder", "bipolar", "mania", "manic",
            
            # Emotional responses
            "crying", "pag-iyak", "tearful", "lumuluha", "sobbing", "humihikbi",
            "laughing", "tumatawa", "laughing inappropriately", "hindi angkop na pagtawa",
            "smiling", "ngumingiti", "flat affect", "walang expression", "blank expression",
            "walang expression sa mukha", "animated", "buhay na buhay", "expressionless", "walang ekspresyon",
            
            # Self-perception
            "self-esteem", "self-worth", "worthlessness", "walang halaga", 
            "worthless", "walang silbi", "helpless", "walang magawa", "hopeless", "walang pag-asa",
            "useless", "walang pakinabang", "inadequacy", "hindi sapat", "inadequate", "hindi sapat",
            "guilt", "guilt", "guilty", "mayroon kasalanan", "shame", "nahihiya", "embarrassed",
            "napapahiya", "remorse", "pagsisisi", "regret", "panghihinayang",
            
            # Coping
            "coping", "coping", "managing emotions", "pamamahala ng emosyon",
            "emotional control", "kontrol sa emosyon", "emotional regulation",
            "self-soothing", "panggiginhawa sa sarili", "coping mechanisms",
            "mekanismo para sa pag-cope", "emotional support", "suportang emosyonal",
            "vulnerability", "kahinaan", "resilience", "katatagan"
        ],
        
        "communication_abilities": [
            # General communication terms
            "communication", "komunikasyon", "expressing", "pagpapahayag",
            "expression", "ekspresyon", "communicating", "pakikipag-communicate",
            "conversation", "pag-uusap", "dialogue", "diyalogo", "interaction",
            "pakikipag-ugnayan", "correspondence", "communicative", "madaling kausapin",
            
            # Verbal communication
            "speech", "pagsasalita", "speaking", "pagsalita", "talks", "nagsasalita",
            "verbal", "berbal", "verbally", "sa pamamagitan ng salita", "articulation",
            "artikulasyon", "pronunciation", "bigkas", "volume", "lakas ng boses",
            "loud", "malakas ang boses", "soft-spoken", "mahina ang boses",
            "mumbling", "bumubulong", "slurred", "malalim na pagsasalita", "tone",
            "tono", "pitch", "tinis", "quality of speech", "kalidad ng pagsasalita",
            
            # Language issues
            "language", "wika", "vocabulary", "bokabularyo", "word finding", 
            "paghahanap ng salita", "word choice", "pagpili ng salita", "grammar",
            "gramatika", "syntax", "sentence structure", "istraktura ng pangungusap",
            "fluency", "kahusayan", "bilingual", "dalawang wika", "mother tongue",
            "katutubong wika", "first language", "unang wika", "second language",
            "ikalawang wika", "language barrier", "hadlang sa wika",
            
            # Speech disorders/issues
            "aphasia", "aphasia", "dysarthria", "dysarthria", "stuttering", 
            "pagutal-utal", "stammering", "pautal-utal", "cluttering", "mabilis na pagsasalita",
            "lisp", "pagkakamali sa pagbigkas", "speech impediment", "kapansanan sa pagsasalita",
            "articulation disorder", "kapansanan sa artikulasyon", "voice disorder",
            "kapansanan sa boses", "monotone", "iisang tono", "prosody", "intonation", "tono",
            
            # Comprehension/expression
            "comprehension", "pag-unawa", "understanding", "pagkakaintindi", "receptive language",
            "expressive language", "processing", "pagproseso", "interpreting", "pag-intindi",
            "following directions", "pagsunod sa utos", "understanding instructions",
            "pag-intindi ng tagubilin", "making self understood", "pagpapaliwanag sa sarili",
            "expressing needs", "pagpapahayag ng pangangailangan", "conveying thoughts",
            "pagpapahayag ng kaisipan", "expressing emotions", "pagpapahayag ng damdamin",
            
            # Non-verbal communication
            "non-verbal", "non-verbal", "body language", "wika ng katawan", "gestures",
            "galaw ng kamay", "gesturing", "paggamit ng senyas", "facial expressions",
            "ekspresyon ng mukha", "eye contact", "mata-sa-mata", "lack of eye contact",
            "walang eye contact", "posture", "postura", "sign language", "sign language",
            "pointing", "pagtuturo", "nodding", "pagtango", "shaking head", "pag-iling",
            
            # Communication challenges
            "difficulty communicating", "kahirapan sa pakikipag-usap", "communication problems",
            "problema sa komunikasyon", "frustration", "pagkabigo", "can't express",
            "hindi maipahayag", "unable to communicate", "hindi makapagkomunika",
            "communication breakdown", "pagkasira ng komunikasyon", "misunderstanding",
            "hindi pagkakaintindihan", "misinterpretation", "maling interpretasyon",
            
            # Assistive communication
            "communication board", "communication device", "assistive technology",
            "teknolohiyang pangtulong", "speech-generating device", "AAC",
            "augmentative communication", "alternative communication", 
            "picture board", "picture communication", "text-to-speech", "interpreter",
            "tagapagsalin", "translator", "translator app", "app para sa pagsasalin"
        ],
        
        "suporta_ng_pamilya": [
            # Family support
            "family", "pamilya", "spouse", "asawa", "husband", "mister", "wife",
            "misis", "partner", "kabiyak", "children", "mga anak", "son", "anak na lalaki",
            "daughter", "anak na babae", "siblings", "kapatid", "brother", "kuya",
            "brother", "kapatid na lalaki", "sister", "ate", "sister", "kapatid na babae",
            "relatives", "kamag-anak", "extended family", "pinalawak na pamilya",
            "in-laws", "biyenan", "grandchildren", "apo", "cousins", "pinsan",
            "family dynamics", "dinamika ng pamilya", "family conflict", "away-pamilya",
            "family tension", "tensyon sa pamilya", "family relationship", "relasyon sa pamilya",
            
            # Caregiving
            "caregiver", "tagapag-alaga", "carer", "nangangalaga", "care provider",
            "nagbibigay-alaga", "primary caregiver", "pangunahing tagapag-alaga",
            "secondary caregiver", "pangalawang tagapag-alaga", "formal caregiver",
            "professional caregiver", "propesyonal na tagapag-alaga", "caregiver burden",
            "pasanin ng tagapag-alaga", "caregiver stress", "stress ng tagapag-alaga",
            "caregiver support", "suporta para sa tagapag-alaga", "caregiving responsibilities",
            "responsibilidad ng tagapag-alaga",
            
            # Friend support
            "friends", "kaibigan", "close friends", "malapit na kaibigan",
            "social circle", "social network", "friendship", "pakikipagkaibigan",
            "peers", "kasukat", "contemporaries", "ka-edad", "social connections",
            "koneksyon sa ibang tao", "acquaintances", "kakilala", "neighbors",
            "kapitbahay", "social group", "samahan", "club", "club", "organization",
            "organisasyon", "association", "asosasyon", "colleagues", "katrabaho",
            "workmates", "kasamahan sa trabaho",
            
            # Professional support
            "healthcare team", "medical support", "suportang medikal", "doctor",
            "doktor", "nurse", "nars", "social worker", "social worker", "therapist",
            "therapist", "psychologist", "psychologist", "psychiatrist", "psychiatrist",
            "counselor", "counselor", "case manager", "case manager", "support worker",
            "occupational therapist", "physiotherapist", "physical therapist",
            "home health aide", "personal care attendant", "personal care assistant",
            
            # Community support
            "community", "komunidad", "community services", "serbisyo ng komunidad",
            "local services", "lokal na serbisyo", "support group", "support group",
            "peer support", "church", "simbahan", "religious group", "grupong relihiyoso",
            "parish", "parokya", "mosque", "mosque", "temple", "temple", "religious community",
            "komunidad ng relihiyon", "volunteer", "boluntaryo", "community center",
            "senior center", "center para sa matatanda",
            
            # Support system quality
            "adequate support", "sapat na suporta", "inadequate support", "hindi sapat na suporta",
            "strong support", "malakas na suporta", "weak support", "mahinang suporta",
            "lack of support", "kakulangan ng suporta", "social isolation", "social na isolation",
            "lonely", "malungkot", "alone", "nag-iisa", "abandoned", "pinabayaan",
            "neglected", "napapabayaan", "estranged", "hiwalay", "distance from family",
            "malayo sa pamilya", "overseas family", "pamilyang nasa ibang bansa",
            
            # Living arrangements
            "lives with", "nakatira kasama", "lives alone", "nakatira mag-isa",
            "independent living", "nakatira mag-isa", "assisted living", "may tulong sa pagtira",
            "shared housing", "nakikitira", "multi-generational home", "bahay na maraming henerasyon",
            "nursing home", "bahay-kalinga", "retirement village", "village para sa retirado",
            "residential care", "home for the aged", "home for the elderly", "bahay ng matanda"
        ],
        
        "safety_risk_factors": [
            # Fall risks
            "fall", "pagkahulog", "fall risk", "risk sa pagkahulog", "fall history",
            "kasaysayan ng pagkahulog", "falling", "pagkakahulog", "fall prevention",
            "pag-iwas sa pagkahulog", "unsteady", "hindi matatag", "stumbling", "natitisod",
            "tripping", "natitisod", "slipping", "nadudulas", "balance issues",
            "problema sa balanse", "dizziness", "pagkahilo", "vertigo", "postural instability",
            "instability", "kawalang-tatag", "unsteady gait", "hindi matatag na lakad",

            # Environmental hazards (continuation)
            "hazards", "panganib", "environmental hazards", "panganib sa kapaligiran", 
            "tripping hazards", "batong katitisuran", "slipping hazards", "madulas na sahig",
            "clutter", "kalat", "obstacles", "hadlang", "poor lighting", "mahinang ilaw",
            "dim lighting", "madilim na ilaw", "uneven surfaces", "hindi pantay na sahig",
            "loose rugs", "gumagalaw na carpet", "loose carpets", "hindi nakakabit na carpet",
            "stairs", "hagdanan", "steps", "mga baitang", "no handrails", "walang hawakan", 
            "missing grab bars", "walang hawakan sa banyo", "wet floors", "basang sahig",
            "slippery floors", "madulas na sahig", "extension cords", "nakaharang na kable",
            "furniture placement", "nakaharang na muwebles", "bathroom hazards", "panganib sa banyo",
            "bathtub safety", "kaligtasan sa bathtub", "shower safety", "kaligtasan sa shower",
            "kitchen hazards", "panganib sa kusina", "kitchen safety", "kaligtasan sa kusina",
            "fire hazard", "panganib ng sunog", "electrical hazard", "panganib ng kuryente",
            "gas leak", "tagas ng gas", "carbon monoxide", "carbon monoxide poisoning",
            "smoke detector", "smoke alarm", "fire extinguisher", "fire escape", "emergency plan",
            "emergency exit", "emergency contact", "emergency response", "safety plan",
            "safety assessment", "safety devices", "security measures", "door locks", "window locks",
            "wandering risk", "risk of wandering", "paglalakad-lakad", "pagkalat",
            "getting lost", "nawawala", "disorientation", "home security", "personal alarm",
            "medical ID", "medical alert", "medical bracelet", "pangsunog na alarma"
            ],

            "vital_signs_measurements": [
            # General vital signs terms
            "vital signs", "vital", "senyales", "vitals", "vital statistics", 
            "measurements", "pagsukat", "vital parameters", "mga sukat", "readings", 
            "health metrics", "physiological measures", "clinical measurements",

            # Blood pressure
            "blood pressure", "presyon", "BP", "hypertension", "high blood pressure",
            "mataas na presyon", "hypotension", "low blood pressure", "mababang presyon",
            "systolic", "systolic pressure", "diastolic", "diastolic pressure",
            "mmHg", "millimeters of mercury", "normal blood pressure", "elevated BP",
            "stage 1 hypertension", "stage 2 hypertension", "hypertensive crisis", 
            "blood pressure monitor", "monitoring blood pressure", "BP check",

            # Pulse/heart rate
            "pulse", "pulso", "heart rate", "rate ng puso", "beats per minute", "BPM",
            "tachycardia", "bradycardia", "irregular heartbeat", "irregular pulse",
            "arrhythmia", "palpitations", "heart rhythm", "ritmo ng puso",
            "rapid heart rate", "slow heart rate", "normal pulse", "mabilis na tibok",
            "mabagal na tibok", "regular pulse", "regular na pulso", "radial pulse",
            "carotid pulse", "apical pulse", "pulse deficit", "pulse pressure",

            # Temperature
            "temperature", "temperatura", "fever", "lagnat", "high temperature",
            "mataas na temperatura", "low temperature", "mababang temperatura",
            "hypothermia", "hyperthermia", "normal temperature", "febrile",
            "afebrile", "pyrexia", "degrees Celsius", "degrees Fahrenheit", 
            "temperature reading", "temporal temperature", "oral temperature",
            "axillary temperature", "rectal temperature", "body temperature",

            # Respiratory
            "respiratory rate", "rate ng paghinga", "breathing rate", "respiration",
            "respirations per minute", "breaths per minute", "respiratory pattern",
            "hinga", "hininga", "paghinga", "tachypnea", "bradypnea", "dyspnea",
            "hirap huminga", "difficulty breathing", "shortness of breath",
            "shallow breathing", "mababaw na paghinga", "deep breathing", 
            "malalim na paghinga", "labored breathing", "hinahabol ang paghinga",
            "respiratory effort", "work of breathing", "oxygen requirement",

            # Oxygen saturation
            "oxygen", "oxygen saturation", "O2 saturation", "SpO2", "O2 sat",
            "oxygen level", "pulse oximetry", "pulse ox", "oxygen concentration",
            "room air", "supplemental oxygen", "oxygen therapy", "oxygen requirement",
            "desaturation", "hypoxemia", "hypoxia", "normal oxygen level",

            # Weight/height measurements
            "weight", "timbang", "weighs", "nagwe-weigh", "body weight", "kilogram", "kg",
            "pound", "lbs", "height", "tangkad", "taas", "centimeter", "cm",
            "inch", "feet", "foot", "ft", "BMI", "body mass index", "ideal body weight",
            "actual body weight", "weight loss", "weight gain", "weight fluctuation",

            # Other measurements
            "blood sugar", "glucose", "blood glucose", "BSL", "BG", "sugar level",
            "diabetes monitoring", "hyperglycemia", "hypoglycemia", "HbA1c",
            "hemoglobin", "Hgb", "Hb", "CBC", "complete blood count", "cholesterol",
            "lipid panel", "triglycerides", "blood test results"
            ],

            "medical_history": [
            # General medical history
            "medical history", "kasaysayang medikal", "health history", "past medical history",
            "history", "kasaysayan", "medical record", "medical documentation", "chart", 
            "patient history", "health record", "health background", "medical background",
            "medical information", "medical profile", "clinical history", "health profile",

            # Diagnoses
            "diagnosis", "diyagnosis", "diagnosed", "na-diagnose", "medical condition",
            "kondisyon", "condition", "sakit", "illness", "illness history", "disease", 
            "karamdaman", "health condition", "disorder", "syndrome", "clinical diagnosis",
            "differential diagnosis", "presumptive diagnosis", "confirmed diagnosis",
            "working diagnosis", "diagnostic impression", "clinical assessment",

            # Chronic conditions
            "chronic condition", "chronic illness", "chronic disease", "matagalang sakit",
            "malubhang karamdaman", "long-term condition", "ongoing condition",
            "persistent condition", "chronic health issue", "chronic health problem",
            "lifelong condition", "degenerative condition", "progressive disease",

            # Comorbidities
            "co-morbidity", "co-morbidities", "comorbid conditions", "multiple conditions",
            "multiple diagnoses", "concurrent conditions", "concurrent diagnoses",
            "coexisting conditions", "coexisting illnesses", "multi-system disease",
            "complex medical history", "multiple medical issues", "health complications",

            # Surgeries and procedures
            "surgery", "operasyon", "surgical history", "surgical procedure", "inoperahan",
            "operation", "procedure", "prosidyur", "intervention", "invasive procedure",
            "treatment procedure", "minor surgery", "major surgery", "elective surgery",
            "emergency surgery", "outpatient procedure", "inpatient procedure",
            "surgical intervention", "post-operative", "post-surgery", "recovery",

            # Hospitalizations
            "hospitalization", "na-ospital", "hospital admission", "hospital stay",
            "inpatient", "confined", "admission", "hospital confinement", "hospital care",
            "inpatient care", "hospital treatment", "acute care", "critical care",
            "intensive care", "ICU stay", "emergency admission", "urgent admission",
            "planned admission", "length of stay", "discharge", "hospital discharge",

            # Trauma and injuries
            "trauma", "injury", "pinsala", "accident", "aksidente", "incident", "insidente",
            "physical trauma", "traumatic injury", "wound", "sugat", "fracture", "bali",
            "sprain", "pilay", "strain", "bone injury", "soft tissue injury", "head injury",
            "brain injury", "spinal injury", "trauma history", "accident history",

            # Medical tests and procedures
            "medical test", "laboratory test", "lab work", "laboratory results",
            "diagnostic test", "test results", "blood work", "urinalysis", "imaging",
            "x-ray", "CT scan", "MRI", "ultrasound", "EKG", "ECG", "biopsy", "endoscopy",
            "colonoscopy", "screening test", "preventive screening", "diagnostic imaging",

            # Specialist care
            "specialist", "specialist care", "specialist consultation", "espesyalista",
            "referral", "specialist referral", "consultation", "konsultasyon", "second opinion",
            "expert opinion", "specialty care", "tertiary care", "specialized treatment",
            "specialized management", "subspecialty care", "specialty clinic", "specialty hospital",

            # Medical devices and implants
            "medical device", "implant", "prosthetic", "prosthesis", "artificial", "artipisyal",
            "assistive device", "implanted device", "pacemaker", "defibrillator", 
            "stent", "artificial joint", "artificial hip", "artificial knee",
            "orthopedic implant", "heart valve", "intraocular lens", "cochlear implant",
            "medical hardware", "internal fixation", "external fixation",

            # Immunizations and preventive care
            "immunization", "vaccination", "bakuna", "vaccine", "shots", "preventive care",
            "immunization history", "vaccination record", "vaccine history", "booster shot",
            "preventative medicine", "preventive health", "health maintenance",
            "routine immunization", "childhood vaccines", "adult vaccines", "flu shot",
            "pneumonia vaccine", "tetanus shot", "immunization schedule",

            # Allergies and reactions
            "allergies", "allergy", "alerhiya", "allergic reaction", "reaksyon", 
            "hypersensitivity", "adverse reaction", "side effect", "drug allergy",
            "food allergy", "environmental allergy", "contact allergy", "seasonal allergy",
            "anaphylaxis", "angioedema", "hives", "pantal", "rash", "itching", "swelling",
            "medication intolerance", "drug intolerance", "latex allergy",

            # Family health history
            "family history", "family medical history", "inherited condition",
            "genetic condition", "hereditary disease", "familial disease", 
            "genetic predisposition", "family risk factor", "family pattern",
            "parent's health", "sibling's health", "maternal history", "paternal history",
            "genetic testing", "genetic screening", "genetic counseling"
            ],

            "preventive_health": [
            # General preventive terms
            "prevention", "preventive care", "preventive health", "preventative",
            "preventive measure", "pangingat", "pagpigil", "prophylaxis", "prophylactic",
            "preventative medicine", "pag-iwas", "preventative care", "prevention strategy",
            "proactive health", "proactive measures", "health maintenance", "health protection",

            # Health screenings
            "screening", "pagsusuri", "health screening", "screening test",
            "early detection", "early identification", "routine screening",
            "diagnostic screening", "screening program", "mass screening",
            "targeted screening", "selective screening", "screening interval",
            "screening result", "positive screening", "negative screening",
            "false positive", "false negative", "screening guidelines",

            # Check-ups
            "check-up", "medical check-up", "annual exam", "annual physical",
            "routine examination", "routine physical", "regular check-up",
            "comprehensive examination", "physical examination", "complete physical",
            "executive check-up", "wellness exam", "health assessment", "periodic health visit",
            "well visit", "preventive visit", "annual wellness visit", "preventive exam",

            # Vaccinations
            "vaccination", "immunization", "bakuna", "vaccine", "shots",
            "booster", "booster dose", "primary series", "vaccine schedule",
            "immunization record", "vaccine history", "flu shot", "trangkaso bakuna",
            "pneumonia vaccine", "pneumococcal vaccine", "shingles vaccine",
            "tetanus shot", "Tdap vaccine", "hepatitis vaccine", "varicella vaccine",
            "HPV vaccine", "COVID vaccine", "vaccine compliance", "vaccine acceptance",

            # Health promotion
            "health promotion", "wellness", "wellness program", "health education",
            "patient education", "health awareness", "health literacy", "health teaching",
            "lifestyle guidance", "preventive counseling", "anticipatory guidance",
            "health advice", "wellness advice", "health recommendation", "self-care education",

            # Risk reduction
            "risk reduction", "risk management", "risk factor modification",
            "risk assessment", "risk evaluation", "risk stratification", 
            "high-risk status", "modifiable risk", "non-modifiable risk",
            "risk factor", "contributing factor", "predisposing factor",
            "risk profile", "risk score", "cardiovascular risk", "health risk",
            "disease risk", "peligro", "panganib", "risk mitigation", "healthier choices",

            # Monitoring and surveillance
            "monitoring", "surveillance", "health monitoring", "disease surveillance",
            "continuous monitoring", "periodic monitoring", "routine monitoring",
            "self-monitoring", "home monitoring", "remote monitoring", "telehealth monitoring",
            "health tracking", "symptom monitoring", "vital sign monitoring", "monitoring protocol",
            "biometric monitoring", "health parameter tracking", "health diary", "health journal",

            # Health goals and targets
            "health goal", "health target", "health objective", "health aim",
            "personal health goal", "individualized goal", "SMART goal",
            "health improvement target", "target parameter", "target range",
            "target value", "health outcome", "desired outcome", "expected outcome",
            "short-term goal", "long-term goal", "treatment goal", "care goal",

            # Follow-up care
            "follow-up", "follow-up care", "follow-up visit", "follow-up appointment",
            "scheduled follow-up", "routine follow-up", "continuity of care",
            "continuity of treatment", "ongoing care", "maintenance therapy",
            "maintenance care", "continuing care", "long-term care", "long-term management",
            "subsequent visit", "follow-up assessment", "follow-up evaluation",

            # Specific preventive services
            "cancer screening", "cancer prevention", "mammogram", "breast exam",
            "breast cancer screening", "cervical screening", "Pap smear", "Pap test",
            "colorectal screening", "colonoscopy", "sigmoidoscopy", "fecal occult blood test",
            "FOBT", "FIT test", "prostate screening", "PSA test", "skin cancer screening",
            "lung cancer screening", "osteoporosis screening", "bone density test", "DEXA scan",
            "cardiovascular screening", "heart disease screening", "heart health check",
            "diabetes screening", "glucose test", "A1C test", "cholesterol screening", "lipid panel",

            # Infection control and prevention
            "infection control", "infection prevention", "disease prevention",
            "communicable disease", "infectious disease", "sanitation", "hygiene practices",
            "hand hygiene", "hand washing", "paghuhugas ng kamay", "personal protective equipment",
            "PPE", "isolation", "quarantine", "contact precautions", "standard precautions",
            "transmission prevention", "environmental cleaning", "disinfection", "sterilization",

            # Risk exposure management
            "exposure management", "exposure control", "exposure prevention",
            "exposure risk", "environmental exposure", "occupational exposure",
            "avoiding exposure", "naiiwasan ang pagkalantad", "risk activity",
            "high-risk behavior", "low-risk behavior", "safety precaution",
            "protective measure", "pangangalaga sa sarili", "self-protection",
            "safe practices", "ligtas na gawain", "environmental safety", "napakainam na pag-iingat"
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
                    # Original entity boosts
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
                        
                    # New entity boosts
                    elif section == "kalusugan_ng_bibig" and ent.label_ in ["BODY_PART", "SYMPTOM"]:
                        entity_boost += 2.5
                    elif section == "mobility_function" and ent.label_ in ["ADL", "MOTION", "BODY_PART"]:
                        entity_boost += 2.5
                    elif section == "pain_discomfort" and ent.label_ in ["SYMPTOM", "BODY_PART"]:
                        entity_boost += 3
                    elif section == "nutrisyon_at_pagkain" and ent.label_ in ["FOOD", "DIET", "MEASUREMENT"]:
                        entity_boost += 2.5
                    elif section == "pamamahala_ng_gamot" and ent.label_ in ["MEDICATION", "CHEM", "TREATMENT"]:
                        entity_boost += 3
                    elif section == "vital_signs_measurements" and ent.label_ in ["MEASUREMENT", "QUANTITY", "NUMERIC"]:
                        entity_boost += 3
                    elif section == "medical_history" and ent.label_ in ["DISEASE", "TREATMENT", "DATE"]:
                        entity_boost += 2.5
            
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
                
                # IMPORTANT CHANGE: Store the sentence AND its original index
                result[section].append((i, sentences[i]))
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
                # IMPORTANT CHANGE: Store the sentence AND its original index
                result[best_section].append((i, sentences[i]))
                assigned_sentences.add(i)
                section_counts[best_section] = section_counts.get(best_section, 0) + 1
    
    # THIRD PASS: Ensure sentences are in logical order and handle potential fragments
    for section in result:
        if not result[section]:
            continue
            
        # Sort by original index first
        result[section].sort(key=lambda x: x[0])
        
        # Look for consecutive sentences that should be kept together
        consolidated = []
        i = 0
        
        while i < len(result[section]):
            current_idx, current_sent = result[section][i]
            
            # Start a group with the current sentence
            group_indices = [current_idx]
            group_texts = [current_sent]
            
            # Look ahead for consecutive index sentences
            j = i + 1
            while j < len(result[section]) and result[section][j][0] == group_indices[-1] + 1:
                next_idx, next_sent = result[section][j]
                group_indices.append(next_idx)
                group_texts.append(next_sent)
                j += 1
            
            # Add this group to consolidated results
            consolidated.append((group_indices[0], " ".join(group_texts)))
            i = j
        
        # Replace with consolidated sentences
        result[section] = consolidated
        
        # Extract just the sentences for the final output, maintaining order
        result[section] = [sent for _, sent in result[section]]
    
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