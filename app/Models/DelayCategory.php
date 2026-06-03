<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DelayCategory extends Model
{
    protected $table = 'delay_categories';
    protected $fillable = ['name'];

    public function delayCodes()
    {
        return $this->hasMany(DelayCode::class, 'delay_category_id');
    }
}