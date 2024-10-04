<?php

namespace EugeneJenkins\JsonRpcServer\Validators;

use EugeneJenkins\JsonRpcServer\Exceptions\ParseErrorException;

class PayloadValidator implements ValidatorInterface
{
    /**
     * @param mixed $data
     * @return array<mixed>
     * @throws ParseErrorException
     */
    public function validate(mixed $data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (!is_string($data)) {
            throw new ParseErrorException;
        }

        $payload = json_decode($data, true);

        if (!is_array($payload)) {
            throw new ParseErrorException;
        }

        return $payload;
    }
}
