<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit\Transformer\GD;

use WebPify\Tests\Unit\Transformer\AbstractImageTransformerTestCase;
use WebPify\Transformer\GD\GDImageTransformer;
use WebPify\Transformer\ImageTransformerInterface;

/**
 * Class GDImageTransformerTest
 *
 * @package WebPify\Tests\Unit\Transformer\GD
 */
final class GDImageTransformerTest extends AbstractImageTransformerTestCase {

	/**
	 * {@inheritdoc}
	 */
	protected function get_testee(): ImageTransformerInterface {

		return new GDImageTransformer();
	}

}