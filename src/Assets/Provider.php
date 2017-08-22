<?php declare( strict_types=1 );

namespace WebPify\Assets;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WebPify\Core\BootableProviderInterface;

/**
 * @package WebPify\Assets
 */
final class Provider implements ServiceProviderInterface, BootableProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ Script::class ] = function ( Container $plugin ): Script {

			return new Script( $plugin[ 'config.plugin_file' ] );
		};

	}

	public function boot( Container $plugin ) {

		if ( is_admin() ) {
			return;
		}

		add_filter(
			'wp_enqueue_scripts',
			[ $plugin[ Script::class ], 'enqueue' ]
		);

		add_filter(
			'script_loader_tag',
			[ $plugin[ Script::class ], 'print_inline' ],
			10,
			2
		);

	}
}
