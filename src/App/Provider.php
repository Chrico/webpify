<?php # -*- coding: utf-8 -*-

namespace WebPify\App;

use WebPify\WebPify;

/**
 * Interface Provider
 *
 * @package WebPify\App
 */
interface Provider
{

    /**
     * @param WebPify $plugin
     */
    public function register(WebPify $plugin);
}
