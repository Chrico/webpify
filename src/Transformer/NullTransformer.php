<?php declare( strict_types=1 );

namespace WebPify\Transformer;

/**
 * @package WebPify\Transformer
 */
class NullTransformer implements ImageTransformerInterface {

	public function create( string $source_file, string $dest_file ): bool {

		return FALSE;
	}

	public function is_activated(): bool {

		return TRUE;
	}
}