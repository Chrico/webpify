<?php declare( strict_types=1 );

namespace WebPify\Attachment;

use WebPify\Transformer\ImageTransformerInterface;

class MetaDataImageGenerator {

	/**
	 * @var ImageTransformerInterface
	 */
	private $transformer;

	/**
	 * @var array
	 */
	private $upload_dir;

	public function __construct( ImageTransformerInterface $transformer, array $upload_dir ) {

		$this->transformer = $transformer;
		$this->upload_dir    = $upload_dir;
	}

	public function generate( array $metadata, $attachment_id ): array {

		// we've to use the "basedir" for the "full"-image,
		// because the "file" already contains the subdir.
		$dir = trailingslashit( $this->upload_dir[ 'basedir' ] );
		$webp_metadata = $this->transformer->create( $metadata, $dir );

		if ( empty( $webp_metadata ) ) {

			return $metadata;
		}

		// append the subdir from full image,
		// because the "sizes" are stored only with filename.
		$dir .= trailingslashit( dirname( $metadata[ 'file' ] ) );

		$webp_metadata[ 'sizes' ] = $this->generate_sizes( $metadata[ 'sizes' ], $dir );

		$success = (bool) update_post_meta(
			$attachment_id,
			WebPImage::ID,
			$webp_metadata
		);
		if ( ! $success ) {
			// Note: WP returns "false" when the existing PostMeta is equal to the new one.
			// So no panic when update_post_meta returns false.
			do_action(
				'WebPify.error',
				'An error occured while updating the WebP-metadata.',
				[ 'metadata' => $metadata, 'webp_metadata' => $webp_metadata ]
			);
		}

		return $metadata;
	}

	/**
	 * Generate for all available "sizes" the webp-version.
	 *
	 * @param array $sizes
	 * @param string $dir
	 *
	 * @return array
	 */
	private function generate_sizes( array $sizes = [], string $dir ): array {
		$build_sizes = [];
		foreach ( $sizes as $size => $data ) {
			$webp_data = $this->transformer->create( $data, $dir );
			if ( isset( $webp_data[ 'file' ] ) ) {
				$build_sizes[ $size ] = $webp_data;
			}
		}

		return $build_sizes;
	}

}
