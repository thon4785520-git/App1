<?php

declare(strict_types=1);

class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function attempt(array $user): void
    {
        $_SESSION['user'] = $user;
        session_regenerate_id(true);
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    public static function requireRole(array $roles): void
    {
        if (!self::check() || !in_array(self::user()['role'], $roles, true)) {
            flash('error', 'คุณไม่มีสิทธิ์เข้าถึงเมนูนี้');
            redirect('login');
        }
    }
}
