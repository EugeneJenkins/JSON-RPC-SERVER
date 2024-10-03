<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

use Closure;
use ReflectionFunction;
use ReflectionException;
use EugeneJenkins\JsonRpcServer\Exceptions\InvalidParamsException;

class MethodHandler implements HandleInterface, MethodHandlerInterface
{
    private mixed $response = [];

    /**
     * @param Closure $method
     * @param array<string, mixed>|array<int, array<string, mixed>> $payload
     */
    public function __construct(
        readonly private Closure $method,
        readonly private array   $payload
    )
    {
    }

    /**
     * @throws InvalidParamsException
     */
    public function handle(): static
    {
        $id = null;

        $params = $this->payload['params'] ?? [];

        if (array_key_exists('id', $this->payload)) {
            $id = $this->payload['id'];
        }

        try {
            $reflection = new ReflectionFunction($this->method);
            $functionParameters = $reflection->getParameters();

            if (count($functionParameters) !== count($params)) {
                throw new InvalidParamsException(id: $id);
            }

            if (empty($params)){
                $this->response = $reflection->invoke();
            }

            if ($this->isNonParameterized($params)) {
                $this->response = $reflection->invoke(...$params);
            } else {
                foreach ($functionParameters as $parameter) {
                    if (!array_key_exists($parameter->getName(), $params)) {
                        throw new InvalidParamsException(id: $id);
                    }
                }

                $this->response = $reflection->invokeArgs($params);
            }

            if (is_null($id)) {
                $this->response = null;
            }
        } catch (ReflectionException $exception) {
            throw new InvalidParamsException(id: $id);
        }

        return $this;
    }

    public function getResponse(): mixed
    {
        return $this->response;
    }

    private function isNonParameterized(array $parameters): bool
    {
        return is_numeric(implode('', array_keys($parameters)));
    }
}
