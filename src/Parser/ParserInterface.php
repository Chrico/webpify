<?php

namespace WebPify\Parser;

interface ParserInterface {

	public function parse( string $content ): string;
}
