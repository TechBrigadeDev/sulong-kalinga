<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Exports\CareworkersExport.php

namespace App\Exports;

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

class CareworkersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $careworkerIds;

    public function __construct($careworkerIds)
    {
        $this->careworkerIds = $careworkerIds;
    }

    public function collection()
    {
        return User::with(['barangay', 'municipality'])
            ->whereIn('id', $this->careworkerIds)
            ->where('role_id', '3')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Status',
            'Assigned Municipality',
            'Assigned Care Manager',
            'Age',
            'Birthday',
            'Gender',
            'Email',
            'Mobile',
            'Landline',
            'Address',
            'Nationality',
            'Civil Status',
            'SSS Number',
            'PhilHealth Number',
            'Pag-Ibig Number',
        ];
    }

    public function map($careworker): array
    {
        return [
            $careworker->first_name . ' ' . $careworker->last_name,
            $careworker->volunteer_status ?? 'N/A',
            $careworker->municipality->municipality_name ?? 'N/A',
            $careworker->assignedCareManager ? $careworker->assignedCareManager->first_name . ' ' . $careworker->assignedCareManager->last_name : 'Unassigned',
            \Carbon\Carbon::parse($careworker->birthday)->age . ' years old',
            \Carbon\Carbon::parse($careworker->birthday)->format('F j, Y') ?? 'N/A',
            $careworker->gender ?? 'N/A',
            $careworker->email ?? 'N/A',
            // Format mobile number with a single quote prefix to force Excel to treat it as text
            ($careworker->mobile ? "'".$careworker->mobile."'" : 'N/A'),
            $careworker->landline ?? 'N/A',
            $careworker->address ?? 'N/A',
            $careworker->nationality ?? 'N/A',
            $careworker->civil_status ?? 'N/A',
            // Format ID numbers with single quotes to prevent scientific notation
            ($careworker->sss_id_number ? "'".$careworker->sss_id_number."'" : 'N/A'),
            ($careworker->philhealth_id_number ? "'".$careworker->philhealth_id_number."'" : 'N/A'),
            ($careworker->pagibig_id_number ? "'".$careworker->pagibig_id_number."'" : 'N/A'),
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
            
            // Style for all cells - updated to include all columns A through O
            'A1:P'.$highestRow => [
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
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Format columns with numbers as text to prevent scientific notation
                // Mobile numbers
                $sheet->getStyle('H2:H'.$highestRow)->getNumberFormat()->setFormatCode('@');
                // ID numbers (SSS, PhilHealth, Pag-IBIG)
                $sheet->getStyle('M2:P'.$highestRow)->getNumberFormat()->setFormatCode('@');
                
                // Apply striped rows for better readability - updated to include all columns
                for ($row = 2; $row <= $highestRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle('A'.$row.':P'.$row)->applyFromArray([
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
                
                // Adjust column widths - updated to include all columns
                $event->sheet->getColumnDimension('A')->setWidth(25); // Full Name
                $event->sheet->getColumnDimension('B')->setWidth(15); // Status 
                $event->sheet->getColumnDimension('C')->setWidth(15); // Assigned Municipality
                $event->sheet->getColumnDimension('D')->setWidth(10); // Age
                $event->sheet->getColumnDimension('E')->setWidth(15); // Birthday
                $event->sheet->getColumnDimension('F')->setWidth(10); // Gender
                $event->sheet->getColumnDimension('G')->setWidth(25); // Email
                $event->sheet->getColumnDimension('H')->setWidth(15); // Mobile
                $event->sheet->getColumnDimension('I')->setWidth(15); // Landline
                $event->sheet->getColumnDimension('J')->setWidth(30); // Address
                $event->sheet->getColumnDimension('K')->setWidth(15); // Nationality
                $event->sheet->getColumnDimension('L')->setWidth(15); // Civil Status
                $event->sheet->getColumnDimension('M')->setWidth(15); // SSS Number
                $event->sheet->getColumnDimension('N')->setWidth(15); // PhilHealth Number
                $event->sheet->getColumnDimension('O')->setWidth(15); // Pag-Ibig Number
                
                // Add auto-filter - updated to include all columns
                $sheet->setAutoFilter('A1:P' . $highestRow);
                
                // Freeze the header row
                $sheet->freezePane('A2');
            },
        ];
    }
}