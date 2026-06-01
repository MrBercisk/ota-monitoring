<?php

namespace App\Exports;

use App\Models\Flight;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Events\AfterSheet;

class OtaSummarySheet implements FromCollection, WithEvents, WithTitle
{
    protected string $type;
    protected string $dateFrom;
    protected string $dateTo;
    protected        $stations;

    const WHITE    = 'FFFFFFFF';
    const BLUE_HDR = 'FF4472C4';
    const BLUE_TTL = 'FF1F497D';
    const RED      = 'FFFF0000';
    const YELLOW   = 'FFFFFF00';
    const GREEN    = 'FF00B050';

    public function __construct(string $type, string $dateFrom, string $dateTo, $stations)
    {
        $this->type     = $type;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
        $this->stations = $stations;
    }

    public function title(): string { return 'SUMMARY'; }
    public function collection(): Collection { return collect([]); }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Kumpulkan semua tanggal dalam range
                $dates = [];
                $current = Carbon::parse($this->dateFrom);
                $end     = Carbon::parse($this->dateTo);
                while ($current->lte($end)) {
                    $dates[] = $current->format('d/m');
                    $current->addDay();
                }

                $dateFrom = Carbon::parse($this->dateFrom);
                $dateTo   = Carbon::parse($this->dateTo);
                $title    = 'OTA RECAP ' . strtoupper($dateFrom->format('d')) . '-' . strtoupper($dateTo->format('dMY')) . ' ' . $dateTo->year;

                // ── Baris 1: Judul ───────────────────────────────────────────
                $lastCol = chr(65 + count($dates)); // B + jumlah hari
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->setCellValue('A1', $title);
                $this->style($sheet, "A1:{$lastCol}1", [
                    'font'      => ['bold' => true, 'size' => 13, 'italic' => true],
                    'alignment' => Alignment::HORIZONTAL_CENTER,
                ]);

                // ── Baris 2: Label DATE ──────────────────────────────────────
                $sheet->mergeCells("B2:{$lastCol}2");
                $sheet->setCellValue('B2', 'DATE');
                $this->style($sheet, "B2:{$lastCol}2", [
                    'font'      => ['bold' => true],
                    'alignment' => Alignment::HORIZONTAL_CENTER,
                    'border'    => true,
                ]);

                // ── Baris 3: Header tanggal ──────────────────────────────────
                $sheet->setCellValue('A3', 'STATION');
                $this->style($sheet, 'A3', [
                    'font'      => ['bold' => true],
                    'alignment' => Alignment::HORIZONTAL_CENTER,
                    'border'    => true,
                ]);

                foreach ($dates as $i => $dateLabel) {
                    $col = chr(66 + $i); // B, C, D, ...
                    $sheet->setCellValue("{$col}3", $dateLabel);
                    $this->style($sheet, "{$col}3", [
                        'font'      => ['bold' => true],
                        'alignment' => Alignment::HORIZONTAL_CENTER,
                        'border'    => true,
                    ]);
                }

                // ── Data per station ─────────────────────────────────────────
                $row = 4;
                foreach ($this->stations as $station) {
                    $sheet->setCellValue("A{$row}", $station->code);
                    $this->style($sheet, "A{$row}", [
                        'font'      => ['bold' => true],
                        'alignment' => Alignment::HORIZONTAL_CENTER,
                        'border'    => true,
                    ]);

                    $isNoop    = true; // anggap NOOP dulu, buktikan sebaliknya
                    $noopCheck = true;

                    foreach ($dates as $i => $dateLabel) {
                        $col  = chr(66 + $i);
                        $date = Carbon::createFromFormat('d/m', $dateLabel)->year(Carbon::parse($this->dateFrom)->year);

                        $flights = Flight::where('station_id', $station->id)
                            ->whereDate('flight_date', $date->format('Y-m-d'))
                            ->get();

                        $counted = $flights->filter(fn($f) => !in_array($f->status, ['night_stop', 'noop']));
                        $total   = $counted->count();

                        if ($total > 0) $isNoop = false;

                        if ($total === 0) {
                            // Cek apakah semua NOOP
                            $allNoop = $flights->every(fn($f) => $f->status === 'noop');
                            $pct     = 0;
                            $display = '0%';
                        } else {
                            $lt15    = $counted->filter(fn($f) => $f->status === 'delayed' && $f->delay_minutes <= 15)->count();
                            $onTime  = $counted->filter(fn($f) => $f->status === 'on_time')->count();
                            $pct     = round(($onTime + $lt15) / $total * 100);
                            $display = $pct . '%';
                        }

                        $numVal = $pct / 100;
                        $sheet->setCellValue("{$col}{$row}", $numVal);
                        $sheet->getStyle("{$col}{$row}")->getNumberFormat()->setFormatCode('0%');

                        // Warna cell berdasarkan %
                        $bgColor = match (true) {
                            $pct === 100 => self::WHITE,
                            $pct === 0   => self::RED,
                            default      => self::YELLOW,
                        };

                        $fontColor = $pct === 0 ? self::WHITE : 'FF000000';

                        $this->style($sheet, "{$col}{$row}", [
                            'fill'      => $bgColor,
                            'font'      => ['bold' => true, 'color' => $fontColor],
                            'alignment' => Alignment::HORIZONTAL_RIGHT,
                            'border'    => true,
                        ]);
                    }

                    // Label NOOP di sebelah kanan jika station selalu 0 flight
                    if ($isNoop) {
                        $noopCol = chr(66 + count($dates));
                        $sheet->setCellValue("{$noopCol}{$row}", 'NOOP');
                    }

                    $row++;
                }

