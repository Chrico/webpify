<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\App\Provider;

use WebPify\App\Provider;
use WebPify\Transformer\GD\GDImageTransformer;
use WebPify\Transformer\ImageTransformerInterface;
use WebPify\Transformer\Imagick\ImagickImageTransformer;
use WebPify\Transformer\NullTransformer;
use WebPify\WebPify;

// phpcs:disable Generic.Metrics.NestingLevel.TooHigh

/**
 * Class TransformerProvider
 *
 * @package WebPify\Transformer
 */
final class TransformerProvider implements Provider
{

    public function register(WebPify $plugin)
    {
        $plugin->set(
            NullTransformer::class,
            function (): NullTransformer {
                return new NullTransformer();
            }
        );

        $plugin->set(
            ImagickImageTransformer::class,
            function (): ImagickImageTransformer {
                return new ImagickImageTransformer();
            }
        );

        $plugin->set(
            GDImageTransformer::class,
            function (): GDImageTransformer {
                return new GDImageTransformer();
            }
        );

        $plugin->set(
            ImageTransformerInterface::class,
            function (WebPify $plugin): ImageTransformerInterface {
                $classes = [GDImageTransformer::class, ImagickImageTransformer::class];
                foreach ($classes as $class) {
                    if ($plugin->get($class)->isActivated()) {
                        return $plugin->get($class);
                    }
                }

                return $plugin->get(NullTransformer::class);
            }
        );
    }
}
