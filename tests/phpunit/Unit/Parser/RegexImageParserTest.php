<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Parser;

use Brain\Monkey\Functions;
use WebPify\Parser\ParserInterface;
use WebPify\Parser\RegexImageParser;
use WebPify\Renderer\ImageRenderInterface;
use WebPify\Tests\Unit\AbstractTestCase;

/**
 * @package WebPify\Tests\Unit\Parser
 */
final class RegexImageParserTest extends AbstractTestCase {

	public function test_basic() {

		$testee = new RegexImageParser();
		$this->assertInstanceOf( ParserInterface::class, $testee );
	}

	/**
	 * Test if parse will not run on "is_feed()".
	 */
	public function test_parse__is_feed() {

		Functions::expect( 'is_feed' )
			->once()
			->andReturn( TRUE );

		Functions::expect( 'is_preview' )
			->never();

		$expected = '<img src="foo.jpg" />';

		$this->assertSame(
			$expected,
			( new RegexImageParser() )->parse( $expected )
		);
	}

	/**
	 * Test if parse will not run on "is_preview()".
	 */
	public function test_parse__is_preview() {

		Functions::expect( 'is_feed' )
			->once()
			->andReturn( FALSE );

		Functions::expect( 'is_preview' )
			->once()
			->andReturn( TRUE );

		$expected = '<img src="foo.jpg" />';
		$this->assertSame(
			$expected,
			( new RegexImageParser() )->parse( $expected )
		);
	}

	/**
	 * Test if parser will not run when e.G. an image already has "data-src"-attribute.
	 */
	public function test_parse__contains_data_attribute() {

		Functions::expect( 'is_feed' )
			->once()
			->andReturn( FALSE );

		Functions::expect( 'is_preview' )
			->once()
			->andReturn( FALSE );

		$expected = '<img data-src="foo.jpg" />';
		$this->assertSame(
			$expected,
			( new RegexImageParser() )->parse( $expected )
		);
	}

	/**
	 * @param string $input
	 * @param string $expected
	 *
	 * @dataProvider provide_parse
	 */
	public function test_parse( $input, $expected ) {

		Functions::expect( 'is_feed' )
			->once()
			->andReturn( FALSE );

		Functions::expect( 'is_preview' )
			->once()
			->andReturn( FALSE );

		$replacment = '~~foo~~';

		$stub = $this->getMockBuilder( ImageRenderInterface::class )
			->getMock();
		$stub->method( 'render' )
			->willReturn( $replacment );

		$this->assertSame(
			sprintf( $expected, $replacment ),
			( new RegexImageParser( $stub ) )->parse( $input )
		);
	}

	/**
	 * Returns a set of test data for the "parse" method.
	 * @return array
	 */
	public function provide_parse(): array {

		$before = 'All right, but apart from the sanitation, medicine, education, wine, public order, irrigation, roads, the fresh water system and public health, what have the Romans ever done for us?';
		$after  = 'Brought peace?';

		return [
			'valid 1' => [
				$before . '<img src="foo.jpg" srcset="foo.jpg" />' . $after,
				$before . '%1$s' . $after
			],
			'valid 2' => [
				'<img src="foo.jpg" srcset="foo.jpg" />' . $before . '<img src="foo.jpg" srcset="foo.jpg" />' . $after . '<img src="foo.jpg" srcset="foo.jpg" />',
				'%1$s' . $before . '%1$s' . $after . '%1$s'
			],
			'no replacments' => [
				$before,
				$before
			]
		];
	}

	/**
	 * @param string $html
	 * @param array  $expected
	 *
	 * @dataProvider provide_get_attributes
	 */
	public function test_get_attributes( string $html, array $expected ) {

		$this->assertSame(
			$expected,
			( new RegexImageParser() )->get_attributes( $html )
		);
	}

	/**
	 * @return array
	 */
	public function provide_get_attributes(): array {

		$nothing_found = [ 'id' => '', 'size' => '' ];

		return [
			'valid'               => [
				'<img class="wp-image-1 size-full" />',
				[ 'id' => '1', 'size' => 'full' ]
			],
			'missing id'          => [
				'<img class="size-full" />',
				[ 'id' => '', 'size' => 'full' ]
			],
			'missing size'        => [
				'<img class="wp-image-1" />',
				[ 'id' => '1', 'size' => '' ]
			],
			'missing id and size' => [
				'<img />',
				$nothing_found
			],
			'invalid id 1'        => [
				'<img class="wp-image-FOO" />',
				$nothing_found
			],
			'invalid id 2'        => [
				'<img class="wp-image-~!@§$%&/()=?" />',
				$nothing_found
			],
			'invalid size 1'      => [
				'<img class="size-~!@§$%&/()=?" />',
				$nothing_found
			],
			'size valid 1'        => [
				'<img class="size-123" />',
				[ 'id' => '', 'size' => '123' ]
			],
			'size valid 2'        => [
				'<img class="size-foo" />',
				[ 'id' => '', 'size' => 'foo' ]
			],
			'size valid 3'        => [
				'<img class="size-FOO" />',
				[ 'id' => '', 'size' => 'FOO' ]
			],
			'size valid 4'        => [
				'<img class="size-lowerUPPER0123456789-_" />',
				[ 'id' => '', 'size' => 'lowerUPPER0123456789-_' ]
			],
		];
	}

}
