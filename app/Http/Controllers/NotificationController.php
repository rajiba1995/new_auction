<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        // Assuming you have a User model and the notifications relationship set up correctly
        Auth::user()->notifications()->update(['read_at' => now(), 'view_count' => 1]);
        return response()->json(['success' => true]);
    }
}
