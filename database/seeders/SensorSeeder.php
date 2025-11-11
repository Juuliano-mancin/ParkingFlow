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
        ]);

        $this->command->info('✅ 4 sensores criados: sensor01, sensor02, sensor03, sensor04');
        $this->command->info('📋 IDs gerados: 1, 2, 3, 4');
    }
}