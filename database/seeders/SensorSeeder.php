<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tb_sensores')->insert([
            ['nomeSensor' => 'sensor01', 'statusManual' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['nomeSensor' => 'sensor02', 'statusManual' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['nomeSensor' => 'sensor03', 'statusManual' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['nomeSensor' => 'sensor04', 'statusManual' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['nomeSensor' => 'sensor05', 'statusManual' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
