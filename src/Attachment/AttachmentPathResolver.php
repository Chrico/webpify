<?php declare( strict_types=1 );

namespace WebPify\Attachment;

/**
 * @package WebPify\Attachment
 */
class AttachmentPathResolver {

	const TYPE_DIR = 'basedir';
	const TYPE_URL = 'baseurl';

	/**
	 * Returns the full path for an attachment.
	 *
	 * @param array  $attachment_meta
	 * @param string $size
	 *
	 * @return string
	 */
	public static function dir( array $attachment_meta, string $size ): string {

		return self::get( $attachment_meta, $size, self::TYPE_DIR );
	}

	/**
	 * Returns the full URL for an attachment,
	 *
	 * @param array  $attachment_meta
	 * @param string $size
	 *
	 * @return string
	 */
	public static function url( array $attachment_meta, string $size ): string {

		return self::get( $attachment_meta, $size, self::TYPE_URL );
	}

	/**
	 * @param array  $attachment_meta
	 * @param string $size
	 * @param string $type
	 *
	 * @return string full path/url to file
	 */
	private static function get( array $attachment_meta, string $size, string $type ): string {

		$upload_dir = wp_get_upload_dir();
		$dir        = trailingslashit( $upload_dir[ $type ] );

		// the full is always required either for..
		// ... returning the full
		// ... or getting the sub-dir for a specific size.
		if ( ! isset( $attachment_meta[ 'file' ] ) ) {
			return '';
		}

		$full = $attachment_meta[ 'file' ];

		if ( $size === 'full' ) {
			return $dir . $full;
		} elseif ( isset( $attachment_meta[ 'sizes' ][ $size ][ 'file' ] ) ) {

			$dir .= trailingslashit( _wp_get_attachment_relative_path( $full ) );

			return $dir . $attachment_meta[ 'sizes' ][ $size ][ 'file' ];
		} else {
			return '';
		}
	}
}