                // ── Lebar kolom ──────────────────────────────────────────────
                $sheet->getColumnDimension('A')->setWidth(10);
                foreach ($dates as $i => $_) {
                    $sheet->getColumnDimension(chr(66 + $i))->setWidth(8);
                }

                // ── Buat Chart ───────────────────────────────────────────────
                $this->addChart($sheet, $dates, $row);

                // Print setup
                $ps = $sheet->getPageSetup();
                $ps->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $ps->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $ps->setFitToWidth(1);
            },
        ];
    }

    private function addChart($sheet, array $dates, int $dataEndRow): void
    {
        // Label sumbu X = nama station (kolom A, baris 4 sampai dataEndRow-1)
        $stationCount = $dataEndRow - 4;
        $lastDataRow  = $dataEndRow - 1;

        $xAxisLabels = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            'SUMMARY!$A$4:$A$' . $lastDataRow,
            null,
            $stationCount
        );

        $dataSeries = [];
        foreach ($dates as $i => $dateLabel) {
            $col = chr(66 + $i);

            // Konversi nilai % ke angka (hapus %)
            // Chart pakai referensi cell langsung

            $seriesLabel = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                'SUMMARY!$' . $col . '$3',
                null,
                1
            );

            $seriesData = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'SUMMARY!$' . $col . '$4:$' . $col . '$' . $lastDataRow,
                null,
                $stationCount
            );

            $dataSeries[] = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                [$i],
                [$seriesLabel],
                [$xAxisLabels],
                [$seriesData]
            );
        }

        // Gabung semua series jadi 1 DataSeries
        $seriesLabels = [];
        $seriesData   = [];
        foreach ($dates as $i => $dateLabel) {
            $col = chr(66 + $i);
            $seriesLabels[] = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                'SUMMARY!$' . $col . '$3',
                null,
                1
            );
            $seriesData[] = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                'SUMMARY!$' . $col . '$4:$' . $col . '$' . $lastDataRow,
                null,
                $stationCount
            );
        }

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($dates) - 1),
            $seriesLabels,
            [$xAxisLabels],
            $seriesData
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        $plotArea = new PlotArea(null, [$series]);
        $legend   = new Legend(Legend::POSITION_TOP, null, false);
        $title    = new Title('OTA RECAP ' . Carbon::parse($this->dateFrom)->format('d') . '-' . Carbon::parse($this->dateTo)->format('dMY'));

        $chart = new Chart(
            'ota_chart',
            $title,
            $legend,
            $plotArea
        );

        // Posisi chart di bawah tabel
        $chartStartRow = $dataEndRow + 2;
        $chart->setTopLeftPosition('A' . $chartStartRow);
        $chart->setBottomRightPosition(chr(65 + count($dates) + 5) . ($chartStartRow + 20));

        $sheet->addChart($chart);
    }

    private function style($sheet, string $range, array $opt): void
    {
        $s = [];

        if (isset($opt['font'])) {
            $f = $opt['font'];
            $s['font'] = array_filter([
                'bold'   => $f['bold'] ?? null,
                'italic' => $f['italic'] ?? null,
                'size'   => $f['size'] ?? null,
                'name'   => 'Arial',
                'color'  => isset($f['color']) ? ['argb' => $f['color']] : null,
            ]);
        }

        if (isset($opt['fill'])) {
            $s['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $opt['fill']]];
        }

        if (isset($opt['alignment'])) {
            $s['alignment'] = [
                'horizontal' => $opt['alignment'],
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => false,
            ];
        }

        if (!empty($opt['border'])) {
            $s['borders'] = ['allBorders' => ['borderStyle' => Border::BORDER_THIN]];
        }

        $sheet->getStyle($range)->applyFromArray($s);
    }
}