<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Parser;

/**
 * @package WebPify\Parser
 */
interface ParserInterface
{

    public function parse(string $content): string;
}
