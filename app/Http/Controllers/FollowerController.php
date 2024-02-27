<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use Illuminate\Http\Request;

class FollowerController extends Controller
{
    public function store(Request $request)
    {
        $follower = new Follower;
        $follower->follower_user_id= $request->follower_user_id;
        $follower->user_id = auth()->id();
        $follower->save();

    }
}
