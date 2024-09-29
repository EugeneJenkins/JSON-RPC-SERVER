<?php

namespace EugeneJenkins\JsonRpcServer\Requests;

class RpcRequest
{
    private string $method;
    private array $params;
    private string|int|null $id;

    public function __construct(
        readonly private array  $payload = [],
        readonly private array $error = []
    )
    {
        $this->method = $this->payload['method'] ?? '';
        $this->params = $this->payload['params'] ?? [];
        $this->id = $this->payload['id'] ?? null;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function getError(): array
    {
        return $this->error;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }
}
