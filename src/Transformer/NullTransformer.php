<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Transformer;

/**
 * @package WebPify\Transformer
 */
class NullTransformer implements ImageTransformerInterface
{

    public function create(string $source_file, string $dest_file): bool
    {

        return false;
    }

    public function isActivated(): bool
    {

        return true;
    }
}
