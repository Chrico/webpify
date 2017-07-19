<?php declare( strict_types=1 );

namespace WebPify\Core;

use Pimple\Container;

/**
 * Interface BootableProviderInterface
 *
 * @package WebPify\Core;
 */
interface BootableProviderInterface {

	/**
	 * Bootstraps the application.
	 *
	 * This method is called after all services are registered
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 *
	 * @param Container $plugin
	 */
	public function boot( Container $plugin );
}