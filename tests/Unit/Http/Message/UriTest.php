<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Http\Message;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TechyScouts\Checkfront\Http\Message\Uri;

#[CoversClass(Uri::class)]
final class UriTest extends TestCase
{
    #[Test]
    public function constructorWithEmptyStringReturnsEmptyUri(): void
    {
        $uri = new Uri('');

        $this->assertSame('', $uri->getScheme());
        $this->assertSame('', $uri->getHost());
        $this->assertNull($uri->getPort());
        $this->assertSame('', $uri->getPath());
        $this->assertSame('', $uri->getQuery());
        $this->assertSame('', $uri->getFragment());
        $this->assertSame('', $uri->getUserInfo());
        $this->assertSame('', $uri->getAuthority());
    }

    #[Test]
    public function constructorParsesFullUri(): void
    {
        $uri = new Uri('https://user:pass@example.com:8443/path/to/resource?key=value&foo=bar#section');

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame(8443, $uri->getPort());
        $this->assertSame('/path/to/resource', $uri->getPath());
        $this->assertSame('key=value&foo=bar', $uri->getQuery());
        $this->assertSame('section', $uri->getFragment());
        $this->assertSame('user:pass', $uri->getUserInfo());
    }

    #[Test]
    public function constructorLowercasesSchemeAndHost(): void
    {
        $uri = new Uri('HTTPS://EXAMPLE.COM/path');

        $this->assertSame('https', $uri->getScheme());
        $this->assertSame('example.com', $uri->getHost());
    }

    #[Test]
    public function constructorThrowsOnInvalidUri(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Uri('http:///invalid');
    }

    #[Test]
    public function constructorParsesUserWithoutPassword(): void
    {
        $uri = new Uri('https://user@example.com/path');

        $this->assertSame('user', $uri->getUserInfo());
    }

    #[Test]
    public function getPortReturnsNullForDefaultHttpPort(): void
    {
        $uri = new Uri('http://example.com:80/path');

        $this->assertNull($uri->getPort());
    }

    #[Test]
    public function getPortReturnsNullForDefaultHttpsPort(): void
    {
        $uri = new Uri('https://example.com:443/path');

        $this->assertNull($uri->getPort());
    }

    #[Test]
    public function getPortReturnsNonDefaultPort(): void
    {
        $uri = new Uri('http://example.com:8080/path');

        $this->assertSame(8080, $uri->getPort());
    }

    #[Test]
    public function getPortReturnsNullWhenNoPortSet(): void
    {
        $uri = new Uri('http://example.com/path');

        $this->assertNull($uri->getPort());
    }

    #[Test]
    public function getAuthorityReturnsEmptyStringWithNoHost(): void
    {
        $uri = new Uri('');

        $this->assertSame('', $uri->getAuthority());
    }

    #[Test]
    public function getAuthorityReturnsHostOnly(): void
    {
        $uri = new Uri('http://example.com/path');

        $this->assertSame('example.com', $uri->getAuthority());
    }

    #[Test]
    public function getAuthorityIncludesUserInfo(): void
    {
        $uri = new Uri('http://user:pass@example.com/path');

        $this->assertSame('user:pass@example.com', $uri->getAuthority());
    }

    #[Test]
    public function getAuthorityIncludesNonDefaultPort(): void
    {
        $uri = new Uri('http://example.com:9090/path');

        $this->assertSame('example.com:9090', $uri->getAuthority());
    }

    #[Test]
    public function getAuthorityExcludesDefaultPort(): void
    {
        $uri = new Uri('https://example.com:443/path');

        $this->assertSame('example.com', $uri->getAuthority());
    }

    #[Test]
    public function getAuthorityIncludesUserInfoAndNonDefaultPort(): void
    {
        $uri = new Uri('http://admin:secret@example.com:8080/path');

        $this->assertSame('admin:secret@example.com:8080', $uri->getAuthority());
    }

    #[Test]
    public function withSchemeReturnsNewInstance(): void
    {
        $uri = new Uri('https://example.com');
        $new = $uri->withScheme('http');

        $this->assertNotSame($uri, $new);
        $this->assertSame('http', $new->getScheme());
        $this->assertSame('https', $uri->getScheme());
    }

