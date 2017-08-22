<?php declare( strict_types=1 );

namespace WebPify\Renderer;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @package WebPify\Renderer
 */
final class Provider implements ServiceProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ ImageRenderer::class ] = function (): ImageRenderer {

			return new ImageRenderer();
		};

	}


}
