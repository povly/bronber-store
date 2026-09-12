@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
@endpush

@if(!empty($block['text']))
    <div class="pb-copyright">{{ $block['text'] }}</div>
@endif
