<?php declare( strict_types=1 );

namespace WebPify\Renderer;

use WebPify\Attachment\WebPImage;

class ImageRenderer implements ImageRenderInterface {

	/**
	 * Replacements "to" mapped to "from".
	 *
	 * @var array
	 */
	private $default_replacements = [
		'data-src="'                               => 'src="',
		'data-srcset="'                            => 'srcset="',
		'src="' . WebPImage::BASE64_IMAGE . '" />' => '/>',
	];

	public function render( string $img, int $attachment_id, string $size ): string {

		$replacments = $this->default_replacements;

		if ( $attachment_id !== 0 ) {
			$webp = new WebPImage( $attachment_id );

			$webp_src = $webp->src( $size );
			if ( $webp_src !== '' ) {
				$key = WebPImage::DATA_SRC . '="' . $webp_src . '" />';

				$replacments[ $key ] = '/>';
			}

			$webp_srcset = $webp->srcset( $size );
			if ( $webp_srcset !== '' ) {
				$key = WebPImage::DATA_SRCSET . '="' . $webp_srcset . '" />';

				$replacments[ $key ] = '/>';
			};
		}

		$output = str_replace(
			array_values( $replacmentss ),
			array_keys( $replacments ),
			$img
		);

		return $output . '<noscript>' . $img . '</noscript>';
	}

}