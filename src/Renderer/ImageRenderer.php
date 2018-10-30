<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Renderer;

use WebPify\Attachment\WebPAttachment;
use WebPify\WebPify;

/**
 * @package WebPify\Renderer
 */
final class ImageRenderer implements ImageRenderInterface
{

    /**
     * @var string
     */
    private $placeholder;

    /**
     * @param string $placeholder
     */
    public function __construct(string $placeholder = '')
    {
        $this->placeholder = $placeholder;
    }

    public function render(string $img, int $attachment_id, string $size): string
    {
        if (substr($img, 0, 4) !== '<img') {
            do_action(
                WebPify::ACTION_ERROR,
                'The given input is not an <code>img</code>-tag.',
                func_get_args()
            );

            return $img;
        }

        $replacments = [
            'data-src="' => 'src="',
            'data-srcset="' => 'srcset="',
        ];

        if ($this->placeholder !== '') {
            $replacments['src="'.$this->placeholder.'" />'] = '/>';
        }

        if ($attachment_id !== 0) {
            $webp = new WebPAttachment($attachment_id);

            $webp_src = $webp->src($size);
            if ($webp_src !== '') {
                $key = WebPAttachment::DATA_SRC.'="'.$webp_src.'" />';

                $replacments[$key] = '/>';
            }

            $webp_srcset = $webp->srcset($size);
            if ($webp_srcset !== '') {
                $key = WebPAttachment::DATA_SRCSET.'="'.$webp_srcset.'" />';

                $replacments[$key] = '/>';
            };
        }

        $output = str_replace(
            array_values($replacments),
            array_keys($replacments),
            $img
        );

        return $output.'<noscript>'.$img.'</noscript>';
    }
}
