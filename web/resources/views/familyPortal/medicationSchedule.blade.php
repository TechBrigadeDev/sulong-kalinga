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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
                            @if($beneficiary['photo'])
                                <img src="{{ asset('storage/' . $beneficiary['photo']) }}" alt="{{ $beneficiary['name'] }}" class="rounded-circle">
                            @else
                                <i class="bi bi-person-circle"></i>
                            @endif
                        </div>
                        <div class="beneficiary-details">
                            <h3>{{ $beneficiary['name'] }}</h3>
                            <p>{{ $beneficiary['age'] }} years old | {{ $beneficiary['gender'] }}</p>
                        </div>
                    </div>

                    <p class="last-updated">Last updated: {{ $lastUpdated['date'] }} at {{ $lastUpdated['time'] }}</p>
                    
                    <!-- Health Status Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card health-card">
                                <div class="health-card-header">
                                    <span><i class="bi bi-heart-pulse me-2"></i> Medical Conditions</span>
                                </div>
                                <div class="health-card-body">
                                    @forelse($healthConditions as $condition)
                                        <div class="condition-item">
                                            <div class="condition-name">{{ $condition['name'] }}</div>
                                        </div>
                                    @empty
                                        <div class="condition-item">
                                            <div class="condition-name">No Medical Conditions</div>
                                            <div class="condition-details">No chronic medical conditions on record</div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card health-card">
                                <div class="health-card-header">
                                    <span><i class="bi bi-shield-check me-2"></i> Immunizations</span>
                                </div>
                                <div class="health-card-body">
                                    @forelse($immunizations as $immunization)
                                        <div class="condition-item">
                                            <div class="condition-name">{{ $immunization['name'] }}</div>
                                        </div>
                                    @empty
                                        <div class="condition-item">
                                            <div class="condition-name">No Immunization Records</div>
                                            <div class="condition-details">No immunization records currently available</div>
                                        </div>
                                    @endforelse
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
                                                    <i class="bi bi-calendar"></i> Started: {{ $medication['start_date'] }}
                                                </div>
                                                <!-- Add enhanced dosage timing display -->
                                                <div class="medication-detail timing-detail">
                                                    <i class="bi bi-alarm"></i> Dosage times:
                                                    <div class="timing-badges">
                                                        @foreach($medication['dosage_times'] as $time)
                                                            <span class="timing-badge {{ $time['with_food'] ? 'with-food' : 'without-food' }}">
                                                                {{ $time['time'] }}
                                                                @if($time['with_food'])
                                                                    <i class="bi bi-egg-fried" title="Take with food">Take with food</i>
                                                                @else
                                                                    <i class="bi bi-cup" title="Take on empty stomach"></i>
                                                                @endif
                                                            </span>
                                                        @endforeach
                                                        @if($medication['as_needed'])
                                                            <span class="timing-badge as-needed">
                                                                <i class="bi bi-stopwatch"></i> Take as needed
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="medication-detail">
                                                    <i class="bi bi-droplet"></i> {{ $medication['administration'] }}
                                                </div>
                                                <div class="medication-detail">
                                                    <i class="bi bi-prescription"></i> Recorded by {{ $medication['recorded_by'] }}
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
                                            No active medications scheduled at this time.
                                        </div>
                                    @endforelse
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
                                                    <i class="bi bi-calendar"></i> Started: {{ $medication['start_date'] }}
                                                </div>
                                                <div class="medication-detail">
                                                    <i class="bi bi-capsule"></i> {{ $medication['remaining'] }}
                                                </div>
                                                <div class="medication-detail">
                                                    <i class="bi bi-droplet"></i> {{ $medication['administration'] }}
                                                </div>
                                                <div class="medication-detail">
                                                    <i class="bi bi-prescription"></i> Recorded by {{ $medication['recorded_by'] }}
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
                                            No PRN (as needed) medications scheduled at this time.
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