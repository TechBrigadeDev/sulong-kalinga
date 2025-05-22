<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $categoryId;
    
    public function __construct($startDate = null, $endDate = null, $categoryId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->categoryId = $categoryId;
    }
    
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Expense::with(['category', 'creator'])
            ->when($this->startDate, function ($query) {
                return $query->whereDate('date', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                return $query->whereDate('date', '<=', $this->endDate);
            })
            ->when($this->categoryId, function ($query) {
                return $query->where('category_id', $this->categoryId);
            })
            ->orderBy('date', 'desc')
            ->get();
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Category',
            'Amount (PHP)',
            'Payment Method',
            'Date',
            'Receipt Number',
            'Description',
            'Created By',
            'Created At'
        ];
    }
    
    /**
     * @var Expense $expense
     */
    public function map($expense): array
    {
        return [
            $expense->expense_id,
            $expense->title,
            $expense->category->name,
            $expense->amount,
            ucfirst(str_replace('_', ' ', $expense->payment_method)),
            $expense->date->format('M d, Y'),
            $expense->receipt_number,
            $expense->description,
            $expense->creator ? $expense->creator->first_name . ' ' . $expense->creator->last_name : 'Unknown',
            $expense->created_at->format('M d, Y h:i A')
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}