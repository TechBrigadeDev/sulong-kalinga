<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Care Workers Report</title>
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
        .careworker-profile {
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
        .profile-image-container {
            width: 80px; 
            height: 80px; 
            border-radius: 50%; 
            border: 1px solid #ddd; 
            margin-right: 15px; 
            background-color: #f5f5f5; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            overflow: hidden;
        }
        .profile-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-icon {
            font-size: 36px; 
            color: #aaa;
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

        /* Replace the current beneficiary grid section with this */
        .beneficiary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin: 10px 0;
        }
        .beneficiary-table td {
            width: 25%;
            padding: 0;
            vertical-align: top;
            border: none;
        }
        .beneficiary-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin: 5px;
            background-color: #f9f9f9;
            height: 110px;
            text-align: center;
        }
        .beneficiary-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin: 0 auto 5px auto;
            background-color: #eee;
            overflow: hidden;
        }
        .beneficiary-name {
            font-size: 10px;
            font-weight: bold;
            margin-top: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Care Workers Profile Report</h1>
        <div class="export-date">Export Date: {{ $exportDate }}</div>
    </div>

    <!-- Table of Contents -->
    <div class="toc-header">Selected Care Workers ({{ count($careworkers) }})</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="20%">Full Name</th>
                <th width="10%">Municipality</th>
                <th width="15%">Care Manager</th>
                <th width="20%">Contact</th>
                <th width="15%">Education</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($careworkers as $index => $cw)
            <tr class="toc-item">
                <td>{{ $index + 1 }}</td>
                <td>{{ $cw->first_name }} {{ $cw->last_name }}</td>
                <td>{{ $cw->municipality->municipality_name }}</td>
                <td>
                    @if($cw->assignedCareManager)
                        {{ $cw->assignedCareManager->first_name }} {{ $cw->assignedCareManager->last_name }}
                    @else
                        <span class="text-muted">Unassigned</span>
                    @endif
                </td>
                <td>{{ $cw->mobile }}</td>
                <td>{{ $cw->educational_background }}</td>
                <td>
                    <span class="status {{ $cw->volunteer_status == 'Active' ? 'status-active' : 'status-inactive' }}" style="float: none;">
                        {{ $cw->volunteer_status }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    @foreach($allData as $data)
        @php 
            $careworker = $data['careworker'];
            $beneficiaries = $data['assignedBeneficiaries'];
        @endphp
        
        <div class="careworker-profile">
            <div class="profile-header">
                <!-- Profile Image -->
                <div class="profile-image-container">
                    <img src="{{ $careworker->photo ? public_path('storage/' . $careworker->photo) : public_path('images/defaultProfile.png') }}" alt="Profile Picture" style="width:100%; height:100%; object-fit:cover;">
                </div>
                
                <div class="profile-details">
                    <h2>{{ $careworker->first_name }} {{ $careworker->last_name }}</h2>
                                        
                    <div class="status {{ $careworker->volunteer_status == 'Active' ? 'status-active' : 'status-inactive' }}">
                        {{ $careworker->volunteer_status }} Care Worker
                    </div>
                </div>
            </div>
            
            <div class="section-title">Personal Information</div>
            <div class="column">
                <table>
                    <tbody>
                        <tr>
                            <td width="40%"><strong>Educational Background:</strong></td>
                            <td>{{ $careworker->educational_background }}</td>
                        </tr>
                        <tr>
                            <td><strong>Birthday:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($careworker->birthday)->format('F j, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Gender:</strong></td>
                            <td>{{ $careworker->gender }}</td>
                        </tr>
                        <tr>
                            <td><strong>Civil Status:</strong></td>
                            <td>{{ $careworker->civil_status }}</td>
                        </tr>
                        <tr>
                            <td><strong>Religion:</strong></td>
                            <td>{{ $careworker->religion ?? 'Prefer Not To Say' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nationality:</strong></td>
                            <td>{{ $careworker->nationality }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="column">
                <table>
                    <tbody>
                        <tr>
                            <td width="40%"><strong>Assigned Municipality:</strong></td>
                            <td>{{ $careworker->municipality->municipality_name }}</td>
                        </tr>
                        <tr>
                            <td width="40%"><strong>Assigned Care Manager:</strong></td>
                            <td>
                                @if($careworker->assignedCareManager)
                                    {{ $careworker->assignedCareManager->first_name }} {{ $careworker->assignedCareManager->last_name }}
                                @else
                                    <span style="color: #777; font-style: italic;">Unassigned</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Email Address:</strong></td>
                            <td>{{ $careworker->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Mobile Number:</strong></td>
                            <td>{{ $careworker->mobile }}</td>
                        </tr>
                        <tr>
                            <td><strong>Landline Number:</strong></td>
                            <td>{{ $careworker->landline ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current Address:</strong></td>
                            <td>{{ $careworker->address }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="section-title">Government ID Numbers</div>
            <table>
                <tbody>
                    <tr>
                        <td width="30%"><strong>SSS ID Number:</strong></td>
                        <td>{{ $careworker->sss_id_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>PhilHealth ID Number:</strong></td>
                        <td>{{ $careworker->philhealth_id_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pag-Ibig ID Number:</strong></td>
                        <td>{{ $careworker->pagibig_id_number ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="section-title">Document Submissions</div>
            <table>
                <tbody>
                    <tr>
                        <td width="30%"><strong>Government Issued ID:</strong></td>
                        <td>{{ $careworker->government_issued_id ? 'Submitted' : 'Not Submitted' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Resume / CV:</strong></td>
                        <td>{{ $careworker->cv_resume ? 'Submitted' : 'Not Submitted' }}</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="section-title">Managed Beneficiaries</div>
                @if(count($beneficiaries) > 0)
                    <table class="beneficiary-table">
                        @foreach($beneficiaries->chunk(4) as $chunk)
                            <tr>
                                @foreach($chunk as $beneficiary)
                                    <td>
                                        <div class="beneficiary-card">
                                            <div class="beneficiary-img">
                                                <img src="{{ $beneficiary->photo ? public_path('storage/' . $beneficiary->photo) : public_path('images/defaultProfile.png') }}" alt="Profile Picture" style="width:100%; height:100%; object-fit:cover;">
                                            </div>
                                            <p class="beneficiary-name">{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</p>
                                        </div>
                                    </td>
                                @endforeach
                                
                                <!-- Fill empty cells to maintain grid -->
                                @for($i = count($chunk); $i < 4; $i++)
                                    <td></td>
                                @endfor
                            </tr>
                        @endforeach
                    </table>
                @else
                    <p style="text-align: center; font-style: italic;">No beneficiaries being handled currently.</p>
                @endif
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