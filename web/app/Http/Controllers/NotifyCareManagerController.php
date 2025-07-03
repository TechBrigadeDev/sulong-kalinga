<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;

class NotifyCareManagerController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Test sending an email notification to all care managers.
     * Example usage: POST /test-notify-care-managers
     */
    public function notifyAll(Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $results = $this->notificationService->notifyAllCareManagersByEmail(
            $request->input('subject'),
            $request->input('message')
        );

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
}
