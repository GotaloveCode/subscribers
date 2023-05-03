<?php

namespace App\Observers;

use App\Events\PostPublished;
use App\Models\Post;

class PostObserver
{
    /**
     * Handle the Post "creating" event.
     *
     * @param Post $post
     * @return void
     */
    public function created(Post $post): void
    {
        if ($post->status == "published"){
            event(new PostPublished($post));
        }
    }
}
