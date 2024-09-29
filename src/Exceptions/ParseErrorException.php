<?php

namespace EugeneJenkins\JsonRpcServer\Exceptions;

class ParseErrorException extends ServerException
{
    /**
     * This error follow RFC exception
     */
    public static int $ERROR_CODE = -32700;
    protected static string $ERROR_MASSAGE = 'Parse error Invalid JSON was received by the server';
}
