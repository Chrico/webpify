<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Parser;

use WebPify\Renderer\ImageRenderer;
use WebPify\Renderer\ImageRenderInterface;

/**
 * @package WebPify\Parser
 */
final class ImageParser implements ParserInterface
{

    const SRC_REGEX = '/src="([^"]*)"/i';
    const ID_REGEX = '/wp-image-([0-9]+)/i';

    /**
     * @var ImageRenderInterface
     */
    private $renderer;

    /**
     * RegexImageParser constructor.
     *
     * @param ImageRenderInterface $renderer
     */
    public function __construct(ImageRenderInterface $renderer = null)
    {
        $this->renderer = $renderer ?? new ImageRenderer();
    }

    public function parse(string $content): string
    {
        // Don't lazyload for feeds or previews.
        if (is_feed() || is_preview()) {
            return $content;
        }

        // Don't lazy-load if the content has already been run through previously
        // TODO: this check is might be a little bit...inflexible, since data-src can be used everywhere.
        if (false !== strpos($content, 'data-src')) {
            return $content;
        }

        return preg_replace_callback(
            '/<img [^>]+>/',
            function (array $match): string {
                $img = $match[0];
                $attributes = $this->attributes($img);

                if (! isset($attributes['id'])) {
                    return $img;
                }

                return $this->renderer->render($img, (int) $attributes['id'], $attributes['size']);
            },
            $content
        );
    }

    /**
     * Returns a collection of image attributes parsed via RegEx.
     *
     * @param string $html the image tag.
     *
     * @return array
     */
    public function attributes(string $html): array
    {
        $id = 0;
        if (preg_match(self::ID_REGEX, $html, $value)) {
            $id = (int) $value[1];
        }

        $src = '';
        if (preg_match(self::SRC_REGEX, $html, $value)) {
            $src = (string) $value[1];
        }

        if ($id === 0 || $src === '') {
            return [];
        }

        $currentFile = basename($src);

        return [
            'id' => $id,
            'src' => $src,
            'file' => $currentFile,
            'size' => $this->size($currentFile, $id),
        ];
    }

    private function size(string $currentFile, $id): string
    {
        $matchedSize = 'full';

        $meta = wp_get_attachment_metadata($id);
        $sizes = $meta['sizes'];
        $sizes['full'] = [
            'file' => basename($meta['file']),
        ];

        foreach ($sizes as $size => $data) {
            if ($data['file'] === $currentFile) {
                $matchedSize = $size;
                break;
            }
        }

        return $matchedSize;
    }
}
