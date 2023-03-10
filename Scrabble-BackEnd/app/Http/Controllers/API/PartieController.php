<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\Joueur;
use App\Models\Partie;
use App\Events\CloseTab;
use App\Events\TourPasse;
use Illuminate\Http\Request;
use App\Events\LettresPlaces;
use App\Events\EchangeEffectue;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\PartieResource;

class PartieController extends Controller
{
    //Les fonctions CRUD
    public function index()
    {
        $data = Partie::latest()->get();
        return response()->json([PartieResource::collection($data), 'Partie trouvés']);
    }

    public function store(Request $request)
    {
        $partie = Partie::create([
            'typePartie' => $request->typePartie
         ]);
        
        return response()->json(['partie ajouté !', new PartieResource($partie)]);
    }

    public function show($id)
    {
        $partie = Partie::find($id);
        if (is_null($partie)) {
            return response()->json('Data not found', 404); 
        }
        return response()->json([new PartieResource($partie)]);
    }

    public function update(Request $request, $id)
    {
        $partie = Partie::find($id); //sans ce ligne, PUT crée une autre ressource et ne fait pas de mise a jour
        if($request->typePartie != NULL){
            $partie->typePartie = $request->typePartie;
            $partie->save();
            return response()->json(['typePartie mise à jour', new PartieResource($partie)]);
        }
        else if($request->reserve != NULL){
            $partie->reserve= $request->reserve;
            $partie->save();
            return response()->json(['reserve mis a jour', new PartieResource($partie)]);
        }
        else if($request->grille != NULL){
            $partie->grille = $request->grille;
            $partie->save();
            return response()->json(['grille mis a jour', new PartieResource($partie)]);
        }
        
        else if($request->dateDebutPartie != NULL){
            $partie->dateDebutPartie= $request->dateDebutPartie;
            $partie->save();
            return response()->json(['dateDebutPartie mis a jour', new PartieResource($partie)]);
        }
        else if($request->dateFinPartie != NULL){
            $partie->dateDebutPartie= $request->dateFinPartie;
            $partie->save();
            return response()->json(['dateFinPartie mis a jour', new PartieResource($partie)]);
        }
        else if($request->statutPartie != NULL){
            $partie->statutPartie= $request->statutPartie;
            $partie->save();
            return response()->json(['statutPartie mis a jour', new PartieResource($partie)]);
        }
        else if($request->tempsJoueur!= NULL){
            $partie->tempsJoueur= $request->tempsJoueur;
            $partie->save();
            return response()->json(['tempsJoueur mis a jour', new PartieResource($partie)]);
        }
    }

    public function preparerPartie(Request $request, $idPartie)
    {
        $partie = Partie::find($idPartie);
        $joueurs = DB::table('joueurs')->where('Partie', $idPartie)->get()->toArray();

        //affectation des ordres de joueurs
        $nbJoueurs = count($joueurs);
        $ordres_prises = [];
        foreach($joueurs as $joueur){
            do{
                $ordre = rand(1, $nbJoueurs);
            }while(array_search($ordre, $ordres_prises) !== false);
            $ordres_prises[] = $ordre;
            Joueur::where('idJoueur', $joueur->idJoueur)->update(array('ordre' => $ordre));
        }

        //affectation des chevalets des joueurs
        $reserve =  'AAAAAAAAABBCCDDDEEEEEEEEEEEEEEEFFGGHHIIIIIIIIJKLLLLLMMMNNNNNNOOOOOOPPQRRRRRRSSSSSSTTTTTTUUUUUUVVWXYZ**';
        $joueurs = DB::table('joueurs')->where('Partie', $idPartie)->orderBy('ordre', 'asc')->get()->toArray();
        foreach($joueurs as $joueur){
            $chevalet = "";
            for($i = 0; $i < 7; $i++){
                $indice = rand(0, strlen($reserve));
                $chevalet = $chevalet . substr($reserve, $indice, 1); //concatene $chevalet avec $reserve[$indice];
                $reserve = substr_replace($reserve, '', $indice, 1);
            }
            if(strlen($chevalet) == 6){
                $indice = rand(0, strlen($reserve));
                $chevalet = $chevalet . substr($reserve, $indice, 1);
                $reserve = substr_replace($reserve, '', $indice, 1);
            }
            Joueur::where('idJoueur', $joueur->idJoueur)->update(array('chevalet' => $chevalet));
        }

        //mise a jour de statut partie a en cours
        Partie::where('idPartie', $idPartie)->update(array('statutPartie' => 'en cours'));
        //mise a jour de la reserve de la partie apres affectation des chevalets
        Partie::where('idPartie', $idPartie)->update(array('reserve' => $reserve));
        //mise a jour du staut du joueur ayant l'ordre 1
        Joueur::where('Partie', $idPartie)->where('ordre', 1)->update(array('statutJoueur' => true));

        return response()->json(["Partie préparée"]);
    }

