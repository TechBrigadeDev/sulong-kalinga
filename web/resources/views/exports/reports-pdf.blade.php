<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Care Plans Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        h1 {
            color: #2b5797;
            font-size: 24px;
            margin-bottom: 5px;
        }
        h2 {
            color: #2b5797;
            font-size: 18px;
            margin: 15px 0 5px 0;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
            page-break-before: auto; /* This prevents unwanted page breaks before headings */
            page-break-after: avoid; /* This keeps the heading with its content */
        }
        .section-break {
            margin-top: 30px; /* Replaces the mt-4 Bootstrap class */
        }
        h3 {
            color: #2b5797;
            font-size: 16px;
            margin: 15px 0 5px 0;
        }
        .export-date {
            color: #666;
            margin-bottom: 15px;
        }
        .plan-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .section-title {
            background-color: #2b5797;
            color: white;
            padding: 5px 10px;
            font-size: 14px;
            margin: 15px 0 10px 0;
        }
        .vital-signs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .vital-sign {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            width: 20%;
            margin-bottom: 10px;
        }
        .vital-value {
            font-size: 16px;
            font-weight: bold;
            color: #2b5797;
        }
        .vital-label {
            font-size: 10px;
            color: #666;
        }
        .interventions-table {
            margin-top: 15px;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .toc-header {
            background-color: #2b5797;
            color: white;
            padding: 8px 10px;
            font-size: 16px;
            margin: 15px 0 10px 0;
        }
        .profile-header {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .profile-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 1px solid #ddd;
            margin-right: 15px;
            object-fit: cover;
        }
        .profile-details {
            flex: 1;
        }
        .column {
            width: 49%;
            display: inline-block;
            vertical-align: top;
        }
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
        }
        .status-active {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .status-inactive {
            background: #ffebee;
            color: #c62828;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Care Plans Report</h1>
        <div class="export-date">Report Date: {{ $exportDate }}</div>
    </div>
    
    <!-- Table of Contents -->
    <div class="toc-header">Contents</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="40%">Care Plan Details</th>
                <th width="25%">Beneficiary</th>
                <th width="20%">Care Worker</th>
                <th width="10%">Date</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp
            
            @foreach($weeklyCarePlans as $plan)
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td>Weekly Care Plan #{{ $plan->weekly_care_plan_id }}</td>
                    <td>{{ $plan->beneficiary->first_name ?? 'Unknown' }} {{ $plan->beneficiary->last_name ?? '' }}</td>
                    <td>{{ $plan->careWorker->first_name ?? 'Unknown' }} {{ $plan->careWorker->last_name ?? '' }}</td>
                    <td>{{ \Carbon\Carbon::parse($plan->date)->format('M d, Y') }}</td>
                </tr>
            @endforeach
            
            @foreach($beneficiaryData as $data)
                <tr>
                    <td>{{ $counter++ }}</td>
                    <td>General Care Plan #{{ $data['beneficiary']->general_care_plan_id }}</td>
                    <td>{{ $data['beneficiary']->first_name }} {{ $data['beneficiary']->last_name }}</td>
                    <td>{{ $data['careWorker']->first_name ?? 'Unknown' }} {{ $data['careWorker']->last_name ?? '' }}</td>
                    <td>{{ \Carbon\Carbon::parse($data['beneficiary']->generalCarePlan->created_at)->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Weekly Care Plans Section -->
    @if(count($weeklyCarePlans) > 0)
        <div class="section-break"></div>
        <h2>Weekly Care Plans</h2>
        
        @foreach($weeklyCarePlans as $plan)
            <div class="plan-section">
                <h3>Weekly Care Plan for {{ $plan->beneficiary->first_name ?? 'Unknown' }} {{ $plan->beneficiary->last_name ?? '' }}</h3>
                <p>
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($plan->date)->format('F j, Y') }}<br>
                    <strong>Care Worker:</strong> {{ $plan->careWorker->first_name ?? 'Unknown' }} {{ $plan->careWorker->last_name ?? '' }}
                </p>
                
                <!-- Vital Signs Section -->
                <div class="section-title">Vital Signs</div>
                <div class="vital-signs">
                    <div class="vital-sign">
                        <div class="vital-value">{{ $plan->vitalSigns->blood_pressure }}</div>
                        <div class="vital-label">Blood Pressure</div>
                    </div>
                    <div class="vital-sign">
                        <div class="vital-value">{{ $plan->vitalSigns->body_temperature }} °C</div>
                        <div class="vital-label">Temperature</div>
                    </div>
                    <div class="vital-sign">
                        <div class="vital-value">{{ $plan->vitalSigns->pulse_rate }} bpm</div>
                        <div class="vital-label">Pulse Rate</div>
                    </div>
                    <div class="vital-sign">
                        <div class="vital-value">{{ $plan->vitalSigns->respiratory_rate }} bpm</div>
                        <div class="vital-label">Respiratory Rate</div>
                    </div>
                </div>
                
                <!-- Illnesses Section -->
                @if($plan->illnesses)
                    <div class="section-title">Current Illnesses</div>
                    @php
                        // Handle illnesses stored as JSON string or array
                        $illnesses = is_string($plan->illnesses) ? json_decode($plan->illnesses, true) : $plan->illnesses;
                        $formattedIllnesses = is_array($illnesses) ? implode(', ', $illnesses) : $plan->illnesses;
                    @endphp
                    <p>{{ $formattedIllnesses }}</p>
                @endif
                
                <!-- Assessment Section -->
                <div class="section-title">Assessment</div>
                <p>{{ $plan->assessment }}</p>
                
                <!-- Interventions Section -->
                <div class="section-title">Interventions</div>

                @php
                    // Direct database query approach like viewWeeklyCareplan uses
                    $standardInterventions = DB::table('weekly_care_plan_interventions as wpi')
                        ->join('interventions as i', 'i.intervention_id', '=', 'wpi.intervention_id')
                        ->join('care_categories as cc', 'cc.care_category_id', '=', 'i.care_category_id')
                        ->where('wpi.weekly_care_plan_id', $plan->weekly_care_plan_id)
                        ->whereNotNull('wpi.intervention_id')
                        ->select(
                            'cc.care_category_id',
                            'cc.care_category_name',
                            'i.intervention_description',
                            'wpi.duration_minutes'
                        )
                        ->get();
                        
                    $interventionsByCategory = $standardInterventions->groupBy('care_category_id');
                    
                    // Get custom interventions
                    $customInterventions = DB::table('weekly_care_plan_interventions as wpi')
                        ->join('care_categories as cc', 'cc.care_category_id', '=', 'wpi.care_category_id')
                        ->where('wpi.weekly_care_plan_id', $plan->weekly_care_plan_id)
                        ->whereNull('wpi.intervention_id')
                        ->select(
                            'wpi.intervention_description as custom_intervention_description',
                            'cc.care_category_name',
                            'wpi.duration_minutes'
                        )
                        ->get();
                @endphp

                <!-- Standard Interventions Section -->
                @foreach($interventionsByCategory as $catId => $interventions)
                    @php
                        $category = $careCategories->where('care_category_id', $catId)->first();
                        $categoryName = $category ? $category->care_category_name : 'Uncategorized';
                    @endphp
                    
                    <h4>{{ $categoryName }}</h4>
                    <table class="interventions-table">
                        <thead>
                            <tr>
                                <th>Intervention</th>
                                <th width="20%">Duration (minutes)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($interventions as $intervention)
                                <tr>
                                    <td>{{ $intervention->intervention_description }}</td>
                                    <td>{{ number_format($intervention->duration_minutes, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach

                <!-- Custom Interventions Section -->
                @if(count($customInterventions) > 0)
                    <h4>Custom Interventions</h4>
                    <table class="interventions-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th width="20%">Category</th>
                                <th width="20%">Duration (minutes)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customInterventions as $intervention)
                                <tr>
                                    <td>{{ $intervention->custom_intervention_description }}</td>
                                    <td>{{ $intervention->care_category_name }}</td>
                                    <td>{{ number_format($intervention->duration_minutes, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                
                <!-- Evaluation and Recommendations -->
                <div class="section-title">Evaluation and Recommendations</div>
                <p>{{ $plan->evaluation_recommendations }}</p>
            </div>
            
            @unless($loop->last)
                <div class="page-break"></div>
            @endunless
        @endforeach
    @endif
    
    @if(count($beneficiaryData) > 0)
        <div class="section-break"></div>
        <h2>General Care Plans</h2>
        
        @foreach($beneficiaryData as $data)
            @php 
                $beneficiary = $data['beneficiary'];
                $careNeeds1 = $data['careNeeds1'];
                $careNeeds2 = $data['careNeeds2'];
                $careNeeds3 = $data['careNeeds3'];
                $careNeeds4 = $data['careNeeds4'];
                $careNeeds5 = $data['careNeeds5'];
                $careNeeds6 = $data['careNeeds6'];
                $careNeeds7 = $data['careNeeds7'];
                $careWorker = $data['careWorker'];
            @endphp
            
            <div class="plan-section">
                <div class="profile-header">
                    <!-- Add Profile Image -->
                    <img class="profile-image" src="{{ $beneficiary->photo ? public_path('storage/' . $beneficiary->photo) : public_path('images/defaultProfile.png') }}" alt="Profile Picture">
                    
                    <div class="profile-details">
                        <h3>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h3>
                        <div class="registration-date">A Beneficiary since {{ \Carbon\Carbon::parse($beneficiary->created_at)->format('F j, Y') }}</div>
                        
                        <div class="status {{ $beneficiary->status->status_name == 'Active' ? 'status-active' : 'status-inactive' }}">
                            {{ $beneficiary->status->status_name }} Beneficiary
                        </div>
                    </div>
                </div>
                
                @if($includeBeneficiaryDetails)
                    <div class="section-title">Personal Information</div>
                    <div class="column">
                        <table>
                            <tbody>
                                <tr>
                                    <td width="30%"><strong>Age:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($beneficiary->birthday)->age }} years old</td>
                                </tr>
                                <tr>
                                    <td><strong>Birthday:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($beneficiary->birthday)->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Gender:</strong></td>
                                    <td>{{ $beneficiary->gender }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Civil Status:</strong></td>
                                    <td>{{ $beneficiary->civil_status }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile Number:</strong></td>
                                    <td>{{ $beneficiary->mobile }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Landline Number:</strong></td>
                                    <td>{{ $beneficiary->landline ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Current Address:</strong></td>
                                    <td>{{ $beneficiary->street_address }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Primary Caregiver:</strong></td>
                                    <td>{{ $beneficiary->primary_caregiver ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="column">
                        <table>
                            <thead>
                                <tr>
                                    <th colspan="2">Medical History</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- For Medical Conditions -->
                                <tr>
                                    <td width="30%"><strong>Medical Conditions:</strong></td>
                                    <td>
                                        @php
                                            $medicalConditions = is_string($beneficiary->generalCarePlan->healthHistory->medical_conditions) 
                                                ? json_decode($beneficiary->generalCarePlan->healthHistory->medical_conditions, true) 
                                                : $beneficiary->generalCarePlan->healthHistory->medical_conditions;
                                                
                                            echo is_array($medicalConditions) ? implode(', ', $medicalConditions) : ($medicalConditions ?? 'N/A');
                                        @endphp
                                    </td>
                                </tr>

                                <!-- For Medications -->
                                <tr>
                                    <td><strong>Medications:</strong></td>
                                    <td>
                                        @php
                                            $medications = is_string($beneficiary->generalCarePlan->healthHistory->medications) 
                                                ? json_decode($beneficiary->generalCarePlan->healthHistory->medications, true) 
                                                : $beneficiary->generalCarePlan->healthHistory->medications;
                                                
                                            echo is_array($medications) ? implode(', ', $medications) : ($medications ?? 'N/A');
                                        @endphp
                                    </td>
                                </tr>

                                <!-- For Allergies -->
                                <tr>
                                    <td><strong>Allergies:</strong></td>
                                    <td>
                                        @php
                                            $allergies = is_string($beneficiary->generalCarePlan->healthHistory->allergies) 
                                                ? json_decode($beneficiary->generalCarePlan->healthHistory->allergies, true) 
                                                : $beneficiary->generalCarePlan->healthHistory->allergies;
                                                
                                            echo is_array($allergies) ? implode(', ', $allergies) : ($allergies ?? 'N/A');
                                        @endphp
                                    </td>
                                </tr>

                                <!-- For Immunizations -->
                                <tr>
                                    <td><strong>Immunizations:</strong></td>
                                    <td>
                                        @php
                                            $immunizations = is_string($beneficiary->generalCarePlan->healthHistory->immunizations) 
                                                ? json_decode($beneficiary->generalCarePlan->healthHistory->immunizations, true) 
                                                : $beneficiary->generalCarePlan->healthHistory->immunizations;
                                                
                                            echo is_array($immunizations) ? implode(', ', $immunizations) : ($immunizations ?? 'N/A');
                                        @endphp
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td>{{ $beneficiary->category->care_category_name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="section-title">Emergency Details</div>
                    <table>
                        <tbody>
                            <tr>
                                <td width="30%"><strong>Emergency Contact:</strong></td>
                                <td>{{ $beneficiary->emergency_contact_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Relation:</strong></td>
                                <td>{{ $beneficiary->emergency_contact_relation ?? 'Not Specified' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Mobile Number:</strong></td>
                                <td>{{ $beneficiary->emergency_contact_mobile }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email Address:</strong></td>
                                <td>{{ $beneficiary->emergency_email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Emergency Procedure:</strong></td>
                                <td>{{ $beneficiary->emergency_procedure }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endif
                
                <div class="section-title">Medication Management</div>
                <table>
                    <thead>
                        <tr>
                            <th width="30%">Medication Name</th>
                            <th width="20%">Dosage</th>
                            <th width="20%">Frequency</th>
                            <th width="30%">Instructions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->medications && $beneficiary->generalCarePlan->medications->count() > 0)
                            @foreach ($beneficiary->generalCarePlan->medications as $medication)
                                <tr>
                                    <td>{{ $medication->medication }}</td>
                                    <td>{{ $medication->dosage }}</td>
                                    <td>{{ $medication->frequency }}</td>
                                    <td>{{ $medication->administration_instructions }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">No medications recorded.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="section-title">Care Needs</div>
                <table>
                    <thead>
                        <tr>
                            <th width="30%">Category</th>
                            <th width="20%">Frequency</th>
                            <th width="50%">Assistance Required</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $firstRow = true; @endphp
                        @foreach ($careNeeds1 as $careNeed)
                        <tr>
                            @if ($firstRow)
                                <td style="width:30%;"><strong>Mobility</strong></td>
                                @php $firstRow = false; @endphp
                            @else
                                <td style="width:30%;"></td>
                            @endif
                            <td style="width:20%;">{{ $careNeed->frequency }}</td>
                            <td style="width:50%;">{{ $careNeed->assistance_required }}</td>
                        </tr>
                        @endforeach

                        @php $firstRow = true; @endphp
                        @foreach ($careNeeds2 as $careNeed)
                        <tr>
                            @if ($firstRow)
                                <td style="width:30%;"><strong>Cognitive / Communication</strong></td>
                                @php $firstRow = false; @endphp
                            @else
                                <td style="width:30%;"></td>
                            @endif
                            <td style="width:20%;">{{ $careNeed->frequency }}</td>
                            <td style="width:50%;">{{ $careNeed->assistance_required }}</td>
                        </tr>
                        @endforeach

                        @php $firstRow = true; @endphp
                        @foreach ($careNeeds3 as $careNeed)
                        <tr>
                            @if ($firstRow)
                                <td style="width:30%;"><strong>Self-sustainability</strong></td>
                                @php $firstRow = false; @endphp
                            @else
                                <td style="width:30%;"></td>
                            @endif
                            <td style="width:20%;">{{ $careNeed->frequency }}</td>
                            <td style="width:50%;">{{ $careNeed->assistance_required }}</td>
                        </tr>
                        @endforeach

                        @php $firstRow = true; @endphp
                        @foreach ($careNeeds4 as $careNeed)
                        <tr>
                            @if ($firstRow)
                                <td style="width:30%;"><strong>Disease / Therapy Handling</strong></td>
                                @php $firstRow = false; @endphp
                            @else
                                <td style="width:30%;"></td>
                            @endif
                            <td style="width:20%;">{{ $careNeed->frequency }}</td>
                            <td style="width:50%;">{{ $careNeed->assistance_required }}</td>
                        </tr>
                        @endforeach

                        @php $firstRow = true; @endphp
                        @foreach ($careNeeds5 as $careNeed)
                        <tr>
                            @if ($firstRow)
                                <td style="width:30%;"><strong>Daily Life / Social Contact</strong></td>
                                @php $firstRow = false; @endphp
                            @else
                                <td style="width:30%;"></td>
                            @endif
                            <td style="width:20%;">{{ $careNeed->frequency }}</td>
                            <td style="width:50%;">{{ $careNeed->assistance_required }}</td>
                        </tr>
                        @endforeach

                        @php $firstRow = true; @endphp
                        @foreach ($careNeeds6 as $careNeed)
                        <tr>
                            @if ($firstRow)
                                <td style="width:30%;"><strong>Outdoor Activities</strong></td>
                                @php $firstRow = false; @endphp
                            @else
                                <td style="width:30%;"></td>
                            @endif
                            <td style="width:20%;">{{ $careNeed->frequency }}</td>
                            <td style="width:50%;">{{ $careNeed->assistance_required }}</td>
                        </tr>
                        @endforeach

                        @php $firstRow = true; @endphp
                        @foreach ($careNeeds7 as $careNeed)
                        <tr>
                            @if ($firstRow)
                                <td style="width:30%;"><strong>Household Keeping</strong></td>
                                @php $firstRow = false; @endphp
                            @else
                                <td style="width:30%;"></td>
                            @endif
                            <td style="width:20%;">{{ $careNeed->frequency }}</td>
                            <td style="width:50%;">{{ $careNeed->assistance_required }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="section-title">Additional Health Information</div>
                <div class="column">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="2">Mobility</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="40%"><strong>Walking Ability:</strong></td>
                                <td>{{ $beneficiary->generalCarePlan->mobility->walking_ability ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Assistive Devices:</strong></td>
                                <td>{{ $beneficiary->generalCarePlan->mobility->assistive_devices ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Transportation Needs:</strong></td>
                                <td>{{ $beneficiary->generalCarePlan->mobility->transportation_needs ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="column">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="2">Cognitive Function</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="40%"><strong>Memory:</strong></td>
                                <td>{{ $beneficiary->generalCarePlan->cognitiveFunction->memory ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Thinking Skills:</strong></td>
                                <td>{{ $beneficiary->generalCarePlan->cognitiveFunction->thinking_skills ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Orientation:</strong></td>
                                <td>{{ $beneficiary->generalCarePlan->cognitiveFunction->orientation ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Behavior:</strong></td>
                                <td>{{ $beneficiary->generalCarePlan->cognitiveFunction->behavior ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="column">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="2">Emotional Well-being</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="40%"><strong>Mood:</strong></td>
                                <td>{{ $beneficiary->generalCarePlan->emotionalWellbeing->mood ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Social Interactions:</strong></td>
                                <td>{{ $beneficiary->generalCarePlan->emotionalWellbeing->social_interactions ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Emotional Support Need:</strong></td>
                                <td>{{ $beneficiary->generalCarePlan->emotionalWellbeing->emotional_support_needs ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="column">
                    <table>
                        <thead>
                            <tr>
                                <th colspan="2">Assigned Care Worker</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="2"><strong>Name:</strong> {{ $careWorker->first_name ?? 'N/A' }} {{ $careWorker->last_name ?? '' }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: center;">
                                    <strong>Tasks and Responsibilities</strong>
                                </td>
                            </tr>
                            @if ($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->careWorkerResponsibility && count($beneficiary->generalCarePlan->careWorkerResponsibility) > 0)
                                @foreach ($beneficiary->generalCarePlan->careWorkerResponsibility as $responsibility)
                                    <tr>
                                        <td colspan="2">{{ $responsibility->task_description }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">No tasks assigned.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
            @unless($loop->last)
                <div class="page-break"></div>
            @endunless
        @endforeach
    @endif
    
    <div class="footer">
        <p>This report was generated from Sulong Kalinga system on {{ $exportDate }}</p>
        <p>© {{ date('Y') }} Coalition of Services of the Elderly, Inc. (COSE). All Rights Reserved.</p>
    </div>
</body>
</html>