<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Portal - Medication</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalHomePage.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #5a67d8;
            --secondary-color: #4c51bf;
            --accent-color: #667eea;
            --text-dark: #2d3748;
            --text-light: #718096;
            --bg-light: #f8fafc;
            --bg-card: #ffffff;
            --success-color: #48bb78;
            --warning-color: #ed8936;
            --danger-color: #f56565;
            --info-color: #4299e1;
        }
        
        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .section-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), var(--primary-color));
            border-radius: 2px;
        }
        
        /* Health Status Cards */
        .health-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .health-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
        }
        
        .health-card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .health-card-header i {
            font-size: 1.5rem;
        }
        
        .health-card-body {
            padding: 20px;
            background-color: var(--bg-card);
        }
        
        .condition-item {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #edf2f7;
        }
        
        .condition-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .condition-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
        }
        
        .condition-details {
            font-size: 0.9rem;
            color: var(--text-light);
        }
        
        /* Medication Cards */
        .medication-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .medication-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
        }
        
        .medication-header {
            background: linear-gradient(135deg, #6b46c1, #805ad5);
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .medication-patient {
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .medication-actions .btn {
            margin-left: 8px;
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .medication-actions .btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        
        .medication-body {
            padding: 0;
            background-color: var(--bg-card);
        }
        
        .medication-title {
            padding: 15px 20px 10px;
            font-size: 1rem;
            color: var(--text-light);
            border-bottom: 1px solid #edf2f7;
        }
        
        .medication-item {
            padding: 15px 20px;
            border-bottom: 1px solid #edf2f7;
            transition: background-color 0.2s ease;
        }
        
        .medication-item:hover {
            background-color: #f8fafc;
        }
        
        .medication-item:last-child {
            border-bottom: none;
        }
        
        .medication-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 5px;
        }
        
        .medication-details {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        
        .medication-detail {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .medication-detail i {
            margin-right: 5px;
            color: var(--accent-color);
        }
        
        .badge-time {
            background-color: #ebf4ff;
            color: #4c51bf;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
        }
        
        .badge-time i {
            margin-right: 5px;
        }
        
        .badge-status {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-active {
            background-color: #ebf7ef;
            color: var(--success-color);
        }
        
        .status-completed {
            background-color: #f0f5ff;
            color: var(--info-color);
        }
        
        .status-discontinued {
            background-color: #fff5f5;
            color: var(--danger-color);
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .home-section {
                margin-left: 0;
            }
        }

        .beneficiary-info {
            background-color: var(--bg-card);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
        }

        .beneficiary-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            overflow: hidden;
        }

        .beneficiary-avatar i {
            font-size: 2.5rem;
            color: var(--primary-color);
        }

        .beneficiary-details h3 {
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        .beneficiary-details p {
            margin-bottom: 0;
            color: var(--text-light);
        }

        .last-updated {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-bottom: 20px;
            font-style: italic;
        }
    </style>
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