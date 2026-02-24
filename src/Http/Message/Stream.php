<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Http\Message;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

final class Stream implements StreamInterface
{
    /** @var array<string, array<string, bool>> */
    private const array READ_WRITE_MAP = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true, 'c+b' => true,
            'rt' => true, 'w+t' => true, 'r+t' => true, 'x+t' => true, 'c+t' => true,
            'a+' => true, 'a+b' => true, 'a+t' => true,
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'wb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true, 'c+b' => true,
            'wt' => true, 'w+t' => true, 'r+t' => true, 'x+t' => true, 'c+t' => true,
            'a' => true, 'a+' => true, 'ab' => true, 'a+b' => true, 'at' => true, 'a+t' => true,
        ],
    ];

    /** @var resource|null */
    private $stream;

    private bool $seekable;
    private bool $readable;
    private bool $writable;
    private ?int $size = null;

    /**
     * @param resource $stream
     */
    public function __construct($stream)
    {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException('Stream must be a valid PHP resource.');
        }

        $this->stream = $stream;

        $meta = stream_get_meta_data($stream);
        $this->seekable = $meta['seekable'];
        $this->readable = isset(self::READ_WRITE_MAP['read'][$meta['mode']]);
        $this->writable = isset(self::READ_WRITE_MAP['write'][$meta['mode']]);
    }

    /**
     * Create a stream from a string.
     */
    public static function create(string $content): self
    {
        $resource = fopen('php://temp', 'r+b');

        if ($resource === false) {
            throw new RuntimeException('Unable to open php://temp stream.');
        }

        if ($content !== '') {
            fwrite($resource, $content);
            fseek($resource, 0);
        }

        return new self($resource);
    }

    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }

            return $this->getContents();
        } catch (\Throwable) {
            return '';
        }
    }

    public function close(): void
    {
        if ($this->stream !== null) {
            $resource = $this->detach();

            if ($resource !== null) {
                fclose($resource);
            }
        }
    }

    /**
     * @return resource|null
     */
    public function detach()
    {
        if ($this->stream === null) {
            return null;
        }

        $resource = $this->stream;
        $this->stream = null;
        $this->size = null;
        $this->seekable = false;
        $this->readable = false;
        $this->writable = false;

        return $resource;
    }

    public function getSize(): ?int
    {
        if ($this->stream === null) {
            return null;
        }

        if ($this->size !== null) {
            return $this->size;
        }

        $stats = fstat($this->stream);

        if ($stats !== false) {
            $this->size = $stats['size'];

            return $this->size;
        }

        return null;
    }

    public function tell(): int
    {
        $this->ensureStream();

        $position = ftell($this->stream);

        if ($position === false) {
            throw new RuntimeException('Unable to determine stream position.');
        }

        return $position;
    }

    public function eof(): bool
    {
        if ($this->stream === null) {
            return true;
        }

        return feof($this->stream);
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        $this->ensureStream();

        if (!$this->seekable) {
            throw new RuntimeException('Stream is not seekable.');
        }

        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException("Unable to seek to stream position {$offset} with whence {$whence}.");
        }

        $this->size = null;
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    public function write(string $string): int
    {
        $this->ensureStream();

        if (!$this->writable) {
            throw new RuntimeException('Stream is not writable.');
        }

        $this->size = null;

        $result = fwrite($this->stream, $string);

        if ($result === false) {
            throw new RuntimeException('Unable to write to stream.');
        }

        return $result;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function read(int $length): string
    {
        $this->ensureStream();

        if (!$this->readable) {
            throw new RuntimeException('Stream is not readable.');
        }

        if ($length < 0) {
            throw new RuntimeException('Length parameter must not be negative.');
        }

        if ($length === 0) {
            return '';
        }

        $result = fread($this->stream, $length);

        if ($result === false) {
            throw new RuntimeException('Unable to read from stream.');
        }

        return $result;
    }

    public function getContents(): string
    {
        $this->ensureStream();

        if (!$this->readable) {
            throw new RuntimeException('Stream is not readable.');
        }

        $contents = stream_get_contents($this->stream);

        if ($contents === false) {
            throw new RuntimeException('Unable to read stream contents.');
        }

        return $contents;
    }

    public function getMetadata(?string $key = null): mixed
    {
        if ($this->stream === null) {
            return $key !== null ? null : [];
        }

        $meta = stream_get_meta_data($this->stream);

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }

    /**
     * @phpstan-assert !null $this->stream
     */
    private function ensureStream(): void
    {
        if ($this->stream === null) {
            throw new RuntimeException('Stream is detached.');
        }
    }
}
