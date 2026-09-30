<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $opts = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    if (DB_DRIVER === 'mysql') {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opts);
        $pdo->exec("SET time_zone = '+01:00'");
        return $pdo;
    }
    $fresh = !is_file(DB_PATH);
    if ($fresh && !is_dir(dirname(DB_PATH))) {
        mkdir(dirname(DB_PATH), 0775, true);
    }
    $pdo = new PDO('sqlite:' . DB_PATH, null, null, $opts);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode = WAL');
    $pdo->exec('PRAGMA busy_timeout = 4000');
    if ($fresh) {
        db_install_schema($pdo);
        require_once __DIR__ . '/data/catalog_seed.php';
        seed_catalog($pdo);
    }
    return $pdo;
}

function db_install_schema(PDO $pdo): void
{
    $file = __DIR__ . '/../sql/schema.' . (DB_DRIVER === 'mysql' ? 'mysql' : 'sqlite') . '.sql';
    $sql = (string) file_get_contents($file);
    $sql = preg_replace('/^--.*$/m', '', $sql) ?? $sql;
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        $pdo->exec($stmt);
    }
}

function db_all(string $sql, array $params = []): array
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function db_one(string $sql, array $params = []): ?array
{
    $st = db()->prepare($sql);
    $st->execute($params);
    $row = $st->fetch();
    return $row === false ? null : $row;
}

/** @return mixed */
function db_val(string $sql, array $params = [])
{
    $st = db()->prepare($sql);
    $st->execute($params);
    $v = $st->fetchColumn();
    return $v === false ? null : $v;
}

function db_exec(string $sql, array $params = []): int
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->rowCount();
}

function db_insert(string $table, array $data): int
{
    $cols = array_keys($data);
    $sql = 'INSERT INTO ' . $table . ' (' . implode(',', $cols) . ') VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')';
    db_exec($sql, array_values($data));
    return (int) db()->lastInsertId();
}

function db_update(string $table, array $data, int $id): int
{
    $set = implode(',', array_map(fn($c) => "$c = ?", array_keys($data)));
    return db_exec("UPDATE $table SET $set WHERE id = ?", [...array_values($data), $id]);
}

function now(): string
{
    return date('Y-m-d H:i:s');
}
