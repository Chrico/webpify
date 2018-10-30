<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Attachment\Column;

use WebPify\Attachment\WebPAttachment;

/**
 * @package WebPify\Attachment\Column
 */
final class MediaEditColumn
{

    const ID = 'WebPify__media-column';

    public function title(array $columns): array
    {
        $columns[self::ID] = __('WebP', 'webpify');

        return $columns;
    }

    public function content(string $column_name, int $attachment_id): bool
    {
        if ($column_name !== self::ID) {
            return false;
        }

        $original = wp_get_attachment_metadata($attachment_id, true);

        if (! isset($original['file'])) {
            return false;
        }

        $webp = new WebPAttachment($attachment_id);
        $sizes = [];
        foreach ($original['sizes'] as $size => $data) {
            $exists = $webp->sizeExists($size);
            $saved = $exists
                ? ' <code>'.round($webp->diffFilesize($original, $size) / 1024, 2).' kB</code>'
                : '';
            $sizes[] = ($exists
                    ? '✔'
                    : '✘').' '.$size.$saved;
        }
        printf(
            '<div class="webp__sizes">%s</div>',
            // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
            implode('<br/>', $sizes)
        );

        return true;
    }
}
