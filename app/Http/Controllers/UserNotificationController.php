<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class UserNotificationController extends Controller
{
    //
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->get();
        auth()->user()->unreadNotifications->markAsRead();
        return Inertia::render('Notification', [
            'notifications' => $notifications
        ]);
    }
}
