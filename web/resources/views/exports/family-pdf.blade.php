<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Members Report</title>
    <style>
        @page {
            size: letter portrait;
            margin: 0.5in;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            font-size: 12px;
            margin: 0;
            padding: 0;
            width: 100%;
            zoom: 100%; /* Fix scaling issues */
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
        .family-member-profile {
            page-break-inside: avoid;
            page-break-after: auto;
            margin-bottom: 30px;
            border: 1px solid #ddd;
            padding: 10px;
            background: #fff;
            width: 100%;
            box-sizing: border-box;
        }
        .profile-header {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            width: 100%;
            box-sizing: border-box;
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
            table-layout: fixed;
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
        .toc-item:hover {
            background-color: #f9f9f9;
        }
        .related-beneficiary {
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 10px;
            text-align: center;
        }
        .related-beneficiary-title {
            font-weight: bold;
            margin-bottom: 10px;
            background-color: #f5f5f5;
            padding: 5px;
        }
        .related-beneficiary-image {
            margin-bottom: 5px;
        }
        .related-beneficiary-name {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Family Members Profile Report</h1>
        <div class="export-date">Export Date: {{ $exportDate }}</div>
    </div>

    <!-- Table of Contents -->
    <div class="toc-header">Selected Family Members ({{ count($familyMembers) }})</div>
    <table style="width: 100%; table-layout: fixed;">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="25%">Full Name</th>
                <th width="15%">Relationship</th>
                <th width="25%">Related Beneficiary</th>
                <th width="15%">Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($familyMembers as $index => $member)
            <tr class="toc-item">
                <td>{{ $index + 1 }}</td>
                <td>{{ $member->first_name }} {{ $member->last_name }}</td>
                <td>{{ $member->relation_to_beneficiary }}</td>
                <td>{{ $member->beneficiary->first_name ?? 'N/A' }} {{ $member->beneficiary->last_name ?? '' }}</td>
                <td>{{ $member->mobile ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    @foreach($allData as $data)
    @php 
        $family_member = $data['family_member'];
        $relatedBeneficiaryInfo = $data['relatedBeneficiaryInfo'];
    @endphp
    
    <div class="family-member-profile">
        <div class="profile-header">
            <div class="profile-image-container" style="overflow:hidden; width: 80px; height: 80px; min-width: 80px;">
                <img src="{{ $family_member->photo ? public_path('storage/' . $family_member->photo) : public_path('images/defaultProfile.png') }}" alt="Profile Picture" 
                    style="width:80px; height:80px; object-fit:cover;">
            </div>
            
            <div class="profile-details">
                <h2>{{ $family_member->first_name }} {{ $family_member->last_name }}</h2>
                <div class="registration-date">Family Member since {{ \Carbon\Carbon::parse($family_member->created_at)->format('F j, Y') }}</div>
            </div>
        </div>
        
        <div class="section-title">Personal Information</div>
        <table>
            <tbody>
                <tr>
                    <td width="30%"><strong>Gender:</strong></td>
                    <td>{{ $family_member->gender }}</td>
                </tr>
                <tr>
                    <td><strong>Birthday:</strong></td>
                    <td>{{ \Carbon\Carbon::parse($family_member->birthday)->format('F j, Y') }}</td>
                </tr>
                <tr>
                    <td><strong>Mobile Number:</strong></td>
                    <td>{{ $family_member->mobile ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Landline Number:</strong></td>
                    <td>{{ $family_member->landline ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Current Address:</strong></td>
                    <td>{{ $family_member->street_address }}</td>
                </tr>
                <tr>
                    <td><strong>Relationship:</strong></td>
                    <td>{{ $family_member->relation_to_beneficiary }}</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td>{{ $family_member->email ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>
        
        <div class="section-title">Related Beneficiary</div>
        @if($family_member->beneficiary)
        <div class="related-beneficiary">
            <div class="related-beneficiary-title">{{ $family_member->beneficiary->first_name }} {{ $family_member->beneficiary->last_name }}</div>
            <div class="related-beneficiary-image">
                <img src="{{ $family_member->beneficiary->photo ? public_path('storage/' . $family_member->beneficiary->photo) : public_path('images/defaultProfile.png') }}" alt="Profile Picture" 
                    style="width:60px; height:60px; border-radius:50%; border:1px solid #ddd; margin:0 auto; object-fit:cover;">
            </div>
            <div class="related-beneficiary-name">
                Beneficiary Category: {{ $relatedBeneficiaryInfo['category'] ?? 'N/A' }}
            </div>
        </div>
        @else
        <p>No related beneficiary found.</p>
        @endif
    </div><!-- Properly closing the family-member-profile div -->
    
    @if(!$loop->last)
    <div style="page-break-after: always;"></div>
    @endif
    @endforeach

    <div class="footer">
        <p>This report was generated from Sulong Kalinga system on {{ $exportDate }}</p>
        <p>© {{ date('Y') }} Coalition of Services of the Elderly, Inc. (COSE). All Rights Reserved.</p>
    </div>
</body>
</html>