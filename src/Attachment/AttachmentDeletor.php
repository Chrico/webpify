<?php declare(strict_types=1);

namespace WebPify\Attachment;

use WebPify\WebPify;

/**
 * Class AttachmentDeletor
 *
 * @package WebPify\Attachment
 */
class AttachmentDeletor
{

    /**
     * @param int $post_id
     * @return bool
     */
    public function delete(int $post_id): bool
    {

        $attachment = new WebPAttachment($post_id);

        $sizes   = array_keys($attachment->sizes());
        $sizes[] = 'full';

        foreach ($sizes as $size) {

            if (!$attachment->sizeExists($size)) {
                continue;
            }

            $path = $attachment->path($size);

            if (!file_exists($path)) {
                continue;
            }

            $deleted = @unlink($path);
            if (!$deleted) {
                do_action(
                    WebPify::ACTION_ERROR,
                    'Could not delete file',
                    [
                        'post_id' => $post_id,
                        'path'    => $path,
                    ]
                );
            }
        }

        return true;
    }
}
