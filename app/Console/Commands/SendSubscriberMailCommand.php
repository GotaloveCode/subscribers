<?php

namespace App\Console\Commands;

use App\Mail\NewPostMail;
use App\Models\Post;
use App\Models\Story;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSubscriberMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:subscribers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send new post email to subscribers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $new_posts = Post::query()->published()->current()->get();
        $posted_stories = Story::query()->whereIn('post_id', $new_posts->pluck('post_id'))->get();
        $subscribers = Subscription::with('user')->whereIn('website_id', $new_posts->pluck('website_id'))->get();

        foreach ($new_posts as $post) {
            $stories = [];
            foreach ($subscribers as $subscriber) {
                $posted = $posted_stories->where(fn($story) => $story->post_id == $post->id && $story->user_id == $subscriber->user_id);
                if ($posted) break;
                Mail::to($subscriber->user)->queue(new NewPostMail($post));
                $story = new Story();
                $story->post_id = $post->id;
                $story->user_id = $subscriber->user_id;
                $stories[] = $story->attributesToArray();
            }
            Story::insert($stories);
        }

        return Command::SUCCESS;
    }
}
