@php
    $caption = (string) ($data['caption'] ?? '');
    $images = [];

    if (isset($data['files']) && is_array($data['files'])) {
        foreach ($data['files'] as $file) {
            if (is_array($file) && isset($file['url'])) {
                $images[] = ['url' => $file['url'], 'path' => $file['path'] ?? null];
            }
        }
    }

    if ($images === [] && isset($data['file']['url'])) {
        $images[] = ['url' => $data['file']['url'], 'path' => $data['file']['path'] ?? null];
    }

    // Scope contract: the converted-images output (<x-img>, lazy loading,
    // avif/webp priority) applies ONLY to the about page — everywhere else
    // this block renders the stock package markup (raw <img> gallery).
    // The about page is identified by the page shared from
    // PageController::renderPage(); admin previews and legacy contexts
    // have no shared page and fall back to the stock branch.
    $isAbout = (view()->shared('currentPage')?->slug ?? null) === 'about';

    if ($isAbout) {
        // MediaManagerPicker stores disk-relative paths: normalize to the
        // public /storage/ prefix so <x-img> resolves the file and picks
        // the best converted format (same convention as delivery icons).
        // Absolute/URL paths pass through.
        $images = array_map(static function (array $image): array {
            $path = $image['path'];

            if ($path !== null && ! str_starts_with((string) $path, '/') && ! str_starts_with((string) $path, 'http')) {
                $image['path'] = '/storage/'.ltrim((string) $path, '/');
            }

            return $image;
        }, $images);
    }
@endphp
@if ($images !== [])
    @if ($isAbout)
        <figure class="about__image-wrap img--full">
            @foreach ($images as $image)
                @if (! empty($image['path']))
                    <x-img path="{{ $image['path'] }}" :alt="$caption" class="about__image" />
                @else
                    <img src="{{ $image['url'] }}" alt="{{ $caption }}" class="about__image">
                @endif
            @endforeach
            @if ($caption !== '')
                <figcaption class="image-caption">{{ $caption }}</figcaption>
            @endif
        </figure>
    @else
        <figure class="image @if (count($images) > 1) mm-editorjs-gallery @endif">
            @foreach ($images as $image)
                <img src="{{ $image['url'] }}" alt="{{ $caption }}">
            @endforeach
            @if ($caption !== '')
                <figcaption class="image-caption">{{ $caption }}</figcaption>
            @endif
        </figure>
    @endif
@endif
