<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

use EugeneJenkins\JsonRpcServer\Exceptions\MethodNotFoundException;
use EugeneJenkins\JsonRpcServer\Exceptions\ServerException;
use EugeneJenkins\JsonRpcServer\Requests\RpcRequest;
use EugeneJenkins\JsonRpcServer\Exceptions\InvalidRequestException;

class RequestHandler implements HandleInterface, RequestHandlerInterface
{
    /**
     * @var array<int, RpcRequest>
     */
    private array $requests;

    /**
     * @param array<string, mixed>|array<int, array<string, array>> $payload
     * @param array<int, string> $existMethods
     */
    public function __construct(
        readonly private array $payload,
        readonly private array $existMethods
    )
    {
    }

    /**
     * @return array<RpcRequest>
     */
    public function getRequests(): array
    {
        return $this->requests;
    }

    public function handle(): static
    {
        try {
            if (array_keys($this->payload) !== range(0, count($this->payload) - 1)) {
                $this->requests[] = $this->createRequest($this->payload);

                return $this;
            }

            foreach ($this->payload as $item) {
                $this->requests[] = $this->createRequest($item);
            }
        } catch (ServerException $exception) {
            $this->requests[] = new RpcRequest(error: [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'id' => $exception->getId()
            ]);;
        }

        return $this;
    }

    /**
     * @throws InvalidRequestException|MethodNotFoundException
     */
    private function createRequest(array $payload): RpcRequest
    {
        $this->validateRequest($payload);

        return new RpcRequest($payload);
    }

    /**
     * @throws InvalidRequestException|MethodNotFoundException
     */
    private function validateRequest(array $payload): void
    {
        if (!isset($payload['method']) && !is_string($payload['method'])) {
            throw new InvalidRequestException;
        }

        if (!in_array($payload['method'], $this->existMethods)) {
            throw new MethodNotFoundException(id: $payload['id'] ?? null);
        }
    }
}
