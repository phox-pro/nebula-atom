<?php

namespace Phox\Nebula\Atom\Implementation;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionParameter;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\Atom\Implementation\Exceptions\NonStaticCall;
use Phox\Nebula\Atom\Implementation\Exceptions\DependencyNotFound;
use Phox\Nebula\Atom\Implementation\Exceptions\AnotherInjectionExists;
use Phox\Nebula\Atom\Implementation\Exceptions\BadParamsToDependencyInjection;

class ServiceContainer implements IDependencyInjection 
{
    /**
     * Registered singletons
     */
    private array $singletons = [];

    /**
     * Registered transients
     */
    private array $transients = [];

    public function __construct()
    {
        $this->reset();
    }

    public function reset() : ServiceContainer
    {
        $this->singletons = [];
        $this->transients = [];
        $this->singleton($this, IDependencyInjection::class);
        return $this;
    }

    public function singleton($object, ?string $dependency = null)
    {
        $dependency ??= is_object($object) ? get_class($object) : $object;
        if (array_key_exists($dependency, $this->transients)) {
            error(AnotherInjectionExists::class, $dependency);
        }
        $this->singletons[$dependency] = $object;
    }

    public function transient(string $class, ?string $dependency = null)
    {
        $dependency ??= $class;
        if (array_key_exists($dependency, $this->singletons)) {
            error(AnotherInjectionExists::class, $dependency);
        }
        $this->transients[$dependency] = $class;
    }

    public function make(string $class, array $params = []): object
    {
        $reflection = new ReflectionClass($class);
        return $reflection->isInterface()
            ? $this->get($class) ?: error(DependencyNotFound::class, $class)
            : (($constructor = $reflection->getConstructor())
                ? $reflection->newInstanceArgs($this->getArguments($constructor, $params))
                : $reflection->newInstance() 
            );
    }

    public function call($struct, array $params = [])
    {
        if (is_string($struct) && !function_exists($struct)) {
            $struct = explode('::', $struct);
        }
        if (is_array($struct) && is_string($struct[0])) {
            $reflectionClass = new ReflectionClass($struct[0]);
            if (!$reflectionClass->getMethod($struct[1])->isStatic()) {
                error(NonStaticCall::class, ...$struct);
            }
        }
        if (is_object($struct) && !($struct instanceof Closure)) {
            $struct = [$struct, '__invoke'];
        }
        $reflection = is_array($struct)
            ? new ReflectionMethod(...$struct)
            : new ReflectionFunction($struct);
        return call_user_func_array($struct, $this->getArguments($reflection, $params));
    }

    public function get(string $class): ?object
    {
        if (array_key_exists($class, $this->singletons)) {
            return is_object($this->singletons[$class])
                ? $this->singletons[$class]
                : ($this->singletons[$class] = make($this->singletons[$class]));
        } else if (array_key_exists($class, $this->transients)) {
            return $this->make($this->transients[$class]);
        }
        $reflection = new ReflectionClass($class);
        return $reflection->isInterface() ? null : $this->make($class);
    }

    /**
     * Prepare arguments to function
     *
     * @param ReflectionMethod|ReflectionFunction $method
     * @param array $defaults
     * @return array
     */
    protected function getArguments($method, array $defaults) : array
    {
        $parameters = $method->getParameters();
        $arguments = [];
        foreach ($parameters as $param) {
            $position = $param->getPosition();
            $arguments[$position] = array_key_exists($paramName = $param->getName(), $defaults)
                ? $defaults[$paramName]
                : (array_key_exists($position, $defaults) ? $defaults[$position] : $this->makeArgument($param));
        }
        return $arguments;
    }

    /**
     * Make one argument
     *
     * @param ReflectionParameter $param
     * @return void
     */
    protected function makeArgument(ReflectionParameter $param)
    {
        return ($class = $param->getClass())
            ? $this->makeArgumentObject($param, $class->getName())
            : $this->makeArgumentDefault($param);
    }

    /**
     * Make object as argument
     *
     * @param ReflectionParameter $param
     * @param string $class
     * @return object|null
     */
    protected function makeArgumentObject(ReflectionParameter $param, string $class) : ?object
    {
        return $this->get($class) ?? ($param->allowsNull() ? null : error(DependencyNotFound::class, $class));
    }

    /**
     * Make value as argument
     *
     * @param ReflectionParameter $param
     * @return void
     */
    protected function makeArgumentDefault(ReflectionParameter $param)
    {
        return $param->allowsNull() || ($param->isOptional() && !$param->isDefaultValueAvailable()) ? null : (
            $param->isDefaultValueAvailable() ? $param->getDefaultValue() : error(
                BadParamsToDependencyInjection::class,
                $param->getDeclaringFunction()->getName(),
                $param
            )
        );
    }
}