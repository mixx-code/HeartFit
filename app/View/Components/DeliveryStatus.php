<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DeliveryStatus extends Component
{
    /** @var mixed|null */
    public $row;

    /** @var string */
    public $date;

    /** @var array */
    public $steps;

    public function __construct($row = null, string $date = '', array $steps = [])
    {
        $this->row   = $row;
        $this->date  = $date;
        $this->steps = $steps ?: ['PENDING', 'DIPROSES', 'SEDANG DIKIRIM', 'SAMPAI'];
    }

    public function render(): View|Closure|string
    {
        return view('components.delivery-status');
    }
}
