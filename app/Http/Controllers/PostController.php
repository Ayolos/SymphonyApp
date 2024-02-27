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
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get(); // Fetch all posts from the database
        return Inertia::render('Welcome', ['posts' => $posts]); // Pass the posts to the view
    }


    public function store(Request $request)
    {
        $post = new Post;
        $post->content = $request->content;
        $post->user_id = auth()->id();
        $post->save();

    }


}