    #[Test]
    public function withSchemeReturnsSameInstanceWhenUnchanged(): void
    {
        $uri = new Uri('https://example.com');
        $new = $uri->withScheme('https');

        $this->assertSame($uri, $new);
    }

    #[Test]
    public function withSchemeLowercasesInput(): void
    {
        $uri = new Uri('');
        $new = $uri->withScheme('HTTPS');

        $this->assertSame('https', $new->getScheme());
    }

    #[Test]
    public function withUserInfoReturnsNewInstance(): void
    {
        $uri = new Uri('https://example.com');
        $new = $uri->withUserInfo('user', 'pass');

        $this->assertNotSame($uri, $new);
        $this->assertSame('user:pass', $new->getUserInfo());
        $this->assertSame('', $uri->getUserInfo());
    }

    #[Test]
    public function withUserInfoWithoutPasswordSetsUserOnly(): void
    {
        $uri = new Uri('https://example.com');
        $new = $uri->withUserInfo('user');

        $this->assertSame('user', $new->getUserInfo());
    }

    #[Test]
    public function withUserInfoReturnsSameInstanceWhenUnchanged(): void
    {
        $uri = new Uri('https://user:pass@example.com');
        $new = $uri->withUserInfo('user', 'pass');

        $this->assertSame($uri, $new);
    }

    #[Test]
    public function withUserInfoWithEmptyPasswordIgnoresIt(): void
    {
        $uri = new Uri('https://example.com');
        $new = $uri->withUserInfo('user', '');

        $this->assertSame('user', $new->getUserInfo());
    }

    #[Test]
    public function withHostReturnsNewInstance(): void
    {
        $uri = new Uri('https://example.com');
        $new = $uri->withHost('other.com');

        $this->assertNotSame($uri, $new);
        $this->assertSame('other.com', $new->getHost());
        $this->assertSame('example.com', $uri->getHost());
    }

    #[Test]
    public function withHostReturnsSameInstanceWhenUnchanged(): void
    {
        $uri = new Uri('https://example.com');
        $new = $uri->withHost('example.com');

        $this->assertSame($uri, $new);
    }

    #[Test]
    public function withHostLowercasesInput(): void
    {
        $uri = new Uri('https://example.com');
        $new = $uri->withHost('NEWHOST.COM');

        $this->assertSame('newhost.com', $new->getHost());
    }

    #[Test]
    public function withPortReturnsNewInstance(): void
    {
        $uri = new Uri('https://example.com');
        $new = $uri->withPort(9090);

        $this->assertNotSame($uri, $new);
        $this->assertSame(9090, $new->getPort());
    }

    #[Test]
    public function withPortAcceptsNull(): void
    {
        $uri = new Uri('https://example.com:9090');
        $new = $uri->withPort(null);

        $this->assertNull($new->getPort());
    }

    #[Test]
    public function withPortReturnsSameInstanceWhenUnchanged(): void
    {
        $uri = new Uri('https://example.com:9090');
        $new = $uri->withPort(9090);

        $this->assertSame($uri, $new);
    }

    #[Test]
    public function withPortThrowsOnNegativePort(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $uri = new Uri('https://example.com');
        $uri->withPort(-1);
    }

    #[Test]
    public function withPortThrowsOnPortAbove65535(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $uri = new Uri('https://example.com');
        $uri->withPort(65536);
    }

    #[Test]
    public function withPathReturnsNewInstance(): void
    {
        $uri = new Uri('https://example.com/old');
        $new = $uri->withPath('/new');

        $this->assertNotSame($uri, $new);
        $this->assertSame('/new', $new->getPath());
        $this->assertSame('/old', $uri->getPath());
    }

    #[Test]
    public function withPathReturnsSameInstanceWhenUnchanged(): void
    {
        $uri = new Uri('https://example.com/path');
        $new = $uri->withPath('/path');

        $this->assertSame($uri, $new);
    }

