<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DelayCode;

class DelayCodeSeeder extends Seeder
{
    public function run()
    {
        $codes = [
            ['code' => 'AD', 'reason' => 'Restrictions at destination airport',       'category' => 'Airport and Governmental Authorities'],
            ['code' => 'AF', 'reason' => 'Airport Facilities',                         'category' => 'Airport and Governmental Authorities'],
            ['code' => 'AG', 'reason' => 'Immigration, Custom and Health',             'category' => 'Airport and Governmental Authorities'],
            ['code' => 'AR', 'reason' => 'Restrictions at departure airport',          'category' => 'Airport and Governmental Authorities'],
            ['code' => 'AS', 'reason' => 'Mandatory Security',                         'category' => 'Airport and Governmental Authorities'],
            ['code' => 'AT', 'reason' => 'Air Traffic Services',                       'category' => 'Airport and Governmental Authorities'],
            ['code' => 'CC', 'reason' => 'Late Acceptance',                            'category' => 'Mail'],
            ['code' => 'CD', 'reason' => 'Documentation',                              'category' => 'Mail'],
            ['code' => 'CI', 'reason' => 'Inadequate Packing',                        'category' => 'Mail'],
            ['code' => 'CO', 'reason' => 'Oversales',                                  'category' => 'Mail'],
            ['code' => 'CP', 'reason' => 'Late Positioning',                           'category' => 'Mail'],
            ['code' => 'CU', 'reason' => 'Late Preparation in warehouse',              'category' => 'Mail'],
            ['code' => 'DF', 'reason' => 'Damage During Flight',                       'category' => 'Damage to Aircraft'],
            ['code' => 'DG', 'reason' => 'Damage On Ground',                           'category' => 'Damage to Aircraft'],
            ['code' => 'EC', 'reason' => 'Cargo preparation/documentation',            'category' => 'EDP (Automated Equipment Failure)'],
            ['code' => 'ED', 'reason' => 'Departure Control',                          'category' => 'EDP (Automated Equipment Failure)'],
            ['code' => 'EF', 'reason' => 'Flight Plan',                                'category' => 'EDP (Automated Equipment Failure)'],
            ['code' => 'EO', 'reason' => 'Other System Failure',                       'category' => 'EDP (Automated Equipment Failure)'],
            ['code' => 'FB', 'reason' => 'Captain Request for Security Check',         'category' => 'Flight Operation / Movement'],
            ['code' => 'FC', 'reason' => 'Cabin Crew Shortage',                        'category' => 'Flight Operation / Movement'],
            ['code' => 'FF', 'reason' => 'Operational Requirement, Fuel Load Alteration', 'category' => 'Flight Operation / Movement'],
            ['code' => 'FM', 'reason' => 'Movement Control',                           'category' => 'Flight Operation / Movement'],
            ['code' => 'FP', 'reason' => 'Flight Plan',                                'category' => 'Flight Operation / Movement'],
            ['code' => 'FR', 'reason' => 'Flight Deck Crew Special Request',           'category' => 'Flight Operation / Movement'],
            ['code' => 'FS', 'reason' => 'Flight Deck Crew Shortage',                  'category' => 'Flight Operation / Movement'],
            ['code' => 'FT', 'reason' => 'Late Boarding of Crew',                      'category' => 'Flight Operation / Movement'],
            ['code' => 'GB', 'reason' => 'Catering',                                   'category' => 'Aircraft and Ramp Handling'],
            ['code' => 'GC', 'reason' => 'Aircraft Cleaning',                          'category' => 'Aircraft and Ramp Handling'],
            ['code' => 'GD', 'reason' => 'Aircraft Documentation late/inaccurate',    'category' => 'Aircraft and Ramp Handling'],
            ['code' => 'GE', 'reason' => 'Loading Equipment',                          'category' => 'Aircraft and Ramp Handling'],
            ['code' => 'GF', 'reason' => 'Fuelling/Defuelling',                        'category' => 'Aircraft and Ramp Handling'],
            ['code' => 'GL', 'reason' => 'Loading/Unloading',                          'category' => 'Aircraft and Ramp Handling'],
            ['code' => 'GS', 'reason' => 'Servicing Equipment',                        'category' => 'Aircraft and Ramp Handling'],
            ['code' => 'GT', 'reason' => 'Technical Equipment',                        'category' => 'Aircraft and Ramp Handling'],
            ['code' => 'GU', 'reason' => 'ULD',                                        'category' => 'Aircraft and Ramp Handling'],
            ['code' => 'PB', 'reason' => 'Baggage Processing',                         'category' => 'Passenger & Baggage'],
            ['code' => 'PC', 'reason' => 'Catering Order',                             'category' => 'Passenger & Baggage'],
            ['code' => 'PD', 'reason' => 'Late Check In (acceptance after deadline)',  'category' => 'Passenger & Baggage'],
            ['code' => 'PE', 'reason' => 'Check in Error',                             'category' => 'Passenger & Baggage'],
            ['code' => 'PH', 'reason' => 'Boarding',                                   'category' => 'Passenger & Baggage'],
            ['code' => 'PL', 'reason' => 'Late Check In (due congestion at check-in area)', 'category' => 'Passenger & Baggage'],
            ['code' => 'PO', 'reason' => 'Over sales',                                 'category' => 'Passenger & Baggage'],
            ['code' => 'PS', 'reason' => 'Commercial Publicity/Passenger',             'category' => 'Passenger & Baggage'],
            ['code' => 'RA', 'reason' => 'Late Arrival of Aircraft',                   'category' => 'Reactionary/Consequential'],
            ['code' => 'RC', 'reason' => 'Crew Rotation (flight deck or entire)',      'category' => 'Reactionary/Consequential'],
            ['code' => 'RL', 'reason' => 'Load Connection',                            'category' => 'Reactionary/Consequential'],
            ['code' => 'RS', 'reason' => 'Cabin Crew Rotation',                        'category' => 'Reactionary/Consequential'],
            ['code' => 'RT', 'reason' => 'Through Check In Error',                     'category' => 'Reactionary/Consequential'],
            ['code' => 'TA', 'reason' => 'AOG Spares',                                 'category' => 'Technical and Aircraft Equipment'],
            ['code' => 'TC', 'reason' => 'Aircraft Change',                            'category' => 'Technical and Aircraft Equipment'],
            ['code' => 'TD', 'reason' => 'Aircraft Defect',                            'category' => 'Technical and Aircraft Equipment'],
            ['code' => 'TL', 'reason' => 'Standby Aircraft',                           'category' => 'Technical and Aircraft Equipment'],
            ['code' => 'TM', 'reason' => 'Schedule Maintenance',                       'category' => 'Technical and Aircraft Equipment'],
            ['code' => 'TN', 'reason' => 'Non Schedule Maintenance',                   'category' => 'Technical and Aircraft Equipment'],
            ['code' => 'TS', 'reason' => 'Spares and Maintenance Equipment',           'category' => 'Technical and Aircraft Equipment'],
            ['code' => 'WG', 'reason' => 'Ground Handling Impaired',                   'category' => 'Weather'],
            ['code' => 'WI', 'reason' => 'De Icing',                                   'category' => 'Weather'],
            ['code' => 'WO', 'reason' => 'Departure Station',                          'category' => 'Weather'],
            ['code' => 'WR', 'reason' => 'Enroute or Alternate Station',               'category' => 'Weather'],
            ['code' => 'WS', 'reason' => 'Removal of Snow, Ice, Water, Sand',         'category' => 'Weather'],
            ['code' => 'WT', 'reason' => 'Destination Station',                        'category' => 'Weather'],
            ['code' => 'MI', 'reason' => 'Industrial Action (with own airline)',        'category' => 'Miscellaneous'],
            ['code' => 'MO', 'reason' => 'Industrial Action (outside own airline)',    'category' => 'Miscellaneous'],
            ['code' => 'MX', 'reason' => 'Reason Cannot be Matched',                  'category' => 'Miscellaneous'],
        ];

        foreach ($codes as $code) {
            DelayCode::updateOrCreate(['code' => $code['code']], $code);
        }
    }
}