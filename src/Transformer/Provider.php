<?php declare( strict_types=1 );

namespace WebPify\Transformer;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class Provider implements ServiceProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ ImageTransformerInterface::class ] = function (): ImageTransformerInterface {

			return function_exists( 'imagewebp' )
				? new NativeExtensionTransformer()
				: new NullTransformer();
		};

	}

}
