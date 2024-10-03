<?php

namespace EugeneJenkins\JsonRpcServer\Exceptions;

class MethodNotFoundException extends ServerException
{
    /**
     * This error follow RFC exception
     */
    public static int $ERROR_CODE = -32601;
    public static string $ERROR_MASSAGE = 'The method does not exist  / is not available.';
}
