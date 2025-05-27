<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Portal - Medication</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalMedicationSchedule.css') }}">
</head>
<body>
    @include('components.familyPortalNavbar')
    @include('components.familyPortalSidebar')

    <div class="home-section">
        <div class="text-left">MEDICATION SCHEDULE</div>
        <div class="container-fluid">
            <div class="row p-3" id="home-content">
                <div class="col-12">
                    <!-- Beneficiary Information -->
            <div class="beneficiary-info">
                <div class="beneficiary-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="beneficiary-details">
                    <h3>John Doe</h3>
                    <p>68 years old | Male</p>
                </div>
            </div>

            <p class="last-updated">Last updated: June 15, 2023 at 2:45 PM</p>
            
            <!-- Health Status Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card health-card">
                        <div class="health-card-header">
                            <span><i class="fas fa-heartbeat me-2"></i> Medical Conditions</span>
                        </div>
                        <div class="health-card-body">
                            <div class="condition-item">
                                <div class="condition-name">Type 2 Diabetes</div>
                                <div class="condition-details">Diagnosed 2018, Managed with medication and diet</div>
                            </div>
                            <div class="condition-item">
                                <div class="condition-name">Hypertension</div>
                                <div class="condition-details">Stage 1, Controlled with medication</div>
                            </div>
                            <div class="condition-item">
                                <div class="condition-name">Hyperlipidemia</div>
                                <div class="condition-details">Cholesterol levels improving with statins</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card health-card">
                        <div class="health-card-header">
                            <span><i class="fas fa-thermometer me-2"></i> Current Illness</span>
                        </div>
                        <div class="health-card-body">
                            <div class="condition-item">
                                <div class="condition-name">Seasonal Allergies</div>
                                <div class="condition-details">Pollen allergy, symptoms managed with antihistamines</div>
                            </div>
                            <div class="condition-item">
                                <div class="condition-name">Mild Arthritis</div>
                                <div class="condition-details">Occasional joint pain in knees, managed with NSAIDs as needed</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Medication Schedule -->
            <div class="row mt-4">
                <div class="col-12">
                    <h4 class="mb-4" style="color: var(--text-dark); font-weight: 600;">Current Medications</h4>
                </div>
                
                <!-- Active Medications Card -->
                <div class="col-12">
                    <div class="card medication-card">
                        <div class="medication-header">
                            <div class="medication-patient">Active Medications</div>
                            <div class="medication-actions">
                                <button class="btn" title="Print"><i class="fas fa-print"></i></button>
                                <button class="btn" title="Download"><i class="fas fa-download"></i></button>
                            </div>
                        </div>
                        <div class="medication-body">
                            <div class="medication-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="medication-name">Metformin (500mg)</div>
                                        <div class="text-muted small">For Type 2 Diabetes</div>
                                    </div>
                                    <span class="badge-time"><i class="far fa-clock"></i> 8:00 AM, 6:00 PM</span>
                                </div>
                                <div class="medication-details">
                                    <div class="medication-detail">
                                        <i class="fas fa-calendar-alt"></i> Started: 15 Jan 2020
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-pills"></i> 60 tablets remaining
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-syringe"></i> Oral, with meals
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-prescription-bottle-alt"></i> Prescribed by Dr. Sarah Johnson
                                    </div>
                                </div>
                            </div>
                            
                            <div class="medication-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="medication-name">Lisinopril (10mg)</div>
                                        <div class="text-muted small">For Hypertension</div>
                                    </div>
                                    <span class="badge-time"><i class="far fa-clock"></i> 8:00 AM</span>
                                </div>
                                <div class="medication-details">
                                    <div class="medication-detail">
                                        <i class="fas fa-calendar-alt"></i> Started: 22 Mar 2019
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-pills"></i> 30 tablets remaining
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-syringe"></i> Oral, before breakfast
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-prescription-bottle-alt"></i> Prescribed by Dr. Michael Chen
                                    </div>
                                </div>
                            </div>
                            
                            <div class="medication-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="medication-name">Atorvastatin (20mg)</div>
                                        <div class="text-muted small">For Hyperlipidemia</div>
                                    </div>
                                    <span class="badge-time"><i class="far fa-clock"></i> 9:00 PM</span>
                                </div>
                                <div class="medication-details">
                                    <div class="medication-detail">
                                        <i class="fas fa-calendar-alt"></i> Started: 05 Oct 2021
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-pills"></i> 90 tablets remaining
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-syringe"></i> Oral, at bedtime
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-prescription-bottle-alt"></i> Prescribed by Dr. Sarah Johnson
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- PRN (As Needed) Medications Card -->
                <div class="col-12 mt-4">
                    <div class="card medication-card">
                        <div class="medication-header">
                            <div class="medication-patient">PRN (As Needed) Medications</div>
                        </div>
                        <div class="medication-body">
                            <div class="medication-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="medication-name">Cetirizine (10mg)</div>
                                        <div class="text-muted small">For Seasonal Allergies</div>
                                    </div>
                                    <span class="badge-status status-active">As needed</span>
                                </div>
                                <div class="medication-details">
                                    <div class="medication-detail">
                                        <i class="fas fa-calendar-alt"></i> Started: 01 Apr 2023
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-pills"></i> 15 tablets remaining
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-syringe"></i> Oral, when symptoms appear
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-prescription-bottle-alt"></i> Prescribed by Dr. Michael Chen
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-info-circle"></i> Max 1 tablet per day
                                    </div>
                                </div>
                            </div>
                            
                            <div class="medication-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="medication-name">Ibuprofen (200mg)</div>
                                        <div class="text-muted small">For Pain/Inflammation</div>
                                    </div>
                                    <span class="badge-status status-active">As needed</span>
                                </div>
                                <div class="medication-details">
                                    <div class="medication-detail">
                                        <i class="fas fa-calendar-alt"></i> Started: 12 May 2023
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-pills"></i> 45 tablets remaining
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-syringe"></i> Oral, with food
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-prescription-bottle-alt"></i> Prescribed by Dr. Sarah Johnson
                                    </div>
                                    <div class="medication-detail">
                                        <i class="fas fa-info-circle"></i> Max 3 tablets per day
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            // Example: Add click event for medication items
            const medicationItems = document.querySelectorAll('.medication-item');
            medicationItems.forEach(item => {
                item.addEventListener('click', function() {
                    // This could open a modal with more details
                    console.log('Medication clicked:', this.querySelector('.medication-name').textContent);
                });
            });
        });
    </script>
</body>
</html>