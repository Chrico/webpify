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

    public function render(string $img, int $attachmentId, string $size): string
    {
        if (substr($img, 0, 4) !== '<img') {
            do_action(
                WebPify::ACTION_ERROR,
                'The given input is not an <code>img</code>-tag.',
                func_get_args()
            );

            return $img;
        }

        $replacements = [
            'data-src="' => 'src="',
            'data-srcset="' => 'srcset="',
        ];

        if ($this->placeholder !== '') {
            $replacements['src="'.$this->placeholder.'" />'] = '/>';
        }

        if ($attachmentId !== 0) {
            $webp = new WebPAttachment($attachmentId);

            $webpSrc = $webp->src($size);
            if ($webpSrc !== '') {
                $key = WebPAttachment::DATA_SRC.'="'.$webpSrc.'" />';

                $replacements[$key] = '/>';
            }

            $webpSrcset = $webp->srcset($size);
            if ($webpSrcset !== '') {
                $key = WebPAttachment::DATA_SRCSET.'="'.$webpSrcset.'" />';

                $replacements[$key] = '/>';
            };
        }

        $output = str_replace(
            array_values($replacements),
            array_keys($replacements),
            $img
        );

        return $output.'<noscript>'.$img.'</noscript>';
    }
}
