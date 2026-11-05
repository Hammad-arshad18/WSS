<?php

declare(strict_types=1);

/**
 * Database queries used by the pages. Keeping them together makes the simple
 * page files easier to read.
 */

function user_find(int $id): ?array
{
    return db_fetch('SELECT * FROM users WHERE id = ? LIMIT 1', [$id]);
}

function user_find_by_email(string $email): ?array
{
    return db_fetch('SELECT * FROM users WHERE email = ? LIMIT 1', [mb_strtolower(trim($email))]);
}

function user_all(string $search = ''): array
{
    $sql = 'SELECT * FROM users';
    $params = [];

    if ($search !== '') {
        $sql .= ' WHERE name LIKE ? OR email LIKE ?';
        $term = '%' . $search . '%';
        $params = [$term, $term];
    }

    $sql .= " ORDER BY (role = 'super_admin') DESC, name ASC LIMIT 200";

    return db_fetch_all($sql, $params);
}

function user_create(string $name, string $email, string $passwordHash, string $role, ?int $createdBy): int
{
    db_execute(
        'INSERT INTO users (name, email, password_hash, role, is_active, created_by)
         VALUES (?, ?, ?, ?, 1, ?)',
        [$name, mb_strtolower($email), $passwordHash, $role, $createdBy]
    );

    return db_last_id();
}

function user_update(int $id, string $name, string $email, string $role): void
{
    db_execute(
        'UPDATE users SET name = ?, email = ?, role = ?, updated_at = UTC_TIMESTAMP() WHERE id = ?',
        [$name, mb_strtolower($email), $role, $id]
    );
}

function user_update_password(int $id, string $passwordHash, bool $invalidateSessions = true): void
{
    if ($invalidateSessions) {
        db_execute(
            'UPDATE users
             SET password_hash = ?, password_changed_at = UTC_TIMESTAMP(), updated_at = UTC_TIMESTAMP()
             WHERE id = ?',
            [$passwordHash, $id]
        );

        return;
    }

    db_execute('UPDATE users SET password_hash = ?, updated_at = UTC_TIMESTAMP() WHERE id = ?', [$passwordHash, $id]);
}

function user_set_active(int $id, bool $active): void
{
    db_execute('UPDATE users SET is_active = ?, updated_at = UTC_TIMESTAMP() WHERE id = ?', [$active ? 1 : 0, $id]);
}

function user_touch_login(int $id): void
{
    db_execute('UPDATE users SET last_login_at = UTC_TIMESTAMP() WHERE id = ?', [$id]);
}

function user_delete(int $id): void
{
    db_execute('DELETE FROM users WHERE id = ?', [$id]);
}

function user_email_exists(string $email, ?int $ignoreId = null): bool
{
    if ($ignoreId !== null) {
        return (bool) db_value('SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?', [mb_strtolower($email), $ignoreId]);
    }

    return (bool) db_value('SELECT COUNT(*) FROM users WHERE email = ?', [mb_strtolower($email)]);
}

function user_count_super_admins(bool $activeOnly = true): int
{
    $sql = "SELECT COUNT(*) FROM users WHERE role = 'super_admin'";

    if ($activeOnly) {
        $sql .= ' AND is_active = 1';
    }

    return (int) db_value($sql);
}

function user_stats(): array
{
    $row = db_fetch(
        "SELECT
            COUNT(*) AS total,
            SUM(is_active = 1) AS active,
            SUM(role = 'super_admin') AS admins
         FROM users"
    ) ?? [];

    return [
        'total' => (int) ($row['total'] ?? 0),
        'active' => (int) ($row['active'] ?? 0),
        'admins' => (int) ($row['admins'] ?? 0),
    ];
}

function user_recent(int $limit = 6): array
{
    $limit = max(1, min(50, $limit));

    return db_fetch_all(
        'SELECT id, name, email, role, is_active, last_login_at, created_at
         FROM users ORDER BY created_at DESC LIMIT ' . $limit
    );
}

function resource_find(int $id): ?array
{
    return db_fetch('SELECT * FROM resources WHERE id = ? LIMIT 1', [$id]);
}

