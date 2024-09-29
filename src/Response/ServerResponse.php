<?php

namespace EugeneJenkins\JsonRpcServer\Response;

class ServerResponse
{
    public function __construct(private readonly array $data)
    {
    }

    public function show(): void
    {
        header('Content-Type: application/json');

        echo json_encode($this->getBody());
    }

    private function getParsedResponse(): array
    {
        $keys = array_keys($this->data);

        if ($keys[0] === 0 && count($this->data) === 1) {
            return $this->data[0];
        }

        return $this->data;
    }

    public function getBody(): array
    {
        return $this->getParsedResponse();
    }
}
