@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
@endpush

@if(!empty($block['items']))
    <section class="pb-faq" x-data="{ open: -1 }">
        <div class="container">
            <div class="pb-faq__list">
                @foreach($block['items'] as $i => $item)
                    <div class="pb-faq__item" :class="{ 'is-open': open === {{ $i }} }">
                        <button type="button" class="pb-faq__question" @click="open = open === {{ $i }} ? -1 : {{ $i }}">
                            <span>{{ $item['question'] ?? '' }}</span>
                            <svg class="pb-faq__chevron" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="pb-faq__answer" x-show="open === {{ $i }}" x-collapse>
                            {!! nl2br(e($item['answer'] ?? '')) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
