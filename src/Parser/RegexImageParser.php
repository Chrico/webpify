<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Parser;

use WebPify\Renderer\ImageRenderer;
use WebPify\Renderer\ImageRenderInterface;

/**
 * @package WebPify\Parser
 */
final class RegexImageParser implements ParserInterface
{

    /**
     * RegExes to parse the <img>-tag and collect required values.
     *
     * @var array
     */
    private $regex_search = [
        'id'   => '/wp-image-([0-9]+)/i',
        'size' => '/size-([A-Za-z-_0-9]+)/i',
    ];

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
                $img        = $match[ 0 ];
                $attributes = $this->attributes($img);

                return $this->renderer->render($img, (int)$attributes[ 'id' ], $attributes[ 'size' ]);
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
        $data = array_map(
            function (string $regex) use ($html): string {
                // phpcs:disable WordPress.Arrays.CommaAfterArrayItem.NoComma)
                if (preg_match($regex, $html, $value)) {
                    return $value[ 1 ];
                }

                return '';
            },
            $this->regex_search
        );

        return $data;
    }
}
