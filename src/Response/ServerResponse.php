<?php

namespace EugeneJenkins\JsonRpcServer\Response;

class ServerResponse
{
    public function __construct(private array $data)
    {
    }

    public function show(): void
    {
        if (empty($this->getBody())) {
            echo '';
            return;
        }

        header('Content-Type: application/json');

        echo json_encode($this->getBody());
    }

    private function getParsedResponse(): array
    {
        if (empty($this->data)) {
            return [];
        }

        $keys = array_keys($this->data);

        if ($keys[0] === 0 && count($this->data) === 1) {
            return $this->data[0];
        }

        $this->data = $this->clearEmptyResponse($this->data);

        return $this->data;
    }

    public function getBody(): array
    {
        return $this->getParsedResponse();
    }

    private function clearEmptyResponse(array $data): array
    {
        //TODO refactor
        $toReturn = [];

        if (array_keys($data) !== range(0, count($data) - 1)) {
            return $data;
        }

        foreach ($data as $key => $item) {
            if (is_array($item) && empty($item)) {
                continue;
            }

            $toReturn[] = $item;
        }

        if (count($toReturn) === 1){
            return $toReturn[0];
        }

        return $toReturn;
    }
}
