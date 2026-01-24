<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Http\Controllers\Api\NotificationController;

class SendMatchNotification implements ShouldQueue , ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notificationData;
    public $tries = 5;
    public $backoff = 10;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notificationData)
    {
        // Log::info('executedheereeeeeeee');
        $this->notificationData = $notificationData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        Log::info('notification job executed');
        try {
            if ($this->notificationData) {
                $notificationController = new NotificationController();
                $notificationController->sendPushNotification($this->notificationData);
                $this->delete(); 
            }
        } catch (\Exception $e) {
            Log::error("Notification failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function backoff()
    {
        return $this->backoff;
    }
}
