<?php

use EugeneJenkins\JsonRpcServer\Server;

require './vendor/autoload.php';


$subtract = function ($minuend, $subtrahend) {
    return [
        'minuend' => $minuend,
        'subtrahend' => $subtrahend,
    ];
};

$update = fn(int $a, int $b, int $c, int $d, int $e) => 1;
$notify_sum = fn(int $a, int $b, int $c) => 1;
$notify_hello = fn(int $a) => 1;
$sum = fn(int $a, int $b) => $a + $b;
$get_data = fn() => ['hello', 5];

try {
    $server = new Server;
    $server->register('subtract', $subtract);
    $server->register('update', $update);
    $server->register('notify_sum', $notify_sum);
    $server->register('notify_hello', $notify_hello);
    $server->register('sum', $sum);
    $server->register('get_data', $get_data);
    $server->execute()->show();
} catch (Throwable $exception) {
    echo $exception->getMessage();
}
