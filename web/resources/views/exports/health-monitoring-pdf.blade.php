php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Monitoring Report</title>
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
        h2 {
            color: #2b5797;
            font-size: 18px;
            margin: 15px 0 10px 0;
        }
        .export-date {
            color: #666;
            margin-bottom: 15px;
        }
        .filters {
            background-color: #f5f5f5;
            padding: 10px;
            font-size: 11px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #eaeaea;
        }
        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .summary-card {
            width: 31%;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            background-color: #f9f9f9;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #2b5797;
        }
        .summary-label {
            color: #666;
            font-size: 10px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            color: #333;
        }
        td {
            padding: 8px;
        }
        .section-title {
            background-color: #2b5797;
            color: white;
            padding: 5px 10px;
            font-size: 14px;
            margin: 15px 0 10px 0;
        }
        .table-header {
            background-color: #2b5797;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
        .vitals-chart {
            width: 100%;
            height: 200px;
            margin: 10px 0;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .beneficiary-details {
            margin-bottom: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        .detail-label {
            width: 25%;
            font-weight: bold;
            color: #333;
        }
        .detail-value {
            width: 75%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Health Monitoring Report</h1>
        <div class="export-date">Export Date: {{ $exportDate }}</div>
    </div>
    
    <div class="filters">
        <strong>Applied Filters:</strong> {{ $filterDescription }}
    </div>
    
    @if($selectedBeneficiary)
    <!-- Beneficiary Details Section -->
    <div class="section-title">Beneficiary Details</div>
    <div class="beneficiary-details">
        <div class="detail-row">
            <div class="detail-label">Name:</div>
            <div class="detail-value">{{ $selectedBeneficiary->first_name }} {{ $selectedBeneficiary->last_name }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Age:</div>
            <div class="detail-value">{{ \Carbon\Carbon::parse($selectedBeneficiary->birthday)->age }} years</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Gender:</div>
            <div class="detail-value">{{ $selectedBeneficiary->gender }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Civil Status:</div>
            <div class="detail-value">{{ $selectedBeneficiary->civil_status }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Address:</div>
            <div class="detail-value">
                {{ $selectedBeneficiary->street_address }}, 
                {{ $selectedBeneficiary->barangay->barangay_name ?? 'N/A' }}, 
                {{ $selectedBeneficiary->municipality->municipality_name ?? 'N/A' }}
            </div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Category:</div>
            <div class="detail-value">{{ $selectedBeneficiary->category->category_name ?? 'N/A' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Total Care Hours:</div>
            <div class="detail-value">{{ $totalCareTime }}</div>
        </div>
    </div>
    @else
    <!-- Health Statistics Section -->
    <div class="section-title">Health Statistics</div>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-center">Age 60-69</th>
                <th class="text-center">Age 70-79</th>
                <th class="text-center">Age 80-89</th>
                <th class="text-center">Age 90+</th>
                <th class="text-center">Male</th>
                <th class="text-center">Female</th>
                <th class="text-center">Single</th>
                <th class="text-center">Married</th>
                <th class="text-center">Widowed</th>
                <th class="text-center">%</th>
            </tr>
        </thead>
        <tbody>
            @foreach($healthStatistics as $category => $stats)
            <tr>
                <td>{{ $category }}</td>
                <td style="text-align: center">{{ $stats['age_60_69'] ?? 0 }}</td>
                <td style="text-align: center">{{ $stats['age_70_79'] ?? 0 }}</td>
                <td style="text-align: center">{{ $stats['age_80_89'] ?? 0 }}</td>
                <td style="text-align: center">{{ $stats['age_90_plus'] ?? 0 }}</td>
                <td style="text-align: center">{{ $stats['male'] ?? 0 }}</td>
                <td style="text-align: center">{{ $stats['female'] ?? 0 }}</td>
                <td style="text-align: center">{{ $stats['single'] ?? 0 }}</td>
                <td style="text-align: center">{{ $stats['married'] ?? 0 }}</td>
                <td style="text-align: center">{{ $stats['widowed'] ?? 0 }}</td>
                <td style="text-align: center">{{ $stats['percentage'] }}%</td>
            </tr>
            @endforeach
            <tr>
                <td><strong>Total</strong></td>
                <td style="text-align: center"><strong>{{ $totals['age_60_69'] ?? 0 }}</strong></td>
                <td style="text-align: center"><strong>{{ $totals['age_70_79'] ?? 0 }}</strong></td>
                <td style="text-align: center"><strong>{{ $totals['age_80_89'] ?? 0 }}</strong></td>
                <td style="text-align: center"><strong>{{ $totals['age_90_plus'] ?? 0 }}</strong></td>
                <td style="text-align: center"><strong>{{ $totals['male'] ?? 0 }}</strong></td>
                <td style="text-align: center"><strong>{{ $totals['female'] ?? 0 }}</strong></td>
                <td style="text-align: center"><strong>{{ $totals['single'] ?? 0 }}</strong></td>
                <td style="text-align: center"><strong>{{ $totals['married'] ?? 0 }}</strong></td>
                <td style="text-align: center"><strong>{{ $totals['widowed'] ?? 0 }}</strong></td>
                <td style="text-align: center"><strong>100%</strong></td>
            </tr>
        </tbody>
    </table>
    @endif
    
    <div class="page-break"></div>

    <!-- Care Services Summary -->
    <div class="section-title">Care Services Summary</div>
    
    @foreach($careCategories as $index => $category)
        @if(isset($careServicesSummary[$category->care_category_id]) && $careServicesSummary[$category->care_category_id]['has_interventions'])
            <div class="table-header">{{ $category->care_category_name }}</div>
            <table>
                <thead>
                    <tr>
                        <th>Intervention</th>
                        <th style="text-align: center; width: 20%">Times Implemented</th>
                        <th style="text-align: center; width: 25%">Total Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($careServicesSummary[$category->care_category_id]['interventions'] as $intervention)
                        <tr>
                            <td>{{ $intervention['description'] }}</td>
                            <td style="text-align: center">{{ $intervention['implementations'] }}</td>
                            <td style="text-align: center">{{ $intervention['formatted_duration'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        
        @if(!$loop->last && isset($careServicesSummary[$category->care_category_id]) && $careServicesSummary[$category->care_category_id]['has_interventions'])
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="page-break"></div>

    <!-- Vital Signs Section -->
    <div class="section-title">Vital Signs</div>
    <div style="margin-bottom: 15px;">
        <p>Time Period: {{ $dateRangeLabel }}</p>
    </div>

    <!-- Blood Pressure Chart -->
    <div style="margin-bottom: 20px;">
        <div style="font-weight: bold; margin-bottom: 5px; color: #2b5797;">Blood Pressure</div>
        {!! $chartHtml['bloodPressure'] !!}
    </div>

    <!-- Heart Rate Chart -->
    <div style="margin-bottom: 20px;">
        <div style="font-weight: bold; margin-bottom: 5px; color: #2b5797;">Heart Rate</div>
        {!! $chartHtml['heartRate'] !!}
    </div>

    <!-- Respiratory Rate Chart -->
    <div style="margin-bottom: 20px;">
        <div style="font-weight: bold; margin-bottom: 5px; color: #2b5797;">Respiratory Rate</div>
        {!! $chartHtml['respiratoryRate'] !!}
    </div>

    <!-- Temperature Chart -->
    <div style="margin-bottom: 20px;">
        <div style="font-weight: bold; margin-bottom: 5px; color: #2b5797;">Temperature</div>
        {!! $chartHtml['temperature'] !!}
    </div>

    @if(!$selectedBeneficiary)
        <div class="page-break"></div>
        <!-- Statistical Charts Section (only for aggregate view) -->
        <div class="section-title">Medical Conditions & Illnesses</div>
        
        <!-- Medical Conditions Chart -->
        <div style="margin-bottom: 20px;">
            <div style="font-weight: bold; margin-bottom: 5px; color: #2b5797;">Top Medical Conditions</div>
            {!! $chartHtml['medicalConditions'] !!}
        </div>

        <!-- Illnesses Chart -->
        <div style="margin-bottom: 20px;">
            <div style="font-weight: bold; margin-bottom: 5px; color: #2b5797;">Top Reported Illnesses</div>
            {!! $chartHtml['illnesses'] !!}
        </div>
    @endif

    <div class="footer">
        <p>This report was generated from Sulong Kalinga Health Monitoring Dashboard on {{ $exportDate }}</p>
        <p>© {{ date('Y') }} Coalition of Services of the Elderly, Inc. (COSE). All Rights Reserved.</p>
    </div>
</body>
</html>