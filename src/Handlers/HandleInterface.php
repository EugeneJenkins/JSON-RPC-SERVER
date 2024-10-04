<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

interface HandleInterface
{
    /**
     * @return mixed
     */
    public function handle();
}
