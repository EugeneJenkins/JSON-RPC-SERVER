<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

interface HandleInterface
{
    public function handle(): static;
}
