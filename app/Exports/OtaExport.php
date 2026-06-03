<?php

namespace App\Exports;

use App\Models\Flight;
use App\Models\Station;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OtaExport implements WithMultipleSheets
{
    protected string $type;
    protected string $dateFrom;
    protected string $dateTo;

    // Warna tab per urutan station
    const TAB_COLORS = [
        '1F497D', // biru tua
        '808080', // abu
        '1E7B1E', // hijau tua
        '808080', // abu
        'FF6600', // oranye
        '00B050', // hijau terang
        '1E7B1E', // hijau tua
        '7030A0', // ungu
        '808080', // abu
        '808080', // abu
        'FFFF00', // kuning
        '808020', // olive
    ];

    public function __construct(string $type, string $dateFrom, string $dateTo)
    {
        $this->type     = $type;
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
    }

    public function sheets(): array
    {
        $stations = Station::orderBy('code')->get();
        $sheets   = [];

        // Sheet station dulu
        foreach ($stations as $i => $station) {
            $hasFlights = Flight::where('station_id', $station->id)
                ->whereDate('flight_date', '>=', $this->dateFrom)
                ->whereDate('flight_date', '<=', $this->dateTo)
                ->exists();

            $tabColor = $hasFlights
                ? (self::TAB_COLORS[$i % count(self::TAB_COLORS)])
                : 'FF0000'; // merah jika NOOP

            $sheets[] = new OtaStationSheet(
                $this->type,
                $this->dateFrom,
                $this->dateTo,
                $station,
                $hasFlights,
                $tabColor
            );
        }

        // Delay Code sheet
        $sheets[] = new DelayCodeSheet();

        // OTA RECAP di paling belakang
        $sheets[] = new OtaSummarySheet($this->type, $this->dateFrom, $this->dateTo, $stations);

        return $sheets;
    }
}