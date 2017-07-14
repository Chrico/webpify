<?php declare( strict_types=1 );

namespace WebPify\Transformer;

interface ImageTransformerInterface {

	public function create( string $file ): string;
}