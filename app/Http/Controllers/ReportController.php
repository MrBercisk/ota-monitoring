<?php
namespace App\Http\Controllers;

use App\Exports\OtaRecapExport;
use App\Models\Flight;
use App\Models\Station;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default: 7 hari terakhir
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->subDays(6)->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();

        // Batasi maksimal 31 hari agar chart tidak terlalu padat
        if ($startDate->diffInDays($endDate) > 30) {
            $endDate = $startDate->copy()->addDays(30)->endOfDay();
        }

        $stations = Station::orderBy('code')->get();

        // Semua tanggal dalam range (untuk label kolom)
        $period = CarbonPeriod::create($startDate->toDateString(), $endDate->toDateString());
        $dates = collect($period)->map(fn(Carbon $d) => $d->toDateString());

        // Query OTA per station per hari — satu query, efisien
        $raw = Flight::whereBetween('flight_date', [$startDate, $endDate])
            ->selectRaw('
                station_id,
                flight_date,
                COUNT(*)                    AS total,
                SUM(status = "on_time")     AS on_time
            ')
            ->groupBy('station_id', 'flight_date')
            ->get();

        // Struktur: [ station_id => [ date => pct ] ]
        $matrix = [];
        foreach ($raw as $row) {
            $date = Carbon::parse($row->flight_date)->toDateString();
            $pct  = $row->total > 0 ? round(($row->on_time / $row->total) * 100) : null;
            $matrix[$row->station_id][$date] = $pct;
        }

        // Hanya station yang punya data di range ini
        $activeStationIds = $raw->pluck('station_id')->unique()->values();
        $activeStations   = $stations->whereIn('id', $activeStationIds)->values();

        // Build dataset untuk Chart.js
        // Setiap station = satu dataset (line), setiap tanggal = satu point
        $chartDatasets = $activeStations->map(function ($station) use ($dates, $matrix) {
            $data = $dates->map(fn($date) => $matrix[$station->id][$date] ?? null)->values();
            return [
                'label' => $station->code,
                'data'  => $data,
            ];
        })->values();

        // ── Tabel Rekap Per Station ───────────────────────────────────
        $tableData = $activeStations->map(function ($station) use ($startDate, $endDate, $matrix, $dates) {
            $stationMatrix = $matrix[$station->id] ?? [];
            $dailyPct      = $dates->map(fn($d) => $stationMatrix[$d] ?? null);

            // Ambil aggregate langsung dari matrix (sudah ada datanya)
            $flightData = Flight::where('station_id', $station->id)
                ->whereBetween('flight_date', [$startDate, $endDate])
                ->selectRaw('COUNT(*) AS total, SUM(status = "on_time") AS on_time, SUM(status = "delayed") AS delayed_count')
                ->first();

            $total   = $flightData ? (int) $flightData->total         : 0;
            $onTime  = $flightData ? (int) $flightData->on_time       : 0;
            $delayed = $flightData ? (int) $flightData->delayed_count : 0;
            $pct     = $total > 0 ? round(($onTime / $total) * 100) : 0;

            return [
                'code'      => $station->code,
                'name'      => $station->name,
                'total'     => $total,
                'on_time'   => $onTime,
                'delayed'   => $delayed,
                'pct'       => $pct,
                'daily_pct' => $dailyPct, // untuk grouped bar per baris tabel
            ];
        })->filter(fn($s) => $s['total'] > 0)->values();

        return view('reports.index', compact(
            'startDate', 'endDate', 'dates',
            'activeStations', 'chartDatasets',
            'tableData', 'stations'
        ));
    }

  
    public function export(Request $request)
    {
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate   = Carbon::parse($request->end_date)->endOfDay();

        $period = CarbonPeriod::create($startDate->toDateString(), $endDate->toDateString());
        $dates = collect($period)->map(fn(Carbon $d) => $d->toDateString());

        // ── Query data ────────────────────────────────────────────────────
        $raw = Flight::whereBetween('flight_date', [$startDate, $endDate])
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

        $stations = Station::whereIn('id', $activeStationIds)->orderBy('code')->get();

        // ── Build spreadsheet ─────────────────────────────────────────────
        $spreadsheet = new Spreadsheet();

        // ── Sheet 1: DATA (hidden, sumber chart) ─────────────────────────
        $dataSheet = $spreadsheet->getActiveSheet();
        $dataSheet->setTitle('Data');

        // Header baris 1: Station | date1 | date2 | ...
        $dataSheet->setCellValue('A1', 'Station');
        foreach ($dates as $dIdx => $date) {
            $col = Coordinate::stringFromColumnIndex($dIdx + 2);
            $dataSheet->setCellValue("{$col}1", Carbon::parse($date)->format('d/m'));
        }

        // Data baris 2 dst: station code | pct per hari
        foreach ($stations as $sIdx => $station) {
            $row = $sIdx + 2;
            $dataSheet->setCellValue("A{$row}", $station->code);
            foreach ($dates as $dIdx => $date) {
                $col = Coordinate::stringFromColumnIndex($dIdx + 2);
                $pct = $matrix[$station->id][$date]['pct'] ?? 0;
                $dataSheet->setCellValue("{$col}{$row}", $pct ?? 0);
            }
        }

        $dataRowCount = $stations->count();
        $dataColCount = $dates->count();
        $lastDataCol  = Coordinate::stringFromColumnIndex($dataColCount + 1);

        // ── Sheet 2: CHART ────────────────────────────────────────────────
        $chartSheet = $spreadsheet->createSheet();
        $chartSheet->setTitle('OTA Chart');
        $chartSheet->setCellValue('S1', ' '); 

        // Palet warna per hari (mirip screenshot client)
        $palette = [
            '4472C4','ED7D31','A9A9A9','FFC000',
            '5B9BD5','70AD47','264478','9E480E',
            '636363','997300','255E91','43682B',
        ];

        // Build DataSeries — setiap dataset = satu tanggal
        $dataSeriesLabels = [];
        $xAxisTickValues  = null;
        $dataSeriesValues = [];

        // X axis = station names (A2:A{last}) dari sheet Data
        $xAxisTickValues = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            "Data!\$A\$2:\$A\$" . ($dataRowCount + 1),
            null,
            $dataRowCount
        );

        foreach ($dates as $dIdx => $date) {
            $col   = Coordinate::stringFromColumnIndex($dIdx + 2);
            $label = Carbon::parse($date)->format('d/m');

            // Label series (nama tanggal)
            $dataSeriesLabels[] = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                "Data!\${$col}\$1",
                null,
                1
            );

            // Data values per station untuk tanggal ini
            $dataSeriesValues[] = new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                "Data!\${$col}\$2:\${$col}\$" . ($dataRowCount + 1),
                null,
                $dataRowCount,
                [],
                null,
                $palette[$dIdx % count($palette)]
            );
            
        }

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, $dates->count() - 1),
            $dataSeriesLabels,
            [$xAxisTickValues],
            $dataSeriesValues
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        $plotArea = new PlotArea(null, [$series]);
        $legend   = new Legend(Legend::POSITION_TOP, null, false);

        $titleText = 'OTA RECAP ' .
            strtoupper($startDate->format('d M Y')) . ' — ' .
            strtoupper($endDate->format('d M Y'));

        $chart = new Chart(
            'OTA_Chart',
            new Title($titleText),
            $legend,
            $plotArea,
            true,
            0,
            null,
            null
        );

        // Posisi chart di sheet: A1 sampai P30 (lebar penuh)
        $chart->setTopLeftPosition('A1');
        $chart->setBottomRightPosition('R30');

        $chartSheet->addChart($chart);

        // ── Sheet 3: TABEL REKAP ──────────────────────────────────────────
        $tableSheet = $spreadsheet->createSheet();
        $tableSheet->setTitle('Rekap');

        // Title
        $lastTblCol = Coordinate::stringFromColumnIndex(5 + $dataColCount);
        $tableSheet->mergeCells("A1:{$lastTblCol}1");
        $tableSheet->setCellValue('A1', $titleText);
        $tableSheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF1F3864']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $tableSheet->getRowDimension(1)->setRowHeight(28);

        // Header row 3
        $headers = ['Station', 'Station Name', 'Total Flight', 'On Time', 'Delayed', 'OTA %'];
        foreach ($dates as $date) {
            $headers[] = Carbon::parse($date)->format('d-M');
        }
        foreach ($headers as $hIdx => $header) {
            $col = Coordinate::stringFromColumnIndex($hIdx + 1);
            $tableSheet->setCellValue("{$col}3", $header);
        }
        $tableSheet->getStyle("A3:{$lastTblCol}3")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F3864']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $tableSheet->getRowDimension(3)->setRowHeight(20);

        // Data rows mulai row 4
        foreach ($stations as $sIdx => $station) {
            $row         = $sIdx + 4;
            $stationData = $matrix[$station->id] ?? [];
            $total       = collect($stationData)->sum('total');
            $onTime      = collect($stationData)->sum('on_time');
            $delayed     = $total - $onTime;
            $pct         = $total > 0 ? round(($onTime / $total) * 100) : 0;

            $rowData = [$station->code, $station->name, $total, $onTime, $delayed, $pct . '%'];
            foreach ($dates as $date) {
                $dayPct    = $stationData[$date]['pct'] ?? null;
                $rowData[] = $dayPct !== null ? $dayPct . '%' : '-';
            }

            foreach ($rowData as $cIdx => $val) {
                $col = Coordinate::stringFromColumnIndex($cIdx + 1);
                $tableSheet->setCellValue("{$col}{$row}", $val);
            }

            // Stripe
            $bgArgb = $sIdx % 2 === 1 ? 'FFD9E1F2' : 'FFFFFFFF';
            $tableSheet->getStyle("A{$row}:{$lastTblCol}{$row}")->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgArgb]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'font'      => ['size' => 10],
            ]);
            $tableSheet->getStyle("A{$row}")->applyFromArray([
                'font'      => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ]);
            $tableSheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Warna OTA %
            [$fgArgb, $bgOta] = match(true) {
                $pct === 100 => ['FF375623', 'FFC6EFCE'],
                $pct >= 80   => ['FF7F6000', 'FFFFEB9C'],
                default      => ['FF843C0C', 'FFFFC7CE'],
            };
            $tableSheet->getStyle("F{$row}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => $fgArgb]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgOta]],
            ]);

            // Warna kolom daily
            foreach ($dates as $dIdx => $date) {
                $col    = Coordinate::stringFromColumnIndex($dIdx + 7);
                $dayPct = $stationData[$date]['pct'] ?? null;
                if ($dayPct === null) continue;
                [$fg, $bg] = match(true) {
                    $dayPct === 100 => ['FF375623', 'FFC6EFCE'],
                    $dayPct >= 80   => ['FF7F6000', 'FFFFEB9C'],
                    default         => ['FF843C0C', 'FFFFC7CE'],
                };
                $tableSheet->getStyle("{$col}{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => $fg]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                ]);
            }
            $tableSheet->getRowDimension($row)->setRowHeight(18);
        }

        // Border tabel
        $lastDataRow = 3 + $stations->count();
        $tableSheet->getStyle("A3:{$lastTblCol}{$lastDataRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFB8CCE4']]],
        ]);

        // Column widths
        $tableSheet->getColumnDimension('A')->setWidth(10);
        $tableSheet->getColumnDimension('B')->setWidth(24);
        $tableSheet->getColumnDimension('C')->setWidth(13);
        $tableSheet->getColumnDimension('D')->setWidth(10);
        $tableSheet->getColumnDimension('E')->setWidth(10);
        $tableSheet->getColumnDimension('F')->setWidth(10);
        for ($i = 0; $i < $dataColCount; $i++) {
            $col = Coordinate::stringFromColumnIndex($i + 7);
            $tableSheet->getColumnDimension($col)->setWidth(11);
        }

        $tableSheet->freezePane('C4');
        $tableSheet->setAutoFilter("A3:{$lastTblCol}3");

        // Aktifkan sheet Chart sebagai sheet pertama yang dilihat
        $spreadsheet->setActiveSheetIndex(1);

        // ── Output ────────────────────────────────────────────────────────
        $filename = 'OTA_Recap_' . $startDate->format('dMY') . '_' . $endDate->format('dMY') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);  // ← WAJIB agar chart ikut tersimpan

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}