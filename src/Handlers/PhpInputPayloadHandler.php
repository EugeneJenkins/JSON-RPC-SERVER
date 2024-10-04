<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

class PhpInputPayloadHandler implements HandleInterface, PayloadHandlerInterface
{

    /**
     * @return string
     */
    public function handle(): string
    {
        $content = file_get_contents('php://input');

        if (empty($content)) {
            return '';
        }

        return $content;
    }

    public function setPayload(mixed $payload): static
    {
        return $this;
    }
}
