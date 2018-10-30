<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Transformer;

/**
 * @package WebPify\Transformer
 */
class NullTransformer implements ImageTransformerInterface
{

    public function create(string $sourceFile, string $destFile): bool
    {
        return false;
    }

    public function isActivated(): bool
    {
        return true;
    }
}
