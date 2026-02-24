<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Exception;

class RateLimitException extends CheckfrontException
{
    public function __construct(
        string $message = '',
        int $statusCode = 429,
        array $responseBody = [],
        private readonly ?int $retryAfter = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode, $responseBody, $previous);
    }

    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
