<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flight extends Model
{
    protected $fillable = [
        'flight_date', 'flight_number', 'station_id',
        'flight_schedule_id',
        'sta', 'std', 'ata', 'atd',
        'delay_minutes', 'delay_code_id', 'status', 'remarks'
    ];

    protected $casts = [
        'flight_date' => 'date',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function delayCode()
    {
        return $this->belongsTo(DelayCode::class, 'delay_code_id');
    }

    public function schedule()
    {
        return $this->belongsTo(FlightSchedule::class, 'flight_schedule_id');
    }

    public static function calculateDelay($sta, $ata): int
    {
        if (!$ata) return 0;
        $scheduled = \Carbon\Carbon::parse($sta);
        $actual    = \Carbon\Carbon::parse($ata);
        $diff      = $actual->diffInMinutes($scheduled, false);
        return $diff < 0 ? abs($diff) : 0;
    }

    public static function determineStatus($delayMinutes, $remarks = null): string
    {
        if ($remarks === 'night_stop') return 'night_stop';
        if ($remarks === 'noop')       return 'noop';
        return $delayMinutes > 0 ? 'delayed' : 'on_time';
    }
}