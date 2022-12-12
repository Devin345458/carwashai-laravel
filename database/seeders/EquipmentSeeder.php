<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Equipment;
use App\Models\File;
use App\Models\Location;
use App\Models\Store;
use App\Models\User;
use DB;
use Illuminate\Database\Seeder;
use Throwable;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Throwable
     */
    public function run()
    {
        $equipments = [
            // Sonnys
            [
                'name' => 'Buff-n-Dry',
                'file' => null,
                'supplied_index' => 0,
                'model_number' => NULL,
            ],
            [
                'name' => 'Tire Seal Machine',
                'supplied_index' => 0,
                'model_number' => NULL,
                'file' => 'database/seeders/SeederFiles/Tire Seal.png'
            ],
//            [
//                'name' => 'Omni Wash Arches',
//                'supplied_index' => 0,
//                'model_number' => NULL,
//                'file' => 'database/seeders/SeederFiles/Omni.png'
//            ],
//            [
//                'name' => 'TECH 21 DRYERS',
//                'supplied_index' => 0,
//                'model_number' => NULL,
//                'file' => 'database/seeders/SeederFiles/Tech 21 Dryers.jpeg'
//            ],
//            [
//                'name' => 'RG-440 Conveyor',
//                'supplied_index' => 0,
//                'model_number' => 'RG-440',
//                'file' => 'database/seeders/SeederFiles/RG-440 Conveyor.jpeg'
//            ],
//            [
//                'name' => 'Tape Switch',
//                'supplied_index' => 0,
//                'model_number' => '10007272',
//                'file' => null
//            ],
//            [
//                'name' => 'Undercarriage Wash',
//                'supplied_index' => 0,
//                'model_number' => '20002890',
//                'file' => null
//            ],
//            [
//                'name' => 'Rain Manifold',
//                'supplied_index' => 0,
//                'model_number' => NULL,
//                'file' => null
//            ],
//            [
//                'name' => 'Mirror Rinse',
//                'supplied_index' => 0,
//                'model_number' => 'MIRROR_RINSE',
//                'file' => null
//            ],
//            [
//                'name' => 'Blower',
//                'supplied_index' => 0,
//                'model_number' => NULL,
//                'file' => null
//            ],
//            [
//                'name' => 'Applicator Arch - Foaming & Non-Foaming',
//                'supplied_index' => 0,
//                'model_number' => NULL,
//                'file' => 'database/seeders/SeederFiles/Applicator Arch - Foaming & Non-Foaming.png'
//            ],
//            [
//                'name' => 'Hydraulic Power Pack',
//                'supplied_index' => 0,
//                'model_number' => NULL,
//                'file' => null
//            ],
//            [
//                'name' => 'Entrance Arch',
//                'supplied_index' => 0,
//                'model_number' => NULL,
//                'file' => null
//            ],
//            [
//                'name' => 'Air Compressor',
//                'supplied_index' => 0,
//                'model_number' => NULL,
//                'file' => 'database/seeders/SeederFiles/Air Compressor.png',
//            ],
//
//            // McNeil
//            [
//                'name' => 'Photo Eyes',
//                'supplied_index' => 1,
//                'model_number' => NULL,
//                'file' => 'database/seeders/SeederFiles/Photo Eyes.png'
//            ],
//            [
//                'name' => 'Conveyor',
//                'supplied_index' => 1,
//                'model_number' => 'XR1000',
//                'file' => null
//            ],
//            [
//                'name' => 'RC-120 - Correlator',
//                'supplied_index' => 1,
//                'model_number' => 'RC-120',
//                'file' => 'database/seeders/SeederFiles/RC-120 Correlator.png'
//            ],
//            [
//                'name' => 'Evolution Top Brush',
//                'supplied_index' => 1,
//                'model_number' => 'RS-1000',
//                'file' => 'database/seeders/SeederFiles/Evolution Top Brush.png'
//            ],
//            [
//                'name' => 'SuperFlex Wrap-Arounds',
//                'supplied_index' => 1,
//                'model_number' => 'RS-701',
//                'file' => 'database/seeders/SeederFiles/SuperFlex Wrap-Arounds.png'
//            ],
//            [
//                'name' => 'Low Side Washers',
//                'supplied_index' => 1,
//                'model_number' => 'RS-400',
//                'file' => 'database/seeders/SeederFiles/Low Side Washers.png'
//            ],
//            [
//                'name' => 'Magnum Wheel Boss',
//                'supplied_index' => 1,
//                'model_number' => 'MW-2000',
//                'file' => 'database/seeders/SeederFiles/Magnum Wheel Boss.png'
//            ],
//            [
//                'name' => 'Magnum Wheel Blaster - 3 Nozzel',
//                'supplied_index' => 1,
//                'model_number' => 'WB-300',
//                'file' => 'database/seeders/SeederFiles/Magnum Wheel Blaster - 3 Nozzel.png'
//            ],
//            [
//                'name' => 'High Side Washer',
//                'supplied_index' => 1,
//                'model_number' => 'RS-301',
//                'file' => 'database/seeders/SeederFiles/High Side Washer.png'
//            ],
//            [
//                'name' => 'CTA - Wheel Rite Tire Foamer',
//                'supplied_index' => 1,
//                'model_number' => NULL,
//                'file' => 'database/seeders/SeederFiles/CTA - Wheel Rite Tire Foamer.png'
//            ],
//            [
//                'name' => 'Gloss Boss',
//                'supplied_index' => 1,
//                'model_number' => 'MT-2500',
//                'file' => 'database/seeders/SeederFiles/Gloss Boss.png'
//            ],
//            [
//                'name' => 'Magnum Wheel Blaster - 6 Nozzles',
//                'supplied_index' => 1,
//                'model_number' => 'WB600',
//                'file' => 'database/seeders/SeederFiles/Wheel Blaster 6 Nozzels.jpeg'
//            ],
//            [
//                'name' => 'Boomerang Foamer',
//                'supplied_index' => 1,
//                'model_number' => NULL,
//                'file' => 'database/seeders/SeederFiles/Boomerang Foamer.png'
//            ],
//            [
//                'name' => 'Magnum High Pressure Pump 15HP',
//                'supplied_index' => 1,
//                'model_number' => 'M2000R',
//                'file' => 'database/seeders/SeederFiles/Magnum High Pressure Pump 15HP.png'
//            ],
//            [
//                'name' => 'Magnum Direct Drive Pumping Station 2X',
//                'supplied_index' => 1,
//                'model_number' => 'M2200RD',
//                'file' => 'database/seeders/SeederFiles/Magnum Direct Drive Pumping Station 2X.png'
//            ],
//            [
//                'name' => 'Magnum Direct Drive Pumping Station',
//                'supplied_index' => 1,
//                'model_number' => 'M1100RD',
//                'file' => 'database/seeders/SeederFiles/Magnum Direct Drive Pumping Station.png'
//            ],
//
//            // DRB
//            [
//                'name' => 'DRB - Ultra Sonic Sensor',
//                'supplied_index' => 3,
//                'model_number' => NULL,
//                'file' => null
//            ],
//
//            // Generic
//            [
//                'name' => 'Emergency Stop Button',
//                'model_number' => NULL,
//                'file' => 'database/seeders/SeederFiles/Emergency Stop.jpeg'
//            ],
//            [
//                'name' => 'Light Bar',
//                'model_number' => NULL,
//                'file' => null
//            ],

        ];
        $suppliersData = [
            [
                'name' => 'Sonny\'s Direct',
                'website' => 'https://www.sonnysdirect.com',
                'phone' => NULL,
                'email' => 'support@sonnysdirect.com',
                'contact_name' => '',
                'file' => 'database/seeders/SeederFiles/Sonny\'s Logo.png',
                'supplier_type_id' => NULL,
            ],
            [
                'name' => 'MacNeil',
                'website' => 'https://www.macneilwash.com',
                'phone' => NULL,
                'email' => 'support@macneilwash.com',
                'contact_name' => '',
                'file' => 'database/seeders/SeederFiles/MacNeil Logo.png',
                'supplier_type_id' => NULL,
            ],
            [
                'name' => 'DRB',
                'website' => 'https://drb.com',
                'phone' => NULL,
                'email' => 'support@drb.com',
                'contact_name' => '',
                'file' => NULL,
                'supplier_type_id' => NULL,
            ],
        ];

        DB::beginTransaction();

        try {
            $company = Company::find(1);
            /** @var Store $store */
            $store = $company->stores()->first();
            /** @var Location $location */
            $location = $store->locations()->first();
            /** @var User $user */
            $user = $company->users()->first();
            \Auth::setUser($user);

            $suppliers = [];
            foreach ($suppliersData as $supplier) {
                $file = null;
                if ($supplier['file'] ?? false) {
                    $fileName = $supplier['name'] . ' logo';
                    $file = File::where([
                        'name' => $fileName,
                        'company_id' => $company->id,
                    ])->first();
                    if (!$file) {
                        $file = File::upload($supplier['file'], $fileName);
                    }
                }
                unset($supplier['file']);
                $suppliers[] = $store->suppliers()->firstOrCreate(['name' => $supplier['name']], [...$supplier, 'file_id' => optional($file)->id]);
            }

            foreach ($equipments as $i => $equipment) {
                $file = null;
                echo "\r";
                echo "Equipment processing: " . $i + 1 . "/" . count($equipments);
                if ($equipment['file'] ?? false) {
                    $fileName = $equipment['name'] . ' image';
                    $file = File::where([
                        'name' => $fileName,
                        'company_id' => $company->id,
                    ])->first();
                    if (!$file) {
                        $file = File::upload($equipment['file'], $fileName);
                    }
                }
                $equipment = Equipment::firstOrNew([
                    'name' => $equipment['name'],
                    'location_id' => $location->id,
                    'store_id' => $store->id,
                ]);
                $equipment->fill([
                    'file_id' => optional($file)->id,
                    'manufacturer_id' => isset($equipment['supplied_index']) ? $suppliers[$equipment['supplied_index']] : null,
                    'created_by_id' => $user->id,
                    'updated_by_id' => $user->id,
                    'model_number' => $equipment['model_number']
                ]);
                $equipment->save();
            }
            echo "\n";
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
