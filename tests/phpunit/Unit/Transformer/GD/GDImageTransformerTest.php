<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Transformer\GD;

use WebPify\Tests\Unit\AbstractTestCase;
use WebPify\Transformer\GD\GDImageTransformer;
use WebPify\Transformer\ImageTransformerInterface;

/**
 * Class GDImageTransformerTest
 *
 * @package WebPify\Tests\Unit\Transformer\GD
 */
final class GDImageTransformerTest extends AbstractTestCase {

	protected function setUp() {

		parent::setUp();

		if ( ! ( new GDImageTransformer() )->is_activated() ) {
			$this->markTestSkipped( 'Required extensions are not installed' );
		}
	}

	public function test_basic() {

		$testee = new GDImageTransformer();
		$this->assertInstanceOf( ImageTransformerInterface::class, $testee );
	}

	public function test_is_activated() {

		$this->assertTrue( ( new GDImageTransformer() )->is_activated() );
	}

}