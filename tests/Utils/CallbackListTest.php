<?php

namespace Tests\Utils;

use Closure;
use PHPUnit\Framework\TestCase;
use EugeneJenkins\JsonRpcServer\Utils\CallbackList;

class CallbackListTest extends TestCase
{
    private CallbackList $callbackList;

    public function setUp(): void
    {
        $this->callbackList = new CallbackList;
    }

    public function testAddNewCallback(): void
    {
        ['name' => $name, 'closure' => $closure] = $this->createSumClosure();

        $this->callbackList->add($name, $closure);
        $this->assertContains($name, $this->callbackList->getCallbackNames());
    }

    public function testGetCallback(): void
    {
        ['name' => $name, 'closure' => $closure] = $this->createSumClosure();
        $this->callbackList->add($name, $closure);

        $firstNumber = 10;
        $secondNumber = 20;

        // Directly invoke the closure to calculate the sum
        $createClosureSum = $closure($firstNumber, $secondNumber);

        $retrievedCallback = $this->callbackList->get($name);

        $this->assertIsCallable($retrievedCallback);
        $retrievedClosureSum = $retrievedCallback($firstNumber, $secondNumber);

        $this->assertSame($closure, $retrievedCallback);

        //Same response
        $this->assertSame($createClosureSum, $retrievedClosureSum);
    }

    public function testRemoveCallback(): void
    {
        ['name' => $name, 'closure' => $closure] = $this->createSumClosure();
        $this->callbackList->add($name, $closure);

        $this->callbackList->remove($name);
        $retrievedCallback = $this->callbackList->get($name);

        $this->assertNull($retrievedCallback);
    }

    public function testGetCallbackNames(): void
    {
        $firstClosureName = 'sum';
        $secondClosureName = 'minus';

        $this->callbackList->add($firstClosureName, fn() => 1);
        $this->callbackList->add($secondClosureName, fn() => 2);

        $callbackNames = $this->callbackList->getCallbackNames();

        $this->assertEqualsCanonicalizing([$firstClosureName, $secondClosureName], $callbackNames);
    }

    /**
     * @return array{name: string, closure: Closure}
     */
    private function createSumClosure(): array
    {
        $sum = fn(int $a, int $b) => $a + $b;

        return [
            'name' => 'sum',
            'closure' => $sum
        ];
    }
}
