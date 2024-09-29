<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

interface RequestHandlerInterface
{
    public function getRequests(): array;
}
