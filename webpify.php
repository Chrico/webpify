<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-
/*
 * Plugin Name:       WebPify
 * Description:       Generating automatically WebP-images and provide them via feature detection and lazy loading.
 * Author:            ChriCo
 * Author URI:        https://www.chrico.info
 * Version:           0.2.0
 * Text Domain:       webpify
 * Domain Path:       languages
 * License:           MIT
 * Requires at least: 4.8
 */

namespace WebPify;

use WebPify\Attachment\WebPAttachment;

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

		if ( ! class_exists( WebPAttachment::class ) && file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
			require __DIR__ . '/vendor/autoload.php';
		}

		$plugin = new WebPify(
			[
				'config.plugin_file'     => __FILE__,
				'config.placeholder_src' => 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs='
			]
		);
		$plugin->register( new Assets\Provider() );
		$plugin->register( new Attachment\Provider() );
		$plugin->register( new Renderer\Provider() );
		$plugin->register( new Parser\Provider() );
		$plugin->register( new Transformer\Provider() );

		$plugin->boot();
	}
	catch ( \Throwable $throwable ) {

		do_action( 'WebPify.error', $throwable );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			throw $throwable;
		}
	}
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\\initialize' );