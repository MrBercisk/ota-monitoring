<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DelayCode extends Model
{
    protected $table = 'delay_codes';
    protected $fillable = ['code', 'reason', 'delay_category_id'];

    public function category()
    {
        return $this->belongsTo(DelayCategory::class, 'delay_category_id');
    }

    public function flights()
    {
        return $this->hasMany(Flight::class, 'delay_code', 'code');
    }
}