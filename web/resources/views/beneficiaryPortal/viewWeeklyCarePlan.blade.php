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
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.beneficiaryPortalNavbar')
    @include('components.beneficiaryPortalSidebar')
    
    <!-- Acknowledgment Confirmation Modal -->
    <div class="modal fade" id="acknowledgmentModal" tabindex="-1" aria-labelledby="acknowledgmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="acknowledgmentModalLabel">{{ T::translate('Confirm Acknowledgment', 'Kumpirmahin ang Pagkilala')}}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ T::translate('By acknowledging this care plan, you confirm that', 'Sa pamamagitan ng pagkilala sa care plan na ito, kinukumpirma mo na')}}:</p>
                    <ul>
                        <li>{{ T::translate('You have thoroughly reviewed all of the information in this care plan', 'Nasuri mo nang lubusan ang lahat ng impormasyon sa plano ng pangangalagang ito')}}.</li>
                        <li>{{ T::translate('You understand the assessment, care needs, and interventions outlined for you', 'Nauunawaan mo ang pagtatasa, mga pangangailangan sa pangangalaga, at mga interbensyon na nakabalangkas para sa iyo')}}.</li>
                        <li>{{ T::translate('You agree with the care plan as documented', 'Sumasang-ayon ka sa plano ng pangangalaga na dokumentado')}}.</li>
                    </ul>
                    <p>{{ T::translate('This action will be recorded with your name, date, and time', 'Ang aksyon na ito ay itatala kasama ang iyong pangalan, petsa, at oras')}}.</p>
                    
                    <form id="acknowledgmentForm" method="POST" action="{{ route('beneficiary.care.plan.acknowledge', $weeklyCareplan->weekly_care_plan_id) }}">
                        @csrf
                        <input type="hidden" name="confirmation" value="confirmed">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'I-Kansela')}}</button>
                    <button type="button" class="btn btn-primary" id="confirmAcknowledgment">{{ T::translate('I Acknowledge This Care Plan', 'Aking Kinikilala ang Care Plan na ito')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="container-fluid">
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <!-- Back Button -->
                <a href="{{ route('beneficiary.care.plan.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> {{ T::translate('Back to Care Plans', 'Bumalik sa Care Plan')}}
                </a>

                <div class="text-center" style="font-size: 20px; font-weight: bold; padding: 10px;">
                    {{ T::translate('WEEKLY CARE PLAN DETAILS', 'MGA DETALYE NG WEEKLY CARE PLAN')}}
                </div>

                <div>
                    @if(!$weeklyCareplan->acknowledged_by_beneficiary && !$weeklyCareplan->acknowledged_by_family)
                        <button type="button" class="btn btn-primary w-100" id="acknowledgeBtn">
                            <i class="bi bi-check-circle"></i> {{ T::translate('Acknowledge Care Plan', 'Kilalanin ang Care Plan')}}
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
            <div class="row" id="home-content">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ T::translate('Personal Information', 'Personal na Impormasyon')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label">{{ T::translate('Name', 'Pangalan')}}</label>
                            <input type="text" class="form-control" value="{{ $weeklyCareplan->beneficiary->first_name }} {{ $weeklyCareplan->beneficiary->last_name }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ T::translate('Age', 'Edad')}}</label>
                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($weeklyCareplan->beneficiary->birthday)->age }} {{ T::translate('years old', 'taong gulang')}}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ T::translate('Birthdate', 'Petsa ng Kapanganakan')}}</label>
                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($weeklyCareplan->beneficiary->birthday)->format('M d, Y') }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ T::translate('Gender', 'Kasarian')}}</label>
                            <input type="text" class="form-control" value="{{ $weeklyCareplan->beneficiary->gender }}" readonly>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label">{{ T::translate('Civil Status', 'Katayuan sa Pag-aasawa')}}</label>
                            <input type="text" class="form-control" value="{{ $weeklyCareplan->beneficiary->civil_status }}" readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">{{ T::translate('Address', 'Tirahan')}}</label>
                            <input type="text" class="form-control" value="{{ $weeklyCareplan->beneficiary->street_address }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">{{ T::translate('Medical Conditions', 'Mga Medikal na Kondisyon')}}</label>
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
                                                echo T::translate('No medical conditions recorded', 'Walang naitalang medikal na kondisyon');
                                            }
                                        } else {
                                            echo nl2br(e($medicalConditions));
                                        }
                                    } else {
                                        echo T::translate('No medical conditions recorded', 'Walang naitalang medikal na kondisyon');
                                    }
                                @endphp
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ T::translate('Illness', 'Mga Sakit')}}</label>
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
                                                echo T::translate('No illnesses recorded', 'Walang naitalang sakit');
                                            }
                                        } else {
                                            echo nl2br(e($illnesses));
                                        }
                                    } else {
                                        echo T::translate('No illnesses recorded', 'Walang naitalang sakit');
                                    }
                                @endphp
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ T::translate('Assessment & Vital Signs', 'Pagtatasa at Vital Signs')}}</h5>
                    <div class="text-white">
                        <small>{{ T::translate('Date', 'Petsa')}}: {{ \Carbon\Carbon::parse($weeklyCareplan->date)->format('M d, Y') }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">{{ T::translate('Assessment', 'Pagtatasa') }}</span>
                                <div class="btn-group btn-group-sm" role="group" aria-label="Assessment Toggle">
                                    <button type="button" class="btn btn-outline-primary active" id="btn-assessment-original">Original</button>
                                    <button type="button" class="btn btn-outline-primary" id="btn-assessment-summary">Summary</button>
                                </div>
                            </div>
                            <div class="border p-3 rounded mb-3 bg-light">
                                <span id="assessment-original">
                                    {!! nl2br(e($weeklyCareplan->assessment ?? T::translate('No assessment recorded', 'Walang assessment na naitala'))) !!}
                                </span>
                                <span id="assessment-summary" style="display:none;">
                                    {!! nl2br(e($weeklyCareplan->assessment_summary_final ?? T::translate('No summary available', 'Walang buod na available'))) !!}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ T::translate('Vital Signs & Photo Documentation', 'Vital Signs at Larawan sa Dokumentasyon')}}</h5>
                    <div class="text-white">
                        <small>{{ T::translate('Date', 'Petsa')}}: {{ \Carbon\Carbon::parse($weeklyCareplan->date)->format('M d, Y') }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">                        
                        <div class="col-md-12">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">{{ T::translate('Vital Signs', 'Vital Signs')}}</label>
                                    <table class="table table-sm table-bordered">
                                        <tbody>
                                            <tr>
                                                <th width="40%">{{ T::translate('Blood Pressure', 'Presyon ng Dugo')}}</th>
                                                <td>{{ $weeklyCareplan->vitalSigns->blood_pressure ?? T::translate('Not recorded', 'Hindi naitala') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ T::translate('Heart Rate', 'Bilis ng Pulso')}}</th>
                                                <td>{{ $weeklyCareplan->vitalSigns->pulse_rate ? $weeklyCareplan->vitalSigns->pulse_rate.' bpm' : T::translate('Not recorded', 'Hindi naitala') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ T::translate('Respiratory Rate', 'Bilis ng Paghinga')}}</th>
                                                <td>{{ $weeklyCareplan->vitalSigns->respiratory_rate ? $weeklyCareplan->vitalSigns->respiratory_rate.' breaths/min' : T::translate('Not recorded', 'Hindi naitala') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ T::translate('Temperature', 'Temperatura')}}</th>
                                                <td>{{ $weeklyCareplan->vitalSigns->body_temperature ? $weeklyCareplan->vitalSigns->body_temperature.'°C' : T::translate('Not recorded', 'Hindi naitala') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            
                                <div class="col-6">
                                    <label class="form-label">{{ T::translate('Photo', 'Larawan')}}</label>
                                    <div class="border p-2 d-flex justify-content-center align-items-center" style="height: 200px;">
                                        @if($weeklyCareplan->photo_path)
                                            <img src="{{ asset('storage/' . $weeklyCareplan->photo_path) }}" alt="Care Plan Photo" class="img-fluid" style="max-height: 100%;">
                                        @else
                                            <div class="text-center text-muted">{{ T::translate('No image available', 'Walang available na larawan') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ T::translate('Evaluations & Recommendations', 'Ebalwasyon at Rekomendasyon')}}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">                            
                            @if($weeklyCareplan->evaluation_recommendations)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold">{{ T::translate('Evaluation & Recommendations', 'Ebalwasyon at Rekomendasyon') }}</span>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Evaluation Toggle">
                                        <button type="button" class="btn btn-outline-primary active" id="btn-evaluation-original">Original</button>
                                        <button type="button" class="btn btn-outline-primary" id="btn-evaluation-summary">Summary</button>
                                    </div>
                                </div>
                                <div class="border p-3 rounded bg-light">
                                    <span id="evaluation-original">
                                        {!! nl2br(e($weeklyCareplan->evaluation_recommendations ?? T::translate('No evaluation recorded', 'Walang ebalwasyon na naitala'))) !!}
                                    </span>
                                    <span id="evaluation-summary" style="display:none;">
                                        {!! nl2br(e($weeklyCareplan->evaluation_summary_final ?? T::translate('No summary available', 'Walang buod na available'))) !!}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ T::translate('Care Needs & Interventions', 'Pangangailangan sa Pangangalaga at Interbensyon')}}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">{{ T::translate('Care Needs', 'Pangangailangan sa Pangangalaga')}}</th>
                                    <th>{{ T::translate('Interventions Implemented', 'Isinagawang Interbensyon')}}</th>
                                    <th width="15%">{{ T::translate('Duration', 'Oras')}} (min)</th>
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
                                                    @if(!empty($useTagalog) && $useTagalog && isset($tagalogInterventions[$intervention->intervention_id]) && !empty($tagalogInterventions[$intervention->intervention_id]->t_intervention_description))
                                                        {{ $tagalogInterventions[$intervention->intervention_id]->t_intervention_description }}
                                                    @elseif($intervention->intervention && $intervention->intervention->intervention_description)
                                                        {{ $intervention->intervention->intervention_description }}
                                                    @else
                                                        {{ T::translate('Unnamed Intervention', 'Interbensyon na Walang Pangalan') }}
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
                                                    {{ $custom->intervention_description ?? T::translate('Unnamed Intervention', 'Interbensyon na Walang Pangalan') }}
                                                    <span class="badge bg-info">{{ T::translate('Custom', 'Pasadyang')}}</span>
                                                </td>
                                                <td class="text-center">{{ number_format($custom->duration_minutes, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach

                                @if(count($displayedCategories) === 0)
                                    <tr>
                                        <td colspan="3" class="text-center py-3">{{ T::translate('No interventions recorded for this care plan.', 'Walang interbensyon ang naitala para sa care plan na ito.')}}</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2" class="text-end">{{ T::translate('Total Duration', 'Kabuuang Oras')}}:</th>
                                    <th>{{ number_format($totalMinutes, 2) }} min</th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="text-end">{{ T::translate('Acknowledgement Status', 'Status ng Pagkilala')}}:</th>
                                    <th>
                                        @if($weeklyCareplan->acknowledged_by_beneficiary)
                                            <span class="badge bg-success">
                                                {{ T::translate('Acknowledged by', 'Kinilala ni')}} {{ Auth::guard('beneficiary')->check() ? T::translate('You', 'Ikaw') : T::translate('Beneficiary', 'Benepisyaryo') }}
                                            </span>
                                        @elseif($weeklyCareplan->acknowledged_by_family)
                                            <span class="badge bg-success">
                                                {{ T::translate('Acknowledged by', 'Kinilala ni')}} {{ Auth::guard('family')->check() ? T::translate('You', 'Ikaw') : T::translate('Family', 'Pamilya') }}
                                            </span>
                                        @elseif($weeklyCareplan->acknowledgement_signature)
                                            <span class="badge bg-success">{{ T::translate('Acknowledged', 'Kinilala')}}</span>
                                        @else
                                            <span class="badge bg-warning">{{ T::translate('Pending Acknowledgement', 'Nakabinbing Pagkilala')}}</span>
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
                        <h5 class="mb-0">{{ T::translate('Acknowledgement Details', 'Detalye ng Pagkilala')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>{{ T::translate('Acknowledged By', 'Kinilala ni')}}:</strong> {{ $signatureData['name'] ?? T::translate('Unknown', 'Hindi kilala') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>{{ T::translate('Role', 'Papel')}}:</strong> {{ $signatureData['acknowledged_by'] ?? T::translate('Unknown', 'Hindi kilala') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>{{ T::translate('Date', 'Petsa')}}:</strong> {{ isset($signatureData['date']) ? \Carbon\Carbon::parse($signatureData['date'])->format('M d, Y g:i A') : T::translate('Unknown', 'Hindi kilala') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Assessment toggle
            document.getElementById('btn-assessment-original').onclick = function() {
                document.getElementById('assessment-original').style.display = '';
                document.getElementById('assessment-summary').style.display = 'none';
                this.classList.add('active');
                document.getElementById('btn-assessment-summary').classList.remove('active');
            };
            document.getElementById('btn-assessment-summary').onclick = function() {
                document.getElementById('assessment-original').style.display = 'none';
                document.getElementById('assessment-summary').style.display = '';
                this.classList.add('active');
                document.getElementById('btn-assessment-original').classList.remove('active');
            };

            // Evaluation toggle
            document.getElementById('btn-evaluation-original').onclick = function() {
                document.getElementById('evaluation-original').style.display = '';
                document.getElementById('evaluation-summary').style.display = 'none';
                this.classList.add('active');
                document.getElementById('btn-evaluation-summary').classList.remove('active');
            };
            document.getElementById('btn-evaluation-summary').onclick = function() {
                document.getElementById('evaluation-original').style.display = 'none';
                document.getElementById('evaluation-summary').style.display = '';
                this.classList.add('active');
                document.getElementById('btn-evaluation-original').classList.remove('active');
            };
        });
    </script>
</body>
</html>