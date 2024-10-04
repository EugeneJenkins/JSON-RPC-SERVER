<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

interface PayloadHandlerInterface extends HandleInterface
{
    public function setPayload(mixed $payload): static;
}
