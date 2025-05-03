<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beneficiary Profile Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
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
        .export-date {
            color: #666;
            margin-bottom: 15px;
        }
        .beneficiary-profile {
            page-break-inside: avoid;
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 10px;
            background: #fff;
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
        h2 {
            color: #2b5797;
            font-size: 18px;
            margin: 0 0 5px 0;
        }
        .registration-date {
            color: #666;
            font-style: italic;
            font-size: 11px;
        }
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
            float: right;
        }
        .status-active {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .status-inactive {
            background: #ffebee;
            color: #c62828;
        }
        .section-title {
            background-color: #2b5797;
            color: white;
            padding: 5px 10px;
            font-size: 14px;
            margin: 15px 0 10px 0;
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
        .column {
            width: 49%;
            display: inline-block;
            vertical-align: top;
        }
        strong {
            font-weight: bold;
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
        .category-header {
            background-color: #f5f5f5;
            font-weight: bold;
            padding: 5px;
        }
        .toc-header {
            background-color: #2b5797;
            color: white;
            padding: 8px 10px;
            font-size: 16px;
            margin: 15px 0 10px 0;
        }
        .toc-item {
            border-bottom: 1px solid #eee;
        }
        .toc-item:hover {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Beneficiary Profile Report</h1>
        <div class="export-date">Export Date: {{ $exportDate }}</div>
    </div>

    <!-- Table of Contents -->
    <div class="toc-header">Selected Beneficiaries ({{ count($beneficiaries) }})</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="30%">Full Name</th>
                <th width="10%">Age</th>
                <th width="20%">Category</th>
                <th width="20%">Location</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($beneficiaries as $index => $ben)
            <tr class="toc-item">
                <td>{{ $index + 1 }}</td>
                <td>{{ $ben->first_name }} {{ $ben->last_name }}</td>
                <td>{{ \Carbon\Carbon::parse($ben->birthday)->age }} years</td>
                <td>{{ $ben->category->category_name ?? 'N/A' }}</td>
                <td>{{ $ben->barangay->barangay_name ?? 'N/A' }}, {{ $ben->municipality->municipality_name ?? 'N/A' }}</td>
                <td>
                    <span class="status {{ $ben->status->status_name == 'Active' ? 'status-active' : 'status-inactive' }}" style="float: none;">
                        {{ $ben->status->status_name }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    @foreach($allData as $data)
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

        <div class="beneficiary-profile">
            <div class="profile-header">
                <!-- Add Profile Image -->
                <img class="profile-image" src="{{ $beneficiary->photo ? public_path('storage/' . $beneficiary->photo) : public_path('images/defaultProfile.png') }}" alt="Profile Picture">
                
                <div class="profile-details">
                    <h2>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h2>
                    <div class="registration-date">A Beneficiary since {{ \Carbon\Carbon::parse($beneficiary->created_at)->format('F j, Y') }}</div>
                    
                    <div class="status {{ $beneficiary->status->status_name == 'Active' ? 'status-active' : 'status-inactive' }}">
                        {{ $beneficiary->status->status_name }} Beneficiary
                    </div>
                </div>
            </div>
            
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
                        <tr>
                            <td width="30%"><strong>Medical Conditions:</strong></td>
                            <td>{{ $beneficiary->generalCarePlan->healthHistory->medical_conditions ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Medications:</strong></td>
                            <td>{{ $beneficiary->generalCarePlan->healthHistory->medications ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Allergies:</strong></td>
                            <td>{{ $beneficiary->generalCarePlan->healthHistory->allergies ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Immunizations:</strong></td>
                            <td>{{ $beneficiary->generalCarePlan->healthHistory->immunizations ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Category:</strong></td>
                            <td>{{ $beneficiary->category->category_name }}</td>
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
                    {{-- Check if generalCarePlan exists --}}
                    @if ($beneficiary->generalCarePlan)
                        {{-- Check if medications exist and iterate --}}
                        @forelse ($beneficiary->generalCarePlan->medications as $medication)
                            <tr>
                                <td>{{ $medication->medication }}</td>
                                <td>{{ $medication->dosage }}</td>
                                <td>{{ $medication->frequency }}</td>
                                <td>{{ $medication->administration_instructions }}</td>
                            </tr>
                        @empty
                            {{-- No medications recorded --}}
                            <tr>
                                <td colspan="4">No medications recorded.</td>
                            </tr>
                        @endforelse
                    @else
                        {{-- General Care Plan not available --}}
                        <tr>
                            <td colspan="4">No medications recorded.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!--<div class="page-break"></div>-->
            
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
                        {{-- Check if generalCarePlan exists --}}
                        @if ($beneficiary->generalCarePlan)
                            {{-- Iterate over careWorkerResponsibility if it exists --}}
                            @forelse ($beneficiary->generalCarePlan->careWorkerResponsibility as $responsibility)
                                <tr>
                                    <td colspan="2">{{ $responsibility->task_description }}</td>
                                </tr>
                            @empty
                                {{-- No tasks assigned --}}
                                <tr>
                                    <td colspan="2">No tasks assigned.</td>
                                </tr>
                            @endforelse
                        @else
                            {{-- General Care Plan not available --}}
                            <tr>
                                <td colspan="2">No tasks assigned.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="footer">
        <p>This report was generated from Sulong Kalinga system on {{ $exportDate }}</p>
        <p>© {{ date('Y') }} Coalition of Services of the Elderly, Inc. (COSE). All Rights Reserved.</p>
    </div>
</body>
</html>