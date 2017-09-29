<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Attachment;

use Brain\Monkey\Functions;
use Mockery;
use WebPify\Attachment\AttachmentPathResolver;
use WebPify\Tests\Unit\AbstractTestCase;

/**
 * @package WebPify\Tests\Unit\Attachment
 */
final class AttachmentPathResolverTest extends AbstractTestCase {

	public function test_basic() {

		Functions\expect( 'wp_get_upload_dir' )
			->once();

		$this->assertInstanceOf( AttachmentPathResolver::class, new AttachmentPathResolver( [] ) );
	}

	public function test_for_meta() {

		Functions\expect( 'wp_get_upload_dir' )
			->once();

		$this->assertInstanceOf( AttachmentPathResolver::class, AttachmentPathResolver::for_meta( [] ) );
	}

	/**
	 * Test if the wp_get_upload_dir does not return the url/dir in it's array.
	 */
	public function test_resolve__type_not_exists() {

		Functions\expect( 'wp_get_upload_dir' )
			->once();

		$this->assertSame(
			'',
			( new AttachmentPathResolver( [] ) )->resolve( '', '' )
		);
	}

	/**
	 * Test if empty string is returned when no 'file' exists in attachment_meta.
	 */
	public function test_resolve__no_file() {

		$type = 'foo';
		Functions\expect( 'wp_get_upload_dir' )
			->once()
			->andReturn( [ $type => 'bar' ] );

		$this->assertSame(
			'',
			( new AttachmentPathResolver( [] ) )->resolve( '', $type )
		);
	}

	/**
	 * Test if a non existing size does return empty string.
	 */
	public function test_resolve__size_not_available() {

		$meta = [ 'file' => 'foo.jpg' ];

		Functions\expect( 'wp_get_upload_dir' )
			->once()
			->andReturn( [ AttachmentPathResolver::TYPE_DIR => 'bar/' ] );

		Functions\when( 'trailingslashit' )
			->returnArg();

		$this->assertSame(
			'',
			( new AttachmentPathResolver( $meta ) )->resolve( 'non-existing-size', AttachmentPathResolver::TYPE_DIR )
		);
	}

	/**
	 * Test if the full url and dir is correctly returned.
	 */
	public function test_resolve__full() {

		$expected_dir  = __DIR__;
		$expected_file = 'foo.jpg';
		$expected      = $expected_dir . $expected_file;

		$meta = [ 'file' => $expected_file ];

		Functions\expect( 'wp_get_upload_dir' )
			->once()
			->andReturn( [ AttachmentPathResolver::TYPE_DIR => $expected_dir ] );

		Functions\when( 'trailingslashit' )
			->returnArg();

		$this->assertSame(
			$expected,
			( new AttachmentPathResolver( $meta ) )->resolve( 'full', AttachmentPathResolver::TYPE_DIR )
		);
	}

	/**
	 * Test if the url and dir for a defined size is correctly returned.
	 */
	public function test_resolve__size() {

		$expected_dir  = __DIR__;
		$expected_file = 'foo.jpg';
		$size          = 'foo';
		$sub_dir       = '1987/06/';
		$expected      = $expected_dir . $sub_dir . $expected_file;

		$meta = [
			'file'  => $sub_dir . 'full.jpg',
			'sizes' => [
				$size => [
					'file' => $expected_file
				]
			]
		];

		Functions\expect( 'wp_get_upload_dir' )
			->once()
			->andReturn( [ AttachmentPathResolver::TYPE_DIR => $expected_dir ] );

		Functions\when( 'trailingslashit' )
			->returnArg();

		Functions\expect( '_wp_get_attachment_relative_path' )
			->once()
			->with( Mockery::type( 'string' ) )
			->andReturn( $sub_dir );

		$this->assertSame(
			$expected,
			( new AttachmentPathResolver( $meta ) )->resolve( $size, AttachmentPathResolver::TYPE_DIR )
		);
	}

}