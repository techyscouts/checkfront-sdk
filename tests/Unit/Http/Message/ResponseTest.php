<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Http\Message;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TechyScouts\Checkfront\Http\Message\Response;
use TechyScouts\Checkfront\Http\Message\Stream;

#[CoversClass(Response::class)]
final class ResponseTest extends TestCase
{
    #[Test]
    public function defaultStatusCodeIs200(): void
    {
        $response = new Response();

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function defaultReasonPhraseIsOk(): void
    {
        $response = new Response();

        $this->assertSame('OK', $response->getReasonPhrase());
    }

    #[Test]
    public function constructorSetsStatusCode(): void
    {
        $response = new Response(404);

        $this->assertSame(404, $response->getStatusCode());
    }

    #[Test]
    public function constructorAutoResolvesReasonPhraseForKnownCodes(): void
    {
        $knownCodes = [
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            204 => 'No Content',
            301 => 'Moved Permanently',
            302 => 'Found',
            304 => 'Not Modified',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            409 => 'Conflict',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
        ];

        foreach ($knownCodes as $code => $phrase) {
            $response = new Response($code);
            $this->assertSame($phrase, $response->getReasonPhrase(), "Failed for status code {$code}");
        }
    }

    #[Test]
    public function constructorUsesCustomReasonPhraseWhenProvided(): void
    {
        $response = new Response(200, [], null, 'All Good');

        $this->assertSame('All Good', $response->getReasonPhrase());
    }

    #[Test]
    public function constructorReturnsEmptyReasonPhraseForUnknownCode(): void
    {
        $response = new Response(299);

        $this->assertSame('', $response->getReasonPhrase());
    }

    #[Test]
    public function constructorSetsHeaders(): void
    {
        $response = new Response(200, [
            'Content-Type' => 'application/json',
            'X-Custom' => ['value1', 'value2'],
        ]);

        $this->assertSame(['application/json'], $response->getHeader('Content-Type'));
        $this->assertSame(['value1', 'value2'], $response->getHeader('X-Custom'));
    }

    #[Test]
    public function constructorSetsBody(): void
    {
        $body = Stream::create('{"key":"value"}');
        $response = new Response(200, [], $body);

        $this->assertSame('{"key":"value"}', (string) $response->getBody());
    }

    #[Test]
    public function constructorCreatesEmptyBodyWhenNoneProvided(): void
    {
        $response = new Response();

        $this->assertSame('', (string) $response->getBody());
    }

    #[Test]
    public function constructorSetsProtocolVersion(): void
    {
        $response = new Response(200, [], null, '', '2.0');

        $this->assertSame('2.0', $response->getProtocolVersion());
    }

    #[Test]
    public function defaultProtocolVersionIs11(): void
    {
        $response = new Response();

        $this->assertSame('1.1', $response->getProtocolVersion());
    }

    #[Test]
    public function constructorThrowsOnInvalidStatusCodeBelow100(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP status code: 99');

        new Response(99);
    }

    #[Test]
    public function constructorThrowsOnInvalidStatusCodeAbove599(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid HTTP status code: 600');

        new Response(600);
    }

    #[Test]
    public function withStatusReturnsNewInstance(): void
    {
        $response = new Response(200);
        $new = $response->withStatus(404);

        $this->assertNotSame($response, $new);
        $this->assertSame(404, $new->getStatusCode());
        $this->assertSame('Not Found', $new->getReasonPhrase());
        $this->assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function withStatusAcceptsCustomReasonPhrase(): void
    {
        $response = new Response(200);
        $new = $response->withStatus(200, 'Custom OK');

        $this->assertSame(200, $new->getStatusCode());
        $this->assertSame('Custom OK', $new->getReasonPhrase());
    }

    #[Test]
    public function withStatusAutoResolvesReasonPhraseWhenNotProvided(): void
    {
        $response = new Response(200);
        $new = $response->withStatus(500);

        $this->assertSame('Internal Server Error', $new->getReasonPhrase());
    }

    #[Test]
    public function withStatusThrowsOnInvalidCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $response = new Response();
        $response->withStatus(600);
    }

    #[Test]
    public function headersAreCaseInsensitive(): void
    {
        $response = new Response(200, [
            'Content-Type' => 'application/json',
        ]);

        $this->assertTrue($response->hasHeader('content-type'));
        $this->assertTrue($response->hasHeader('CONTENT-TYPE'));
        $this->assertSame(['application/json'], $response->getHeader('content-type'));
    }

    #[Test]
    public function getHeaderReturnsEmptyArrayForMissingHeader(): void
    {
        $response = new Response();

        $this->assertSame([], $response->getHeader('X-Missing'));
    }

    #[Test]
    public function getHeaderLineReturnsCommaSeparatedValues(): void
    {
        $response = new Response(200, [
            'Accept' => ['text/html', 'application/json'],
        ]);

        $this->assertSame('text/html, application/json', $response->getHeaderLine('Accept'));
    }

    #[Test]
    public function getHeaderLineReturnsEmptyStringForMissingHeader(): void
    {
        $response = new Response();

        $this->assertSame('', $response->getHeaderLine('X-Missing'));
    }

    #[Test]
    public function withHeaderReturnsNewInstance(): void
    {
        $response = new Response(200, ['Accept' => 'text/html']);
        $new = $response->withHeader('Accept', 'application/json');

        $this->assertNotSame($response, $new);
        $this->assertSame(['application/json'], $new->getHeader('Accept'));
        $this->assertSame(['text/html'], $response->getHeader('Accept'));
    }

    #[Test]
    public function withHeaderReplacesCaseInsensitively(): void
    {
        $response = new Response(200, ['Content-Type' => 'text/plain']);
        $new = $response->withHeader('content-type', 'application/json');

        $this->assertSame(['application/json'], $new->getHeader('content-type'));
    }

    #[Test]
    public function withAddedHeaderAppendsValues(): void
    {
        $response = new Response(200, ['Accept' => 'text/html']);
        $new = $response->withAddedHeader('Accept', 'application/json');

        $this->assertNotSame($response, $new);
        $this->assertSame(['text/html', 'application/json'], $new->getHeader('Accept'));
    }

    #[Test]
    public function withAddedHeaderCreatesNewHeaderIfNotExists(): void
    {
        $response = new Response();
        $new = $response->withAddedHeader('X-Custom', 'value');

        $this->assertSame(['value'], $new->getHeader('X-Custom'));
    }

    #[Test]
    public function withoutHeaderRemovesHeader(): void
    {
        $response = new Response(200, [
            'Accept' => 'text/html',
            'Content-Type' => 'application/json',
        ]);
        $new = $response->withoutHeader('Accept');

        $this->assertNotSame($response, $new);
        $this->assertFalse($new->hasHeader('Accept'));
        $this->assertTrue($response->hasHeader('Accept'));
    }

    #[Test]
    public function withoutHeaderReturnsSameInstanceWhenNotPresent(): void
    {
        $response = new Response();
        $new = $response->withoutHeader('X-Missing');

        $this->assertSame($response, $new);
    }

    #[Test]
    public function withBodyReturnsNewInstance(): void
    {
        $response = new Response();
        $body = Stream::create('new body');
        $new = $response->withBody($body);

        $this->assertNotSame($response, $new);
        $this->assertSame('new body', (string) $new->getBody());
    }

    #[Test]
    public function withBodyReturnsSameInstanceForSameBody(): void
    {
        $body = Stream::create('body');
        $response = new Response(200, [], $body);
        $new = $response->withBody($body);

        $this->assertSame($response, $new);
    }

    #[Test]
    public function withProtocolVersionReturnsNewInstance(): void
    {
        $response = new Response();
        $new = $response->withProtocolVersion('2.0');

        $this->assertNotSame($response, $new);
        $this->assertSame('2.0', $new->getProtocolVersion());
        $this->assertSame('1.1', $response->getProtocolVersion());
    }

    #[Test]
    public function withProtocolVersionReturnsSameInstanceWhenUnchanged(): void
    {
        $response = new Response();
        $new = $response->withProtocolVersion('1.1');

        $this->assertSame($response, $new);
    }

    #[Test]
    public function headerValuesAreTrimmed(): void
    {
        $response = new Response(200, [
            'X-Custom' => "  padded value \t",
        ]);

        $this->assertSame(['padded value'], $response->getHeader('X-Custom'));
    }

    #[Test]
    public function withHeaderThrowsOnEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Header value must not be an empty array');

        $response = new Response();
        $response->withHeader('Accept', []);
    }

    #[Test]
    public function statusCode100IsValid(): void
    {
        $response = new Response(100);

        $this->assertSame(100, $response->getStatusCode());
    }

    #[Test]
    public function statusCode599IsValid(): void
    {
        $response = new Response(599);

        $this->assertSame(599, $response->getStatusCode());
    }
}
