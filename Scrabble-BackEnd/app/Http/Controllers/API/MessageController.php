<?php

namespace App\Http\Controllers\API;

use App\Models\Joueur;
use App\Models\Partie;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\MessageEnvoye;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Http\Controllers\API\PartieController;

class MessageController extends Controller
{
    //Les fonctions CRUD
    public function index()
    {
        $data = Message::latest()->get();
        return response()->json([MessageResource::collection($data), 'Messages trouvés']);
    }

    public function store(Request $request)
    {
        $message = Message::create([
            'envoyeur' => $request->envoyeur,
            'Partie' => $request->Partie,
            'contenu' => $request->contenu,
            'statutMessage' => $request->statutMessage
         ]);
        
        return response()->json(['Message ajouté !', new MessageResource($message)]);
    }

    public function show($id)
    {
        $message = Message::find($id);
        if (is_null($message)) {
            return response()->json('Data not found', 404); 
        }
        return response()->json([new MessageResource($message)]);
    }

    public function update(Request $request, Message $message)
    {
       //
    }

    public function getMessages(Request $request, $idPartie)
    {
        $messages = DB::table('messages')->where('Partie', $idPartie)->orderby('idMessage', 'asc')->get()->toArray();
        $envoyeurs = DB::table('joueurs')->where('Partie', $idPartie)->get()->toArray();
        return response()->json(['liste de messages', $messages, 'envoyeurs', $envoyeurs]);
    }

    function verifierCommande($msg)
    {
        $pos1 = strpos($msg, '!passer');
        $pos2 = strpos($msg, '!placer');
        $pos3 = strpos($msg, '!changer');
        $pos4 = strpos($msg, '!aide');
        if(($pos1 === false) and ($pos2 === false) and ($pos3 === false) and ($pos4 === false)){
            return false;
        }
        else {
            return true;
        }
    } 

    public function envoiMessage(Request $request, $idPartie, $idJoueur)
    {
        $statut = false;
        if(($request->contenu)[0] == '!'){
            $statut = true;
        }
        if($statut == false){
            Message::create([
                'contenu' => $request->contenu,
                'envoyeur' => $idJoueur,
                'Partie' => $idPartie,
                'statutMessage' => $statut
            ]);
            event(new MessageEnvoye($request->contenu, $idJoueur));
        }
        else if($this->verifierCommande($request->contenu) == false){
            //message d'erreur
            event(new MessageEnvoye('Commande non valide !', $idJoueur));
        }
        else if($request->contenu == "!aide"){
            //menu aide
            event(new MessageEnvoye('HELP MENU', $idJoueur));
        }
        else{
            if(strpos($request->contenu, '!passer') !== false){
                app('App\Http\Controllers\API\PartieController')->passerTour($idPartie, $idJoueur);
            }

            else if(strpos($request->contenu, '!changer') !== false){
                //on passe l'id du joueur et partie et les lettres à échanger
                app('App\Http\Controllers\API\PartieController')->echangerLettres($idPartie, $idJoueur, substr($request->contenu, 9));
            }
            else if(strpos($request->contenu, '!placer') !== false){
                $joueur = Joueur::find($idJoueur);
                $chevalet = $joueur->chevalet;
                $partie = Partie::find($idPartie);
                $grille = $partie->grille;
                $mot = substr($request->contenu, 13);
                $mot_tab = str_split($mot);

                $valide = true;
                foreach($mot_tab as $lettre){
                    if( substr_count($mot, $lettre) > substr_count($chevalet, $lettre) ){
                        $valide = false;
                    }
                }
                $valide2 = true;
                if((substr($partie->grille, 112, 1) == '.') and (substr($request->contenu, 8, 3) != 'H08')){
                    $valide2 = false;
                }
                

                if($valide == false){
                    event(new MessageEnvoye('Chevalet ne contient pas les lettre requises', $idJoueur));
                }
                else if($valide2 == false){
                    event(new MessageEnvoye('Vous devez commencer par H08 !', $idJoueur));
                }
                else{
                    app('App\Http\Controllers\API\PartieController')->PlacerLettres($idPartie, $idJoueur, $request->contenu);
                }
            }
        }
        return response()->json(['Message envoyé']);
    }
  
}
