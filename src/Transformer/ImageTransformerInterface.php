<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Transformer;

/**
 * @package WebPify\Transformer
 */
interface ImageTransformerInterface
{

    /**
     * @param string $sourceFile the source file with full path in file-system.
     * @param string $destFile the destination file
     *
     * @return bool     TRUE if successfully created | FALSE if an error occurred.
     */
    public function create(string $sourceFile, string $destFile): bool;

    /**
     * Function to detect, if the current Transformer is available.
     *
     * @return bool
     */
    public function isActivated(): bool;
}
