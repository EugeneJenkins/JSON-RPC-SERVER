<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

interface MethodHandlerInterface
{
    public function getResponse(): mixed;
}
