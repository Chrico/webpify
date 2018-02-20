<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Transformer;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WebPify\Transformer\GD\GDImageTransformer;
use WebPify\Transformer\Imagick\ImagickImageTransformer;

// phpcs:disable Generic.Metrics.NestingLevel.TooHigh

/**
 * @package WebPify\Transformer
 */
final class Provider implements ServiceProviderInterface
{

    /**
     * @param Container $plugin
     */
    public function register(Container $plugin)
    {

        $plugin->offsetSet(
            NullTransformer::class,
            function (): NullTransformer {

                return new NullTransformer();
            }
        );

        $plugin->offsetSet(
            ImagickImageTransformer::class,
            function (): ImagickImageTransformer {

                return new ImagickImageTransformer();
            }
        );

        $plugin->offsetSet(
            GDImageTransformer::class,
            function (): GDImageTransformer {

                return new GDImageTransformer();
            }
        );

        $plugin->offsetSet(
            ImageTransformerInterface::class,
            function (Container $plugin): ImageTransformerInterface {
                $classes = [GDImageTransformer::class, ImagickImageTransformer::class];
                foreach ($classes as $class) {
                    if ($plugin[ $class ]->isActivated()) {
                        return $plugin[ $class ];
                    }
                }

                return $plugin[ NullTransformer::class ];
            }
        );
    }
}
