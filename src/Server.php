<?php

namespace EugeneJenkins\JsonRpcServer;

use Closure;
use EugeneJenkins\JsonRpcServer\Response\RpcResponse;
use EugeneJenkins\JsonRpcServer\Handlers\MethodHandler;
use EugeneJenkins\JsonRpcServer\Handlers\RequestHandler;
use EugeneJenkins\JsonRpcServer\Response\ServerResponse;
use EugeneJenkins\JsonRpcServer\Exceptions\ServerException;
use EugeneJenkins\JsonRpcServer\Exceptions\ParseErrorException;

class Server
{
    /**
     * @var array<string, Closure>
     */
    private array $callbackList = [];

    /**
     * @var RpcResponse
     */
    private RpcResponse $response;

    public function __construct()
    {
        $this->response = new RpcResponse;
    }

    public function register(string $name, Closure $callback): void
    {
        $this->callbackList[$name] = $callback;
    }

    public function execute(): ServerResponse
    {
        $responses = [];

        //todo Refactor batch requests
        try {
            $requests = (new RequestHandler($this->getPayload(), array_keys($this->callbackList)))
                ->handle()
                ->getRequests();

            foreach ($requests as $request) {
                $method = $this->callbackList[$request->getMethod()];

                if (!empty($request->getError())) {
                    $responses[] = $this->response->error(...$request->getError());
                }

                $response = (new MethodHandler($method, $request->getPayload()))
                    ->handle()
                    ->getResponse();

                //Notification method called
                if (!$response) {
                    $responses[] = [];
                    continue;
                }

                $responses[] = $this->response->success($response, $request->getId());
            }
        } catch (ServerException $exception) {
            $responses[] = $this->response->error(
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getId()
            );
        }

        return new ServerResponse($responses);
    }


    /**
     * @throws ParseErrorException
     * @return array<string, mixed>|array<int, array<string, mixed>>
     */
    private function getPayload(): array
    {
        $json = file_get_contents('php://input');

        if (!$json) {
            throw new ParseErrorException;
        }

        $payload = json_decode($json, true);

        if (!is_array($payload)) {
            throw new ParseErrorException;
        }

        return $payload;
    }
}
