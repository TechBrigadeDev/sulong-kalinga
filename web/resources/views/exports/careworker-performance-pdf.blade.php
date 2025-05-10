<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Care Worker Performance Report</title>
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
        .highlighted-row {
            background-color: #e6f2ff;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Care Worker Performance Report</h1>
        <div class="export-date">Export Date: {{ $exportDate }}</div>
    </div>
    
    <div class="filters">
        <strong>Applied Filters:</strong> {{ $filterDescription }}
    </div>
    
    <div class="summary-cards">
        <div class="summary-card">
            <span class="summary-value">{{ $data['formattedCareTime']['hours'] }} hrs {{ $data['formattedCareTime']['minutes'] }} min</span>
            <span class="summary-label">TOTAL CARE HOURS</span>
        </div>
        <div class="summary-card">
            <span class="summary-value">{{ $data['activeCareWorkersCount'] }}</span>
            <span class="summary-label">ACTIVE CARE WORKERS</span>
        </div>
        <div class="summary-card">
            <span class="summary-value">{{ $data['totalInterventions'] }}</span>
            <span class="summary-label">TOTAL SERVICES</span>
        </div>
    </div>
    
    <div class="section-title">Care Worker Performance Summary</div>
    <table>
        <thead>
            <tr>
                <th>Care Worker</th>
                <th style="text-align: center">Hours Worked</th>
                <th style="text-align: center">Beneficiary Visits</th>
                <th style="text-align: center">Interventions Performed</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['careWorkerPerformance'] as $worker)
                <tr @if($worker['is_selected']) class="highlighted-row" @endif>
                    <td><strong>{{ $worker['name'] }}</strong></td>
                    <td style="text-align: center">{{ $worker['hours_worked']['formatted_time'] }}</td>
                    <td style="text-align: center">{{ $worker['beneficiary_visits'] }}</td>
                    <td style="text-align: center">{{ $worker['interventions_performed'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center">No performance data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if(count($data['mostImplementedInterventions']) > 0)
    <div class="section-title">Most Implemented Interventions</div>
    <table>
        <thead>
            <tr>
                <th>Intervention</th>
                <th style="text-align: center">Times Implemented</th>
            </tr>
        </thead>
        <tbody>
            @foreach(array_slice($data['mostImplementedInterventions'], 0, 7) as $intervention)
                <tr>
                    <td>{{ $intervention['intervention_description'] }}</td>
                    <td style="text-align: center">{{ $intervention['count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    @if(isset($data['clientCareBreakdown']) && count($data['clientCareBreakdown']) > 0)
    <div class="section-title">Beneficiary Care Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Beneficiary</th>
                <th style="text-align: center">Care Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['clientCareBreakdown'] as $client)
                <tr>
                    <td>{{ $client['beneficiary_name'] }}</td>
                    <td style="text-align: center">{{ $client['formatted_time'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    
    <div class="page-break"></div>
    
    <div class="section-title">Care Services Summary</div>
    
    @foreach($data['careCategories'] as $index => $category)
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
                @if(isset($data['categorySummaries'][$category->care_category_id]) && 
                    !empty($data['categorySummaries'][$category->care_category_id]['interventions']))
                    @foreach($data['categorySummaries'][$category->care_category_id]['interventions'] as $intervention)
                        <tr>
                            <td>{{ $intervention['description'] }}</td>
                            <td style="text-align: center">{{ $intervention['times_implemented'] }}</td>
                            <td style="text-align: center">{{ $intervention['total_hours'] }} hrs {{ $intervention['total_minutes'] > 0 ? $intervention['total_minutes'] . ' min' : '' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" style="text-align: center">No interventions recorded for this category</td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        @if(!$loop->last)
        <div class="page-break"></div>
        @endif
    @endforeach
    
    <div class="footer">
        <p>This report was generated from Sulong Kalinga Care Worker Performance Dashboard on {{ $exportDate }}</p>
        <p>© {{ date('Y') }} Coalition of Services of the Elderly, Inc. (COSE). All Rights Reserved.</p>
    </div>
</body>
</html>