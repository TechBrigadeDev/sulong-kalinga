<?php

namespace App\Exports;

use App\Models\Beneficiary;
use App\Models\User;
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

class BeneficiariesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $beneficiaryIds;

    public function __construct($beneficiaryIds)
    {
        $this->beneficiaryIds = $beneficiaryIds;
    }

    public function collection()
    {
        return Beneficiary::with([
            'category', 
            'barangay', 
            'municipality', 
            'status',
            'generalCarePlan.careWorkerResponsibility.careWorker' // Add this relationship
        ])->whereIn('beneficiary_id', $this->beneficiaryIds)->get();
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Category',
            'Status',
            'Barangay',
            'Municipality',
            'Primary Caregiver',
            'Age',
            'Birth Date',
            'Gender',
            'Civil Status',
            'Mobile',
            'Landline',
            'Address',
            'Emergency Contact',
            'Emergency Contact Relation',
            'Emergency Contact Number',
            'Emergency Contact Email',
            'Assigned Care Worker',
            'Care Worker Contact'
        ];
    }

    public function map($beneficiary): array
    {
        // Get the first care worker responsibility for each general care plan
        $careWorker = null;
        $careWorkerContact = 'N/A';
        
        if ($beneficiary->generalCarePlan) {
            $careWorkerResponsibility = $beneficiary->generalCarePlan->careWorkerResponsibility->first();
            $careWorker = $careWorkerResponsibility ? $careWorkerResponsibility->careWorker : null;
            $careWorkerContact = $careWorker ? "'".$careWorker->mobile."'" : 'N/A'; // Add single quote prefix        
            }

        return [
            $beneficiary->first_name . ' ' . $beneficiary->last_name,
            $beneficiary->category->category_name ?? 'N/A',
            $beneficiary->status->status_name ?? 'N/A',
            $beneficiary->barangay->barangay_name ?? 'N/A',
            $beneficiary->municipality->municipality_name ?? 'N/A',
            $beneficiary->primary_caregiver ?? 'N/A',
            \Carbon\Carbon::parse($beneficiary->birthday)->age ?? 'N/A',
            $beneficiary->birthday ? \Carbon\Carbon::parse($beneficiary->birthday)->format('m/d/Y') : 'N/A',
            $beneficiary->gender ?? 'N/A',
            $beneficiary->civil_status ?? 'N/A',
            ($beneficiary->mobile ? "'".$beneficiary->mobile."'" : 'N/A'),            $beneficiary->landline ?? 'N/A',
            $beneficiary->street_address ?? 'N/A',
            $beneficiary->emergency_contact_name ?? 'N/A',
            $beneficiary->emergency_contact_relation ?? 'N/A',
            ($beneficiary->emergency_contact_mobile ? "'".$beneficiary->emergency_contact_mobile."'" : 'N/A'),
            $beneficiary->emergency_contact_email ?? 'N/A',
            $careWorker ? $careWorker->first_name . ' ' . $careWorker->last_name : 'Not Assigned', // Add care worker name
            $careWorkerContact // Add care worker contact
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
            
            // Style for all cells - update range to cover all 19 columns (A-S)
            'A1:S'.$highestRow => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'wrapText' => true,
                    'indent' => 1 // This adds space at the start (padding)
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
                
                // Apply striped rows for better readability - update range to cover all columns
                $highestRow = $sheet->getHighestRow();
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A'.$row.':S'.$row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F5F5F5'] // Light grey for even rows
                            ]
                        ]);
                    }
                }
                
                // Set row height for better padding
                $sheet->getDefaultRowDimension()->setRowHeight(22);
                $sheet->getRowDimension(1)->setRowHeight(26); // Make header slightly taller
                
                // Adjust column widths for all columns
                $event->sheet->getColumnDimension('A')->setWidth(25); // Full Name
                $event->sheet->getColumnDimension('B')->setWidth(15); // Category
                $event->sheet->getColumnDimension('C')->setWidth(15); // Status
                $event->sheet->getColumnDimension('D')->setWidth(20); // Barangay
                $event->sheet->getColumnDimension('E')->setWidth(20); // Municipality
                $event->sheet->getColumnDimension('F')->setWidth(20); // Primary Caregiver
                $event->sheet->getColumnDimension('G')->setWidth(10); // Age
                $event->sheet->getColumnDimension('H')->setWidth(15); // Birth Date
                $event->sheet->getColumnDimension('I')->setWidth(12); // Gender
                $event->sheet->getColumnDimension('J')->setWidth(15); // Civil Status
                $event->sheet->getColumnDimension('K')->setWidth(15); // Mobile
                $event->sheet->getColumnDimension('L')->setWidth(15); // Landline
                $event->sheet->getColumnDimension('M')->setWidth(30); // Address
                $event->sheet->getColumnDimension('N')->setWidth(20); // Emergency Contact
                $event->sheet->getColumnDimension('O')->setWidth(20); // Emergency Contact Relation
                $event->sheet->getColumnDimension('P')->setWidth(20); // Emergency Contact Number
                $event->sheet->getColumnDimension('Q')->setWidth(20); // Emergency Contact Email
                $event->sheet->getColumnDimension('R')->setWidth(25); // Assigned Care Worker
                $event->sheet->getColumnDimension('S')->setWidth(15); // Care Worker Contact
                
                // Add auto-filter - update range to cover all columns
                $lastRow = $sheet->getHighestRow();
                $sheet->setAutoFilter('A1:S' . $lastRow);
                
                // Add a bit of top/bottom padding using margin
                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setBottom(0.5);
                
                // Freeze the header row
                $sheet->freezePane('A2');

                // Format columns with mobile numbers as text
$sheet->getStyle('K2:K'.$highestRow)->getNumberFormat()->setFormatCode('@');
$sheet->getStyle('P2:P'.$highestRow)->getNumberFormat()->setFormatCode('@');
$sheet->getStyle('S2:S'.$highestRow)->getNumberFormat()->setFormatCode('@');
            },
        ];
    }
}