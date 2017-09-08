<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Transformer;

use WebPify\Tests\Unit\AbstractTestCase;
use WebPify\Transformer\ImageTransformerInterface;
use WebPify\Transformer\NullTransformer;

/**
 * @package WebPify\Tests\Unit\Transformer
 */
final class NullTransformerTest extends AbstractTestCase {

	public function test_basic() {

		$testee = new NullTransformer();
		$this->assertInstanceOf( ImageTransformerInterface::class, $testee );
	}

	public function test_is_activated() {

		$this->assertTrue( ( new NullTransformer() )->is_activated() );
	}

	public function test_create() {

		$this->assertFalse( ( new NullTransformer() )->create( '', '' ) );
	}
}