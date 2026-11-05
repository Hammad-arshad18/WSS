<?php

declare(strict_types=1);

/**
 * PDO connection helpers. Every query in the site uses prepared statements.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = (array) config('db', []);
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'] ?? '127.0.0.1',
        (int) ($config['port'] ?? 3306),
        $config['database'] ?? 'wss_site',
        $config['charset'] ?? 'utf8mb4'
    );

    $pdo = new PDO($dsn, (string) ($config['username'] ?? ''), (string) ($config['password'] ?? ''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ]);

    $pdo->exec("SET time_zone = '+00:00'");

    return $pdo;
}

function db_query(string $sql, array $params = []): PDOStatement
{
    $statement = db()->prepare($sql);
    $statement->execute($params);

    return $statement;
}

function db_fetch(string $sql, array $params = []): ?array
{
    $row = db_query($sql, $params)->fetch();

    return $row === false ? null : $row;
}

function db_fetch_all(string $sql, array $params = []): array
{
    return db_query($sql, $params)->fetchAll();
}

function db_value(string $sql, array $params = []): mixed
{
    $value = db_query($sql, $params)->fetchColumn();

    return $value === false ? null : $value;
}

function db_execute(string $sql, array $params = []): int
{
    return db_query($sql, $params)->rowCount();
}

function db_last_id(): int
{
    return (int) db()->lastInsertId();
}
