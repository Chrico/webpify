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
			return [];
		}

		$resource = $this->resouce_map[ $ext ]( $file );
		$file     = $file . '.webp';
		$success  = imagewebp( $resource, $file );

		if ( ! $success ) {
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