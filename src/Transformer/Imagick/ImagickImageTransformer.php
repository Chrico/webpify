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

    public function create(string $source_file, string $dest_file): bool
    {
        try {
            $error_context = [
                'source_file' => $source_file,
                'dest_file' => $dest_file,
            ];

            if (! file_exists($source_file)) {
                do_action(
                    WebPify::ACTION_ERROR,
                    'Source file does not exist.',
                    $error_context
                );

                return false;
            }

            $ext = strtolower(pathinfo($source_file, PATHINFO_EXTENSION));
            $allowedExtensions = [
                'jpg',
                'jpeg',
                'png',
            ];
            if (! in_array($ext, $allowedExtensions, true)) {
                do_action(
                    WebPify::ACTION_ERROR,
                    sprintf('The extension "%s" is not supported', $ext),
                    $error_context
                );

                return false;
            }

            $imagick = new \Imagick($source_file);
            $imagick->setImageFormat('WEBP');
            $imagick->setOption('webp:method', '6');
            $imagick->setOption('webp:low-memory', 'true');

            if ($ext === 'png') {
                $imagick->setOption('webp:lossless', 'true');
            }

            return $imagick->writeImage($dest_file);
        } catch (\Throwable $error) {
            $error_context['exception'] = $error;
            if (isset($imagick)) {
                $error_context['imagick'] = $imagick;
            }

            do_action(
                WebPify::ACTION_ERROR,
                $error->getMessage(),
                $error_context
            );

            return false;
        }
    }

    public function isActivated(): bool
    {
        return extension_loaded('imagick') && class_exists(\Imagick::class);
    }
}