function resource_published(?string $category = null, string $search = ''): array
{
    $sql = 'SELECT r.*, u.name AS author_name
            FROM resources r
            LEFT JOIN users u ON u.id = r.created_by
            WHERE r.is_published = 1';
    $params = [];

    if ($category !== null && $category !== '') {
        $sql .= ' AND r.category = ?';
        $params[] = $category;
    }

    if ($search !== '') {
        $sql .= ' AND (r.title LIKE ? OR r.description LIKE ?)';
        $term = '%' . $search . '%';
        $params[] = $term;
        $params[] = $term;
    }

    $sql .= ' ORDER BY r.created_at DESC LIMIT 200';

    return db_fetch_all($sql, $params);
}

function resource_all(?string $status = null, string $search = ''): array
{
    $sql = 'SELECT r.*, u.name AS author_name
            FROM resources r
            LEFT JOIN users u ON u.id = r.created_by
            WHERE 1 = 1';
    $params = [];

    if ($status === 'published') {
        $sql .= ' AND r.is_published = 1';
    } elseif ($status === 'draft') {
        $sql .= ' AND r.is_published = 0';
    }

    if ($search !== '') {
        $sql .= ' AND (r.title LIKE ? OR r.description LIKE ? OR r.original_name LIKE ?)';
        $term = '%' . $search . '%';
        $params = array_merge($params, [$term, $term, $term]);
    }

    $sql .= ' ORDER BY r.created_at DESC LIMIT 300';

    return db_fetch_all($sql, $params);
}

function resource_create(array $data, ?int $userId): int
{
    db_execute(
        'INSERT INTO resources
            (title, description, category, stored_name, original_name, mime_type, file_size, is_published, created_by)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [
            $data['title'],
            $data['description'],
            $data['category'],
            $data['stored_name'],
            $data['original_name'],
            $data['mime_type'],
            $data['file_size'],
            $data['is_published'] ? 1 : 0,
            $userId,
        ]
    );

    return db_last_id();
}

function resource_update(int $id, array $data): void
{
    db_execute(
        'UPDATE resources
         SET title = ?, description = ?, category = ?, is_published = ?, updated_at = UTC_TIMESTAMP()
         WHERE id = ?',
        [
            $data['title'],
            $data['description'],
            $data['category'],
            $data['is_published'] ? 1 : 0,
            $id,
        ]
    );
}

function resource_replace_file(int $id, array $file): void
{
    db_execute(
        'UPDATE resources
         SET stored_name = ?, original_name = ?, mime_type = ?, file_size = ?, updated_at = UTC_TIMESTAMP()
         WHERE id = ?',
        [
            $file['stored_name'],
            $file['original_name'],
            $file['mime_type'],
            $file['file_size'],
            $id,
        ]
    );
}

function resource_delete(int $id): void
{
    db_execute('DELETE FROM resources WHERE id = ?', [$id]);
}

function resource_count_published(): int
{
    return (int) db_value('SELECT COUNT(*) FROM resources WHERE is_published = 1');
}

function resource_count_all(): int
{
    return (int) db_value('SELECT COUNT(*) FROM resources');
}

function resource_latest_published(int $limit = 4): array
{
    $limit = max(1, min(20, $limit));

    return db_fetch_all(
        'SELECT r.*, u.name AS author_name
         FROM resources r
         LEFT JOIN users u ON u.id = r.created_by
         WHERE r.is_published = 1
         ORDER BY r.created_at DESC LIMIT ' . $limit
    );
}

function resource_category_counts(): array
{
    $rows = db_fetch_all(
        'SELECT category, COUNT(*) AS total
         FROM resources WHERE is_published = 1
         GROUP BY category ORDER BY category'
    );

    $counts = [];

    foreach ($rows as $row) {
        $counts[(string) $row['category']] = (int) $row['total'];
    }

    return $counts;
}

function enquiry_create(array $data): int
{
    db_execute(
        'INSERT INTO enquiries (name, email, phone, company, subject, message, ip, user_agent)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
        [
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['company'],
            $data['subject'],
            $data['message'],
            $data['ip'],
            $data['user_agent'],
        ]
    );

    return db_last_id();
}

