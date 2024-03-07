<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class UserController extends Controller
{
//    public function index()
//    {
//
//
//        //dd(Inertia::render('SymphonyLayout', ['users' => $users]));
//
//        return Inertia::render('SymphonyLayout', ['users' => $users]);
//
//    }

    public function following(Request $request)
    {
        $follower = new Follower;
        $follower->follower_id = auth()->id();
        $follower->following_id = $request->trendingUser;
        $follower->save();
    }
}
