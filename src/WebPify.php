<?php declare( strict_types=1 ); # -*- coding: utf-8 -*-

namespace WebPify;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

/**
 * @package WebPify
 */
final class WebPify extends Container implements ContainerInterface {

	const ACTION_BOOT = 'WebPify.boot';
	const ACTION_ERROR = 'WebPify.error';

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
	 * @return ServiceProviderInterface[]
	 */
	public function providers(): array {

		return $this->providers;
	}

	/**
	 * @return bool
	 */
	public function booted(): bool {

		return $this->booted;
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
		\do_action( self::ACTION_BOOT, $this );

		foreach ( $this->providers as $provider ) {
			if ( $provider instanceof Core\BootableProviderInterface ) {
				$provider->boot( $this );
			}
		}

		return TRUE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $id ) {

		return $this[ $id ];
	}

	/**
	 * {@inheritdoc}
	 */
	public function has( $id ) {

		return isset( $this[ $id ] );
	}
}