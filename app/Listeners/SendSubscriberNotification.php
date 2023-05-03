<?php

namespace App\Listeners;

use App\Events\PostPublished;
use App\Mail\NewPostMail;
use App\Models\Story;
use App\Models\Subscription;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendSubscriberNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PostPublished  $event
     * @return void
     */
    public function handle(PostPublished $event)
    {
        $post =  $event->post;
        //get subscribers
        $subscribers = Subscription::with('user')->where('website_id',$post->website_id)->get();
        $stories = [];
        foreach ($subscribers as $subscriber){
            Mail::to($subscriber->user)->queue(new NewPostMail($post));
            $story = new Story();
            $story->post_id = $post->id;
            $story->user_id = $subscriber->user_id;
            $stories[] = $story->attributesToArray();
        }
        Story::insert($stories);
    }
}
