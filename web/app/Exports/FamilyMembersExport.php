<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Exports\FamilyMembersExport.php

namespace App\Exports;

use App\Models\FamilyMember;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FamilyMembersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $familyMemberIds;

    public function __construct($familyMemberIds)
    {
        $this->familyMemberIds = $familyMemberIds;
    }

    public function collection()
    {
        return FamilyMember::with(['beneficiary'])
            ->whereIn('family_member_id', $this->familyMemberIds)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Registered Beneficiary',
            'Relation to Beneficiary',
            'Age',
            'Birthday',
            'Gender',
            'Mobile Number',
            'Landline Number',
            'Email Address',
            'Current Address'
        ];
    }

    public function map($familyMember): array
    {
        return [
            $familyMember->first_name . ' ' . $familyMember->last_name,
            $familyMember->beneficiary->first_name . ' ' . $familyMember->beneficiary->last_name,
            $familyMember->relation_to_beneficiary ?? 'N/A',
            \Carbon\Carbon::parse($familyMember->birthday)->age ?? 'N/A',
            $familyMember->birthday ? \Carbon\Carbon::parse($familyMember->birthday)->format('m/d/Y') : 'N/A',
            $familyMember->gender ?? 'N/A',
            ($familyMember->mobile ? "'".$familyMember->mobile."'" : 'N/A'),
            $familyMember->landline ?? 'N/A',
            $familyMember->email ?? 'N/A',
            $familyMember->street_address ?? 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Get the highest row number
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
            
            // Style for all cells - UPDATED from F to J for all 11 columns
            'A1:J'.$highestRow => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'wrapText' => true,
                    'indent' => 1
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
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get worksheet
                $sheet = $event->sheet->getDelegate();
                
                // Apply striped rows for better readability - UPDATED from F to J
                $highestRow = $sheet->getHighestRow();
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
                
                // Adjust column widths - UPDATED to include all 11 columns with correct descriptions
                $event->sheet->getColumnDimension('A')->setWidth(25); // Full Name
                $event->sheet->getColumnDimension('B')->setWidth(25); // Registered Beneficiary
                $event->sheet->getColumnDimension('C')->setWidth(20); // Relation to Beneficiary
                $event->sheet->getColumnDimension('D')->setWidth(10); // Age
                $event->sheet->getColumnDimension('E')->setWidth(15); // Birthday
                $event->sheet->getColumnDimension('F')->setWidth(12); // Gender
                $event->sheet->getColumnDimension('G')->setWidth(15); // Mobile Number
                $event->sheet->getColumnDimension('H')->setWidth(15); // Landline Number
                $event->sheet->getColumnDimension('I')->setWidth(25); // Email Address
                $event->sheet->getColumnDimension('J')->setWidth(30); // Current Address
                
                // Add auto-filter - UPDATED from F to J
                $sheet->setAutoFilter('A1:J' . $highestRow);
                
                // Freeze the header row
                $sheet->freezePane('A2');

                // Format column H (Mobile Number) as text to prevent plus sign issues
$sheet->getStyle('H2:H'.$highestRow)->getNumberFormat()->setFormatCode('@');
            },
        ];
    }
}