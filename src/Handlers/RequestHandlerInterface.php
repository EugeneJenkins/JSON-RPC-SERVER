<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

use EugeneJenkins\JsonRpcServer\Requests\RpcRequest;

interface RequestHandlerInterface
{
    /**
     * @return array<RpcRequest>
     */
    public function getRequests(): array;
}
