<?php

namespace EugeneJenkins\JsonRpcServer\Handlers;

use Throwable;
use ReflectionException;
use EugeneJenkins\JsonRpcServer\Response\RpcResponse;
use EugeneJenkins\JsonRpcServer\Exceptions\ServerException;
use EugeneJenkins\JsonRpcServer\Exceptions\ParseErrorException;
use EugeneJenkins\JsonRpcServer\Exceptions\InvalidParamsException;

class ExceptionHandler implements HandleInterface
{
    private Throwable $throwable;

    public function __construct(
        readonly private RpcResponse $response
    )
    {
    }

    /**
     * Log info
     * @return void
     */
    private function report(): void
    {

    }

    /**
     * Client readable response
     * @param Throwable $throwable
     * @return array<mixed>
     */
    private function render(Throwable $throwable): array
    {
        if ($throwable instanceof ParseErrorException) {
            return $this->response->error(
                $throwable->getCode(),
                $throwable->getMessage()
            );
        }

        if ($throwable instanceof ReflectionException) {
            return $this->response->error(
                InvalidParamsException::$ERROR_CODE,
                InvalidParamsException::$ERROR_MASSAGE
            );
        }

        return $this->response->error(
            ServerException::$ERROR_CODE,
            ServerException::$ERROR_MASSAGE
        );
    }

    /**
     * @return array<mixed>
     */
    public function handle(): array
    {
        $this->report();

        return $this->render($this->throwable);
    }

    public function setException(Throwable $throwable): static
    {
        $this->throwable = $throwable;

        return $this;
    }
}
