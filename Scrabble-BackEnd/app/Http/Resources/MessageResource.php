<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'idMessage' => $this->idMessage,
            'dateCreation' => $this->dateCreation,
            'envoyeur' => $this->envoyeur,
            'Partie' => $this->Partie,
            'contenu' => $this->contenu,
            'statutMessage' => $this->statutMessage,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
