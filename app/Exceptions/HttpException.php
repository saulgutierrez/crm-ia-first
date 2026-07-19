<?php

declare(strict_types=1);

namespace App\Exceptions;

class HttpException extends \RuntimeException
{
    private int $statusCode;

    public function __construct(string $message = 'Internal Server Error', int $statusCode = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Create a 404 Not Found exception.
     */
    public static function notFound(string $message = 'Resource not found'): self
    {
        return new self($message, 404);
    }

    /**
     * Create a 401 Unauthorized exception.
     */
    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return new self($message, 401);
    }

    /**
     * Create a 403 Forbidden exception.
     */
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return new self($message, 403);
    }

    /**
     * Create a 422 Unprocessable Entity exception.
     */
    public static function validationError(string $message = 'Validation failed'): self
    {
        return new self($message, 422);
    }

    /**
     * Create a 409 Conflict exception.
     */
    public static function conflict(string $message = 'Resource already exists'): self
    {
        return new self($message, 409);
    }
}
