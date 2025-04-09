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
            ->subject('Pesanan Baru Diterima: #' . $this->order->order_number)
            ->greeting('Halo Admin!')
            ->line('Pesanan baru telah dibuat oleh ' . $this->order->user->name . '.')
            ->line('Nomor Pesanan: ' . $this->order->order_number)
            ->line('Total Pesanan: Rp' . number_format($this->order->grand_total, 0, ',', '.'))
            ->line('Metode Pembayaran: ' . ucfirst(str_replace('_', ' ', $this->order->payment_method)))
            ->action('Lihat Detail Pesanan', $orderUrl)
            ->line('Terima kasih telah menggunakan aplikasi kami!');
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