<?php

namespace App\Http\Controllers\API;

use App\Models\Joueur;
use App\Models\Partie;
use Illuminate\Http\Request;
use App\Events\JoueurInscrit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Http\Resources\JoueurResource;
use App\Http\Resources\PartieResource;
use Illuminate\Support\Facades\Storage;

class InscriptionController extends Controller
{
    public function inscrire(Request $request)
    {
        //recherche d'une partie avec le meme typePartie dans la base de données et avec un statut = "en attente"
        $partie = DB::table('parties')->where('typePartie', $request->typePartie)->where('statutPartie', 'en attente')->first();
        //stockage de la photo
        $filename = "default_image.png";
        if($request->file('photo') != NULL){
            $path = $request->file('photo')->storeAs('public/images_joueurs', $request->file('photo')->getClientOriginalName());
            $filename = $request->file('photo')->getClientOriginalName();
        }

        if($partie != NULL){ //Si on a trouvé une partie
            //affectation du joueur a cette partie
            $joueur = Joueur::create([
                'nom' => $request->nom,
                'photo' => $filename,
                'Partie' => $partie->idPartie,
            ]);
        }
        else{
            //création d'une nouvelle partie et affectation du joueur a cette partie
            $partie = Partie::create([
                'typePartie' => $request->typePartie,
                'statutPartie' => 'en attente'
            ]);
            $partie = $partie->fresh();//Met a jour les champs d'apres la base de données
            $joueur = Joueur::create([
                'nom' => $request->nom,
                'photo' => $filename,
                'Partie' => $partie->idPartie
            ]);
        }
        
        event(new JoueurInscrit($joueur->toArray()));
        return response()->json(['Inscription complete !', $partie->idPartie, 'idJoueur', $joueur->idJoueur]);
    }
}
