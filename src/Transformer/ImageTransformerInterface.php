<?php declare( strict_types=1 );

namespace WebPify\Transformer;

interface ImageTransformerInterface {

	/**
	 * @param array $data    [
	 *                       'width'    => int,
	 *                       'height'   => int,
	 *                       'file'     => string,
	 *                       'type'     => string
	 *                     ]
	 * @param string $dir   the full path to the file.
	 *
	 * @return array
	 */
	public function create( array $data = [], string $dir ): array;
}