<?php

namespace EugeneJenkins\JsonRpcServer\Tests\Helper;

class JsonRpcTestHelper
{
    private const VERSION = '2.0';

    /**
     * @param string $method
     * @param array $params
     * @param string|int|null $id
     * @return array{jsonrpc: string, method: string, params: array, id: int|null|string}
     */
    public function createRequest(string $method, array $params = [], string|int|null $id = null): array
    {
        return [
            'jsonrpc' => self::VERSION,
            'method' => $method,
            'params' => $params,
            'id' => $id,
        ];
    }

    /**
     * @param string $method
     * @param array $params
     * @param string|int|null $id
     * @return string
     */
    public function createRequestJsonEncoded(string $method, array $params = [], string|int|null $id = null): string
    {
        $response = json_encode($this->createRequest($method, $params, $id));

        if (!$response) {
            return '{}';
        }

        return $response;
    }

    /**
     * @param array|string|int $result
     * @param string|int|null $id
     * @return array{jsonrpc: string, result: array|int|string, id: int|null|string}
     */
    public function createResponse(array|string|int $result, string|int|null $id = null): array
    {
        return [
            'jsonrpc' => self::VERSION,
            'result' => $result,
            'id' => $id,
        ];
    }

    /**
     * @param int $code
     * @param string $message
     * @param string|int|null $id
     * @return array{jsonrpc: string, error: array{code: int, message: string}, id: int|null|string}
     */
    public function createError(int $code, string $message, string|int|null $id = null): array
    {
        return [
            'jsonrpc' => self::VERSION,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
            'id' => $id,
        ];
    }
}
