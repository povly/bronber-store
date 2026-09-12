@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
@endpush

@if(!empty($block['links']))
    <div class="pb-footer-col">
        @if(!empty($block['title']))
            <h3 class="pb-footer-col__title">{{ $block['title'] }}</h3>
        @endif
        <ul class="pb-footer-col__list">
            @foreach($block['links'] as $link)
                @if(!empty($link['label']))
                    <li class="pb-footer-col__item">
                        <a class="pb-footer-col__link" href="{{ $link['url'] ?? '#' }}">{{ $link['label'] }}</a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endif
