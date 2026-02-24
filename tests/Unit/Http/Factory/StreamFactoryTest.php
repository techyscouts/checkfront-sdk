<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Http\Factory;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use TechyScouts\Checkfront\Http\Factory\StreamFactory;

#[CoversClass(StreamFactory::class)]
final class StreamFactoryTest extends TestCase
{
    private StreamFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new StreamFactory();
    }

    #[Test]
    public function createStreamReturnsStreamInterface(): void
    {
        $stream = $this->factory->createStream('hello');

        $this->assertInstanceOf(StreamInterface::class, $stream);
    }

    #[Test]
    public function createStreamWithContent(): void
    {
        $stream = $this->factory->createStream('hello world');

        $this->assertSame('hello world', (string) $stream);
    }

    #[Test]
    public function createStreamWithEmptyContent(): void
    {
        $stream = $this->factory->createStream('');

        $this->assertSame('', (string) $stream);
    }

    #[Test]
    public function createStreamWithDefaultEmptyContent(): void
    {
        $stream = $this->factory->createStream();

        $this->assertSame('', (string) $stream);
    }

    #[Test]
    public function createStreamFromFileWithPhpTemp(): void
    {
        $stream = $this->factory->createStreamFromFile('php://temp', 'r+');

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
    }

    #[Test]
    public function createStreamFromFileReadMode(): void
    {
        $stream = $this->factory->createStreamFromFile('php://temp', 'r');

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertTrue($stream->isReadable());
    }

    #[Test]
    public function createStreamFromFileThrowsOnInvalidFile(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to open file');

        $this->factory->createStreamFromFile('/nonexistent/path/to/file.txt', 'r');
    }

    #[Test]
    public function createStreamFromResource(): void
    {
        $resource = fopen('php://temp', 'r+b');
        fwrite($resource, 'resource content');
        fseek($resource, 0);

        $stream = $this->factory->createStreamFromResource($resource);

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertSame('resource content', (string) $stream);
    }

    #[Test]
    public function createStreamFromResourcePreservesReadability(): void
    {
        $resource = fopen('php://temp', 'r+b');
        $stream = $this->factory->createStreamFromResource($resource);

        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());
    }

    #[Test]
    public function createStreamFromFileWriteMode(): void
    {
        $stream = $this->factory->createStreamFromFile('php://temp', 'w+');

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertTrue($stream->isWritable());
        $stream->write('test data');
        $stream->rewind();
        $this->assertSame('test data', $stream->getContents());
    }
}
