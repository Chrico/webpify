<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Transformer\Imagick;

use WebPify\Tests\Unit\AbstractTestCase;
use WebPify\Transformer\ImageTransformerInterface;
use WebPify\Transformer\Imagick\ImagickImageTransformer;

/**
 * @package WebPify\Tests\Unit\Transformer\Imagick
 */
final class ImagickImageTransformerTest extends AbstractTestCase {

	protected function setUp() {

		parent::setUp();

		if ( ! ( new ImagickImageTransformer() )->is_activated() ) {
			$this->markTestSkipped( 'Required extensions are not installed' );
		}
	}

	public function test_basic() {

		$testee = new ImagickImageTransformer();
		$this->assertInstanceOf( ImageTransformerInterface::class, $testee );
	}

	public function test_is_activated() {

		$this->assertTrue( ( new ImagickImageTransformer() )->is_activated() );
	}

}