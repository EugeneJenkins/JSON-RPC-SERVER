<?php

namespace EugeneJenkins\JsonRpcServer\Exceptions;

class InvalidRequestException extends ServerException
{
    /**
     * This error follow RFC exception
     */
    public static int $ERROR_CODE = -32600;
    protected static string $ERROR_MASSAGE = 'The JSON sent is not a valid Request object.';
}
