<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Auth;

use Psr\Http\Message\RequestInterface;

interface AuthenticationInterface
{
    public function authenticate(RequestInterface $request): RequestInterface;
}
