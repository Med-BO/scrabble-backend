<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JoueurResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'idJoueur' => $this->idJoueur,
            'nom' => $this->nom,
            'photo' => $this->photo,
            'chevalet' => $this->chevalet,
            'score' => $this->score,
            'statutJoueur' => $this->statutJoueur,
            'Partie' => $this->Partie,
            'ordre' => $this->ordre,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}