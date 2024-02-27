<?php

namespace App\Http\Controllers;

use App\Models\Following;
use Illuminate\Http\Request;

class FollowingController extends Controller
{
    public function store(Request $request)
    {
        $following = new Following;
        $following->following_user_id= $request->following_user_id;
        $following->user_id = auth()->id();
        $following->save();

    }
}
