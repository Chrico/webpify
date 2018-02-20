<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Assets;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WebPify\Core\BootableProviderInterface;

/**
 * @package WebPify\Assets
 */
final class Provider implements ServiceProviderInterface, BootableProviderInterface
{

    public function register(Container $plugin)
    {

        $plugin->offsetSet(
            LazyLoadScriptData::class,
            function (): LazyLoadScriptData {

                return new LazyLoadScriptData();
            }
        );

        $plugin->offsetSet(
            LazyLoadScript::class,
            function (Container $plugin): LazyLoadScript {

                return new LazyLoadScript(
                    $plugin[ 'config.plugin_file' ],
                    $plugin[ LazyLoadScriptData::class ]
                );
            }
        );
    }

    public function boot(Container $plugin)
    {

        if (is_admin()) {
            return;
        }

        add_filter(
            'wp_enqueue_scripts',
            [$plugin[ LazyLoadScript::class ], 'enqueue']
        );

        add_filter(
            'script_loader_tag',
            [$plugin[ LazyLoadScript::class ], 'print_inline'],
            10,
            2
        );
    }
}
