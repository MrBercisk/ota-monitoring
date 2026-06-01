<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $fillable = ['code', 'name'];

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }
}