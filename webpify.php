<?php declare(strict_types=1); # -*- coding: utf-8 -*-
/*
 * Plugin Name:       WebPify
 * Description:       Generating automatically WebP-images and provide them via feature detection and lazy loading.
 * Author:            ChriCo
 * Author URI:        https://www.chrico.info
 * Version:           1.1.1
 * Text Domain:       webpify
 * Domain Path:       languages
 * License:           MIT
 * Requires at least: 4.8
 */

namespace WebPify;

use WebPify\App\Provider;

if (! defined('ABSPATH')) {
    return;
}

/**
 * Display an error message in the WP admin
 *
 * @param string $message The message content
 *
 * @return void
 */
function errorNotice(string $message)
{
    foreach (['admin_notices', 'network_admin_notices'] as $hook) {
        add_action(
            $hook,
            function () use ($message) {
                $class = 'notice notice-error';
                printf(
                    '<div class="%1$s"><p>%2$s</p></div>',
                    esc_attr($class),
                    wp_kses_post($message)
                );
            }
        );
    }
}

/**
 * Handle any exception that might occur during plugin setup
 *
 * @param \Throwable $throwable The Exception
 *
 * @return void
 */
function handleException(\Throwable $throwable)
{
    do_action('WebPify.critical', $throwable);

    errorNotice(
        sprintf(
            '<strong>Error:</strong> %s <br><pre>%s</pre>',
            $throwable->getMessage(),
            $throwable->getTraceAsString()
        )
    );
}

/**
 * Initialize all the plugin things.
 *
 * @wp-hook plugins_loaded
 */
function initialize()
{
    try {
        if (file_exists(__DIR__.'/vendor/autoload.php')) {
            require __DIR__.'/vendor/autoload.php';
        }

        $plugin = new WebPify();
        $plugin
            ->set('config.plugin_file', __FILE__)
            ->set('config.placeholder_src', 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=')
            ->register(new Provider\AssetProvider())
            ->register(new Provider\AttachmentProvider())
            ->register(new Provider\RendererProvider())
            ->register(new Provider\ParserProvider())
            ->register(new Provider\TransformerProvider());

        $plugin->boot();
    } catch (\Throwable $throwable) {
        handleException($throwable);
    }
}

add_action('plugins_loaded', __NAMESPACE__.'\\initialize');
