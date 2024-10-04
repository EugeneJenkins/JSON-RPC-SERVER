<?php

namespace EugeneJenkins\JsonRpcServer\Processors;

use ReflectionException;
use EugeneJenkins\JsonRpcServer\Utils\CallbackList;
use EugeneJenkins\JsonRpcServer\Requests\RpcRequest;
use EugeneJenkins\JsonRpcServer\Response\RpcResponse;
use EugeneJenkins\JsonRpcServer\Handlers\MethodHandler;
use EugeneJenkins\JsonRpcServer\Exceptions\ServerException;
use EugeneJenkins\JsonRpcServer\Exceptions\MethodNotFoundException;

class RequestProcessor implements ProcessorInterface
{
    /**
     * @param RpcRequest[] $requests
     */
    public function __construct(
        readonly array                $requests,
        readonly private CallbackList $callbackList,
        readonly private RpcResponse  $response
    )
    {
    }

    public function process(): array
    {
        return array_map(fn($request) => $this->stepByStepProcessing($request), $this->requests);
    }

    /**
     * @param RpcRequest $request
     * @return array<mixed>
     * @throws ReflectionException
     */
    private function stepByStepProcessing(RpcRequest $request): array
    {
        try {
            $method = $this->callbackList->get($request->getMethod());

            if (!empty($request->getError())) {
                return $this->response->error(...$request->getError());
            }

            if (!$method) {
                throw new  MethodNotFoundException(id: $request->getId());
            }

            $response = (new MethodHandler($method, $request))->handle();

            //Notification method called
            if (!$response) {
                return [];
            }

            return $this->response->success($response, $request->getId());
        } catch (ServerException $exception) {
            return $this->response->error(
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getId()
            );
        }
    }
}
