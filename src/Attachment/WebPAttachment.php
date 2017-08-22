<?php declare( strict_types=1 );

namespace WebPify\Attachment;

/**
 * @package WebPify\Attachment
 */
class WebPAttachment {

	const ID = '_webpify_attachment_meta';

	const DATA_SRC = 'data-webp-src';
	const DATA_SRCSET = 'data-webp-srcset';

	/**
	 * Default base64-image as placeholder for not loaded images.
	 */
	const BASE64_IMAGE = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';

	/**
	 * Default meta array which holds the same image formats as "_wp_attachment_metadata".
	 *
	 * @var array
	 */
	private $meta = [
		'file'   => '',
		'width'  => 0,
		'height' => 0,
		'sizes'  => []
	];

	/**
	 * WebPImage constructor.
	 *
	 * @param int $id
	 */
	public function __construct( $id ) {

		$this->id = (int) $id;

		$meta = get_post_meta( $this->id, self::ID, TRUE );
		if ( $meta === '' ) {
			$meta = [];
		}
		$this->meta = array_merge( $this->meta, $meta );
	}

	public function srcset( string $size ): string {

		$src = $this->src( $size );
		if ( $src === '' ) {
			return '';
		}

		$size   = $this->size( $size );
		$srcset = (string) wp_calculate_image_srcset(
			[ $size[ 'width' ], $size[ 'height' ] ],
			$src,
			$this->meta,
			$this->id
		);

		return $srcset;
	}

	public function src( string $size ): string {

		return AttachmentPathResolver::url( $this->meta, $size );
	}

	public function size( string $size ): array {

		if ( $size === 'full' ) {
			return [
				'width'  => $this->meta[ 'width' ],
				'height' => $this->meta[ 'height' ],
				'file'   => $this->meta[ 'file' ],
				'size'   => $size
			];
		} else if ( $this->size_exists( $size ) ) {
			return $this->meta[ 'sizes' ][ $size ];
		}

		return [];
	}

	public function size_exists( string $size ): bool {

		return ( $size === 'full' )
			? $this->meta[ 'file' ] !== ''
			: isset( $this->meta[ 'sizes' ][ $size ][ 'file' ] );
	}

	public function sizes(): array {

		return $this->meta[ 'sizes' ];
	}

	/**
	 * @param array  $original_meta
	 * @param string $size
	 *
	 * @return int filesize in bytes.
	 */
	public function diff_filesize( array $original_meta, string $size ): int {

		$webp_file     = AttachmentPathResolver::dir( $this->meta(), $size );
		$original_file = AttachmentPathResolver::dir( $original_meta, $size );

		if ( $webp_file === '' || $original_file === '' ) {
			return 0;
		}

		return filesize( $webp_file ) - filesize( $original_file );
	}

	public function meta(): array {

		return $this->meta;
	}
}
