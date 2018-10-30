<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Attachment;

use WebPify\Transformer\ImageTransformerInterface;
use WebPify\WebPify;

/**
 * @package WebPify\Attachment
 */
class MetaDataImageGenerator
{

    /**
     * @var ImageTransformerInterface
     */
    private $transformer;

    /**
     * @var array
     */
    private $uploadDir;

    /**
     * @param ImageTransformerInterface $transformer
     * @param array $uploadDir
     */
    public function __construct(ImageTransformerInterface $transformer, array $uploadDir)
    {
        $this->transformer = $transformer;
        $this->uploadDir = $uploadDir;
    }

    /**
     * @param array $metadata
     * @param int $attachmentId
     *
     * @return array
     */
    public function generate(array $metadata, int $attachmentId): array
    {
        // we've to use the "basedir" for the "full"-image,
        // because the "file" already contains the subdir.
        $dir = trailingslashit($this->uploadDir['basedir']);
        $webpMetadata = $this->createMetadata($metadata, $dir);

        if (! isset($webpMetadata['file'])) {
            return $metadata;
        }

        // append the subdir from full image,
        // because the "sizes" are stored only with filename.
        $dir .= trailingslashit(dirname($metadata['file']));
        // create all sizes.
        $webpMetadata['sizes'] = [];
        foreach ($metadata['sizes'] as $size => $data) {
            $webpData = $this->createMetadata($data, $dir);
            if (isset($webpData['file'])) {
                $webpMetadata['sizes'][$size] = $webpData;
            }
        }

        $success = (bool) update_post_meta(
            $attachmentId,
            WebPAttachment::ID,
            $webpMetadata
        );

        if (! $success) {
            // Note: WP returns "false" when the existing PostMeta is equal to the new one.
            // So no panic when update_post_meta returns false.
            do_action(
                WebPify::ACTION_ERROR,
                'An error occurred while updating the WebP-metadata.',
                ['metadata' => $metadata, 'webpMetadata' => $webpMetadata]
            );
        }

        return $metadata;
    }

    /**
     * Internal function to create the metadata.
     *
     * @param array $data
     * @param string $dir
     *
     * @return array
     */
    private function createMetadata(array $data, string $dir): array
    {
        $sourceFile = $dir.$data['file'];
        $destFile = $sourceFile.'.webp';

        if (! $this->transformer->create($sourceFile, $destFile)) {
            return [];
        }

        return [
            'width' => $data['width'],
            'height' => $data['height'],
            'mime-type' => 'image/webp',
            'file' => str_replace($dir, '', $destFile),
        ];
    }
}
