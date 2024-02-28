<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\In;
use Inertia\Inertia;

class PostController extends Controller
{
    //
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')->get();
        $posts->each(function ($post) {
            $post->load(['comments.user' => function ($query) {
                $query->take(3);
            }]);
            $post->load('user');
            $post->linkedByUser = $post->likedBy(auth()->user());
            $post->nbComments = $post->comments->count();
            $post->nbLikes = $post->likes->count();
        });

        return Inertia::render('Welcome', [
            'posts' => $posts,
        ]);
    }


    public function store(Request $request)
    {
        $post = new Post;
        $post->content = $request->content;
        $post->user_id = auth()->id();
        $post->save();

    }

    public function show($id)
    {
        $post = Post::with(['comments' => function ($query) {
            $query->with('user');
        }, 'user'])->find($id);

        return Inertia::render('Post', ['post' => $post]);
    }


}
