<?php

declare(strict_types=1);

namespace App\Http;

final class Response
{
    public function __construct(
        private readonly string $body,
        private readonly int $statusCode = 200,
        private readonly array $headers = ['Content-Type' => 'text/html; charset=UTF-8'],
    ) {
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->body;
    }

    public static function html(string $body, int $status = 200): self
    {
        return new self($body, $status);
    }

    public static function notFound(string $body): self
    {
        return new self($body, 404);
    }
}
