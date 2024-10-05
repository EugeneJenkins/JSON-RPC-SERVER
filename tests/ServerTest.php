<?php

namespace EugeneJenkins\JsonRpcServer\Tests;

use PHPUnit\Framework\TestCase;
use EugeneJenkins\JsonRpcServer\Server;
use EugeneJenkins\JsonRpcServer\Tests\Helper\JsonRpcTestHelper;
use EugeneJenkins\JsonRpcServer\Exceptions\ParseErrorException;
use EugeneJenkins\JsonRpcServer\Exceptions\InvalidRequestException;
use EugeneJenkins\JsonRpcServer\Exceptions\MethodNotFoundException;
use EugeneJenkins\JsonRpcServer\Tests\Fixtures\TestFunctionRepository;

class ServerTest extends TestCase
{
    private JsonRpcTestHelper $jsonRpcHelper;
    private TestFunctionRepository $functionsFixtures;

    public function setUp(): void
    {
        $this->jsonRpcHelper = new JsonRpcTestHelper;
        $this->functionsFixtures = new TestFunctionRepository;
    }

    public function testNonParameterizedRequest(): void
    {
        $methodName = 'subtract';
        $request = $this->jsonRpcHelper->createRequestJsonEncoded($methodName, [42, 23], 1);
        $successResponse = $this->jsonRpcHelper->createResponse(19, 1);

        $server = new Server($request);
        $server->register($methodName, $this->functionsFixtures->getFunction($methodName));
        $response = $server->execute()->getBody();

        $this->assertEqualsCanonicalizing($successResponse, $response);
    }

    public function testParameterizedRequest(): void
    {
        $methodName = 'subtract';
        $requestParams = ['subtrahend' => 23, 'minuend' => 42];
        $request = $this->jsonRpcHelper->createRequestJsonEncoded($methodName, $requestParams, 1);
        $successResponse = $this->jsonRpcHelper->createResponse(19, 1);

        $server = new Server($request);
        $server->register($methodName, $this->functionsFixtures->getFunction($methodName));
        $response = $server->execute()->getBody();

        $this->assertEqualsCanonicalizing($successResponse, $response);
    }

    public function testNotification(): void
    {
        $methodName = 'update';
        $requestParams = [1, 2, 3, 4, 5];
        $request = $this->jsonRpcHelper->createRequestJsonEncoded($methodName, $requestParams);

        $server = new Server($request);
        $server->register($methodName, $this->functionsFixtures->getFunction($methodName));
        $response = $server->execute()->getBody();

        $this->assertEmpty($response);
    }

    public function testMethodNotFound(): void
    {
        $request = $this->jsonRpcHelper->createRequestJsonEncoded('foobar');

        $server = new Server($request);
        $response = $server->execute()->getBody();

        $this->assertEquals(MethodNotFoundException::$ERROR_CODE, $response['error']['code']);
    }

    public function testParseError(): void
    {
        $request = '{"jsonrpc": "2.0", "method": "foobar, "params": "bar", "baz]';
        $server = new Server($request);
        $response = $server->execute()->getBody();

        $this->assertEquals(ParseErrorException::$ERROR_CODE, $response['error']['code']);
    }

    public function testInvalidRequest(): void
    {
        $request = '{"jsonrpc": "2.0", "method": 1, "params": "bar"}';
        $server = new Server($request);
        $server->register('1', fn() => 1);
        $response = $server->execute()->getBody();

        $this->assertEquals(InvalidRequestException::$ERROR_CODE, $response['error']['code']);
    }

    public function testButchRequestParseError(): void
    {
        $methodName = 'sum';
        $request = '[';
        $request .= $this->jsonRpcHelper->createRequestJsonEncoded($methodName, [1, 2]);
        $request .= '{"jsonrpc": "2.0", "method"]';

        $server = new Server($request);
        $server->register($methodName, $this->functionsFixtures->getFunction($methodName));

        $response = $server->execute()->getBody();
        $this->assertEquals(ParseErrorException::$ERROR_CODE, $response['error']['code']);
    }

