<?php

namespace EugeneJenkins\JsonRpcServer\Validators;

use EugeneJenkins\JsonRpcServer\Exceptions\InvalidRequestException;
use EugeneJenkins\JsonRpcServer\Exceptions\MethodNotFoundException;

class RpcRequestValidator implements ValidatorInterface
{
    /**
     * @param mixed $data
     * @param array<mixed> $existMethods
     */
    public function __construct(
        readonly private mixed $data,
        readonly private array $existMethods,
    )
    {
    }

    /**
     * @throws MethodNotFoundException
     * @throws InvalidRequestException
     */
    public function validate(): void
    {
        if (!is_array($this->data)) {
            throw new InvalidRequestException;
        }

        if (!isset($this->data['method']) || !is_string($this->data['method'])) {
            throw new InvalidRequestException;
        }

        if (!in_array($this->data['method'], $this->existMethods)) {
            throw new MethodNotFoundException(id: $this->data['id'] ?? null);
        }
    }
}
