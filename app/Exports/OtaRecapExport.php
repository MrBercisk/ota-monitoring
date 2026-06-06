<?php
namespace App\Exports;

use App\Models\Flight;
use App\Models\Station;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class OtaRecapExport implements
    FromCollection,
    WithHeadings,
    WithTitle,
    WithStyles,
    WithColumnWidths,
    WithEvents
{
    public function __construct(
        protected Carbon $startDate,
        protected Carbon $endDate,
    ) {}

    protected function getDates(): Collection
    {
        return collect(CarbonPeriod::create(
            $this->startDate->toDateString(),
            $this->endDate->toDateString()
        ))->map(fn(Carbon $d) => $d->toDateString());
    }

    protected function buildMatrix(): array
    {
        $raw = Flight::whereBetween('flight_date', [$this->startDate, $this->endDate])
            ->selectRaw('station_id, flight_date, COUNT(*) AS total, SUM(status = "on_time") AS on_time')
            ->groupBy('station_id', 'flight_date')
            ->get();

        $matrix           = [];
        $activeStationIds = $raw->pluck('station_id')->unique();

        foreach ($raw as $row) {
            $date = Carbon::parse($row->flight_date)->toDateString();
            $matrix[$row->station_id][$date] = [
                'total'   => (int) $row->total,
                'on_time' => (int) $row->on_time,
                'pct'     => $row->total > 0 ? round(($row->on_time / $row->total) * 100) : null,
            ];
        }

        return [$matrix, $activeStationIds];
    }

    public function headings(): array
    {
        $dates = $this->getDates();

        $head = ['Station', 'Station Name', 'Total Flight', 'On Time', 'Delayed', 'OTA %'];

        foreach ($dates as $date) {
            $head[] = Carbon::parse($date)->format('d-M-Y'); // e.g. 22-May-2026
        }

        return $head;
    }

    public function collection(): Collection
    {
        $dates              = $this->getDates();
        [$matrix, $activeIds] = $this->buildMatrix();

        $stations = Station::whereIn('id', $activeIds)->orderBy('code')->get();

        return $stations->map(function ($station) use ($dates, $matrix) {
            $stationData = $matrix[$station->id] ?? [];

            $total   = collect($stationData)->sum('total');
            $onTime  = collect($stationData)->sum('on_time');
            $delayed = $total - $onTime;
            $pct     = $total > 0 ? round(($onTime / $total) * 100) : 0;

            $row = [
                $station->code,
                $station->name,
                $total,
                $onTime,
                $delayed,
                $pct . '%',
            ];

            foreach ($dates as $date) {
                $dayPct = $stationData[$date]['pct'] ?? null;
                $row[]  = $dayPct !== null ? $dayPct . '%' : '-';
            }

            return $row;
        });
    }

    public function title(): string
    {
        return 'OTA Recap ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y');
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 10,  // Station code
            'B' => 22,  // Station name
            'C' => 14,  // Total Flight
            'D' => 10,  // On Time
            'E' => 10,  // Delayed
            'F' => 10,  // OTA %
        ];

        // Kolom tanggal mulai G
        $dates   = $this->getDates();
        $colIdx  = 7; // G = 7
        foreach ($dates as $date) {
            $col          = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
            $widths[$col] = 11;
            $colIdx++;
        }

        return $widths;
    }

    public function styles($sheet): array
    {
        $dates      = $this->getDates();
        $totalCols  = 6 + $dates->count();
        $lastCol    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);
        $lastRow    = 1 + Station::count() + 5; // buffer

        return [
            // Header row — background biru tua, teks putih, bold
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1F3864'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $dates      = $this->getDates();
                $totalCols  = 6 + $dates->count();
                $lastCol    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);
                $totalRows  = 1 + $sheet->getHighestRow();

                // Title di atas header
                $sheet->insertNewRowBefore(1, 2);
                $sheet->mergeCells('A1:' . $lastCol . '1');
                $sheet->setCellValue('A1',
                    'OTA RECAP ' .
                    strtoupper($this->startDate->format('d M Y')) .
                    ' — ' .
                    strtoupper($this->endDate->format('d M Y'))
                );
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1F3864']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(5); // spacer

                // Header row sekarang di row 3
                $headerRow = 3;
                $sheet->getStyle('A3:' . $lastCol . '3')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(20);

                // Data rows
                $dataStart = 4;
                $dataEnd   = $sheet->getHighestRow();

                for ($r = $dataStart; $r <= $dataEnd; $r++) {
                    $isEven = ($r - $dataStart) % 2 === 1;
                    $bgArgb = $isEven ? 'FFD9E1F2' : 'FFFFFFFF'; // stripe biru muda / putih

                    $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgArgb]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'font'      => ['size' => 10],
                    ]);

                    // Station code & name — left align
                    $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("A{$r}")->getFont()->setBold(true);

                    // Warna OTA % di kolom F berdasarkan nilai
                    $otaCell  = $sheet->getCell("F{$r}");
                    $otaValue = (int) str_replace('%', '', $otaCell->getValue());

                    $otaColor = match(true) {
                        $otaValue === 100 => 'FF375623', // hijau tua
                        $otaValue >= 80   => 'FF7F6000', // kuning tua
                        default           => 'FF843C0C', // merah tua
                    };
                    $otaBg = match(true) {
                        $otaValue === 100 => 'FFC6EFCE', // hijau muda
                        $otaValue >= 80   => 'FFFFEB9C', // kuning muda
                        default           => 'FFFFC7CE', // merah muda
                    };

                    $sheet->getStyle("F{$r}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['argb' => $otaColor]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $otaBg]],
                    ]);

                    // Warna kolom daily OTA (G dst)
                    for ($c = 7; $c <= $totalCols; $c++) {
                        $col      = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                        $cellVal  = $sheet->getCell("{$col}{$r}")->getValue();
                        if ($cellVal === '-') continue;

                        $dayPct = (int) str_replace('%', '', $cellVal);
                        $fgArgb = match(true) {
                            $dayPct === 100 => 'FF375623',
                            $dayPct >= 80   => 'FF7F6000',
                            default         => 'FF843C0C',
                        };
                        $bgArgbDay = match(true) {
                            $dayPct === 100 => 'FFC6EFCE',
                            $dayPct >= 80   => 'FFFFEB9C',
                            default         => 'FFFFC7CE',
                        };
                        $sheet->getStyle("{$col}{$r}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['argb' => $fgArgb]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgArgbDay]],
                        ]);
                    }

                    $sheet->getRowDimension($r)->setRowHeight(18);
                }

                // Border seluruh tabel
                $sheet->getStyle("A3:{$lastCol}{$dataEnd}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFB8CCE4'],
                        ],
                    ],
                ]);

                // Freeze pane: beku kolom A-B dan header row
                $sheet->freezePane('C4');

                // Auto filter di header
                $sheet->setAutoFilter("A3:{$lastCol}3");
            },
        ];
    }
}