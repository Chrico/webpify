<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\App\Provider;

use WebPify\App\BootableProvider;
use WebPify\Parser\ImageParser;
use WebPify\Parser\ParserInterface;
use WebPify\Renderer\ImageRenderInterface;
use WebPify\WebPify;

/**
 * Class ParserProvider
 *
 * @package WebPify\Parser
 */
final class ParserProvider implements BootableProvider
{

    public function register(WebPify $plugin)
    {
        $plugin->set(
            ParserInterface::class,
            function (WebPify $plugin): ParserInterface {
                return new ImageParser($plugin->get(ImageRenderInterface::class));
            }
        );
    }

    public function boot(WebPify $plugin)
    {
        if (is_admin()) {
            return;
        }

        $filters = [
            'post_thumbnail_html',
            'the_content',
        ];

        foreach ($filters as $filter) {
            add_filter(
                $filter,
                [$plugin->get(ParserInterface::class), 'parse'],
                PHP_INT_MAX
            );
        }

        add_filter(
            'wp_get_attachment_image_attributes',
            // we're adding the attachment->ID to the image attributes to parse them lateron.
            function (array $attr, \WP_Post $attachment): array {
                $attr['class'] .= ' wp-image-'.$attachment->ID;

                return $attr;
            },
            10,
            2
        );
    }
}
