<?php declare( strict_types=1 );

namespace WebPify\Parser;

/**
 * @package WebPify\Parser
 */
interface ParserInterface {

	public function parse( string $content ): string;
}
