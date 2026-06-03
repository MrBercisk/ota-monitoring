<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DelayCategory;

class DelayCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Airport and Governmental Authorities',
            'Mail',
            'Damage to Aircraft',
            'EDP (Authomated Equipment Failure)',
            'Flight Operation / Movement',
            'Aircraft and Ramp Handling',
            'Passenger & Baggage',
            'Reactionary/Consequential',
            'Technical and Aircraft Equipment',
            'Weather',
            'Miscellaneous',
        ];

        foreach ($categories as $name) {
            DelayCategory::firstOrCreate(['name' => $name]);
        }
    }
}