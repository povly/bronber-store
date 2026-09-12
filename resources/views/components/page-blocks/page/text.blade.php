@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
@endpush

<div class="pb-text">
    <div class="container">
        @if(!empty($block['body']))
            {!! app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render((string) $block['body']) !!}
        @endif
    </div>
</div>
