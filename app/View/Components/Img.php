<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Img extends Component
{
    /**
     * Supported image formats in priority order (best compression first).
     */
    protected const FORMATS = ['avif', 'webp', 'png', 'jpg', 'jpeg', 'gif', 'svg'];

    /**
     * Resolved image source path (relative to public/) with the best available format.
     */
    public readonly string $src;

    /**
     * Whether a valid image file was found on disk.
     */
    public readonly bool $found;

    /**
     * Whether the resolved image is an SVG file.
     */
    public readonly bool $isSvg;

    /**
     * Inline SVG content (only populated when isSvg is true).
     */
    public readonly string $svgContent;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public readonly string $path,
        public readonly ?string $alt = null,
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly bool $lazy = true,
        public readonly ?string $placeholder = null,
    ) {
        $resolved = $this->resolveSrc($path);

        $this->src = $resolved;
        $this->found = $resolved !== '';
        $this->isSvg = $this->found && str_ends_with(strtolower($resolved), '.svg');
        $this->svgContent = $this->isSvg ? $this->readSvg($resolved) : '';
    }

    /**
     * Resolve the best available image format for the given path.
     *
     * Strips any existing extension from the path, then checks each
     * supported format in priority order using file_exists on the public disk.
     */
    protected function resolveSrc(string $path): string
    {
        $basePath = $this->stripExtension($path);

        foreach (self::FORMATS as $format) {
            $candidate = "{$basePath}.{$format}";
            $diskPath = public_path($candidate);

            if (file_exists($diskPath)) {
                return $candidate;
            }
        }

        return '';
    }

    /**
     * Remove the file extension from a path if present.
     */
    protected function stripExtension(string $path): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if ($extension && in_array(strtolower($extension), self::FORMATS, true)) {
            return Str::beforeLast($path, '.'.$extension);
        }

        return $path;
    }

    /**
     * Read and sanitize an SVG file for inline embedding.
     */
    protected function readSvg(string $path): string
    {
        $content = file_get_contents(public_path($path));

        if ($content === false) {
            return '';
        }

        // Strip XML declaration
        $content = preg_replace('/<\?xml[^?]*\?>\s*/', '', $content);

        return trim($content);
    }

    /**
     * Get the View / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.img');
    }
}
