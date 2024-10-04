<?php

namespace EugeneJenkins\JsonRpcServer\Validators;

interface ValidatorInterface
{
    public function validate(mixed $data): array;
}
