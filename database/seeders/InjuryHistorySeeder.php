<?php

namespace Database\Seeders;


use App\Models\InjuryHistory;
use Database\Factories\InjuryHistoryFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InjuryHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        InjuryHistory::factory()->count(10)->create();

    }
}
