<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\In;
use Inertia\Inertia;

class PostController extends Controller
{
    //
    public function index()
    {
        $posts = Post::with('comments') // Load the comments relationship
            ->with('user') // Load the user relationship
            ->orderBy('created_at', 'desc')
            ->get();
        return Inertia::render('Welcome', ['posts' => $posts]);
    }


    public function store(Request $request)
    {
        $post = new Post;
        $post->content = $request->content;
        $post->user_id = auth()->id();
        $post->save();

    }


}
