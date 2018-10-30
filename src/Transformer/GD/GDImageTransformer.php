<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Transformer\GD;

use WebPify\Transformer\ImageTransformerInterface;
use WebPify\WebPify;

/**
 * @package WebPify\Transformer\GD
 */
final class GDImageTransformer implements ImageTransformerInterface
{

    private $imageFunctions = [
        'jpg' => 'imagecreatefromjpeg',
        'jpeg' => 'imagecreatefromjpeg',
        'png' => 'imagecreatefrompng',
    ];

    public function create(string $sourceFile, string $destFile): bool
    {
        $errorContext = [
            'dest_file' => $destFile,
            'source_file' => $sourceFile,
            'image_functions' => $this->imageFunctions,
        ];

        $ext = pathinfo($sourceFile, PATHINFO_EXTENSION);
        if (! isset($this->imageFunctions[$ext])) {
            do_action(
                WebPify::ACTION_ERROR,
                sprintf('Could not find "%s" in available extension.', $ext),
                $errorContext
            );

            return false;
        }

        $func = $this->imageFunctions[$ext];
        // adding the selected function to error context for debugging.
        $errorContext['func'] = $func;
        if (! function_exists($func)) {
            do_action(
                WebPify::ACTION_ERROR,
                sprintf('The extension "%s" is not available.', $ext),
                $errorContext
            );

            return false;
        }

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        $resource = @$func($sourceFile);
        if (! $resource) {
            do_action(
                WebPify::ACTION_ERROR,
                sprintf('Creating resource failed.', $ext),
                $errorContext
            );

            return false;
        }

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        $success = @imagewebp($resource, $destFile);
        imagedestroy($resource);

        if (! $success) {
            do_action(
                WebPify::ACTION_ERROR,
                'Image creation failed.',
                $errorContext
            );

            return false;
        }

        return true;
    }

    public function isActivated(): bool
    {
        return extension_loaded('gd') && function_exists('imagewebp');
    }
}
