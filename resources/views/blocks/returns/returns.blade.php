@push('block-styles')
    @vite(['resources/css/blocks/returns/style.css'])
@endpush

<section class="returns">
    <div class="container">
        <div class="returns__block">
            <h1 class="section__title">{{ __('returns.title') }}</h1>

            <p>{!! nl2br(e(__('returns.body_1'))) !!}</p>
            <p>{!! nl2br(e(__('returns.body_2'))) !!}</p>

            <h2>{!! nl2br(e(__('returns.subtitle_non_returnable'))) !!}</h2>

            <p>{!! nl2br(e(__('returns.body_3'))) !!}</p>

            <h2 class="returns__subtitle">{{ __('returns.subtitle_defective') }}</h2>

            <p>{!! nl2br(e(__('returns.body_4'))) !!}</p>
        </div>
    </div>
</section>