    public function panneau(Request $request, $idPartie)
    {
        $joueurs = DB::table('joueurs')->where('Partie', $idPartie)->get()->toArray();
        $partie = Partie::find($idPartie);
        $reponse = [];
        foreach($joueurs as $joueur){
            $reponse[] = [
                $joueur->nom,
                $joueur->photo,
                $joueur->score,
                strlen($joueur->chevalet),
                $joueur->statutJoueur,
            ];
        }
        
        return response()->json([$reponse, 'Reserve', strlen($partie->reserve)]);
    }

    public function afficheGrille(Request $request, $idPartie)
    {
        $partie = Partie::find($idPartie);
        return response()->json([$partie->grille]);
    }
  
    public function quitte(Request $request, $idJoueur){
   
        $joueur = Joueur::find($idJoueur);
        $chevalet=$joueur->chevalet;
        $partie = Partie::find($joueur->Partie);
        $reserve=$partie->reserve;
        $reserve.=$chevalet;
        Partie::where('idPartie', $joueur->Partie)->update(array('reserve' => $reserve));
        Joueur::where('idJoueur', $idJoueur)->update(array('chevalet'=> ''));
        event(new CloseTab($joueur->nom));
        return response()->json(['joueur a quitté']);
    }

    public function passerTour($idPartie, $idJoueur)
    {
        $partie = Partie::find($idPartie);
        $typePartie = $partie->typePartie;
        Joueur::where('idJoueur', $idJoueur)->update(array('statutJoueur' => false));
        $joueur = Joueur::find($idJoueur);
        $ordre = $joueur->ordre;
        if($ordre == $typePartie){
            $ordre = 1;
        }
        else{
            $ordre = $ordre + 1;
        }
        Joueur::where('Partie', $idPartie)->where('ordre', $ordre)->update(array('statutJoueur' => true));

        $joueur_suivant = Joueur::where('Partie', $idPartie)->where('ordre', $ordre)->first();
        event(new TourPasse($joueur->nom, $joueur_suivant->nom));
    }

    public function echangerLettres($idPartie, $idJoueur, $lettres)
    {
        $joueur = Joueur::find($idJoueur);
        $chevalet = $joueur->chevalet;
        $ordre = $joueur->ordre;
        
        $partie = Partie::find($idPartie);
        $reserve = $partie->reserve;
        $lettres_tab = str_split($lettres);
        $typePartie = $partie->typePartie;
        if($ordre == $typePartie){
            $ordre_suivant = 1;
        }
        else{
            $ordre_suivant = $ordre + 1;
        }
        
        foreach($lettres_tab as $lettre){
            $reserve .= $lettre;
            $i = 0;
            $done = false;
            do{
                if(substr($chevalet, $i, 1) == $lettre){
                    $chevalet = substr_replace($chevalet, '', $i, 1);
                    $done = true;
                }
                else{
                    $i++;
                }
            }while($done == false);
        }
        for($i = 0; $i < strlen($lettres); $i++){
            $indice = rand(0, strlen($reserve));
            $chevalet = $chevalet . substr($reserve, $indice, 1); //concatene $chevalet avec $reserve[$indice];
            $reserve = substr_replace($reserve, '', $indice, 1);
        }
        event(new EchangeEffectue($chevalet));
        $joueur_suivant=Joueur::where('Partie', $idPartie)->where('ordre', $ordre_suivant)->first();
        event(new TourPasse($joueur->nom, $joueur_suivant->nom));
        Joueur::where('idJoueur', $idJoueur)->update(array('chevalet' => $chevalet));
        Joueur::where('idJoueur', $idJoueur)->update(array('statutJoueur' => false));
        Joueur::where('Partie', $idPartie)->where('ordre', $ordre_suivant)->update(array('statutJoueur' => true));
        Partie::where('idPartie', $idPartie)->update(array('reserve' => $reserve));
    }

