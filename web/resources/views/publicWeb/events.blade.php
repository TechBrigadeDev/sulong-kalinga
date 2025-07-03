<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events | COSE Mobile Healthcare Service</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
</head>
<body>
    @include('components.navbar')

    <!-- Header Banner -->
    <div class="header-banner">
        <div class="header-content container">
            <h1 class="display-5 fw-bold mb-3">Mobile Healthcare Service Events</h1>
            <p class="lead mb-4">Upcoming health missions, caregiver trainings, and community outreach programs</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#events" class="btn btn-lg btn-outline-light rounded-pill px-4">
                    <i class="bi bi-calendar-event me-2"></i>View Events
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mb-5" id="events">
        <!-- Filter Buttons -->
        <div class="filter-container">
            <div class="d-flex flex-wrap justify-content-center">
                <button type="button" class="btn filter-btn active" data-filter="all">All Events</button>
                <button type="button" class="btn filter-btn" data-filter="health-mission">Health Missions</button>
                <button type="button" class="btn filter-btn" data-filter="training">Caregiver Training</button>
                <button type="button" class="btn filter-btn" data-filter="outreach">Community Outreach</button>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="row g-4" id="events-container">
            <!-- Event 1 -->
            <div class="col-md-6 col-lg-4" data-category="health-mission">
                <div class="event-card">
                    <div class="event-img-container">
                        <img src="{{ asset('images/health-mission-1.jpg') }}" class="event-img" alt="Mobile health clinic">
                        <span class="event-badge">Health Mission</span>
                    </div>
                    <div class="event-card-body">
                        <div class="event-date">
                            <i class="bi bi-calendar-event"></i> June 15, 2025
                        </div>
                        <h3 class="event-title">Mobile Clinic in Mondragon</h3>
                        <div class="event-location">
                            <i class="bi bi-geo-alt-fill"></i> Barangay Health Center
                        </div>
                        <button class="btn btn-details" data-bs-toggle="modal" data-bs-target="#eventModal1">
                            View Details <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Event 2 -->
            <div class="col-md-6 col-lg-4" data-category="training">
                <div class="event-card">
                    <div class="event-img-container">
                        <img src="{{ asset('images/caregiver-training.jpg') }}" class="event-img" alt="Caregiver training">
                        <span class="event-badge">Training</span>
                    </div>
                    <div class="event-card-body">
                        <div class="event-date">
                            <i class="bi bi-calendar-event"></i> June 22, 2025
                        </div>
                        <h3 class="event-title">Elderly Care Techniques</h3>
                        <div class="event-location">
                            <i class="bi bi-geo-alt-fill"></i> COSE Training Center
                        </div>
                        <button class="btn btn-details" data-bs-toggle="modal" data-bs-target="#eventModal2">
                            View Details <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Event 3 -->
            <div class="col-md-6 col-lg-4" data-category="outreach">
                <div class="event-card">
                    <div class="event-img-container">
                        <img src="{{ asset('images/community-outreach.jpg') }}" class="event-img" alt="Community outreach">
                        <span class="event-badge">Outreach</span>
                    </div>
                    <div class="event-card-body">
                        <div class="event-date">
                            <i class="bi bi-calendar-event"></i> June 28, 2025
                        </div>
                        <h3 class="event-title">Senior Health Awareness Day</h3>
                        <div class="event-location">
                            <i class="bi bi-geo-alt-fill"></i> San Roque Plaza
                        </div>
                        <button class="btn btn-details" data-bs-toggle="modal" data-bs-target="#eventModal3">
                            View Details <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Event 4 -->
            <div class="col-md-6 col-lg-4" data-category="health-mission">
                <div class="event-card">
                    <div class="event-img-container">
                        <img src="{{ asset('images/hypertension-screening.jpg') }}" class="event-img" alt="Hypertension screening">
                        <span class="event-badge">Health Mission</span>
                    </div>
                    <div class="event-card-body">
                        <div class="event-date">
                            <i class="bi bi-calendar-event"></i> July 5, 2025
                        </div>
                        <h3 class="event-title">Hypertension Screening</h3>
                        <div class="event-location">
                            <i class="bi bi-geo-alt-fill"></i> Various Barangays
                        </div>
                        <button class="btn btn-details" data-bs-toggle="modal" data-bs-target="#eventModal4">
                            View Details <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Event 5 -->
            <div class="col-md-6 col-lg-4" data-category="training">
                <div class="event-card">
                    <div class="event-img-container">
                        <img src="{{ asset('images/first-aid-training.jpg') }}" class="event-img" alt="First aid training">
                        <span class="event-badge">Training</span>
                    </div>
                    <div class="event-card-body">
                        <div class="event-date">
                            <i class="bi bi-calendar-event"></i> July 12, 2025
                        </div>
                        <h3 class="event-title">First Aid for Elderly</h3>
                        <div class="event-location">
                            <i class="bi bi-geo-alt-fill"></i> COSE Training Center
                        </div>
                        <button class="btn btn-details" data-bs-toggle="modal" data-bs-target="#eventModal5">
                            View Details <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Event 6 -->
            <div class="col-md-6 col-lg-4" data-category="health-mission">
                <div class="event-card">
                    <div class="event-img-container">
                        <img src="{{ asset('images/medication-distribution.jpg') }}" class="event-img" alt="Medication distribution">
                        <span class="event-badge">Health Mission</span>
                    </div>
                    <div class="event-card-body">
                        <div class="event-date">
                            <i class="bi bi-calendar-event"></i> July 20, 2025
                        </div>
                        <h3 class="event-title">Medication Distribution Day</h3>
                        <div class="event-location">
                            <i class="bi bi-geo-alt-fill"></i> Barangay Health Centers
                        </div>
                        <button class="btn btn-details" data-bs-toggle="modal" data-bs-target="#eventModal6">
                            View Details <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    
    <!-- Event Modals -->
    <!-- Modal 1 -->
    <div class="modal fade" id="eventModal1" tabindex="-1" aria-labelledby="eventModal1Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModal1Label">Mobile Clinic in Mondragon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('images/health-mission-1.jpg') }}" class="modal-event-img" alt="Mobile health clinic">
                    
                    <div class="event-meta">
                        <div class="meta-item">
                            <i class="bi bi-calendar-event"></i> June 15, 2025
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-clock"></i> 8:00 AM - 4:00 PM
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-geo-alt-fill"></i> Barangay Health Center, Mondragon
                        </div>
                    </div>
                    
                    <div class="event-description">
                        <p>Our Mobile Healthcare Service team will conduct a full-day medical mission providing comprehensive health services to elderly residents in Mondragon. This event is part of our ongoing commitment to bring healthcare directly to underserved communities.</p>
                    </div>
                    
                    <div class="event-section">
                        <h5>Services Offered</h5>
                        <ul>
                            <li>Free medical consultations with licensed physicians</li>
                            <li>Blood pressure and blood sugar monitoring</li>
                            <li>Basic medication distribution for common conditions</li>
                            <li>Personalized health education sessions</li>
                            <li>Referrals for specialized care when needed</li>
                            <li>Nutrition counseling for seniors</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Target Beneficiaries</h5>
                        <p>Senior citizens aged 60 and above from 5 barangays in Mondragon. Priority will be given to those with chronic conditions and limited mobility.</p>
                    </div>
                    
                    <div class="event-section">
                        <h5>What to Bring</h5>
                        <ul>
                            <li>Valid ID (Senior Citizen ID if available)</li>
                            <li>List of current medications</li>
                            <li>Medical records (if available)</li>
                            <li>Water and snacks</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modal btn-modal-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Close
                    </button>
                    <button type="button" class="btn btn-modal btn-modal-primary">
                        <i class="bi bi-calendar-plus me-2"></i>Add to Calendar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2 -->
    <div class="modal fade" id="eventModal2" tabindex="-1" aria-labelledby="eventModal2Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModal2Label">Elderly Care Techniques</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('images/caregiver-training.jpg') }}" class="modal-event-img" alt="Caregiver training">
                    
                    <div class="event-meta">
                        <div class="meta-item">
                            <i class="bi bi-calendar-event"></i> June 22, 2025
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-clock"></i> 9:00 AM - 3:00 PM
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-geo-alt-fill"></i> COSE Training Center, Catarman
                        </div>
                    </div>
                    
                    <div class="event-description">
                        <p>This hands-on training session is designed for family caregivers and community health workers to learn proper elderly care techniques. The workshop combines theoretical knowledge with practical demonstrations to ensure participants gain valuable skills.</p>
                    </div>
                    
                    <div class="event-section">
                        <h5>Training Content</h5>
                        <ul>
                            <li>Proper hygiene care for bedridden seniors</li>
                            <li>Safe mobility assistance techniques (transferring, walking support)</li>
                            <li>Medication management and administration</li>
                            <li>Nutrition planning for elderly with special dietary needs</li>
                            <li>Recognizing signs of emergency situations</li>
                            <li>Basic first aid for age-related conditions</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Who Should Attend</h5>
                        <ul>
                            <li>Family members caring for elderly relatives</li>
                            <li>Community health workers</li>
                            <li>Barangay health volunteers</li>
                            <li>Anyone interested in elderly care</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Registration</h5>
                        <p>Limited to 25 participants. Please register by June 18 by calling (02) 1234-5678 or emailing mhcs@cose.org.ph. Light snacks and training materials will be provided.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modal btn-modal-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Close
                    </button>
                    <button type="button" class="btn btn-modal btn-modal-primary">
                        <i class="bi bi-calendar-plus me-2"></i>Add to Calendar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 3 -->
    <div class="modal fade" id="eventModal3" tabindex="-1" aria-labelledby="eventModal3Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModal3Label">Senior Health Awareness Day</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('images/community-outreach.jpg') }}" class="modal-event-img" alt="Community outreach">
                    
                    <div class="event-meta">
                        <div class="meta-item">
                            <i class="bi bi-calendar-event"></i> June 28, 2025
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-clock"></i> 8:00 AM - 12:00 PM
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-geo-alt-fill"></i> San Roque Plaza Covered Court
                        </div>
                    </div>
                    
                    <div class="event-description">
                        <p>A community event focused on health awareness and social engagement for senior citizens in San Roque. This event combines health services with social activities to promote overall wellbeing among elderly community members.</p>
                    </div>
                    
                    <div class="event-section">
                        <h5>Activities</h5>
                        <ul>
                            <li>Free blood pressure and blood sugar check-ups</li>
                            <li>Interactive health education talks on common age-related conditions</li>
                            <li>Demonstration of light exercises suitable for seniors</li>
                            <li>Social interaction activities and games</li>
                            <li>Distribution of health information materials</li>
                            <li>Nutritional snack demonstration and tasting</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Participating Organizations</h5>
                        <ul>
                            <li>COSE Mobile Healthcare Team</li>
                            <li>Local Government Health Unit</li>
                            <li>San Roque Senior Citizens Association</li>
                            <li>Botika Binhi Community Pharmacy</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Notes</h5>
                        <p>This event is open to all senior citizens in San Roque and neighboring communities. No registration required. Participants are encouraged to wear comfortable clothing and bring their own water bottles.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modal btn-modal-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Close
                    </button>
                    <button type="button" class="btn btn-modal btn-modal-primary">
                        <i class="bi bi-calendar-plus me-2"></i>Add to Calendar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 4 -->
    <div class="modal fade" id="eventModal4" tabindex="-1" aria-labelledby="eventModal4Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModal4Label">Hypertension Screening</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('images/hypertension-screening.jpg') }}" class="modal-event-img" alt="Hypertension screening">
                    
                    <div class="event-meta">
                        <div class="meta-item">
                            <i class="bi bi-calendar-event"></i> July 5, 2025
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-clock"></i> Various Times
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-geo-alt-fill"></i> Multiple Barangays, San Roque
                        </div>
                    </div>
                    
                    <div class="event-description">
                        <p>Our mobile teams will visit multiple barangays in San Roque to conduct hypertension screening and provide follow-up care for identified cases. This initiative aims to detect undiagnosed hypertension and improve management of existing cases in the elderly population.</p>
                    </div>
                    
                    <div class="event-section">
                        <h5>Services</h5>
                        <ul>
                            <li>Blood pressure monitoring with digital devices</li>
                            <li>Basic health assessment (weight, height, BMI)</li>
                            <li>Medication adjustment for existing hypertensive patients</li>
                            <li>Referral system for severe or complicated cases</li>
                            <li>Personalized diet and lifestyle counseling</li>
                            <li>Free informational materials on hypertension management</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Schedule by Barangay</h5>
                        <ul>
                            <li><strong>Brgy. Poblacion:</strong> 8:00-10:00 AM at Barangay Hall</li>
                            <li><strong>Brgy. Magsaysay:</strong> 10:30 AM-12:30 PM at Health Center</li>
                            <li><strong>Brgy. Rizal:</strong> 1:30-3:30 PM at Day Care Center</li>
                            <li><strong>Brgy. San Isidro:</strong> 8:00-10:00 AM at Basketball Court</li>
                            <li><strong>Brgy. Sto. Niño:</strong> 10:30 AM-12:30 PM at Chapel</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Preparation</h5>
                        <p>For accurate blood pressure readings, participants are advised to:</p>
                        <ul>
                            <li>Avoid caffeine and smoking 30 minutes before measurement</li>
                            <li>Rest for at least 5 minutes before screening</li>
                            <li>Wear loose clothing that allows access to the upper arm</li>
                            <li>Bring current medications if already diagnosed with hypertension</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modal btn-modal-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Close
                    </button>
                    <button type="button" class="btn btn-modal btn-modal-primary">
                        <i class="bi bi-calendar-plus me-2"></i>Add to Calendar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 5 -->
    <div class="modal fade" id="eventModal5" tabindex="-1" aria-labelledby="eventModal5Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModal5Label">First Aid for Elderly</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('images/first-aid-training.jpg') }}" class="modal-event-img" alt="First aid training">
                    
                    <div class="event-meta">
                        <div class="meta-item">
                            <i class="bi bi-calendar-event"></i> July 12, 2025
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-clock"></i> 9:00 AM - 4:00 PM
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-geo-alt-fill"></i> COSE Training Center, Catarman
                        </div>
                    </div>
                    
                    <div class="event-description">
                        <p>A specialized training session focusing on first aid techniques particularly relevant for elderly care. This full-day workshop combines classroom instruction with hands-on practice using mannequins and simulation scenarios.</p>
                    </div>
                    
                    <div class="event-section">
                        <h5>Training Content</h5>
                        <ul>
                            <li>Recognizing emergencies specific to elderly individuals</li>
                            <li>Fall response and prevention strategies</li>
                            <li>Choking response techniques for seniors</li>
                            <li>Managing minor wounds, burns, and skin tears</li>
                            <li>Recognizing signs of stroke and heart attack</li>
                            <li>When and how to seek professional help</li>
                            <li>Basic life support considerations for frail elderly</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Key Features</h5>
                        <ul>
                            <li>Certified instructors with geriatric care experience</li>
                            <li>Small group practice sessions (5:1 participant to instructor ratio)</li>
                            <li>Training materials and reference guide included</li>
                            <li>Certificate of completion provided</li>
                            <li>Lunch and snacks provided</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Registration Details</h5>
                        <p>Fee: ₱500 (includes materials, certificate, and meals). Limited to 20 participants to ensure quality training. Register by July 5 by calling (02) 1234-5678 or emailing mhcs@cose.org.ph. Early registration is encouraged as this workshop typically fills quickly.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modal btn-modal-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Close
                    </button>
                    <button type="button" class="btn btn-modal btn-modal-primary">
                        <i class="bi bi-calendar-plus me-2"></i>Add to Calendar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 6 -->
    <div class="modal fade" id="eventModal6" tabindex="-1" aria-labelledby="eventModal6Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModal6Label">Medication Distribution Day</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="{{ asset('images/medication-distribution.jpg') }}" class="modal-event-img" alt="Medication distribution">
                    
                    <div class="event-meta">
                        <div class="meta-item">
                            <i class="bi bi-calendar-event"></i> July 20, 2025
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-clock"></i> 8:00 AM - 3:00 PM
                        </div>
                        <div class="meta-item">
                            <i class="bi bi-geo-alt-fill"></i> Barangay Health Centers
                        </div>
                    </div>
                    
                    <div class="event-description">
                        <p>Monthly medication distribution for elderly patients enrolled in our chronic care management program. This service ensures continuity of care for seniors with hypertension, diabetes, and other chronic conditions who rely on our MHCS for their medications.</p>
                    </div>
                    
                    <div class="event-section">
                        <h5>Distribution Details</h5>
                        <ul>
                            <li>Distribution of 30-day supply of maintenance medications</li>
                            <li>Individual medication counseling with our pharmacists</li>
                            <li>Follow-up on medication adherence and side effects</li>
                            <li>Basic health check-up (blood pressure, weight monitoring)</li>
                            <li>Schedule adjustment for next refill date</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Requirements</h5>
                        <ul>
                            <li>Bring empty medication packets for refill</li>
                            <li>MHCS patient ID card</li>
                            <li>Medication logbook with adherence record</li>
                            <li>Recent lab results (if available)</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Important Notes</h5>
                        <ul>
                            <li>This service is only for pre-enrolled patients in our MHCS program</li>
                            <li>New patients need to undergo health assessment first</li>
                            <li>Patients with uncontrolled conditions may require clinic visit before refill</li>
                            <li>Family members may pick up medications with proper authorization</li>
                        </ul>
                    </div>
                    
                    <div class="event-section">
                        <h5>Participating Barangays</h5>
                        <p>All 16 barangays in our MHCS coverage area will have distribution points. Please check with your barangay health worker for specific location and time in your area.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modal btn-modal-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i>Close
                    </button>
                    <button type="button" class="btn btn-modal btn-modal-primary">
                        <i class="bi bi-calendar-plus me-2"></i>Add to Calendar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('components.footer')

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Enhanced Event Filtering
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-pressed', 'false');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
                this.setAttribute('aria-pressed', 'true');
                
                // Get filter value
                const filter = this.dataset.filter;
                const events = document.querySelectorAll('#events-container .col-md-6');
                
                // Filter events
                events.forEach(event => {
                    if (filter === 'all' || event.dataset.category === filter) {
                        event.style.display = 'block';
                        event.setAttribute('aria-hidden', 'false');
                    } else {
                        event.style.display = 'none';
                        event.setAttribute('aria-hidden', 'true');
                    }
                });
                
                // Smooth scroll to maintain position
                document.getElementById('events').scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Modal focus management
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                const modalId = this.getAttribute('data-bs-target');
                const modal = document.querySelector(modalId);
                
                modal.addEventListener('shown.bs.modal', function() {
                    const closeButton = modal.querySelector('.btn-close');
                    closeButton.focus();
                });
            });
        });

        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>