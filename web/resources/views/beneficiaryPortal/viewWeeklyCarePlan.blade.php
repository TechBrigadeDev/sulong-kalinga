<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Care Plan Details | Beneficiary Portal</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewWeeklyCareplan.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        /* Ensure scrolling works properly */
        body {
            height: 100%;
            overflow-y: auto;
        }
        
        /* Fix for home-section to ensure it's scrollable */
        .home-section {
            position: relative;
            min-height: 100%;
            height: auto;
            transition: all 0.5s ease;
            padding: 12px;
            overflow-y: visible;
        }
        
        /* Prevent content clipping */
        .container-fluid {
            padding-bottom: 60px;
        }
        
        /* Responsive adjustment when sidebar is closed */
        .sidebar.close ~ .home-section {
            left: 78px;
            width: calc(100% - 78px);
        }
        
        @media (max-width: 768px) {
            .home-section {
                width: 100%;
                left: 0;
            }
            
            .sidebar.close ~ .home-section {
                left: 0;
                width: 100%;
            }
        }
    </style>

</head>
<body>
    @include('components.beneficiaryPortalNavbar')
    @include('components.beneficiaryPortalSidebar')
    
    <!-- Acknowledgment Confirmation Modal -->
    <div class="modal fade" id="acknowledgmentModal" tabindex="-1" aria-labelledby="acknowledgmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="acknowledgmentModalLabel">Confirm Acknowledgment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>By acknowledging this care plan, you confirm that:</p>
                    <ul>
                        <li>You have thoroughly reviewed all of the information in this care plan.</li>
                        <li>You understand the assessment, care needs, and interventions outlined for you.</li>
                        <li>You agree with the care plan as documented.</li>
                    </ul>
                    <p>This action will be recorded with your name, date, and time.</p>
                    
                    <form id="acknowledgmentForm" method="POST" action="{{ route('beneficiary.care.plan.acknowledge', $weeklyCareplan->weekly_care_plan_id) }}">
                        @csrf
                        <input type="hidden" name="confirmation" value="confirmed">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAcknowledgment">I Acknowledge This Care Plan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Back Button -->
                <a href="{{ route('beneficiary.care.plan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Care Plans
                </a>

                <div class="mx-auto text-center" style="font-size: 20px; font-weight: bold;">
                    WEEKLY CARE PLAN DETAILS
                </div>

                <div>
                    @if(!$weeklyCareplan->acknowledged_by_beneficiary && !$weeklyCareplan->acknowledged_by_family)
                        <button type="button" class="btn btn-primary" id="acknowledgeBtn">
                            <i class="bi bi-check-circle"></i> Acknowledge Care Plan
                        </button>
                    @endif
                </div>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" value="{{ $weeklyCareplan->beneficiary->first_name }} {{ $weeklyCareplan->beneficiary->last_name }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Age</label>
                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($weeklyCareplan->beneficiary->birthday)->age }} years old" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Birthdate</label>
                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($weeklyCareplan->beneficiary->birthday)->format('M d, Y') }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Gender</label>
                            <input type="text" class="form-control" value="{{ $weeklyCareplan->beneficiary->gender }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label">Civil Status</label>
                            <input type="text" class="form-control" value="{{ $weeklyCareplan->beneficiary->civil_status }}" readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" value="{{ $weeklyCareplan->beneficiary->street_address }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Medical Conditions</label>
                            <div class="border p-3 rounded bg-light">
                                @php
                                    $medicalConditions = $weeklyCareplan->beneficiary->generalCarePlan->healthHistory->medical_conditions ?? null;
                                    if ($medicalConditions) {
                                        // Check if it's a JSON string
                                        if (is_string($medicalConditions) && is_array(json_decode($medicalConditions, true))) {
                                            $conditions = json_decode($medicalConditions, true);
                                            if (is_array($conditions) && count($conditions) > 0) {
                                                echo '<ul class="mb-0">';
                                                foreach ($conditions as $condition) {
                                                    echo '<li>' . e($condition) . '</li>';
                                                }
                                                echo '</ul>';
                                            } else {
                                                echo 'No medical conditions recorded';
                                            }
                                        } else {
                                            echo nl2br(e($medicalConditions));
                                        }
                                    } else {
                                        echo 'No medical conditions recorded';
                                    }
                                @endphp
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Illness</label>
                            <div class="border p-3 rounded bg-light">
                                @php
                                    $illnesses = $weeklyCareplan->illnesses ?? null;
                                    if ($illnesses) {
                                        // Check if it's a JSON string
                                        if (is_string($illnesses) && is_array(json_decode($illnesses, true))) {
                                            $illnessList = json_decode($illnesses, true);
                                            if (is_array($illnessList) && count($illnessList) > 0) {
                                                echo '<ul class="mb-0">';
                                                foreach ($illnessList as $illness) {
                                                    echo '<li>' . e($illness) . '</li>';
                                                }
                                                echo '</ul>';
                                            } else {
                                                echo 'No illnesses recorded';
                                            }
                                        } else {
                                            echo nl2br(e($illnesses));
                                        }
                                    } else {
                                        echo 'No illnesses recorded';
                                    }
                                @endphp
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Assessment & Vital Signs</h5>
                    <div class="text-white">
                        <small>Date: {{ \Carbon\Carbon::parse($weeklyCareplan->date)->format('M d, Y') }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Assessment</label>
                            <div class="border p-3 rounded mb-3 bg-light">
                                {!! nl2br(e($weeklyCareplan->assessment)) !!}
                            </div>
                            
                            @if($weeklyCareplan->evaluation_recommendations)
                                <label class="form-label">Evaluation & Recommendations</label>
                                <div class="border p-3 rounded bg-light">
                                    {!! nl2br(e($weeklyCareplan->evaluation_recommendations)) !!}
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Vital Signs</label>
                                    <table class="table table-sm table-bordered">
                                        <tbody>
                                            <tr>
                                                <th width="40%">Blood Pressure</th>
                                                <td>{{ $weeklyCareplan->vitalSigns->blood_pressure ?? 'Not recorded' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Heart Rate</th>
                                                <td>{{ $weeklyCareplan->vitalSigns->pulse_rate ? $weeklyCareplan->vitalSigns->pulse_rate.' bpm' : 'Not recorded' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Respiratory Rate</th>
                                                <td>{{ $weeklyCareplan->vitalSigns->respiratory_rate ? $weeklyCareplan->vitalSigns->respiratory_rate.' breaths/min' : 'Not recorded' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Temperature</th>
                                                <td>{{ $weeklyCareplan->vitalSigns->body_temperature ? $weeklyCareplan->vitalSigns->body_temperature.'°C' : 'Not recorded' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label">Photo</label>
                                    <div class="border p-2 d-flex justify-content-center align-items-center" style="height: 200px;">
                                        @if($weeklyCareplan->photo_path)
                                            <img src="{{ asset('storage/' . $weeklyCareplan->photo_path) }}" alt="Care Plan Photo" class="img-fluid" style="max-height: 100%;">
                                        @else
                                            <div class="text-center text-muted">No image available</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Care Needs & Interventions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Care Needs</th>
                                    <th>Interventions Implemented</th>
                                    <th width="15%">Duration (min)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $totalMinutes = 0;
                                    $displayedCategories = [];
                                @endphp

                                @foreach($categories as $category)
                                    @php
                                        $categoryInterventions = $interventionsByCategory->get($category->care_category_id, collect());
                                        $categoryCustInterventions = $customInterventions->where('care_category_id', $category->care_category_id);
                                        $totalInterventions = $categoryInterventions->count() + $categoryCustInterventions->count();
                                    @endphp

                                    @if($totalInterventions > 0)
                                        @php
                                            $displayedCategories[] = $category->care_category_id;
                                        @endphp
                                        <tr>
                                            <td rowspan="{{ $totalInterventions + 1 }}" class="align-middle bg-light">
                                                <strong>{{ strtoupper($category->care_category_name) }}</strong>
                                            </td>
                                        </tr>
                                        
                                        {{-- Standard interventions (from interventions table) --}}
                                        @foreach($categoryInterventions as $intervention)
                                            <tr>
                                                @php $totalMinutes += $intervention->duration_minutes; @endphp
                                                <td class="interventions-column">
                                                    @if($intervention->intervention)
                                                        {{ $intervention->intervention->intervention_description }}
                                                    @else
                                                        {{ $intervention->intervention_description ?? 'Unnamed Intervention' }}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ number_format($intervention->duration_minutes, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        
                                        {{-- Custom interventions (description in weekly_care_plan_interventions) --}}
                                        @foreach($categoryCustInterventions as $custom)
                                            <tr>
                                                @php $totalMinutes += $custom->duration_minutes; @endphp
                                                <td class="interventions-column">
                                                    {{ $custom->intervention_description ?? 'Unnamed Intervention' }}
                                                    <span class="badge bg-info">Custom</span>
                                                </td>
                                                <td class="text-center">{{ number_format($custom->duration_minutes, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach

                                @if(count($displayedCategories) === 0)
                                    <tr>
                                        <td colspan="3" class="text-center py-3">No interventions recorded for this care plan.</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">Total Duration:</th>
                                    <th>{{ number_format($totalMinutes, 2) }} min</th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="text-end">Acknowledgement Status:</th>
                                    <th>
                                        @if($weeklyCareplan->acknowledged_by_beneficiary)
                                            <span class="badge bg-success">
                                                Acknowledged by {{ Auth::guard('beneficiary')->check() ? 'You' : 'Beneficiary' }}
                                            </span>
                                        @elseif($weeklyCareplan->acknowledged_by_family)
                                            <span class="badge bg-success">
                                                Acknowledged by {{ Auth::guard('family')->check() ? 'You' : 'Family' }}
                                            </span>
                                        @elseif($weeklyCareplan->acknowledgement_signature)
                                            <span class="badge bg-success">Acknowledged</span>
                                        @else
                                            <span class="badge bg-warning">Pending Acknowledgement</span>
                                        @endif
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            @if($weeklyCareplan->acknowledgement_signature)
                @php
                    $signatureData = json_decode($weeklyCareplan->acknowledgement_signature, true);
                @endphp
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Acknowledgement Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Acknowledged By:</strong> {{ $signatureData['name'] ?? 'Unknown' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Role:</strong> {{ $signatureData['acknowledged_by'] ?? 'Unknown' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Date:</strong> {{ isset($signatureData['date']) ? \Carbon\Carbon::parse($signatureData['date'])->format('M d, Y g:i A') : 'Unknown' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle acknowledge button click
            const acknowledgeBtn = document.getElementById('acknowledgeBtn');
            if (acknowledgeBtn) {
                acknowledgeBtn.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(document.getElementById('acknowledgmentModal'));
                    modal.show();
                });
            }
            
            // Handle confirm acknowledgment
            const confirmAcknowledgment = document.getElementById('confirmAcknowledgment');
            if (confirmAcknowledgment) {
                confirmAcknowledgment.addEventListener('click', function() {
                    document.getElementById('acknowledgmentForm').submit();
                });
            }
        });
    </script>
</body>
</html>