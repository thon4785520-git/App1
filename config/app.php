<?php

declare(strict_types=1);

const APP_NAME = 'ระบบฐานข้อมูลผู้เชี่ยวชาญ มหาวิทยาลัยราชภัฏสงขลา';
const APP_URL = 'http://localhost';
const BASE_PATH = __DIR__ . '/..';

return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'expert_directory',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
];
