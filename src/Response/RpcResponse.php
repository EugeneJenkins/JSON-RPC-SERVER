<?php

namespace EugeneJenkins\JsonRpcServer\Response;

use InvalidArgumentException;

class RpcResponse
{
    private const RPC_VERSION = '2.0';

    /**
     * @param mixed $result
     * @param string|int $id
     * @return array
     */
    public function success(mixed $result, string|int $id): array
    {
        return $this->format([
            'result' => $result
        ], $id);
    }

    public function error(int $code, string $message, string|int|null $id = null): array
    {
        return $this->format([
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ], $id);
    }

    private function format(array $data, string|int|null $id): array
    {
        return [
            'jsonrpc' => self::RPC_VERSION,
            ...$data,
            'id' => $id
        ];
    }
}
