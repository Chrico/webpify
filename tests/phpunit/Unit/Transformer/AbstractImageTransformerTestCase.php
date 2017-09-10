<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Transformer;

use Brain\Monkey\WP\Actions;
use WebPify\Tests\Unit\AbstractTestCase;
use WebPify\Transformer\ImageTransformerInterface;
use WebPify\WebPify;

/**
 * @package WebPify\Tests\Unit\Transformer
 */
abstract class AbstractImageTransformerTestCase extends AbstractTestCase {

	/**
	 * @return ImageTransformerInterface
	 */
	abstract protected function get_testee(): ImageTransformerInterface;

	protected function setUp() {

		parent::setUp();

		if ( ! $this->get_testee()
			->is_activated() ) {
			$this->markTestSkipped( 'Required extensions are not installed' );
		}
	}

	/**
	 * Test default behavior of the instance.
	 */
	public function test_basic() {

		$this->assertInstanceOf( ImageTransformerInterface::class, $this->get_testee() );
	}

	/**
	 * Test if a non supported file format returns "false".
	 */
	public function test_create__not_supported_file_format() {

		Actions::expectFired( WebPify::ACTION_ERROR )
			->once();

		$this->assertFalse(
			$this->get_testee()
				->create( __DIR__ . '../../fixtures/no-image.txt', '' )
		);
	}

	/**
	 * Test if a broken source file returns false.
	 */
	public function test_create__broken_source_file() {

		Actions::expectFired( WebPify::ACTION_ERROR )
			->once();

		$this->assertFalse(
			$this->get_testee()
				->create( '../../fixtures/text-file-as-image.jpg', '' )
		);
	}

	/**
	 * Test if a not existing source file returns false.
	 */
	public function test_create__not_existing_file() {

		Actions::expectFired( WebPify::ACTION_ERROR )
			->once();

		$this->assertFalse(
			$this->get_testee()
				->create( 'undefined.jpg', '' )
		);
	}

	/**
	 * Test if detection of required extensions works properly.
	 */
	public function test_is_activated() {

		$this->assertTrue(
			$this->get_testee()
				->is_activated()
		);
	}
}