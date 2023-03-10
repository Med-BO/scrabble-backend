<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LettresPlaces implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $grille;
    public $nom;
    public $score;
    public $nom_suivant;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($grille, $nom, $score, $nom_suivant)
    {
        $this->grille = $grille;
        $this->nom = $nom;
        $this->score = $score;
        $this->nom_suivant = $nom_suivant;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('my-channel');
    }
}
