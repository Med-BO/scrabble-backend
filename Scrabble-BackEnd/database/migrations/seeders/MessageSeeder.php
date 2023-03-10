<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker=Factory::create();
        for($i=0;$i < 20; $i++){
           Message::create([
              'dateCreation'=>$faker->dateTime($max = 'now', $timezone = null),
              'envoyeur'=>$faker->randomDigitNotNull()  ,
              'Partie'=>$faker->randomDigitNotNull()  ,
              'contenu'=>$faker->text($maxNbChars = 50)  ,
              'statutMessage'=>$faker->boolean()
            ]);
    }
}
}