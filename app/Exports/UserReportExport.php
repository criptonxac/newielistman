<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class UserReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect();
        
        // Add user info
        $rows->push([
            'Ma\'lumot Turi' => 'Foydalanuvchi Ma\'lumotlari',
            'Qiymat' => '',
            'Izoh' => ''
        ]);
        $rows->push([
            'Ma\'lumot Turi' => 'Ism',
            'Qiymat' => $this->data['user']->name,
            'Izoh' => ''
        ]);
        $rows->push([
            'Ma\'lumot Turi' => 'Email',
            'Qiymat' => $this->data['user']->email,
            'Izoh' => ''
        ]);
        $rows->push([
            'Ma\'lumot Turi' => 'Jami Urinishlar',
            'Qiymat' => $this->data['totalAttempts'],
            'Izoh' => ''
        ]);
        $rows->push([
            'Ma\'lumot Turi' => 'Umumiy O\'rtacha',
            'Qiymat' => number_format($this->data['overallAverage'], 1) . '%',
            'Izoh' => ''
        ]);
        
        // Add empty row
        $rows->push(['', '', '']);
        
        // Add skill averages
        $rows->push([
            'Ma\'lumot Turi' => 'Ko\'nikmalar O\'rtachasi',
            'Qiymat' => '',
            'Izoh' => ''
        ]);
        $rows->push([
            'Ma\'lumot Turi' => 'Listening',
            'Qiymat' => number_format($this->data['averages']['listening'], 1) . '%',
            'Izoh' => count($this->data['skillResults']['listening']) . ' ta test'
        ]);
        $rows->push([
            'Ma\'lumot Turi' => 'Reading',
            'Qiymat' => number_format($this->data['averages']['reading'], 1) . '%',
            'Izoh' => count($this->data['skillResults']['reading']) . ' ta test'
        ]);
        $rows->push([
            'Ma\'lumot Turi' => 'Writing',
            'Qiymat' => number_format($this->data['averages']['writing'], 1) . '%',
            'Izoh' => count($this->data['skillResults']['writing']) . ' ta test'
        ]);
        $rows->push([
            'Ma\'lumot Turi' => 'Speaking',
            'Qiymat' => number_format($this->data['averages']['speaking'], 1) . '%',
            'Izoh' => count($this->data['skillResults']['speaking']) . ' ta test'
        ]);
        
        // Add empty row
        $rows->push(['', '', '']);
        
        // Add detailed results for each skill
        foreach (['listening', 'reading', 'writing', 'speaking'] as $skill) {
            $skillName = ucfirst($skill);
            $rows->push([
                'Ma\'lumot Turi' => $skillName . ' Test Natijalari',
                'Qiymat' => '',
                'Izoh' => ''
            ]);
            
            if (count($this->data['skillResults'][$skill]) > 0) {
                foreach ($this->data['skillResults'][$skill] as $result) {
                    $rows->push([
                        'Ma\'lumot Turi' => $result['test_name'],
                        'Qiymat' => $result['score'] . '%',
                        'Izoh' => $result['date']
                    ]);
                }
            } else {
                $rows->push([
                    'Ma\'lumot Turi' => 'Test topshirilmagan',
                    'Qiymat' => '0%',
                    'Izoh' => '-'
                ]);
            }
            
            // Add empty row after each skill
            $rows->push(['', '', '']);
        }
        
        return $rows;
    }

    public function headings(): array
    {
        return [
            'Ma\'lumot Turi',
            'Qiymat',
            'Izoh'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // Style all cells
            'A:C' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return $this->data['user']->name . ' - Test Natijalari';
    }
}
