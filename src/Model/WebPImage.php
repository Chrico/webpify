<?php declare( strict_types=1 );

namespace WebPify\Model;

class WebPImage {

	const ID = '_webpify_attachment_meta';

	const DATA_SRC = 'data-webp-src';
	const DATA_SRCSET = 'data-webp-srcset';

	/**
	 * default base64-image as placeholder for not loaded images.
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

	private $default_size = 'full';

	/**
	 * @var array
	 */
	private $upload_dir;

	/**
	 * WebPImage constructor.
	 *
	 * @param int    $id
	 * @param string $default_size
	 */
	public function __construct( $id, string $default_size = 'full' ) {

		$this->id           = (int) $id;
		$this->default_size = $default_size;

		$meta = get_post_meta( $this->id, self::ID, TRUE );
		if ( $meta === '' ) {
			$meta = [];
		}
		$this->meta       = array_merge( $this->meta, $meta );
		$this->upload_dir = wp_get_upload_dir();
	}

	public function src( string $size = '' ): string {

		if ( $size === '' ) {
			$size = $this->default_size;
		}

		$full     = $this->meta[ 'file' ];
		$base_dir = trailingslashit( $this->upload_dir[ 'baseurl' ] );
		$sub_dir  = trailingslashit( _wp_get_attachment_relative_path( $full ) );

		if ( $size === 'full' ) {

			return $base_dir . $full;

		} elseif ( isset( $this->meta[ 'sizes' ][ $size ] ) ) {

			$file = $this->meta[ 'sizes' ][ $size ][ 'file' ];

			return $base_dir . $sub_dir . $file;
		}

		return '';
	}

	public function srcset( string $size = '' ): string {

		if ( $size === '' ) {
			$size = $this->default_size;
		}

		$src    = $this->src( $size );
		$size   = $this->size( $size );
		$srcset = wp_calculate_image_srcset(
			[ $size[ 'width' ], $size[ 'height' ] ],
			$src,
			$this->meta,
			$this->id
		);

		if ( ! $srcset ) {
			return '';
		}

		return $srcset;
	}

	public function size( string $size = '' ): array {

		if ( $size === '' ) {
			$size = $this->default_size;
		}

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

	public function sizes() {

		return $this->meta[ 'sizes' ];
	}

	public function size_exists( string $size = '' ) {

		if ( $size === '' ) {
			$size = $this->default_size;
		}

		return ( $size === 'full' )
			? $this->meta[ 'file' ] !== ''
			: isset( $this->meta[ 'sizes' ][ $size ] );
	}
}
