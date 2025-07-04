<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Medication Schedule | Beneficiary Portal</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalMedicationSchedule.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.beneficiaryPortalNavbar')
    @include('components.beneficiaryPortalSidebar')

    <div class="home-section">
        <div class="text-left">{{ T::translate('MEDICATION SCHEDULE', 'ISKEDYUL NG GAMOT')}}</div>
        <div class="container-fluid">
            <div class="row p-3" id="home-content">
                <div class="col-12">
                    <!-- Beneficiary Information -->
                    <div class="beneficiary-info">
                        <div class="beneficiary-avatar">
                            @if($beneficiary['photo'])
                                <img src="{{ asset('storage/' . $beneficiary['photo']) }}" alt="{{ $beneficiary['name'] }}" class="rounded-circle">
                            @else
                                <i class="bi bi-person-circle"></i>
                            @endif
                        </div>
                        <div class="beneficiary-details">
                            <h3>{{ $beneficiary['name'] }}</h3>
                            <p>{{ $beneficiary['age'] }} {{ T::translate('years old', 'taong gulang')}} | {{ $beneficiary['gender'] }}</p>
                        </div>
                    </div>

                    <p class="last-updated">{{ T::translate('Last updated', 'Huling na-update')}}: {{ $lastUpdated['date'] }} at {{ $lastUpdated['time'] }}</p>
                    
                    <!-- Health Status Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card health-card">
                                <div class="health-card-header">
                                    <span><i class="bi bi-heart-pulse me-2"></i> {{ T::translate('Medical Conditions', 'Mga Medikal na Kondisyon')}}</span>
                                </div>
                                <div class="health-card-body">
                                    @forelse($healthConditions as $condition)
                                        <div class="condition-item">
                                            <div class="condition-name">{{ $condition['name'] }}</div>
                                        </div>
                                    @empty
                                        <div class="condition-item">
                                            <div class="condition-name">{{ T::translate('No Medical Conditions', 'Walang mga medikal na kondisyon')}}</div>
                                            <div class="condition-details">{{ T::translate('No chronic medical conditions on record', 'Walang chronic na medikal na kondisyon sa tala')}}</div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card health-card">
                                <div class="health-card-header">
                                    <span><i class="bi bi-shield-check me-2"></i> {{ T::translate('Immunizations', 'Mga Bakuna')}}</span>
                                </div>
                                <div class="health-card-body">
                                    @forelse($immunizations as $immunization)
                                        <div class="condition-item">
                                            <div class="condition-name">{{ $immunization['name'] }}</div>
                                        </div>
                                    @empty
                                        <div class="condition-item">
                                            <div class="condition-name">{{ T::translate('No Immunization Records', 'Walang Tala ng mga Bakuna')}}</div>
                                            <div class="condition-details">{{ T::translate('No immunization records currently available', 'Walang tala ng mga bakuna ang available sa kasalukuyan')}}</div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Medication Schedule -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4 class="mb-4" style="color: var(--text-dark); font-weight: 600;">{{ T::translate('Current Medications', 'Kasalukuyang Gamot')}}</h4>
                        </div>
                        
                        <!-- Active Medications Card -->
                        <div class="col-12">
                            <div class="card medication-card">
                                <div class="medication-header">
                                    <div class="medication-patient">{{ T::translate('Active Medications', 'Akitbong mga Gamot')}}</div>
                                </div>
                                <div class="medication-body">
                                    @forelse($activeMedications as $medication)
                                        <div class="medication-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="medication-name">{{ $medication['name'] }} ({{ $medication['dosage'] }})</div>
                                                    <div class="text-muted small">{{ $medication['frequency'] }}</div>
                                                </div>
                                                <span class="badge-time"><i class="bi bi-clock"></i> {{ $medication['times_string'] }}</span>
                                            </div>
                                            <div class="medication-details">
                                                <div class="medication-detail">
                                                    <i class="bi bi-calendar"></i> {{ T::translate('Started', 'Nagsimula')}}: {{ $medication['start_date'] }}
                                                </div>
                                                <!-- Add enhanced dosage timing display -->
                                                <div class="medication-detail timing-detail">
                                                    <i class="bi bi-alarm"></i> {{ T::translate('Dosage times', 'Oras ng Dosis')}}:
                                                    <div class="timing-badges">
                                                        @foreach($medication['dosage_times'] as $time)
                                                            <span class="timing-badge {{ $time['with_food'] ? 'with-food' : 'without-food' }}">
                                                                {{ $time['time'] }}
                                                                @if($time['with_food'])
                                                                    <i class="bi bi-egg-fried" title="Take with food">{{ T::translate('Take with food', 'May Pagkain')}}</i>
                                                                @else
                                                                    <i class="bi bi-cup" title="Take on empty stomach"></i>
                                                                @endif
                                                            </span>
                                                        @endforeach
                                                        @if($medication['as_needed'])
                                                            <span class="timing-badge as-needed">
                                                                <i class="bi bi-stopwatch"></i> {{ T::translate('Take as needed', 'Inumin kung kinakailangaan')}}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="medication-detail">
                                                    <i class="bi bi-droplet"></i> {{ $medication['administration'] }}
                                                </div>
                                                <div class="medication-detail">
                                                    <i class="bi bi-prescription"></i> {{ T::translate('Recorded by', 'Naitala ni')}} {{ $medication['recorded_by'] }}
                                                </div>
                                                @if($medication['special_instructions'])
                                                    <div class="medication-detail">
                                                        <i class="bi bi-info-circle"></i> {{ $medication['special_instructions'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 text-center text-muted">
                                            {{ T::translate('No active medications scheduled at this time.', 'Walang akitbong iksedyul ng gamot sa ngayon')}}
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        
                        <!-- PRN (As Needed) Medications Card -->
                        <div class="col-12 mt-4">
                            <div class="card medication-card">
                                <div class="medication-header">
                                    <div class="medication-patient">{{ T::translate('PRN (As Needed) Medications', 'PRN (Kung Kinakailangan) mga Gamot')}}</div>
                                </div>
                                <div class="medication-body">
                                    @forelse($prnMedications as $medication)
                                        <div class="medication-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="medication-name">{{ $medication['name'] }} ({{ $medication['dosage'] }})</div>
                                                    <div class="text-muted small">{{ $medication['frequency'] }}</div>
                                                </div>
                                                <span class="badge-time"><i class="bi bi-clock"></i> {{ $medication['times_string'] }}</span>
                                            </div>
                                            <div class="medication-details">
                                                <div class="medication-detail">
                                                    <i class="bi bi-calendar"></i> {{ T::translate('Started', 'Nagsimula')}}: {{ $medication['start_date'] }}
                                                </div>
                                                <div class="medication-detail">
                                                    <i class="bi bi-capsule"></i> {{ $medication['remaining'] }}
                                                </div>
                                                <div class="medication-detail">
                                                    <i class="bi bi-droplet"></i> {{ $medication['administration'] }}
                                                </div>
                                                <div class="medication-detail">
                                                    <i class="bi bi-prescription"></i> {{ T::translate('Recorded by', 'Naitala ni')}} {{ $medication['recorded_by'] }}
                                                </div>
                                                @if($medication['special_instructions'])
                                                    <div class="medication-detail">
                                                        <i class="bi bi-info-circle"></i> {{ $medication['special_instructions'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="p-4 text-center text-muted">
                                            {{ T::translate('No PRN (as needed) medications scheduled at this time.', 'Walang (PRN) na mga gamot ang naka-iskedyul sa ngayon')}}
                                        </div>
                                    @endforelse
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
            // Add click event for medication items
            const medicationItems = document.querySelectorAll('.medication-item');
            medicationItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Toggle expanded class
                    this.classList.toggle('expanded');
                });
            });
        });
    </script>
</body>
</html>