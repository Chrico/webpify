<?php declare( strict_types=1 );

namespace WebPify;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @package WebPify
 */
final class WebPify extends Container {

	/**
	 * @var bool
	 */
	private $booted = FALSE;

	/**
	 * @var array
	 */
	private $providers = [];

	/**
	 * Registers a service provider.
	 *
	 * @param ServiceProviderInterface $provider A ServiceProviderInterface instance
	 * @param array                    $values   An array of values that customizes the provider
	 *
	 * @return WebPify
	 */
	public function register( ServiceProviderInterface $provider, array $values = [] ) {

		$this->providers[] = $provider;
		$provider->register( $this );

		foreach ( $values as $key => $value ) {
			$this[ $key ] = $value;
		}

		return $this;
	}

	/**
	 * Boots all service providers.
	 *
	 * This method is automatically called by handle(), but you can use it
	 * to boot all service providers when not handling a request.
	 *
	 * @return bool TRUE if successfully booted | FALSE if already booted before.
	 */
	public function boot(): bool {

		if ( $this->booted ) {
			return FALSE;
		}
		$this->booted = TRUE;

		/**
		 * Fires right before GoogleTagManager gets bootstrapped.
		 *
		 * Hook here to register custom service providers.
		 *
		 * @param GoogleTagManager
		 */
		\do_action( 'WebPify.boot', $this );

		foreach ( $this->providers as $provider ) {
			if ( $provider instanceof Core\BootableProviderInterface ) {
				$provider->boot( $this );
			}
		}

		return TRUE;
	}

}