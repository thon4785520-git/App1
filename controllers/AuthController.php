<?php

declare(strict_types=1);

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', ['title' => 'เข้าสู่ระบบ'], 'guest');
    }

    public function login(): void
    {
        $this->ensureCsrf();
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = (string) ($_POST['password'] ?? '');
        $_SESSION['_old'] = ['email' => $_POST['email'] ?? ''];

        if (!$email || $password === '') {
            flash('error', 'กรุณากรอกอีเมลและรหัสผ่านให้ถูกต้อง');
            redirect('login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'อีเมลหรือรหัสผ่านไม่ถูกต้อง');
            redirect('login');
        }

        Auth::attempt($user);
        flash('success', 'เข้าสู่ระบบสำเร็จ');
        redirect('dashboard');
    }

    public function showRegister(): void
    {
        $this->view('auth/register', ['title' => 'ลงทะเบียน'], 'guest');
    }

    public function register(): void
    {
        $this->ensureCsrf();
        $_SESSION['_old'] = $_POST;

        $data = [
            'full_name' => trim((string) ($_POST['full_name'] ?? '')),
            'email' => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL),
            'password' => (string) ($_POST['password'] ?? ''),
            'role' => in_array($_POST['role'] ?? 'viewer', ['expert', 'viewer'], true) ? $_POST['role'] : 'viewer',
        ];

        if ($data['full_name'] === '' || !$data['email'] || strlen($data['password']) < 8) {
            flash('error', 'ข้อมูลไม่ครบถ้วน หรือรหัสผ่านต้องยาวอย่างน้อย 8 ตัวอักษร');
            redirect('register');
        }

        $userModel = new User();
        if ($userModel->findByEmail($data['email'])) {
            flash('error', 'อีเมลนี้ถูกใช้งานแล้ว');
            redirect('register');
        }

        $id = $userModel->create($data);
        $user = $userModel->findByEmail($data['email']);
        Auth::attempt($user + ['id' => $id]);
        flash('success', 'ลงทะเบียนสำเร็จ กรุณาเพิ่มโปรไฟล์ผู้เชี่ยวชาญ');
        redirect('experts/create');
    }

    public function logout(): void
    {
        $this->ensureCsrf();
        Auth::logout();
        flash('success', 'ออกจากระบบเรียบร้อยแล้ว');
        redirect('login');
    }
}
