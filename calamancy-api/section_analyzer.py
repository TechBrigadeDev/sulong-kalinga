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
        # Enhance SYMPTOMS patterns to better capture key symptoms
        "mga_sintomas": [
            # Specific symptom patterns
            r'(nagsimulang|nakakaranas|dumaranas|nakakaramdam) (ng|sa) (sakit|pananakit|sintomas|symptoms)',
            r'(nagpapakita|nagkaroon|nakaranas) (ng|sa) (mga sintomas|kondisyon|karamdaman)',
            r'(nahihirapan|nahihirapang|hirap) (siyang|siya|na) (huminga|lumunok|matulog)',
            r'(dumaranas|nararamdaman|nakakaramdam) (niya|nila|nya|ko|ng) (pananakit|kirot)',
            r'sumasakit ang (kanyang|kaniyang) (ulo|tiyan|dibdib|likod|tuhod|legs)',
            r'(binabangungot|pabalik-balik na panaginip|insomnia)',
            r'(madalas|palagi) (siya|siyang|silang) (nahihilo|nasusuka|naduduwal)',
            r'(nagtatae|constipated|hirap dumumi|pagbabago sa bowel movements)',
            r'may (madalas|paulit-ulit) na (ubo|sipon|lagnat|fever)',
            r'nagkakaroon ng (acid reflux|heartburn|pananakit ng tiyan)',
            r'(bumaba|tumaas) ang (timbang|gana sa pagkain|appetito)',
            r'hindi (makontrol|mapigilan) ang (pag-ihi|pagdumi|bladder|bowel)',
            r'hirap (siya|siyang) mag-(lakad|pasok sa banyo|upo|kilos)',
            r'pabalik-balik na (sakit|kirot|pananakit)',
            r'nagpapahirap sa (kanya|kaniyang) (paglalakad|pag-upo|pagkain)',
            r'mabilis (mapagod|mahapo|pagod)',
            r'nararamdaman (niyang|niya na) mahina ang (kanyang|kaniyang) (katawan)',
            # Add specific symptom patterns for malnutrition
            r'(signs|sintomas) ng (malnutrition|malnutrisyon)',
            r'(significant|kapansin-pansin) na (pagbaba|pagbababa) ng (timbang|weight)',
            r'(unti-unting|gradual|mabilis) (lumalala|bumababa) (sa nakaraang|sa last)',
            r'(visible|halata|kapansin-pansin) (muscle wasting|pagkawala ng laman)',
            r'(altered|changed|pagbabago sa) (taste sensation|panlasa)',
            r'(early satiety|mabilis mabusog|mabusog agad)',
            r'(occasional|intermittent|paminsan-minsan) (dysphagia|hirap lumunok)',
            r'(dry|solid) foods (hirap|nahihirapang) (lunukin|kainin)',
            r'(significant|marked|notable) (decline|pagbaba) sa (kanyang|kaniyang) (energy|lakas)',
            r'(nahihirapan|hirap) siyang maglakad (sa|ng) (distances|distansya)',
            r'(indikasyon|indication|sign) ng (muscle loss|kawalan ng laman)',
            r'(borderline low|mababa ang) (albumin|hemoglobin)',
            r'(delayed wound healing|mabagal na paghilom)',
            r'(nagiinitang|namumula|namamaga) (ang|yung) (sugat|wound|cut)',
            r'(significantly below|mas mababa sa|below ang) (recommended|normal)',
            # Add psychological symptom patterns
            r'(matinding|significant|marked) (pagbabago|changes) sa (social|pakikisalamuha)',
            r'(huminto|stopped|hininto) ang (kanyang|kaniyang) (participation|pakikilahok)',
            r'(hindi|ayaw) (na|ng) (lumabas|sumama|makisali)',
            # Additional symptoms patterns
            r'(nagreport|nag-ulat|nagbanggit) (siya|siyang|ng) (tungkol sa|hinggil sa) (kanyang|kaniyang) (nararamdaman|symptoms)',
            r'(napapansin|naobserbahan) (ko|namin|niya|nila) na (siya|siyang) (nagkaroon|nagkakaroon|nakakaranas)',
            r'(noon|noong|simula|mula) (pa|noong|nung) (nakaraang|last|nakalipas na) (araw|linggo|buwan)',
            r'(gradually|unti-unti|dahan-dahan) (nag-develop|lumalala|bumibigat|lumalaki)',
            r'(biglaan|bigla|sudden) (onset|pagsimula|nagsimula) ng (sakit|pananakit|pakiramdam)',
            r'(exacerbated|lumalala|lumalalang) (kapag|when|tuwing|habang) (gumagalaw|kumakain|naglalakad)',
            r'(relieved|gumagaan|bumubuti) (kapag|when|tuwing|habang|after|pagkatapos) (nagpapahinga|umiinom ng gamot)',
            r'(history|kasaysayan) ng (weight loss|pagbaba ng timbang|pagbabago sa gana)',
            r'(dating|noon) (kaya|nagagawa|nakakaganap) pero (ngayon|sa kasalukuyan) (nahihirapan|hindi na|di na)',
            r'(pansin|napapansin|cited|observed) na (bumababa|tumataas|bumabagal|humihina) ang (kanyang|kaniyang)'
        ],
        
        # Improve PHYSICAL CONDITION patterns
        "kalagayan_pangkatawan": [
            # Focus on current physical state
            r'(ang|yung) (kanyang|kaniyang|kanilang) (pisikal na|physical) (kondisyon|kalagayan|estado)',
            r'(sa|tungkol sa) (pisikal na|physical) (lakas|kalagayan|kundisyon)',
            r'(nagpapakita|nagpapamalas) (ng|sa) (kahinaan|kahinaang) (pisikal|sa katawan)',
            r'(hindi|di) stable ang (kanyang|kaniyang) (paglakad|balanse|pisikal na kondisyon)',
            r'(mababa|mataas) ang (kanyang|kaniyang) (blood pressure|presyon|heart rate)',
            r'(lumalala|lumalalang|bumubuti) ang (kahinaan|panghihina|lakas) ng (kanyang|kaniyang) (muscles|kalamnan)',
            r'(nahihirapan|hirap|nahihirapang) (siya|siyang) (tumayo|bumangon|gumalaw|kumilos)',
            r'(nangangailangan|kailangan) (niya|niyang) ng (tulong|suporta) sa (paglalakad|pagkilos)',
            r'(limited|limitado) ang (range of motion|movement|galaw) ng (kanyang|kaniyang) (joints|kasukasuan)',
            r'(hirap|nahihirapan|nahihirapang) sa mga (hagdanan|uneven surfaces|hindi patag)',
            r'ang (kanyang|kaniyang) (strength|lakas) sa (itaas na|ibabang) parte ng katawan',
            r'(naaapektuhan|apektado) ang (kanyang|kaniyang) (balanse|coordination|pagkilos)',
            r'(vital signs|temperature|BP|heart rate|respiratory rate) (are|ay|is) (.*)',
            r'(mahina|malakas) ang (kanyang|kaniyang) (upper body|lower body)',
            r'(may|merong) (difficulty|kahirapan) sa (pagkilos|paggalaw)',
            r'(may|meron) siyang (edema|pamamaga) sa (kanyang|kaniyang)',
            r'ang (kanyang|kaniyang) (physical condition|weight|timbang) ay',
            # Add patterns for appetite/nutrition as part of physical condition
            r'(pagkain|diet|nutrition|nutrisyon|kumakain) (niya|daily|araw-araw)',
            r'(average|daily) (intake|consumption|caloric)',
            r'(calories|protein|nutrients|sustansiya) (daily|kada araw)',
            r'(significantly|substantially) (below|lower|mababa)',
            r'(food diary|pagkain logs)',
            r'(kumakain|intake|kinakain) (niya|niyang|lang|only) (approximately|estimated)',
            r'(kumakain|nakakakain|kakain) (siya|siyang) (ng|nang)',
            r'(recommended|required|kailangan|ideal) (calorie|protein|nutritional) (intake|requirements)',
            # Add specific appearance patterns
            r'(damit|clothes|clothing) (ay|niya) (maluwag|malaki|masikip|tight)',
            r'(visual|nakikita) (assessment|observation) (ng|sa) (kanyang|kaniyang) (katawan|appearance)',
            r'(napansin|nakita) (ko|namin) ang (kanyang|kaniyang) (muscles|kalamnan|temples|braso|arm)',
            r'(prominent|visible|halata|kapansin-pansin) (ang|na) (collarbones|ribs|sunken cheeks)',
            # Add pattern for urinary symptoms regardless of reporter
            r'(pattern|pagbabago) (ng|sa) (urine output|pag-ihi|ihi)',
            r'(mas kaunti|mas madalang|dumadalang|dumarami) (ang|na|niyang) (pag-ihi|umihi|ihi)',
            r'(nag-iba|nagbago) (ang|rin|din) (pattern|anyo|characteristics) (ng|sa) (kanyang|kaniyang) (urine|ihi)',
            # Extended version to capture the exact issue
            r'ayon sa (kanyang|kaniyang) (asawa|pamilya|anak).*?(urine output|pag-ihi|ihi)',
            # Additional physical condition patterns
            r'(current|kasalukuyang) (vital signs|temperature|BP|presyon|heart rate|respiratory rate)',
            r'(nasukat|recorded|measured) (na|ang) (kanyang|kaniyang) (height|weight|timbang|tangkad)',
            r'(cachectic|malnourished|undernourished|underweight) (appearance|physical state|katawan)',
            r'(sarcopenia|muscle wasting|pagkaubos ng muscle|pagkawala ng laman)',
            r'(posture|postura) (ay|ni|niya) (nakayuko|nakatuwad|slumped|unstable|hindi stable)',
            r'(nagpapakita|nagpapamalas) (ng|siya ng|niya ang) (signs of|senyales ng) (dehydration|malnourishment)',
            r'(nutritional status|nutrisyon|nutritional profile) (ay|niya) (poor|mababa|compromised)',
            r'(BMI|body mass index) (niya|nya) (ay|is) (below|mababa sa|under) (normal|recommended)',
            r'(temple area|mukha|arms|braso|legs|binti) (ay|appears|nagmumukha) (sunken|nanliligdig|payat na payat)',
            r'(skin turgor|elasticity ng balat) (ay|is) (decreased|mababa|poor|hindi normal)',
            r'(energy level|antas ng enerhiya|lakas|resistensya) (ay|appears|mukhang) (significantly reduced|mababa)',
            r'(breathing pattern|paghinga|respiratory effort) (ay|shows|nagpapakita ng) (labored|hirap|distress)',
            r'(food intake|nutritional intake|calories|kaloryia|protein) (niya|nya) (ay) (significantly|substantial|notably) (below|under|kulang)',
            r'(clinical assessment|assessment|physical exam|physical examination) (confirms|nagkukumpirma|showed|revealed) (ang|na may)'
        ],
        
        "kalagayan_mental": [
            # More specific mental health patterns
            r'(mental state|mental status|cognitive|pag-iisip|memory|confusion|nalilito|kalimutan|naguguluhan|depression|anxiety|mood|emosyon|emotional)',
            r'(nagpapakita|nagpapahiwatig) ng (signs|sintomas) ng (depression|anxiety|dementia)',
            r'(kalimutan|nakakalimutan|nalilimutan) (niya|nila|nya) (kung|ang)',
            r'(nalilito|confused|naguguluhan) (siya|sila) (tungkol sa|kapag|sa)',
            r'(nag-aalala|worried|concerned) (siya|siyang|sila) (tungkol sa|dahil sa) (kanyang|kaniyang|sarili|pag-iisip|memorya|kalungkutan|takot)',
            r'(bumaba|tumaas) ang (kanyang|kaniyang) (mood|disposition|estado ng isip)',
            r'(nagiging|naging) (iritable|mainitim ang ulo|short-tempered)',
            r'mental health (issues|concerns|problems)',
            r'hindi (niya|nya|nila) (matandaan|maalala) ang (kanyang|kaniyang)',
            r'(nagpapakita|nagpapamalas) ng (anxiety|depression|lungkot|kalungkutan|mood swings|pagbabago ng emosyon)',
            r'(nagbabago-bago|unstable) ang (kanyang|kaniyang) (emosyon|damdamin)',
            r'(nahihirapan|hirap) (siyang|siyang) mag-(focus|concentrate)',
            r'(nababalisa|nag-aalalala|worried) (siya|siyang) lagi',
            r'(nagiging|naging) (defensive|agitated|irritable) (siya|siyang)',
            r'may cognitive (impairment|decline|deterioration)',
            r'(bumababa|lumalala) ang (kanyang|kaniyang) kakayahang (mag-isip|magdesisyon)',
            r'(may|nagpapakita ng|nagpapahiwatig ng) confusion (siya|sila)',
            r'(feeling|nakakaramdam ng) (hopeless|worthless|helpless|walang halaga)',
            # Add specific patterns for grief and interest loss
            r'(hindi|di) pa (siya|siyang) nakakapag-process ng (grief|lungkot|kalungkutan)',
            r'(pagkawala|loss|namatay) (ng|nila|ng kanyang) (close friends|malapit na kaibigan)',
            r'(declining|bumababa|nawala|tumitigil) (interest|interes|kagustuhan) sa (dating|previous)',
            r'(hindi|di) (na|pa) (siya|siyang) (nag-e-engage|nakiki-engage|sumasali)',
            r'(hindi|di) talaga (siya|siyang) (nanonood|nakikinig|nagbabasa)',
            r'(isip|mind|thoughts) (niya|niyang) (ay|is) (malayo|wandering|elsewhere)',
            r'(kawalan|loss|nawalan) ng (sigla|enthusiasm|interest)',
            r'("Matanda na ako"|"Ano pa ang silbi")',
            r'(negative|negatibong|pessimistic) (thoughts|pag-iisip|outlook)',
            r'(withdrawal|pag-iwas|isolation) (from|sa) (social|family|friends)',
            r'dating (soul of the party|masayahin|social|friendly)',
            r'ng(ayon|unit) ay (halos hindi na|barely) (nagsasalita|participates|sumasali)',
            # Additional mental state patterns
            r'(mental status exam|cognitive assessment|assessment ng mental state) (shows|revealed|nagpapakita ng)',
            r'(naging|nagiging) (malungkot|lungkot|depressed|anxious|worried|matakutin) (siya|siyang) (recently|lately|nitong mga nakaraang)',
            r'(pag-iisip|thought process|thought content|train of thought) (ay|is|niya) (disorganized|mahirap sundan|disconnected)',
            r'(motivation|motibasyon|will|kagustuhan) (to|na) (participate|engage|join|sumali|lumahok) (is|ay) (waning|decreasing|bumababa)',
            r'(ruminating|nag-aalala|fixated|obsessed) (siya|siyang) (tungkol sa|about|sa) (kanyang|kaniyang) (kalusugan|kalagayan)',
            r'(hindi|di) (niya|nya|na niya) (matandaan|natatandaan|maalala|naaalala) (kung|ang|paano|kailan|saan) (siya|siyang)',
            r'(nakakalimutan|forgotten|nilimutan|hindi maalala) (niya|nya) (ang|yung|kung paano) (pangalan|name|address|medication|schedule)',
            r'(verbal responses|sagot|communication|pakikipag-usap) (ay|are|is) (limited|minimal|one-word|single-word)',
            r'(executive function|decision making|memory|retention|recall|attention span) (exhibits|shows|nagpapakita ng) (decline|deterioration)',
            r'(emotional lability|mood swings|biglaang pagbabago ng emosyon) (napapansin|observed|noted)',
            r'(nihilistic|negative|hopeless|walang pag-asa) (statements|thoughts|expressions|sinasabi) (such as|like|tulad ng) ("Matanda na ako"|"Ano pa ang silbi")',
            r'(delusions|hallucinations|pagwawala|matinding pagkabalisa) (nararanasan|experienced|reported) (niya|nya)',
            r'(paranoia|suspiciousness|pagdududa|pagiging mapagduda) (tungkol sa|about) (mga tao|intentions|layunin) (ng ibang tao|ng pamilya)',
            r'(orientation|oryentasyon) (to|sa) (person|place|time|tao|lugar|panahon) (is|ay) (impaired|compromised|hindi normal)',
            r'(gets easily|madaling) (frustrated|ma-frustrate|mainis|mairita) (when|kapag|tuwing) (trying to|sinusubukang)',
            r'(cognitive decline|paghina ng pag-iisip) (evidenced by|nakikita sa|manifested in) (inability to|kawalan ng kakayahang)'
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
            r'hindi na (siya|siyang) (nag|nakaka|nakakasali) sa (church|social activities)',
            # Additional activity patterns
            r'(assessment|evaluation) (ng|of) (activities of daily living|ADLs|basic activities) (nagpapakita|shows|revealed)',
            r'(kumpara sa|compared to) (dati|before|dating) (abilities|kakayahan|nagagawa) (niya|nya)',
            r'(significant|notable|kapansin-pansin) (decline|deterioration|pagbaba) (sa|in) (functional capacity|kakayahan|performance)',
            r'(independent|nakakaraos|nakakaya) (pa rin|still) (siya|siyang) (sa|in) (paggamit ng|performing|pagsagawa ng)',
            r'(requires|nangangailangan|kailangan) (na) (partial|minimal|moderate|maximum) (assistance|tulong|suporta) (sa|for) (pagkain|pagligo)',
            r'(dressing|pagbibihis) (requires|nangangailangan ng) (setup|preparation|assistance|tulong) (dahil sa|due to|because of)',
            r'(hindi|di) (siya|siyang) (makapag-button|makapagsuot|makapagbihis|makakain|makapagluto|makapaglinis) (nang|ng) (mag-isa|sarili)',
            r'(mobility status|kakayahang gumalaw|kakayahang lumipat) (ay|is) (restricted|limited|impaired)',
            r'(assistive devices|mobility aids) (such as|like|gaya ng) (walker|cane|tungkod|wheelchair) (ginagamit|used|needed|kailangan)',
            r'(need for|pangangailangan ng) (supervision|constant supervision|verbal cueing|verbal reminders) (para|to) (mapigilan|prevent|maiwasan)',
            r'(basic self-care|basic grooming|personal hygiene tasks) (ay|are|is) (challenging|difficult|nagiging mahirap)',
            r'(ambulates|nakakalakad|naglalakad) (with|using|gamit ang) (assistance|tulong|support ng iba)',
            r'(limited|restricted|limitado|mababang) (community access|socialization|pakikisalamuha) (due to|dahil sa) (mobility issues|transportation barriers)',
            r'(household management|gawaing bahay|chores) (ay|are) (now|ngayon) (managed by|handled by|ginagawa ng) (kanyang|kaniyang) (pamilya|asawa|anak)',
            r'(transportation|access to services|pagbibiyahe) (is|ay) (dependent on|umaasa sa|nakasalalay sa) (family|pamilya|iba)'
        ],
        
        "kalagayan_social": [
            # Make these patterns specifically about social interactions and relationships
            r'(tungkol sa|about) (kanyang|kaniyang) (social support|social network|pakikisalamuha|pakikihalubilo|friends)',
            r'(nakikitungo|nakikisalamuha) (siya|siyang) sa (kaniyang|kanyang) (pamilya|friends|komunidad)',
            r'(bihira|madalang|regular) (siyang|siya|silang) (bumibisita|dumalaw|makipagkita|makisalamuha)',
            r'(umaasa|dependent|nakadepende) (siya|siyang) sa (kanyang|kaniyang) (pamilya|asawa|anak) (?!.*pagkain)',
            r'(naninirahan|nakatira) (siya|siyang|sila) kasama ang (kanyang|kaniyang)',
            r'(nag-iisa|mag-isa|isolated) (siya|siyang) (nakatira|namumuhay)',
            r'(nahihirapan|hirap) (siyang|siya) makisalamuha sa (ibang tao|kapwa|komunidad)',
            r'(may tension|may alitan|strained relationship) sa (kanyang|kaniyang) (pamilya|anak)',
            r'(nararamdaman|feeling) (niya|niyang) (hiniwalayan|inabandona|iniwanan)',
            r'financial (concerns|issues|problems) sa (kanyang|kaniyang) (pamilya)',
            r'(nawala|nabawasan) ang (kanyang|kaniyang) (social contacts|pakikisalamuha|social activities)',
            r'(nababawasan|humihina) ang (supportive|suportang) (network|komunidad)',
            r'(hirap|nahihirapan) (siyang|siya) mag-adjust sa (bagong|new) environment',
            r'(generational gap|pagkakaiba ng edad) sa (kanyang|kaniyang) (pamilya|household)',
            r'(nakikilala|nakikita) bilang (burden|pabigat) sa (kanyang|kaniyang) (pamilya|komunidad)',
            r'(feelings of|nararamdamang) (isolation|pagkakalayo|disconnection|social withdrawal)',
            r'(nawala|nabawasan) ang (kanyang|kaniyang) (social role|papel sa lipunan)',
            r'ayon sa (kanyang|kaniyang) (asawa|pamilya|anak)(?!.*(pagkain|diet|kumakain|nutrisyon|calorie|caloric))',
            r'ayon sa (kanilang|kanila|kanya) (asawa|pamilya|anak)(?!.*(pagkain|diet|kumakain|nutrisyon|calorie|caloric))',
            # Add specific community engagement patterns
            r'(partisipasyon|pakikilahok) sa (mga|kanyang|kaniyang) (simbahan|community|komunidad|grupo)',
            r'(social gatherings|salu-salo|pagtitipon|social activities)',
            r'(community involvement|participation|pakikilahok sa komunidad)',
            r'(hirap|nahihirapan) (siyang|siya) makisalamuha',
            r'(social withdrawal|pag-iwas sa social situations)',
            r'(visitors|bisita|kaibigan|kapitbahay|kamag-anak) (hindi|ayaw|tumanggi) (niyang|niya) (tanggapin|makipag-usap)',
            # Additional social condition patterns
            r'(social support system|social network|supportive relationships) (niya|nya) (ay|is) (limited|minimal|kulang|extensive|malapit)',
            r'(lives|nakatira|namumuhay) (with|kasama ang|kasama si|kasama sina) (spouse|asawa|children|anak|grandchildren|apo|family|pamilya)',
            r'(family dynamics|dynamics ng pamilya|ugnayan sa pamilya) (ay|is|are) (strained|complicated|complex|healthy|supportive)',
            r'(primary caregiver|pangunahing tagapag-alaga) (niya|nya) (ay|is) (ang|si|sina) (kanyang|kaniyang) (asawa|anak|pamangkin)',
            r'(dating|previously|noon) (active|aktibo|involved|kasali) (siya|siyang) (sa|in) (church|simbahan|community|komunidad|organization|organisasyon)',
            r'(withdrawal|isolation|pag-iwas) (mula sa|from) (social activities|gatherings|pagtitipon) (napansin|observed|napapansin)',
            r'(peers|kaibigan|friends|friends circle) (have|ay) (passed away|namatay na|wala na|deceased)',
            r'(retirement|pagreretiro) (has|ay) (significantly|negatively|positively) (affected|nakaapekto sa) (social identity|social life)',
            r'(cultural|religious|spiritual|faith-based) (activities|connections|pagkakaugnay) (ay|are) (important|mahalagang|significant) (sa|to) (kanya|kaniya)',
            r'(social isolation|feelings of loneliness|kalungkutan|isolation) (reported by|nireport ni|sinabi ni|expressed by) (patient|family|pamilya)',
            r'(financial concerns|economic challenges|financial difficulties) (affecting|nakakaapekto sa) (access to|ability to|kakayahang) (socialize|makisalamuha)',
            r'(loss of|nawala ang|pagkawala ng) (purpose|role|sense of identity|pagkakakilanlan) (sa|in) (komunidad|community|society|lipunan)',
            r'(generational gap|cultural differences|pagkakaiba ng kultura) (between|sa pagitan ng) (patient|pasyente) (and|at) (caregivers|tagapag-alaga|family|pamilya)',
            r'(previously|dating) (held position|nagtataglay ng posisyon|officer|opisyal) (sa|in) (community organization|church|simbahan|asosasyon|association)',
            r'(social engagement|social activities|participation) (is|ay) (now limited to|ngayon ay limitado sa) (immediate family|pamilya|household|sambahayan)'
        ],
        # Refine the MEDICAL HISTORY patterns to be strictly about past conditions
        "medical_history": [
            # Focus on diagnosis history, past conditions
            r'(medical history|history ng sakit|nakaraang karamdaman|dating kalusugan)',
            r'(diagnosed|na-diagnose|diagnosed with|na-diagnose na may) (.*?)(sa nakalipas|noon|dati|years ago)',
            r'(dating may sakit|previously had|dating nakaranas ng) (.*?)(sakit|kondisyon|diagnosis)',
            r'(history|kasaysayan) (ng|ni|niya|nila) (heart attack|stroke|surgery|operasyon)',
            r'(dati|noon|previously) (siyang|siya) (diagnosed|na-diagnose|nagkaroon) (ng|sa|with)',
            r'(ilang|mga|maraming|nakaraang) (taon|buwan|linggo) (na|ang nakalipas) (siyang|siya) (diagnosed|na-diagnose)',
            r'(chronic|long-term|pangmatagalan) (na )?(condition|illness|sakit)',
            r'(controlled|hindi kontrolado|controlled with|managed with) (gamot|medication) (sa|for) (matagal na|ilang taon)',
            r'(previous|dati|nakaraan|dating) (surgery|operasyon|hospital admission|pagkakaospital)',
            r'(family history|history sa pamilya|genetic|hereditary|namana)',
            r'(allerg(y|ies)|allergy sa|allergic reactions|reaksyon sa)',
            r'(tinanong|kinuha|inalam) (ko|namin) (ang|tungkol sa) (kanyang|kaniyang) (medical history|kasaysayan ng kalusugan)',
            r'(previous|dati|dating) (stroke|heart attack|cardiovascular event|myocardial infarction)',
            r'(comorbid condition|comorbidity|multiple conditions|diagnosed din with)',
            r'(dati|previously|noon|in the past) (nagkaroon|diagnosed|na-diagnose|naranasan) (ng|with|sa)',
            # Add specific patterns for medication history
            r'(matagal nang|long-term|pangmatagalang) (iniinom|paggamit ng) (gamot|medication)',
            r'(nakaraang|past|previous) (treatments|therapies|paggagamot)',
            r'(surgical history|kasaysayan ng operasyon)',
            # Add timeframe indicators for medical history
            r'(naranasan|nagkaroon|dumanas) (na|noong|noon|dati|sa nakalipas) (.+?) (taon|buwan|linggo|araw)',
            # Additional medical history patterns
            r'(diagnosed|na-diagnose|diagnosed with) (.*?)(sa nakalipas|noon|dati|years ago|noong|nung) (.*?)(taon|years|buwan|months)',
            r'(past medical history|dating karamdaman|dating medical record|dating kondisyon) (includes|kasama ang|may|nagsasaad ng)',
            r'(surgeries|operasyon|surgical procedures) (in the past|noon|dati|nakaraan|dating) (includes|kasama|kinabibilangan ng)',
            r'(hospitalized|naospital|na-confine) (para sa|for|dahil sa) (.*?) (noong|nung|last|past) (.*?)(taon|buwan|linggo)',
            r'(previous treatment|dating paggagamot|past management) (para sa|for|ng) (kondisyon|sakit|diagnosis)',
            r'(family history|history sa pamilya|kasaysayan ng pamilya) (ng|of|positive for) (.*?)(disease|condition|disorder)',
            r'(hereditary|namana|genetic|genetically predisposed) (condition|sakit|disorder|syndrome)',
            r'(maintenance medications|regular medications) (for|para sa) (.*?)(started|prescribed|inireseta) (.*?)(years|months|taon|buwan) (ago|na)',
            r'(allergies to|allergic reactions|reaksyon|alerhiya) (sa|to) (specific medications|food|pagkain|gamot)',
            r'(history of|nakaranas ng|nagkaroon ng|nagdusa sa) (trauma|abuse|neglect|physical injury|emotional trauma)',
            r'(menopausal|menopause|andropause) (since|simula|mula) (.*?)(years ago|taon na|noong|nung)',
            r'(chronic condition|chronic illness|pangmatagalang karamdaman) (managed with|controlled by|treated with) (.*?)(for|sa loob ng) (.*?)(years|taon)',
            r'(previous diagnosis|dating diagnosis|nauna nang na-diagnose) (ng|of) (.*?)(pero|but|however) (ngayon|now) (resolved|controlled|managed na)',
            r'(immunization|bakuna|vaccination) (history|record|kasaysayan) (ay|is) (complete|incomplete|kulang|kumpleto)',
            r'(previous exposure|dating exposure|dating pagkalantad) (to|sa) (chemicals|toxins|radiation|infectious agents)'
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
            r'(matigas|sensitive|tender|namamaga) (kapag|when|pag) (pressed|pinipindot|hinihipo)',
            r'(hindi|di) (makatulog|makagalaw|makalakad|makapaghinga) dahil sa sakit',
            r'(lumalala|lumalalang|sumisingkit) (ang|yung) (pananakit|kirot|sakit)',
            r'(gumiginhawa|lumilinaw|bumubuti) (ang|yung) (pananakit|kirot|sakit)',
            r'(pain medications?|pain relievers?|pampatanggal ng sakit)',
            r'(joint pain|muscle pain|sakit ng kalamnan|sakit ng kasukasuan)',
            r'(cramping|paninigas ng kalamnan|pulikat)',
            r'(fibromyalgia|neuropathic pain|nerve pain)',
            r'(headache|migraine|sakit ng ulo)',
            r'(stomach pain|abdominal pain|sakit ng tiyan)',
            r'(back pain|lower back pain|sakit ng likod)',
            # Specifically add denture discomfort
            r'(ill-fitting|maluwag|masikip) (dentures|pustiso|false teeth)',
            r'(nagdudulot|causes|causing) (ng|of) (discomfort|hindi kumportable|sakit)',
            r'(discomfort|uncomfortable|hindi kumportable) (kapag|when|habang) (kumakain|eating|tumatawa)',
            r'(dentures?|pustiso) (na|that) (hindi|don\'t|doesn\'t) (fit|kasya) (properly|ng maayos)',
            # Additional pain/discomfort patterns
            r'(pain assessment|assessment ng sakit|evaluation ng pananakit) (reveals|nagpapakita|shows)',
            r'(pain level|antas ng sakit|intensity ng sakit) (ay|is) (rated|inirating|sinusukat|rated by patient) (as|na) ([0-9]|ten|sampung|siyam|walo|pito|anim|lima|apat|tatlo|dalawa|isa)/10',
            r'(characterizes|inilalarawan|describes) (ang|the) (pain|sakit|pananakit) (as|na) (sharp|dull|stabbing|burning|throbbing|aching|radiating)',
            r'(identifies|tinutukoy|pinupunto) (ang|the) (pain|sakit|pananakit) (location|lokasyon) (as|na) (primarily|mainly|mostly) (sa|in) (kanyang|kaniyang)',
            r'(reports|nagsasabi|nagrereport) (ng|of) (intermittent|occasional|tuloy-tuloy|persistent|chronic) (pain|discomfort|sakit|pananakit)',
            r'(pain|sakit|pananakit) (is|ay) (worse|lumalala|lumalalang|nagiging matindi) (in the|sa) (morning|evening|afternoon|umaga|hapon|gabi)',
            r'(pain|sakit|pananakit) (affects|nakakaapekto sa|limits|nililimitahan ang) (kanyang|kaniyang) (ability to|kakayahang) (maglakad|kumain|matulog|mag-concentrate)',
            r'(aggravating factors|factors na nagpapalala) (includes?|kasama ang|ay ang) (movement|certain positions|prolonged sitting|standing|paggalaw|pag-upo)',
            r'(relieving factors|factors na nagpapagaan) (includes?|kasama ang|ay ang) (rest|medication|heat application|massage|pahinga|gamot|painument)',
            r'(pain management strategies|pain coping techniques) (na|that) (ginagamit|gamit|currently used|used) (includes?|kasama ang)',
            r'(pain|sakit|pananakit) (has|ay) (improved|worsened|unchanged|bumuti|lumala|walang pagbabago) (since|mula|simula) (last assessment|noong huling assessment)',
            r'(denies|itinatanggi|walang) (pain|sakit|pananakit) (sa|in) (specific area|partikular na parte ng katawan)',
            r'(non-verbal indicators|non-verbal signs|facial expressions) (ng|of) (pain|discomfort|sakit) (napansin|observed|noted)',
            r'(breakthrough pain|sudden pain|biglaang sakit) (despite|kahit|kahit na|even with) (regular medication|pain management|gamot)',
            r'(referred pain|radiating pain|pain radiation) (to|papunta sa|extending to|hanggang sa) (adjacent areas|nearby regions|katabing bahagi)'
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
            r'(proper|improper) (hygiene|kalinisan) (practices|gawi|habits|nakagawian)',
            # Add specific self-care and grooming patterns
            r'(hindi|di) (na|pa) (niya|niyang) (inaayusan|inaasikaso|inaalagaan) ang sarili',
            r'(dating|dati ay) (maayos|malinis|presentable) ang (kanyang|kaniyang) (appearance|itsura)',
            r'(suot|wearing|using) (pa rin|the same) (damit|clothes) (sa loob ng|for) (ilang|several) (araw|days)',
            r'(hindi|di) (na|pa) (nagpapalit|nagbibihis) ng (damit|clothes|pants)',
            r'(hindi|di) (na|pa) (naghuhugas|nag-aasikaso) (ng kamay|ng katawan)',
            # Additional hygiene patterns
            r'(assessment|evaluation) (ng|of) (personal hygiene|hygiene status|kakayahan sa hygiene) (nagpapakita|shows|reveals)',
            r'(ability to|kakayahang) (maintain|panatilihin ang) (personal hygiene|personal cleanliness|sariling kalinisan) (is|ay) (impaired|compromised|limited)',
            r'(personal appearance|itsura|appearance) (is|ay) (unkempt|disheveled|unclean|neglected|malinis|neat|well-kept)',
            r'(body odor|amoy|malansang amoy) (present|noticeable|napapansin|hindi napapansin)',
            r'(oral hygiene|kalinisan ng bibig|dental hygiene) (is|ay) (poor|inadequate|good|appropriate|needs improvement)',
            r'(nail care|pangangalaga ng kuko) (shows|exhibits|nagpapakita ng) (neglect|attention|appropriate care|lack of care)',
            r'(hair|buhok) (is|ay) (matted|tangled|uncombed|unkempt|well-groomed|clean|malinis)',
            r'(clothing|damit|pananamit) (is|ay) (soiled|stained|unclean|clean|appropriate|malinis|marumi)',
            r'(bathing|pagliligo) (frequency|dalas) (is|ay) (daily|every other day|weekly|irregular|hindi regular)',
            r'(toileting hygiene|hygiene sa paggamit ng banyo) (ay|is) (maintained|properly done|hindi nasusunod|adequate|inadequate)',
            r'(incontinence|problema sa pag-ihi|problema sa pagdumi) (affecting|nakakaapekto sa) (personal hygiene|kalinisan)',
            r'(skin integrity|condition ng balat) (is|ay) (intact|compromised|shows signs of|nagpapakita ng) (neglect|poor hygiene)',
            r'(assistance|tulong) (with|sa) (grooming tasks|hygiene activities|basic hygiene) (ay|is) (needed|required|kailangan)',
            r'(previously|dating|noon) (independent|self-sufficient|nakakaraos mag-isa) (sa|in) (hygiene tasks|hygiene care|pangangalaga sa sarili)',
            r'(necessary hygiene supplies|hygiene materials|products for hygiene) (ay|are) (available|not available|inadequate|kulang)'
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
            "hindi mapigilan ang pag-ihi", "bowel problems", "urinary issues",

            # Additional symptom-specific terms
            "lumalala", "paglala", "worsening", "pagbuti", "improvement", "relief",
            "episodic", "episodyik", "pana-panahon", "intermittent", "unpredictable", 
            "biglaan", "sudden onset", "persistent", "tuloy-tuloy", "recurring",
            "dizziness", "vertigo", "pagkahilo", "lightheaded", "fatigue", "pagod", 
            "exhaustion", "tingling", "pamamanhid", "numbness", "shortness of breath", 
            "hirap huminga", "palpitations", "mabilis na tibok", "cough", "ubo", 
            "phlegm", "plema", "rash", "pantal", "swelling", "pamamaga", "edema",
            "itching", "pangangati", "pruritus", "bloating", "gas pain", "manas",
            "jaundice", "paninilaw", "yellowing", "lumilipat", "radiating"
        ],
        
        "kalagayan_pangkatawan": [
            # Original physical terms
            "pisikal", "physical", "katawan", "body", "lakas", "strength", "bigat", "weight",
            "timbang", "tangkad", "height", "vital signs", "temperatura", "temperature",
            "pagkain", "eating", "paglunok", "swallowing", "paglakad", "walking",
            "balanse", "balance", "paggalaw", "movement", "koordinasyon", "coordination",
            "panginginig", "tremors", "nanghihina", "weakness", "pagod", "fatigue",
            "paglalakad", "mobility", "joints", "kasukasuan", "namamaga", "swelling",
            "blood pressure", "presyon", "heart rate", "pulso", "respiratory",
            "paghinga", "oxygen", "sugar level", "glucose", "hydration", "dehydration",
            "nutrisyon", "weight loss", "pagbaba ng timbang", "pagtaba", "edema", "pamamaga",
            "kakayahang gumalaw", "stamina", "lakas ng katawan", "posture",
            # Add specific nutrition/malnutrition terms
            "food diary", "caloric intake", "protein intake", "average daily intake",
            "significantly below", "substantially lower", "recommended intake",
            "muscle wasting", "malnutrition", "malnutrisyon", "visible weight loss",
            "prominent bones", "sunken cheeks", "loose clothing", "collar bones",
            "delayed wound healing", "edema", "skinny", "thin", "payat", 
            "energy levels", "significant decline", "pagbaba ng lakas",
            "limited endurance", "reduced stamina", "kakulangan ng lakas",
            "pagbaba ng timbang", "weight monitoring", "timbang", "malnourished",
            "nutritional status", "low albumin", "mababa ang albumin",
            # Physical appearance terms
            "prominent cheekbones", "visible ribs", "maluwag na damit", 
            "ill-fitting clothes", "appearance", "hitsura", "itsura",
            "muscle mass", "muscle tone", "skin turgor", "skin texture",
            "frail appearance", "mukhang mahina", "mukhang matanda",
            "eyes sunken", "lips dry", "dry skin", "poor skin turgor",
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
            "sit-to-stand", "pagbangon mula sa pagkakaupo", "physical frailty",
            # Additional physical condition indicators
            "muscle atrophy", "pagkawala ng laman", "muscle wasting", "sarcopenia",
            "frailty", "frail", "mahina ang pangangatawan", "physically weak",
            "brittle bones", "osteoporosis", "mahinang buto", "bone density",
            "unstable gait", "hindi stable ang paglakad", "unsteady walking",
            "poor posture", "slouching", "nakayuko", "hindi tuwid ang likod",
            "wheelchair-bound", "bed-bound", "nakatali sa kama", "bedridden",
            "cachectic", "malnutrition", "malnourished", "undernourished",
            "sunken eyes", "sunken cheeks", "nanliligdig na pisngi", "hollow temples",
            "dehydration", "kulang sa tubig", "poor skin turgor", "dry mucous membranes",
            "difficulty breathing", "respiratory distress", "naghahabol ng hininga",
            "pallor", "maputla", "cyanosis", "maasul na balat", "bluish discoloration",
            "BMI", "body mass index", "height-
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
            "loss of identity", "pagkawala ng identidad", "sense of self", "defensive behavior",

            # Additional mental state indicators
            "disorientation", "confusion", "nalilito", "pagkalito", "delusions",
            "hallucinations", "nakakakita ng hindi totoo", "false beliefs",
            "sundowning", "gabi-gabing pagkalito", "confusion at night",
            "paranoia", "suspiciousness", "pagiging mapaghinala", "distrust",
            "apathy", "kawalan ng interes", "lack of interest", "walang pakialam",
            "irritability", "pag-iinit ng ulo", "short temper", "agitation", 
            "restlessness", "hindi mapakali", "withdrawal", "pag-iwas sa tao",
            "grief", "bereavement", "pagdadalamhati", "coping mechanism",
            "mental fatigue", "pagod ang isip", "difficulty concentrating",
            "wandering", "paglibot-libot", "paglayas", "obsessive thoughts",
            "compulsive behavior", "ritualistic behavior", "suicidal ideation",
            "hopelessness", "helplessness", "worthlessness", "walang halaga",
            "mood swing", "emotional lability", "pabago-bagong damdamin",
            "executive function", "decision-making capacity", "poor judgment"
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
            "volunteer work", "household safety", "bathroom modifications",

            # Additional activity and functional terms
            "ADLs", "activities of daily living", "pang-araw-araw na gawain",
            "IADLs", "instrumental ADLs", "complex activities", "komplikadong gawain",
            "self-feeding", "pagpapakain sa sarili", "independent feeding",
            "transferring", "paglipat", "bed mobility", "paggalaw sa kama",
            "toilet use", "paggamit ng banyo", "bowel management", "bladder management",
            "stair climbing", "pag-akyat ng hagdan", "navigating stairs",
            "shopping", "pamimili", "grocery shopping", "pagpunta sa palengke",
            "meal preparation", "pagluluto", "food preparation", "paghahanda ng pagkain",
            "housekeeping", "paglilinis ng bahay", "chores", "gawaing-bahay",
            "medication management", "pangangasiwa ng gamot", "taking medications",
            "financial management", "pangangasiwa ng pera", "managing finances",
            "transportation", "transportasyon", "commuting", "paggamit ng sasakyan",
            "community mobility", "going out", "paglabas", "leisure activities",
            "functional limitations", "functional capacity", "activity tolerance"
        ],
        
        "kalagayan_social": [
            # Social-specific keywords only
            "relasyon", "relationship", "pamilya", "family", "social", "pakikisalamuha",
            "kaibigan", "friends", "komunidad", "community", "suporta", "support network",
            "pakikipag-usap", "communication", "pakikipag-interact", "interaction",
            "asawa", "spouse", "anak", "children", "kamag-anak", "relatives",
            "kapitbahay", "neighbors", "kakilala", "acquaintances", "visitors",
            "bisita", "group", "organization", "samahan", "club", "association",
            "socialization", "pakikihalubilo", "isolation", "pagkakahiwalay",
            "loneliness", "kalungkutan", "social withdrawal", "pag-iwas", "social network",
            "involvement", "participation", "pakikilahok", "church", "simbahan",
            "volunteer", "boluntaryo", "caregiver", "tagapag-alaga", "friends visit",
            "social gatherings", "social events", "outings", "pagtitipon", 
            "social connections", "social ties", "social bonds", "pakikipagkapwa",
            "volunteer work", "community service", "pakikisalamuha sa iba",
            "group activities", "social activities", "social invitations", "imbistasyon",
            "unti-unting umiiwas", "declining invitations", "ayaw makisalamuha",
            "ayaw sumama", "social circle", "friends circle", "social opportunities",
            "exclusion", "involvement in church", "community engagement", "pakikilahok sa komunidad",
            "role in society", "papel sa lipunan", "withdrawal from friends", "pag-iwas sa kaibigan",
            # Position/organizational roles
            "treasurer", "officer", "member", "miyembro", "leader", "participation in groups",
            "organizational role", "volunteer position", "committee member", "dating position",
            "dating tungkulin", "community leadership", "community standing", "reputation"
            
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
            "social identity loss", "loss of social standing", "peer relationships",
            # Additional social context terms
            "social network", "social circle", "social connections", "social ties",
            "social isolation", "loneliness", "nag-iisa", "social withdrawal",
            "abandoned", "neglected", "pinabayaan", "nakalimutan", "intergenerational",
            "multigenerational", "cultural differences", "culture gap", 
            "family dynamics", "relasyon sa pamilya", "elder care", "aging parents",
            "caregiving", "caregiving stress", "caregiver burden", "pasanin ng tagapag-alaga",
            "living arrangements", "living situation", "tirahan", "housing",
            "social roles", "role changes", "pagbabago ng papel", "loss of role",
            "dependency", "financial dependency", "emotional dependency", "pag-asa sa iba",
            "respect", "dignity", "independence", "autonomy", "stigma", "discrimination",
            "community resources", "community support", "community services",
            "faith community", "religious group", "church involvement", "spiritual community",
            "social engagement", "social activities", "social interaction",
            "marital status", "civil status", "biyudo/biyuda", "widowed"
        ],
        # NEW SECTION: Medical History Keywords
        "medical_history": [
            # Past-focused keywords only
            "medical history", "history", "kasaysayan", "diagnosis", "diagnosed", "diyagnosis", 
            "na-diagnose", "dati", "noon", "past", "previous", "nakaraang", "dating",
            "chronic condition", "matagal na sakit", "pangmatagalang sakit", 
            "hypertension history", "dating altapresyon", "dating mataas na presyon",
            "previous diabetes", "dating dyabetis", "previous heart attack", "dating atake sa puso", 
            "myocardial infarction", "previous MI", "previous stroke", "dating stroke", 
            "cardiovascular history", "heart disease history", "dating sakit sa puso", 
            "previous coronary", "dating pulmonary", "previous respiratory", 
            "past respiratory disease", "dating sakit sa baga", "history of COPD", 
            "dating emphysema", "past chronic bronchitis", "dating bronchitis", 
            "history of asthma", "dating hika", "previous cancer", "dating kanser", 
            "history of tumor", "dating bukol", "past arthritis", "dating rayuma", 
            "previous osteoporosis", "dating osteoarthritis", "previous joint disease",
            "dating sakit sa kasukasuan", "previous kidney disease", "dating sakit sa kidney", 
            "history of renal", "past liver condition", "dating sakit sa atay", 
            "previous hepatic", "history of thyroid", "dating thyroid disorder", 
            "previous goiter", "allergies history", "dating alerhiya", "past medications", 
            "dating gamot", "previous maintenance", "dating maintenance medication", 
            "noon iniinom na gamot", "surgical history", "dating operasyon", 
            "previous surgery", "past surgeries", "previous hospitalizations", 
            "dating naospital", "past hospitalization", "dating sakit", 
            "previous illness", "family history", "history sa pamilya", 
            "hereditary condition", "namana", "genetic predisposition", "genetic", 
            "genetically", "pre-existing condition", "dati nang kondisyon",
            # Additional medical history terms
            "case history", "patient history", "medical background", "health history",
            "comorbidities", "comorbid conditions", "coexisting conditions",
            "previous hospitalizations", "hospital admissions", "confinements",
            "surgical history", "previous surgeries", "operative history", "dating operasyon",
            "trauma history", "injury history", "past injuries", "dating pinsala",
            "childhood illnesses", "developmental history", "childhood diseases",
            "immunization history", "vaccination record", "bakuna", "immunizations",
            "allergies", "drug allergies", "food allergies", "hypersensitivity",
            "adverse reactions", "medication reactions", "drug sensitivity",
            "family diseases", "hereditary conditions", "genetic disorders",
            "chronic conditions", "long-standing illnesses", "matagal nang sakit",
            "congenital conditions", "birth defects", "inborn conditions",
            "major illnesses", "significant diagnoses", "malalang sakit",
            "disease progression", "course of illness", "trajectory",
            "remission", "exacerbation", "flare-ups", "disease control"
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
            "tender", "sensitive", "namamaga", "swelling", "pamamaga",
            # Additional pain-specific terms
            "acute pain", "chronic pain", "persistent pain", "recurrent pain",
            "breakthrough pain", "incident pain", "procedural pain", "movement-related pain",
            "nociceptive pain", "neuropathic pain", "inflammatory pain", 
            "pain characteristics", "pain quality", "pain descriptors", "pain pattern",
            "pain location", "pain site", "anatomical location", "affected area",
            "pain radiation", "spreading pain", "pain distribution", 
            "pain intensity", "pain severity", "pain magnitude", "pain level",
            "pain score", "numerical rating scale", "visual analog scale",
            "pain triggers", "aggravating factors", "nagpapalala ng sakit",
            "pain alleviating factors", "pain relief", "nagpapagaan ng sakit",
            "pain duration", "onset of pain", "pain history", "temporal pattern",
            "pain frequency", "pain periodicity", "pain timing", "pain episodes",
            "pain behavior", "pain expression", "non-verbal signs", "pain cues",
            "pain impact", "functional impact", "effect on activities",
            "pain management", "pain control", "pain treatment", "pain therapy"
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
            "independent", "nakakaya", "needs help", "nangangailangan ng tulong",
            # Additional hygiene-related terms
            "personal care", "self-care", "grooming", "pangangalaga sa sarili",
            "bathing assistance", "shower assistance", "bath aids", "tulong sa pagligo",
            "dental care", "oral hygiene", "mouth care", "pangangalaga ng bibig",
            "denture care", "false teeth cleaning", "pustiso", "dental prosthesis",
            "hair care", "shampooing", "hair washing", "hair grooming",
            "nail care", "trimming nails", "nail hygiene", "foot care",
            "skin care", "skin integrity", "skin protection", "pangangalaga ng balat",
            "feminine hygiene", "intimate hygiene", "perineal care",
            "toilet hygiene", "toileting", "bathroom assistance", "tulong sa CR",
            "incontinence management", "diaper changing", "adult diapers",
            "hand washing", "hand hygiene", "paghuhugas ng kamay", "sanitizing",
            "laundry", "clean clothing", "clothes changing", "pagpapalit ng damit",
            "environmental hygiene", "clean environment", "sanitized surroundings",
            "infection control", "preventive hygiene", "disease prevention",
            "hygiene products", "toiletries", "personal care items", "hygiene supplies",
            "hygiene routine", "hygiene schedule", "regular hygiene", "daily hygiene"
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
    max_sentences_per_section = 5  # Increase to 5 sentences or more
    
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
            r'specialized care',

            # Additional primary recommendation patterns
            r'(dapat|kailangan) (na )?i-(refer|konsulta|assess|evaluate)',
            r'(tungkol sa|hinggil sa|ukol sa) (pangunahing|primary|main|key) (rekomendasyon|advice)',
            r'(agarang|immediate|promptly) (kumunsulta|magpatingin|mag-seek) (sa|ng)',
            r'(para sa optimal|para sa pinakamahusay|for optimal) (management|pangangasiwa|outcomes)',
            r'(nangangailangan ng|needs|requires) (comprehensive|komprehensibo|multidisciplinary)',
            r'(mahalaga|essential|critical) (na|to) (ma-address|tugunan|konsultahin)',
            r'(kailangang|should be|must be) (considered|i-consider|isaalang-alang)',
            r'(nagmumungkahi|suggesting|recommending) (akong|ako ng|kami ng) (immediate|kaagad)',
            r'(ang aking|my|our) (assessment|puna|recommendation) (ay|is) (na|that)',
            r'(main priority|pangunahing priyoridad) (ay|is|should be)',
            r'(strong indication|malakas na indikasyon) (para sa|for) (prompt|agarang)',
            r'(clearly|malinaw na) (indicated|ipinapahiwatig|needed)'
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
            r'tinuruan sa proper',

            # Additional steps/procedures patterns
            r'(sa pamamagitan ng|through|by) (proper|tamang|following|sumusunod na) (techniques|techniques|methods)',
            r'(gumawa|binuo|created) (ako|namin|kami) (ng|sa|for) (step-by-step|hakbang-hakbang)',
            r'(itinuro ko|taught|showed) (sa kanila|sa pamilya|sa caregiver) (kung paano|how to)',
            r'(kinakailangan|needed|necessary) (ang|to) (conduct|magsagawa ng|perform)',
            r'(program|plano|plan) (na|that) (involves|kasama|includes)',
            r'(proper positioning|tamang posisyon|technique) (para sa|for) (optimal|maximum)',
            r'(demonstrated|ipinakita|ipinakita ko) (to|sa|kay) (the patient|ang pasyente)',
            r'(process involves|proseso ay kinabibilangan ng|steps include)',
            r'(sundan ang|follow the|adhere to) (sumusunod|following|specific) (na proseso|process)',
            r'(specific technique|partikular na technique|methodology) (na|that) (ginagamit|used)',
            r'(practice sessions|pagsasanay|training sessions) (para sa|for|to) (mastery|kasanayan)',
            r'(detailed instructions|madetalyeng instructions|specific steps) (tungkol sa|about)',
            r'(using|paggamit ng|gamit ang) (specialized|specialized na|proper) (equipment|kagamitan)'
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
            r'regular reporting',

            # Additional care patterns
            r'(regular|routinely|daily) (i-assess|assess|monitor) (ang|the) (progress|condition|status)',
            r'(dapat|should|must) (regularly|palagian|routinely) (i-check|suriin|monitor)',
            r'(pangangalaga sa|care for|maintenance of) (araw-araw|daily|everyday)',
            r'(carefully|maingat na|close) (obserbahan|observe|monitor) (para sa|for|any)',
            r'(documentation ng|documenting|recording) (daily|araw-araw|regular) (observations|pagbabago)',
            r'(schedule para sa|scheduled|timetable for) (routine|regular|araw-araw)',
            r'(consistent|tuloy-tuloy|regular) (na|that) (aplikasyon|application|implementation)',
            r'(care plan|plano ng pangangalaga) (includes|kasama|covering)',
            r'(proper|tamang|appropriate) (handling|pangangalaga|management) (ng|of|sa)',
            r'(continuous monitoring|patuloy na pagsubaybay|surveillance) (para sa|for|to detect)',
            r'(hygiene protocol|protocol sa kalinisan|cleanliness routine) (na|that) (dapat|should)',
            r'(detailed|detalyado|comprehensive) (na|that) (pangangalaga|care regimen)',
            r'(close supervision|malapitang pagbabantay|vigilant monitoring) (during|habang|sa)'
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
            r'pag-iwas sa paninigarilyo',
            # Additional lifestyle changes patterns
            r'(pagpapanatili ng|maintaining|sustaining) (healthy|malusog|balanced) (lifestyle|pamumuhay)',
            r'(pagbabawas|reduction|limiting) (ng|of|in) (harmful|nakakapinsala|unhealthy)',
            r'(pagsasaayos|adjusting|modifying) (ng|the|your) (daily routine|araw-araw na gawain)',
            r'(incorporating|pagsasama|integration) (ng|of) (healthy habits|malusog na gawain)',
            r'(gradual|unti-unti|progressive) (changes|pagbabago|modifications) (sa|in|to)',
            r'(sustainable|sustainable na|long-term) (lifestyle|pamumuhay|practices)',
            r'(balancing|pag-balance|achieving balance) (between|sa pagitan ng|among)',
            r'(holistic approach|holistikong approach|comprehensive changes) (sa|to|in)',
            r'(quality of life|kalidad ng buhay|well-being) (improvement|pagpapabuti|enhancement)',
            r'(overall wellness|pangkalahatang wellness|total health) (promotion|pagtataguyod)',
            r'(mind-body|isip-katawan|holistic) (connection|koneksyon|approach)',
            r'(establishing|pagbuo|creating) (ng|of|sa) (healthier patterns|mas malusog na pattern)'
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
            r'(warning signs|alarm system|emergency response)',
            # Additional safety risk patterns
            r'(risk assessment|pagsusuri ng panganib|hazard evaluation) (reveals|nagpapakita|shows)',
            r'(dapat|need to|important to) (i-secure|secure|make safe) (ang|the) (environment|kapaligiran)',
            r'(prevention ng|preventing|avoiding) (accidents|aksidente|injuries)',
            r'(identifying|pagtukoy|recognition) (ng|of) (potential hazards|posibleng panganib)',
            r'(safety measures|hakbang para sa kaligtasan|precautions) (such as|tulad ng|like)',
            r'(reduce|bawasan|minimize) (ang risk|the risk|possibility) (ng|of|para sa)',
            r'(environmental|kapaligiran|surroundings) (safety|kaligtasan|security)',
            r'(accident-prone|delikado|hazardous) (areas|lugar|zones)',
            r'(protective|protektibo|protective na) (equipment|kagamitan|devices)',
            r'(emergency response|emergency plan|emergency action) (in case of|sakaling|kung)',
            r'(proper|tamang|appropriate) (use of|paggamit ng|utilization of) (safety devices|safety equipment)',
            r'(avoiding|pag-iwas sa|preventing) (dangerous|mapanganib|unsafe) (situations|sitwasyon)'
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
            r'(swallowing techniques|paglunok|pagkain nang dahan-dahan)',
            # Additional nutrition patterns
            r'(nutrient-rich|mayaman sa sustansiya|nutrient-dense) (foods|pagkain|diet)',
            r'(caloric|calorie|energy) (requirements|pangangailangan|needs)',
            r'(protein intake|konsumo ng protina|protein consumption) (should be|dapat|needs to be)',
            r'(food preparation|paghahanda ng pagkain|meal preparation) (techniques|techniques|methods)',
            r'(portion control|pagkontrol sa portion|serving size) (para sa|for optimal|to manage)',
            r'(meal timing|oras ng pagkain|eating schedule) (is important|ay mahalaga)',
            r'(texture modification|pagbabago ng texture|texture adjustment) (para sa|for|to help with)',
            r'(nutrient deficiency|kakulangan ng nutrients|nutritional gaps) (addressed by|nilulutas ng)',
            r'(increasing|pagdaragdag|adding more) (fiber|dietary fiber|high-fiber foods)',
            r'(hydration status|antas ng hydration|fluid balance) (maintained by|pinapanatili sa pamamagitan ng)',
            r'(nutritional supplements|food supplements|supplementary nutrition) (to address|para tugunan)',
            r'(limiting|pagbabawas|reducing) (intake of|konsumo ng|consumption of) (unhealthy|hindi malusog)'
        ],
        
        "kalusugan_ng_bibig": [
            # More specific oral health patterns that won't capture general medication info
            r'(oral health|oral care|dental care|dental health) ((?!medication|regimen|dose|gamot).)*$',
            r'(kalinisan ng bibig|pangangalaga ng ngipin|dental hygiene)',
            r'(pagsesepilyo|toothbrushing|brushing (of )?teeth) ((?!medication|regimen|dose|gamot).)*$',
            r'(dental checkups?|dental visits?|pagpapa-dentista)',
            r'(flossing|paggamit ng dental floss)',
            r'(dry mouth|tuyong bibig|xerostomia) ((?!medication|regimen|dose|gamot).)*$',
            r'(dentures?|pustiso|false teeth|ngipin)',
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
            # Strong negative pattern to exclude medication discussions
            r'(?!.*?(gamot|medication|regimen|dose|frequency)).*?(bibig|ngipin|dental|oral|teeth)',
            # Additional oral health patterns
            r'(oral assessment|assessment ng bibig|dental evaluation) (reveals|shows|indicates)',
            r'(proper|tamang|thorough) (toothbrushing|pagsesepilyo|oral hygiene)',
            r'(gum health|kalusugan ng gilagid|periodontal condition) (needs|requires|kailangan)',
            r'(dental prosthesis|pustiso|dentures) (maintenance|pangangalaga|care)',
            r'(oral rinse|mouthwash|antiseptic rinse) (after|following|pagkatapos)',
            r'(xerostomia management|pamamahala ng tuyong bibig|dry mouth solutions)',
            r'(oral cancer screening|pagsusuri para sa kanser sa bibig|screening for oral malignancies)',
            r'(regular dental check-ups|regular na pagpapa-dentista|periodic dental examinations)',
            r'(oral pain management|pamamahala ng sakit sa bibig|relief for oral discomfort)',
            r'(soft-bristled|malambot na|gentle) (toothbrush|sepilyo|dental brush)',
            r'(interdental cleaning|paglilinis sa pagitan ng ngipin|flossing)',
            r'(adaptive dental tools|adaptive na kagamitan sa ngipin|specialized oral hygiene tools)'
        ],
        
        "mobility_function": [
            # Mobility patterns
            r'(walker|wheelchair|cane|tungkod|mobility aid|assistive device|gait|paglalakad|paggalaw|mobility)',
            r'(gumamit|gamitin|iminungkahi|inirerekomenda|recommended|suggested) (ang|siya|niya|ni lolo|ni lola)? (walker|wheelchair|cane|tungkod|mobility aid|assistive device)',
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
            r'(physiotherapy|physical therapy|PT exercises)',
            # Additional mobility patterns
            r'(physical capacity|kakayahang pisikal|functional ability) (assessment|evaluation)',
            r'(transfer technique|technique sa paglipat|safe transferring) (mula sa|from|to)',
            r'(proper body mechanics|tamang body mechanics|ergonomic movement) (when|habang|during)',
            r'(mobility device|device para sa mobility|assistive technology) (selection|pagpili|fitting)',
            r'(range of motion exercises|ehersisyo para sa range of motion|flexibility training)',
            r'(muscle strengthening|pagpapalakas ng muscles|strength training) (program|program|regimen)',
            r'(gait pattern|pattern ng paglakad|walking style) (improvement|improvement|enhancement)',
            r'(mobility limitations|limitasyon sa paggalaw|movement restrictions) (addressed through|addressed by)',
            r'(functional training|functional na pagsasanay|activity-based exercises) (focused on|nakatuon sa)',
            r'(postural alignment|alignment ng posture|body positioning) (correction|pagwawasto|improvement)',
            r'(safe ambulation|ligtas na paglalakad|secure mobility) (strategies|strategies|techniques)',
            r'(fall recovery techniques|technique sa pagbangon mula sa pagkahulog|post-fall strategies)'
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
            r'(regular na paggising|consistent waking time)',
            # Additional sleep patterns
            r'(sleep assessment|assessment ng tulog|sleep evaluation) (indicates|nagpapakita|shows)',
            r'(establishing|pagbuo ng|creating) (bedtime routine|routine bago matulog|pre-sleep ritual)',
            r'(optimizing|pag-optimize|improving) (sleep environment|kapaligiran ng tulog|bedroom conditions)',
            r'(sleep duration|tagal ng tulog|hours of sleep) (should be|dapat|needs to be)',
            r'(sleep continuity|tuloy-tuloy na tulog|uninterrupted sleep) (promotion|pagtataguyod|enhancement)',
            r'(circadian rhythm|rhythm ng katawan|body clock) (regulation|regulasyon|alignment)',
            r'(relaxation techniques|technique ng relaxation|calming methods) (before|bago|prior to)',
            r'(addressing|pagtugon sa|managing) (nighttime disruptions|pagkagambala sa gabi|nocturnal awakenings)',
            r'(comfortable sleeping position|komportableng posisyon sa pagtulog|optimal sleep posture)',
            r'(daytime alertness|pagka-alerto sa umaga|wakefulness) (improvement|pagpapabuti|enhancement)',
            r'(sleep tracking|pagsubaybay sa tulog|sleep monitoring) (to identify|para matukoy|to determine)',
            r'(progressive relaxation|unti-unting relaxation|gradual muscle relaxation) (before sleep|bago matulog)'
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
            r'(understanding|pag-unawa sa) (medications?|prescriptions?|gamot)',
            # Add specific pattern for medication simplification discussions
            r'(simplification|simplify|pagpapasimple) (ng|sa) (medication|gamot|drug|medicine) (regimen|schedule|routine)',
            r'(mabawasan|bawasan) (ang|no|sa) (frequency|dalas|dosis|doses) (ng|sa) (gamot|medication)',
            r'(combination medications|combination drugs|pagsasama ng gamot)',
            r'(tinalakay|discussed|pinag-usapan) (ang|tungkol sa) (simplification|pagpapasimple) (ng|sa) (gamot|medication)',
            # Additional medication management patterns
            r'(medication reconciliation|pagsasaayos ng gamot|medication review) (shows|nagpapakita|indicates)',
            r'(compliance strategies|strategies para sa compliance|adherence techniques) (including|kasama|such as)',
            r'(potential interactions|posibleng interaction|drug interactions) (between|sa pagitan ng|among)',
            r'(adjusting|pag-adjust|modifying) (dosing schedule|schedule ng pag-inom|medication timing)',
            r'(simplifying|pagpapasimple|streamlining) (medication regimen|regimen ng gamot|drug schedule)',
            r'(monitoring for|pagbabantay para sa|watching for) (adverse effects|side effects|adverse reactions)',
            r'(therapeutic effectiveness|therapeutic na bisa|drug efficacy) (evaluation|evaluation|assessment)',
            r'(patient education|edukasyon ng pasyente|teaching) (tungkol sa|about|regarding) (medications|gamot)',
            r'(addressing concerns|pagtugon sa mga alalahanin|resolving issues) (tungkol sa|about|regarding) (gamot|medication)',
            r'(proper storage|tamang storage|appropriate storage) (ng|of|for) (medications|gamot|prescriptions)',
            r'(coordinating with|pakikipag-coordinate sa|working with) (physician|doktor|healthcare provider) (tungkol sa|about)',
            r'(medication administration|pag-administer ng gamot|drug delivery) (techniques|techniques|methods)',
            r'(alternative formulations|alternatibong formulation|different forms) (like|tulad ng|such as)'
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
            r'(kasama ang|involvement of|role of) (children|grandchildren|anak|apo)',
            # Additional family support patterns
            r'(family counseling|pagpapayo sa pamilya|family therapy) (sessions|sessions|meetings)',
            r'(enhancing|pagpapahusay|improving) (family communication|komunikasyon sa pamilya)',
            r'(caregiver training|pagsasanay ng caregiver|education for caregivers) (includes|kasama|covering)',
            r'(shared decision-making|sama-samang pagdedesisyon|collaborative approach) (with|kasama|involving) (family|pamilya)',
            r'(setting boundaries|pagtatakda ng boundaries|establishing limits) (within|sa loob ng|in the) (family dynamics|family dynamics)',
            r'(addressing|pagtugon sa|managing) (family conflict|alitan sa pamilya|intergenerational issues)',
            r'(support group|support group|mutual aid group) (for|para sa|dedicated to) (family members|miyembro ng pamilya)',
            r'(managing expectations|pamamahala ng expectations|setting realistic goals) (ng|of|for) (pamilya|family)',
            r'(family-centered|nakatuon sa pamilya|family-focused) (approach|approach|care)',
            r'(cultural considerations|cultural na pagsasaalang-alang|cultural sensitivity) (sa|in|when) (family interventions|family interactions)',
            r'(involving|pagsasama|inclusion) (ng|of) (extended family|malawak na pamilya|family network)',
            r'(filial responsibility|responsibilidad ng anak|family duty) (discussions|usapan|conversations)'
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
            r'(grief counseling|counseling|psychological support)',
            r'(emotional assessment|assessment ng emosyon|psychological evaluation) (indicates|nagpapakita|shows)',
            r'(coping mechanism|mekanismo sa pag-cope|adaptation strategies) (enhancement|pagpapahusay|improvement)',
            r'(mood regulation|pag-regulate ng mood|emotional control) (techniques|techniques|strategies)',
            r'(addressing|pagtugon sa|managing) (anxiety symptoms|sintomas ng anxiety|feelings of worry)',
            r'(depression screening|screening para sa depression|mood assessment) (results|resulta|findings)',
            r'(cognitive stimulation|stimulation ng isip|mental exercises) (activities|activities|program)',
            r'(reminiscence therapy|therapy na reminiscence|life review activities) (to promote|para itaguyod)',
            r'(validation approach|approach ng validation|affirming communication) (when|kapag|during)',
            r'(psychological support|suportang psychological|emotional assistance) (through|sa pamamagitan ng)',
            r'(building resilience|pagbuo ng resilience|strengthening coping abilities) (sa|in|for)',
            r'(therapeutic|therapy|therapeutic na) (activities|activities|interventions) (for|para sa) (emotional well-being|mental health)',
            r'(meaning-making|pagbibigay kahulugan|finding purpose) (activities|gawain|exercises)'
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
            r'(lifestyle modifications?|pagbabago ng pamumuhay)',
            # Additional preventive health patterns
            r'(health maintenance|pagpapanatili ng kalusugan|preventive care) (measures|hakbang|activities)',
            r'(screening schedule|schedule ng screening|preventive screenings) (based on|batay sa|according to)',
            r'(risk factor modification|pagbabago ng risk factors|risk reduction) (strategies|strategies|approaches)',
            r'(early detection|maagang pagtuklas|prompt identification) (ng|of|for) (potential issues|posibleng problema)',
            r'(immunization|pagbabakuna|vaccination) (status|status|records) (update|update|review)',
            r'(proactive measures|proactive na hakbang|preventive strategies) (to avoid|para maiwasan)',
            r'(health promotion|pagsusulong ng kalusugan|wellness initiatives) (activities|activities|programs)',
            r'(preventing complications|pag-iwas sa komplikasyon|complication prevention) (through|sa pamamagitan ng)',
            r'(routine health checks|regular na check-up|periodic examinations) (schedule|schedule|timing)',
            r'(modifiable risk factors|nabababagong risk factors|changeable health risks) (intervention|intervention|management)',
            r'(long-term prevention|pangmatagalang pag-iwas|sustained preventive measures) (strategy|strategy|plan)',
            r'(health monitoring|pagsubaybay sa kalusugan|surveillance) (systems|sistema|protocols)'
        ],
        
        "vital_signs_measurements": [
            # Vital signs monitoring patterns
            r'(blood pressure|BP|systolic|diastolic|mm Hg|vital signs|pulse|heart rate|respiratory rate|oxygen saturation|SpO2)',
            r'(pag-check|pinapa-check|sinusukat|monitor|napapansin) (ang|kanyang|niya|ni lolo|ni lola)? (blood pressure|BP|vital signs|pulse|heart rate|respiratory rate|oxygen saturation|SpO2)',
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
            r'(home monitoring|pagsusukat sa bahay)',
            # Additional vital signs patterns
            r'(vital sign trends|trend ng vital signs|pattern of measurements) (analysis|analysis|review)',
            r'(blood pressure goals|target na presyon|BP objectives) (set at|nakatakda sa|established at)',
            r'(heart rate variability|pagbabago-bago ng heart rate|pulse fluctuations) (monitoring|monitoring|tracking)',
            r'(temperature monitoring|pagsubaybay ng temperatura|fever surveillance) (schedule|schedule|protocol)',
            r'(oxygen saturation|saturation ng oxygen|SpO2 levels) (maintenance|pagpapanatili|keeping) (above|above|at)',
            r'(respiratory rate|bilis ng paghinga|breathing frequency) (assessment|assessment|evaluation)',
            r'(home monitoring equipment|equipment para sa pagsukat sa bahay|self-monitoring devices) (usage|paggamit|operation)',
            r'(vital sign documentation|documentation ng vital signs|recording measurements) (system|sistema|method)',
            r'(abnormal readings|abnormal na resulta|concerning measurements) (follow-up|follow-up|response) (protocol|protocol)',
            r'(calibration|pag-calibrate|maintenance) (ng|of|for) (measurement devices|aparato sa pagsukat)',
            r'(baseline vitals|baseline measurements|reference values) (comparison|comparison|tracking)',
            r'(vital signs|mga vital sign|physiological parameters) (outside|labas sa|beyond) (normal range|normal na saklaw)'
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
            "key recommendation", "pangunahing rekomendasyon", "pangangailangang",
            "iniuutos", "primary action", "unang hakbang", "kritikal", "fundamental", "prayoridad", 
            "kagyat", "non-negotiable", "dapat matugunan agad", "hindi pwedeng hindi", "dapat muna", 
            "primary concern", "immediate attention", "priority intervention", "kailangan-kailangan", 
            "pinakamahalagang aksyon", "vital step", "urgent recommendation", "imperative", 
            "pinakamahalagang hakbang", "top priority", "emergency consultation", "kagyat na aksyon", 
            "hindi dapat ipagpaliban", "dapat iprioridad", "unang-unang hakbang", "primary focus"
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
            "therapy session", "treatment course", "physical therapy", "kognitibong",
            "sunod-sunod", "konkretong hakbang", "practical steps", "action plan", "sequential", 
            "programmed approach", "operational steps", "phase", "hakbang-hakbang", "action items", 
            "implementation details", "sequence", "staged approach", "tactical steps", "execution plan", 
            "guidelines", "protokol", "pamantayan", "mga alituntunin", "proseso", "pagpapatupad", 
            "pagpapatupad ng mga hakbang", "specific instructions", "hands-on training", 
            "demonstration steps", "daloy ng proseso", "step-wise instructions", "detailed procedure"
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
            "documentation ng symptoms", "report", "consistent", "assistance", "suporta",
            "pang-araw-araw na atensyon", "routine maintenance", "palagiang pagbabantay", 
            "continuous monitoring", "nurturing", "attentiveness", "pagkalinga", "watchful eye", 
            "hands-on assistance", "patient handling", "custodial care", "supportive care", 
            "compassionate care", "day-to-day support", "pangangalagang tuloy-tuloy", 
            "palaging pagmamasid", "routine assessment", "regular evaluation", 
            "patuloy na pagsubaybay", "habitual monitoring", "araw-araw na pagsusuri", 
            "pangmatagalang pangangalaga", "sustained attention", "ongoing care", "caretaking"
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
            "stress reduction", "home modification", "safety modification", "daily habit",
            "bagong gawi", "panghabang-buhay na pagbabago", "sustainable changes", "transformative habits", 
            "lifestyle overhaul", "habit formation", "bagong sistema", "lifestyle redesign", 
            "life pattern adjustment", "lifestyle adaptation", "behavioral adjustment", "new normal", 
            "quality of life improvement", "pagbabago ng araw-araw na gawi", "pangmatagalang pagbabago", 
            "health-promoting behavior", "wellness routines", "restorative practices", 
            "rehabilitative approach", "adaptation strategy", "lifestyle management", 
            "self-care practices", "pagbabago ng nakagawian", "sustainable routine", "wellness plan"
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
            "fall prevention", "pag-iwas sa pagkadapa", "preventative", "emergency plan",
            "peligro", "banta sa kaligtasan", "risk assessment", "danger points", "vulnerabilities", 
            "safety considerations", "precautionary measures", "high-risk areas", "accident-prone", 
            "injury prevention", "risk mitigation", "safety protocol", "safety compliance", 
            "panganib sa kalusugan", "safety evaluation", "risk factor analysis", "hazard identification", 
            "safety planning", "risk prioritization", "preventable dangers", "fall-proofing", 
            "environmental safety", "protective measures", "safety maintenance", "preventive safeguards",
            "safety inspection", "risk detection"
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
            "modified diet", "meal schedule", "eating assistance", "texture modified",
            "sustansiya", "pagkain plan", "nutritional intake", "caloric needs", "food variety", 
            "meal composition", "digestible foods", "nutritional assessment", "nutrient-rich", 
            "calorie counting", "meal frequency", "malnutrisyon", "nutritional supplements", 
            "balanced intake", "macro nutrients", "micro nutrients", "nutrient density", 
            "dyeta", "food consistency", "meal planning", "nutritional status", "food texture", 
            "calorie augmentation", "food fortification", "nutrient supplementation", "dietary management", 
            "nutritional therapy", "food preferences", "dietary restrictions", "feeding assistance"
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
            "dental x-ray", "oral cancer screening", "oral hygiene", "kalinisan ng bibig",
            "oral cavity", "dental structure", "oral hygiene protocol", "mucous membranes", 
            "periodontal health", "dental assessment", "oral inspection", "lingual care", "oral irrigation", 
            "oral hydration", "dental apparatus", "nguya", "pagmumog", "oral tissues", "gum line", 
            "salivary flow", "masticatory function", "dental prosthetics maintenance", "oral comfort", 
            "dental health education", "oral motor function", "dental cleaning routine", "oral moisture", 
            "dental fitting", "oral care products", "dental examination", "periodontal status", 
            "oral self-care", "teeth alignment"
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
            "mobility aid", "strengthening", "transferring", "bed mobility", "sit to stand",
            "kilos", "independent movement", "functional capacity", "motor skills", "movement pattern", 
            "ambulatory status", "locomotion", "physical capability", "galaw capacity", "motion range", 
            "functional performance", "activity tolerance", "physical endurance", "movement restriction", 
            "assisted mobility", "transfer capability", "functional independence", "kinesiotherapy", 
            "movement therapy", "mobility training", "gait training", "functional recovery", 
            "motion improvement", "activity adaptation", "ergonomic movement", "body mechanics", 
            "muscle function", "joint mobility", "physical ability", "movement safety"
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
            "excessive daytime sleeping", "sleep-wake cycle", "sleeping pills", "pagkakatulog",
            "circadian rhythm", "sleep architecture", "sleep cycle", "resting pattern", "pahinga", 
            "sleep disturbance", "sleep continuity", "sleep-wake pattern", "bedtime ritual", 
            "sleep promotion", "restful sleep", "sleep duration", "sleep monitoring", "sleep position", 
            "daytime drowsiness", "gabi-gabi", "pagtulog", "insomnya", "sleep habits", "bedtime routine", 
            "sleep disturbances", "sleep comfort", "sleep surface", "sleep environment optimization", 
            "sleep aids", "sleep timing", "sleep-promoting activities", "relaxation before sleep", 
            "sleep efficiency", "sleep onset", "sleep maintenance"
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
            "medication schedule explanation", "drug administration teaching",
            "drug regimen", "pharmacotherapy", "pharmacological intervention", "drug schedule", 
            "medicine intake", "drug adherence", "therapeutic compliance", "proper administration", 
            "drug efficacy monitoring", "treatment protocol", "medication reconciliation", 
            "polypharmacy management", "drug identification", "medication simplification", 
            "medication timing", "drug formulation", "medication adjustment", "therapeutic dosing", 
            "medication efficacy", "drug administration schedule", "medication storage", 
            "prescription management", "medication understanding", "drug education", "medication system", 
            "medication sorting", "drug preparation"
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
            "caregiver burden", "family coping", "family education", "family roles",
            "family engagement", "kinship care", "family-centered approach", "family coordination", 
            "multigenerational support", "family cohesion", "supportive kinship", "family resources", 
            "family inclusion", "family participation", "family care planning", "collaborative family approach", 
            "family systems", "pamilyang sumusuporta", "family counseling", "family education", 
            "caregiver instruction", "family role adjustment", "extended family involvement", 
            "family communication strategies", "family decision making", "family care conference", 
            "caregiver wellbeing", "family adaptation", "supporting relatives", "intergenerational care", 
            "family caregiving"
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
            "emotional well-being", "motivation", "engagement", "dementia care", "confusion",
            "sikolohikal", "cognitive state", "emotional processing", "psychological intervention", 
            "mental stimulation", "emotional support", "cognitive enhancement", "mental engagement", 
            "emotional validation", "mental processing", "cognitive therapy", "emotional regulation", 
            "mental stability", "cognitive exercise", "emotional intelligence", "mental acuity", 
            "psychological wellbeing", "cognitive health", "mental wellness", "emotional care", 
            "psychological comfort", "cognitive training", "mood therapy", "mental health support", 
            "mindfulness practice", "psychological resilience", "emotional coping", "cognitive stimulation", 
            "therapeutic conversation"
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
            "health screening", "prophylactic", "preventative care", "lifestyle modification",
            "proactive measures", "prophylactic approach", "preventative strategy", "anticipatory care", 
            "preemptive intervention", "health protection", "defensive health strategy", "health safeguarding", 
            "preventive protocols", "proactive health management", "health preservation", 
            "disease avoidance", "preventive assessment", "anticipatory guidance", "pagpigil sa sakit", 
            "preventive screenings", "health vigilance", "preemptive health care", "preventive monitoring", 
            "health surveillance", "risk reduction measures", "preventive health education", 
            "wellness protection", "health deterioration prevention", "complication prevention", 
            "preventive health practices"
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
            "vital signs monitoring", "tracking changes", "recording", "baseline measurements",
            "clinical parameters", "health metrics", "physiological signs", "biometric data", 
            "health indicators", "baseline measurements", "clinical indicators", "physiological parameters", 
            "health status markers", "medical metrics", "body function indicators", "physiological monitoring", 
            "vital parameters", "health statistics", "clinical vitals", "parametric measurement", 
            "health data tracking", "monitoring protocol", "vital trends", "measurement recording", 
            "trend analysis", "parameter documentation", "health data collection", "physiological tracking", 
            "routine measurements", "health parameter analysis", "biomarker monitoring", "health sign recording"
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
        max_sentences = 5  # Increase to 10 sentences or more
        
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
        'tulogSa': 'tulog. Sa',
        # Add more Filipino compound words
        'mag-i': 'mag-isa',
        'mag-isasiya': 'mag-isa siya',
        'mag-isabumangon': 'mag-isa bumangon',
        'mag-isakumain': 'mag-isa kumain',
        'mag-isamaglakad': 'mag-isa maglakad',
        'mag-isamaligo': 'mag-isa maligo',
        'nagiisasiya': 'nag-iisa siya',
        'mag-isangpagtitipon': 'mag-isang pagtitipon',
        'pangmatagalangansakit': 'pangmatagalang ansakit',
        'pangmatagalangsakit': 'pangmatagalang sakit',
        'ngayon ay': 'ngayon ay',
        'isa\'t i': 'isa\'t isa',
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
    
    # Fix hyphenated words that shouldn't be broken
    summary = re.sub(r'mag-(i)([^s])', r'mag-isa \2', summary)  # Fix mag-i to mag-isa
    summary = re.sub(r'mag-(i)$', r'mag-isa', summary)  # Fix mag-i at end of text
    
    # Fix Filipino compound words
    summary = re.sub(r'\b(mag)-([a-zA-Z])', r'\1-\2', summary)  # Ensure hyphen in mag- words
    summary = re.sub(r'\b(pang)-([a-zA-Z])', r'\1-\2', summary)  # Ensure hyphen in pang- words
    
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
                 if section != original_section and 
                    section != "additional_information" and
                    len(sections[section]) < max_sentences_per_section],
                key=lambda x: -x[1]  # Sort by descending score
            )
            
            # If we found an alternative section with room and reasonable score, use it
            if alternative_sections and alternative_sections[0][1] > 0.5:
                alt_section = alternative_sections[0][0]
                sections[alt_section].append(sent)
            else:
                # Only add to additional_information if the sentence has meaningful content
                # Skip single-word sentences or filler phrases
                if len(sent.split()) > 3 and not any(x in sent.lower() for x in 
                                                   ["matanda na ako", "ano pa ang silbi", 
                                                    "di ko na alam", "hindi ko alam"]):
                    sections["additional_information"].append(sent)
    
    # Remove the overflow section if empty or has only very short/filler sentences
    if not sections["additional_information"] or all(len(s.split()) <= 3 for s in sections["additional_information"]):
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