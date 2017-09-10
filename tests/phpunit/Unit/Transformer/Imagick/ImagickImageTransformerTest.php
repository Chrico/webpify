<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Transformer\Imagick;

use WebPify\Tests\Unit\Transformer\AbstractImageTransformerTestCase;
use WebPify\Transformer\ImageTransformerInterface;
use WebPify\Transformer\Imagick\ImagickImageTransformer;

/**
 * @package WebPify\Tests\Unit\Transformer\Imagick
 */
final class ImagickImageTransformerTest extends AbstractImageTransformerTestCase {

	/**
	 * {@inheritdoc}
	 */
	protected function get_testee(): ImageTransformerInterface {

		return new ImagickImageTransformer();
	}
}