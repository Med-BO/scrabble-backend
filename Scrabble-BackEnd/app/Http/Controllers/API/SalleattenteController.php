<?php

namespace App\Http\Controllers\API;
use App\Models\Joueur;
use App\Models\Partie;
use App\Events\JoueurEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class SalleattenteController extends Controller{

    //retourne les joueurs de la partie ayant l'id idPartie
    public function getJoueurs(Request $request, $idPartie)
    {
        $joueurs = DB::table('joueurs')->where('Partie', $idPartie)->get()->toArray();
        $partie = Partie::find($idPartie);
        return response()->json(['Joueurs dans la partie', $joueurs, 'typePartie', $partie->typePartie]);
    }

    //rend le statut de la partie 'en cours'
    public function changerStatut(Request $request, $idPartie)
    {
        Partie::where('idPartie', $idPartie)->update(array('statutPartie' => 'en cours'));
        return response()->json(['Partie mise a jour']);
    }
    public function supprimeJoueur(Request $request, $idJoueur){
        $joueur = Joueur::find($idJoueur);
        event(new JoueurEvent($joueur->nom));
        Joueur::where('idJoueur', $idJoueur)->update(array('Partie' => 0));
        return response()->json(['joueur quitte la salle d-attente']);

}}