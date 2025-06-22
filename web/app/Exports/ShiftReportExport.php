<?php

namespace App\Exports;

use App\Models\Shift;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ShiftReportExport implements FromView
{
    protected $shiftId;

    public function __construct($shiftId)
    {
        $this->shiftId = $shiftId;
    }

    /**
     * Prepare the data for the export view.
     */
    public function view(): View
    {
        $shift = Shift::with([
            'careWorker',
            'tracks.visitation.beneficiary'
        ])->findOrFail($this->shiftId);

        // Prepare tracks as in your controller (add proximity if needed)
        $tracks = $shift->tracks
            ->whereIn('arrival_status', ['arrived', 'departed'])
            ->sortBy('recorded_at')
            ->values();

        // You can add proximity logic here if you want it in the export

        return view('admin.pdf.shiftReportPdf', [
            'shift' => $shift,
            'tracks' => $tracks
        ]);
    }
}
