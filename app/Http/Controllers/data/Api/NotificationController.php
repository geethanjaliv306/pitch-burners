<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\DeviceToken;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function storePushToken(Request $request){
        try {
            $validated = $request->validate([
                'device_id' => 'required|string',
                'device_name' => 'required|string',
                'device_type' => 'required|in:android,ios,web',
                'push_token' => 'required|string'
            ]);

            DeviceToken::updateOrCreate(
                ['device_id' => $validated['device_id']],
                [
                    'device_name' => $validated['device_name'],
                    'device_type' => $validated['device_type'],
                    'push_token' => $validated['push_token'],
                    'is_active' => true,
                    'last_used_at' => Carbon::now()
                ]
            );

            Log::info('Push token stored successfully', [
                'device_id' => $validated['device_id'],
                'device_type' => $validated['device_type']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Push token stored successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to store push token', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store push token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function sendPushNotification($data){
		//$data = $request->all();
        Log::info('Sending push notification:', ['data' => $data]);
      	Log::info('Using Expo Access Token:', ['token' => config('services.expo.access_token')]);


        try {
            $tokens = DeviceToken::where('is_active', true)
                ->pluck('push_token')
                ->toArray();

            if (empty($tokens)) {
                Log::warning('No active devices found for notification');
                return response()->json([
                    'status' => 'warning',
                    'message' => 'No active devices found'
                ], 200);
            }

            $successCount = 0;
            $failedTokens = [];

            foreach ($tokens as $token) {
                try {
                  Log::info('Push Token being sent', ['token' => $token]);
                    $notificationPayload = [
                        'to' => $token,
                        'title' => $data['title'],
                        'body' => $data['body'],
                        'data' => $data['data'],
                        'sound' => 'default',
                        '_displayInForeground' => true,
                        'priority' => 'high',
                        'channelId' => 'default',
                        'android' => [
                            'notification' => [
                                'color' => $data['data']['color'] ?? '#000000',
                                'icon' => $data['data']['icon'] ?? '🏏'
                            ]
                        ],
                        'ios' => [
                            'sound' => 'default'
                        ]
                    ];

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . config('services.expo.access_token'),
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ])->post('https://exp.host/--/api/v2/push/send', $notificationPayload);
                    
                    $responseData = $response->json();
                  	Log::info('Response data', ['response' => $responseData]);

                    if ($response->successful()) {
                        $successCount++;
                        Log::info('Notification sent successfully', [
                            'token' => $token,
                            'response' => $responseData
                        ]);
                    } else {
                        $failedTokens[] = $token;
                        Log::error('Failed to send notification', [
                            'token' => $token,
                            'response' => $responseData
                        ]);
                    }
                } catch (\Exception $e) {
                    $failedTokens[] = $token;
                    Log::error('Error sending notification', [
                        'token' => $token,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Notifications sent successfully to {$successCount} devices",
                'data' => [
                    'total_sent' => count($tokens),
                    'successful' => $successCount,
                    'failed' => count($failedTokens)
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to process notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send notifications: ' . $e->getMessage()
            ], 500);
        }
    }

}