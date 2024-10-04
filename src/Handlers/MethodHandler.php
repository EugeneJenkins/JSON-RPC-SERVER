<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

use Closure;
use EugeneJenkins\JsonRpcServer\Requests\RpcRequest;
use ReflectionFunction;
use ReflectionException;
use EugeneJenkins\JsonRpcServer\Exceptions\InvalidParamsException;

class MethodHandler implements HandleInterface
{

    /**
     * @param Closure $method
     * @param RpcRequest $request
     */
    public function __construct(
        readonly private Closure    $method,
        readonly private RpcRequest $request
    )
    {
    }

    /**
     * @throws InvalidParamsException|ReflectionException
     */
    public function handle(): mixed
    {
        $id = $this->request->getId();
        $params = $this->request->getParams();

        $reflection = new ReflectionFunction($this->method);

        // The number of request parameters must match the number of function arguments
        if (count($reflection->getParameters()) !== count($params)) {
            throw new InvalidParamsException(id: $id);
        }

        $responses = $this->isNonParameterized($params)
            ? $reflection->invoke(...$params)
            : $reflection->invokeArgs($params);

        // This request serves as notification
        if (is_null($id)) {
            $responses = [];
        }

        return $responses;
    }

    /**
     * @param string[]|int[] $parameters
     * @return bool
     */
    private function isNonParameterized(array $parameters): bool
    {
        return is_numeric(implode('', array_keys($parameters)));
    }
}
