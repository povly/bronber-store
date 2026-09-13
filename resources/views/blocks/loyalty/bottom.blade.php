@push('block-styles')
    @once
        @vite(['resources/css/blocks/loyalty/bottom/style.css'])
    @endonce
@endpush

@php
    // Prototype markup of blocks/loyalty/bottom/bottom.blade.php, data now
    // comes from the flexible-layouts block. Left column: example rows
    // (left/right text, optional color — an inline color: style replaces
    // the prototype's --accent modifier) and the gift banner. Right
    // column: FAQ accordion (Alpine, first item open).
    $normalize = static fn (mixed $value): ?string => trim((string) ($value ?? '')) === ''
        ? null
        : (str_starts_with((string) $value, '/') || str_starts_with((string) $value, 'http')
            ? (string) $value
            : '/storage/'.ltrim((string) $value, '/'));

    $title = $block['title'] ?? '';
    $rows = collect($block['rows'] ?? [])
        ->filter(static fn ($row): bool => is_array($row))
        ->values();
    $giftIcon = $normalize($block['gift_icon'] ?? null);
    $giftTitle = $block['gift_title'] ?? '';
    $giftText = $block['gift_text'] ?? '';
    $faqTitle = $block['faq_title'] ?? '';
    $faqs = collect($block['faqs'] ?? [])
        ->filter(static fn ($faq): bool => is_array($faq))
        ->values();
@endphp

<section class="loyalty-bottom section">
    <div class="container loyalty-bottom__container">
        <div class="loyalty-bottom__example">
            @if (trim((string) $title) !== '')
                <h2 class="loyalty-bottom__title section__title">{{ $title }}</h2>
            @endif
            <div class="loyalty-bottom__example-card">
                @foreach ($rows as $row)
                    <div class="loyalty-bottom__example-row"
                        @if (! empty($row['color'])) style="color: {{ $row['color'] }}" @endif>
                        <span class="loyalty-bottom__example-label">{{ $row['left_text'] ?? '' }}</span>
                        <span class="loyalty-bottom__example-value">{{ $row['right_text'] ?? '' }}</span>
                    </div>
                @endforeach
                <div class="loyalty-bottom__banner">
                    <div class="loyalty-bottom__banner-left">
                        @if ($giftIcon !== null)
                            <span class="loyalty-bottom__banner-icon"><x-img path="{{ $giftIcon }}" /></span>
                        @endif
                        <div class="loyalty-bottom__banner-title loyalty-bottom__banner-title--mb">
                            {{ $giftTitle }}
                        </div>
                    </div>
                    <div class="loyalty-bottom__banner-text loyalty-bottom__banner-text--mb">{{ $giftText }}</div>

                    <div class="loyalty-bottom__banner-body">
                        <div class="loyalty-bottom__banner-title">{{ $giftTitle }}</div>
                        <div class="loyalty-bottom__banner-text">{{ $giftText }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="loyalty-bottom__faq">
            @if (trim((string) $faqTitle) !== '')
                <h2 class="loyalty-bottom__title section__title">{{ $faqTitle }}</h2>
            @endif
            <div class="loyalty-bottom__faq-list" x-data="{ open: 0 }">
                @foreach ($faqs as $i => $faq)
                    <div class="loyalty-bottom__faq-item" :class="{ 'is-open': open === {{ $i }} }">
                        <button type="button" class="loyalty-bottom__faq-question"
                            @click="open = open === {{ $i }} ? null : {{ $i }}">
                            <span>{{ $faq['question'] ?? '' }}</span>
                            <svg class="loyalty-bottom__faq-icon" width="15" height="20" viewBox="0 0 15 20"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path class="loyalty-bottom__faq-icon-h" d="M2 10H13" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" />
                                <path class="loyalty-bottom__faq-icon-v" d="M7.5 4V16" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                        <div class="loyalty-bottom__faq-answer-wrap" x-show="open === {{ $i }}" x-collapse
                            {{ $i !== 0 ? 'x-cloak' : '' }}>
                            <p class="loyalty-bottom__faq-answer">{{ $faq['answer'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
