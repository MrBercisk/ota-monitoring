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
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
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

    public function title(): string { return 'OTA RECAP'; }
    public function collection(): Collection { return collect([]); }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getTabColor()->setRGB('1F497D');

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

                // lastCol = kolom A (index 1) + jumlah hari
                $lastCol = Coordinate::stringFromColumnIndex(count($dates) + 1);

                // ── Baris 1: Judul ───────────────────────────────────────────
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
                    $col = Coordinate::stringFromColumnIndex($i + 2);
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

                    $isNoop = true;

                    foreach ($dates as $i => $dateLabel) {
                        $col  = Coordinate::stringFromColumnIndex($i + 2);
                        $date = Carbon::createFromFormat('d/m', $dateLabel)->year(Carbon::parse($this->dateFrom)->year);

                        $flights = Flight::where('station_id', $station->id)
                            ->whereDate('flight_date', $date->format('Y-m-d'))
                            ->get();

                        $counted = $flights->filter(fn($f) => !in_array($f->status, ['night_stop', 'noop']));
                        $total   = $counted->count();

                        if ($total > 0) $isNoop = false;

                        if ($total === 0) {
                            $pct = 0;
                        } else {
                            $lt15   = $counted->filter(fn($f) => $f->status === 'delayed' && $f->delay_minutes <= 15)->count();
                            $onTime = $counted->filter(fn($f) => $f->status === 'on_time')->count();
                            $pct    = round(($onTime + $lt15) / $total * 100);
                        }

                        $sheet->setCellValue("{$col}{$row}", $pct / 100);
                        $sheet->getStyle("{$col}{$row}")->getNumberFormat()->setFormatCode('0%');

                        $bgColor   = match (true) {
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

                    // Label NOOP di kolom setelah lastCol
                    if ($isNoop) {
                        $noopCol = Coordinate::stringFromColumnIndex(count($dates) + 2);
                        $sheet->setCellValue("{$noopCol}{$row}", 'NOOP');
                    }

                    $row++;
                }

                // ── Lebar kolom ──────────────────────────────────────────────
                $sheet->getColumnDimension('A')->setWidth(10);
                foreach ($dates as $i => $_) {
                    $col = Coordinate::stringFromColumnIndex($i + 2);
                    $sheet->getColumnDimension($col)->setWidth(8);
                }

                // ── Buat Chart ───────────────────────────────────────────────
                $this->addChart($sheet, $dates, $row);

                // ── Print setup ──────────────────────────────────────────────
                $ps = $sheet->getPageSetup();
                $ps->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $ps->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $ps->setFitToWidth(1);
            },
        ];
    }

    private function addChart($sheet, array $dates, int $dataEndRow): void
    {
        $stationCount = $dataEndRow - 4;
        $lastDataRow  = $dataEndRow - 1;

        $xAxisLabels = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            'SUMMARY!$A$4:$A$' . $lastDataRow,
            null,
            $stationCount
        );

        $seriesLabels = [];
        $seriesData   = [];
        foreach ($dates as $i => $dateLabel) {
            $col = Coordinate::stringFromColumnIndex($i + 2); 

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
        $title    = new Title(
            'OTA RECAP ' . Carbon::parse($this->dateFrom)->format('d') . '-' . Carbon::parse($this->dateTo)->format('dMY')
        );

        $chart = new Chart('ota_chart', $title, $legend, $plotArea);

        $chartStartRow  = $dataEndRow + 2;
        $chartEndCol    = Coordinate::stringFromColumnIndex(count($dates) + 6);
        $chart->setTopLeftPosition('A' . $chartStartRow);
        $chart->setBottomRightPosition("{$chartEndCol}" . ($chartStartRow + 20));

        $sheet->addChart($chart);
    }

    private function style($sheet, string $range, array $opt): void
    {
        $s = [];

        if (isset($opt['font'])) {
            $f = $opt['font'];
            $s['font'] = array_filter([
                'bold'   => $f['bold']   ?? null,
                'italic' => $f['italic'] ?? null,
                'size'   => $f['size']   ?? null,
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