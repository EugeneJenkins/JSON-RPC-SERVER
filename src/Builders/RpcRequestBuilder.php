<?php

namespace EugeneJenkins\JsonRpcServer\Builders;

use EugeneJenkins\JsonRpcServer\Requests\RpcRequest;
use EugeneJenkins\JsonRpcServer\Exceptions\ServerException;
use EugeneJenkins\JsonRpcServer\Validators\RpcRequestValidator;

class RpcRequestBuilder implements BuilderInterface
{
    /**
     * @param mixed $payload
     * @param string[] $existMethods
     */
    public function __construct(
        readonly private mixed $payload,
        readonly private array $existMethods,
    )
    {
    }

    public function build(): RpcRequest
    {
        try {
            (new RpcRequestValidator($this->payload, $this->existMethods))->validate();
        } catch (ServerException $e) {
            return $this->createBadRequest($e);
        }

        return $this->createSuccessRequest();
    }

    private function createBadRequest(ServerException $exception): RpcRequest
    {
        return new RpcRequest(error: [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'id' => $exception->getId()
        ]);
    }

    private function createSuccessRequest(): RpcRequest
    {
        $data = [
            'method' => $this->payload['method']
        ];

        !empty($this->payload['params']) && $data['params'] = $this->payload['params'];
        isset($this->payload['id']) && $data['id'] = $this->payload['id'];

        return new RpcRequest(...$data);
    }
}
