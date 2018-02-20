<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit;

use Brain\Monkey\Actions;
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

		static::assertInstanceOf( ContainerInterface::class, $testee );
        static::assertInstanceOf( Container::class, $testee );
        static::assertCount( 0, $testee->providers() );
        static::assertFalse( $testee->booted() );
	}

	public function test_constructor() {

		$key   = 'foo';
		$value = 'bar';

		$testee = new WebPify( [ $key => $value ] );
        static::assertSame( $value, $testee->get( $key ) );
        static::assertSAme( $value, $testee[ $key ] );
	}

	public function test_register() {

		$stub = $this->getMockBuilder( ServiceProviderInterface::class )
			->getMock();

		$stub->expects( $this->once() )
			->method( 'register' );

		$testee = new WebPify();
		$testee->register( $stub );

        static::assertCount( 1, $testee->providers() );
	}

	public function test_boot() {

		Actions\expectDone( WebPify::ACTION_BOOT )
			->once()
			->with( Mockery::type( WebPify::class ) );

		$testee = new WebPify();
		$testee->boot();

        static::assertTrue( $testee->booted() );
	}
}