<?php
abstract class BaseController
{
    protected mysqli $mysqli;

    protected function sendResponse($data, int  $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function sendError(string $message, int $statusCode = 400)
    {
        $this->sendResponse(['error' => $message], $statusCode);
    }

    protected function getRequestData(): ?array
    {
        return json_decode(file_get_contents('php://input'), true);
    }
}
