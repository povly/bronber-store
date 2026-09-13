@push('block-styles')
    @vite(['resources/css/blocks/returns/style.css'])
@endpush

<section class="returns section">
    <div class="container">
        <div class="returns__block">
            <h1 class="section__title">{{ $block['title'] }}</h1>

            @if (! empty($block['body']))
                {!! app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render((string) $block['body']) !!}
            @endif
        </div>
    </div>
</section>