function enquiry_all(?string $status = null, string $search = ''): array
{
    $sql = 'SELECT * FROM enquiries WHERE 1 = 1';
    $params = [];

    if ($status === 'new' || $status === 'handled') {
        $sql .= ' AND status = ?';
        $params[] = $status;
    }

    if ($search !== '') {
        $sql .= ' AND (name LIKE ? OR email LIKE ? OR company LIKE ? OR subject LIKE ?)';
        $term = '%' . $search . '%';
        $params = array_merge($params, [$term, $term, $term, $term]);
    }

    $sql .= " ORDER BY (status = 'new') DESC, created_at DESC LIMIT 300";

    return db_fetch_all($sql, $params);
}

function enquiry_find(int $id): ?array
{
    return db_fetch('SELECT * FROM enquiries WHERE id = ? LIMIT 1', [$id]);
}

function enquiry_toggle_handled(int $id): void
{
    db_execute(
        "UPDATE enquiries
         SET status = IF(status = 'new', 'handled', 'new'),
             handled_at = IF(status = 'new', NULL, UTC_TIMESTAMP())
         WHERE id = ?",
        [$id]
    );
}

function enquiry_count_new(): int
{
    return (int) db_value("SELECT COUNT(*) FROM enquiries WHERE status = 'new'");
}

function enquiry_count_all(): int
{
    return (int) db_value('SELECT COUNT(*) FROM enquiries');
}

function enquiry_recent(int $limit = 4): array
{
    $limit = max(1, min(20, $limit));

    return db_fetch_all('SELECT * FROM enquiries ORDER BY created_at DESC LIMIT ' . $limit);
}

function enquiry_count_recent_by_ip(string $ip, int $hours): int
{
    return (int) db_value(
        'SELECT COUNT(*) FROM enquiries WHERE ip = ? AND created_at >= ?',
        [$ip, gmdate('Y-m-d H:i:s', time() - ($hours * 3600))]
    );
}

function audit_record(?int $userId, string $action, ?string $details = null): void
{
    try {
        db_execute(
            'INSERT INTO audit_logs (user_id, action, details, ip, user_agent)
             VALUES (?, ?, ?, ?, ?)',
            [
                $userId,
                mb_substr($action, 0, 80),
                $details !== null ? mb_substr($details, 0, 500) : null,
                client_ip(),
                user_agent(),
            ]
        );
    } catch (Throwable $e) {
        error_log('[' . REQUEST_ID . '] Audit log failed: ' . $e->getMessage());
    }
}

function audit_recent(int $limit = 100): array
{
    $limit = max(1, min(300, $limit));

    return db_fetch_all(
        'SELECT a.*, u.name AS user_name, u.email AS user_email
         FROM audit_logs a
         LEFT JOIN users u ON u.id = a.user_id
         ORDER BY a.created_at DESC, a.id DESC
         LIMIT ' . $limit
    );
}

function audit_count_today(): int
{
    return (int) db_value('SELECT COUNT(*) FROM audit_logs WHERE created_at >= ?', [gmdate('Y-m-d 00:00:00')]);
}

function login_attempt_record(string $email, string $ip, bool $success): void
{
    db_execute(
        'INSERT INTO login_attempts (email, ip, success) VALUES (?, ?, ?)',
        [mb_strtolower($email), $ip, $success ? 1 : 0]
    );

    if (random_int(1, 50) === 1) {
        login_attempt_prune();
    }
}

function login_attempt_failures_for_email(string $email, int $minutes): int
{
    return (int) db_value(
        'SELECT COUNT(*) FROM login_attempts WHERE email = ? AND success = 0 AND attempted_at >= ?',
        [mb_strtolower($email), gmdate('Y-m-d H:i:s', time() - ($minutes * 60))]
    );
}

function login_attempt_failures_for_ip(string $ip, int $minutes): int
{
    return (int) db_value(
        'SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND success = 0 AND attempted_at >= ?',
        [$ip, gmdate('Y-m-d H:i:s', time() - ($minutes * 60))]
    );
}

function login_attempt_clear_failures(string $email, string $ip): void
{
    db_execute(
        'DELETE FROM login_attempts WHERE email = ? AND ip = ? AND success = 0',
        [mb_strtolower($email), $ip]
    );
}

function login_attempt_prune(): void
{
    db_execute('DELETE FROM login_attempts WHERE attempted_at < ?', [gmdate('Y-m-d H:i:s', time() - (7 * 86400))]);
}
