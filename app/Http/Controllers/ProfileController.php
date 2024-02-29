<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    //
    public function index()
    {
        $userPost = Post::query()
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $userPost->each(function ($post) {
            $post->load(['comments.user' => function ($query) {
                $query->take(3);
            }]);
            $post->load('user');
            $post->linkedByUser = $post->likedBy(auth()->user());
            $post->nbComments = $post->comments->count();
            $post->nbLikes = $post->likes->count();
        });

        return Inertia::render('Profile/ProfileApp', [
            'posts' => $userPost
                ->load('comments.user')
                ->load('likes')
                ->load('user'),
        ]);

    }
}
