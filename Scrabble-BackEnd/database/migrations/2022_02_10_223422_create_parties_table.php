<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->increments("idPartie");
            $table->integer("typePartie");
            $table->string("reserve")->default('AAAAAAAAABBCCDDDEEEEEEEEEEEEEEEFFGGHHIIIIIIIIJKLLLLLMMMNNNNNNOOOOOOPPQRRRRRRSSSSSSTTTTTTUUUUUUVVWXYZ');
            $table->string("grille")->default('.................................................................................................................................................................................................................................');
            $table->timestamp("dateCreation")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp("dateDebutPartie")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp("dateFinPartie")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string("statutPartie")->default('en attente');
            $table->integer("tempsJoueur")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parties');
    }
};
