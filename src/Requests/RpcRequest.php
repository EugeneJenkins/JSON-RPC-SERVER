<?php

namespace EugeneJenkins\JsonRpcServer\Requests;

class RpcRequest
{
    /**
     * @param string $method
     * @param string[]|int[] $params
     * @param string|int|null $id
     * @param string[] $error
     */
    public function __construct(
        readonly private string          $method = '',
        readonly private array           $params = [],
        readonly private string|int|null $id = null,
        readonly private array           $error = []
    )
    {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return int[]|string[]
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    /**
     * @return
     */
    public function getError(): array
    {
        return $this->error;
    }
}
