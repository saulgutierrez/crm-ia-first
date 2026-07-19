<?php

declare(strict_types=1);

namespace App\Helpers;

class Session
{
    /**
     * Start or resume a session.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set a session value.
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists.
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session key.
     */
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the session entirely.
     */
    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Set a flash message that will be available for the next request only.
     */
    public static function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    /**
     * Get and clear a flash message.
     */
    public static function getFlash(string $key, string $default = ''): string
    {
        $message = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $message;
    }

    /**
     * Check if user is authenticated.
     */
    public static function isAuthenticated(): bool
    {
        return self::has('user_id');
    }

    /**
     * Get the authenticated user's ID.
     */
    public static function userId(): ?int
    {
        return self::get('user_id');
    }

    /**
     * Get the authenticated user's role.
     */
    public static function userRole(): ?string
    {
        return self::get('user_role');
    }

    /**
     * Check if the authenticated user has a specific role.
     */
    public static function isRole(string $role): bool
    {
        return self::userRole() === $role;
    }
}
