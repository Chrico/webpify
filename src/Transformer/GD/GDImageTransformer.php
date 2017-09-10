<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Transformer\GD;

use WebPify\Transformer\ImageTransformerInterface;
use WebPify\WebPify;

/**
 * @package WebPify\Transformer\GD
 */
final class GDImageTransformer implements ImageTransformerInterface {

	private $image_functions = [
		'jpg'  => 'imagecreatefromjpeg',
		'jpeg' => 'imagecreatefromjpeg',
		'png'  => 'imagecreatefrompng'
	];

	public function create( string $source_file, string $dest_file ): bool {

		$error_context = [
			'dest_file'       => $dest_file,
			'source_file'     => $source_file,
			'image_functions' => $this->image_functions
		];

		$ext = pathinfo( $source_file, PATHINFO_EXTENSION );
		if ( ! isset( $this->image_functions[ $ext ] ) ) {

			do_action(
				WebPify::ACTION_ERROR,
				sprintf( 'Could not find "%s" in available extension.', $ext ),
				$error_context
			);

			return FALSE;
		}

		$func = $this->image_functions[ $ext ];
		// adding the selected function to error context for debugging.
		$error_context[ 'func' ] = $func;
		if ( ! function_exists( $func ) ) {

			do_action(
				WebPify::ACTION_ERROR,
				sprintf( 'The extension "%s" is not available.', $ext ),
				$error_context
			);

			return FALSE;
		}

		$resource = @$func( $source_file );
		if ( ! $resource ) {
			do_action(
				WebPify::ACTION_ERROR,
				sprintf( 'Creating resource failed.', $ext ),
				$error_context
			);

			return FALSE;
		}

		$success = @imagewebp( $resource, $dest_file );
		imagedestroy( $resource );

		if ( ! $success ) {

			do_action(
				WebPify::ACTION_ERROR,
				'Image creation failed.',
				$error_context
			);

			return FALSE;
		}

		return TRUE;
	}

	public function is_activated(): bool {

		return extension_loaded( 'gd' ) && function_exists( 'imagewebp' );
	}
}
