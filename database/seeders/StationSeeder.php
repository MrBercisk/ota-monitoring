<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Station;

class StationSeeder extends Seeder
{
    public function run()
    {
        $stations = [
            ['code' => 'CGK', 'name' => 'Soekarno-Hatta'],
            ['code' => 'DPS', 'name' => 'Ngurah Rai'],
            ['code' => 'KNO', 'name' => 'Kualanamu'],
            ['code' => 'SUB', 'name' => 'Juanda'],
            ['code' => 'PKU', 'name' => 'Sultan Syarif Kasim II'],
            ['code' => 'YIA', 'name' => 'Yogyakarta International'],
            ['code' => 'BPN', 'name' => 'Sultan Aji Muhammad Sulaiman'],
            ['code' => 'UPG', 'name' => 'Sultan Hasanuddin'],
            ['code' => 'LHR', 'name' => 'London Heathrow'],
        ];

        foreach ($stations as $station) {
            Station::create($station);
        }
    }
}