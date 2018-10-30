<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\App\Provider;

use WebPify\App\BootableProvider;
use WebPify\Attachment\AttachmentDeletor;
use WebPify\Attachment\Column\MediaEditColumn;
use WebPify\Attachment\MetaDataImageGenerator;
use WebPify\Transformer\ImageTransformerInterface;
use WebPify\WebPify;

/**
 * Class AttachmentProvider
 *
 * @package WebPify\Attachment
 */
final class AttachmentProvider implements BootableProvider
{

    public function register(WebPify $plugin)
    {
        $plugin->set(
            MetaDataImageGenerator::class,
            function (WebPify $plugin): MetaDataImageGenerator {
                return new MetaDataImageGenerator(
                    $plugin->get(ImageTransformerInterface::class),
                    wp_get_upload_dir()
                );
            }
        );

        $plugin->set(
            MediaEditColumn::class,
            function (): MediaEditColumn {
                return new MediaEditColumn();
            }
        );

        $plugin->set(
            AttachmentDeletor::class,
            function (): AttachmentDeletor {
                return new AttachmentDeletor();
            }
        );
    }

    public function boot(WebPify $plugin)
    {
        add_filter(
            'wp_generate_attachment_metadata',
            [$plugin->get(MetaDataImageGenerator::class), 'generate'],
            10,
            2
        );

        if (is_admin()) {
            add_filter(
                'manage_media_columns',
                [$plugin->get(MediaEditColumn::class), 'title']
            );

            add_filter(
                'manage_media_custom_column',
                [$plugin->get(MediaEditColumn::class), 'content'],
                10,
                2
            );

            add_action(
                'delete_attachment',
                [$plugin->get(AttachmentDeletor::class), 'delete']
            );
        }
    }
}
