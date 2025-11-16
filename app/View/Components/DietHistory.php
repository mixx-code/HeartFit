<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DietHistory extends Component
{
    /** @var string */
    public string $title;

    /** @var string */
    public string $emptyText;

    /** @var int|null */
    public ?int $limit;

    /** @var array<int,array<string,mixed>> */
    public array $items;

    /**
     * items: [
     *   [
     *     'period' => 'Okt 2025 (Batch II)',
     *     'start_date' => '2025-10-01',
     *     'end_date' => '2025-10-24',
     *     'package' => 'Bulanan',
     *     'delivered_days' => 22,
     *     'serve_days' => 24,
     *     'status' => 'Selesai', // Selesai | Berjalan | Dibatalkan
     *     'weight_start' => 72.5, // optional
     *     'weight_end' => 69.8,   // optional
     *     'notes' => 'Good adherence' // optional
     *   ],
     *   ...
     * ]
     */
    public function __construct(
        ?array $items = null,
        string $title = 'Riwayat Diet',
        string $emptyText = 'Belum ada riwayat diet.',
        ?int $limit = 5
    ) {
        $this->title     = $title;
        $this->emptyText = $emptyText;
        $this->limit     = $limit;

        // Dummy default jika belum dikirim dari controller
        $dummy = [
            [
                'period'         => 'Oktober 2025 (Batch II)',
                'start_date'     => '2025-10-01',
                'end_date'       => '2025-10-24',
                'package'        => 'Bulanan',
                'delivered_days' => 22,
                'serve_days'     => 24,
                'status'         => 'Selesai',
                'weight_start'   => 72.5,
                'weight_end'     => 69.8,
                'notes'          => 'Adherence 92%',
            ],
            [
                'period'         => 'September 2025 (Batch I)',
                'start_date'     => '2025-09-02',
                'end_date'       => '2025-09-25',
                'package'        => 'Mingguan',
                'delivered_days' => 14,
                'serve_days'     => 16,
                'status'         => 'Selesai',
                'weight_start'   => 74.0,
                'weight_end'     => 72.8,
                'notes'          => 'Sempat skip 2 hari',
            ],
        ];

        $this->items = $items ?? $dummy;

        // Terapkan limit jika ada
        if ($this->limit && count($this->items) > $this->limit) {
            $this->items = array_slice($this->items, 0, $this->limit);
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.diet-history');
    }
}
