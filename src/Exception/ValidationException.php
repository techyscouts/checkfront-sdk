<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Exception;

class ValidationException extends CheckfrontException
{
    public function __construct(
        string $message = '',
        int $statusCode = 400,
        array $responseBody = [],
        private readonly array $errors = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $responseBody, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
