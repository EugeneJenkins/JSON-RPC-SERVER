<?php

namespace Tests\Fixtures;

use Closure;

class TestFunctionRepository
{
    /**
     * @var Closure[]
     */
    private array $functions = [];

    public function __construct()
    {
        $this->initFunctions();
    }

    public function getFunction(string $name): Closure
    {
        return $this->functions[$name];
    }

    private function initFunctions(): void
    {
        $this->functions['subtract'] = fn(int $minuend, int $subtrahend) => $minuend - $subtrahend;
        $this->functions['sum'] = fn(int $a, int $b) => $a + $b;
        $this->functions['multiply'] = fn(int $a, int $b) => $a * $b;
        $this->functions['update'] = fn(int $a, int $b, int $c, int $d, int $e) => 1;
        $this->functions['notify_sum'] = fn(int $a, int $b, int $c) => 1;
        $this->functions['notify_hello'] = fn(int $a) => 1;
        $this->functions['get_data'] = fn() => ['hello', 5];
    }
}
