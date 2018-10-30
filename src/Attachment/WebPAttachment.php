<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Attachment;

/**
 * @package WebPify\Attachment
 */
class WebPAttachment
{

    const ID = '_WebPify_attachment_meta';
    const DATA_SRC = 'data-webp-src';
    const DATA_SRCSET = 'data-webp-srcset';

    /**
     * Default meta array which holds the same image formats as "_wp_attachment_metadata".
     *
     * @var array
     */
    private $meta = [
        'file' => '',
        'width' => 0,
        'height' => 0,
        'sizes' => [],
    ];

    /**
     * @var int
     */
    private $id;

    /**
     * @var AttachmentPathResolver
     */
    private $attachmentPathResolver;

    /**
     * WebPImage constructor.
     *
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = (int) $id;

        $meta = get_post_meta($this->id, self::ID, true);
        if ($meta === '') {
            $meta = [];
        }
        $this->meta = array_merge($this->meta, $meta);
        $this->attachmentPathResolver = AttachmentPathResolver::forMeta($this->meta);
    }

    public function srcset(string $size): string
    {
        $src = $this->src($size);
        if ($src === '') {
            return '';
        }

        $size = $this->size($size);
        $srcset = (string) wp_calculate_image_srcset(
            [$size['width'], $size['height']],
            $src,
            $this->meta,
            $this->id
        );

        return $srcset;
    }

    public function src(string $size): string
    {
        return $this->attachmentPathResolver->withUrl($size);
    }

    public function size(string $size): array
    {
        if ($size === 'full') {
            return [
                'width' => $this->meta['width'],
                'height' => $this->meta['height'],
                'file' => $this->meta['file'],
                'size' => $size,
            ];
        } elseif ($this->sizeExists($size)) {
            return $this->meta['sizes'][$size];
        }

        return [];
    }

    public function sizeExists(string $size): bool
    {
        return ($size === 'full')
            ? $this->meta['file'] !== ''
            : isset($this->meta['sizes'][$size]['file']);
    }

    public function path(string $size): string
    {
        return $this->attachmentPathResolver->withDir($size);
    }

    public function sizes(): array
    {
        return $this->meta['sizes'];
    }

    /**
     * @param array $original_meta
     * @param string $size
     *
     * @return int filesize in bytes.
     */
    public function diffFilesize(array $original_meta, string $size): int
    {
        $webp_file = $this->attachmentPathResolver->withDir($size);
        $original_file = AttachmentPathResolver::forMeta($original_meta)
            ->withDir($size);

        if ($webp_file === '' || $original_file === '') {
            return 0;
        }

        return filesize($webp_file) - filesize($original_file);
    }

    public function meta(): array
    {
        return $this->meta;
    }
}
