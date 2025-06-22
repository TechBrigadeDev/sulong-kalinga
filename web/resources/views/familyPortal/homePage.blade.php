<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Portal - Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalHomePage.css') }}">
</head>
<body>
    @include('components.familyPortalNavbar')
    @include('components.familyPortalSidebar')

    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    <!-- Welcome Back Modal -->
    <div class="modal fade" id="welcomeBackModal" tabindex="-1" aria-labelledby="welcomeBackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="welcomeBackModalLabel">{{ T::translate('Welcome to Family Portal', 'Welcome sa Family Portal')}}!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class='bi bi-people-fill display-4 text-primary mb-3'></i>
                    <h4>Hello, {{ $familyMember->first_name }}!</h4>
                    <p>{{ T::translate('Welcome to your Family Access Portal where you can monitor care services', 'Welcome sa iyong Family Access Portal kung saan maaring mong ma-monitor ang mga serbisyo sa pangangalaga.')}}.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Get Started</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="container-fluid">
            <div class="row p-3" id="home-content">
                <!-- Combined Welcome and Alert Banners -->
                <div class="banner-grid">
                    <div class="welcome-banner">
                        <h1 class="welcome-title">
                            {{ T::translate('Welcome, ' . $familyMember->first_name . '!', 'Maligayang pagdating, ' . $familyMember->first_name . '!') }}
                        </h1
                        <p class="welcome-subtitle">{{ T::translate('You\'re viewing the profile of', 'Ikaw ay tumitingin sa Profile ni')}} {{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</p>
                        <p>Last care worker visit: {{ \Carbon\Carbon::now()->subDays(3)->format('F d, Y') }}</p>
                    </div>
                    
                    <div class="alert-banner">
                        <span class="alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                        <div class="alert-content">
                            @if ($nextVisit)
                                <strong>{{ T::translate('Upcoming Visit', 'Nalalapit na Pagbisita')}}:</strong> {{ T::translate('Care worker', 'Tagapag-alaga')}} {{ $nextVisit['care_worker'] }} scheduled for {{ $nextVisit['date'] }} at {{ $nextVisit['time'] }}
                            @else
                                <strong>{{ T::translate('No upcoming visits', 'Walang nalalapit na pagbisita')}}</strong> {{ T::translate('currently scheduled', 'ang kasalukuyang naka-iskedyul')}}.
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-calendar-check me-2"></i>
                                {{ T::translate('Visitation Schedule', 'Iskedyul ng Pagbisita')}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                @if ($nextVisit)
                                    {{ T::translate('Next visit scheduled for', 'Ang susunod na pagbisita ay naka-iskedyul sa')}} {{ $nextVisit['date'] }} {{ T::translate('at', 'sa oras na')}} {{ $nextVisit['time'] }}
                                    <div class="mt-1 text-muted">
                                        <small>{{ T::translate('Care Worker', 'Tagapag-alaga')}}: {{ $nextVisit['care_worker'] }}</small>
                                    </div>
                                    <div class="mt-1 text-muted">
                                        <small>{{ T::translate('Visit Type', 'Uri ng Pagbisita')}}: {{ $nextVisit['visit_type'] }}</small>
                                    </div>
                                @else
                                    {{ T::translate('No upcoming visits scheduled at this time.', 'Walang nalalapit na pagbisita ang naka-iskedyul sa ngayon.')}}
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('family.visitation.schedule.index') }}" class="card-link">
                                <span>{{ T::translate('View Full Schedule', 'Tingnan ang Buong Iskedyul')}}</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-capsule-pill me-2"></i>
                                {{ T::translate('Medications', 'Mga Gamot')}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                @if ($nextMedication)
                                    <div>
                                        {{ T::translate('Next medication', 'Susunod na gamot')}}: {{ $nextMedication['name'] }} {{ T::translate('at', 'sa oras na')}} {{ $nextMedication['time'] }} 
                                        @if($nextMedication['day'] == 'tomorrow')
                                            tomorrow
                                        @endif
                                    </div>
                                    <div class="mt-1 text-muted">
                                        <small>{{ T::translate('Dosage', 'Dosis')}}: {{ $nextMedication['dosage'] }}</small>
                                    </div>
                                    @if($nextMedication['with_food'])
                                    <div class="mt-1 text-muted">
                                        <small><i class="bi bi-egg-fried"></i> {{ T::translate('Take with food', 'May Pagkain')}}</small>
                                    </div>
                                    @endif
                                @else
                                    {{ T::translate('No upcoming medications scheduled at this time.', 'Walang nalalapit na ikedyul ng gamot sa ngayon.')}}
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('family.medication.schedule.index') }}" class="card-link">
                                <span>{{ T::translate('View All Medications', 'Tingnan Lahat ng mga Gamot')}}</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card emergency-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-exclamation-octagon me-2"></i>
                                {{ T::translate('Emergency Contact', 'Contact sa Emergency')}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                {{ T::translate('In case of emergency, you may send an emergency notice to COSE here', 'Sakaling may emergency, maaari kang magpadala ng emergency notice sa COSE dito')}}
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('family.emergency.service.index') }}" class="card-link">
                                <span>Emergency Notice</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-chat-dots me-2"></i>
                                {{ T::translate('Messages', 'Mensahe')}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                @if ($unreadMessageCount > 0)
                                    <div>
                                        {{ T::translate('You have', 'Ikaw ay meron')}} <span class="unread-message-count">{{ $unreadMessageCount }}</span> {{ T::translate('unread', 'di-nabasang mensahe')}} 
                                        {{ Str::plural('message', $unreadMessageCount) }} {{ T::translate('from COSE staff', 'galing sa staff ng COSE')}}
                                    </div>
                                    <div class="mt-2 small text-muted">
                                        {{ T::translate('Click below to view your conversations', 'Mag-click sa baba upang makita ang iyong mga pag-uusap')}}
                                    </div>
                                @else
                                    <div>
                                        {{ T::translate('No unread messages at this time', 'Walang di-nabasang mga mensahe sa ngayon')}}
                                    </div>
                                    <div class="mt-2 small text-muted">
                                        {{ T::translate('You can start a conversation with your care worker', 'Ikaw ay maaaring magsimula ng pag-uusap kasama ang iyong tagapag-alaga')}}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('family.messaging.index') }}" class="card-link">
                                <span>{{ T::translate('View Messages', 'Tingnan ang mga Mensahe')}}</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-clipboard2-check me-2"></i>
                                {{ T::translate('My Care Plan', 'Aking Care Plan')}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                {{ T::translate('View your personalized care plan and weekly goals', 'Tingnan ang iyong personalized na care plan at lingguhang goals')}}
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('family.care.plan.index') }}" class="card-link">
                                <span>{{ T::translate('View Care Plan', 'Tingnan ang Care Plan')}}</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-person-circle me-2"></i>
                                {{ T::translate('My Profile', 'Aking Profile')}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                {{ T::translate('Update your personal information and preferences', 'I-Update ang iyong personal na impormasyon at mga kagustuhan')}}
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('family.profile.index') }}" class="card-link">
                                <span>{{ T::translate('View Profile', 'Tingnan ang Profile')}}</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
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
            @if($showWelcome)
                var welcomeModal = new bootstrap.Modal(document.getElementById('welcomeBackModal'));
                welcomeModal.show();
            @endif
        });
    </script>
</body>
</html>