<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TechyScouts\Checkfront\Exception\AuthenticationException;
use TechyScouts\Checkfront\Exception\CheckfrontException;
use TechyScouts\Checkfront\Exception\ConflictException;
use TechyScouts\Checkfront\Exception\ForbiddenException;
use TechyScouts\Checkfront\Exception\NotFoundException;
use TechyScouts\Checkfront\Exception\RateLimitException;
use TechyScouts\Checkfront\Exception\ValidationException;

#[CoversClass(CheckfrontException::class)]
#[CoversClass(AuthenticationException::class)]
#[CoversClass(ForbiddenException::class)]
#[CoversClass(NotFoundException::class)]
#[CoversClass(ConflictException::class)]
#[CoversClass(RateLimitException::class)]
#[CoversClass(ValidationException::class)]
final class ExceptionTest extends TestCase
{
    // --- CheckfrontException ---

    #[Test]
    public function checkfrontExceptionExtendsRuntimeException(): void
    {
        $exception = new CheckfrontException('test');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    #[Test]
    public function checkfrontExceptionStoresMessage(): void
    {
        $exception = new CheckfrontException('Something went wrong');

        $this->assertSame('Something went wrong', $exception->getMessage());
    }

    #[Test]
    public function checkfrontExceptionStoresStatusCode(): void
    {
        $exception = new CheckfrontException('Error', 500);

        $this->assertSame(500, $exception->getStatusCode());
    }

    #[Test]
    public function checkfrontExceptionDefaultStatusCodeIsZero(): void
    {
        $exception = new CheckfrontException('Error');

        $this->assertSame(0, $exception->getStatusCode());
    }

    #[Test]
    public function checkfrontExceptionStoresResponseBody(): void
    {
        $body = ['error' => 'Server error', 'details' => ['key' => 'value']];
        $exception = new CheckfrontException('Error', 500, $body);

        $this->assertSame($body, $exception->getResponseBody());
    }

    #[Test]
    public function checkfrontExceptionDefaultResponseBodyIsEmptyArray(): void
    {
        $exception = new CheckfrontException('Error');

        $this->assertSame([], $exception->getResponseBody());
    }

    #[Test]
    public function checkfrontExceptionStoresPreviousException(): void
    {
        $previous = new \RuntimeException('Previous error');
        $exception = new CheckfrontException('Error', 500, [], $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    #[Test]
    public function checkfrontExceptionCodeMatchesStatusCode(): void
    {
        $exception = new CheckfrontException('Error', 404);

        // The parent __construct is called with statusCode as the code
        $this->assertSame(404, $exception->getCode());
    }

    // --- AuthenticationException ---

    #[Test]
    public function authenticationExceptionExtendsCheckfrontException(): void
    {
        $exception = new AuthenticationException('Unauthorized');

        $this->assertInstanceOf(CheckfrontException::class, $exception);
    }

    #[Test]
    public function authenticationExceptionHasCorrectProperties(): void
    {
        $exception = new AuthenticationException('Invalid token', 401, ['error' => 'unauthorized']);

        $this->assertSame('Invalid token', $exception->getMessage());
        $this->assertSame(401, $exception->getStatusCode());
        $this->assertSame(['error' => 'unauthorized'], $exception->getResponseBody());
    }

    // --- ForbiddenException ---

    #[Test]
    public function forbiddenExceptionExtendsCheckfrontException(): void
    {
        $exception = new ForbiddenException('Forbidden');

        $this->assertInstanceOf(CheckfrontException::class, $exception);
    }

    #[Test]
    public function forbiddenExceptionHasCorrectProperties(): void
    {
        $exception = new ForbiddenException('Access denied', 403, ['error' => 'forbidden']);

        $this->assertSame('Access denied', $exception->getMessage());
        $this->assertSame(403, $exception->getStatusCode());
    }

    // --- NotFoundException ---

    #[Test]
    public function notFoundExceptionExtendsCheckfrontException(): void
    {
        $exception = new NotFoundException('Not Found');

        $this->assertInstanceOf(CheckfrontException::class, $exception);
    }

    #[Test]
    public function notFoundExceptionHasCorrectProperties(): void
    {
        $exception = new NotFoundException('Resource not found', 404, ['error' => 'not_found']);

        $this->assertSame('Resource not found', $exception->getMessage());
        $this->assertSame(404, $exception->getStatusCode());
    }

    // --- ConflictException ---

    #[Test]
    public function conflictExceptionExtendsCheckfrontException(): void
    {
        $exception = new ConflictException('Conflict');

        $this->assertInstanceOf(CheckfrontException::class, $exception);
    }

    #[Test]
    public function conflictExceptionHasCorrectProperties(): void
    {
        $exception = new ConflictException('Resource conflict', 409, ['error' => 'conflict']);

        $this->assertSame('Resource conflict', $exception->getMessage());
        $this->assertSame(409, $exception->getStatusCode());
    }

    // --- RateLimitException ---

    #[Test]
    public function rateLimitExceptionExtendsCheckfrontException(): void
    {
        $exception = new RateLimitException('Rate limited');

        $this->assertInstanceOf(CheckfrontException::class, $exception);
    }

    #[Test]
    public function rateLimitExceptionGetRetryAfterReturnsValue(): void
    {
        $exception = new RateLimitException('Too many requests', 429, [], 60);

        $this->assertSame(60, $exception->getRetryAfter());
    }

    #[Test]
    public function rateLimitExceptionGetRetryAfterReturnsNullByDefault(): void
    {
        $exception = new RateLimitException('Too many requests');

        $this->assertNull($exception->getRetryAfter());
    }

    #[Test]
    public function rateLimitExceptionDefaultStatusCodeIs429(): void
    {
        $exception = new RateLimitException('Rate limited');

        $this->assertSame(429, $exception->getStatusCode());
    }

    #[Test]
    public function rateLimitExceptionStoresResponseBody(): void
    {
        $body = ['error' => 'rate_limited', 'retry_after' => 30];
        $exception = new RateLimitException('Rate limited', 429, $body, 30);

        $this->assertSame($body, $exception->getResponseBody());
        $this->assertSame(30, $exception->getRetryAfter());
    }

    #[Test]
    public function rateLimitExceptionStoresPreviousException(): void
    {
        $previous = new \RuntimeException('Underlying error');
        $exception = new RateLimitException('Rate limited', 429, [], null, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    // --- ValidationException ---

    #[Test]
    public function validationExceptionExtendsCheckfrontException(): void
    {
        $exception = new ValidationException('Validation failed');

        $this->assertInstanceOf(CheckfrontException::class, $exception);
    }

    #[Test]
    public function validationExceptionGetErrorsReturnsErrors(): void
    {
        $errors = [
            'name' => ['Name is required'],
            'email' => ['Email is invalid', 'Email already exists'],
        ];

        $exception = new ValidationException('Validation failed', 400, [], $errors);

        $this->assertSame($errors, $exception->getErrors());
    }

    #[Test]
    public function validationExceptionGetErrorsReturnsEmptyArrayByDefault(): void
    {
        $exception = new ValidationException('Validation failed');

        $this->assertSame([], $exception->getErrors());
    }

    #[Test]
    public function validationExceptionDefaultStatusCodeIs400(): void
    {
        $exception = new ValidationException('Validation failed');

        $this->assertSame(400, $exception->getStatusCode());
    }

    #[Test]
    public function validationExceptionStoresResponseBody(): void
    {
        $body = ['message' => 'Invalid input', 'errors' => ['field' => ['error']]];
        $exception = new ValidationException('Invalid input', 400, $body, ['field' => ['error']]);

        $this->assertSame($body, $exception->getResponseBody());
    }

    #[Test]
    public function validationExceptionStoresPreviousException(): void
    {
        $previous = new \RuntimeException('Underlying error');
        $exception = new ValidationException('Validation failed', 400, [], [], $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    // --- All exceptions are catchable as CheckfrontException ---

    #[Test]
    public function allExceptionsAreCatchableAsCheckfrontException(): void
    {
        $exceptions = [
            new AuthenticationException('auth', 401),
            new ForbiddenException('forbidden', 403),
            new NotFoundException('not found', 404),
            new ConflictException('conflict', 409),
            new RateLimitException('rate limit', 429),
            new ValidationException('validation', 400),
        ];

        foreach ($exceptions as $exception) {
            $this->assertInstanceOf(
                CheckfrontException::class,
                $exception,
                get_class($exception) . ' should extend CheckfrontException'
            );
        }
    }
}
