<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Auth;

use Psr\Http\Message\RequestInterface;

final class TokenAuthentication implements AuthenticationInterface
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $apiSecret,
    ) {}

    public function authenticate(RequestInterface $request): RequestInterface
    {
        $credentials = base64_encode("{$this->apiKey}:{$this->apiSecret}");

        return $request->withHeader('Authorization', "Basic {$credentials}");
    }
}
