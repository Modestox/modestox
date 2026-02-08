<?php

/**
 * Modestox CMS - E-commerce Platform
 *
 * @copyright Copyright (c) 2026 Sergey Kuzmitsky
 * @license   AGPL-3.0-or-later
 * @link      https://github.com/Modestox/modestox
 */

declare(strict_types=1);

namespace Core\Http\Exceptions;

use Exception;
use Throwable;

/**
 * Class HttpException
 * Base exception for all HTTP-related errors in Modestox Core.
 */
class HttpException extends Exception
{
    public function __construct(
        string $message,
        protected readonly int $statusCode = 500,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public static function badRequest(string $msg = "Bad Request"): self { return new self($msg, 400); }
    public static function unauthorized(string $msg = "Unauthorized"): self { return new self($msg, 401); }
    public static function forbidden(string $msg = "Forbidden"): self { return new self($msg, 403); }
    public static function notFound(string $msg = "Not Found"): self { return new self($msg, 404); }
    public static function methodNotAllowed(string $msg = "Method Not Allowed"): self { return new self($msg, 405); }
    public static function unprocessable(string $msg = "Validation Error"): self { return new self($msg, 422); }
    public static function tooManyRequests(string $msg = "Too Many Requests"): self { return new self($msg, 429); }
    public static function internalError(string $msg = "Internal Server Error"): self { return new self($msg, 500); }
    public static function unavailable(string $msg = "Service Unavailable"): self { return new self($msg, 503); }
}