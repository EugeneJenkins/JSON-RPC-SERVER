<?php

namespace EugeneJenkins\JsonRpcServer;

use Closure;
use EugeneJenkins\JsonRpcServer\Utils\CallbackList;
use EugeneJenkins\JsonRpcServer\Requests\RpcRequest;
use EugeneJenkins\JsonRpcServer\Response\RpcResponse;
use EugeneJenkins\JsonRpcServer\Handlers\MethodHandler;
use EugeneJenkins\JsonRpcServer\Handlers\PayloadHandler;
use EugeneJenkins\JsonRpcServer\Handlers\RequestHandler;
use EugeneJenkins\JsonRpcServer\Response\ServerResponse;
use EugeneJenkins\JsonRpcServer\Exceptions\ServerException;
use EugeneJenkins\JsonRpcServer\Exceptions\ParseErrorException;
use Throwable;

class Server
{
    private CallbackList $callbackList;

    /**
     * @var RpcResponse
     */
    private RpcResponse $response;

    public function __construct(readonly private string $payload = '')
    {
        $this->response = new RpcResponse;
        $this->callbackList = new CallbackList;
    }

    public function register(string $name, Closure $callback): void
    {
        $this->callbackList->add($name, $callback);
    }

    public function execute(): ServerResponse
    {
        try {
            $payload = (new PayloadHandler($this->getPayload()))
                ->handle()
                ->getPayload();
        } catch (ParseErrorException $exception) {
            return new ServerResponse($this->createErrorResponse($exception));
        }

        $requests = (new RequestHandler($payload, $this->callbackList->getCallbackNames()))
            ->handle()
            ->getRequests();

        $responses = array_map(fn($request) => $this->processRequest($request), $requests);

        return new ServerResponse($responses);
    }

    /**
     * @return bool|string
     */
    public function getPayload(): bool|string
    {
        if (!empty($this->payload)) {
            return $this->payload;
        }

        return file_get_contents('php://input');
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
                ->handle()
                ->getResponse();

            //Notification method called
            if (!$response) {
                return [];
            }

            return $this->response->success($response, $request->getId());
        } catch (ServerException $exception) {
            return $this->createErrorResponse($exception);
        }catch (Throwable $exception){
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
