<?php declare( strict_types=1 );

namespace WebPify\Transformer;

class NativeExtensionTransformer implements ImageTransformerInterface {

	private $resouce_map = [
		'jpg'  => 'imagecreatefromjpeg',
		'jpeg' => 'imagecreatefromjpeg',
		'png'  => 'imagecreatefrompng'
	];

	public function create( array $data = [], string $dir ): array {

		$file = $dir . $data[ 'file' ];

		$ext = pathinfo( $file, PATHINFO_EXTENSION );
		if ( ! isset( $this->resouce_map[ $ext ] ) ) {

			do_action(
				'WebPify.error',
				sprintf( 'Could not find "%s" in available extension.', $ext ),
				[ 'extensions' => $this->resouce_map ]
			);

			return [];
		}

		$resource = $this->resouce_map[ $ext ]( $file );
		$file     = $file . '.webp';
		$success  = imagewebp( $resource, $file );
		imagedestroy( $resource );

		if ( ! $success ) {

			$context = [
				'file'       => $file,
				'extensions' => $this->resouce_map
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