<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartieResource extends JsonResource
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
            'idPartie' => $this->idPartie,
            'typePartie' => $this->typePartie,
            'reserve' => $this->reserve,
            'grille' => $this->grille,
            'dateCreation' => $this->dateCreation,
            'dateDebutPartie' => $this->dateDebutPartie,
            'dateFinPartie' => $this->dateFinPartie,
            'statutPartie' => $this->statutPartie,
            'tempsJoueur' => $this->tempsJoueur,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
