<?php

namespace App\Controllers;

use App\Services\DataService;
use App\Services\SlugService;

class DataController
{
    private $connection;
    private $requestMethod;
    private $dataService;
    private $table;

    public function __construct($connection, $requestMethod, $table)
    {
        $this->connection = $connection;
        $this->requestMethod = $requestMethod;
        $this->dataService = new DataService($connection);
        $this->table = $table;
    }

    public function processRequest()
    {
        switch ($this->requestMethod) {
            case 'POST':
                $response = $this->createTable();
                break;
            case 'GET':
                $response = $this->showTable();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['data']) {
            echo json_encode($response['data']);
        }
    }

    private function createTable()
    {
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['data'] = $this->dataService->create($_FILES['csv']);

        return $response;
    }

    private function showTable()
    {
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['data'] = $this->dataService->show($this->table);
        foreach ($response['data'] as &$data) {
            $data['url'] = '/action/' . $data['ID акции'] . '-' . SlugService::get($data['Название акции']);
        }

        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['data'] = null;

        return $response;
    }
}