<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments("idMessage");
            $table->timestamp("dateCreation")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('envoyeur')->default(0);
            $table->foreign('envoyeur')
            ->references('idJoueur')
            ->on('joueurs')
            ->onDelete('restrict')
            ->onUpdate('restrict');
            $table->unsignedBigInteger('Partie')->default(0);;
            $table->foreign('Partie')
            ->references('idPartie')
            ->on('parties')
            ->onDelete('restrict')
            ->onUpdate('restrict');
            $table->string("contenu")->default('');
            $table->boolean("statutMessage")->default(0);;
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
        Schema::dropIfExists('messages');
    }
};
