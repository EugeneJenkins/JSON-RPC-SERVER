<?php

namespace EugeneJenkins\JsonRpcServer\Controllers;

use EugeneJenkins\JsonRpcServer\Exceptions\ParseErrorException;
use EugeneJenkins\JsonRpcServer\Handlers\HandleInterface;
use EugeneJenkins\JsonRpcServer\Validators\PayloadValidator;
use EugeneJenkins\JsonRpcServer\Handlers\PayloadHandlerInterface;

class RequestController
{
    /**
     * @var HandleInterface|PayloadHandlerInterface[]
     */
    private array $handlers;

    public function __construct()
    {
    }

    public function registerHandler(HandleInterface $handle): void
    {
        $this->handlers[] = $handle;
    }

    /**
     * @param string|array $request
     * @return array<mixed>
     * @throws ParseErrorException
     */
    public function handleRequest(string|array $request): array
    {
        $handledRequest = [];

        foreach ($this->handlers as $handler) {
            $handler->setPayload($request);
            $tmp = $handler->handle();

            if (!empty($tmp)) {
                $handledRequest = $tmp;
            }
        }

        return (new PayloadValidator)->validate($handledRequest);
    }
}
