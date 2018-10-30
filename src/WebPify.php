<?php declare(strict_types=1); # -*- coding: utf-8 -*-

namespace WebPify;

use Psr\Container\ContainerInterface;
use WebPify\App\BootableProvider;
use WebPify\App\Provider;
use WebPify\Exception\AlreadyBootedException;
use WebPify\Exception\NotFoundException;

// phpcs:disable InpsydeCodingStandard.CodeQuality.ReturnTypeDeclaration.NoReturnType
// phpcs:disable InpsydeCodingStandard.CodeQuality.ArgumentTypeDeclaration.NoArgumentType

/**
 * @package WebPify
 */
final class WebPify implements ContainerInterface
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

    private $values = [];

    /**
     * @param Provider $provider
     *
     * @return WebPify
     * @throws AlreadyBootedException
     */
    public function register(Provider $provider): WebPify
    {
        if ($this->booted) {
            throw new AlreadyBootedException();
        }

        $this->providers[] = $provider;

        $provider->register($this);

        return $this;
    }

    /**
     * @return Provider[]
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
         * Fires right before Plugin gets bootstrapped.
         *
         * Hook here to register custom service providers.
         */
        \do_action(self::ACTION_BOOT, $this);

        foreach ($this->providers as $provider) {
            if ($provider instanceof BootableProvider) {
                $provider->boot($this);
            }
        }

        return true;
    }

    /**
     * @param string $id
     * @param $value
     *
     * @return WebPify
     * @throws AlreadyBootedException
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    public function set(string $id, $value): WebPify
    {
        if ($this->booted) {
            throw new AlreadyBootedException();
        }

        $this->values[$id] = $value;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws NotFoundException
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    // phpcs:disable Inpsyde.CodeQuality.ReturnTypeDeclaration.NoReturnType
    public function get($id)
    {
        if (! $this->has($id)) {
            throw new NotFoundException(
                sprintf('No entry was found for "%s identifier.', (string) $id)
            );
        }

        if (! \is_object($this->values[$id])
            || ! \method_exists($this->values[$id], '__invoke')
        ) {
            return $this->values[$id];
        }

        $raw = $this->values[$id];
        $val = $this->values[$id] = $raw($this);

        return $val;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    // phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
    public function has($id): bool
    {
        return isset($this->values[$id]);
    }
}
