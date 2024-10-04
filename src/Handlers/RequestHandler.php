<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

use EugeneJenkins\JsonRpcServer\Requests\RpcRequest;
use EugeneJenkins\JsonRpcServer\Exceptions\ServerException;
use EugeneJenkins\JsonRpcServer\Exceptions\MethodNotFoundException;
use EugeneJenkins\JsonRpcServer\Exceptions\InvalidRequestException;

class RequestHandler implements HandleInterface
{
    /**
     * @var RpcRequest[]
     */
    private array $requests;

    /**
     * @param array<mixed> $payload
     * @param array<int, string> $existMethods
     */
    public function __construct(
        readonly private array $payload,
        readonly private array $existMethods
    )
    {
    }

    /**
     * @return RpcRequest[]
     */
    public function handle(): array
    {
        if (array_keys($this->payload) !== range(0, count($this->payload) - 1)) {
            $this->requests[] = $this->createRequest($this->payload);

            return $this->requests;
        }

        //handle batch request
        foreach ($this->payload as $item) {
            $this->requests[] = $this->createRequest($item);
        }

        return $this->requests;
    }

    /**
     * @param mixed $payload
     * @return RpcRequest
     */
    private function createRequest(mixed $payload): RpcRequest
    {
        try {
            $this->validateRequest($payload);
        } catch (ServerException $exception) {
            return new RpcRequest(error: [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'id' => $exception->getId()
            ]);
        }

        /** @var array<mixed> $payload */
        return new RpcRequest($payload);
    }

    /**
     * @param mixed $payload
     * @throws InvalidRequestException
     * @throws MethodNotFoundException
     */
    private function validateRequest(mixed $payload): void
    {
        if (!is_array($payload)) {
            throw new InvalidRequestException;
        }

        if (!isset($payload['method']) || !is_string($payload['method'])) {
            throw new InvalidRequestException;
        }

        if (!in_array($payload['method'], $this->existMethods)) {
            throw new MethodNotFoundException(id: $payload['id'] ?? null);
        }
    }
}
