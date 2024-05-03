<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LikeController extends Controller
{
    //
    public function likePost(Request $request, $id)
    {
        //check if the user has already liked the post
        $like = Like::where('user_id', auth()->user()->id)
            ->where('likeable_id', $id)
            ->where('likeable_type', Post::class)
            ->first();

        if (!$like) {
            $like = new Like();
            $like->user_id = auth()->user()->id;
            $like->likeable_id = $id;
            $like->likeable_type = Post::class;
            $like->save();
        }
        return redirect()->back();
    }

    public function unlikePost(Request $request, $id)
    {
        $like = Like::where('user_id', auth()->user()->id)
            ->where('likeable_id', $id)
            ->where('likeable_type', Post::class)
            ->first();
        $like->delete();

        return redirect()->back();
    }

    public function likeComment(Request $request, $id)
    {
        //check if the user has already liked the post
        $like = Like::where('user_id', auth()->user()->id)
            ->where('likeable_id', $id)
            ->where('likeable_type', Comment::class)
            ->first();

        if (!$like) {
            $like = new Like();
            $like->user_id = auth()->user()->id;
            $like->likeable_id = $id;
            $like->likeable_type = Comment::class;
            $like->save();
        }
        return redirect()->back();
    }

    public function unlikeComment(Request $request, $id)
    {
        $like = Like::where('user_id', auth()->user()->id)
            ->where('likeable_id', $id)
            ->where('likeable_type', Comment::class)
            ->first();
        //dd($like);
        $like->delete();

        return redirect()->back();
    }
}
