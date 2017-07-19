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

		if ( is_admin() ) {
			return;
		}

		$filters = [
			'post_thumbnail_html',
			'the_content'
		];

		foreach ( $filters as $filter ) {
			add_filter( $filter, [ $plugin[ RegexImageParser::class ], 'parse' ], PHP_INT_MAX );
		}

	}
}
