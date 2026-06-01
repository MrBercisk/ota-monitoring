<?php

namespace App\Exports;

use App\Models\Flight;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class OtaStationSheet implements FromCollection, WithEvents, WithTitle
{
    protected string  $type;
    protected string  $dateFrom;
    protected string  $dateTo;
    protected Station $station;

    // Warna persis dari gambar
    const WHITE      = 'FFFFFFFF';
    const BLACK      = 'FF000000';
    const BLUE_HDR   = 'FF4472C4';   // header kolom kiri
    const BLUE_TITLE = 'FF9DC3E6';   // judul station (biru muda)
    const PEACH      = 'FFF4B183';   // header recap (salmon/peach)
    const GREEN_HDR  = 'FF00B050';   // OTA <15Mins
    const RED_HDR    = 'FFFF0000';   // OTA >15Mins
    const YELLOW_ROW = 'FFFFFF00';   // delayed >15 menit (baris & total delay)
    const GREEN_ROW  = 'FF92D050';   // delayed ≤15 menit
    const PINK_DELAY = 'FFFFC0CB';   // total delay ≤15 menit (pink)
    const RED_NOOP   = 'FFFF0000';   // NOOP
    const ORANGE_DLY = 'FFFFC000';   // warna teks delay di status

    public function __construct(string $type, string $dateFrom, string $dateTo, Station $station)
    {
        $this->type     = $type;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
        $this->station  = $station;
    }

    public function title(): string
    {
        return substr(preg_replace('/[\/\\\?\*\[\]:]/', '', $this->station->code), 0, 31);
    }

    public function collection(): Collection { return collect([]); }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $flights = Flight::with('station')
                    ->where('station_id', $this->station->id)
                    ->whereDate('flight_date', '>=', $this->dateFrom)
                    ->whereDate('flight_date', '<=', $this->dateTo)
                    ->orderBy('flight_date')
                    ->orderBy('sta')
                    ->get();

                $grouped = $flights->groupBy(fn($f) => $f->flight_date->format('d/m'));

                // ── Baris 1: Judul station ───────────────────────────────────
                $stationLabel = $this->station->code . ' STATION';

                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', $stationLabel);
                $this->applyStyle($sheet, 'A1:J1', [
                    'font'      => ['bold' => true, 'size' => 12, 'color' => self::BLACK],
                    'fill'      => self::BLUE_TITLE,
                    'halign'    => Alignment::HORIZONTAL_CENTER,
                    'border'    => true,
                ]);

                // Recap judul di kanan
                $sheet->mergeCells('L1:P1');
                $sheet->setCellValue('L1', $stationLabel);
                $this->applyStyle($sheet, 'L1:P1', [
                    'font'      => ['bold' => true, 'size' => 12, 'color' => self::BLACK],
                    'fill'      => self::BLUE_TITLE,
                    'halign'    => Alignment::HORIZONTAL_CENTER,
                    'border'    => true,
                ]);

                $sheet->mergeCells('L2:P2');
                $sheet->setCellValue('L2', 'RECAP OTA');
                $this->applyStyle($sheet, 'L2:P2', [
                    'font'      => ['bold' => true, 'color' => self::BLACK],
                    'fill'      => self::PEACH,
                    'halign'    => Alignment::HORIZONTAL_CENTER,
                    'border'    => true,
                ]);

                // ── Baris 2: Header kolom data ───────────────────────────────
                $headers = [
                    'A' => 'DATE',
                    'B' => "FLIGHT\nNO",
                    'C' => 'STA',
                    'D' => 'STD',
                    'E' => 'ATA',
                    'F' => 'ATD',
                    'G' => "ACTUAL\nTAT",
                    'H' => "DELAY\nCODE",
                    'I' => "TOTAL\nDELAY",
                    'J' => "ON TIME\nDELAYED",
                ];
                foreach ($headers as $col => $label) {
                    $sheet->setCellValue("{$col}2", $label);
                }
                $this->applyStyle($sheet, 'A2:J2', [
                    'font'      => ['bold' => true, 'color' => self::WHITE],
                    'fill'      => self::BLUE_HDR,
                    'halign'    => Alignment::HORIZONTAL_CENTER,
                    'valign'    => Alignment::VERTICAL_CENTER,
                    'wrap'      => true,
                    'border'    => true,
                ]);
                // Garis bawah ON TIME / DELAYED
                $sheet->getStyle('J2')->getFont()->setUnderline(true);

                // ── Baris 3: Header recap ────────────────────────────────────
                $recapCols = ['L' => 'DATE', 'M' => 'OTA < 15Mins', 'N' => 'OTA >15Mins', 'O' => 'ON TIME', 'P' => '%'];
                foreach ($recapCols as $col => $label) {
                    $sheet->setCellValue("{$col}3", $label);
                }
                $this->applyStyle($sheet, 'L3:P3', [
                    'font'      => ['bold' => true, 'color' => self::WHITE],
                    'fill'      => self::PEACH,
                    'halign'    => Alignment::HORIZONTAL_CENTER,
                    'border'    => true,
                ]);
                // OTA <15 hijau
                $this->applyStyle($sheet, 'M3', ['fill' => self::GREEN_HDR, 'font' => ['bold' => true, 'color' => self::WHITE]]);
                // OTA >15 merah
                $this->applyStyle($sheet, 'N3', ['fill' => self::RED_HDR,   'font' => ['bold' => true, 'color' => self::WHITE]]);

                // ── Tulis data ───────────────────────────────────────────────
                $row      = 3;
                $recapRow = 4;

                foreach ($grouped as $dateLabel => $dayFlights) {
                    $startRow = $row;

                    foreach ($dayFlights as $flight) {
                        // Cek apakah flight punya delay_code ganda (RA + AT)
                        // Kita simulasikan: jika ada remarks delay_code berisi koma atau multi
                        $delayCodes = $flight->delay_code
                            ? array_filter(array_map('trim', explode(',', $flight->delay_code)))
                            : [''];

                        $firstCode = true;
                        foreach ($delayCodes as $code) {
                            // DATE hanya di baris pertama flight pertama
                            if ($firstCode) {
                                $sheet->setCellValue("B{$row}", $flight->flight_number);
                                $sheet->setCellValue("C{$row}", substr($flight->sta ?? '', 0, 5));
                                $sheet->setCellValue("D{$row}", substr($flight->std ?? '', 0, 5));
                                $sheet->setCellValue("E{$row}", $flight->ata ? substr($flight->ata, 0, 5) : '');
                                $sheet->setCellValue("F{$row}", $flight->atd ? substr($flight->atd, 0, 5) : '');

                                // Actual TAT
                                if ($flight->ata && $flight->atd) {
                                    $tat = Carbon::parse($flight->atd)->diffInMinutes(Carbon::parse($flight->ata));
                                    $sheet->setCellValue("G{$row}", sprintf('%d:%02d', intdiv($tat, 60), $tat % 60));
                                } elseif ($flight->status === 'night_stop') {
                                    $sheet->setCellValue("G{$row}", 'Night Stop');
                                }
                            }

                            // Delay code per baris
                            $sheet->setCellValue("H{$row}", $code);

                            // Total delay
                            if ($flight->delay_minutes > 0) {
                                $dm = $flight->delay_minutes;
                                $delayDisplay = sprintf('%d:%02d', intdiv($dm, 60), $dm % 60);
                                $sheet->setCellValue("I{$row}", $delayDisplay);

                                // Pink jika ≤15, kuning jika >15
                                $delayBg = $dm > 15 ? self::YELLOW_ROW : self::PINK_DELAY;
                                $this->applyStyle($sheet, "I{$row}", ['fill' => $delayBg]);
                            }

                            // Status hanya di baris pertama
                            if ($firstCode) {
                                $statusText = match (true) {
                                    $flight->status === 'night_stop' => 'ON TIME',
                                    $flight->status === 'noop'       => 'NOOP',
                                    $flight->status === 'delayed'    => $this->delayLabel($flight->delay_minutes),
                                    default                          => 'ON TIME',
                                };
                                $sheet->setCellValue("J{$row}", $statusText);

                                // Warna status
                                $bgJ = match (true) {
                                    $flight->status === 'noop'                                   => self::RED_NOOP,
                                    $flight->status === 'delayed' && $flight->delay_minutes > 15 => self::YELLOW_ROW,
                                    $flight->status === 'delayed'                                => self::GREEN_ROW,
                                    default                                                      => self::WHITE,
                                };
                                $fontJ = match (true) {
                                    $flight->status === 'noop'    => self::WHITE,
                                    $flight->status === 'delayed' => self::BLACK,
                                    default                       => self::BLACK,
                                };
                                $this->applyStyle($sheet, "J{$row}", [
                                    'fill' => $bgJ,
                                    'font' => ['bold' => $flight->status === 'delayed', 'color' => $fontJ],
                                ]);
                            }

                            // Border & alignment semua kolom
                            $this->applyStyle($sheet, "A{$row}:J{$row}", [
                                'halign' => Alignment::HORIZONTAL_CENTER,
                                'border' => true,
                            ]);

                            $firstCode = false;
                            $row++;
                        }
                    }

                    // Merge DATE untuk seluruh grup hari
                    if ($row - 1 >= $startRow) {
                        if ($row - 1 > $startRow) {
                            $sheet->mergeCells("A{$startRow}:A" . ($row - 1));
                        }
                        $sheet->setCellValue("A{$startRow}", $dateLabel);
                        $this->applyStyle($sheet, "A{$startRow}", [
                            'font'   => ['bold' => true],
                            'halign' => Alignment::HORIZONTAL_CENTER,
                            'valign' => Alignment::VERTICAL_CENTER,
                            'border' => true,
                        ]);
                    }

                    // ── Recap per hari ───────────────────────────────────────
                    $counted = $dayFlights->filter(fn($f) => !in_array($f->status, ['night_stop', 'noop']));
                    $total   = $counted->count();
                    $lt15    = $counted->filter(fn($f) => $f->status === 'delayed' && $f->delay_minutes <= 15)->count();
                    $gt15    = $counted->filter(fn($f) => $f->status === 'delayed' && $f->delay_minutes > 15)->count();
                    $onTime  = $counted->filter(fn($f) => $f->status === 'on_time')->count();
                    $pct     = $total > 0 ? round(($onTime + $lt15) / $total * 100) : 0;

                    $sheet->setCellValue("L{$recapRow}", $dateLabel);
                    $sheet->setCellValue("M{$recapRow}", $lt15);
                    $sheet->setCellValue("N{$recapRow}", $gt15);
                    $sheet->setCellValue("O{$recapRow}", $onTime);
                    $sheet->setCellValue("P{$recapRow}", $pct . '%');

                    $this->applyStyle($sheet, "L{$recapRow}:P{$recapRow}", [
                        'halign' => Alignment::HORIZONTAL_CENTER,
                        'border' => true,
                    ]);
                    $this->applyStyle($sheet, "N{$recapRow}", ['fill' => self::RED_HDR,   'font' => ['color' => self::WHITE]]);
                    $this->applyStyle($sheet, "M{$recapRow}", ['fill' => self::GREEN_HDR,  'font' => ['color' => self::WHITE]]);

                    $recapRow++;
                }

                // ── Grand Total ──────────────────────────────────────────────
                $counted  = $flights->filter(fn($f) => !in_array($f->status, ['night_stop', 'noop']));
                $total    = $counted->count();
                $lt15All  = $counted->filter(fn($f) => $f->status === 'delayed' && $f->delay_minutes <= 15)->count();
                $gt15All  = $counted->filter(fn($f) => $f->status === 'delayed' && $f->delay_minutes > 15)->count();
                $onTimeAll= $counted->filter(fn($f) => $f->status === 'on_time')->count();
                $pctAll   = $total > 0 ? round(($onTimeAll + $lt15All) / $total * 100) : 0;

                $sheet->setCellValue("L{$recapRow}", 'TOTAL');
                $sheet->setCellValue("M{$recapRow}", $lt15All);
                $sheet->setCellValue("N{$recapRow}", $gt15All);
                $sheet->setCellValue("O{$recapRow}", $onTimeAll);
                $sheet->setCellValue("P{$recapRow}", $pctAll . '%');

                $this->applyStyle($sheet, "L{$recapRow}:P{$recapRow}", [
                    'font'   => ['bold' => true, 'color' => self::WHITE],
                    'fill'   => self::BLUE_HDR,
                    'halign' => Alignment::HORIZONTAL_CENTER,
                    'border' => true,
                ]);

                // ── Lebar kolom ──────────────────────────────────────────────
                foreach ([
                    'A' => 7, 'B' => 9, 'C' => 7, 'D' => 7, 'E' => 7,
                    'F' => 7, 'G' => 11, 'H' => 11, 'I' => 11, 'J' => 18,
                    'K' => 2, 'L' => 7, 'M' => 13, 'N' => 13, 'O' => 10, 'P' => 7,
                ] as $c => $w) {
                    $sheet->getColumnDimension($c)->setWidth($w);
                }

                // Tinggi baris 2 (header 2 baris)
                $sheet->getRowDimension(2)->setRowHeight(30);

                // ── Print setup ──────────────────────────────────────────────
                $ps = $sheet->getPageSetup();
                $ps->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $ps->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $ps->setFitToWidth(1)->setFitToHeight(0);
                $ps->setRowsToRepeatAtTopByStartAndEnd(1, 3);
                $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.5)->setRight(0.5);
            },
        ];
    }

    private function delayLabel(int $minutes): string
    {
        if ($minutes <= 0) return 'ON TIME';
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        if ($minutes > 15) {
            return $h > 0
                ? 'DELAYED ' . ($m > 0 ? "{$h}H{$m}MINS" : "{$h}H")
                : "DELAYED {$m}MINS";
        }
        return $h > 0 ? "DELAYED {$h}H{$m}MINS" : "DELAYED {$m}MINS";
    }

    private function applyStyle($sheet, string $range, array $opt): void
    {
        $s = [];

        if (isset($opt['font'])) {
            $f = $opt['font'];
            $s['font'] = array_filter([
                'bold'      => $f['bold'] ?? null,
                'size'      => $f['size'] ?? null,
                'name'      => 'Calibri',
                'color'     => isset($f['color']) ? ['argb' => $f['color']] : null,
                'underline' => $f['underline'] ?? null,
            ]);
        }

        if (isset($opt['fill'])) {
            $s['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $opt['fill']]];
        }

        if (isset($opt['halign']) || isset($opt['valign']) || isset($opt['wrap'])) {
            $s['alignment'] = [
                'horizontal' => $opt['halign'] ?? Alignment::HORIZONTAL_GENERAL,
                'vertical'   => $opt['valign'] ?? Alignment::VERTICAL_CENTER,
                'wrapText'   => $opt['wrap']   ?? false,
            ];
        }

        if (!empty($opt['border'])) {
            $s['borders'] = ['allBorders' => ['borderStyle' => Border::BORDER_THIN]];
        }

        if (!empty($s)) {
            $sheet->getStyle($range)->applyFromArray($s);
        }
    }
}