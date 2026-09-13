@push('block-styles')
    @vite(['resources/css/blocks/about/style.css'])
@endpush

@push('block-scripts')
    @vite(['resources/js/blocks/about/index.js'])
@endpush

@php
    // Заголовки устройств: ПК — кнопка слайдера, телефон — шапка аккордеона.
    // Поля взаимно фолбэчатся друг в друга; date — legacy-поле, удалённое
    // из админки, но сохранённые элементы могут его ещё нести.
    $timeline = array_map(static function (array $item): array {
        $desktop = (string) ($item['title_desktop'] ?? '');
        $mobile = (string) ($item['title_mobile'] ?? '');
        $legacyDate = (string) ($item['date'] ?? '');

        $item['heading_desktop'] = $desktop !== '' ? $desktop : ($mobile !== '' ? $mobile : $legacyDate);
        $item['heading_mobile'] = $mobile !== '' ? $mobile : ($desktop !== '' ? $desktop : $legacyDate);

        return $item;
    }, $block['items'] ?? []);
@endphp

<section class="about" x-data="about()">
    <div class="container">
        <h1 class="about__title section__title">{{ $block['title'] }}</h1>

        @if (count($timeline) > 0)
            <div class="about__desktop">
                <x-slider :config="['breakpoints' => [0 => ['perView' => 2], 768 => ['perView' => 3], 1200 => ['perView' => 5]]]" viewport-class="about__years" track-class="about__years-track"
                    :label="$block['title']">
                    @foreach ($timeline as $i => $item)
                        <div class="about__year-slide slider__slide"
                            :class="{ 'is-active': active === {{ $i }} }">
                            <span class="about__year-marker">
                                <svg width="27" height="23" viewBox="0 0 27 23" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.4238 22.5L0.000434935 -6.21215e-07L26.8472 1.72581e-06L13.4238 22.5Z"
                                        fill="black" />
                                </svg>
                            </span>
                            <button type="button" class="about__year"
                                @click="select({{ $i }}); $nextTick(() => scrollToReveal({{ $i }}))">
                                {{ $item['heading_desktop'] }}
                            </button>
                        </div>
                    @endforeach
                </x-slider>

                <div class="about__separator"></div>

                <div class="about__panels">
                    @foreach ($timeline as $i => $item)
                        <div class="about__panel" x-show="active === {{ $i }}" x-collapse>
                            <div class="about__text">
                                @if (! empty($item['text']))
                                    {!! app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render((string) $item['text']) !!}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="about__accordion">
                <div class="about__acc-separator"></div>
                @foreach ($timeline as $i => $item)
                    <div class="about__acc-item" :class="{ 'is-open': accActive === {{ $i }} }">
                        <button type="button" class="about__acc-header" @click="accToggle({{ $i }})"
                            :aria-expanded="accActive === {{ $i }}">
                            <span class="about__acc-title">{{ $item['heading_mobile'] }}</span>
                            <span class="about__acc-chevron">
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                        <div class="about__acc-content" x-show="accActive === {{ $i }}" x-collapse
                            @if ($i !== 0) x-cloak @endif>
                            <div class="about__text">
                                @if (! empty($item['text']))
                                    {!! app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render((string) $item['text']) !!}
                                @endif
                            </div>
                        </div>
                        <div class="about__acc-separator"></div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
