<?php
// filepath: c:\xampp\htdocs\sulong_kalinga\app\Exports\AdministratorsExport.php

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

class AdministratorsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $administratorIds;

    public function __construct($administratorIds)
    {
        $this->administratorIds = $administratorIds;
    }

    public function collection()
    {
        return User::with(['organizationRole'])
            ->whereIn('id', $this->administratorIds)
            ->where('role_id', '1')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Full Name',
            'Organization Role',
            'Area',
            'Status',
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

    public function map($administrator): array
    {
        // Helper function to format area nicely
        $formatArea = function($area) {
            if (!$area) return 'N/A';
            // Convert snake_case to Title Case (e.g., luzon_region to Luzon Region)
            return ucwords(str_replace('_', ' ', $area));
        };

        return [
            $administrator->first_name . ' ' . $administrator->last_name,
            $administrator->organizationRole ? ucwords(str_replace('_', ' ', $administrator->organizationRole->role_name)) : 'N/A',
            $formatArea($administrator->organizationRole->area ?? null),
            $administrator->volunteer_status ?? 'N/A',
            \Carbon\Carbon::parse($administrator->birthday)->age . ' years old',
            \Carbon\Carbon::parse($administrator->birthday)->format('F j, Y') ?? 'N/A',
            $administrator->gender ?? 'N/A',
            $administrator->email ?? 'N/A',
            // Format mobile number with a single quote prefix to force Excel to treat it as text
            ($administrator->mobile ? "'".$administrator->mobile."'" : 'N/A'),
            $administrator->landline ?? 'N/A',
            $administrator->address ?? 'N/A',
            $administrator->nationality ?? 'N/A',
            $administrator->civil_status ?? 'N/A',
            // Format ID numbers with single quotes to prevent scientific notation
            ($administrator->sss_id_number ? "'".$administrator->sss_id_number."'" : 'N/A'),
            ($administrator->philhealth_id_number ? "'".$administrator->philhealth_id_number."'" : 'N/A'),
            ($administrator->pagibig_id_number ? "'".$administrator->pagibig_id_number."'" : 'N/A'),
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
            
            // Style for all cells - updated to include all columns A through P
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
                $sheet->getStyle('I2:I'.$highestRow)->getNumberFormat()->setFormatCode('@');
                // ID numbers (SSS, PhilHealth, Pag-IBIG)
                $sheet->getStyle('N2:P'.$highestRow)->getNumberFormat()->setFormatCode('@');
                
                // Apply striped rows for better readability
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
                
                // Adjust column widths
                $event->sheet->getColumnDimension('A')->setWidth(25); // Full Name
                $event->sheet->getColumnDimension('B')->setWidth(20); // Organization Role
                $event->sheet->getColumnDimension('C')->setWidth(15); // Area
                $event->sheet->getColumnDimension('D')->setWidth(15); // Status
                $event->sheet->getColumnDimension('E')->setWidth(10); // Age
                $event->sheet->getColumnDimension('F')->setWidth(15); // Birthday
                $event->sheet->getColumnDimension('G')->setWidth(10); // Gender
                $event->sheet->getColumnDimension('H')->setWidth(25); // Email
                $event->sheet->getColumnDimension('I')->setWidth(15); // Mobile
                $event->sheet->getColumnDimension('J')->setWidth(15); // Landline
                $event->sheet->getColumnDimension('K')->setWidth(30); // Address
                $event->sheet->getColumnDimension('L')->setWidth(15); // Nationality
                $event->sheet->getColumnDimension('M')->setWidth(15); // Civil Status
                $event->sheet->getColumnDimension('N')->setWidth(15); // SSS Number
                $event->sheet->getColumnDimension('O')->setWidth(15); // PhilHealth Number
                $event->sheet->getColumnDimension('P')->setWidth(15); // Pag-Ibig Number
                
                // Add auto-filter
                $sheet->setAutoFilter('A1:P' . $highestRow);
                
                // Freeze the header row
                $sheet->freezePane('A2');
            },
        ];
    }
}