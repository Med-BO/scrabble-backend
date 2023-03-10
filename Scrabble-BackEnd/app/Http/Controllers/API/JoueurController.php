<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\Joueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\JoueurResource;

class JoueurController extends Controller
{
    //Les fonctions CRUD
    public function index()
    {
        $data = Joueur::latest()->get();
        return response()->json([JoueurResource::collection($data), 'Joueurs trouvés']);
    }

    public function store(Request $request)
    {
        $joueur = Joueur::create([
            'nom' => $request->nom,
            'photo' => $request->photo
         ]);
        
        return response()->json(['Joueur ajouté !', new JoueurResource($joueur)]);
    }

    public function show($id)
    {
        $joueur = Joueur::find($id);
        if (is_null($joueur)) {
            return response()->json('Data not found', 404); 
        }
        return response()->json([new JoueurResource($joueur)]);
    }

    public function update(Request $request, Joueur $joueur)
    {
        if($request->photo != NULL){
            $joueur->photo = $request->photo;
            $joueur->save();
            return response()->json(['Photo mise à jour', new JoueurResource($joueur)]);
        }
        else if($request->statutJoueur != NULL){
            $joueur->statutJoueur = $request->statutJoueur;
            $joueur->save();
            return response()->json(['Statut mis a jour', new JoueurResource($joueur)]);
        }
        else if($request->chevalet != NULL){
            $joueur->chevalet = $request->chevalet;
            $joueur->save();
            return response()->json(['chevalet mis a jour', new JoueurResource($joueur)]);
        }
        else if($request->score != NULL){
            $joueur->score = $request->score;
            $joueur->save();
            return response()->json(['Score mis a jour', new JoueurResource($joueur)]);
        }
    }

    public function vueJeu(Request $request, $idJoueur)
    {
        $joueur = Joueur::find($idJoueur);
        return response()->json(['chevalet', $joueur->chevalet]);
    }

}
