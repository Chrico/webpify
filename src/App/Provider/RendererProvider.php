<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\App\Provider;

use WebPify\App\Provider;
use WebPify\Renderer\ImageRenderer;
use WebPify\Renderer\ImageRenderInterface;
use WebPify\WebPify;

/**
 * Class RendererProvider
 *
 * @package WebPify\Renderer
 */
final class RendererProvider implements Provider
{

    public function register(WebPify $plugin)
    {
        $plugin->set(
            ImageRenderInterface::class,
            function (WebPify $plugin): ImageRenderInterface {
                return new ImageRenderer($plugin->get('config.placeholder_src'));
            }
        );
    }
}
