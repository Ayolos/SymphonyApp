<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FollowerController extends Controller
{
    public function follow(Request $request){
        $request->validate([
            'following_id' => 'required | exists:users,id'
        ]);
        $follower = new Follower;
        $follower->user_id = $request->following_id;
        $follower->follower_id = auth()->id();
        $follower->save();
        request()->session()->flash('alert', [
            'type' => 'success',
            'message' => 'Vous suivez cet utilisateur.',
        ]);
    }

    public function unfollow(Request $request){
        $request->validate([
            'following_id' => 'required | exists:users,id'
        ]);
        $follower = Follower::where('user_id', $request->following_id)
            ->where('follower_id', auth()->id())
            ->first();
        $follower->delete();
        request()->session()->flash('alert', [
            'type' => 'success',
            'message' => 'Vous ne suivez plus cet utilisateur.',
        ]);
    }
}
