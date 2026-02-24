<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Http\Message;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TechyScouts\Checkfront\Http\Message\Request;
use TechyScouts\Checkfront\Http\Message\Stream;
use TechyScouts\Checkfront\Http\Message\Uri;

#[CoversClass(Request::class)]
final class RequestTest extends TestCase
{
    #[Test]
    public function constructorSetsMethodAndUri(): void
    {
        $request = new Request('GET', 'https://example.com/path');

        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('https://example.com/path', (string) $request->getUri());
    }

    #[Test]
    public function constructorAcceptsUriInterface(): void
    {
        $uri = new Uri('https://example.com/path');
        $request = new Request('POST', $uri);

        $this->assertSame($uri, $request->getUri());
    }

    #[Test]
    public function constructorSetsHeaders(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'Content-Type' => 'application/json',
            'Accept' => ['text/html', 'application/json'],
        ]);

        $this->assertSame(['application/json'], $request->getHeader('Content-Type'));
        $this->assertSame(['text/html', 'application/json'], $request->getHeader('Accept'));
    }

    #[Test]
    public function constructorSetsBodyWhenProvided(): void
    {
        $body = Stream::create('request body');
        $request = new Request('POST', 'https://example.com', [], $body);

        $this->assertSame('request body', (string) $request->getBody());
    }

    #[Test]
    public function constructorCreatesEmptyBodyWhenNotProvided(): void
    {
        $request = new Request('GET', 'https://example.com');

        $this->assertSame('', (string) $request->getBody());
    }

    #[Test]
    public function constructorSetsProtocolVersion(): void
    {
        $request = new Request('GET', 'https://example.com', [], null, '2.0');

        $this->assertSame('2.0', $request->getProtocolVersion());
    }

    #[Test]
    public function defaultProtocolVersionIs11(): void
    {
        $request = new Request('GET', 'https://example.com');

        $this->assertSame('1.1', $request->getProtocolVersion());
    }

    #[Test]
    public function constructorAutoSetsHostHeaderFromUri(): void
    {
        $request = new Request('GET', 'https://example.com/path');

        $this->assertTrue($request->hasHeader('Host'));
        $this->assertSame('example.com', $request->getHeaderLine('Host'));
    }

    #[Test]
    public function constructorHostHeaderIncludesNonDefaultPort(): void
    {
        $request = new Request('GET', 'https://example.com:8443/path');

        $this->assertSame('example.com:8443', $request->getHeaderLine('Host'));
    }

    #[Test]
    public function constructorDoesNotOverrideExplicitHostHeader(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'Host' => 'custom.com',
        ]);

        $this->assertSame('custom.com', $request->getHeaderLine('Host'));
    }

    #[Test]
    public function constructorDoesNotSetHostHeaderWhenUriHasNoHost(): void
    {
        $request = new Request('GET', '/relative/path');

        $this->assertFalse($request->hasHeader('Host'));
    }

    #[Test]
    public function headersAreCaseInsensitive(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'Content-Type' => 'application/json',
        ]);

        $this->assertTrue($request->hasHeader('content-type'));
        $this->assertTrue($request->hasHeader('CONTENT-TYPE'));
        $this->assertSame(['application/json'], $request->getHeader('content-type'));
    }

    #[Test]
    public function getHeaderReturnsEmptyArrayForMissingHeader(): void
    {
        $request = new Request('GET', 'https://example.com');

        $this->assertSame([], $request->getHeader('X-Missing'));
    }

    #[Test]
    public function getHeaderLineReturnsCommaSeparatedValues(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'Accept' => ['text/html', 'application/json'],
        ]);

        $this->assertSame('text/html, application/json', $request->getHeaderLine('Accept'));
    }

    #[Test]
    public function getHeaderLineReturnsEmptyStringForMissingHeader(): void
    {
        $request = new Request('GET', 'https://example.com');

        $this->assertSame('', $request->getHeaderLine('X-Missing'));
    }

    #[Test]
    public function getHeadersReturnsAllHeaders(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'Content-Type' => 'application/json',
            'Accept' => 'text/html',
        ]);

        $headers = $request->getHeaders();

        $this->assertArrayHasKey('Host', $headers);
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertArrayHasKey('Accept', $headers);
    }

    #[Test]
    public function withHeaderReturnsNewInstanceWithReplacedHeader(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'Accept' => 'text/html',
        ]);

        $new = $request->withHeader('Accept', 'application/json');

        $this->assertNotSame($request, $new);
        $this->assertSame(['application/json'], $new->getHeader('Accept'));
        $this->assertSame(['text/html'], $request->getHeader('Accept'));
    }

    #[Test]
    public function withHeaderReplacesCaseInsensitively(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'Content-Type' => 'text/plain',
        ]);

        $new = $request->withHeader('content-type', 'application/json');

        $this->assertSame(['application/json'], $new->getHeader('Content-Type'));
        // The new key name is used
        $this->assertTrue($new->hasHeader('content-type'));
    }

    #[Test]
    public function withAddedHeaderAppendsValues(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'Accept' => 'text/html',
        ]);

        $new = $request->withAddedHeader('Accept', 'application/json');

        $this->assertNotSame($request, $new);
        $this->assertSame(['text/html', 'application/json'], $new->getHeader('Accept'));
        $this->assertSame(['text/html'], $request->getHeader('Accept'));
    }

    #[Test]
    public function withAddedHeaderCreatesNewHeaderIfNotExists(): void
    {
        $request = new Request('GET', 'https://example.com');

        $new = $request->withAddedHeader('X-Custom', 'value');

        $this->assertSame(['value'], $new->getHeader('X-Custom'));
    }

    #[Test]
    public function withoutHeaderRemovesHeader(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'Accept' => 'text/html',
            'Content-Type' => 'application/json',
        ]);

        $new = $request->withoutHeader('Accept');

        $this->assertNotSame($request, $new);
        $this->assertFalse($new->hasHeader('Accept'));
        $this->assertTrue($request->hasHeader('Accept'));
    }

    #[Test]
    public function withoutHeaderReturnsSameInstanceWhenHeaderNotPresent(): void
    {
        $request = new Request('GET', 'https://example.com');

        $new = $request->withoutHeader('X-Missing');

        $this->assertSame($request, $new);
    }

    #[Test]
    public function withoutHeaderIsCaseInsensitive(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'Content-Type' => 'application/json',
        ]);

        $new = $request->withoutHeader('content-type');

        $this->assertFalse($new->hasHeader('Content-Type'));
    }

    #[Test]
    public function withBodyReturnsNewInstanceWithNewBody(): void
    {
        $request = new Request('GET', 'https://example.com');
        $body = Stream::create('new body');

        $new = $request->withBody($body);

        $this->assertNotSame($request, $new);
        $this->assertSame('new body', (string) $new->getBody());
    }

    #[Test]
    public function withBodyReturnsSameInstanceForSameBody(): void
    {
        $body = Stream::create('body');
        $request = new Request('GET', 'https://example.com', [], $body);

        $new = $request->withBody($body);

        $this->assertSame($request, $new);
    }

    #[Test]
    public function withProtocolVersionReturnsNewInstance(): void
    {
        $request = new Request('GET', 'https://example.com');
        $new = $request->withProtocolVersion('2.0');

        $this->assertNotSame($request, $new);
        $this->assertSame('2.0', $new->getProtocolVersion());
        $this->assertSame('1.1', $request->getProtocolVersion());
    }

    #[Test]
    public function withProtocolVersionReturnsSameInstanceWhenUnchanged(): void
    {
        $request = new Request('GET', 'https://example.com');
        $new = $request->withProtocolVersion('1.1');

        $this->assertSame($request, $new);
    }

    #[Test]
    public function getRequestTargetReturnsPathAndQuery(): void
    {
        $request = new Request('GET', 'https://example.com/path?key=value');

        $this->assertSame('/path?key=value', $request->getRequestTarget());
    }

    #[Test]
    public function getRequestTargetReturnsSlashForEmptyPath(): void
    {
        $request = new Request('GET', 'https://example.com');

        $this->assertSame('/', $request->getRequestTarget());
    }

    #[Test]
    public function getRequestTargetReturnsPathWithoutQueryWhenNoQuery(): void
    {
        $request = new Request('GET', 'https://example.com/path');

        $this->assertSame('/path', $request->getRequestTarget());
    }

    #[Test]
    public function withRequestTargetReturnsNewInstance(): void
    {
        $request = new Request('GET', 'https://example.com/path');
        $new = $request->withRequestTarget('*');

        $this->assertNotSame($request, $new);
        $this->assertSame('*', $new->getRequestTarget());
    }

    #[Test]
    public function withRequestTargetReturnsSameInstanceWhenUnchanged(): void
    {
        $request = new Request('GET', 'https://example.com');
        $new = $request->withRequestTarget('');

        $this->assertSame($request, $new);
    }

    #[Test]
    public function getRequestTargetReturnsExplicitTargetWhenSet(): void
    {
        $request = new Request('GET', 'https://example.com/path?key=val');
        $new = $request->withRequestTarget('*');

        $this->assertSame('*', $new->getRequestTarget());
    }

    #[Test]
    public function withMethodReturnsNewInstance(): void
    {
        $request = new Request('GET', 'https://example.com');
        $new = $request->withMethod('POST');

        $this->assertNotSame($request, $new);
        $this->assertSame('POST', $new->getMethod());
        $this->assertSame('GET', $request->getMethod());
    }

    #[Test]
    public function withMethodReturnsSameInstanceWhenUnchanged(): void
    {
        $request = new Request('GET', 'https://example.com');
        $new = $request->withMethod('GET');

        $this->assertSame($request, $new);
    }

    #[Test]
    public function withMethodThrowsOnEmptyString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('HTTP method must not be empty');

        $request = new Request('GET', 'https://example.com');
        $request->withMethod('');
    }

    #[Test]
    public function withUriReturnsNewInstance(): void
    {
        $request = new Request('GET', 'https://example.com');
        $newUri = new Uri('https://other.com/path');

        $new = $request->withUri($newUri);

        $this->assertNotSame($request, $new);
        $this->assertSame($newUri, $new->getUri());
    }

    #[Test]
    public function withUriReturnsSameInstanceForSameUri(): void
    {
        $uri = new Uri('https://example.com');
        $request = new Request('GET', $uri);

        $new = $request->withUri($uri);

        $this->assertSame($request, $new);
    }

    #[Test]
    public function withUriUpdatesHostHeader(): void
    {
        $request = new Request('GET', 'https://example.com');
        $newUri = new Uri('https://other.com');

        $new = $request->withUri($newUri);

        $this->assertSame('other.com', $new->getHeaderLine('Host'));
    }

    #[Test]
    public function withUriPreservesHostHeaderWhenFlagIsTrue(): void
    {
        $request = new Request('GET', 'https://example.com');
        $newUri = new Uri('https://other.com');

        $new = $request->withUri($newUri, true);

        $this->assertSame('example.com', $new->getHeaderLine('Host'));
    }

    #[Test]
    public function withUriSetsHostFromUriWhenPreserveHostAndNoHostHeader(): void
    {
        $request = new Request('GET', '/relative/path');
        $this->assertFalse($request->hasHeader('Host'));

        $newUri = new Uri('https://other.com');
        $new = $request->withUri($newUri, true);

        $this->assertSame('other.com', $new->getHeaderLine('Host'));
    }

    #[Test]
    public function headerValuesAreTrimmed(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'X-Custom' => "  value with spaces  \t",
        ]);

        $this->assertSame(['value with spaces'], $request->getHeader('X-Custom'));
    }

    #[Test]
    public function withHeaderThrowsOnEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Header value must not be an empty array');

        $request = new Request('GET', 'https://example.com');
        $request->withHeader('Accept', []);
    }

    #[Test]
    public function hostHeaderIsFirstInHeadersList(): void
    {
        $request = new Request('GET', 'https://example.com', [
            'Accept' => 'text/html',
            'Content-Type' => 'application/json',
        ]);

        $headers = $request->getHeaders();
        $keys = array_keys($headers);

        $this->assertSame('Host', $keys[0]);
    }
}
