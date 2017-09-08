<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit;

use Brain\Monkey\WP\Actions;
use Mockery;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use WebPify\WebPify;

/**
 * @package WebPify\Tests\Unit
 */
final class WebPifyTest extends AbstractTestCase {

	public function test_basic() {

		$testee = new WebPify();

		$this->assertInstanceOf( ContainerInterface::class, $testee );
		$this->assertInstanceOf( Container::class, $testee );
		$this->assertCount( 0, $testee->providers() );
		$this->assertFalse( $testee->booted() );
	}

	public function test_constructor() {

		$key   = 'foo';
		$value = 'bar';

		$testee = new WebPify( [ $key => $value ] );
		$this->assertSame( $value, $testee->get( $key ) );
		$this->assertSAme( $value, $testee[ $key ] );
	}

	public function test_register() {

		$stub = $this->getMockBuilder( ServiceProviderInterface::class )
			->getMock();

		$stub->expects( $this->once() )
			->method( 'register' );

		$testee = new WebPify();
		$testee->register( $stub );

		$this->assertCount( 1, $testee->providers() );
	}

	public function test_boot() {

		Actions::expectFired( WebPify::ACTION_BOOT )
			->once()
			->with( Mockery::type( WebPify::class ) );

		$testee = new WebPify();
		$testee->boot();

		$this->assertTrue( $testee->booted() );
	}
}