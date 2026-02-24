<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Tests\Unit\Http\Message;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use TechyScouts\Checkfront\Http\Message\Stream;

#[CoversClass(Stream::class)]
final class StreamTest extends TestCase
{
    #[Test]
    public function createFromStringReturnsStreamWithContent(): void
    {
        $stream = Stream::create('hello world');

        $this->assertSame('hello world', (string) $stream);
    }

    #[Test]
    public function createFromEmptyString(): void
    {
        $stream = Stream::create('');

        $this->assertSame('', (string) $stream);
        $this->assertSame(0, $stream->getSize());
    }

    #[Test]
    public function constructorRequiresValidResource(): void
    {
        $this->expectException(InvalidArgumentException::class);

        /** @phpstan-ignore argument.type */
        new Stream('not a resource');
    }

    #[Test]
    public function constructorWithValidResource(): void
    {
        $resource = fopen('php://temp', 'r+b');
        fwrite($resource, 'test data');
        fseek($resource, 0);

        $stream = new Stream($resource);

        $this->assertSame('test data', (string) $stream);
    }

    #[Test]
    public function toStringRewindsBeforeReading(): void
    {
        $stream = Stream::create('hello');
        $stream->read(3);

        $this->assertSame('hello', (string) $stream);
    }

    #[Test]
    public function toStringReturnsEmptyStringOnError(): void
    {
        $stream = Stream::create('hello');
        $stream->detach();

        $this->assertSame('', (string) $stream);
    }

    #[Test]
    public function readReturnsRequestedBytes(): void
    {
        $stream = Stream::create('hello world');

        $this->assertSame('hello', $stream->read(5));
        $this->assertSame(' ', $stream->read(1));
        $this->assertSame('world', $stream->read(5));
    }

    #[Test]
    public function readReturnsEmptyStringForZeroLength(): void
    {
        $stream = Stream::create('hello');

        $this->assertSame('', $stream->read(0));
    }

    #[Test]
    public function readThrowsOnNegativeLength(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Length parameter must not be negative');

        $stream = Stream::create('hello');
        $stream->read(-1);
    }

    #[Test]
    public function readThrowsWhenDetached(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is detached');

        $stream = Stream::create('hello');
        $stream->detach();
        $stream->read(1);
    }

    #[Test]
    public function writeAppendsToStream(): void
    {
        $stream = Stream::create('');
        $written = $stream->write('hello');

        $this->assertSame(5, $written);
        $this->assertSame('hello', (string) $stream);
    }

    #[Test]
    public function writeThrowsWhenDetached(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is detached');

        $stream = Stream::create('');
        $stream->detach();
        $stream->write('test');
    }

    #[Test]
    public function writeThrowsOnReadOnlyStream(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is not writable');

        $resource = fopen('php://temp', 'r');
        $stream = new Stream($resource);
        $stream->write('test');
    }

    #[Test]
    public function getContentsReturnsRemainingContent(): void
    {
        $stream = Stream::create('hello world');
        $stream->read(6);

        $this->assertSame('world', $stream->getContents());
    }

    #[Test]
    public function getContentsThrowsWhenDetached(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is detached');

        $stream = Stream::create('hello');
        $stream->detach();
        $stream->getContents();
    }

    #[Test]
    public function seekMovesToPosition(): void
    {
        $stream = Stream::create('hello world');
        $stream->seek(6);

        $this->assertSame('world', $stream->getContents());
    }

    #[Test]
    public function seekThrowsWhenDetached(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is detached');

        $stream = Stream::create('hello');
        $stream->detach();
        $stream->seek(0);
    }

    #[Test]
    public function rewindMovesToBeginning(): void
    {
        $stream = Stream::create('hello world');
        $stream->read(6);
        $stream->rewind();

        $this->assertSame('hello world', $stream->getContents());
    }

    #[Test]
    public function getSizeReturnsContentLength(): void
    {
        $stream = Stream::create('hello world');

        $this->assertSame(11, $stream->getSize());
    }

    #[Test]
    public function getSizeReturnsNullAfterDetach(): void
    {
        $stream = Stream::create('hello');
        $stream->detach();

        $this->assertNull($stream->getSize());
    }

    #[Test]
    public function getSizeCachesResult(): void
    {
        $stream = Stream::create('hello');

        $this->assertSame(5, $stream->getSize());
        $this->assertSame(5, $stream->getSize());
    }

    #[Test]
    public function writeInvalidatesSizeCache(): void
    {
        $stream = Stream::create('hello');
        $this->assertSame(5, $stream->getSize());

        // After Stream::create, cursor is at position 0 (rewound).
        // Seek to end before writing to append.
        $stream->seek(0, SEEK_END);
        $stream->write(' world');
        $this->assertSame(11, $stream->getSize());
    }

