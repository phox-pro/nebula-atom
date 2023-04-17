<?php

namespace Phox\Nebula\Atom\Implementation;

use Composer\InstalledVersions;
use Phox\Nebula\Atom\Implementation\Events\ApplicationCompletedEvent;
use Phox\Nebula\Atom\Implementation\Events\ApplicationInitEvent;
use Phox\Nebula\Atom\Implementation\Provider\ProvidersContainer;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainer;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainerAccess;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainerFacade;
use Phox\Nebula\Atom\Implementation\State\State;
use Phox\Nebula\Atom\Implementation\State\StateContainer;
use Phox\Nebula\Atom\Notion\INebulaConfig;
use Phox\Nebula\Atom\Notion\IProviderContainer;
use Phox\Nebula\Atom\Notion\IStateContainer;

class Application
{
    use ServiceContainerAccess;

    protected const NEBULA_CONFIG_FILE_NAME = 'nebula.php';

    public function __construct(protected ?StartupConfiguration $configuration = null)
    {
        $this->configuration ??= new StartupConfiguration();

        ServiceContainerFacade::setContainer($this->configuration->container ?? new ServiceContainer());

        $this->container()->singleton(
            $this->container()->make(ProvidersContainer::class),
            IProviderContainer::class,
        );

        $this->container()->singleton(
            $this->container()->make(StateContainer::class),
            IStateContainer::class,
        );

        $this->container()->singleton($this);

        if ($this->configuration->registerProvidersFromPackages) {
            $this->registerNebulaPackages();
        }
    }

    public function registerNebulaPackages(): void
    {
        $packages = InstalledVersions::getInstalledPackages();

        foreach ($packages as $package) {
            $packagePath = InstalledVersions::getInstallPath($package);
            $configFilePath = $packagePath . DIRECTORY_SEPARATOR . static::NEBULA_CONFIG_FILE_NAME;

            if (
                file_exists($configFilePath) &&
                is_object($config = require $configFilePath) &&
                $config instanceof INebulaConfig
            ) {
                $this->registerByConfig($config);
            }
        }
    }

    public function registerByConfig(INebulaConfig $config): void
    {
        if (!is_null($provider = $config->getProvider())) {
            $this->container()->get(IProviderContainer::class)
                ->addProvider($provider);
        }
    }

    public function run(): void
    {
        (new ApplicationInitEvent($this))->notify();

        $this->registerProviders();

        $this->callStates();

        (new ApplicationCompletedEvent($this))->notify();
    }

    protected function registerProviders(): void
    {
        $providerContainer = $this->container()->get(IProviderContainer::class);
        $providers = $providerContainer->getProviders();

        foreach ($providers as $provider) {
            $provider->register();
        }
    }

    protected function callStates(): void
    {
        $rootStates = $this->container()->get(IStateContainer::class)->getRoot();

        foreach ($rootStates as $state) {
            $this->callState($state);
        }
    }

    protected function callState(State $state, ?State &$previous = null): void
    {
        $state->setPrevious($previous);
        $this->container()->singleton($state, State::class);

        $state->notify();

        $children = $this->container()->get(IStateContainer::class)->getChildren($state::class);

        foreach ($children as $child) {
            $this->callState($child, $state);
        }
    }
}