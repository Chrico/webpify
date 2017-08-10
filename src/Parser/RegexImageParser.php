<?php declare( strict_types=1 );

namespace WebPify\Parser;

use WebPify\Renderer\ImageRenderInterface;

final class RegexImageParser implements ParserInterface {

	/**
	 * RegExes to parse the <img>-tag and collect required values.
	 *
	 * @var array
	 */
	private $regex_search = [
		'id'   => '/wp-image-([0-9]+)/i',
		'size' => '/size-([a-z]+)/i'
	];

	/**
	 * @var ImageRenderInterface
	 */
	private $renderer;

	/**
	 * RegexImageParser constructor.
	 *
	 * @param ImageRenderInterface $renderer
	 */
	public function __construct( ImageRenderInterface $renderer ) {

		$this->renderer = $renderer;

	}

	public function parse( string $content ): string {

		// Don't lazyload for feeds or previews.
		if ( is_feed() || is_preview() ) {
			return $content;
		}

		// Don't lazy-load if the content has already been run through previously
		if ( FALSE !== strpos( $content, 'data-src' ) ) {
			return $content;
		}

		return preg_replace_callback(
			'/<img [^>]+>/',
			function ( $match ) {

				$img        = $match[ 0 ];
				$attributes = $this->get_attributes( $img );

				return $this->renderer->render( $img, (int) $attributes[ 'id' ], $attributes[ 'size' ] );
			},
			$content
		);
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
