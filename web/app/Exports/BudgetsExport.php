<?php

namespace App\Exports;

use App\Models\BudgetAllocation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BudgetsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $budgetTypeId;
    
    public function __construct($startDate = null, $endDate = null, $budgetTypeId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->budgetTypeId = $budgetTypeId;
    }
    
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return BudgetAllocation::with(['budgetType', 'creator'])
            ->when($this->startDate, function ($query) {
                return $query->whereDate('start_date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                return $query->whereDate('end_date', '<=', $this->endDate);
            })
            ->when($this->budgetTypeId, function ($query) {
                return $query->where('budget_type_id', $this->budgetTypeId);
            })
            ->orderBy('start_date', 'desc')
            ->get();
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Amount (PHP)',
            'Budget Type',
            'Start Date',
            'End Date',
            'Description',
            'Created By',
            'Created At',
            'Last Updated At'
        ];
    }
    
    /**
     * @var BudgetAllocation $budget
     */
    public function map($budget): array
    {
        return [
            $budget->budget_allocation_id,
            $budget->amount,
            $budget->budgetType->name,
            $budget->start_date->format('M d, Y'),
            $budget->end_date->format('M d, Y'),
            $budget->description,
            $budget->creator ? $budget->creator->first_name . ' ' . $budget->creator->last_name : 'Unknown',
            $budget->created_at->format('M d, Y h:i A'),
            $budget->updated_at->format('M d, Y h:i A')
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}