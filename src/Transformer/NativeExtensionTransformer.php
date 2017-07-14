<?php declare( strict_types=1 );

namespace WebPify\Transformer;

class NativeExtensionTransformer implements ImageTransformerInterface {

	private $resouce_map = [
		'jpg'  => 'imagecreatefromjpeg',
		'jpeg' => 'imagecreatefromjpeg',
		'png'  => 'imagecreatefrompng'
	];

	public function create( string $file ): string {

		$ext = pathinfo( $file, PATHINFO_EXTENSION );

		if ( ! isset( $this->resouce_map[ $ext ] ) ) {
			return '';
		}

		$resource = $this->resouce_map[ $ext ]( $file );
		$file     = $file . '.webp';
		$success  = imagewebp( $resource, $file );

		return $success ? $file : '';
	}

}