<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Btn extends Component
{
    public function __construct(
        public ?string $text = null,
        public ?string $href = null,
        public ?string $icon = null,
        public string $variant = 'primary',
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.btn');
    }
}
