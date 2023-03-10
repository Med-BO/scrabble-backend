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
        Schema::create('joueurs', function (Blueprint $table) {
            $table->increments("idJoueur");
            $table->string("nom");
            $table->text("photo");
            $table->string("chevalet")->default('');
            $table->integer("score")->default('0');
            $table->boolean("statutJoueur")->default(false);
            $table->unsignedBigInteger('Partie')->default(0);
            $table->foreign('Partie')
            ->references('idPartie')
            ->on('parties')
            ->onDelete('restrict')
            ->onUpdate('restrict');
            $table->integer("ordre")->default('0');
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
        Schema::dropIfExists('joueurs');
    }
};
