<?php
/**
 * Database access. One PDO connection, prepared statements only.
 *
 * Works with SQLite (local preview, zero setup) and MySQL (live server).
 * Tables are created automatically the first time the site connects, so a new
 * database only needs its connection details in config.local.php.
 */

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $cfg = config('db');
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    if ($cfg['driver'] === 'mysql') {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $cfg['host'], (int) $cfg['port'], $cfg['name']);
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], $options);
    } else {
        $path = $cfg['sqlite_path'] ?? (STORAGE_DIR . '/hlts.sqlite');
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }
        $pdo = new PDO('sqlite:' . $path, null, null, $options);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA journal_mode = WAL');
    }

    migrate($pdo);
    return $pdo;
}

function db_driver(): string
{
    return config('db.driver') === 'mysql' ? 'mysql' : 'sqlite';
}

function db_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function db_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

function db_value(string $sql, array $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $value = $stmt->fetchColumn();
    return $value === false ? null : $value;
}

function db_run(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/** Insert a row and return its id. Column names come from code, never from input. */
function db_insert(string $table, array $row): int
{
    $columns = array_keys($row);
    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $table,
        implode(', ', $columns),
        implode(', ', array_map(fn ($c) => ':' . $c, $columns))
    );
    db_run($sql, $row);
    return (int) db()->lastInsertId();
}

function db_update(string $table, int $id, array $row): void
{
    $sets = implode(', ', array_map(fn ($c) => "$c = :$c", array_keys($row)));
    $row['id'] = $id;
    db_run("UPDATE $table SET $sets WHERE id = :id", $row);
}

