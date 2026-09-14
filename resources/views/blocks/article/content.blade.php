<div class="article-page-block__content">
    @if (! empty($block['text']))
        {!! app(\Sckatik\MoonshineEditorJs\RenderEditorJs::class)->render((string) $block['text']) !!}
    @endif
</div>