    public function calculScoreLettre($lettre, $case)
    {
        $valeur = 0;
        switch ($lettre) {
        case "A":
        case "E":
        case "I":
        case "L":
        case "N":
        case "O":
        case "R":
        case "S":
        case "T":
        case "U":
            $valeur=1;
        break;

        case "D":
        case "G":
        case "M":
            $valeur = 2;
        break;

        case "B":
        case "C":
        case "P":
            $valeur = 3;
        break;

        case "F":
        case "H":
        case "V":
            $valeur = 4;
        break;

        case "J":
        case "Q":
            $valeur = 8;
        break;

        case "K":
        case "W":
        case "X":
        case "Y":
        case "Z":
            $valeur = 10;
        break;

        default:
            $valeur = 0;
        }

        $multiple = 1;
        switch($case){
            case '4': case '12': case '37': case '39': case '53': case '46': case '60': case '103':
                case '99': case '129': case '97': case '127': case '93': case '123': case '109': case '117':
                  case '133': case '180': case '166': case '173': case '189': case '187': case '214': case '222': $multiple = 2; break;
            case '21': case '25': case '81': case '85': case '141': case '145': case '77': case '137':
                case '149': case '89': case '205': case '201': $multiple = 3; break;
            default: $multiple = 1;
        }
        return $valeur * $multiple;
    }

    public function placerLettres($idPartie, $idJoueur, $commande)
    {
        $mot = substr($commande, 13);
        $ligne = substr($commande, 8, 1);
        switch($ligne){
            case 'A': $ligne_valeur = 0; break;
            case 'B': $ligne_valeur = 15; break;
            case 'C': $ligne_valeur = 30; break;
            case 'D': $ligne_valeur = 45; break;
            case 'E': $ligne_valeur = 60; break;
            case 'F': $ligne_valeur = 75; break;
            case 'G': $ligne_valeur = 90; break;
            case 'H': $ligne_valeur = 105; break;
            case 'I': $ligne_valeur = 120; break;
            case 'J': $ligne_valeur = 135; break;
            case 'K': $ligne_valeur = 150; break;
            case 'L': $ligne_valeur = 165; break;
            case 'M': $ligne_valeur = 180; break;
            case 'N': $ligne_valeur = 195; break;
            case 'O': $ligne_valeur = 210; break;
        }
        $colonne = substr($commande, 9, 2);
        $colonne_valeur = intval($colonne) - 1;
        $sens = substr($commande, 11, 1);

        $pos_debut = $ligne_valeur + $colonne - 1;
        $partie = Partie::find($idPartie);
        $grille = $partie->grille;
        $reserve = $partie->reserve;
        $mot_tab = str_split($mot);
        $joueur = Joueur::find($idJoueur);
        $chevalet = $joueur->chevalet;
        $score = $joueur->score;

        $score_a_ajouter = 0;
        if($sens == 'H'){
            foreach($mot_tab as $lettre){
                $grille = substr_replace($grille, $lettre, $pos_debut, 1);
                $pos_lettre = strpos($chevalet, $lettre);
                $chevalet = substr_replace($chevalet, '', $pos_lettre, 1);
                $pos_debut++ ;
                $score_a_ajouter += $this->calculScoreLettre($lettre, $pos_debut);
            }
        }
        else{
            foreach($mot_tab as $lettre){
                $grille = substr_replace($grille, $lettre, $pos_debut, 1);
                $pos_lettre = strpos($chevalet, $lettre);
                $chevalet = substr_replace($chevalet, '', $pos_lettre, 1);
                $pos_debut += 15 ;
                $score_a_ajouter += $this->calculScoreLettre($lettre, $pos_debut);
            }
        }

        for($i = 0; $i < strlen($mot); $i++){
            $indice = rand(0, strlen($reserve));
            $chevalet = $chevalet . substr($reserve, $indice, 1);
            $reserve = substr_replace($reserve, '', $indice, 1);
        }

        Joueur::where('idJoueur', $idJoueur)->update(array('chevalet' => $chevalet));
        Partie::where('idPartie', $idPartie)->update(array('grille' => $grille));
        Partie::where('idPartie', $idPartie)->update(array('reserve' => $reserve));

        Joueur::where('idJoueur', $idJoueur)->update(array('score' => $score + $score_a_ajouter));

        Joueur::where('idJoueur', $idJoueur)->update(array('statutJoueur' => false));
        $ordre = $joueur->ordre;
        $typePartie = $partie->typePartie;
        if($ordre == $typePartie){
            $ordre_suivant = 1;
        }
        else{
            $ordre_suivant = $ordre + 1;
        }
        Joueur::where('Partie', $idPartie)->where('ordre', $ordre_suivant)->update(array('statutJoueur' => true));
        $joueur_suivant = DB::table('joueurs')->where('Partie', $idPartie)->where('ordre', $ordre_suivant)->first();
        $nom = $joueur->nom;
        event(new LettresPlaces($grille, $nom, $score + $score_a_ajouter, $joueur_suivant->nom));
        event(new echangeEffectue($chevalet));
    }


}
