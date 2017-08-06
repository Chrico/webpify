<?php declare( strict_types=1 );

namespace WebPify\Transformer;

class NullTransformer implements ImageTransformerInterface {

	public function create( array $data = [], string $dir ): array {

		return [];
	}

}