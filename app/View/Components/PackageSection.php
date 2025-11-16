<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PackageSection extends Component
{
    /** @var string */
    public string $title;

    /** @var string */
    public string $subtitle;

    /** @var array<int,array<string,mixed>> */
    public array $packages;

    /** @var string|null */
    public ?string $orderRoute;

    /**
     * @param array<int,array<string,mixed>>|null $packages
     */
    public function __construct(
        string $title = 'HeartFit Diet Reguler',
        string $subtitle = 'Pilihan paket makan sehat untuk kebutuhan harian Anda.',
        ?array $packages = null,
        ?string $orderRoute = 'orders.create'
    ) {
        $this->title      = $title;
        $this->subtitle   = $subtitle;
        $this->orderRoute = $orderRoute;

        // Dummy default jika belum kirim dari controller
        $this->packages = $packages ?? [
            [
                'label'    => 'Reguler',
                'price'    => 50000,
                'features' => ['Menu seimbang', 'Pilihan karbo sehat'],
                'button'   => ['text' => 'Pesan Paket', 'variant' => 'outline-primary'],
            ],
            [
                'label'    => 'Mingguan',
                'price'    => 400000,
                'features' => ['4 hari × 2 kali makan', '8 hari × 1 kali makan'],
                'button'   => ['text' => 'Pesan Paket', 'variant' => 'primary'],
            ],
            [
                'label'    => 'Bulanan',
                'price'    => 1180000,
                'features' => ['12 hari × 2 kali makan', '24 hari × 1 kali makan'],
                'button'   => ['text' => 'Pesan Paket', 'variant' => 'primary'],
            ],
            [
                'label'    => '3 Bulanan',
                'price'    => 3540000,
                'features' => ['36 hari × 2 kali makan', '72 hari × 1 kali makan'],
                'button'   => ['text' => 'Pesan Paket', 'variant' => 'primary'],
            ],
        ];
    }

    public function render(): View|Closure|string
    {
        return view('components.package-section');
    }
}
