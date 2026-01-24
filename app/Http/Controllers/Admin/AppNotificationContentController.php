<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotificationContent;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\NotificationController;

class AppNotificationContentController extends Controller
{
    public function index()
    {
        return view('cms.app-notification.index');
    }

    
    public function sendDirect(Request $request){
    $request->validate([
        'title' => 'required|string',
        'description' => 'required|string'
    ]);

    try {
        $notificationController = new NotificationController();
        
        $notificationData = [
            'title' => $request->title,
            'body' => $request->description,
            'data' => [
                'type' => 'custom_notification',
                'color' => '#2196F3',
                'icon' => '📢',
                'additional_data' => [
                    'sent_at' => now()
                ]
            ]
        ];

        $response = $notificationController->sendPushNotification($notificationData);
        
        if ($response->getStatusCode() === 200) {
            return response()->json([
                'status' => 'success',
                'message' => 'Notification sent successfully!'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send notification'
        ], 500);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error sending notification: ' . $e->getMessage()
        ], 500);
    }
}
}