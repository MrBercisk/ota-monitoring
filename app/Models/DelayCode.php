<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DelayCode extends Model
{
    protected $fillable = ['code', 'reason', 'category'];

    public function flights()
    {
        return $this->hasMany(Flight::class, 'delay_code', 'code');
    }
}