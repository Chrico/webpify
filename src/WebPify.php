<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Container\ContainerInterface;

// phpcs:disable InpsydeCodingStandard.CodeQuality.ReturnTypeDeclaration.NoReturnType
// phpcs:disable InpsydeCodingStandard.CodeQuality.ArgumentTypeDeclaration.NoArgumentType

/**
 * @package WebPify
 */
final class WebPify extends Container implements ContainerInterface
{

    const ACTION_BOOT = 'WebPify.boot';
    const ACTION_ERROR = 'WebPify.error';

    /**
     * @var bool
     */
    private $booted = false;

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
    public function register(ServiceProviderInterface $provider, array $values = []): WebPify
    {

        $this->providers[] = $provider;
        $provider->register($this);

        foreach ($values as $key => $value) {
            $this[ $key ] = $value;
        }

        return $this;
    }

    /**
     * @return ServiceProviderInterface[]
     */
    public function providers(): array
    {

        return $this->providers;
    }

    /**
     * @return bool
     */
    public function booted(): bool
    {

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
    public function boot(): bool
    {

        if ($this->booted) {
            return false;
        }
        $this->booted = true;

        /**
         * Fires right before GoogleTagManager gets bootstrapped.
         *
         * Hook here to register custom service providers.
         *
         * @param GoogleTagManager
         */
        \do_action(self::ACTION_BOOT, $this);

        foreach ($this->providers as $provider) {
            if ($provider instanceof Core\BootableProviderInterface) {
                $provider->boot($this);
            }
        }

        return true;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {

        return $this[ $id ];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id): bool
    {

        return isset($this[ $id ]);
    }
}
