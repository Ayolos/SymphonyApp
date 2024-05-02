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


        return Inertia::render('Profile/ProfileApp', [
            'posts' => $userPost,
            'nbPosts' => $userPost->count(),
        ]);

    }

    public function show($id)
    {
        $userPost = Post::query()
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Profile/ProfileApp', [
            'posts' => $userPost,
            'nbPosts' => $userPost->count(),
        ]);
    }
}