    public function testEmptyBatchRequest(): void
    {
        $server = new Server('[]');

        $response = $server->execute()->getBody();
        $this->assertEquals(InvalidRequestException::$ERROR_CODE, $response['error']['code']);
    }

    public function testBatchInvalidRequestWithOneArgument(): void
    {
        $server = new Server('[1]');

        $response = $server->execute()->getBody();
        $this->assertEquals(InvalidRequestException::$ERROR_CODE, $response['error']['code']);
    }

    public function testBatchInvalidRequestWithMultipleArguments(): void
    {
        $expectsResponses = [];

        $server = new Server('[1,2,3]');
        $response = $server->execute()->getBody();

        $expectsResponses[] = $this->jsonRpcHelper->createError(
            InvalidRequestException::$ERROR_CODE,
            InvalidRequestException::$ERROR_MASSAGE
        );

        $expectsResponses[] = $this->jsonRpcHelper->createError(
            InvalidRequestException::$ERROR_CODE,
            InvalidRequestException::$ERROR_MASSAGE
        );

        $expectsResponses[] = $this->jsonRpcHelper->createError(
            InvalidRequestException::$ERROR_CODE,
            InvalidRequestException::$ERROR_MASSAGE
        );

        $this->assertEqualsCanonicalizing($expectsResponses, $response);
    }

    public function testBatchNotificationsRequest(): void
    {
        $batchRequests = [];
        $notifySumMethod = 'notify_sum';
        $notifyHelloMethod = 'notify_hello';

        $batchRequests[] = $this->jsonRpcHelper->createRequest($notifySumMethod, [1, 2, 4]);
        $batchRequests[] = $this->jsonRpcHelper->createRequest($notifyHelloMethod, [1]);

        $server = new Server(json_encode($batchRequests));
        $server->register($notifySumMethod, $this->functionsFixtures->getFunction($notifySumMethod));
        $server->register($notifyHelloMethod, $this->functionsFixtures->getFunction($notifyHelloMethod));
        $response = $server->execute()->getBody();

        $this->assertEmpty($response);
    }

    public function testBatchRequestWithSuccessAndErrorResponses(): void
    {
        $batchRequests = [];
        $expectsResponses = [];

        $sumMethod = 'sum';
        $notifyHelloMethod = 'notify_hello';
        $subtract = 'subtract';
        $fooGetMethod = 'foo.get';
        $getDataMethod = 'get_data';

        $batchRequests[] = $this->jsonRpcHelper->createRequest($sumMethod, [1, 2], 1);
        $batchRequests[] = $this->jsonRpcHelper->createRequest($notifyHelloMethod, [1]);
        $batchRequests[] = $this->jsonRpcHelper->createRequest($subtract, [42, 23], 2);
        $batchRequests[] = ['foo' => 'boo'];
        $batchRequests[] = $this->jsonRpcHelper->createRequest($fooGetMethod, [42, 23], 5);
        $batchRequests[] = $this->jsonRpcHelper->createRequest($getDataMethod, id: 9);

        $expectsResponses[] = $this->jsonRpcHelper->createResponse(3, 1);
        $expectsResponses[] = $this->jsonRpcHelper->createResponse(19, 2);
        $expectsResponses[] = $this->jsonRpcHelper->createError(
            InvalidRequestException::$ERROR_CODE,
            InvalidRequestException::$ERROR_MASSAGE
        );
        $expectsResponses[] = $this->jsonRpcHelper->createError(
            MethodNotFoundException::$ERROR_CODE,
            MethodNotFoundException::$ERROR_MASSAGE,
            5
        );
        $expectsResponses[] = $this->jsonRpcHelper->createResponse(['hello', 5], 9);

        $server = new Server(json_encode($batchRequests));
        $server->register($sumMethod, $this->functionsFixtures->getFunction($sumMethod));
        $server->register($notifyHelloMethod, $this->functionsFixtures->getFunction($notifyHelloMethod));
        $server->register($subtract, $this->functionsFixtures->getFunction($subtract));
        $server->register($getDataMethod, $this->functionsFixtures->getFunction($getDataMethod));
        $response = $server->execute()->getBody();

        $this->assertEqualsCanonicalizing($expectsResponses, $response);
    }
}
