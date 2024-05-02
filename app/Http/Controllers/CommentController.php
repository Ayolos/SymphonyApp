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


    }
    public function store(Request $request)
    {
        $comment = new Comment;
        $comment->content = $request->content;
        $comment->user_id = auth()->id();
        $comment->post_id = $request->post_id;
        $comment->save();
    }

    public function reply(Request $request)
    {
        $comment = new Comment;
        $comment->content = $request->content;
        $comment->user_id = auth()->id();
        $comment->post_id = $request->post_id;
        $comment->parent_id = $request->parent_id;
        $comment->save();
    }
}
