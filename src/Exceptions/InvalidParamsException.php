<?php

namespace EugeneJenkins\JsonRpcServer\Exceptions;

class InvalidParamsException extends ServerException
{
    /**
     * This error follow RFC exception
     */
    public static int $ERROR_CODE = -32602;
    protected static string $ERROR_MASSAGE = 'Invalid method parameter(s).';
}
