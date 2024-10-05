<?php


use EugeneJenkins\JsonRpcServer\Server;

require './vendor/autoload.php';

$server = new Server;
$server->register('subtract', fn($minuend, $subtrahend) => $minuend - $subtrahend);
$server->register('add', fn($a, $b) => $a + $b);
$server->registerClass(Calculator::class);
$response = $server->execute();
$response->show();
