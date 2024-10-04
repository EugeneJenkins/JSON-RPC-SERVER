<?php

namespace EugeneJenkins\JsonRpcServer\Processors;

interface ProcessorInterface
{
    /**
     * @return array<mixed>
     */
    public function process(): array;
}