function migrate(PDO $pdo): void
{
    $version = 1;
    $marker = STORAGE_DIR . '/.schema-' . db_driver() . '-' . md5((string) config('db.name') . (string) config('db.sqlite_path'));
    if (is_file($marker) && (int) file_get_contents($marker) >= $version) {
        return;
    }

    $mysql = db_driver() === 'mysql';
    $id = $mysql ? 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $key = 'VARCHAR(191)';
    $text = $mysql ? 'MEDIUMTEXT' : 'TEXT';
    $suffix = $mysql ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' : '';

    $tables = [
        "admins (
            id $id,
            email $key NOT NULL UNIQUE,
            name VARCHAR(120) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at VARCHAR(19) NOT NULL,
            last_login VARCHAR(19) NULL
        )",
        "leads (
            id $id,
            type VARCHAR(40) NOT NULL,
            name VARCHAR(160) NOT NULL DEFAULT '',
            email VARCHAR(191) NOT NULL DEFAULT '',
            phone VARCHAR(40) NOT NULL DEFAULT '',
            organisation VARCHAR(191) NOT NULL DEFAULT '',
            summary VARCHAR(255) NOT NULL DEFAULT '',
            payload $text NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'new',
            notes $text NULL,
            ip VARCHAR(64) NOT NULL DEFAULT '',
            created_at VARCHAR(19) NOT NULL,
            updated_at VARCHAR(19) NOT NULL
        )",
        "students (
            id $id,
            student_no $key NOT NULL UNIQUE,
            name VARCHAR(160) NOT NULL,
            email $key NOT NULL UNIQUE,
            phone VARCHAR(40) NOT NULL DEFAULT '',
            course_slug VARCHAR(60) NOT NULL DEFAULT '',
            password_hash VARCHAR(255) NOT NULL,
            must_change_password INT NOT NULL DEFAULT 1,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            lead_id INT NULL,
            created_at VARCHAR(19) NOT NULL,
            last_login VARCHAR(19) NULL
        )",
        "payments (
            id $id,
            reference $key NOT NULL UNIQUE,
            email VARCHAR(191) NOT NULL,
            name VARCHAR(160) NOT NULL DEFAULT '',
            phone VARCHAR(40) NOT NULL DEFAULT '',
            course_slug VARCHAR(60) NOT NULL DEFAULT '',
            plan VARCHAR(30) NOT NULL DEFAULT '',
            amount_kobo INT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            channel VARCHAR(40) NOT NULL DEFAULT '',
            lead_id INT NULL,
            student_id INT NULL,
            paid_at VARCHAR(19) NULL,
            created_at VARCHAR(19) NOT NULL,
            raw $text NULL
        )",
        "result_batches (
            id $id,
            title VARCHAR(191) NOT NULL,
            school VARCHAR(191) NOT NULL DEFAULT '',
            session_label VARCHAR(40) NOT NULL DEFAULT '',
            term VARCHAR(40) NOT NULL DEFAULT '',
            published INT NOT NULL DEFAULT 1,
            created_at VARCHAR(19) NOT NULL
        )",
        "results (
            id $id,
            batch_id INT NOT NULL,
            student_ref VARCHAR(80) NOT NULL,
            student_name VARCHAR(160) NOT NULL,
            class_name VARCHAR(80) NOT NULL DEFAULT '',
            pin_hash VARCHAR(128) NOT NULL,
            data $text NOT NULL,
            created_at VARCHAR(19) NOT NULL
        )",
        "certificates (
            id $id,
            code $key NOT NULL UNIQUE,
            holder_name VARCHAR(160) NOT NULL,
            course VARCHAR(160) NOT NULL,
            issued_on VARCHAR(10) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'valid',
            student_id INT NULL,
            created_at VARCHAR(19) NOT NULL
        )",
        "posts (
            id $id,
            slug $key NOT NULL UNIQUE,
            title VARCHAR(191) NOT NULL,
            category VARCHAR(60) NOT NULL DEFAULT '',
            excerpt VARCHAR(400) NOT NULL DEFAULT '',
            body $text NOT NULL,
            cover VARCHAR(255) NOT NULL DEFAULT '',
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            published_at VARCHAR(19) NULL,
            created_at VARCHAR(19) NOT NULL
        )",
        "events (
            id $id,
            slug $key NOT NULL UNIQUE,
            title VARCHAR(191) NOT NULL,
            starts_at VARCHAR(19) NOT NULL,
            location VARCHAR(191) NOT NULL DEFAULT '',
            mode VARCHAR(20) NOT NULL DEFAULT 'in-person',
            summary VARCHAR(400) NOT NULL DEFAULT '',
            body $text NOT NULL,
            cover VARCHAR(255) NOT NULL DEFAULT '',
            register_url VARCHAR(255) NOT NULL DEFAULT '',
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            created_at VARCHAR(19) NOT NULL
        )",
        "projects (
            id $id,
            slug $key NOT NULL UNIQUE,
            title VARCHAR(191) NOT NULL,
            client VARCHAR(191) NOT NULL DEFAULT '',
            category VARCHAR(60) NOT NULL DEFAULT '',
            summary VARCHAR(400) NOT NULL DEFAULT '',
            body $text NOT NULL,
            cover VARCHAR(255) NOT NULL DEFAULT '',
            url VARCHAR(255) NOT NULL DEFAULT '',
            results VARCHAR(255) NOT NULL DEFAULT '',
            sort_order INT NOT NULL DEFAULT 0,
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            created_at VARCHAR(19) NOT NULL
        )",
        "jobs (
            id $id,
            slug $key NOT NULL UNIQUE,
            title VARCHAR(191) NOT NULL,
            job_type VARCHAR(40) NOT NULL DEFAULT 'Full-time',
            location VARCHAR(120) NOT NULL DEFAULT 'Lagos',
            summary VARCHAR(400) NOT NULL DEFAULT '',
            body $text NOT NULL,
            closes_on VARCHAR(10) NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'draft',
            created_at VARCHAR(19) NOT NULL
        )",
        "materials (
            id $id,
            course_slug VARCHAR(60) NOT NULL,
            title VARCHAR(191) NOT NULL,
            url VARCHAR(255) NOT NULL,
            kind VARCHAR(30) NOT NULL DEFAULT 'link',
            sort_order INT NOT NULL DEFAULT 0,
            status VARCHAR(20) NOT NULL DEFAULT 'published',
            created_at VARCHAR(19) NOT NULL
        )",
    ];

    foreach ($tables as $table) {
        $pdo->exec('CREATE TABLE IF NOT EXISTS ' . $table . $suffix);
    }

    $indexes = [
        'leads_type' => 'leads (type, status)',
        'results_lookup' => 'results (student_ref, batch_id)',
        'payments_email' => 'payments (email)',
        'materials_course' => 'materials (course_slug)',
    ];
    foreach ($indexes as $name => $definition) {
        try {
            $pdo->exec($mysql ? "CREATE INDEX $name ON $definition" : "CREATE INDEX IF NOT EXISTS $name ON $definition");
        } catch (PDOException $e) {
            // MySQL has no IF NOT EXISTS for indexes; an existing index is fine.
        }
    }

    seed_drafts($pdo);

    @file_put_contents($marker, (string) $version);
}

