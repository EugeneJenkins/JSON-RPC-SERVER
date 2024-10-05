<?php

namespace EugeneJenkins\JsonRpcServer;

use Closure;
use Throwable;
use ReflectionException;
use EugeneJenkins\JsonRpcServer\Utils\CallbackList;
use EugeneJenkins\JsonRpcServer\Response\RpcResponse;
use EugeneJenkins\JsonRpcServer\Response\ServerResponse;
use EugeneJenkins\JsonRpcServer\Utils\ClassMethodRegister;
use EugeneJenkins\JsonRpcServer\Handlers\ExceptionHandler;
use EugeneJenkins\JsonRpcServer\Processors\RequestProcessor;
use EugeneJenkins\JsonRpcServer\Handlers\StringPayloadHandler;
use EugeneJenkins\JsonRpcServer\Controllers\RequestController;
use EugeneJenkins\JsonRpcServer\Handlers\PhpInputPayloadHandler;

class Server
{
    private CallbackList $callbackList;
    private ClassMethodRegister $classRegister;
    private RpcResponse $response;
    private ExceptionHandler $exceptionHandler;
    private RequestController $controller;

    public function __construct(readonly private string $payload = '')
    {
        $this->response = new RpcResponse;
        $this->callbackList = new CallbackList;
        $this->exceptionHandler = new ExceptionHandler($this->response);
        $this->classRegister = new ClassMethodRegister($this->callbackList);
    }

    public function register(string $name, Closure $callback): void
    {
        $this->callbackList->add($name, $callback);
    }

    public function registerClass(string $classNamespace): void
    {
        $this->classRegister->add($classNamespace);
    }

    public function execute(): ServerResponse
    {
        try {
            //boot all settings
            $this->boot();

            $requests = $this->controller->handleRequest($this->payload);

            $processor = new RequestProcessor($requests, $this->callbackList, $this->response);
            $responses = $processor->process();
        } catch (Throwable $exception) {
            $responses = $this->exceptionHandler->setException($exception)->handle();
        }

        return new ServerResponse($responses);
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    private function boot(): void
    {
        //Registering Payload Receiving Methods
        $this->controller = new RequestController($this->callbackList);
        $this->controller->registerHandler(new StringPayloadHandler);
        $this->controller->registerHandler(new PhpInputPayloadHandler);

        $this->classRegister->register();
    }
}
