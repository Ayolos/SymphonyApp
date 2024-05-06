<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\In;
use Inertia\Inertia;

class PostController extends Controller
{
    //
    public function index()
    {
        $posts = Post::orderBy('created_at', 'desc')
            ->get();

        $trendingUsers = User::all();

        return Inertia::render('Feed', [
            'posts' => $posts,
            'trendingUsers' => $trendingUsers
        ]);
    }


    public function store(Request $request)
    {
        if ($request->file('file')->isValid()) {
            $file = $request->file('file');
            $path = $file->store('songs');

            if (!$path) {
                // Handle error if file storage fails
                return redirect()->back()->with('error', 'File upload failed.');
            }

            $post = new Post;
            $post->content = $request->content;
            $post->user_id = auth()->id();
            $post->save();

            $song = new Song;
            $song->post_id = $post->id;
            $song->path = $path;
            $song->save();

            return redirect()->back()->with('success', 'File uploaded successfully.');
        } else {
            // Handle invalid file
            return redirect()->back()->with('error', 'Invalid file.');
        }
    }

    public function show($id)
    {
        $post = Post::find($id);
        return Inertia::render('Post', [
            'post' => $post
        ]);
    }


}