/**
 * Example content, saved as drafts so nothing appears publicly until someone
 * edits and publishes it in the admin area.
 */
function seed_drafts(PDO $pdo): void
{
    if ((int) $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn() > 0) {
        return;
    }

    $now = now();
    $insert = function (string $table, array $row) use ($pdo, $now) {
        $row['created_at'] = $now;
        $cols = array_keys($row);
        $stmt = $pdo->prepare(sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(',', $cols), implode(',', array_map(fn ($c) => ':' . $c, $cols))));
        $stmt->execute($row);
    };

    $insert('posts', [
        'slug' => 'edtech-student-engagement',
        'title' => '5 ways EdTech improves student engagement',
        'category' => 'Teaching',
        'excerpt' => 'How digital tools, interactive content and personalised learning paths keep students motivated.',
        'body' => "EXAMPLE DRAFT. Replace this text before publishing.\n\n## 1. Instant feedback\nStudents see how they are doing straight away, so they correct mistakes while the lesson is still fresh.\n\n## 2. Learning at their own pace\n- Revisit lessons\n- Practise with CBT questions\n- Track progress over the term",
        'cover' => 'images/data.jpeg',
        'status' => 'draft',
    ]);
    $insert('posts', [
        'slug' => 'cbt-exam-readiness',
        'title' => 'Preparing your school for computer-based exams',
        'category' => 'Assessment',
        'excerpt' => 'A practical checklist for schools moving their tests and exams onto computers.',
        'body' => "EXAMPLE DRAFT. Replace this text before publishing.\n\nComputer-based testing works best when the lab, the question bank and the students are ready before exam week.",
        'cover' => 'images/cbt.jpg',
        'status' => 'draft',
    ]);
    $insert('events', [
        'slug' => 'techmind-meetup',
        'title' => 'TechMind Africa community meetup',
        'starts_at' => date('Y-m-d 10:00:00', strtotime('+30 days')),
        'location' => 'HLTS office, Somolu, Lagos',
        'mode' => 'in-person',
        'summary' => 'EXAMPLE DRAFT. An open session for learners, builders and educators to meet and share projects.',
        'body' => "EXAMPLE DRAFT. Describe the agenda, who should attend and what to bring.",
        'cover' => 'images/2027images/WhatsApp Image 2026-09-26 at 3.21.20 PM.jpeg',
        'status' => 'draft',
    ]);
    $insert('projects', [
        'slug' => 'school-result-portal',
        'title' => 'School result portal',
        'client' => 'Secondary school, Lagos',
        'category' => 'Portal',
        'summary' => 'EXAMPLE DRAFT. Online result checking for parents with scratch-card PINs.',
        'body' => "EXAMPLE DRAFT. Describe the problem, what HLTS built and the outcome.",
        'cover' => 'images/mockup.jpeg',
        'results' => 'Results published in hours, not weeks',
        'status' => 'draft',
    ]);
    $insert('jobs', [
        'slug' => 'ict-facilitator',
        'title' => 'ICT Facilitator (School Deployment)',
        'job_type' => 'Full-time',
        'location' => 'Lagos',
        'summary' => 'EXAMPLE DRAFT. Teach computer studies and support CBT in a partner school.',
        'body' => "EXAMPLE DRAFT.\n\n## What you will do\n- Teach ICT to primary and secondary students\n- Support CBT exams and the computer lab\n\n## What you need\n- A relevant degree or diploma\n- Classroom experience",
        'status' => 'draft',
    ]);
}
