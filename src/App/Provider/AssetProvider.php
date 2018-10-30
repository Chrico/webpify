<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\App\Provider;

use WebPify\App\BootableProvider;
use WebPify\Assets\LazyLoadScript;
use WebPify\Assets\LazyLoadScriptData;
use WebPify\WebPify;

/**
 * Class AssetProvider
 *
 * @package WebPify\Assets
 */
final class AssetProvider implements BootableProvider
{

    public function register(WebPify $plugin)
    {
        $plugin->set(
            LazyLoadScriptData::class,
            function (): LazyLoadScriptData {
                return new LazyLoadScriptData();
            }
        );

        $plugin->set(
            LazyLoadScript::class,
            function (WebPify $plugin): LazyLoadScript {
                return new LazyLoadScript(
                    $plugin->get('config.plugin_file'),
                    $plugin->get(LazyLoadScriptData::class)
                );
            }
        );
    }

    public function boot(WebPify $plugin)
    {
        if (is_admin()) {
            return;
        }

        add_filter(
            'wp_enqueue_scripts',
            [$plugin->get(LazyLoadScript::class), 'enqueue']
        );

        add_filter(
            'script_loader_tag',
            [$plugin->get(LazyLoadScript::class), 'printInline'],
            10,
            2
        );
    }
}
