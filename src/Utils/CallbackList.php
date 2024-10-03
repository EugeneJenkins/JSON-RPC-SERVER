<?php

namespace EugeneJenkins\JsonRpcServer\Utils;

use Closure;

class CallbackList
{
    /**
     * @var Closure[]
     */
    private array $list = [];

    public function add(string $name, Closure $closure): void
    {
        $this->list[$name] = $closure;
    }

    public function get(string $name): ?Closure
    {
        return $this->list[$name] ?? null;
    }

    public function remove(string $name): void
    {
        unset($this->list[$name]);
    }

    /**
     * @return string[]
     */
    public function getCallbackNames(): array
    {
        return array_keys($this->list);
    }
}
