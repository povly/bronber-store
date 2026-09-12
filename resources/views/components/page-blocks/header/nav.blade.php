@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
@endpush

@if(!empty($block['links']))
    <nav class="pb-nav">
        <ul class="pb-nav__list">
            @foreach($block['links'] as $link)
                @if(!empty($link['label']))
                    <li class="pb-nav__item">
                        <a class="pb-nav__link" href="{{ $link['url'] ?? '#' }}">{{ $link['label'] }}</a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>
@endif
