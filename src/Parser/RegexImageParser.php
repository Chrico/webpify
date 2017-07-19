<?php declare( strict_types=1 );

namespace WebPify\Parser;


use WebPify\Attachment\WebPImage;

class RegexImageParser implements ParserInterface {

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

	/**
	 * RegExes to parse the <img>-tag and collect required values.
	 *
	 * @var array
	 */
	private $regex_search = [
		'id'   => '/wp-image-([0-9]+)/i',
		'size' => '/size-([a-z]+)/i'
	];

	public function parse( string $content ): string {

		return preg_replace_callback(
			'/<img [^>]+>/',
			function ( $match ) {

				return $this->build_image( $match[ 0 ] );
			},
			$content
		);
	}

	public function build_image( string $img ): string {

		$attributes = $this->get_attributes( $img );
		$webp       = new WebPImage( $attributes[ 'id' ] );

		$webp_src = $webp->src( $attributes[ 'size' ] );
		if ( $webp_src !== '' ) {
			$key = WebPImage::DATA_SRC . '="' . $webp_src . '" />';

			$this->replacements[ $key ] = '/>';
		}

		$webp_srcset = $webp->srcset( $attributes[ 'size' ] );
		if ( $webp_srcset !== '' ) {
			$key = WebPImage::DATA_SRCSET . '="' . $webp_srcset . '" />';

			$this->replacements[ $key ] = '/>';
		};

		$output = str_replace(
			array_values( $this->replacements ),
			array_keys( $this->replacements ),
			$img
		);
		$output .= '<noscript>' . $img . '</noscript>';

		return $output;
	}

	/**
	 * Returns a collection of image attributes parsed via RegEx.
	 *
	 * @param string $html the image tag.
	 *
	 * @return array
	 */
	public function get_attributes( string $html ): array {

		$data = array_map(
			function ( $regex ) use ( $html ) {

				if ( preg_match( $regex, $html, $value ) ) {
					return $value[ 1 ];
				}

				return '';
			},
			$this->regex_search
		);

		return $data;
	}

}
