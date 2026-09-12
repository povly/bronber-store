@push('block-styles')
    @vite(['resources/css/blocks/home/advs/style.css'])
@endpush

@php
    // MediaManagerPicker stores disk-relative paths; absolute/URL paths pass through.
    $items = collect($block['items'] ?? [])
        ->filter(static fn ($item): bool => is_array($item))
        ->map(static function (array $item): array {
            $image = $item['image'] ?? null;

            if (! empty($image)) {
                $item['image'] = str_starts_with((string) $image, '/') || str_starts_with((string) $image, 'http')
                    ? (string) $image
                    : '/storage/'.ltrim((string) $image, '/');
            }

            return $item;
        })
        ->values();
@endphp

@if($items->isNotEmpty())
    <section class="home-advs section">
        <div class="container">
            <x-slider
                :config="['pagination' => true, 'breakpoints' => [0 => ['perView' => 1], 768 => ['perView' => 2], 1200 => ['perView' => 4]]]"
                class="home-advs__root"
                viewport-class="home-advs__slider"
                track-class="home-advs__track"
                label="Преимущества">
                @foreach($items as $item)
                    <div class="home-advs__slide slider__slide">
                        @if(!empty($item['title']))
                            <div class="home-advs__title">{{ $item['title'] }}</div>
                        @endif
                        @if(!empty($item['image']))
                            <div class="home-advs__svg">
                                <x-img path="{{ $item['image'] }}" />
                            </div>
                        @endif
                        @if(!empty($item['text']))
                            <div class="home-advs__text">{{ $item['text'] }}</div>
                        @endif
                    </div>
                @endforeach

                <x-slot:nav>
                    <x-slider-pagination class="home-advs__pagination" />
                </x-slot:nav>
            </x-slider>

            <div class="home-advs__pc">
                @foreach($items as $item)
                    <div class="home-advs__slide slider__slide">
                        @if(!empty($item['title']))
                            <div class="home-advs__title">{{ $item['title'] }}</div>
                        @endif
                        @if(!empty($item['image']))
                            <div class="home-advs__svg">
                                <x-img path="{{ $item['image'] }}" />
                            </div>
                        @endif
                        @if(!empty($item['text']))
                            <div class="home-advs__text">{{ $item['text'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
