<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightSchedule extends Model
{
    protected $table = 'flight_schedules';
    protected $fillable = ['flight_number', 'sta', 'std'];

    public function flights()
    {
        return $this->hasMany(Flight::class, 'flight_schedule_id');
    }
}