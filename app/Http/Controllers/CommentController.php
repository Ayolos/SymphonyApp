<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CommentController extends Controller
{
    //
    public function index()
    {
        $comments = Comment::orderBy('created_at', 'desc')->get();
        $comments->each(function ($comment) {
            $comment->load('post');
            $comment->load('user');
        });

        return Inertia::render('Welcome', ['comments' => $comments]);
    }
    public function store(Request $request)
    {
        $comment = new Comment;
        $comment->content = $request->content;
        $comment->user_id = auth()->id();
        $comment->post_id = $request->post_id;
        $comment->save();
    }
}
