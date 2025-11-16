<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use App\Notifications\DeliveryPushNotification;
use App\Models\User;

class DeliveryStatusUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public int $userId;
    public int $deliveryStatusId;
    public int $orderId;
    public string $shift;
    public string $status;
    public string $date;
    public ?string $note;

    public function __construct(
        int $userId,
        int $deliveryStatusId,
        int $orderId,
        string $shift,
        string $status,
        string $date,
        ?string $note = null
    ) {
        $this->userId           = $userId;
        $this->deliveryStatusId = $deliveryStatusId;
        $this->orderId          = $orderId;
        $this->shift            = $shift;
        $this->status           = $status;
        $this->date             = $date;
        $this->note             = $note;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('user.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'delivery.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'delivery_status_id' => $this->deliveryStatusId,
            'order_id' => $this->orderId,
            'shift' => $this->shift,
            'status' => $this->status,
            'date' => $this->date,
            'note' => $this->note,
            'message' => $this->getBroadcastMessage(),
        ];
    }

    protected function getBroadcastMessage(): string
    {
        $statusText = [
            'pending' => 'sedang menunggu',
            'diproses' => 'sedang diproses',
            'sedang dikirim' => 'sedang dikirim',
            'sampai' => 'sudah sampai',
            'gagal dikirim' => 'gagal dikirim'
        ];

        $status = $statusText[$this->status] ?? $this->status;

        return "Pengantaran {$this->shift} untuk pesanan #{$this->orderId} {$status}";
    }
}
