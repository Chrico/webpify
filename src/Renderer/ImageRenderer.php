<?php declare( strict_types=1 );

namespace WebPify\Renderer;

use WebPify\Attachment\WebPImage;

class ImageRenderer implements ImageRenderInterface {

	/**
	 * Replacements "to" mapped to "from".
	 *
	 * @var array
	 */
	private $replacements = [
		'data-src="'                               => 'src="',
		'data-srcset="'                            => 'srcset="',
		'src="' . WebPImage::BASE64_IMAGE . '" />' => '/>',
	];

	public function render( string $img, int $attachment_id, string $size ) : string {

		if ( $attachment_id !== 0 ) {
			$webp = new WebPImage( $attachment_id );

			$webp_src = $webp->src( $size );
			if ( $webp_src !== '' ) {
				$key = WebPImage::DATA_SRC . '="' . $webp_src . '" />';

				$this->replacements[ $key ] = '/>';
			}

			$webp_srcset = $webp->srcset( $size );
			if ( $webp_srcset !== '' ) {
				$key = WebPImage::DATA_SRCSET . '="' . $webp_srcset . '" />';

				$this->replacements[ $key ] = '/>';
			};
		}

		$output = str_replace(
			array_values( $this->replacements ),
			array_keys( $this->replacements ),
			$img
		);

		return $output . '<noscript>' . $img . '</noscript>';
	}

}