<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Complaint;
use Faker\Factory as Faker;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        foreach (range(1, 30) as $index) {

            $user =  Complaint::create([
                'title'       => $faker->name,
                'category_id' => rand(1, 10),
                'description' => 'auto generated',
                'priority'    => 'High',
                'user_id'     => rand(2, 3),
                'created_at'  => date('Y-m-' . $index . ' H:i:s')
            ]);
        }
    }
}
