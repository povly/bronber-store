@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
@endpush

<section class="pb-contacts">
    <div class="container">
        <div class="pb-contacts__info">
            @if(!empty($block['address']))
                <p class="pb-contacts__address">{{ $block['address'] }}</p>
            @endif
            @if(!empty($block['phone']))
                <a class="pb-contacts__phone" href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $block['phone']) }}">{{ $block['phone'] }}</a>
            @endif
            @if(!empty($block['email']))
                <a class="pb-contacts__email" href="mailto:{{ $block['email'] }}">{{ $block['email'] }}</a>
            @endif
        </div>
        @if(!empty($block['map_url']))
            <div class="pb-contacts__map">
                <iframe src="{{ $block['map_url'] }}" width="100%" height="100%" frameborder="0" loading="lazy"></iframe>
            </div>
        @endif
    </div>
</section>
