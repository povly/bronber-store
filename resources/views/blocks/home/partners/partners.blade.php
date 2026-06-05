@push('block-styles')
    @vite(['resources/css/blocks/home/partners/style.css'])
@endpush

<section class="home-partners" x-data="slider({ breakpoints: { 0: 3, 768: 4, 1200: 6 } })" @resize.window.debounce.150ms="onResize()">
    <div class="container">
        <div class="home-partners__header">
            <h2 class="home-partners__heading">Наши партнеры</h2>
            <div class="home-partners__arrows">
                <button class="home-partners__arrow" type="button" aria-label="Назад" @click="prev()" :disabled="!canPrev">
                    <svg width="20" height="20" viewBox="0 0 16 16" fill="none"><path d="M10 4L6 8l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <button class="home-partners__arrow" type="button" aria-label="Вперёд" @click="next()" :disabled="!canNext">
                    <svg width="20" height="20" viewBox="0 0 16 16" fill="none"><path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </div>

        <div class="home-partners__slider slider">
            <div class="home-partners__track slider__track" x-ref="track"
                 @pointerdown.prevent="onPointerDown($event)"
                 @pointermove.window="onPointerMove($event)"
                 @pointerup.window="onPointerUp()"
                 @pointercancel.window="onPointerUp()">
                @foreach(['Akrapovič','BMC','Eventuri','Armytrix','Capristo','IPE','Quicksilver','Milltek'] as $name)
                <div class="home-partners__slide slider__slide">
                    <div class="home-partners__item" aria-label="{{ $name }}">
                        <img src="https://placehold.co/160x60/eaeaea/bfbfbf?text={{ urlencode($name) }}" alt="{{ $name }}" width="160" height="60" loading="lazy">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
