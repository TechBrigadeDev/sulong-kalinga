<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Care Managers Report</title>
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
        .caremanager-profile {
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Care Managers Profile Report</h1>
        <div class="export-date">Export Date: {{ $exportDate }}</div>
    </div>

    <!-- Table of Contents -->
    <div class="toc-header">Selected Care Managers ({{ count($caremanagers) }})</div>
    <table>
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="25%">Full Name</th>
                <th width="15%">Municipality</th>
                <th width="20%">Contact</th>
                <th width="20%">Education</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($caremanagers as $index => $cm)
            <tr class="toc-item">
                <td>{{ $index + 1 }}</td>
                <td>{{ $cm->first_name }} {{ $cm->last_name }}</td>
                <td>{{ $cm->municipality->municipality_name }}</td>
                <td>{{ $cm->mobile }}</td>
                <td>{{ $cm->educational_background }}</td>
                <td>
                    <span class="status {{ $cm->volunteer_status == 'Active' ? 'status-active' : 'status-inactive' }}" style="float: none;">
                        {{ $cm->volunteer_status }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    @foreach($allData as $data)
        @php 
            $caremanager = $data['caremanager'];
        @endphp
        
        <div class="caremanager-profile">
            <div class="profile-header">
                <!-- Profile Image -->
                <div class="profile-image-container">
                    <img src="{{ $caremanager->photo ? public_path('storage/' . $caremanager->photo) : public_path('images/defaultProfile.png') }}" alt="Profile Picture" style="width:100%; height:100%; object-fit:cover;">
                </div>
                
                <div class="profile-details">
                    <h2>{{ $caremanager->first_name }} {{ $caremanager->last_name }}</h2>
                                        
                    <div class="status {{ $caremanager->volunteer_status == 'Active' ? 'status-active' : 'status-inactive' }}">
                        {{ $caremanager->volunteer_status }} Care Manager
                    </div>
                </div>
            </div>
            
            <div class="section-title">Personal Information</div>
            <div class="column">
                <table>
                    <tbody>
                        <tr>
                            <td width="40%"><strong>Educational Background:</strong></td>
                            <td>{{ $caremanager->educational_background }}</td>
                        </tr>
                        <tr>
                            <td><strong>Birthday:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($caremanager->birthday)->format('F j, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Gender:</strong></td>
                            <td>{{ $caremanager->gender }}</td>
                        </tr>
                        <tr>
                            <td><strong>Civil Status:</strong></td>
                            <td>{{ $caremanager->civil_status }}</td>
                        </tr>
                        <tr>
                            <td><strong>Religion:</strong></td>
                            <td>{{ $caremanager->religion ?? 'Prefer Not To Say' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Nationality:</strong></td>
                            <td>{{ $caremanager->nationality }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="column">
                <table>
                    <tbody>
                        <tr>
                            <td width="40%"><strong>Assigned Municipality:</strong></td>
                            <td>{{ $caremanager->municipality->municipality_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email Address:</strong></td>
                            <td>{{ $caremanager->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Mobile Number:</strong></td>
                            <td>{{ $caremanager->mobile }}</td>
                        </tr>
                        <tr>
                            <td><strong>Landline Number:</strong></td>
                            <td>{{ $caremanager->landline ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current Address:</strong></td>
                            <td>{{ $caremanager->address }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="section-title">Document Submissions</div>
            <table>
                <tbody>
                    <tr>
                        <td width="30%"><strong>Government Issued ID:</strong></td>
                        <td>{{ $caremanager->government_issued_id ? 'Submitted' : 'Not Submitted' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Resume / CV:</strong></td>
                        <td>{{ $caremanager->cv_resume ? 'Submitted' : 'Not Submitted' }}</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="section-title">Government ID Numbers</div>
            <table>
                <tbody>
                    <tr>
                        <td width="30%"><strong>SSS ID Number:</strong></td>
                        <td>{{ $caremanager->sss_id_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>PhilHealth ID Number:</strong></td>
                        <td>{{ $caremanager->philhealth_id_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pag-Ibig ID Number:</strong></td>
                        <td>{{ $caremanager->pagibig_id_number ?? 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
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