<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FAQ | Family Portal</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/FAQ.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.familyPortalNavbar')
    @include('components.familyPortalSidebar')

    <div class="home-section p-2">
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <h1 class="faq-header">Mobile Healthcare Service (MHCS) FAQs</h1>
                    <h2 class="faq-subheader">{{ T::translate('Your Questions About Our Elderly Care Services', 'Ang Inyong mga Katanungan Tungkol sa Aming Elderly Care Services')}}</h2>
                    
                    <div class="faq-container">
                        <!-- General Information -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>{{ T::translate('What is the Mobile Healthcare Service', 'Ano ang Mobile Healthcare Service')}} (MHCS)?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ T::translate('The Mobile Healthcare Service (MHCS) is a program by the Coalition of Services for the Elderly (COSE) that brings essential healthcare services directly to elderly beneficiaries in their homes. We currently operate in 34 barangays across Northern Samar, serving seniors aged 60 and above who may have difficulty accessing traditional healthcare facilities.', 
                                    'Ang Mobile Healthcare Service (MHCS) ay isang programa ng Coalition of Services for the Elderly (COSE) na nagdadala ng mahahalagang serbisyong pangkalusugan nang direkta sa mga matatandang benepisyaryo sa kanilang mga tahanan. Kami ay kasalukuyang nag-ooperate sa 34 na barangay sa Northern Samar, na nagsisilbi sa mga senior na may edad 60 pataas na nahihirapang ma-access ang tradisyonal na mga pasilidad ng pangangalagang pangkalusugan.')}}</p>
                            </div>
                        </div>
                        
                        <!-- Eligibility -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>{{ T::translate('Who is eligible for MHCS services', 'Sino ang maaaring makatanggap ng mga serbisyo ng MHCS')}}?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ T::translate('Our services are available to senior citizens aged 60 and above residing in our service areas (currently Mondragon and San Roque in Northern Samar). We prioritize those with chronic illnesses, disabilities, or limited mobility who face challenges accessing regular healthcare facilities.', 
                                    'Ang aming mga serbisyo ay available para sa mga senior citizen na may edad 60 pataas na nakatira sa aming mga lugar ng serbisyo (kasalukuyang Mondragon at San Roque sa Northern Samar). Binibigyan namin ng prayoridad ang mga may malalang sakit, kapansanan, o limitadong kakayahang gumalaw na nahihirapang ma-access ang regular na mga pasilidad ng pangangalagang pangkalusugan.')}}</p>
                            </div>
                        </div>
                        
                        <!-- Services Offered -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>{{ T::translate('What services does MHCS provide', 'Anong mga serbisyo ang ibinibigay ng MHCS')}}?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ T::translate('Our comprehensive services include', 'Kabilang sa aming komprehensibong mga serbisyo ang')}}:</p>
                                <ul>
                                    <li>{{ T::translate('Regular health monitoring (blood pressure, temperature, etc.)', 'Regular na pagmo-monitor ng kalusugan (presyon ng dugo, temperatura, atbp.)')}}</li>
                                    <li>{{ T::translate('Assistance with medication management', 'Tulong sa pamamahala ng gamot')}}</li>
                                    <li>{{ T::translate('Basic hygiene care and personal assistance', 'Pangunahing pangangalaga sa kalinisan at personal na tulong')}}</li>
                                    <li>{{ T::translate('Mobility support and physical therapy guidance', 'Suporta sa paggalaw at gabay sa physical therapy')}}</li>
                                    <li>{{ T::translate('Nutritional guidance and meal assistance', 'Gabay sa nutrisyon at tulong sa pagkain')}}</li>
                                    <li>{{ T::translate('Referrals to hospitals or specialists when needed', 'Referral sa mga ospital o espesyalista kung kinakailangan')}}</li>
                                    <li>{{ T::translate('Emotional support and social engagement activities', 'Suportang emosyonal at mga aktibidad para sa pakikisalamuha')}}</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Frequency of Visits -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>{{ T::translate('How often will the healthcare workers visit', 'Suportang emosyonal at mga aktibidad para sa pakikisalamuha')}}?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ T::translate('Visit frequency is determined based on individual care plans. Most beneficiaries receive weekly visits, but those with more critical needs may receive more frequent care. Our team develops personalized care plans for each beneficiary to ensure their specific needs are met.', 
                                    'Ang dalas ng pagbisita ay tinutukoy batay sa indibidwal na mga plano ng pangangalaga. Karamihan sa mga benepisyaryo ay tumatanggap ng lingguhang pagbisita, ngunit ang mga may mas kritikal na pangangailangan ay maaaring makatanggap ng mas madalas na pangangalaga. Ang aming koponan ay gumagawa ng mga personalized na plano ng pangangalaga para sa bawat benepisyaryo upang matiyak na natutugunan ang kanilang mga partikular na pangangailangan.')}}</p>
                            </div>
                        </div>
                        
                        <!-- Cost -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>{{ T::translate('Is there any cost for these services', 'May bayad ba ang mga serbisyong ito')}}?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ T::translate('MHCS services are provided free of charge to eligible beneficiaries as part of COSE\'s mission to support marginalized senior citizens. Some specialized medications or treatments may require additional arrangements, which our care workers will discuss with you if needed.', 
                                    'Ang mga serbisyo ng MHCS ay ibinibigay nang libre sa mga kwalipikadong benepisyaryo bilang bahagi ng misyon ng COSE na suportahan ang mga marginalized na senior citizen. Ang ilang espesyalisadong gamot o paggamot ay maaaring mangailangan ng karagdagang mga pag-aayos, na tatalakayin ng aming mga care worker sa iyo kung kinakailangan.')}}</p>
                            </div>
                        </div>
                        
                        <!-- Care Workers -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>{{ T::translate('Are your care workers qualified', 'Kwalipikado ba ang inyong mga care worker')}}?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ T::translate('All our care workers undergo rigorous training in elderly care, including home care techniques, massage therapy, and basic medical assistance. They are supervised by healthcare professionals and receive continuous education to maintain high service standards.', 
                                    'Lahat ng aming mga care worker ay sumasailalim sa masusing pagsasanay sa pangangalaga ng matatanda, kabilang ang mga teknik sa home care, massage therapy, at pangunahing tulong medikal. Sila ay pinangangasiwaan ng mga propesyonal sa pangangalagang pangkalusugan at tumatanggap ng patuloy na edukasyon upang mapanatili ang mataas na pamantayan ng serbisyo.')}}</p>
                            </div>
                        </div>
                        
                        <!-- Privacy -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>{{ T::translate('How is my personal health information protected', 'Paano pinoprotektahan ang aking personal na impormasyon sa kalusugan')}}?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ T::translate('We maintain strict confidentiality of all health records. Information is only shared with your consent or when medically necessary with other healthcare providers. Our documentation system tracks care while protecting your privacy.', 
                                    'Mahigpit naming pinapanatili ang pagkumpidensyal ng lahat ng mga rekord sa kalusugan. Ang impormasyon ay ibinabahagi lamang sa iyong pahintulot o kung medikal na kinakailangan sa iba pang mga tagapagbigay ng pangangalagang pangkalusugan. Ang aming sistema ng dokumentasyon ay sumusubaybay sa pangangalaga habang pinoprotektahan ang iyong privacy.')}}</p>
                            </div>
                        </div>
                        
                        <!-- Emergency -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>{{ T::translate('What should I do in case of a medical emergency', 'Ano ang dapat kong gawin sa kaso ng medical emergency')}}?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ T::translate('In emergencies, please contact local emergency services immediately. You can also notify your assigned care worker who can help coordinate with appropriate medical facilities. We recommend keeping emergency contacts readily available at all times.', 
                                    'Sa mga emergency, mangyaring makipag-ugnayan kaagad sa lokal na emergency services. Maaari mo ring ipaalam sa itinalagang care worker mo na makakatulong sa pakikipag-ugnayan sa angkop na mga pasilidad medikal. Inirerekomenda namin na panatilihing laging handa ang mga emergency contact.')}}</p>
                            </div>
                        </div>
                        
                        <!-- Family Involvement -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>{{ T::translate('How can family members be involved in the care process', 'Paano maaaring makibahagi ang mga miyembro ng pamilya sa proseso ng pangangalaga')}}?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ T::translate('Family involvement is encouraged! You can', 'Hinihikayat ang pakikibahagi ng pamilya! Maaari kang')}}:</p>
                                <ul>
                                    <li>{{ T::translate('Participate in care plan discussions', 'Makibahagi sa mga talakayan tungkol sa plano ng pangangalaga')}}</li>
                                    <li>{{ T::translate('Provide updates on your loved one\'s condition', 'Magbigay ng mga update tungkol sa kalagayan ng iyong mahal sa buhay')}}</li>
                                    <li>{{ T::translate('Learn basic care techniques from our workers', 'Matuto ng mga pangunahing teknik sa pangangalaga mula sa aming mga worker')}}</li>
                                    <li>{{ T::translate('Join scheduled family support sessions', 'Sumali sa mga nakatakdang sesyon ng suporta para sa pamilya')}}</li>
                                    <li>{{ T::translate('Volunteer with our program', 'Magboluntaryo sa aming programa')}}</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Additional Support -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>{{ T::translate('Does COSE offer other support besides healthcare', 'Nag-aalok ba ang COSE ng iba pang suporta bukod sa pangangalagang pangkalusugan')}}?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>{{ T::translate('Yes! COSE provides various programs including', 'Oo! Nagbibigay ang COSE ng iba\'t ibang programa kabilang ang')}}:</p>
                                <ul>
                                    <li>{{ T::translate('Community health facilities (Botika Binhi pharmacies, wellness centers)', 'Mga pasilidad ng pangkalusugan sa komunidad (Botika Binhi pharmacies, wellness centers)')}}</li>
                                    <li>{{ T::translate('Livelihood programs and income-generating activities', 'Mga programa sa kabuhayan at mga aktibidad na nagbibigay-kita')}}</li>
                                    <li>{{ T::translate('Social engagement opportunities through Older Persons Organizations', 'Mga oportunidad para sa pakikisalamuha sa pamamagitan ng Older Persons Organizations')}}</li>
                                    <li>{{ T::translate('Advocacy for senior citizens\' rights', 'Adbokasiya para sa mga karapatan ng mga senior citizen')}}</li>
                                    <li>{{ T::translate('Residential care for abandoned elderly at our Group Home in Bulacan', 'Residential care para sa mga inabandunang matatanda sa aming Group Home sa Bulacan')}}</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="contact-info">
                            <h3 class="contact-title">{{ T::translate('Still have questions', 'May mga tanong pa')}}?</h3>
                            <p class="contact-details">{{ T::translate('Contact our MHCS team at', 'Makipag-ugnayan sa aming koponan ng MHCS sa')}}:<br>
                            Phone: [Insert COSE contact number]<br>
                            Email: [Insert COSE email]<br>
                            {{ T::translate('Or visit your local Older Persons Organization (OPO)', 'O bisitahin ang iyong lokal na Older Persons Organization (OPO)')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questions = document.querySelectorAll('.faq-question');
            
            questions.forEach(question => {
                question.addEventListener('click', () => {
                    const answer = question.nextElementSibling;
                    const icon = question.querySelector('.faq-icon');
                    
                    // Close all other answers first
                    document.querySelectorAll('.faq-answer').forEach(item => {
                        if (item !== answer && item.classList.contains('active')) {
                            item.classList.remove('active');
                            item.previousElementSibling.querySelector('.faq-icon').classList.remove('active');
                        }
                    });
                    
                    // Toggle the current answer
                    answer.classList.toggle('active');
                    icon.classList.toggle('active');
                });
            });
            
            // Close answers when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.faq-card')) {
                    document.querySelectorAll('.faq-answer').forEach(item => {
                        if (item.classList.contains('active')) {
                            item.classList.remove('active');
                            item.previousElementSibling.querySelector('.faq-icon').classList.remove('active');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>