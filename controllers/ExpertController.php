<?php

declare(strict_types=1);

class ExpertController extends Controller
{
    private Expert $expertModel;

    public function __construct()
    {
        $this->expertModel = new Expert();
    }

    public function index(): void
    {
        Auth::requireRole(['admin', 'expert', 'viewer']);
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $filters = [
            'keyword' => trim((string) ($_GET['keyword'] ?? '')),
            'skill' => trim((string) ($_GET['skill'] ?? '')),
        ];

        $result = $this->expertModel->paginate($filters, $page, 6);
        $this->view('experts/index', [
            'title' => 'รายการผู้เชี่ยวชาญ',
            'filters' => $filters,
            'result' => $result,
            'user' => Auth::user(),
        ]);
    }

    public function show(): void
    {
        Auth::requireRole(['admin', 'expert', 'viewer']);
        $id = (int) ($_GET['id'] ?? 0);
        $expert = $this->expertModel->find($id);
        if (!$expert) {
            flash('error', 'ไม่พบข้อมูลผู้เชี่ยวชาญ');
            redirect('experts');
        }
        $this->view('experts/show', ['title' => $expert['full_name'], 'expert' => $expert]);
    }

    public function create(): void
    {
        Auth::requireRole(['admin', 'expert']);
        $this->view('experts/form', ['title' => 'เพิ่มโปรไฟล์ผู้เชี่ยวชาญ', 'expert' => null]);
    }

    public function store(): void
    {
        Auth::requireRole(['admin', 'expert']);
        $this->ensureCsrf();
        $_SESSION['_old'] = $_POST;
        $payload = $this->validatedPayload();
        $payload['user_id'] = Auth::user()['id'];
        $payload['approval_status'] = Auth::user()['role'] === 'admin' ? 'approved' : 'pending';
        $this->expertModel->create($payload);
        flash('success', 'บันทึกโปรไฟล์ผู้เชี่ยวชาญเรียบร้อย');
        redirect('experts');
    }

    public function edit(): void
    {
        Auth::requireRole(['admin', 'expert']);
        $id = (int) ($_GET['id'] ?? 0);
        $expert = $this->expertModel->find($id);
        if (!$expert || !$this->canManage($expert)) {
            flash('error', 'คุณไม่มีสิทธิ์แก้ไขรายการนี้');
            redirect('experts');
        }
        $this->view('experts/form', ['title' => 'แก้ไขโปรไฟล์ผู้เชี่ยวชาญ', 'expert' => $expert]);
    }

    public function update(): void
    {
        Auth::requireRole(['admin', 'expert']);
        $this->ensureCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $expert = $this->expertModel->find($id);
        if (!$expert || !$this->canManage($expert)) {
            flash('error', 'คุณไม่มีสิทธิ์แก้ไขรายการนี้');
            redirect('experts');
        }
        $_SESSION['_old'] = $_POST;
        $payload = $this->validatedPayload($expert);
        $payload['approval_status'] = Auth::user()['role'] === 'admin' ? ($_POST['approval_status'] ?? 'approved') : 'pending';
        $this->expertModel->updateProfile($id, $payload);
        flash('success', 'อัปเดตโปรไฟล์เรียบร้อย');
        redirect('experts/show?id=' . $id);
    }

    public function delete(): void
    {
        Auth::requireRole(['admin']);
        $this->ensureCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $this->expertModel->deleteProfile($id);
        flash('success', 'ลบข้อมูลเรียบร้อย');
        redirect('experts');
    }

    public function approve(): void
    {
        Auth::requireRole(['admin']);
        $this->ensureCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $this->expertModel->approve($id);
        flash('success', 'อนุมัติโปรไฟล์เรียบร้อย');
        redirect('experts/show?id=' . $id);
    }

    public function exportPdf(): void
    {
        Auth::requireRole(['admin', 'expert', 'viewer']);
        $id = (int) ($_GET['id'] ?? 0);
        $expert = $this->expertModel->find($id);
        if (!$expert) {
            flash('error', 'ไม่พบข้อมูลสำหรับส่งออก');
            redirect('experts');
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="expert-profile-' . $id . '.pdf"');
        echo "%PDF-1.4\n";
        echo "1 0 obj<<>>endobj\n";
        echo "2 0 obj<< /Length 66 >>stream\nBT /F1 18 Tf 40 760 Td (Expert Profile: " . addslashes($expert['full_name']) . ") Tj ET\nendstream endobj\n";
        echo "3 0 obj<< /Type /Page /Parent 4 0 R /Contents 2 0 R >>endobj\n";
        echo "4 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n";
        echo "5 0 obj<< /Type /Catalog /Pages 4 0 R >>endobj\n";
        echo "xref\n0 6\n0000000000 65535 f \ntrailer<< /Root 5 0 R /Size 6 >>\nstartxref\n256\n%%EOF";
    }

    private function validatedPayload(?array $existing = null): array
    {
        $fullName = trim((string) ($_POST['full_name'] ?? ''));
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if ($fullName === '' || !$email) {
            flash('error', 'กรุณากรอกชื่อและอีเมลให้ถูกต้อง');
            redirect($_SERVER['HTTP_REFERER'] ?? 'experts');
        }

        return [
            'full_name' => $fullName,
            'position_title' => trim((string) ($_POST['position_title'] ?? '')),
            'department' => trim((string) ($_POST['department'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'email' => $email,
            'profile_image' => $this->handleUpload('profile_image', 'uploads/profile', ['image/jpeg', 'image/png', 'image/webp'], $existing['profile_image'] ?? null),
            'resume_file' => $this->handleUpload('resume_file', 'uploads/resume', ['application/pdf'], $existing['resume_file'] ?? null),
            'expertise_summary' => trim((string) ($_POST['expertise_summary'] ?? '')),
            'portfolio_url' => trim((string) ($_POST['portfolio_url'] ?? '')),
            'skills' => array_values(array_filter(array_map('trim', explode(',', (string) ($_POST['skills'] ?? ''))))),
            'work_experience' => $this->normalizeRepeater('work_experience'),
            'research' => $this->normalizeRepeater('research'),
            'training' => $this->normalizeRepeater('training'),
            'awards' => $this->normalizeRepeater('awards'),
            'social_links' => $this->normalizeRepeater('social_links'),
        ];
    }

    private function normalizeRepeater(string $key): array
    {
        $rows = $_POST[$key] ?? [];
        return is_array($rows) ? array_values($rows) : [];
    }

    private function handleUpload(string $field, string $directory, array $allowedMimeTypes, ?string $fallback = null): ?string
    {
        if (empty($_FILES[$field]['name'])) {
            return $fallback;
        }

        $file = $_FILES[$field];
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            flash('error', 'อัปโหลดไฟล์ไม่สำเร็จ');
            redirect($_SERVER['HTTP_REFERER'] ?? 'experts');
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $allowedMimeTypes, true)) {
            flash('error', 'ชนิดไฟล์ไม่ถูกต้อง');
            redirect($_SERVER['HTTP_REFERER'] ?? 'experts');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid($field . '-', true) . '.' . strtolower($extension);
        $targetDir = BASE_PATH . '/' . trim($directory, '/');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }
        $target = $targetDir . '/' . $filename;
        move_uploaded_file($file['tmp_name'], $target);

        return trim($directory, '/') . '/' . $filename;
    }

    private function canManage(array $expert): bool
    {
        return Auth::user()['role'] === 'admin' || (int) Auth::user()['id'] === (int) $expert['user_id'];
    }
}
