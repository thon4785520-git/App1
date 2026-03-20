<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function find_user_by_email(string $email): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    return $stmt->fetch() ?: null;
}

function create_user(array $data): int
{
    $stmt = db()->prepare('INSERT INTO users (full_name, email, password_hash, role, created_at) VALUES (:full_name, :email, :password_hash, :role, NOW())');
    $stmt->execute([
        'full_name' => $data['full_name'],
        'email' => $data['email'],
        'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        'role' => $data['role'],
    ]);

    return (int) db()->lastInsertId();
}

function dashboard_summary(): array
{
    return [
        'experts' => (int) db()->query('SELECT COUNT(*) FROM experts')->fetchColumn(),
        'approved' => (int) db()->query("SELECT COUNT(*) FROM experts WHERE approval_status = 'approved'")->fetchColumn(),
        'pending' => (int) db()->query("SELECT COUNT(*) FROM experts WHERE approval_status = 'pending'")->fetchColumn(),
        'research' => (int) db()->query('SELECT COUNT(*) FROM research')->fetchColumn(),
    ];
}

function latest_experts(int $limit = 4): array
{
    $stmt = db()->prepare("SELECT e.*, GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ') AS skills
        FROM experts e
        LEFT JOIN expert_skill es ON es.expert_id = e.id
        LEFT JOIN skills s ON s.id = es.skill_id
        GROUP BY e.id
        ORDER BY e.updated_at DESC
        LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function search_experts(array $filters, int $page, int $perPage): array
{
    $conditions = ['1=1'];
    $params = [];

    if ($filters['keyword'] !== '') {
        $conditions[] = '(e.full_name LIKE :keyword OR e.position_title LIKE :keyword OR e.department LIKE :keyword OR e.expertise_summary LIKE :keyword)';
        $params['keyword'] = '%' . $filters['keyword'] . '%';
    }

    if ($filters['skill'] !== '') {
        $conditions[] = 'EXISTS (
            SELECT 1 FROM expert_skill es2
            JOIN skills s2 ON s2.id = es2.skill_id
            WHERE es2.expert_id = e.id AND s2.name LIKE :skill
        )';
        $params['skill'] = '%' . $filters['skill'] . '%';
    }

    $where = implode(' AND ', $conditions);
    $offset = ($page - 1) * $perPage;

    $countStmt = db()->prepare("SELECT COUNT(*) FROM experts e WHERE {$where}");
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $sql = "SELECT e.*, GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ') AS skills
        FROM experts e
        LEFT JOIN expert_skill es ON es.expert_id = e.id
        LEFT JOIN skills s ON s.id = es.skill_id
        WHERE {$where}
        GROUP BY e.id
        ORDER BY e.updated_at DESC, e.id DESC
        LIMIT :limit OFFSET :offset";
    $stmt = db()->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return [
        'items' => $stmt->fetchAll(),
        'meta' => paginate_meta($total, $page, $perPage),
    ];
}

function find_expert(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM experts WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $expert = $stmt->fetch();
    if (!$expert) {
        return null;
    }

    $expert['skills'] = expert_skills($id);
    $expert['work_experience'] = fetch_rows('work_experience', $id);
    $expert['research'] = fetch_rows('research', $id);
    $expert['training'] = fetch_rows('training', $id);
    $expert['seminars'] = fetch_rows('seminars', $id);
    $expert['awards'] = fetch_rows('awards', $id);
    $expert['social_links'] = fetch_rows('social_links', $id);

    return $expert;
}

function fetch_rows(string $table, int $expertId): array
{
    $stmt = db()->prepare("SELECT * FROM {$table} WHERE expert_id = :expert_id ORDER BY id DESC");
    $stmt->execute(['expert_id' => $expertId]);
    return $stmt->fetchAll();
}

function expert_skills(int $expertId): array
{
    $stmt = db()->prepare('SELECT s.id, s.name FROM expert_skill es JOIN skills s ON s.id = es.skill_id WHERE es.expert_id = :expert_id ORDER BY s.name');
    $stmt->execute(['expert_id' => $expertId]);
    return $stmt->fetchAll();
}

function expert_owner_can_edit(array $expert): bool
{
    $user = current_user();
    return $user !== null && ($user['role'] === 'admin' || (int) $user['id'] === (int) $expert['user_id']);
}

function validate_expert_form(array $post, array $files, ?array $currentExpert = null): array
{
    $fullName = trim((string) ($post['full_name'] ?? ''));
    $email = filter_var((string) ($post['email'] ?? ''), FILTER_VALIDATE_EMAIL);

    if ($fullName === '' || !$email) {
        throw new InvalidArgumentException('กรุณากรอกชื่อ-นามสกุล และอีเมลให้ถูกต้อง');
    }

    $profileImage = save_upload('profile_image', 'profile', ['image/jpeg', 'image/png', 'image/webp'], $currentExpert['profile_image'] ?? null);
    $resumeFile = save_upload('resume_file', 'resume', ['application/pdf'], $currentExpert['resume_file'] ?? null);

    return [
        'full_name' => $fullName,
        'position_title' => trim((string) ($post['position_title'] ?? '')),
        'department' => trim((string) ($post['department'] ?? '')),
        'phone' => trim((string) ($post['phone'] ?? '')),
        'email' => $email,
        'profile_image' => $profileImage,
        'resume_file' => $resumeFile,
        'expertise_summary' => trim((string) ($post['expertise_summary'] ?? '')),
        'portfolio_url' => trim((string) ($post['portfolio_url'] ?? '')),
        'approval_status' => (current_user()['role'] ?? 'expert') === 'admin' ? (string) ($post['approval_status'] ?? 'pending') : 'pending',
        'skills' => array_values(array_filter(array_map('trim', explode(',', (string) ($post['skills'] ?? ''))))),
        'work_experience' => normalise_rows($post['work_experience'] ?? []),
        'research' => normalise_rows($post['research'] ?? []),
        'training' => normalise_rows($post['training'] ?? []),
        'seminars' => normalise_rows($post['seminars'] ?? []),
        'awards' => normalise_rows($post['awards'] ?? []),
        'social_links' => normalise_rows($post['social_links'] ?? []),
    ];
}

function normalise_rows($rows): array
{
    return is_array($rows) ? array_values($rows) : [];
}

function insert_expert(array $data): int
{
    $stmt = db()->prepare('INSERT INTO experts (user_id, full_name, position_title, department, phone, email, profile_image, resume_file, expertise_summary, portfolio_url, approval_status, created_at, updated_at) VALUES (:user_id, :full_name, :position_title, :department, :phone, :email, :profile_image, :resume_file, :expertise_summary, :portfolio_url, :approval_status, NOW(), NOW())');
    $stmt->execute([
        'user_id' => $data['user_id'],
        'full_name' => $data['full_name'],
        'position_title' => $data['position_title'],
        'department' => $data['department'],
        'phone' => $data['phone'],
        'email' => $data['email'],
        'profile_image' => $data['profile_image'],
        'resume_file' => $data['resume_file'],
        'expertise_summary' => $data['expertise_summary'],
        'portfolio_url' => $data['portfolio_url'],
        'approval_status' => $data['approval_status'],
    ]);

    $expertId = (int) db()->lastInsertId();
    sync_expert_relations($expertId, $data);
    return $expertId;
}

function update_expert(int $id, array $data): void
{
    $stmt = db()->prepare('UPDATE experts SET full_name=:full_name, position_title=:position_title, department=:department, phone=:phone, email=:email, profile_image=:profile_image, resume_file=:resume_file, expertise_summary=:expertise_summary, portfolio_url=:portfolio_url, approval_status=:approval_status, updated_at=NOW() WHERE id=:id');
    $stmt->execute([
        'id' => $id,
        'full_name' => $data['full_name'],
        'position_title' => $data['position_title'],
        'department' => $data['department'],
        'phone' => $data['phone'],
        'email' => $data['email'],
        'profile_image' => $data['profile_image'],
        'resume_file' => $data['resume_file'],
        'expertise_summary' => $data['expertise_summary'],
        'portfolio_url' => $data['portfolio_url'],
        'approval_status' => $data['approval_status'],
    ]);

    sync_expert_relations($id, $data);
}

function delete_expert(int $id): void
{
    foreach (['expert_skill', 'social_links', 'awards', 'seminars', 'training', 'research', 'work_experience'] as $table) {
        $stmt = db()->prepare("DELETE FROM {$table} WHERE expert_id = :expert_id");
        $stmt->execute(['expert_id' => $id]);
    }

    $stmt = db()->prepare('DELETE FROM experts WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function approve_expert(int $id): void
{
    $stmt = db()->prepare("UPDATE experts SET approval_status = 'approved', updated_at = NOW() WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

function sync_expert_relations(int $expertId, array $data): void
{
    replace_rows('work_experience', $expertId, $data['work_experience'], ['organization', 'project_name', 'role_title', 'start_date', 'end_date', 'description']);
    replace_rows('research', $expertId, $data['research'], ['category', 'title', 'publication_name', 'published_year', 'description', 'link_url']);
    replace_rows('training', $expertId, $data['training'], ['course_name', 'provider_name', 'certificate_name', 'certificate_file', 'start_date', 'end_date', 'description']);
    replace_rows('seminars', $expertId, $data['seminars'], ['seminar_name', 'organizer_name', 'joined_date', 'description']);
    replace_rows('awards', $expertId, $data['awards'], ['title', 'issuer_name', 'award_year', 'description']);
    replace_rows('social_links', $expertId, $data['social_links'], ['platform_name', 'link_url']);
    sync_skills($expertId, $data['skills']);
}

function replace_rows(string $table, int $expertId, array $rows, array $columns): void
{
    $stmt = db()->prepare("DELETE FROM {$table} WHERE expert_id = :expert_id");
    $stmt->execute(['expert_id' => $expertId]);

    foreach ($rows as $row) {
        $filtered = [];
        foreach ($columns as $column) {
            $filtered[$column] = trim((string) ($row[$column] ?? ''));
        }

        if (count(array_filter($filtered, static fn($value) => $value !== '')) === 0) {
            continue;
        }

        $names = implode(',', array_merge(['expert_id'], array_keys($filtered)));
        $placeholders = implode(',', array_map(static fn($column) => ':' . $column, array_merge(['expert_id'], array_keys($filtered))));
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, $names, $placeholders);
        $insert = db()->prepare($sql);
        $insert->execute(['expert_id' => $expertId] + $filtered);
    }
}

function sync_skills(int $expertId, array $skills): void
{
    db()->prepare('DELETE FROM expert_skill WHERE expert_id = :expert_id')->execute(['expert_id' => $expertId]);

    foreach ($skills as $name) {
        $name = trim($name);
        if ($name === '') {
            continue;
        }

        $find = db()->prepare('SELECT id FROM skills WHERE name = :name LIMIT 1');
        $find->execute(['name' => $name]);
        $skillId = $find->fetchColumn();

        if (!$skillId) {
            $insert = db()->prepare('INSERT INTO skills (name) VALUES (:name)');
            $insert->execute(['name' => $name]);
            $skillId = db()->lastInsertId();
        }

        $pivot = db()->prepare('INSERT INTO expert_skill (expert_id, skill_id) VALUES (:expert_id, :skill_id)');
        $pivot->execute(['expert_id' => $expertId, 'skill_id' => $skillId]);
    }
}

function search_skill_suggestions(string $keyword): array
{
    $stmt = db()->prepare('SELECT name FROM skills WHERE name LIKE :keyword ORDER BY name LIMIT 10');
    $stmt->execute(['keyword' => '%' . $keyword . '%']);
    return $stmt->fetchAll();
}
