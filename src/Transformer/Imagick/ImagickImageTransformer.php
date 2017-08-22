<?php declare( strict_types=1 );

namespace WebPify\Transformer\Imagick;

use WebPify\Transformer\ImageTransformerInterface;
use WP_CLI\Iterators\Exception;

/**
 * @package WebPify\Transformer\Imagick
 *
 * @link    http://www.imagemagick.org/script/webp.php
 * @link    https://stackoverflow.com/questions/37711492/imagemagick-specific-webp-calls-in-php
 */
final class ImagickImageTransformer implements ImageTransformerInterface {

	public function create( string $source_file, string $dest_file ): bool {

		try {

			$error_context = [
				'source_file' => $source_file,
				'dest_file'   => $dest_file
			];

			$ext = strtolower( pathinfo( $source_file, PATHINFO_EXTENSION ) );
			if ( ! in_array( $ext, [ 'jpg', 'jpeg', 'png' ] ) ) {
				do_action(
					'WebPify.error',
					sprintf( 'The extension "%s" is not supported', $ext ),
					$error_context
				);

				return FALSE;
			}

			$im = new \Imagick( $source_file );
			$im->setImageFormat( 'WEBP' );
			$im->setOption( 'webp:method', '6' );
			$im->setOption( 'webp:low-memory', 'true' );

			if ( $ext === 'png' ) {
				$im->setOption( 'webp:lossless', 'true' );
			}

			return $im->writeImage( $dest_file );
		}
		catch ( Exception $e ) {

			$error_context[ 'exception' ] = $e;
			if ( isset( $im ) ) {
				$error_context[ 'imagick' ] = $im;
			}

			do_action(
				'WebPify.error',
				$e->getMessage(),
				$error_context
			);

			return FALSE;
		}
	}

	public function is_activated(): bool {

		return extension_loaded( 'imagick' ) && class_exists( \Imagick::class );
	}

}
