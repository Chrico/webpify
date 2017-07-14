<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-
/*
 * Plugin Name:       WebPify
 * Description:       Generating automatically WebP-images and provide them via feature detection and lazy loading.
 * Author:            ChriCo
 * Author URI:        https://www.chrico.info
 * Version:           0.0.1
 * Text Domain:       webpify
 * Domain Path:       languages
 * License:           MIT
 * Requires at least: 4.8
 */

namespace WebPify;

use WebPify\Builder\WebPImageBuilder;
use WebPify\Model\WebPImage;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Initialize all the plugin things.
 *
 * @wp-hook plugins_loaded
 *
 * @throws \Throwable
 */
function initialize() {

	try {

		if ( ! class_exists( WebPImage::class ) && file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			require __DIR__ . '/vendor/autoload.php';
		}

		add_filter(
			'wp_generate_attachment_metadata',
			function ( array $metadata, $attachment_id ): array {

				$builder = new WebPImageBuilder(
					new Transformer\NativeExtensionTransformer(),
					wp_get_upload_dir()
				);

				update_post_meta(
					$attachment_id,
					WebPImage::ID,
					$builder->build( $metadata )
				);

				return $metadata;
			},
			10,
			2
		);

		add_filter(
			'the_content',
			function ( $content ): string {

				return ( new Parser\RegexImageParser() )->parse( $content );
			},
			PHP_INT_MAX
		);

		add_filter(
			'wp_enqueue_scripts',
			[ new Assets\Script( __FILE__ ), 'enqueue' ]
		);

	}
	catch ( \Throwable $throwable ) {

		do_action( 'webpify.error', $throwable );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			throw $throwable;
		}
	}
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\initialize' );