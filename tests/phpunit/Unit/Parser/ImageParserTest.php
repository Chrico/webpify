<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Parser;

use Brain\Monkey\Functions;
use WebPify\Parser\ImageParser;
use WebPify\Parser\ParserInterface;
use WebPify\Renderer\ImageRenderInterface;
use WebPify\Tests\Unit\AbstractTestCase;

/**
 * @package WebPify\Tests\Unit\Parser
 */
final class ImageParserTest extends AbstractTestCase
{

    public function test_basic()
    {
        $testee = new ImageParser();
        static::assertInstanceOf(ParserInterface::class, $testee);
    }

    /**
     * Test if parse will not run on "is_feed()".
     */
    public function test_parse__is_feed()
    {
        Functions\expect('is_feed')
            ->once()
            ->andReturn(true);

        Functions\expect('is_preview')
            ->never();

        $expected = '<img src="foo.jpg" />';

        static::assertSame(
            $expected,
            (new ImageParser())->parse($expected)
        );
    }

    /**
     * Test if parse will not run on "is_preview()".
     */
    public function test_parse__is_preview()
    {
        Functions\expect('is_feed')
            ->once()
            ->andReturn(false);

        Functions\expect('is_preview')
            ->once()
            ->andReturn(true);

        $expected = '<img src="foo.jpg" />';
        static::assertSame(
            $expected,
            (new ImageParser())->parse($expected)
        );
    }

    /**
     * Test if parser will not run when e.G. an image already has "data-src"-attribute.
     */
    public function test_parse__contains_data_attribute()
    {
        Functions\expect('is_feed')
            ->once()
            ->andReturn(false);

        Functions\expect('is_preview')
            ->once()
            ->andReturn(false);

        $expected = '<img data-src="foo.jpg" />';
        static::assertSame(
            $expected,
            (new ImageParser())->parse($expected)
        );
    }

    /**
     * @param string $input
     * @param string $expected
     *
     * @dataProvider provide_parse
     */
    public function test_parse($input, $expected)
    {
        Functions\expect('is_feed')
            ->once()
            ->andReturn(false);

        Functions\expect('is_preview')
            ->once()
            ->andReturn(false);

        $replacement = '~~foo~~';

        $stub = $this->getMockBuilder(ImageRenderInterface::class)
            ->getMock();
        $stub->method('render')
            ->willReturn($replacement);

        static::assertSame(
            sprintf($expected, $replacement),
            (new ImageParser($stub))->parse($input)
        );
    }

    /**
     * Returns a set of test data for the "parse" method.
     */
    public function provide_parse()
    {
        static::markTestIncomplete(
            'After replacing the RegExImageParser with ImageParser, this test has to be implemented again.'
        );

        $before = 'All right, but apart from the sanitation, medicine, education, wine, public order, irrigation, roads, the fresh water system and public health, what have the Romans ever done for us?';
        $after = 'Brought peace?';

        yield 'valid 1' => [
            $before.'<img src="foo.jpg" srcset="foo.jpg" />'.$after,
            $before.'%1$s'.$after,
        ];

        yield 'valid 2' => [
            '<img src="foo.jpg" srcset="foo.jpg" />'
            .$before
            .'<img src="foo.jpg" srcset="foo.jpg" />'
            .$after
            .'<img src="foo.jpg" srcset="foo.jpg" />',
            '%1$s'.$before.'%1$s'.$after.'%1$s',
        ];

        yield 'no replacments' => [
            $before,
            $before,
        ];
    }

    /**
     * @param string $html
     * @param array $expected
     *
     * @dataProvider provide_get_attributes
     */
    public function test_get_attributes(string $html, array $expected)
    {
        static::assertSame(
            $expected,
            (new ImageParser())->attributes($html)
        );
    }

    public function provide_get_attributes()
    {
        $nothing_found = [];

        yield 'missing id' => [
            '<img class="size-full" />',
            $nothing_found,
        ];

        yield 'missing src' => [
            '<img class="wp-image-1" />',
            $nothing_found,
        ];

        yield 'missing id and size' => [
            '<img />',
            $nothing_found,
        ];

        yield 'invalid id 1' => [
            '<img class="wp-image-FOO" />',
            $nothing_found,
        ];

        yield 'invalid id 2' => [
            '<img class="wp-image-~!@§$%&/()=?" />',
            $nothing_found,
        ];
    }
}
