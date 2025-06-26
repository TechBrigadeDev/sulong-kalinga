{{-- filepath: c:\xampp\htdocs\sulong-kalinga-2\web\resources\views\admin\pdf\shiftReportPdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shift Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
        h2 { text-align: center; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        th, td { border: 1px solid #444; padding: 6px 8px; }
        th { background: #e9ecef; }
        .section-title { font-weight: bold; margin-top: 18px; margin-bottom: 6px; }
        .info-table { width: 60%; margin-bottom: 18px; }
        .info-table th, .info-table td { border: none; background: none; padding: 4px 8px; }
        .info-table th { text-align: left; width: 38%; }
        .info-table td { text-align: left; }
    </style>
</head>
<body>
    <h2>Care Worker Shift Report</h2>

    <table class="info-table">
        <tr>
            <th>Care Worker:</th>
            <td>{{ $shift->careWorker->first_name ?? '' }} {{ $shift->careWorker->last_name ?? '' }}</td>
        </tr>
        <tr>
            <th>Date:</th>
            <td>{{ \Carbon\Carbon::parse($shift->time_in)->format('F d, Y') }}</td>
        </tr>
        <tr>
            <th>Shift Time:</th>
            <td>
                {{ \Carbon\Carbon::parse($shift->time_in)->format('h:i A') }} - 
                {{ $shift->time_out ? \Carbon\Carbon::parse($shift->time_out)->format('h:i A') : '--:--' }}
            </td>
        </tr>
        <tr>
            <th>Total Hours:</th>
            <td>
                @if($shift->time_out)
                    @php
                        $start = \Carbon\Carbon::parse($shift->time_in);
                        $end = \Carbon\Carbon::parse($shift->time_out);
                        $totalMinutes = $start->diffInMinutes($end);
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                    @endphp
                    {{ $hours }} hours{{ $minutes > 0 ? ' ' . $minutes . ' minutes' : '' }}
                @else
                    In Progress
                @endif
            </td>
        </tr>
        <tr>
            <th>Status:</th>
            <td>{{ ucfirst($shift->status) }}</td>
        </tr>
    </table>

    <div class="section-title">Location History</div>
    <table>
        <thead>
            <tr>
                <th style="width: 18%;">Time</th>
                <th style="width: 32%;">Location (Lat, Lng)</th>
                <th style="width: 18%;">Event</th>
                <th style="width: 18%;">Beneficiary</th>
                <th style="width: 14%;">Proximity</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tracks as $track)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($track->recorded_at)->format('h:i A') }}</td>
                    <td>
                        {{ $track->address ?? 'Unknown location' }}
                        @if(isset($track->track_coordinates['lat']) && isset($track->track_coordinates['lng']))
                            <br>
                            <span style="font-size: 11px; color: #555;">
                                ({{ $track->track_coordinates['lat'] }}, {{ $track->track_coordinates['lng'] }})
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($track->arrival_status === 'arrived')
                            Arrived
                        @elseif($track->arrival_status === 'departed')
                            Departed
                        @else
                            Unknown
                        @endif
                    </td>
                    <td>
                        @if($track->visitation && $track->visitation->beneficiary)
                            {{ $track->visitation->beneficiary->first_name ?? '' }} {{ $track->visitation->beneficiary->last_name ?? '' }}
                            @if(isset($track->visitation->visit_type_display))
                                <br>
                                <span style="font-size: 11px; color: #555;">
                                    {{ $track->visitation->visit_type_display }}
                                </span>
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        {{ $track->proximity ?? 'N/A' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #999;">No location history available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>