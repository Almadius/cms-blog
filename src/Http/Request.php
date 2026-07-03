<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    /**
     * @param array<string, string> $routeParams
     */
    public function __construct(
        private readonly string $method,
        private readonly string $uri,
        private readonly array $query,
        private array $routeParams = [],
    ) {
    }

    public static function fromGlobals(): self
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        return new self(
            method: strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            uri: rtrim($uri, '/') ?: '/',
            query: $_GET,
        );
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getQuery(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function getIntQuery(string $key, int $default = 1): int
    {
        $value = $this->query[$key] ?? $default;

        return max(1, (int) $value);
    }

    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function getRouteParam(string $key, ?string $default = null): ?string
    {
        return $this->routeParams[$key] ?? $default;
    }
}
