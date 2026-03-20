<?php

declare(strict_types=1);

class Expert extends Model
{
    public function paginate(array $filters, int $page = 1, int $perPage = 8): array
    {
        $conditions = ['1=1'];
        $params = [];

        if (!empty($filters['keyword'])) {
            $conditions[] = '(e.full_name LIKE :keyword OR e.position_title LIKE :keyword OR e.expertise_summary LIKE :keyword)';
            $params['keyword'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['skill'])) {
            $conditions[] = 'EXISTS (
                SELECT 1 FROM expert_skill es
                JOIN skills s ON s.id = es.skill_id
                WHERE es.expert_id = e.id AND s.name = :skill
            )';
            $params['skill'] = $filters['skill'];
        }

        $where = implode(' AND ', $conditions);
        $offset = ($page - 1) * $perPage;

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM experts e WHERE {$where}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT e.*, u.role,
                    GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ', ') AS skills
                FROM experts e
                JOIN users u ON u.id = e.user_id
                LEFT JOIN expert_skill es ON es.expert_id = e.id
                LEFT JOIN skills s ON s.id = es.skill_id
                WHERE {$where}
                GROUP BY e.id
                ORDER BY e.updated_at DESC, e.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => (int) ceil(max($total, 1) / $perPage),
        ];
    }

    public function stats(): array
    {
        return [
            'experts' => (int) $this->db->query('SELECT COUNT(*) FROM experts')->fetchColumn(),
            'approved' => (int) $this->db->query("SELECT COUNT(*) FROM experts WHERE approval_status = 'approved'")->fetchColumn(),
            'pending' => (int) $this->db->query("SELECT COUNT(*) FROM experts WHERE approval_status = 'pending'")->fetchColumn(),
            'research' => (int) $this->db->query('SELECT COUNT(*) FROM research')->fetchColumn(),
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM experts WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $expert = $stmt->fetch();
        if (!$expert) {
            return null;
        }

        $expert['work_experience'] = $this->fetchMany('work_experience', $id);
        $expert['research'] = $this->fetchMany('research', $id);
        $expert['training'] = $this->fetchMany('training', $id);
        $expert['awards'] = $this->fetchMany('awards', $id);
        $expert['social_links'] = $this->fetchMany('social_links', $id);
        $expert['skills'] = $this->fetchSkillTags($id);
        return $expert;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO experts (user_id, full_name, position_title, department, phone, email, profile_image, resume_file, expertise_summary, portfolio_url, approval_status, created_at, updated_at) VALUES (:user_id, :full_name, :position_title, :department, :phone, :email, :profile_image, :resume_file, :expertise_summary, :portfolio_url, :approval_status, NOW(), NOW())');
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
            'approval_status' => $data['approval_status'] ?? 'pending',
        ]);

        $expertId = (int) $this->db->lastInsertId();
        $this->syncChildren($expertId, $data);
        return $expertId;
    }

    public function updateProfile(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE experts SET full_name=:full_name, position_title=:position_title, department=:department, phone=:phone, email=:email, profile_image=:profile_image, resume_file=:resume_file, expertise_summary=:expertise_summary, portfolio_url=:portfolio_url, approval_status=:approval_status, updated_at=NOW() WHERE id=:id');
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
            'approval_status' => $data['approval_status'] ?? 'pending',
        ]);

        $this->syncChildren($id, $data);
    }

    public function deleteProfile(int $id): void
    {
        foreach (['expert_skill', 'social_links', 'awards', 'training', 'research', 'work_experience'] as $table) {
            $stmt = $this->db->prepare("DELETE FROM {$table} WHERE expert_id = :expert_id");
            $stmt->execute(['expert_id' => $id]);
        }
        $stmt = $this->db->prepare('DELETE FROM experts WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function approve(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE experts SET approval_status = 'approved', updated_at = NOW() WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function searchSkills(string $query): array
    {
        $stmt = $this->db->prepare('SELECT name FROM skills WHERE name LIKE :query ORDER BY name LIMIT 10');
        $stmt->execute(['query' => '%' . $query . '%']);
        return $stmt->fetchAll();
    }

    private function fetchMany(string $table, int $expertId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE expert_id = :expert_id ORDER BY id DESC");
        $stmt->execute(['expert_id' => $expertId]);
        return $stmt->fetchAll();
    }

    private function fetchSkillTags(int $expertId): array
    {
        $stmt = $this->db->prepare('SELECT s.id, s.name FROM expert_skill es JOIN skills s ON s.id = es.skill_id WHERE es.expert_id = :expert_id ORDER BY s.name');
        $stmt->execute(['expert_id' => $expertId]);
        return $stmt->fetchAll();
    }

    private function syncChildren(int $expertId, array $data): void
    {
        $this->replaceRows('work_experience', $expertId, $data['work_experience'] ?? []);
        $this->replaceRows('research', $expertId, $data['research'] ?? []);
        $this->replaceRows('training', $expertId, $data['training'] ?? []);
        $this->replaceRows('awards', $expertId, $data['awards'] ?? []);
        $this->replaceRows('social_links', $expertId, $data['social_links'] ?? []);
        $this->syncSkills($expertId, $data['skills'] ?? []);
    }

    private function replaceRows(string $table, int $expertId, array $rows): void
    {
        $this->db->prepare("DELETE FROM {$table} WHERE expert_id = :expert_id")->execute(['expert_id' => $expertId]);

        $map = [
            'work_experience' => ['organization', 'project_name', 'role_title', 'start_date', 'end_date', 'description'],
            'research' => ['category', 'title', 'publication_name', 'published_year', 'description', 'link_url'],
            'training' => ['course_name', 'provider_name', 'certificate_name', 'start_date', 'end_date', 'description'],
            'awards' => ['title', 'issuer_name', 'award_year', 'description'],
            'social_links' => ['platform_name', 'link_url'],
        ];

        foreach ($rows as $row) {
            $filtered = array_intersect_key($row, array_flip($map[$table] ?? []));
            if (count(array_filter($filtered, static fn($value) => $value !== '' && $value !== null)) === 0) {
                continue;
            }
            $columns = array_merge(['expert_id'], array_keys($filtered));
            $placeholders = array_map(static fn($column) => ':' . $column, $columns);
            $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(',', $columns), implode(',', $placeholders));
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['expert_id' => $expertId] + $filtered);
        }
    }

    private function syncSkills(int $expertId, array $skills): void
    {
        $this->db->prepare('DELETE FROM expert_skill WHERE expert_id = :expert_id')->execute(['expert_id' => $expertId]);
        foreach ($skills as $skillName) {
            $skillName = trim((string) $skillName);
            if ($skillName === '') {
                continue;
            }
            $stmt = $this->db->prepare('SELECT id FROM skills WHERE name = :name LIMIT 1');
            $stmt->execute(['name' => $skillName]);
            $skillId = $stmt->fetchColumn();
            if (!$skillId) {
                $this->db->prepare('INSERT INTO skills (name) VALUES (:name)')->execute(['name' => $skillName]);
                $skillId = $this->db->lastInsertId();
            }
            $this->db->prepare('INSERT INTO expert_skill (expert_id, skill_id) VALUES (:expert_id, :skill_id)')->execute([
                'expert_id' => $expertId,
                'skill_id' => $skillId,
            ]);
        }
    }
}
