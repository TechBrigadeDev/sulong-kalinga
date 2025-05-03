<?php
namespace App\Http\Controllers;

use App\Models\SupabaseNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $notification = SupabaseNotification::create([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'body' => $validated['body'],
            'is_read' => false,
            'created_at' => now(),
        ]);

        return response()->json(['success' => true, 'notification' => $notification]);
    }
}
