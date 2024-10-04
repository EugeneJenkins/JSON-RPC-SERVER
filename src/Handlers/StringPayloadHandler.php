<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

class StringPayloadHandler implements PayloadHandlerInterface
{
    private mixed $payload;

    public function handle(): string
    {
        if (empty($this->payload) || !is_string($this->payload)) {
            return '';
        }

        return $this->payload;
    }

    public function setPayload(mixed $payload): static
    {
        $this->payload = $payload;

        return $this;
    }
}
