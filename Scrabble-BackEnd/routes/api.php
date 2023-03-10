<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\JoueurController;
use App\Http\Controllers\API\PartieController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\InscriptionController;

use App\Http\Controllers\API\SalleattenteController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::resource('joueurs', JoueurController::class);
Route::resource('parties', PartieController::class);
Route::resource('messages', MessageController::class);

//inscription
Route::post('inscrire', [InscriptionController::class, 'inscrire']);
//joueurs d'nne partie
Route::get('partie/{idPartie}/joueurs',[SalleattenteController::class, 'getJoueurs']);
//rend le statut d'une partie 'en cours'
Route::get('partie/{idPartie}/statut', [SalleattenteController::class, 'changerStatut']);
//rend la vue de la partie en cours pour un joueur (contient le chevalet)
Route::get('joueur/{idJoueur}', [JoueurController::class, 'vueJeu']);

//retourne les infos du panneau informatif d'une partie
Route::get('partie/{idPartie}/panneau', [PartieController::class, 'panneau']);

//préparation d'une partie
Route::get('partie/{idPartie}/preparer',[PartieController::class, 'preparerPartie']);
//rend la liste des messages 
Route::get('partie/{idPartie}/messages',[MessageController::class, 'getMessages']);
//inserer un message 
Route::post('partie/{idPartie}/joueur/{idJoueur}/envoyer', [MessageController::class, 'envoiMessage']);
//afficher grille
Route::get('/partie/{idPartie}/grille',[PartieController::class,'afficheGrille']);
//
Route::get('joueur/{idJoueur}/quitte',[PartieController::class,'quitte']);
//supprimer un joueur de la salle d'attente
Route::get('joueur/{idJoueur}/supprimeJoueur',[SalleattenteController::class,'supprimeJoueur']);

//retourne l'image du joueur
Route::get('photo/{filename}', function ($filename)
{
    $path = storage_path('app\public\images_joueurs\\' . $filename);
    $file = File::get(storage_path('app\public\images_joueurs\\' . $filename));
    $type = File::mimeType($path);
    //dd($file);
    return response($file, 200)->header('Content-Type', $type);
});