<?php declare( strict_types=1 );

namespace WebPify\Transformer;

class NativeExtensionTransformer implements ImageTransformerInterface {

	private $image_functions = [
		'jpg'  => 'imagecreatefromjpeg',
		'jpeg' => 'imagecreatefromjpeg',
		'png'  => 'imagecreatefrompng'
	];

	public function create( array $data = [], string $dir ): array {

		$file = $dir . $data[ 'file' ];

		$ext = pathinfo( $file, PATHINFO_EXTENSION );
		if ( ! isset( $this->image_functions[ $ext ] ) ) {

			do_action(
				'WebPify.error',
				sprintf( 'Could not find "%s" in available extension.', $ext ),
				[ 'image_functions' => $this->image_functions ]
			);

			return [];
		}

		$func = $this->image_functions[ $ext ];
		if ( ! function_exists( $func ) ) {

			do_action(
				'WebPify.error',
				sprintf( 'The extension "%s" is not available.', $ext ),
				[ 'image_functions' => $this->image_functions, 'extension' => $ext ]
			);

			return [];
		}

		$resource = $func( $file );
		$file     = $file . '.webp';
		$success  = imagewebp( $resource, $file );
		imagedestroy( $resource );

		if ( ! $success ) {

			$context = [
				'file'            => $file,
				'image_functions' => $this->image_functions
			];

			do_action(
				'WebPify.error',
				'Image creation failed.',
				$context
			);

			return [];
		}

		return [
			'width'     => $data[ 'width' ],
			'height'    => $data[ 'height' ],
			'mime-type' => 'image/webp',
			'file'      => str_replace(
				$dir,
				'',
				$file
			)
		];
	}

}