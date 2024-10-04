<?php

namespace EugeneJenkins\JsonRpcServer\Validators;

use EugeneJenkins\JsonRpcServer\Exceptions\ParseErrorException;

class PayloadValidator implements ValidatorInterface
{
    public function __construct(readonly private mixed $data)
    {
    }

    /**
     * @return array<mixed>
     * @throws ParseErrorException
     */
    public function validate(): array
    {
        if (is_array($this->data)) {
            return $this->data;
        }

        if (!is_string($this->data)) {
            throw new ParseErrorException;
        }

        $payload = json_decode($this->data, true);

        if (!is_array($payload)) {
            throw new ParseErrorException;
        }

        return $payload;
    }
}
