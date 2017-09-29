<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Renderer;

use Brain\Monkey\Actions;
use WebPify\Renderer\ImageRenderer;
use WebPify\Renderer\ImageRenderInterface;
use WebPify\Tests\Unit\AbstractTestCase;
use WebPify\WebPify;

/**
 * @package WebPify\Tests\Unit\Renderer
 */
final class ImageRendererTest extends AbstractTestCase {

	public function test_basic() {

		$testee = new ImageRenderer();
		$this->assertInstanceOf( ImageRenderInterface::class, $testee );
	}

	/**
	 * Test complete image with "src"- and "srcset"-attribute.
	 */
	public function test_render() {

		Actions\expectDone( WebPify::ACTION_ERROR )
			->never();

		$placeholder = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';

		$input  = '<img src="foo.jpg" srcset="bar.jpg" />';
		$output = ( new ImageRenderer( $placeholder ) )->render( $input, 0, '' );

		$this->assertContains( 'data-src="foo.jpg"', $output );
		$this->assertContains( 'data-srcset="bar.jpg"', $output );
		$this->assertContains( 'src="' . $placeholder . '"', $output );
		$this->assertContains( '<noscript>' . $input . '</noscript>', $output );
	}

	/**
	 * Test if no placeholder (empty string) is given, that no "src"-attribute is printed out.
	 */
	public function test_render__empty_placeholder() {

		Actions\expectDone( WebPify::ACTION_ERROR )
			->never();

		$input  = '<img src="foo.jpg" />';
		$output = ( new ImageRenderer( '' ) )->render( $input, 0, '' );

		$this->assertNotContains( 'src=""', $output );
		$this->assertContains( '<noscript>' . $input . '</noscript>', $output );
	}

	/**
	 * Test with only "src"-attribute
	 */
	public function test_render__only_src() {

		Actions\expectDone( WebPify::ACTION_ERROR )
			->never();

		$input  = '<img src="foo.jpg" />';
		$output = ( new ImageRenderer( '' ) )->render( $input, 0, '' );

		$this->assertContains( 'data-src="foo.jpg"', $output );
		$this->assertContains( '<noscript>' . $input . '</noscript>', $output );
	}

	/**
	 * Test with only "srcset"-attribute
	 */
	public function test_render__only_srcset() {

		Actions\expectDone( WebPify::ACTION_ERROR )
			->never();

		$input  = '<img srcset="foo.jpg" />';
		$output = ( new ImageRenderer( '' ) )->render( $input, 0, '' );

		$this->assertContains( 'data-srcset="foo.jpg"', $output );
		$this->assertContains( '<noscript>' . $input . '</noscript>', $output );
	}

	/**
	 * Test if a non `img`-Tag input will just be returned and error hook is triggered.
	 */
	public function test_render__no_image() {

		Actions\expectDone( WebPify::ACTION_ERROR )
			->once();

		$input  = '<div class="foo"></div>';
		$output = ( new ImageRenderer( '' ) )->render( $input, 0, '' );

		$this->assertSame( $input, $output );
	}
}