@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
@endpush

<div class="pb-header-contacts">
    @if(!empty($block['phone']))
        <a class="pb-header-contacts__phone" href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $block['phone']) }}">{{ $block['phone'] }}</a>
    @endif
    @if(!empty($block['email']))
        <a class="pb-header-contacts__email" href="mailto:{{ $block['email'] }}">{{ $block['email'] }}</a>
    @endif
</div>
