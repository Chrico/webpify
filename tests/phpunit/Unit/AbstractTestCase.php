<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify\Tests\Unit;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;

/**
 * @package WebPify\Tests\Unit
 */
abstract class AbstractTestCase extends TestCase {

	/**
	 * Sets up the environment.
	 *
	 * @return void
	 */
	protected function setUp() {

		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tears down the environment.
	 *
	 * @return void
	 */
	protected function tearDown() {

		Monkey\tearDown();
		parent::tearDown();
	}

}