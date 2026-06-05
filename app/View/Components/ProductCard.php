<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductCard extends Component
{
    public function __construct(
        public readonly string $title,
        public readonly string $image,
        public readonly string $price,
        public readonly ?string $href = null,
        public readonly ?string $imageAlt = null,
        public readonly ?string $oldPrice = null,
        public readonly float $rating = 0,
        public readonly int $reviewsCount = 0,
        public readonly bool $inStock = true,
        public readonly ?string $discount = null,
        public readonly ?string $sale = null,
        public readonly ?string $tag = null,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.product-card');
    }
}
