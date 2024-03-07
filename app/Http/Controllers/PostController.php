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
            $post->load(['comments' => function ($query) {
                $query->whereNull('parent_id')->with('user')->with(['reply.user'])->take(3);
            }]);
            $post->load('user');
            $post->linkedByUser = $post->likedBy(auth()->user());
            $post->nbLikes = $post->likes->count();

            $post->nbComments = $post->comments()->count();

            // Calculate the number of comments and replies for each post
            $post->comments->each(function ($comment) {
                $comment->linkedByUser = $comment->likedBy(auth()->user());
                $comment->nbLikes = $comment->likes->count();
                $comment->nbReplies = $comment->reply->count();
                $comment->reply->each(function ($reply) {
                    $reply->linkedByUser = $reply->likedBy(auth()->user());
                    $reply->nbLikes = $reply->likes->count();
                });
            });
        });

        return Inertia::render('Feed', [
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
            $query->with('user')->with('reply.user');
        }, 'user'])->find($id);

        $post->nbComments = $post->comments->count();
        $post->nbLikes = $post->likes->count();
        $post->linkedByUser = $post->likedBy(auth()->user());
        $post->comments->each(function ($comment) {
            $comment->linkedByUser = $comment->likedBy(auth()->user());
            $comment->nbLikes = $comment->likes->count();
            $comment->nbReplies = $comment->reply->count();
            $comment->reply->each(function ($reply) {
                $reply->linkedByUser = $reply->likedBy(auth()->user());
                $reply->nbLikes = $reply->likes->count();
            });
        });

        return Inertia::render('Post', ['post' => $post]);
    }


}
