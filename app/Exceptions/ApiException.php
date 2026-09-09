<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct(
        string $message,
        protected string $errorCode,
        protected int $statusCode = 400,
        protected ?array $errorsDetail = null
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorsDetail(): ?array
    {
        return $this->errorsDetail;
    }
}