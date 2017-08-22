<?php declare( strict_types=1 );

namespace WebPify\Transformer;

/**
 * @package WebPify\Transformer
 */
interface ImageTransformerInterface {

	/**
	 * @param string $source_file the source file with full path in file-system.
	 * @param string $dest_file   the destination file
	 *
	 * @return bool     TRUE if successfully created | FALSE if an error occurred.
	 */
	public function create( string $source_file, string $dest_file ): bool;

	/**
	 * Function to detect, if the current Transformer is available.
	 *
	 * @return bool
	 */
	public function is_activated(): bool;
}