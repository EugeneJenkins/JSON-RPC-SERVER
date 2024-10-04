<?php

namespace EugeneJenkins\JsonRpcServer;

use Closure;
use Throwable;
use EugeneJenkins\JsonRpcServer\Utils\CallbackList;
use EugeneJenkins\JsonRpcServer\Requests\RpcRequest;
use EugeneJenkins\JsonRpcServer\Response\RpcResponse;
use EugeneJenkins\JsonRpcServer\Handlers\MethodHandler;
use EugeneJenkins\JsonRpcServer\Response\ServerResponse;
use EugeneJenkins\JsonRpcServer\Handlers\RequestHandler;
use EugeneJenkins\JsonRpcServer\Handlers\ExceptionHandler;
use EugeneJenkins\JsonRpcServer\Exceptions\ServerException;
use EugeneJenkins\JsonRpcServer\Handlers\StringPayloadHandler;
use EugeneJenkins\JsonRpcServer\Controllers\RequestController;
use EugeneJenkins\JsonRpcServer\Handlers\PhpInputPayloadHandler;

class Server
{
    private CallbackList $callbackList;

    /**
     * @var RpcResponse
     */
    private RpcResponse $response;
    private RequestController $controller;
    private ExceptionHandler $exceptionHandler;

    public function __construct(readonly private string $payload = '')
    {
        $this->response = new RpcResponse;
        $this->callbackList = new CallbackList;
        $this->controller = new RequestController;
        $this->exceptionHandler = new ExceptionHandler($this->response);

        $this->controller->registerHandler(new StringPayloadHandler);
        $this->controller->registerHandler(new PhpInputPayloadHandler);
    }

    public function register(string $name, Closure $callback): void
    {
        $this->callbackList->add($name, $callback);
    }

    public function execute(): ServerResponse
    {
        try {
            $payload = $this->controller->handleRequest($this->payload);

            $requests = (new RequestHandler($payload, $this->callbackList->getCallbackNames()))
                ->handle();

            $responses = array_map(fn($request) => $this->processRequest($request), $requests);
        } catch (Throwable $exception) {
            $responses = $this->exceptionHandler->setException($exception)->handle();
        }

        return new ServerResponse($responses);
    }

    /**
     * @param RpcRequest $request
     * @return array|mixed[]
     */
    private function processRequest(RpcRequest $request): array
    {
        try {
            $method = $this->callbackList->get($request->getMethod());

            if (!empty($request->getError())) {
                return $this->response->error(...$request->getError());
            }

            $response = (new MethodHandler($method, $request->getPayload()))
                ->handle();

            //Notification method called
            if (!$response) {
                return [];
            }

            return $this->response->success($response, $request->getId());
        } catch (ServerException $exception) {
            return $this->createErrorResponse($exception);
        } catch (Throwable $exception) {
            return [];
        }
    }

    /**
     * @param ServerException $exception
     * @return array<mixed>
     */
    private function createErrorResponse(ServerException $exception): array
    {
        return $this->response->error(
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getId()
        );
    }
}
