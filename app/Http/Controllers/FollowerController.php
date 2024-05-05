<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FollowerController extends Controller
{
    public function follow(Request $request)
    {
        $follower = Follower::where('follower_id', auth()->user()->id)
            ->where('following_id', $request->trendingUser)
            ->first();

        if (!$follower) {
            $follower = new Follower;
            $follower->following_id = $request->trendingUser;
            $follower->follower_id = auth()->id();
            $follower->save();
        }
        return redirect()->back();
    }

    public function unfollow(Request $request){
        $follower = Follower::where('follower_id', auth()->user()->id)
            ->where('following_id', $request->trendingUser)
            ->first();
        $follower->delete();

        return redirect()->back();
    }

    public function isFollowing(Request $request, $trendingUserId)
    {
        $isFollowing = Follower::query()
            ->where('follower_id', auth()->id())
            ->where('following_id', $trendingUserId)
            ->exists();

        return response()->json(['isFollowing' => $isFollowing]);
    }


    public function followingCount() {
        $followingCount = Follower::query()
            ->where('follower_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Profile/ProfileApp', [
            'followingCount' => $followingCount->count(),
        ]);
    }

    public function followerCount() {
        $followedCount = Follower::query()
            ->where('following_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Profile/ProfileApp', [
            'followedCount' => $followedCount->count(),
        ]);
        dd($followedCount);
    }
}
