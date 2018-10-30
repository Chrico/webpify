<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Renderer;

/**
 * @package WebPify\Renderer
 */
interface ImageRenderInterface
{

    /**
     * @param string $img
     * @param int $attachmentId
     * @param string $size
     *
     * @return string
     */
    public function render(string $img, int $attachmentId, string $size): string;
}
