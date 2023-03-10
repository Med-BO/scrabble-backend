<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Joueur extends Model
{
    use HasFactory;
    protected $fillable = ['nom', 'photo', 'Partie'];
    //
    protected $primaryKey = 'idJoueur';
    //added because find() assumes that the primary key is called 'id'
}
