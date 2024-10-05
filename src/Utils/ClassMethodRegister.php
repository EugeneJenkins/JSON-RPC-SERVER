<?php

namespace EugeneJenkins\JsonRpcServer\Utils;

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;

class ClassMethodRegister
{
    /**
     * @var string[]
     */
    private array $namespacesList = [];

    public function __construct(
        private readonly CallbackList $callbackList
    )
    {
    }

    public function add(string $classNamespace): void
    {
        $this->namespacesList[] = $classNamespace;
    }

    /**
     * @throws ReflectionException
     */
    public function register(): void
    {
        foreach ($this->namespacesList as $namespace) {
            $class = new $namespace;
            $reflection = new ReflectionClass($class);

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $this->callbackList->add(
                    $method->getName(),
                    $method->getClosure($class)
                );
            }
        }
    }
}
