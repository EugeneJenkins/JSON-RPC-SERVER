<?php

namespace EugeneJenkins\JsonRpcServer\Response;

class RpcResponse
{
    private const RPC_VERSION = '2.0';

    /**
     * @param mixed $result
     * @param string|int|null $id
     * @return array<mixed>
     */
    public function success(mixed $result, string|int|null $id): array
    {
        return $this->format([
            'result' => $result
        ], $id);
    }

    /**
     * @param int $code
     * @param string $message
     * @param string|int|null $id
     * @return array
     */
    public function error(int $code, string $message, string|int|null $id = null): array
    {
        return $this->format([
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ], $id);
    }

    /**
     * @param array $data
     * @param string|int|null $id
     * @return array<mixed>
     */
    private function format(array $data, string|int|null $id): array
    {
        return [
            'jsonrpc' => self::RPC_VERSION,
            ...$data,
            'id' => $id
        ];
    }
}
