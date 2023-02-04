<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as IlluminateResponse;

trait ResponsableTrait
{
    protected int $statusCode = 200;
    protected bool $status = true;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function setStatus($status): static
    {
        $this->status = $status;

        return $this;
    }

    public function respond($data, $message = 'success'): JsonResponse
    {
        $code = $this->getStatusCode();

        return response()->json([
            'status_code'   =>      $this->statusCode,
            'status'        =>      $this->status,
            'message'       =>      $message,
            'data'          =>      $data,
        ], $code);
    }

    public function respondWithError($message): JsonResponse
    {
        return $this->respond([
            'error' => [
                'message' => $message,
            ],
        ], 'Request failed');
    }

    public function respondNotFound($message = 'Not Found'): JsonResponse
    {
        return $this
            ->setStatus(false)
            ->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)
            ->respondWithError($message);
    }

    protected function respondUnauthorized($message = 'Unauthorized'): JsonResponse
    {
        return $this
            ->setStatus(false)
            ->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)
            ->respondWithError($message);
    }

    protected function respondForbidden($message = 'Forbidden'): JsonResponse
    {
        return $this
            ->setStatus(false)
            ->setStatusCode(IlluminateResponse::HTTP_FORBIDDEN)
            ->respondWithError($message);
    }
}
