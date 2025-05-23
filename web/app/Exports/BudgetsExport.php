<?php

namespace App\Exports;

use App\Models\BudgetAllocation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class BudgetsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, WithColumnFormatting, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $budgetTypeId;
    protected $rowNumber = 0;
    
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
        return BudgetAllocation::with(['budgetType', 'creator', 'updater'])
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
            '#',
            'Amount (PHP)',
            'Budget Type',
            'Start Date',
            'End Date',
            'Description',
            'Created By',
            'Created At',
            'Updated By',
            'Updated At'
        ];
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Budget Allocations';
    }
    
    /**
     * @var BudgetAllocation $budget
     */
    public function map($budget): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            $budget->amount,
            $budget->budgetType->name,
            $budget->start_date->format('M d, Y'),
            $budget->end_date->format('M d, Y'),
            $budget->description ?: 'N/A',
            $budget->creator ? $budget->creator->first_name . ' ' . $budget->creator->last_name : 'Unknown',
            $budget->created_at->format('M d, Y h:i A'),
            $budget->updater ? $budget->updater->first_name . ' ' . $budget->updater->last_name : 'N/A',
            $budget->updated_at ? $budget->updated_at->format('M d, Y h:i A') : 'N/A'
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
            'B2:B'.$highestRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
                'font' => [
                    'bold' => true
                ]
            ],
        ];
    }
    
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
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
                    
                    // Add color formatting for budget amounts
                    $amount = $sheet->getCell('B'.$row)->getValue();
                    if ($amount < 0) {
                        $sheet->getStyle('B'.$row)->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => 'FF0000'] // Red for negative amounts
                            ]
                        ]);
                    } else if ($amount > 0) {
                        $sheet->getStyle('B'.$row)->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => '008000'] // Green for positive amounts
                            ]
                        ]);
                    }
                }
                
                // Set row height for better padding
                $sheet->getDefaultRowDimension()->setRowHeight(22);
                $sheet->getRowDimension(1)->setRowHeight(26);
                
                // Adjust column widths
                $event->sheet->getColumnDimension('A')->setWidth(6);   // Row number
                $event->sheet->getColumnDimension('B')->setWidth(18);  // Amount
                $event->sheet->getColumnDimension('C')->setWidth(20);  // Budget Type
                $event->sheet->getColumnDimension('D')->setWidth(15);  // Start Date
                $event->sheet->getColumnDimension('E')->setWidth(15);  // End Date
                $event->sheet->getColumnDimension('F')->setWidth(35);  // Description
                $event->sheet->getColumnDimension('G')->setWidth(18);  // Created By
                $event->sheet->getColumnDimension('H')->setWidth(18);  // Created At
                $event->sheet->getColumnDimension('I')->setWidth(18);  // Updated By
                $event->sheet->getColumnDimension('J')->setWidth(18);  // Updated At
                
                // Add auto-filter
                $sheet->setAutoFilter('A1:J' . $highestRow);
                
                // Freeze the header row
                $sheet->freezePane('A2');
                
                // Add report metadata
                $date = Carbon::now()->format('M d, Y h:i A');
                $sheet->setCellValue('A' . ($highestRow + 2), "Report generated on: $date");
                $sheet->getStyle('A' . ($highestRow + 2))->getFont()->setItalic(true);
                
                // Add budget type filter information if applicable
                if ($this->budgetTypeId) {
                    $budgetType = \App\Models\BudgetType::find($this->budgetTypeId);
                    if ($budgetType) {
                        $sheet->setCellValue('A' . ($highestRow + 3), "Filtered by Budget Type: {$budgetType->name}");
                        $sheet->getStyle('A' . ($highestRow + 3))->getFont()->setItalic(true);
                    }
                }
                
                // Add date range information if applicable
                if ($this->startDate || $this->endDate) {
                    $dateInfo = "Date Range:";
                    if ($this->startDate) {
                        $dateInfo .= " From " . Carbon::parse($this->startDate)->format('M d, Y');
                    }
                    if ($this->endDate) {
                        $dateInfo .= " To " . Carbon::parse($this->endDate)->format('M d, Y');
                    }
                    $sheet->setCellValue('A' . ($highestRow + 4), $dateInfo);
                    $sheet->getStyle('A' . ($highestRow + 4))->getFont()->setItalic(true);
                }
            },
        ];
    }
}