<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Report Summary</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/nlpUI.css') }}">
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="text-left">
           AI REPORT SUMMARY
        </div>
        
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <!-- Report Selection -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-2">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <select class="form-select select2" id="reportSelect" data-placeholder="Search for a care plan...">
                                        <option></option>
                                        <option value="general_1" data-type="general">John Doe - General Care Plan</option>
                                        <option value="weekly_1" data-type="weekly">John Doe - Weekly Care Plan (May 10, 2023)</option>
                                        <option value="general_2" data-type="general">Jane Smith - General Care Plan</option>
                                        <option value="weekly_2" data-type="weekly">Jane Smith - Weekly Care Plan (May 3, 2023)</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <div class="btn-container">
                                        <button class="btn btn-primary text-white" id="generateSummary">
                                            <i class="bi bi-stars me-1"></i> Generate AI Summary
                                        </button>
                                        <button class="btn btn-success text-white" id="translateReport">
                                            <i class="bi bi-translate me-1"></i> Translate
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Summary Report Column -->
                        <div class="col-12">
                            <div class="summary-container">
                                <div id="emptySummary" class="empty-summary">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                    <h4>No Report Selected</h4>
                                    <p class="text-muted">Select a care plan from the dropdown above to view the report</p>
                                </div>
                                
                                <div id="originalReport" style="display: none;">
                                    <div class="summary-header">
                                        <div class="header-content">
                                            <div>
                                                <h4 class="section-title"><span id="patientName">Patient Name</span></h4>
                                                <div class="patient-meta">
                                                    <span class="patient-meta-item"><span id="patientAge">--</span> years</span>
                                                    <span class="patient-meta-item"><span id="patientGender">--</span></span>
                                                    <span class="patient-meta-item"><span id="reportDate">--</span></span>
                                                </div>
                                                <div class="report-meta">
                                                    <span class="report-meta-item">Author: <span id="reportAuthor">--</span></span>
                                                    <span class="report-meta-item">Beneficiary: <span id="reportBeneficiary">--</span></span>
                                                </div>
                                            </div>
                                            <span class="plan-type-badge" id="planTypeBadge">General Plan</span>
                                        </div>
                                    </div>
                                    
                                    <div class="p-3 position-relative">
                                        <!-- Content will be loaded here dynamically -->
                                        <div id="originalContent"></div>
                                    </div>
                                </div>
                                
                                <div id="summaryReport" style="display: none;">
                                    <div class="summary-header">
                                        <div class="header-content">
                                            <div>
                                                <h4 class="section-title"><span id="summaryPatientName">Patient Name</span></h4>
                                                <div class="patient-meta">
                                                    <span class="patient-meta-item"><span id="summaryPatientAge">--</span> years</span>
                                                    <span class="patient-meta-item"><span id="summaryPatientGender">--</span></span>
                                                    <span class="patient-meta-item"><span id="summaryReportDate">--</span></span>
                                                </div>
                                                <div class="report-meta">
                                                    <span class="report-meta-item">Author: <span id="summaryReportAuthor">--</span></span>
                                                    <span class="report-meta-item">Beneficiary: <span id="summaryReportBeneficiary">--</span></span>
                                                </div>
                                            </div>
                                            <span class="plan-type-badge" id="summaryPlanTypeBadge">General Plan</span>
                                        </div>
                                        <div class="summary-actions mt-5">
                                            <button class="btn btn-sm btn-primary" id="viewOriginalReportBtn">
                                                <i class="bi bi-eye me-1"></i> View Original
                                            </button>
                                            <button class="btn btn-sm btn-secondary edit-summary-btn" id="editSummaryBtn">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-success save-summary-btn" id="saveSummaryBtn">
                                                <i class="bi bi-floppy me-1"></i> Save
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="p-3 position-relative summary-content-container">
                                        <!-- Loader container -->
                                        <div class="loader-container" id="loaderContainer">
                                            <div class="loader"></div>
                                            <div class="loader-text" id="loaderText">Generating AI Summary...</div>
                                            <div class="progress-container">
                                                <div class="progress-bar" id="progressBar"></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Add section button (visible only in edit mode) -->
                                        <button class="btn btn-sm btn-primary add-section-btn" id="addSectionBtn">
                                            <i class="bi bi-plus me-1"></i> Add New Section
                                        </button>
                                        
                                        <!-- Content will be loaded here dynamically -->
                                        <div id="summaryContent"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Original Report Modal -->
    <div class="modal fade full-report-modal" id="originalReportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="originalReportModalTitle">Original Care Plan Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="originalReportContent">
                    <!-- Original report content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize select2 with search
            $('#reportSelect').select2({
                theme: 'bootstrap-5',
                placeholder: 'Search for a care plan...',
                width: '100%'
            });
            
            // Sample data for demonstration (maintaining original content structure)
            const sampleData = {
                general_1: {
                    type: 'general',
                    patientName: "John Doe",
                    patientAge: "72",
                    patientGender: "Male",
                    author: "Dr. Maria Santos",
                    beneficiary: "John Doe",
                    primaryCaregiver: "Maria Santos",
                    emergencyContact: "Juan Doe (Son) - 09123456789",
                    patientAddress: "123 Main St, Barangay 1, City",
                    patientPhone: "09123456789",
                    medicalConditions: ["Hypertension", "Type 2 Diabetes"],
                    careNeeds: [
                        { need: "Mobility", frequency: "Daily", assistance: "Walking assistance" },
                        { need: "Medication", frequency: "Twice daily", assistance: "Administration" },
                        { need: "Personal Hygiene", frequency: "Daily", assistance: "Full assistance" }
                    ],
                    medications: [
                        { name: "Lisinopril", dosage: "10mg", frequency: "Once daily", instructions: "Take in the morning" },
                        { name: "Metformin", dosage: "500mg", frequency: "Twice daily", instructions: "With meals" }
                    ],
                    emergencyProcedures: "Administer prescribed medications and contact emergency services immediately",
                    reviewDate: "June 15, 2023",
                    fullReport: `
                        <h4>Complete General Care Plan Report</h4>
                        <h5 class="mt-4">Patient Information</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> John Doe</p>
                                <p><strong>Age:</strong> 72</p>
                                <p><strong>Gender:</strong> Male</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Address:</strong> 123 Main St, Barangay 1, City</p>
                                <p><strong>Phone:</strong> 09123456789</p>
                                <p><strong>Emergency Contact:</strong> Juan Doe (Son) - 09123456789</p>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">Medical History</h5>
                        <hr>
                        <p><strong>Primary Conditions:</strong> Hypertension, Type 2 Diabetes</p>
                        <p><strong>Other Conditions:</strong> Mild arthritis, High cholesterol</p>
                        <p><strong>Allergies:</strong> Penicillin (rash), Latex (mild irritation)</p>
                        <p><strong>Previous Surgeries:</strong> Appendectomy (1975), Cataract surgery (2018)</p>
                        
                        <h5 class="mt-4">Comprehensive Care Needs</h5>
                        <hr>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Care Need</th>
                                    <th>Frequency</th>
                                    <th>Assistance Required</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Mobility</td>
                                    <td>Daily</td>
                                    <td>Walking assistance</td>
                                    <td>Uses cane for short distances, wheelchair for longer distances</td>
                                </tr>
                                <tr>
                                    <td>Medication</td>
                                    <td>Twice daily</td>
                                    <td>Administration</td>
                                    <td>Needs supervision due to memory issues</td>
                                </tr>
                                <tr>
                                    <td>Personal Hygiene</td>
                                    <td>Daily</td>
                                    <td>Full assistance</td>
                                    <td>Requires help with bathing and dressing</td>
                                </tr>
                                <tr>
                                    <td>Meal Preparation</td>
                                    <td>3x daily</td>
                                    <td>Partial assistance</td>
                                    <td>Diabetic diet requirements</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <h5 class="mt-4">Detailed Medication Plan</h5>
                        <hr>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Instructions</th>
                                    <th>Purpose</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Lisinopril</td>
                                    <td>10mg</td>
                                    <td>Once daily</td>
                                    <td>Take in the morning</td>
                                    <td>Blood pressure control</td>
                                </tr>
                                <tr>
                                    <td>Metformin</td>
                                    <td>500mg</td>
                                    <td>Twice daily</td>
                                    <td>With meals</td>
                                    <td>Blood sugar control</td>
                                </tr>
                                <tr>
                                    <td>Atorvastatin</td>
                                    <td>20mg</td>
                                    <td>Once daily at bedtime</td>
                                    <td>Take with water</td>
                                    <td>Cholesterol management</td>
                                </tr>
                                <tr>
                                    <td>Baby Aspirin</td>
                                    <td>81mg</td>
                                    <td>Once daily</td>
                                    <td>With breakfast</td>
                                    <td>Cardiovascular protection</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <h5 class="mt-4">Comprehensive Emergency Plan</h5>
                        <hr>
                        <p><strong>Emergency Contacts:</strong></p>
                        <ul>
                            <li>Primary: Juan Doe (Son) - 09123456789</li>
                            <li>Secondary: Maria Doe (Daughter) - 09198765432</li>
                            <li>Physician: Dr. Maria Santos - 09223334444</li>
                        </ul>
                        <p><strong>Emergency Procedures:</strong></p>
                        <ol>
                            <li>Check patient's vital signs</li>
                            <li>Administer prescribed emergency medications if applicable</li>
                            <li>Contact emergency services (911 or local equivalent)</li>
                            <li>Notify all emergency contacts</li>
                            <li>Have patient's medical information and medications ready</li>
                            <li>Accompany patient to hospital if transport is needed</li>
                        </ol>
                        
                        <h5 class="mt-4">Additional Notes</h5>
                        <hr>
                        <p>Patient prefers morning showers and has a routine of reading the newspaper after breakfast. He enjoys classical music and responds well to calm environments. Patient has mild short-term memory loss but retains long-term memories well.</p>
                        <p>Dietary restrictions include low sodium and controlled carbohydrates. Patient is allowed one small sweet treat per day with lunch, as per agreement with nutritionist.</p>
                    `
                },
                weekly_1: {
                    type: 'weekly',
                    patientName: "John Doe",
                    patientAge: "72",
                    patientGender: "Male",
                    reportDate: "May 10, 2023",
                    author: "Nurse Robert Johnson",
                    beneficiary: "John Doe",
                    primaryCaregiver: "Maria Santos",
                    emergencyContact: "Juan Doe (Son) - 09123456789",
                    medicalConditions: ["Hypertension", "Type 2 Diabetes"],
                    vitals: {
                        bloodPressure: "142/88 mmHg",
                        temperature: "36.8°C",
                        pulseRate: "78 bpm",
                        weight: "78.5 kg",
                        bloodSugar: "128 mg/dL (fasting)"
                    },
                    interventions: [
                        { activity: "Mobility assistance", duration: "15 min/day", notes: "Improved walking with cane" },
                        { activity: "Medication administration", duration: "10 min/dose", notes: "100% adherence" },
                        { activity: "Cognitive exercises", duration: "30 min/session", notes: "Enjoyed memory games" },
                        { activity: "Blood sugar monitoring", duration: "5 min/day", notes: "Stable readings" }
                    ],
                    careAssessment: "The patient requires assistance with mobility due to knee pain, particularly with walking and transfers. Cognitive function is intact but would benefit from memory exercises. Requires full assistance with personal hygiene and medication management.",
                    evaluation: "This week, patient showed improvement in mobility but blood pressure remains elevated. Medication adherence was excellent. Patient participated in cognitive exercises 3 times this week with good engagement. Nutrition intake was consistent with dietary requirements. Blood sugar levels remained within target range.",
                    emergencyProcedures: "Administer prescribed medications and contact emergency services immediately",
                    reviewDate: "May 17, 2023",
                    fullReport: `
                        <h4>Complete Weekly Care Plan Report - May 10, 2023</h4>
                        <h5 class="mt-4">Patient Summary</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> John Doe</p>
                                <p><strong>Age:</strong> 72</p>
                                <p><strong>Primary Conditions:</strong> Hypertension, Type 2 Diabetes</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Week Covered:</strong> May 3-10, 2023</p>
                                <p><strong>Prepared by:</strong> Nurse Robert Johnson</p>
                                <p><strong>Next Review:</strong> May 17, 2023</p>
                            </div>
                        </div>
                        
                        <h5 class="mt-4">Detailed Vital Signs</h5>
                        <hr>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Blood Pressure</th>
                                    <th>Temperature</th>
                                    <th>Pulse Rate</th>
                                    <th>Weight</th>
                                    <th>Blood Sugar (Fasting)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>May 3</td>
                                    <td>140/86 mmHg</td>
                                    <td>36.7°C</td>
                                    <td>76 bpm</td>
                                    <td>78.7 kg</td>
                                    <td>132 mg/dL</td>
                                </tr>
                                <tr>
                                    <td>May 5</td>
                                    <td>138/84 mmHg</td>
                                    <td>36.6°C</td>
                                    <td>74 bpm</td>
                                    <td>78.5 kg</td>
                                    <td>126 mg/dL</td>
                                </tr>
                                <tr>
                                    <td>May 7</td>
                                    <td>144/90 mmHg</td>
                                    <td>36.9°C</td>
                                    <td>80 bpm</td>
                                    <td>78.3 kg</td>
                                    <td>130 mg/dL</td>
                                </tr>
                                <tr>
                                    <td>May 10</td>
                                    <td>142/88 mmHg</td>
                                    <td>36.8°C</td>
                                    <td>78 bpm</td>
                                    <td>78.5 kg</td>
                                    <td>128 mg/dL</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <h5 class="mt-4">Daily Intervention Log</h5>
                        <hr>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Intervention</th>
                                    <th>Duration</th>
                                    <th>Notes</th>
                                    <th>Staff</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td rowspan="3">May 3</td>
                                    <td>Morning medication</td>
                                    <td>10 min</td>
                                    <td>Taken without issues</td>
                                    <td>R. Johnson</td>
                                </tr>
                                <tr>
                                    <td>Mobility assistance</td>
                                    <td>20 min</td>
                                    <td>Walked in garden</td>
                                    <td>R. Johnson</td>
                                </tr>
                                <tr>
                                    <td>Evening medication</td>
                                    <td>10 min</td>
                                    <td>Taken with dinner</td>
                                    <td>M. Garcia</td>
                                </tr>
                                <tr>
                                    <td rowspan="4">May 4</td>
                                    <td>Morning medication</td>
                                    <td>10 min</td>
                                    <td>Normal administration</td>
                                    <td>M. Garcia</td>
                                </tr>
                                <tr>
                                    <td>Cognitive exercises</td>
                                    <td>30 min</td>
                                    <td>Memory card game</td>
                                    <td>R. Johnson</td>
                                </tr>
                                <tr>
                                    <td>Blood sugar check</td>
                                    <td>5 min</td>
                                    <td>128 mg/dL</td>
                                    <td>R. Johnson</td>
                                </tr>
                                <tr>
                                    <td>Evening medication</td>
                                    <td>10 min</td>
                                    <td>Taken with dinner</td>
                                    <td>M. Garcia</td>
                                </tr>
                            </tbody>
                        </table>
                    `
                }
            };

            // When report is selected from dropdown
            $('#reportSelect').change(function() {
                const selectedReport = $(this).val();
                
                if (!selectedReport) {
                    $('#emptySummary').show();
                    $('#originalReport').hide();
                    $('#summaryReport').hide();
                    return;
                }
                
                const planData = sampleData[selectedReport];
                const isWeeklyPlan = planData.type === 'weekly';
                
                // Update plan type badge
                if (isWeeklyPlan) {
                    $('#planTypeBadge').text('Weekly Plan').removeClass('general-plan-badge').addClass('weekly-plan-badge');
                } else {
                    $('#planTypeBadge').text('General Plan').removeClass('weekly-plan-badge').addClass('general-plan-badge');
                }
                
                // Update patient info
                $('#patientName').text(planData.patientName);
                $('#patientAge').text(planData.patientAge);
                $('#patientGender').text(planData.patientGender);
                $('#reportDate').text(planData.reportDate || '--');
                $('#reportAuthor').text(planData.author || '--');
                $('#reportBeneficiary').text(planData.beneficiary || '--');
                
                // Generate original content based on report type
                let originalContent = '';
                
                if (isWeeklyPlan) {
                    // Weekly Care Plan Original
                    originalContent = `
                        <!-- Patient Overview -->
                        <div class="section-card">
                            <div class="section-header">
                                <h5 class="mb-0">Patient Overview</h5>
                            </div>
                            <div class="section-body">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <small>Primary Caregiver</small>
                                        ${planData.primaryCaregiver}
                                    </div>
                                    <div class="info-item">
                                        <small>Emergency Contact</small>
                                        ${planData.emergencyContact}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Health Summary -->
                        <div class="section-card">
                            <div class="section-header">
                                <h5 class="mb-0">Health Summary</h5>
                            </div>
                            <div class="section-body">
                                <div class="mb-3">
                                    <h6 class="mb-2">Medical Conditions</h6>
                                    <div>
                                        ${planData.medicalConditions.map(condition => 
                                            `<span class="badge bg-primary me-2">${condition}</span>`
                                        ).join('')}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <h6 class="mb-2">Vital Signs</h6>
                                    <div class="info-grid">
                                        <div class="vital-card">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <span>Blood Pressure</span>
                                                    <strong class="vital-value">${planData.vitals.bloodPressure}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="vital-card">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <span>Temperature</span>
                                                    <strong class="vital-value">${planData.vitals.temperature}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="vital-card">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <span>Pulse Rate</span>
                                                    <strong class="vital-value">${planData.vitals.pulseRate}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        ${planData.vitals.weight ? `
                                        <div class="vital-card">
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <span>Weight</span>
                                                    <strong class="vital-value">${planData.vitals.weight}</strong>
                                                </div>
                                            </div>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Care Needs Assessment -->
                        <div class="section-card">
                            <div class="section-header">
                                <h5 class="mb-0">Care Needs Assessment</h5>
                            </div>
                            <div class="section-body">
                                <div class="editable-content">
                                    ${planData.careAssessment}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Implemented Interventions -->
                        <div class="section-card">
                            <div class="section-header">
                                <h5 class="mb-0">Implemented Interventions</h5>
                            </div>
                            <div class="section-body">
                                ${planData.interventions.map(intv => `
                                    <div class="intervention-item">
                                        <div>
                                            <strong>${intv.activity}</strong>
                                            <div class="text-muted">${intv.notes}</div>
                                        </div>
                                        <span class="text-muted">${intv.duration}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        
                        <!-- Evaluation & Notes -->
                        <div class="section-card">
                            <div class="section-header">
                                <h5 class="mb-0">Evaluation & Notes</h5>
                            </div>
                            <div class="section-body">
                                <div class="editable-content">
                                    ${planData.evaluation}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // General Care Plan Original
                    originalContent = `
                        <!-- Patient Overview -->
                        <div class="section-card">
                            <div class="section-header">
                                <h5 class="mb-0">Patient Overview</h5>
                            </div>
                            <div class="section-body">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <small>Primary Caregiver</small>
                                        ${planData.primaryCaregiver}
                                    </div>
                                    <div class="info-item">
                                        <small>Emergency Contact</small>
                                        ${planData.emergencyContact}
                                    </div>
                                    ${planData.patientAddress ? `
                                    <div class="info-item">
                                        <small>Address</small>
                                        ${planData.patientAddress}
                                    </div>
                                    ` : ''}
                                    ${planData.patientPhone ? `
                                    <div class="info-item">
                                        <small>Phone Number</small>
                                        ${planData.patientPhone}
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Health Summary -->
                        <div class="section-card">
                            <div class="section-header">
                                <h5 class="mb-0">Health Summary</h5>
                            </div>
                            <div class="section-body">
                                <div class="mb-3">
                                    <h6 class="mb-2">Medical Conditions</h6>
                                    <div>
                                        ${planData.medicalConditions.map(condition => 
                                            `<span class="badge bg-primary me-2">${condition}</span>`
                                        ).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Care Needs -->
                        <div class="section-card">
                            <div class="section-header">
                                <h5 class="mb-0">Care Needs</h5>
                            </div>
                            <div class="section-body">
                                <table class="care-needs-table">
                                    <thead>
                                        <tr>
                                            <th>Care Need</th>
                                            <th>Frequency</th>
                                            <th>Assistance Required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${planData.careNeeds.map(need => `
                                            <tr>
                                                <td>${need.need}</td>
                                                <td>${need.frequency}</td>
                                                <td>${need.assistance}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Medication Management -->
                        <div class="section-card">
                            <div class="section-header">
                                <h5 class="mb-0">Medication Management</h5>
                            </div>
                            <div class="section-body">
                                ${planData.medications.map(med => `
                                    <div class="medication-item">
                                        <strong>${med.name}</strong> ${med.dosage} - ${med.frequency}
                                        <div class="text-muted">${med.instructions}</div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }
                
                // Update the original content
                $('#originalContent').html(originalContent);
                
                // Show the original report and hide empty state
                $('#emptySummary').hide();
                $('#originalReport').show();
                $('#summaryReport').hide();
                
                // Store the current report ID for original report viewing
                $('#viewOriginalReportBtn').data('report-id', selectedReport);
            });

            // Generate summary button click
            $('#generateSummary').click(function() {
                const selectedReport = $('#reportSelect').val();
                
                if (!selectedReport) {
                    alert('Please select a report first');
                    return;
                }
                
                // Show loader and hide content
                $('#loaderContainer').show();
                $('#summaryContent').hide();
                $('#loaderText').text('Generating AI Summary...');
                $('#progressBar').css('width', '0%');
                
                const planData = sampleData[selectedReport];
                const isWeeklyPlan = planData.type === 'weekly';
                
                // Simulate API call delay with progress updates
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 10;
                    $('#progressBar').css('width', `${progress}%`);
                    $('#loaderText').text(`Generating AI Summary... ${progress}%`);
                    
                    if (progress >= 100) {
                        clearInterval(progressInterval);
                        generateSummaryContent();
                    }
                }, 200);
                
                function generateSummaryContent() {
                    // Update plan type badge
                    if (isWeeklyPlan) {
                        $('#summaryPlanTypeBadge').text('Weekly Plan').removeClass('general-plan-badge').addClass('weekly-plan-badge');
                    } else {
                        $('#summaryPlanTypeBadge').text('General Plan').removeClass('weekly-plan-badge').addClass('general-plan-badge');
                    }
                    
                    // Update patient info
                    $('#summaryPatientName').text(planData.patientName);
                    $('#summaryPatientAge').text(planData.patientAge);
                    $('#summaryPatientGender').text(planData.patientGender);
                    $('#summaryReportDate').text(planData.reportDate || '--');
                    $('#summaryReportAuthor').text(planData.author || '--');
                    $('#summaryReportBeneficiary').text(planData.beneficiary || '--');
                    
                    // Generate summary content based on report type
                    let summaryContent = '';
                    
                    if (isWeeklyPlan) {
                        // Weekly Care Plan Summary (AI generated version would be more concise)
                        summaryContent = `
                            <!-- Patient Overview -->
                            <div class="section-card">
                                <div class="section-header">
                                    <h5 class="mb-0">Patient Overview</h5>
                                </div>
                                <div class="section-body">
                                    <p>This weekly care plan covers ${planData.patientName}, a ${planData.patientAge}-year-old ${planData.patientGender.toLowerCase()} with primary conditions: ${planData.medicalConditions.join(', ')}.</p>
                                    <p>The primary caregiver is ${planData.primaryCaregiver} and emergency contact is ${planData.emergencyContact}.</p>
                                </div>
                            </div>
                            
                            <!-- Key Health Indicators -->
                            <div class="section-card">
                                <div class="section-header">
                                    <h5 class="mb-0">Key Health Indicators</h5>
                                </div>
                                <div class="section-body">
                                    <p>Recent vital signs show:</p>
                                    <ul>
                                        <li>Blood Pressure: ${planData.vitals.bloodPressure}</li>
                                        <li>Temperature: ${planData.vitals.temperature}</li>
                                        <li>Pulse Rate: ${planData.vitals.pulseRate}</li>
                                        ${planData.vitals.weight ? `<li>Weight: ${planData.vitals.weight}</li>` : ''}
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Care Highlights -->
                            <div class="section-card">
                                <div class="section-header">
                                    <h5 class="mb-0">Care Highlights</h5>
                                </div>
                                <div class="section-body">
                                    <p>Key interventions this week:</p>
                                    <ul>
                                        ${planData.interventions.slice(0, 3).map(intv => `
                                            <li><strong>${intv.activity}:</strong> ${intv.notes}</li>
                                        `).join('')}
                                    </ul>
                                    <p>Overall assessment: ${planData.careAssessment.split('.')[0]}.</p>
                                </div>
                            </div>
                            
                            <!-- Progress Summary -->
                            <div class="section-card">
                                <div class="section-header">
                                    <h5 class="mb-0">Progress Summary</h5>
                                </div>
                                <div class="section-body">
                                    <p>${planData.evaluation.split('.')[0]}. ${planData.evaluation.split('.')[1] || ''}</p>
                                </div>
                            </div>
                        `;
                    } else {
                        // General Care Plan Summary (AI generated version would be more concise)
                        summaryContent = `
                            <!-- Patient Overview -->
                            <div class="section-card">
                                <div class="section-header">
                                    <h5 class="mb-0">Patient Summary</h5>
                                </div>
                                <div class="section-body">
                                    <p>${planData.patientName} is a ${planData.patientAge}-year-old ${planData.patientGender.toLowerCase()} with primary conditions: ${planData.medicalConditions.join(', ')}.</p>
                                    <p>Primary caregiver: ${planData.primaryCaregiver}</p>
                                    <p>Emergency contact: ${planData.emergencyContact}</p>
                                </div>
                            </div>
                            
                            <!-- Care Needs Summary -->
                            <div class="section-card">
                                <div class="section-header">
                                    <h5 class="mb-0">Care Needs Summary</h5>
                                </div>
                                <div class="section-body">
                                    <p>Primary care needs:</p>
                                    <ul>
                                        ${planData.careNeeds.slice(0, 3).map(need => `
                                            <li><strong>${need.need}:</strong> ${need.frequency} (${need.assistance})</li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Medication Summary -->
                            <div class="section-card">
                                <div class="section-header">
                                    <h5 class="mb-0">Medication Summary</h5>
                                </div>
                                <div class="section-body">
                                    <p>Key medications:</p>
                                    <ul>
                                        ${planData.medications.slice(0, 3).map(med => `
                                            <li><strong>${med.name} ${med.dosage}:</strong> ${med.frequency} (${med.instructions})</li>
                                        `).join('')}
                                    </ul>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Update the summary content
                    $('#summaryContent').html(summaryContent);
                    
                    // Hide loader and show content
                    setTimeout(() => {
                        $('#loaderContainer').hide();
                        $('#summaryContent').show();
                        $('#editSummaryBtn').show();
                        $('#saveSummaryBtn').hide();
                        $('#viewOriginalReportBtn').show();
                        $('#addSectionBtn').hide();
                    }, 500);
                    
                    // Show the summary and hide original report
                    $('#originalReport').hide();
                    $('#summaryReport').fadeIn();
                    
                    // Store the current report ID for original report viewing
                    $('#viewOriginalReportBtn').data('report-id', selectedReport);
                }
            });

            // Edit summary button click
            $('#editSummaryBtn').click(function() {
                // Make content editable
                $('#summaryContent').attr('contenteditable', 'true');
                $('#summaryContent').addClass('editable-content');
                
                // Show edit controls for each section
                $('.section-card').each(function() {
                    $(this).append(`
                        <div class="edit-controls">
                            <button class="btn btn-sm btn-danger btn-remove-section">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </div>
                    `);
                });
                
                // Show save button and hide edit button
                $(this).hide();
                $('#saveSummaryBtn').show();
                $('#addSectionBtn').show();
                
                // Focus on the first editable element
                $('.section-body p:first').focus();
            });

            // Add new section button click
            $('#addSectionBtn').click(function() {
                const sectionCount = $('.section-card').length + 1;
                const newSection = `
                    <div class="section-card">
                        <div class="section-header">
                            <input type="text" class="section-title-input" value="New Section ${sectionCount}" placeholder="Section Title">
                        </div>
                        <div class="section-body editable-content">
                            <p>Click to edit this section content...</p>
                        </div>
                        <div class="edit-controls">
                            <button class="btn btn-sm btn-danger btn-remove-section">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                $('#summaryContent').append(newSection);
            });

            // Remove section button (delegated event)
            $(document).on('click', '.btn-remove-section', function() {
                $(this).closest('.section-card').remove();
            });

            // Save summary button click
            $('#saveSummaryBtn').click(function() {
                // Show saving loader
                $('#loaderContainer').show();
                $('#loaderText').text('Saving changes...');
                $('#progressBar').css('width', '0%');
                
                // Simulate save process
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 20;
                    $('#progressBar').css('width', `${progress}%`);
                    $('#loaderText').text(`Saving changes... ${progress}%`);
                    
                    if (progress >= 100) {
                        clearInterval(progressInterval);
                        completeSave();
                    }
                }, 150);
                
                function completeSave() {
                    // Make content non-editable
                    $('#summaryContent').removeAttr('contenteditable');
                    $('#summaryContent').removeClass('editable-content');
                    
                    // Remove edit controls
                    $('.edit-controls').remove();
                    
                    // Show edit button and hide save button
                    $('#editSummaryBtn').show();
                    $('#saveSummaryBtn').hide();
                    $('#addSectionBtn').hide();
                    
                    // Hide loader
                    setTimeout(() => {
                        $('#loaderContainer').hide();
                        alert('Summary saved successfully!');
                    }, 300);
                }
            });

            // Translate report button click
            $('#translateReport').click(function() {
                const selectedReport = $('#reportSelect').val();
                
                if (!selectedReport) {
                    alert('Please select a report first');
                    return;
                }
                
                if ($('#summaryReport').is(':hidden')) {
                    alert('Please generate the AI summary first');
                    return;
                }
                
                // Show loader and hide content
                $('#loaderContainer').show();
                $('#loaderText').text('Translating Report...');
                $('#progressBar').css('width', '0%');
                
                // Simulate translation API call with progress updates
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += 10;
                    $('#progressBar').css('width', `${progress}%`);
                    $('#loaderText').text(`Translating Report... ${progress}%`);
                    
                    if (progress >= 100) {
                        clearInterval(progressInterval);
                        completeTranslation();
                    }
                }, 200);
                
                function completeTranslation() {
                    // In a real implementation, this would update the content with translated text
                    $('#loaderText').text('Translation complete!');
                    
                    setTimeout(() => {
                        $('#loaderContainer').hide();
                        $('#summaryContent').show();
                    }, 500);
                }
            });

            // View original report button click
            $('#viewOriginalReportBtn').click(function() {
                const reportId = $(this).data('report-id');
                if (!reportId) return;
                
                const planData = sampleData[reportId];
                $('#originalReportModalTitle').text(`Original ${planData.type === 'weekly' ? 'Weekly' : 'General'} Care Plan Report - ${planData.patientName}`);
                $('#originalReportContent').html(planData.fullReport);
                $('#originalReportModal').modal('show');
            });
        });
    </script>
</body>
</html>