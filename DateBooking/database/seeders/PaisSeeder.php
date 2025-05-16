<?php

namespace Database\Seeders;

use App\Models\Pai;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Pai::create([
            'nombre' => 'Mexico',
        ]);
    }
}
