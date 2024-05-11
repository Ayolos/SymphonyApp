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


        $trendingUsers = User::withCount('followers')
            ->where('id', '!=', auth()->id())
            ->orderByDesc('followers_count') // Trier par le plus grand nombre de followers
            ->take(10) // Limiter à 10 résultats
            ->get();

        return Inertia::render('Feed', [
            'posts' => $posts,
            'trendingUsers' => $trendingUsers
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required',
            'file' => 'required'
        ]);
        if ($request->file('file')->isValid()) {
            $file = $request->file('file');
            $path = $file->store('songs');

            if (!$path) {
                // Handle error if file storage fails
                request()->session()->flash('alert', [
                    'type' => 'error',
                    'message' => 'Fichier non enregistré. Veuillez réessayer.',
                ]);
            }

            $post = new Post;
            $post->content = $request->content;
            $post->user_id = auth()->id();
            $post->save();

            $song = new Song;
            $song->post_id = $post->id;
            $song->path = $path;
            $song->save();

            request()->session()->flash('alert', [
                'type' => 'success',
                'message' => 'Post ajouté avec succès',
            ]);
        } else {
            // Handle invalid file
            request()->session()->flash('alert', [
                'type' => 'error',
                'message' => 'Fichier invalide. Veuillez réessayer.',
            ]);
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
