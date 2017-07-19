<?php declare( strict_types=1 );

namespace WebPify\Transformer;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class Provider implements ServiceProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ NativeExtensionTransformer::class ] = function (): NativeExtensionTransformer {

			return new NativeExtensionTransformer();
		};

	}

}
