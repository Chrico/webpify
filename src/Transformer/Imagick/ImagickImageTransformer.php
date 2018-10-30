<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Transformer\Imagick;

use WebPify\Transformer\ImageTransformerInterface;
use WebPify\WebPify;

/**
 * @package WebPify\Transformer\Imagick
 *
 * @link    http://www.imagemagick.org/script/webp.php
 * @link    https://stackoverflow.com/questions/37711492/imagemagick-specific-webp-calls-in-php
 */
final class ImagickImageTransformer implements ImageTransformerInterface
{

    public function create(string $sourceFile, string $destFile): bool
    {
        try {
            $errorContext = [
                'source_file' => $sourceFile,
                'dest_file' => $destFile,
            ];

            if (! file_exists($sourceFile)) {
                do_action(
                    WebPify::ACTION_ERROR,
                    'Source file does not exist.',
                    $errorContext
                );

                return false;
            }

            $ext = strtolower(pathinfo($sourceFile, PATHINFO_EXTENSION));
            $allowedExtensions = [
                'jpg',
                'jpeg',
                'png',
            ];
            if (! in_array($ext, $allowedExtensions, true)) {
                do_action(
                    WebPify::ACTION_ERROR,
                    sprintf('The extension "%s" is not supported', $ext),
                    $errorContext
                );

                return false;
            }

            $imagick = new \Imagick($sourceFile);
            $imagick->setImageFormat('WEBP');
            $imagick->setOption('webp:method', '6');
            $imagick->setOption('webp:low-memory', 'true');

            if ($ext === 'png') {
                $imagick->setOption('webp:lossless', 'true');
            }

            return $imagick->writeImage($destFile);
        } catch (\Throwable $error) {
            $errorContext['exception'] = $error;
            if (isset($imagick)) {
                $errorContext['imagick'] = $imagick;
            }

            do_action(
                WebPify::ACTION_ERROR,
                $error->getMessage(),
                $errorContext
            );

            return false;
        }
    }

    public function isActivated(): bool
    {
        return extension_loaded('imagick') && class_exists(\Imagick::class);
    }
}
