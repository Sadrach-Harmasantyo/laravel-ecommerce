<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Notification;

class AdminNotificationService
{
    /**
     * Send a notification to all admin users about a new order
     *
     * @param Order $order
     * @return void
     */
    public static function notifyAdminsAboutNewOrder(Order $order)
    {
        // Get all admin users
        $admins = User::where('role', 'admin')->get();
        
        // Send notification to all admins
        Notification::send($admins, new NewOrderNotification($order));
    }
}