<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Renderer;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @package WebPify\Renderer
 */
final class Provider implements ServiceProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ ImageRenderer::class ] = function ( Container $plugin ): ImageRenderer {

			return new ImageRenderer( $plugin[ 'config.placeholder_src' ] );
		};

	}

}
