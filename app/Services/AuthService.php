<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\Database;
use App\Helpers\Session;
use App\Helpers\Csrf;
use App\Exceptions\HttpException;

class AuthService extends BaseService
{
    private UserRepository $userRepo;
    private \PDO $db;

    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
        $this->db = Database::getInstance();
    }

    /**
     * Attempt to authenticate a user with email and password.
     */
    public function login(string $email, string $password): \stdClass
    {
        // Check brute force lockout before anything else
        $this->checkBruteForce($email);

        $user = $this->userRepo->findByEmail($email);

        if (!$user || !$user->is_active) {
            $this->recordFailedAttempt($email);
            throw HttpException::unauthorized('Credenciales inválidas.');
        }

        if (!password_verify($password, $user->password_hash)) {
            $this->recordFailedAttempt($email);
            throw HttpException::unauthorized('Credenciales inválidas.');
        }

        // Login success - clear any failed attempts
        $this->clearFailedAttempts($email);

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        Session::set('user_id', (int) $user->id);
        Session::set('user_name', $user->name);
        Session::set('user_email', $user->email);
        Session::set('user_role', $user->role);

        Csrf::regenerate();

        return $user;
    }

    /**
     * Log out the current user.
     */
    public function logout(): void
    {
        Session::destroy();
        Csrf::regenerate();
    }

    /**
     * Get the currently authenticated user's data.
     */
    public function currentUser(): ?\stdClass
    {
        $userId = Session::userId();
        if (!$userId) {
            return null;
        }

        return $this->userRepo->find($userId);
    }

    /**
     * Check if current user is an admin.
     */
    public function requireAdmin(): void
    {
        if (Session::userRole() !== 'admin') {
            throw HttpException::forbidden('Acceso denegado. Se requiere rol de administrador.');
        }
    }

    /**
     * Hash a password using Argon2id.
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3,
        ]);
    }

    /**
     * Check if the IP has been locked out due to too many failed attempts.
     */
    private function checkBruteForce(string $email): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Check by IP
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total FROM login_attempts 
            WHERE ip_address = :ip 
            AND attempted_at > DATE_SUB(NOW(), INTERVAL :minutes MINUTE)
            AND success = 0
        ");
        $stmt->execute(['ip' => $ip, 'minutes' => self::LOCKOUT_MINUTES]);
        $ipAttempts = (int) $stmt->fetch()->total;

        if ($ipAttempts >= self::MAX_ATTEMPTS) {
            throw new HttpException(
                'Demasiados intentos. Su IP ha sido bloqueada por ' . self::LOCKOUT_MINUTES . ' minutos.',
                429
            );
        }

        // Check by email (account lockout)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total FROM login_attempts 
            WHERE email = :email 
            AND attempted_at > DATE_SUB(NOW(), INTERVAL :minutes MINUTE)
            AND success = 0
        ");
        $stmt->execute(['email' => $email, 'minutes' => self::LOCKOUT_MINUTES]);
        $emailAttempts = (int) $stmt->fetch()->total;

        if ($emailAttempts >= self::MAX_ATTEMPTS) {
            throw new HttpException(
                'Demasiados intentos. Esta cuenta ha sido bloqueada temporalmente por ' . self::LOCKOUT_MINUTES . ' minutos.',
                429
            );
        }
    }

    /**
     * Record a failed login attempt.
     */
    private function recordFailedAttempt(string $email): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (email, ip_address, success) 
            VALUES (:email, :ip, 0)
        ");
        $stmt->execute([
            'email' => $email,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]);
    }

    /**
     * Clear failed attempts after successful login.
     */
    private function clearFailedAttempts(string $email): void
    {
        $stmt = $this->db->prepare("
            DELETE FROM login_attempts WHERE email = :email
        ");
        $stmt->execute(['email' => $email]);
    }
}
