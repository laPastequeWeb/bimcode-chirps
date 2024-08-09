<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\PostCreated;
use App\Notifications\NewPost;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPostCreatedNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        $user = $event->post->user;
        foreach ($user->subscribers()->cursor() as $follower) {
            $follower->notify(new NewPost($event->post));
        }
    }
}
