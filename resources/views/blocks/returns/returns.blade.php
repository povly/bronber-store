@push('block-styles')
    @vite(['resources/css/blocks/returns/style.css'])
@endpush

<section class="returns">
    <div class="container">
        <h1 class="returns__title">{{ __('returns.title') }}</h1>

        <p class="returns__text">{!! nl2br(e(__('returns.body_1'))) !!}</p>
        <p class="returns__text">{!! nl2br(e(__('returns.body_2'))) !!}</p>

        <h2 class="returns__subtitle">{!! nl2br(e(__('returns.subtitle_non_returnable'))) !!}</h2>

        <p class="returns__text">{!! nl2br(e(__('returns.body_3'))) !!}</p>

        <h2 class="returns__subtitle">{{ __('returns.subtitle_defective') }}</h2>

        <p class="returns__text">{!! nl2br(e(__('returns.body_4'))) !!}</p>
    </div>
</section>
