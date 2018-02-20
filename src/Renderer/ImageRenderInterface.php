<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Renderer;

/**
 * @package WebPify\Renderer
 */
interface ImageRenderInterface
{

    /**
     * @param string $img
     * @param int    $attachment_id
     * @param string $size
     *
     * @return string
     */
    public function render(string $img, int $attachment_id, string $size): string;
}
