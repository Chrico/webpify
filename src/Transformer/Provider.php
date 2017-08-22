<?php declare( strict_types=1 );

namespace WebPify\Transformer;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WebPify\Transformer\GD\GDImageTransformer;
use WebPify\Transformer\Imagick\ImagickImageTransformer;

/**
 * @package WebPify\Transformer
 */
final class Provider implements ServiceProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ NullTransformer::class ] = function (): NullTransformer {

			return new NullTransformer();
		};

		$plugin[ ImagickImageTransformer::class ] = function (): ImagickImageTransformer {

			return new ImagickImageTransformer();
		};

		$plugin[ GDImageTransformer::class ] = function (): GDImageTransformer {

			return new GDImageTransformer();
		};

		$plugin[ ImageTransformerInterface::class ] = function ( Container $plugin ): ImageTransformerInterface {

			$classes = [ ImagickImageTransformer::class, GDImageTransformer::class ];
			foreach ( $classes as $class ) {
				if ( $plugin[ $class ]->is_activated() ) {
					return $plugin[ $class ];
				}
			}

			return $plugin[ NullTransformer::class ];
		};

	}

}
