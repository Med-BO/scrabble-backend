<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Partie;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class PartieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        for($i = 0; $i < 10; $i++){
            Partie::create([
                'typePartie' => $faker->numberBetween($min = 2, $max = 4),
                'reserve' => $faker->text($maxNbChars = 104),
                'grille' => $faker->text($maxNbChars = 50),
                'dateCreation' => $faker->dateTime($max = 'now', $timezone = null),
                'dateDebutPartie' => $faker->dateTime($max = 'now', $timezone = null),
                'dateFinPartie' => $faker->dateTime($max = 'now', $timezone = null),
                'statutPartie' => $faker->text($maxNbChars = 10),
                'tempsJoueur' => $faker->numberBetween($min = 0, $max = 300)
            ]);
        }
    }
}
