<?php declare( strict_types=1 );

namespace WebPify\Assets;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WebPify\Core\BootableProviderInterface;

final class Provider implements ServiceProviderInterface, BootableProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ Script::class ] = function ( Container $plugin ): Script {

			return new Script( $plugin[ 'config.plugin_file' ] );
		};

	}

	public function boot( Container $plugin ) {

		add_filter( 'wp_enqueue_scripts', [ $plugin[ Script::class ], 'enqueue' ] );

		add_filter(
			'script_loader_tag',
			function ( $tag, $handle ) {

				if ( $handle === Script::HANDLE ) {

					$src = str_replace(
						home_url( '/wp-content' ),
						WP_CONTENT_DIR,
						wp_scripts()->registered[ $handle ]->src
					);

					$tag = sprintf(
						'<script async>%s</script>',
						file_get_contents( $src )
					);
				}

				return $tag;
			},
			10,
			2
		);

	}
}
