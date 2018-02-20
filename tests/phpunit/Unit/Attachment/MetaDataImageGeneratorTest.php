<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Attachment;

use Brain\Monkey\Functions;
use Brain\Monkey\Actions;
use Mockery;
use WebPify\Attachment\MetaDataImageGenerator;
use WebPify\Tests\Unit\AbstractTestCase;
use WebPify\Transformer\ImageTransformerInterface;
use WebPify\WebPify;

/**
 * @package WebPify\Tests\Unit\Attachment
 */
final class MetaDataImageGeneratorTest extends AbstractTestCase {

	public function test_basic() {

		$stub = $this->getMockBuilder( ImageTransformerInterface::class )
			->getMock();

		$testee = new MetaDataImageGenerator( $stub, [] );
		static::assertInstanceOf( MetaDataImageGenerator::class, $testee );
	}

	/**
	 * Test if no error action is triggered when update_post_meta fails.
	 */
	public function test_generate__success() {

		$stub = $this->getMockBuilder( ImageTransformerInterface::class )
			->getMock();
		$stub->method( 'create' )
			->willReturn( 'test.jpg' );

		Actions\expectDone( WebPify::ACTION_ERROR )
			->never();

		Functions\expect( 'trailingslashit' )
			->twice()
			->with( Mockery::type( 'string' ) )
			->andReturn( '' );

		Functions\expect( 'update_post_meta' )
			->once()
			->with( Mockery::type( 'int' ), Mockery::type( 'string' ), Mockery::type( 'array' ) )
			->andReturn( TRUE );

		$metadata   = [
			'file'   => 'foo.jpg',
			'width'  => 1,
			'height' => 1,
			'sizes'  => []
		];
		$wp_uploads = [
			'upload_dir' => '',
			'basedir'    => ''
		];

		$testee = new MetaDataImageGenerator( $stub, $wp_uploads );
        static::assertSame( $metadata, $testee->generate( $metadata, 0 ) );
	}

	/**
	 * Test if action is fired when update_post_meta fails.
	 */
	public function test_generate__failure() {

		$stub = $this->getMockBuilder( ImageTransformerInterface::class )
			->getMock();
		$stub->method( 'create' )
			->willReturn( 'test.jpg' );

		Actions\expectDone( WebPify::ACTION_ERROR )
			->once();

		Functions\expect( 'trailingslashit' )
			->twice()
			->with( Mockery::type( 'string' ) )
			->andReturn( '' );

		Functions\expect( 'update_post_meta' )
			->once()
			->with( Mockery::type( 'int' ), Mockery::type( 'string' ), Mockery::type( 'array' ) )
			->andReturn( FALSE );

		$metadata   = [
			'file'   => 'foo.jpg',
			'width'  => 1,
			'height' => 1,
			'sizes'  => []
		];
		$wp_uploads = [
			'upload_dir' => '',
			'basedir'    => ''
		];

		$testee = new MetaDataImageGenerator( $stub, $wp_uploads );
        static::assertSame( $metadata, $testee->generate( $metadata, 0 ) );
	}
}