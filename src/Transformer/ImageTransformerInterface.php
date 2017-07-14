<?php

namespace WebPify\Transformer;

interface ImageTransformerInterface {

	public function create( string $file ): string;
}