@push('block-styles')
    @vite(['resources/css/blocks/page-blocks/style.css'])
@endpush

@if(!empty($block['links']))
    <div class="pb-socials">
        <ul class="pb-socials__list">
            @foreach($block['links'] as $link)
                @if(!empty($link['url']))
                    <li class="pb-socials__item">
                        <a class="pb-socials__link" href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer">
                            {{ $link['platform'] ?? '' }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endif
