<?php

namespace EugeneJenkins\JsonRpcServer\Exceptions;

use Exception;
use Throwable;

class ServerException extends Exception
{
    public static int $ERROR_CODE = -32000;
    public static string $ERROR_MASSAGE = 'Server error';

    /**
     * Errors follows the JSON-RPC 2.0 specification (RFC https://www.jsonrpc.org/specification#error_object).
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param string|int|null $id
     */
    public function __construct(
        string                  $message = '',
        int                     $code = 0,
        ?Throwable              $previous = null,
        private string|int|null $id = null)
    {
        empty($message) && $message = static::$ERROR_MASSAGE;
        $code === 0 && $code = static::$ERROR_CODE;

        parent::__construct($message, $code, $previous);
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }
}
