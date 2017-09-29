<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Assets;

use Brain\Monkey\Filters;
use WebPify\Assets\LazyLoadScriptData;
use WebPify\Tests\Unit\AbstractTestCase;

/**
 * @package WebPify\Tests\Unit\Assets
 */
final class LazyLoadScriptDataTest extends AbstractTestCase {

	public function test_get_options() {

		$testee = new LazyLoadScriptData();
		$this->assertNotEmpty( $testee->get_options() );
	}

	/**
	 * Test if we can overwrite the options with the given filter.
	 */
	public function test_get_options__overwrite() {

		$expected = [ 'foo' => 'bar' ];
		Filters\expectApplied( LazyLoadScriptData::FILTER_OPTIONS )
			->once()
			->andReturn( $expected );

		$testee = new LazyLoadScriptData();
		$this->assertSame( $expected, $testee->get_options() );
	}

}