# JSON-RPC Server

This project is a JSON-RPC server implemented in PHP 8.1. It adheres to the <a href="https://www.jsonrpc.org/specification" target="_blank">JSON-RPC 2.0 specification (RFC 7049)</a>. The server is built in a canonical way, following best practices for handling JSON-RPC requests.

## Features
- Complies with the JSON-RPC 2.0 standard.
- Supports method registration and execution.
- Structured exception handling for request processing.

## Requirements
- PHP 8.1 or higher
- Composer


## Installation
1. Clone the repository:

    ```bash
    git clone git@github.com:EugeneJenkins/json-rpc-server.git
    cd json-rpc-server
    ```
2. Install dependencies::
    ```bash
    composer install
    ```

## Example Usage

```php
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
```

To run the server locally, you can use the built-in PHP server:

```bash
php -S localhost:8008 index.php
```

## How to Make Requests
You can send JSON-RPC requests to the server using any HTTP client (e.g., curl, Postman, etc.).

Example request:

```bash
curl -X POST http://localhost:8008 \
    -H "Content-Type: application/json" \
    -d '{
        "jsonrpc": "2.0",
        "method": "subtract",
        "params": {"minuend": 10, "subtrahend": 3},
        "id": 1
    }'
```

Example response:
```json
{
    "jsonrpc": "2.0",
    "result": {
        "minuend": 10,
        "subtrahend": 3
    },
    "id": 1
}
```

## Error Handling
If an error occurs during the execution of a request, the server will respond with a JSON-RPC error object, as specified in the JSON-RPC 2.0 specification.
