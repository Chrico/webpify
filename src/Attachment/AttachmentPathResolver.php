<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Attachment;

/**
 * @package WebPify\Attachment
 */
class AttachmentPathResolver {

	const TYPE_DIR = 'basedir';
	const TYPE_URL = 'baseurl';

	private $meta = [];

	private $uploads_dir = [];

	/**
	 * @param array $attachment_meta
	 *
	 * @return AttachmentPathResolver
	 */
	public static function for_meta( array $attachment_meta ): AttachmentPathResolver {

		return new static( $attachment_meta );
	}

	/**
	 * @param array $attachment_meta
	 */
	public function __construct( array $attachment_meta ) {

		$this->meta        = $attachment_meta;
		$this->uploads_dir = wp_get_upload_dir();
	}

	/**
	 * Returns the full path for an attachment.
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public function with_dir( string $size ): string {

		return $this->resolve( $size, self::TYPE_DIR );
	}

	/**
	 * @param string $size
	 * @param string $type
	 *
	 * @return string full path/url to file
	 */
	public function resolve( string $size, string $type ): string {

		if ( ! isset( $this->uploads_dir[ $type ] ) ) {
			return '';
		}

		// the full is always required either for..
		// ... returning the full
		// ... or getting the sub-dir for a specific size.
		if ( ! isset( $this->meta[ 'file' ] ) ) {
			return '';
		}

		$full = $this->meta[ 'file' ];
		$dir  = trailingslashit( $this->uploads_dir[ $type ] );

		if ( $size === 'full' ) {
			return $dir . $full;
		} elseif ( isset( $this->meta[ 'sizes' ][ $size ][ 'file' ] ) ) {

			$dir .= trailingslashit( _wp_get_attachment_relative_path( $full ) );

			return $dir . $this->meta[ 'sizes' ][ $size ][ 'file' ];
		} else {
			return '';
		}
	}

	/**
	 * Returns the full URL for to an attachment.
	 *
	 * @param string $size
	 *
	 * @return string
	 */
	public function with_url( string $size ): string {

		return $this->resolve( $size, self::TYPE_URL );
	}
}