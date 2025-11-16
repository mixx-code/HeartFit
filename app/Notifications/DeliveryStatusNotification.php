<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class DeliveryStatusNotification extends Notification
{
    use Queueable;

    public $deliveryData;

    public function __construct($deliveryData)
    {
        $this->deliveryData = $deliveryData;
    }

    public function via($notifiable)
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable)
    {
        return (new WebPushMessage)
            ->title('Status Pengantaran Diperbarui')
            ->icon('/icon.png')
            ->body("Pengantaran {$this->deliveryData['shift']} sekarang {$this->deliveryData['status']}")
            ->action('Lihat Detail', 'view_delivery')
            ->data([
                'url' => route('delivery.details', $this->deliveryData['order_id'])
            ]);
    }
}
