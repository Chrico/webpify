<?php declare( strict_types=1 );

namespace WebPify\Parser;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WebPify\Core\BootableProviderInterface;

final class Provider implements ServiceProviderInterface, BootableProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ RegexImageParser::class ] = function (): RegexImageParser {

			return new RegexImageParser();
		};

	}

	public function boot( Container $plugin ) {

		add_filter( 'the_content', [ $plugin[ RegexImageParser::class ], 'parse' ], PHP_INT_MAX );

	}
}
