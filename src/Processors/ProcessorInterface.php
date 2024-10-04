<?php

namespace EugeneJenkins\JsonRpcServer\Processors;

interface ProcessorInterface
{
    public function process(): static;

    public function getResponse();
}
