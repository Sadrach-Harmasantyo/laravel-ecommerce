<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderUrl = route('filament.admin.resources.orders.view', $this->order->id);
        
        return (new MailMessage)
            ->subject('New Order Received: #' . $this->order->order_number)
            ->greeting('Hello Admin!')
            ->line('A new order has been placed by ' . $this->order->user->name . '.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Order Total: Rp' . number_format($this->order->grand_total, 0, ',', '.'))
            ->line('Payment Method: ' . ucfirst(str_replace('_', ' ', $this->order->payment_method)))
            ->action('View Order Details', $orderUrl)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->grand_total,
            'customer' => $this->order->user->name,
        ];
    }
}