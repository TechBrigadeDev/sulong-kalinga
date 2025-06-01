<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Portal - FAQ</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/FAQ.css') }}">
</head>
<body>
    @include('components.beneficiaryPortalNavbar')
    @include('components.beneficiaryPortalSidebar')

    <div class="home-section p-2">
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <h1 class="faq-header">Mobile Healthcare Service (MHCS) FAQs</h1>
                    <h2 class="faq-subheader">Your Questions About Our Elderly Care Services</h2>
                    
                    <div class="faq-container">
                        <!-- General Information -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>What is the Mobile Healthcare Service (MHCS)?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>The Mobile Healthcare Service (MHCS) is a program by the Coalition of Services for the Elderly (COSE) that brings essential healthcare services directly to elderly beneficiaries in their homes. We currently operate in 34 barangays across Northern Samar, serving seniors aged 60 and above who may have difficulty accessing traditional healthcare facilities.</p>
                            </div>
                        </div>
                        
                        <!-- Eligibility -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>Who is eligible for MHCS services?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Our services are available to senior citizens aged 60 and above residing in our service areas (currently Mondragon and San Roque in Northern Samar). We prioritize those with chronic illnesses, disabilities, or limited mobility who face challenges accessing regular healthcare facilities.</p>
                            </div>
                        </div>
                        
                        <!-- Services Offered -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>What services does MHCS provide?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Our comprehensive services include:</p>
                                <ul>
                                    <li>Regular health monitoring (blood pressure, temperature, etc.)</li>
                                    <li>Assistance with medication management</li>
                                    <li>Basic hygiene care and personal assistance</li>
                                    <li>Mobility support and physical therapy guidance</li>
                                    <li>Nutritional guidance and meal assistance</li>
                                    <li>Referrals to hospitals or specialists when needed</li>
                                    <li>Emotional support and social engagement activities</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Frequency of Visits -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>How often will the healthcare workers visit?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Visit frequency is determined based on individual care plans. Most beneficiaries receive weekly visits, but those with more critical needs may receive more frequent care. Our team develops personalized care plans for each beneficiary to ensure their specific needs are met.</p>
                            </div>
                        </div>
                        
                        <!-- Cost -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>Is there any cost for these services?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>MHCS services are provided free of charge to eligible beneficiaries as part of COSE's mission to support marginalized senior citizens. Some specialized medications or treatments may require additional arrangements, which our care workers will discuss with you if needed.</p>
                            </div>
                        </div>
                        
                        <!-- Care Workers -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>Are your care workers qualified?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>All our care workers undergo rigorous training in elderly care, including home care techniques, massage therapy, and basic medical assistance. They are supervised by healthcare professionals and receive continuous education to maintain high service standards.</p>
                            </div>
                        </div>
                        
                        <!-- Privacy -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>How is my personal health information protected?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>We maintain strict confidentiality of all health records. Information is only shared with your consent or when medically necessary with other healthcare providers. Our documentation system tracks care while protecting your privacy.</p>
                            </div>
                        </div>
                        
                        <!-- Emergency -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>What should I do in case of a medical emergency?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>In emergencies, please contact local emergency services immediately. You can also notify your assigned care worker who can help coordinate with appropriate medical facilities. We recommend keeping emergency contacts readily available at all times.</p>
                            </div>
                        </div>
                        
                        <!-- Family Involvement -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>How can family members be involved in the care process?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Family involvement is encouraged! You can:</p>
                                <ul>
                                    <li>Participate in care plan discussions</li>
                                    <li>Provide updates on your loved one's condition</li>
                                    <li>Learn basic care techniques from our workers</li>
                                    <li>Join scheduled family support sessions</li>
                                    <li>Volunteer with our program</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Additional Support -->
                        <div class="faq-card">
                            <div class="faq-question">
                                <span>Does COSE offer other support besides healthcare?</span>
                                <i class="bi bi-chevron-down faq-icon"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Yes! COSE provides various programs including:</p>
                                <ul>
                                    <li>Community health facilities (Botika Binhi pharmacies, wellness centers)</li>
                                    <li>Livelihood programs and income-generating activities</li>
                                    <li>Social engagement opportunities through Older Persons Organizations</li>
                                    <li>Advocacy for senior citizens' rights</li>
                                    <li>Residential care for abandoned elderly at our Group Home in Bulacan</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="contact-info">
                            <h3 class="contact-title">Still have questions?</h3>
                            <p class="contact-details">Contact our MHCS team at:<br>
                            Phone: [Insert COSE contact number]<br>
                            Email: [Insert COSE email]<br>
                            Or visit your local Older Persons Organization (OPO)</p>
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