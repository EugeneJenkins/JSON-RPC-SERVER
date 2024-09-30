<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

use EugeneJenkins\JsonRpcServer\Exceptions\ParseErrorException;

/**
 * Handle
 */
class PayloadHandler implements HandleInterface
{
    /**
     * @var array<mixed>
     */
    private array $handledArray = [];

    public function __construct(readonly private mixed $payload)
    {
    }

    /**
     * Payload must be arrayed
     * @return static
     * @throws ParseErrorException
     */
    public function handle(): static
    {
        if (is_array($this->payload)) {
            $this->handledArray = $this->payload;

            return $this;
        }

        if (!is_string($this->payload)) {
            throw new ParseErrorException;
        }

        $payload = json_decode($this->payload, true);

        if (!is_array($payload)) {
            throw new ParseErrorException;
        }

        $this->handledArray = $payload;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getPayload(): array
    {
        return $this->handledArray;
    }
}
