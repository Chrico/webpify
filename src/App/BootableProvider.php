<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\App;

use WebPify\WebPify;

/**
 * Interface BootableProvider
 *
 * @package WebPify\App
 */
interface BootableProvider extends Provider
{

    /**
     * @param WebPify $plugin
     */
    public function boot(WebPify $plugin);
}
