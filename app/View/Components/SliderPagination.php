<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SliderPagination extends Component
{
    public function __construct(
        public string $class = '',
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.slider.pagination');
    }
}
