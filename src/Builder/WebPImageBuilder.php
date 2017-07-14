<?php declare( strict_types=1 );

namespace WebPify\Builder;

use WebPify\Transformer\ImageTransformerInterface;

class WebPImageBuilder {

	public function __construct( ImageTransformerInterface $image_builder, array $upload_dir ) {

		$this->image_builder = $image_builder;
		$this->upload_dir    = $upload_dir;
	}

	public function build( array $metadata ): array {

		$base_dir = trailingslashit( $this->upload_dir[ 'basedir' ] );
		$dir      = trailingslashit( $this->upload_dir[ 'path' ] );

		$sizes = [];
		foreach ( $metadata[ 'sizes' ] as $size => $data ) {

			$file = $this->image_builder->create( $dir . $data[ 'file' ] );
			if ( $file === '' ) {
				continue;
			}

			$sizes[ $size ] = [
				'file'      => str_replace(
					$dir,
					'',
					$file
				),
				'width'     => $data[ 'width' ],
				'height'    => $data[ 'height' ],
				'mime-type' => 'image/webp'
			];
		}

		$webp_metadata = [
			'sizes' => $sizes
		];

		$file = $this->image_builder->create( $base_dir . $metadata[ 'file' ] );
		if ( $file === '' ) {
			return $webp_metadata;
		}

		$webp_metadata[ 'width' ]  = $metadata[ 'width' ];
		$webp_metadata[ 'height' ] = $metadata[ 'height' ];
		$webp_metadata[ 'file' ]   = str_replace(
			$base_dir,
			'',
			$file
		);

		return $webp_metadata;
	}

}