    #[Test]
    public function eofReturnsTrueAtEndOfStream(): void
    {
        $stream = Stream::create('hi');
        $stream->read(2);

        // feof requires one more read attempt to trigger true
        $stream->read(1);
        $this->assertTrue($stream->eof());
    }

    #[Test]
    public function eofReturnsFalseBeforeEndOfStream(): void
    {
        $stream = Stream::create('hello');

        $this->assertFalse($stream->eof());
    }

    #[Test]
    public function eofReturnsTrueAfterDetach(): void
    {
        $stream = Stream::create('hello');
        $stream->detach();

        $this->assertTrue($stream->eof());
    }

    #[Test]
    public function detachReturnsUnderlyingResource(): void
    {
        $stream = Stream::create('hello');
        $resource = $stream->detach();

        $this->assertIsResource($resource);
    }

    #[Test]
    public function detachReturnsNullOnSubsequentCalls(): void
    {
        $stream = Stream::create('hello');
        $stream->detach();

        $this->assertNull($stream->detach());
    }

    #[Test]
    public function detachMakesStreamUnreadable(): void
    {
        $stream = Stream::create('hello');
        $this->assertTrue($stream->isReadable());

        $stream->detach();
        $this->assertFalse($stream->isReadable());
    }

    #[Test]
    public function detachMakesStreamUnwritable(): void
    {
        $stream = Stream::create('hello');
        $this->assertTrue($stream->isWritable());

        $stream->detach();
        $this->assertFalse($stream->isWritable());
    }

    #[Test]
    public function detachMakesStreamUnseekable(): void
    {
        $stream = Stream::create('hello');
        $this->assertTrue($stream->isSeekable());

        $stream->detach();
        $this->assertFalse($stream->isSeekable());
    }

    #[Test]
    public function tellReturnsCurrentPosition(): void
    {
        $stream = Stream::create('hello world');
        $stream->read(5);

        $this->assertSame(5, $stream->tell());
    }

    #[Test]
    public function tellThrowsWhenDetached(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is detached');

        $stream = Stream::create('hello');
        $stream->detach();
        $stream->tell();
    }

    #[Test]
    public function closeReleasesResource(): void
    {
        $stream = Stream::create('hello');
        $stream->close();

        $this->assertNull($stream->detach());
        $this->assertTrue($stream->eof());
    }

    #[Test]
    public function isReadableReturnsTrueForReadableStream(): void
    {
        $stream = Stream::create('hello');

        $this->assertTrue($stream->isReadable());
    }

    #[Test]
    public function isWritableReturnsTrueForWritableStream(): void
    {
        $stream = Stream::create('hello');

        $this->assertTrue($stream->isWritable());
    }

    #[Test]
    public function isSeekableReturnsTrueForSeekableStream(): void
    {
        $stream = Stream::create('hello');

        $this->assertTrue($stream->isSeekable());
    }

    #[Test]
    public function getMetadataReturnsAllMetadata(): void
    {
        $stream = Stream::create('hello');
        $meta = $stream->getMetadata();

        $this->assertIsArray($meta);
        $this->assertArrayHasKey('mode', $meta);
        $this->assertArrayHasKey('seekable', $meta);
    }

    #[Test]
    public function getMetadataWithKeyReturnsSpecificValue(): void
    {
        $stream = Stream::create('hello');

        $this->assertTrue($stream->getMetadata('seekable'));
    }

    #[Test]
    public function getMetadataWithInvalidKeyReturnsNull(): void
    {
        $stream = Stream::create('hello');

        $this->assertNull($stream->getMetadata('nonexistent_key'));
    }

    #[Test]
    public function getMetadataReturnsNullForKeyWhenDetached(): void
    {
        $stream = Stream::create('hello');
        $stream->detach();

        $this->assertNull($stream->getMetadata('mode'));
    }

    #[Test]
    public function getMetadataReturnsEmptyArrayWhenDetached(): void
    {
        $stream = Stream::create('hello');
        $stream->detach();

        $this->assertSame([], $stream->getMetadata());
    }

    #[Test]
    public function seekResetsSize(): void
    {
        $stream = Stream::create('hello');
        $this->assertSame(5, $stream->getSize());

        $stream->seek(0);
        // After seek, size cache is cleared; next call recalculates
        $this->assertSame(5, $stream->getSize());
    }

    #[Test]
    public function readThrowsOnNonReadableStream(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is not readable');

        $resource = fopen('php://output', 'w');
        $stream = new Stream($resource);
        $stream->read(1);
    }

    #[Test]
    public function seekOnNonSeekableStreamThrows(): void
    {
        $stream = Stream::create('hello');
        $stream->detach();

        $resource = fopen('php://output', 'w');
        $nonSeekable = new Stream($resource);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Stream is not seekable');
        $nonSeekable->seek(0);
    }
}
