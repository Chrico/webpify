<?php declare( strict_types=1 );

namespace WebPify\Parser;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WebPify\Core\BootableProviderInterface;
use WebPify\Renderer\ImageRenderer;

final class Provider implements ServiceProviderInterface, BootableProviderInterface {

	public function register( Container $plugin ) {

		$plugin[ RegexImageParser::class ] = function ( Container $plugin ): RegexImageParser {

			return new RegexImageParser( $plugin[ ImageRenderer::class ] );
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

		add_filter(
			'wp_get_attachment_image_attributes',
			// we're adding the attachment->ID to the image attributes to parse them lateron.
			function ( $attr, \WP_Post $attachment ): array {

				$attr[ 'class' ] .= ' wp-image-' . $attachment->ID;

				return $attr;
			},
			10,
			2
		);
	}
}
