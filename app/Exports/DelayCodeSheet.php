<?php

namespace App\Exports;

use App\Models\DelayCode;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class DelayCodeSheet implements FromCollection, WithEvents, WithTitle
{
    public function title(): string { return 'Delay Code'; }
    public function collection(): Collection { return collect([]); }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ── Warna tab pink ───────────────────────────────────────────
                $sheet->getTabColor()->setRGB('FF69B4');

                // ── Baris 1: Referensi ───────────────────────────────────────
                $sheet->mergeCells('A1:C1');
                $sheet->setCellValue('A1', '*Reference - GOM Rev 07.2.0 Date 04-DEC-25 (3.6.5.(1))');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFF00']],
                ]);

                // ── Baris 2: Header ──────────────────────────────────────────
                $sheet->setCellValue('A2', 'Delay Code');
                $sheet->setCellValue('B2', 'Delay Reasons');
                $sheet->setCellValue('C2', 'Category');

                $sheet->getStyle('A2:C2')->applyFromArray([
                    'font'      => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // ── Data delay codes ─────────────────────────────────────────
                $delayCodes = DelayCode::with('category')->orderBy('code')->get();
                $row = 3;
                $prevCategory = null;
                $categoryStartRow = 3;

                foreach ($delayCodes as $dc) {
                    $sheet->setCellValue("A{$row}", $dc->code);
                    $sheet->setCellValue("B{$row}", $dc->description ?? $dc->reason ?? '');

                    $categoryName = $dc->category->name ?? '';

                    // Merge category cells jika sama
                    if ($categoryName !== $prevCategory) {
                        if ($prevCategory !== null && $row - 1 > $categoryStartRow) {
                            $sheet->mergeCells("C{$categoryStartRow}:C" . ($row - 1));
                        }
                        $sheet->setCellValue("C{$row}", $categoryName);
                        $categoryStartRow = $row;
                        $prevCategory = $categoryName;
                    }

                    $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    $row++;
                }

                // Merge category terakhir
                if ($row - 1 > $categoryStartRow) {
                    $sheet->mergeCells("C{$categoryStartRow}:C" . ($row - 1));
                }

                // ── Lebar kolom ──────────────────────────────────────────────
                $sheet->getColumnDimension('A')->setWidth(12);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(35);
            },
        ];
    }
}