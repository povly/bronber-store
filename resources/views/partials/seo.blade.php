@php
    /**
     * Full SEO tag set, driven by the optional $seo array (shared by PageController).
     * Fallback chains: og:title ← meta_title ← title; og:description ← meta_description;
     * canonical ← current URL; og:url ← canonical. Prototype pages without $seo
     * render the default title only.
     *
     * @var array{title?: string, meta_title?: ?string, meta_description?: ?string,
     *     meta_keywords?: ?string, meta_robots?: ?string, canonical_url?: ?string,
     *     og_image?: ?string, alternates?: array<string, string>} $seo
     */
    $seo = $seo ?? [];

    $seoTitle = trim((string) ($seo['meta_title'] ?? '')) !== ''
        ? (string) $seo['meta_title']
        : (string) ($seo['title'] ?? 'Bronber Store');

    $seoDescription = (string) ($seo['meta_description'] ?? '');
    $seoKeywords = (string) ($seo['meta_keywords'] ?? '');
    $seoRobots = (string) ($seo['meta_robots'] ?? 'index, follow');
    $seoCanonical = trim((string) ($seo['canonical_url'] ?? '')) !== ''
        ? (string) $seo['canonical_url']
        : url()->current();

    $seoOgTitle = $seoTitle;
    $seoOgDescription = $seoDescription;

    $seoOgImage = null;
    if (!empty($seo['og_image'])) {
        $image = (string) $seo['og_image'];
        $seoOgImage = str_starts_with($image, '/') || str_starts_with($image, 'http')
            ? $image
            : '/storage/'.ltrim($image, '/');
        if (!str_starts_with($seoOgImage, 'http')) {
            $seoOgImage = url($seoOgImage);
        }
    }

    $seoAlternates = $seo['alternates'] ?? [];
@endphp

<title>{{ $seoTitle }}</title>

@if($seoDescription !== '')
    <meta name="description" content="{{ $seoDescription }}">
@endif
@if($seoKeywords !== '')
    <meta name="keywords" content="{{ $seoKeywords }}">
@endif
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoCanonical }}">

@foreach($seoAlternates as $hreflang => $href)
    <link rel="alternate" hreflang="{{ $hreflang }}" href="{{ $href }}">
@endforeach

<meta property="og:type" content="website">
<meta property="og:site_name" content="Bronber Store">
<meta property="og:title" content="{{ $seoOgTitle }}">
@if($seoOgDescription !== '')
    <meta property="og:description" content="{{ $seoOgDescription }}">
@endif
@if($seoOgImage !== null)
    <meta property="og:image" content="{{ $seoOgImage }}">
@endif
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">

<meta name="twitter:card" content="{{ $seoOgImage !== null ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $seoOgTitle }}">
@if($seoOgDescription !== '')
    <meta name="twitter:description" content="{{ $seoOgDescription }}">
@endif
@if($seoOgImage !== null)
    <meta name="twitter:image" content="{{ $seoOgImage }}">
@endif
