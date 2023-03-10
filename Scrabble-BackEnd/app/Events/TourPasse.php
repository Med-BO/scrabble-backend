<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TourPasse implements ShouldBroadcast
{ 
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $nom;
    public $nom_suiv;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($nom, $nom_suiv)
    {
        $this->nom = $nom;
        $this->nom_suiv = $nom_suiv;
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
