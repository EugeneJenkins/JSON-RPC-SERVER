<?php

use EugeneJenkins\JsonRpcServer\Server;

require './vendor/autoload.php';


$subtract = function ($minuend, $subtrahend) {
    return [
        'minuend' => $minuend,
        'subtrahend' => $subtrahend,
    ];
};

try {
    $server = new Server;
    $server->register('subtract', $subtract);
    $server->execute()->show();
} catch (Throwable $exception) {
    echo $exception->getMessage();
}
