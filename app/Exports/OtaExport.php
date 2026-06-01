<?php

namespace App\Exports;

use App\Models\Flight;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OtaExport implements WithMultipleSheets
{
    protected string $type;
    protected string $dateFrom;
    protected string $dateTo;

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

        // Sheet SUMMARY di posisi pertama
        $sheets[] = new OtaSummarySheet($this->type, $this->dateFrom, $this->dateTo, $stations);

        // 1 sheet per station
        foreach ($stations as $station) {
            $sheets[] = new OtaStationSheet($this->type, $this->dateFrom, $this->dateTo, $station);
        }

        return $sheets;
    }
}