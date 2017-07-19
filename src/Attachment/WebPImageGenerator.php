<?php declare( strict_types=1 );

namespace WebPify\Attachment;

use WebPify\Transformer\ImageTransformerInterface;

class WebPImageGenerator {

	public function __construct( ImageTransformerInterface $image_builder, array $upload_dir ) {

		$this->image_builder = $image_builder;
		$this->upload_dir    = $upload_dir;
	}

	public function generate( array $metadata, $attachment_id ): bool {

		$webp_metadata = $this->generate_full( $metadata );
		if ( empty( $webp_metadata ) ) {

			return FALSE;
		}

		$webp_metadata[ 'sizes' ] = $this->generate_sizes( $metadata[ 'sizes' ] );

		return (bool) update_post_meta(
			$attachment_id,
			WebPImage::ID,
			$webp_metadata
		);
	}

	/**
	 * Generate for "full"-image the webp-version.
	 *
	 * @param array $metadata
	 *
	 * @return array
	 */
	private function generate_full( array $metadata = [] ): array {

		// we've to use the "basedir" for the "full"-image.
		$dir = trailingslashit( $this->upload_dir[ 'basedir' ] );

		return $this->image_builder->create( $metadata, $dir );
	}

	/**
	 * Generate for all available "sizes" the webp-version.
	 *
	 * @param array $sizes
	 *
	 * @return array
	 */
	private function generate_sizes( array $sizes = [] ): array {

		$dir         = trailingslashit( $this->upload_dir[ 'path' ] );
		$build_sizes = [];

		foreach ( $sizes as $size => $data ) {
			$webp_data = $this->image_builder->create( $data, $dir );
			if ( isset( $webp_data[ 'file' ] ) ) {
				$build_sizes[ $size ] = $webp_data;
			}
		}

		return $build_sizes;
	}

}
