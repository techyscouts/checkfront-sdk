<?php

declare(strict_types=1);

namespace TechyScouts\Checkfront\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TechyScouts\Checkfront\Http\Message\Response;
use TechyScouts\Checkfront\Http\Message\Stream;

final class CurlHttpClient implements ClientInterface
{
    public function __construct(
        private int $timeout = 30,
        private int $connectTimeout = 10,
        private bool $verifySsl = true,
        private bool $followRedirects = true,
        private int $maxRedirects = 5,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $handle = curl_init();

        if ($handle === false) {
            throw new NetworkException('Unable to initialize cURL handle.', $request);
        }

        /** @var array<string, string[]> $responseHeaders */
        $responseHeaders = [];

        curl_setopt($handle, CURLOPT_URL, (string) $request->getUri());
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_HEADER, false);

        curl_setopt($handle, CURLOPT_HEADERFUNCTION, static function ($ch, string $headerLine) use (&$responseHeaders): int {
            $length = strlen($headerLine);
            $header = trim($headerLine);

            if ($header === '' || !str_contains($header, ':')) {
                return $length;
            }

            [$name, $value] = explode(':', $header, 2);
            $name = trim($name);
            $value = trim($value);

            if (!isset($responseHeaders[$name])) {
                $responseHeaders[$name] = [];
            }

            $responseHeaders[$name][] = $value;

            return $length;
        });

        $headers = [];
        foreach ($request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers[] = "{$name}: {$value}";
            }
        }

        if ($headers !== []) {
            curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        }

        $body = (string) $request->getBody();

        if ($body !== '') {
            curl_setopt($handle, CURLOPT_POSTFIELDS, $body);
        }

        curl_setopt($handle, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, $this->verifySsl);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, $this->verifySsl ? 2 : 0);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, $this->followRedirects);
        curl_setopt($handle, CURLOPT_MAXREDIRS, $this->maxRedirects);

        $responseBody = curl_exec($handle);

        if ($responseBody === false) {
            $error = curl_error($handle);
            curl_close($handle);

            throw new NetworkException("cURL error: {$error}", $request);
        }

        $statusCode = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);

        curl_close($handle);

        /** @var string $responseBody */
        $response = new Response(
            statusCode: $statusCode,
            headers: $responseHeaders,
            body: Stream::create($responseBody),
        );

        return $response;
    }
}
