<?php declare( strict_types=1 );

namespace WebPify\Attachment\Column;

use WebPify\Attachment\WebPAttachment;

/**
 * @package WebPify\Attachment\Column
 */
final class MediaEditColumn {

	const ID = 'webpify_media_column';

	public function title( array $columns ): array {

		$columns[ self::ID ] = __( 'WebP', 'WebPify' );

		return $columns;
	}

	public function content( string $column_name, int $attachment_id ): bool {

		if ( $column_name !== self::ID ) {
			return FALSE;
		}

		$original = wp_get_attachment_metadata( $attachment_id, TRUE );

		if ( ! isset( $original[ 'file' ] ) ) {
			return FALSE;
		}

		$webp  = new WebPAttachment( $attachment_id );
		$sizes = [];
		foreach ( $original[ 'sizes' ] as $size => $data ) {
			$exists  = $webp->size_exists( $size );
			$saved   = $exists
				? ' <code>' . round( $webp->diff_filesize( $original, $size ) / 1024, 2 ) . ' kB</code>'
				: '';
			$sizes[] = ( $exists ? '✔' : '✘' ) . ' ' . $size . $saved;
		}
		printf( '<div class="webp__sizes">%s</div>', implode( '<br/>', $sizes ) );

		return TRUE;
	}

}