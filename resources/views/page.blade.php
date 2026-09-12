@extends('layouts.app')

@section('content')
    @php
        // Rendered inside the view (not in the controller) on purpose: block
        // views push their CSS/JS onto the "block-styles"/"block-scripts"
        // stacks, and pushes only survive to the layout when the block views
        // render nested inside another view — a controller-side ->render()
        // flushes the stacks before the layout can @stack them.
    @endphp
    <main class="page">
        {!! resolve(\App\Support\PageBlocks\BlockRenderer::class)->render($translation->content ?? [], 'page') !!}
    </main>
@endsection
