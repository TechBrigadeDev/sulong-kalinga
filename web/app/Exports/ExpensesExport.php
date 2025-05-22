<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, WithColumnFormatting
{
    protected $startDate;
    protected $endDate;
    protected $categoryId;
    protected $rowNumber = 0;
    
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
            '#',
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
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            $expense->title,
            $expense->category->name,
            $expense->amount,
            ucfirst(str_replace('_', ' ', $expense->payment_method)),
            $expense->date->format('M d, Y'),
            $expense->receipt_number ?: 'N/A',
            $expense->description ?: 'N/A',
            $expense->creator ? $expense->creator->first_name . ' ' . $expense->creator->last_name : 'Unknown',
            $expense->created_at->format('M d, Y h:i A')
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        
        return [
            // Style the header row
            1 => [
                'font' => [
                    'bold' => true, 
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID, 
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D9D9D9']
                    ]
                ]
            ],
            
            // Style for all cells
            'A1:J'.$highestRow => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D9D9D9']
                    ],
                    'outline' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ],
            
            // Style for row numbers
            'A2:A'.$highestRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ]
            ],
            
            // Style for amount column
            'D2:D'.$highestRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ]
            ],
        ];
    }
    
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Apply striped rows for better readability
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A'.$row.':J'.$row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F5F5F5'] // Light grey for even rows
                            ]
                        ]);
                    }
                }
                
                // Set row height for better padding
                $sheet->getDefaultRowDimension()->setRowHeight(22);
                $sheet->getRowDimension(1)->setRowHeight(26);
                
                // Adjust column widths
                $event->sheet->getColumnDimension('A')->setWidth(6);  // Row number
                $event->sheet->getColumnDimension('B')->setWidth(25); // Title
                $event->sheet->getColumnDimension('C')->setWidth(20); // Category
                $event->sheet->getColumnDimension('D')->setWidth(15); // Amount
                $event->sheet->getColumnDimension('E')->setWidth(18); // Payment Method
                $event->sheet->getColumnDimension('F')->setWidth(15); // Date
                $event->sheet->getColumnDimension('G')->setWidth(16); // Receipt Number
                $event->sheet->getColumnDimension('H')->setWidth(35); // Description
                $event->sheet->getColumnDimension('I')->setWidth(20); // Created By
                $event->sheet->getColumnDimension('J')->setWidth(18); // Created At
                
                // Add auto-filter
                $sheet->setAutoFilter('A1:J' . $highestRow);
                
                // Freeze the header row
                $sheet->freezePane('A2');
                
                // Format receipt numbers as text to prevent scientific notation
                $sheet->getStyle('G2:G'.$highestRow)->getNumberFormat()->setFormatCode('@');
            },
        ];
    }
}