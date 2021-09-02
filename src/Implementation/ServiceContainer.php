<?php

namespace Phox\Nebula\Atom\Implementation;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\Atom\Implementation\Exceptions\DependencyNotFound;
use Phox\Nebula\Atom\Implementation\Exceptions\AnotherInjectionExists;
use Phox\Nebula\Atom\Implementation\Exceptions\BadParamsToDependencyInjection;
use ReflectionUnionType;

class ServiceContainer implements IDependencyInjection 
{
    private array $singletons = [];
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

    public function singleton(object|string $object, ?string $dependency = null): object|string|null
    {
        $dependency ??= is_object($object) ? get_class($object) : $object;
        if (array_key_exists($dependency, $this->transients)) {
            throw new AnotherInjectionExists($dependency);
        }

        $oldDependency = $this->singletons[$dependency] ?? null;

        $this->singletons[$dependency] = $object;

        return $oldDependency;
    }

    public function transient(string $class, ?string $dependency = null): ?string
    {
        $dependency ??= $class;
        if (array_key_exists($dependency, $this->singletons)) {
            throw new AnotherInjectionExists($dependency);
        }

        $oldDependency = $this->transients[$dependency] ?? null;

        $this->transients[$dependency] = $class;

        return $oldDependency;
    }

    public function deleteDependency(string $dependency): void
    {
        if (array_key_exists($dependency, $this->singletons)) {
            unset($this->singletons[$dependency]);
        } elseif (array_key_exists($dependency, $this->transients)) {
            unset($this->transients[$dependency]);
        }
    }

    public function make(string $class, array $params = []): object
    {
        $reflection = new ReflectionClass($class);

        if ($reflection->isInterface()) {
            return $this->get($class) ?? throw new DependencyNotFound($class);
        }

        $constructor = $reflection->getConstructor();

        return $constructor
            ? $reflection->newInstanceArgs($this->getArguments($constructor, $params))
            : $reflection->newInstance();
    }

    public function call(callable $callback, array $params = []): mixed
    {
        if (is_string($callback) && !function_exists($callback)) {
            $callback = explode('::', $callback);
        }

        if (is_object($callback) && !($callback instanceof Closure)) {
            $callback = [$callback, '__invoke'];
        }

        $reflection = is_array($callback)
            ? new ReflectionMethod(...$callback)
            : new ReflectionFunction($callback);

        return call_user_func_array($callback, $this->getArguments($reflection, $params));
    }

    public function get(string $class): ?object
    {
        if (array_key_exists($class, $this->singletons)) {
            if (!is_object($this->singletons[$class])) {
                $this->singletons[$class] = $this->make($this->singletons[$class]);
            }

            return $this->singletons[$class];
        }

        if (array_key_exists($class, $this->transients)) {
            return $this->make($this->transients[$class]);
        }

        $reflection = new ReflectionClass($class);

        return $reflection->isInterface() ? null : $this->make($class);
    }

    protected function getArguments(ReflectionMethod|ReflectionFunction $method, array $defaults) : array
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
     * @throws BadParamsToDependencyInjection
     * @throws DependencyNotFound
     */
    protected function makeArgument(ReflectionParameter $param): mixed
    {
        $type = $param->getType();

        if (is_null($type)) {
            return $this->makeArgumentDefault($param);
        }

        return $type instanceof ReflectionNamedType
            ? $this->makeNamedArgument($param, $type)
            : $this->makeUnionTypeArgument($param, $type);
    }

    /**
     * @throws BadParamsToDependencyInjection
     * @throws DependencyNotFound
     */
    protected function makeUnionTypeArgument(ReflectionParameter $param, ReflectionUnionType $type): mixed
    {
        $types = $type->getTypes();
        $value = null;
        $lastException = null;

        foreach ($types as $type) {
            try {
                $value = $this->makeNamedArgument($param, $type);

                if (!is_null($value)) {
                    return $value;
                }
            } catch (BadParamsToDependencyInjection|DependencyNotFound $exception) {
                $lastException = $exception;
            }
        }

        return is_null($lastException)
            ? $value
            : throw $lastException;
    }

    /**
     * @throws DependencyNotFound
     */
    protected function makeArgumentObject(ReflectionParameter $param, string $class) : ?object
    {
        return $this->get($class) ?? ($param->allowsNull() ? null : throw new DependencyNotFound($class));
    }

    /**
     * @throws BadParamsToDependencyInjection
     */
    protected function makeArgumentDefault(ReflectionParameter $param): mixed
    {
        if ($param->allowsNull()) {
            return null;
        }

        if ($param->isOptional() && !$param->isDefaultValueAvailable()) {
            return null;
        }

        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }

        throw new BadParamsToDependencyInjection($param->getDeclaringFunction()->getName(), $param);
    }

    /**
     * @throws BadParamsToDependencyInjection
     * @throws DependencyNotFound
     */
    protected function makeNamedArgument(ReflectionParameter $param, ReflectionNamedType $namedType): mixed
    {
        return $namedType->isBuiltin()
            ? $this->makeArgumentDefault($param)
            : $this->makeArgumentObject($param, $namedType->getName());
    }
}