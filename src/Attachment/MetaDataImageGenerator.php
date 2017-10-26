<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Attachment;

use WebPify\Transformer\ImageTransformerInterface;
use WebPify\WebPify;

/**
 * @package WebPify\Attachment
 */
class MetaDataImageGenerator {

	/**
	 * @var ImageTransformerInterface
	 */
	private $transformer;

	/**
	 * @var array
	 */
	private $upload_dir;

	/**
	 * @param ImageTransformerInterface $transformer
	 * @param array                     $upload_dir
	 */
	public function __construct( ImageTransformerInterface $transformer, array $upload_dir ) {

		$this->transformer = $transformer;
		$this->upload_dir  = $upload_dir;
	}

	/**
	 * @param array $metadata
	 * @param int   $attachment_id
	 *
	 * @return array
	 */
	public function generate( array $metadata, int $attachment_id ): array {

		// we've to use the "basedir" for the "full"-image,
		// because the "file" already contains the subdir.
		$dir           = trailingslashit( $this->upload_dir[ 'basedir' ] );
		$webp_metadata = $this->create_metadata( $metadata, $dir );

		if ( ! isset( $webp_metadata[ 'file' ] ) ) {
			return $metadata;
		}

		// append the subdir from full image,
		// because the "sizes" are stored only with filename.
		$dir .= trailingslashit( dirname( $metadata[ 'file' ] ) );
		// create all sizes.
		$webp_metadata[ 'sizes' ] = [];
		foreach ( $metadata[ 'sizes' ] as $size => $data ) {
			$webp_data = $this->create_metadata( $data, $dir );
			if ( isset( $webp_data[ 'file' ] ) ) {
				$webp_metadata[ 'sizes' ][ $size ] = $webp_data;
			}
		}

		$success = (bool) update_post_meta(
			$attachment_id,
			WebPAttachment::ID,
			$webp_metadata
		);

		if ( ! $success ) {
			// Note: WP returns "false" when the existing PostMeta is equal to the new one.
			// So no panic when update_post_meta returns false.
			do_action(
				WebPify::ACTION_ERROR,
				'An error occured while updating the WebP-metadata.',
				[ 'metadata' => $metadata, 'webp_metadata' => $webp_metadata ]
			);
		}

		return $metadata;
	}

	/**
	 * Internal function to create the metadata.
	 *
	 * @param array  $data
	 * @param string $dir
	 *
	 * @return array
	 */
	private function create_metadata( array $data, string $dir ): array {

		$source_file = $dir . $data[ 'file' ];
		$dest_file   = $source_file . '.webp';

		if ( ! $this->transformer->create( $source_file, $dest_file ) ) {
			return [];
		}

		return [
			'width'     => $data[ 'width' ],
			'height'    => $data[ 'height' ],
			'mime-type' => 'image/webp',
			'file'      => str_replace( $dir, '', $dest_file )
		];
	}

}
