<?php

namespace Phox\Nebula\Atom\Implementation\Services;

use Phox\Nebula\Atom\Notion\IServiceContainer;

class ServiceContainer implements IServiceContainer
{
    protected array $singletons = [];
    protected array $transients = [];

    public function __construct()
    {
        $this->reset();
    }

    public function singleton(object|string $service, ?string $dependency = null): void
    {
        $dependency ??= is_object($service) ? get_class($service) : $service;
        $this->singletons[$dependency] = $service;
    }

    public function transient(string $service, ?string $dependency = null): void
    {
        $dependency ??= $service;

        $this->transients[$dependency] = $service;
    }

    public function make(string $service): object
    {
        $realService = $this->singletons[$service] ?? $this->transients[$service] ?? $service;

        return new $realService();
    }

    public function get(string $service): ?object
    {
        if (array_key_exists($service, $this->singletons)) {
            if (!is_object($this->singletons[$service])) {
                $this->singletons[$service] = $this->make($this->singletons[$service]);
            }

            return $this->singletons[$service];
        }

        return $this->make(
            array_key_exists($service, $this->transients)
                ? $this->transients[$service]
                : $service
        );
    }

    public function reset(): void
    {
        $this->singletons = [];
        $this->transients = [];
    }
}