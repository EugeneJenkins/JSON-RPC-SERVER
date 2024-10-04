<?php

namespace EugeneJenkins\JsonRpcServer\Controllers;

use EugeneJenkins\JsonRpcServer\Utils\CallbackList;
use EugeneJenkins\JsonRpcServer\Requests\RpcRequest;
use EugeneJenkins\JsonRpcServer\Builders\RpcRequestBuilder;
use EugeneJenkins\JsonRpcServer\Validators\PayloadValidator;
use EugeneJenkins\JsonRpcServer\Exceptions\ParseErrorException;
use EugeneJenkins\JsonRpcServer\Handlers\PayloadHandlerInterface;

class RequestController
{
    /**
     * @var PayloadHandlerInterface[]
     */
    private array $handlers;

    /**
     * @var RpcRequest[]
     */
    private array $requests;

    public function __construct(readonly private CallbackList $callbackList)
    {
    }

    /**
     * @param PayloadHandlerInterface $handle
     * @return void
     */
    public function registerHandler(PayloadHandlerInterface $handle): void
    {
        $this->handlers[] = $handle;
    }

    /**
     * @param string $request
     * @return RpcRequest[]
     * @throws ParseErrorException
     */
    public function handleRequest(string $request): array
    {
        $handledRequest = [];

        /*
         * Calling all payload handlers for a get request
         */
        foreach ($this->handlers as $handler) {
            $handler->setPayload($request);
            $tmp = $handler->handle();

            if (!empty($tmp)) {
                $handledRequest = $tmp;
            }
        }

        return $this->process(
            (new PayloadValidator($handledRequest))->validate()
        );
    }

    /**
     * @param string[]|array<array<mixed>> $payload
     * @return RpcRequest[]
     */
    private function process(array $payload): array
    {
        $methods = $this->callbackList->getCallbackNames();

        //create simple request
        if (array_keys($payload) !== range(0, count($payload) - 1)) {
            $this->requests[] = (new RpcRequestBuilder($payload, $methods))->build();

            return $this->requests;
        }

        //create batch request
        /** @var string[] $item */
        foreach ($payload as $item) {
            $this->requests[] = (new RpcRequestBuilder($item, $methods))->build();
        }

        return $this->requests;
    }
}