    #[Test]
    public function withQueryReturnsNewInstance(): void
    {
        $uri = new Uri('https://example.com?old=1');
        $new = $uri->withQuery('new=2');

        $this->assertNotSame($uri, $new);
        $this->assertSame('new=2', $new->getQuery());
        $this->assertSame('old=1', $uri->getQuery());
    }

    #[Test]
    public function withQueryReturnsSameInstanceWhenUnchanged(): void
    {
        $uri = new Uri('https://example.com?key=val');
        $new = $uri->withQuery('key=val');

        $this->assertSame($uri, $new);
    }

    #[Test]
    public function withFragmentReturnsNewInstance(): void
    {
        $uri = new Uri('https://example.com#old');
        $new = $uri->withFragment('new');

        $this->assertNotSame($uri, $new);
        $this->assertSame('new', $new->getFragment());
        $this->assertSame('old', $uri->getFragment());
    }

    #[Test]
    public function withFragmentReturnsSameInstanceWhenUnchanged(): void
    {
        $uri = new Uri('https://example.com#frag');
        $new = $uri->withFragment('frag');

        $this->assertSame($uri, $new);
    }

    #[Test]
    public function toStringRendersFullUri(): void
    {
        $uri = new Uri('https://user:pass@example.com:8443/path?key=val#frag');

        $this->assertSame('https://user:pass@example.com:8443/path?key=val#frag', (string) $uri);
    }

    #[Test]
    public function toStringRendersSchemeAndAuthority(): void
    {
        $uri = new Uri('https://example.com');

        $this->assertSame('https://example.com/', (string) $uri);
    }

    #[Test]
    public function toStringRendersPathOnly(): void
    {
        $uri = new Uri('');
        $uri = $uri->withPath('/just/a/path');

        $this->assertSame('/just/a/path', (string) $uri);
    }

    #[Test]
    public function toStringAddsSlashWhenAuthorityPresentAndPathEmpty(): void
    {
        $uri = (new Uri(''))
            ->withScheme('https')
            ->withHost('example.com');

        $this->assertSame('https://example.com/', (string) $uri);
    }

    #[Test]
    public function toStringPrefixesPathWithSlashWhenAuthorityPresent(): void
    {
        $uri = (new Uri(''))
            ->withScheme('https')
            ->withHost('example.com')
            ->withPath('relative');

        $this->assertSame('https://example.com/relative', (string) $uri);
    }

    #[Test]
    public function toStringCollapseDoubleSlashPathWithNoAuthority(): void
    {
        $uri = (new Uri(''))->withPath('//double-slash');

        $this->assertSame('/double-slash', (string) $uri);
    }

    #[Test]
    public function toStringOmitsDefaultPort(): void
    {
        $uri = new Uri('https://example.com:443/path');

        $this->assertSame('https://example.com/path', (string) $uri);
    }

    #[Test]
    public function toStringIncludesNonDefaultPort(): void
    {
        $uri = new Uri('https://example.com:9090/path');

        $this->assertSame('https://example.com:9090/path', (string) $uri);
    }

    #[Test]
    public function filterPortAcceptsZero(): void
    {
        $uri = new Uri('https://example.com');
        $new = $uri->withPort(0);

        $this->assertSame(0, $new->getPort());
    }

    #[Test]
    public function filterPortAccepts65535(): void
    {
        $uri = new Uri('https://example.com');
        $new = $uri->withPort(65535);

        $this->assertSame(65535, $new->getPort());
    }

    #[Test]
    public function pathEncodesSpecialCharacters(): void
    {
        $uri = new Uri('https://example.com/path with spaces');

        $this->assertStringContainsString('%20', $uri->getPath());
    }

    #[Test]
    public function queryEncodesSpecialCharacters(): void
    {
        $uri = (new Uri(''))->withQuery('key=val ue');

        $this->assertStringContainsString('%20', $uri->getQuery());
    }

    #[Test]
    public function fragmentEncodesSpecialCharacters(): void
    {
        $uri = (new Uri(''))->withFragment('frag ment');

        $this->assertStringContainsString('%20', $uri->getFragment());
    }
}
