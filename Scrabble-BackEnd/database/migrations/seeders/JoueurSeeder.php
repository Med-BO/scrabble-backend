<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Joueur;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JoueurSeeder extends Seeder
{

    public function run(){
    $faker=Factory::create();
    for($i=0;$i < 20; $i++){
        Joueur::create([
          'nom'=>$faker->FirstNameMale(),
          'photo'=>$faker->text($maxNbChars = 50)  ,
          'chevalet'=>$faker->text($maxNbChars = 7)  ,
          'score'=>$faker->randomDigitNotNull(),
          'statutJoueur'=>$faker->boolean(),
          'Partie'=>$faker->randomDigitNotNull(),
          'ordre'=>$faker->randomDigitNotNull(),
        ]);
    }    
    }   
}