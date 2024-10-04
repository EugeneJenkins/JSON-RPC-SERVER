<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

interface PayloadHandlerInterface
{
    public function setPayload(mixed $payload): static;
}
