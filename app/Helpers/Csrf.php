<?php

declare(strict_types=1);

namespace App\Helpers;

class Csrf
{
    /**
     * Generate a new CSRF token and store it in session.
     */
    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('_csrf_token', $token);
        return $token;
    }

    /**
     * Get the current CSRF token, generating one if needed.
     */
    public static function token(): string
    {
        $token = Session::get('_csrf_token');
        if ($token === null) {
            $token = self::generate();
        }
        return $token;
    }

    /**
     * Validate a CSRF token against the session-stored token.
     */
    public static function validate(?string $token): bool
    {
        $stored = Session::get('_csrf_token');
        if ($stored === null || $token === null) {
            return false;
        }

        return hash_equals($stored, $token);
    }

    /**
     * Get a hidden HTML input field with the CSRF token.
     */
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . self::token() . '">';
    }

    /**
     * Regenerate the CSRF token (e.g., after login/logout).
     */
    public static function regenerate(): void
    {
        self::generate();
    }
}
