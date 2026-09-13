@push('block-styles')
    @once
        @vite(['resources/css/blocks/faq/style.css'])
    @endonce
@endpush

@php
    // Prototype markup of blocks/faq/faq.blade.php, data now comes from
    // the flexible-layouts block. Alpine state is inline (no dependency
    // on the prototype's js/blocks/faq/index.js): the first item is open,
    // toggling the open item closes it — same as the prototype's faq().
    $title = $block['title'] ?? 'Часто задаваемые вопросы';
    $items = $block['items'] ?? [];
@endphp

<section class="faq" x-data="{ open: 0 }">
    <div class="container">
        <x-breadcrumbs class="faq__breadcrumbs" :items="[
            ['label' => 'Главная', 'url' => route('home')],
            ['label' => 'FAQ'],
        ]" />

        <h1 class="faq__title">{{ $title }}</h1>

        <div class="faq__list">
            @foreach ($items as $i => $item)
                <div class="faq__item" :class="{ 'is-open': open === {{ $i }} }">
                    <div class="faq__separator"></div>
                    <button type="button" class="faq__question" @click="open = open === {{ $i }} ? null : {{ $i }}">
                        <span>{{ $item['question'] ?? '' }}</span>
                        <svg class="faq__icon" width="18" height="24" viewBox="0 0 18 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path class="faq__icon-h" d="M2 12H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path class="faq__icon-v" d="M9 5V19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="faq__answer-wrap" x-show="open === {{ $i }}" x-collapse>
                        <p class="faq__answer">{!! nl2br(e($item['answer'] ?? '')) !!}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
