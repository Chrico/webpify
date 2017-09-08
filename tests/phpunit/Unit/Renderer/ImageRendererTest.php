<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Renderer;

use Brain\Monkey\WP\Actions;
use WebPify\Attachment\WebPAttachment;
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

		$input    = '<img src="foo.jpg" srcset="bar.jpg" />';
		$expected = sprintf(
			'<img data-src="foo.jpg" data-srcset="bar.jpg" src="%s" /><noscript>%s</noscript>',
			WebPAttachment::BASE64_IMAGE,
			$input
		);

		$this->assertSame(
			$expected,
			( new ImageRenderer() )->render( $input, 0, '' )
		);
	}

	/**
	 * Test with only "src"-attribute
	 */
	public function test_render__only_src() {


		$input    = '<img src="foo.jpg" />';
		$expected = sprintf(
			'<img data-src="foo.jpg" src="%s" /><noscript>%s</noscript>',
			WebPAttachment::BASE64_IMAGE,
			$input
		);

		$this->assertSame(
			$expected,
			( new ImageRenderer() )->render( $input, 0, '' )
		);
	}

	/**
	 * Test with only "srcset"-attribute
	 */
	public function test_render__only_srcset() {


		$input    = '<img srcset="foo.jpg" />';
		$expected = sprintf(
			'<img data-srcset="foo.jpg" src="%s" /><noscript>%s</noscript>',
			WebPAttachment::BASE64_IMAGE,
			$input
		);

		$this->assertSame(
			$expected,
			( new ImageRenderer() )->render( $input, 0, '' )
		);
	}

	/**
	 * Test if a non `img`-Tag input will just be returned and error hook is triggered.
	 */
	public function test_render__no_image() {

		Actions::expectFired( WebPify::ACTION_ERROR )
			->once();

		$expected = '<div class="foo"></div>';

		$this->assertSame(
			$expected,
			( new ImageRenderer() )->render( $expected, 0, '' )
		);
	}
}