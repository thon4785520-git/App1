<?php

declare(strict_types=1);

session_start();

$config = require __DIR__ . '/../config/app.php';

function app_config(?string $key = null)
{
    global $config;
    if ($key === null) {
        return $config;
    }

    $value = $config;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return null;
        }
        $value = $value[$segment];
    }

    return $value;
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        app_config('db.host'),
        app_config('db.port'),
        app_config('db.database'),
        app_config('db.charset')
    );

    $pdo = new PDO($dsn, (string) app_config('db.username'), (string) app_config('db.password'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function base_url(string $path = ''): string
{
    return rtrim((string) app_config('base_url'), '/') . '/' . ltrim($path, '/');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = (string) ($_POST['csrf_token'] ?? '');
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('CSRF token mismatch');
    }
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function old(string $key, string $default = ''): string
{
    return $_SESSION['old'][$key] ?? $default;
}

function remember_old_input(array $input): void
{
    $_SESSION['old'] = $input;
}

function clear_old_input(): void
{
    unset($_SESSION['old']);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        redirect('login.php');
    }
}

function require_role(array $roles): void
{
    require_login();

    if (!in_array(current_user()['role'], $roles, true)) {
        flash('error', 'คุณไม่มีสิทธิ์เข้าถึงเมนูนี้');
        redirect('dashboard.php');
    }
}

function login_user(array $user): void
{
    $_SESSION['user'] = $user;
    session_regenerate_id(true);
}

function logout_user(): void
{
    unset($_SESSION['user']);
    session_regenerate_id(true);
}

function paginate_meta(int $total, int $page, int $perPage): array
{
    return [
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'last_page' => max(1, (int) ceil($total / $perPage)),
    ];
}

function save_upload(string $field, string $folder, array $allowedMimeTypes, ?string $currentValue = null): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return $currentValue;
    }

    if ((int) $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('อัปโหลดไฟล์ไม่สำเร็จ');
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES[$field]['tmp_name']);
    if (!in_array($mime, $allowedMimeTypes, true)) {
        throw new RuntimeException('ประเภทไฟล์ไม่ถูกต้อง');
    }

    $extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    $filename = uniqid($field . '-', true) . '.' . $extension;
    $relativePath = 'uploads/' . trim($folder, '/') . '/' . $filename;
    $absolutePath = __DIR__ . '/../public/' . $relativePath;

    $dir = dirname($absolutePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $absolutePath)) {
        throw new RuntimeException('บันทึกไฟล์ไม่สำเร็จ');
    }

    return $relativePath;
}
