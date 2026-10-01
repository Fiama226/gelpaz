<?php
declare(strict_types=1);

namespace App;

use PDO;
use PDOStatement;
use RuntimeException;

/**
 * Accès base de données (PDO) — MySQL/MariaDB ou SQLite.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = self::connect((array) config('db'));
        }
        return self::$pdo;
    }

    public static function connect(array $cfg): PDO
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $driver = $cfg['driver'] ?? 'sqlite';
        if ($driver === 'mysql') {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $cfg['host'] ?? 'localhost',
                (int) ($cfg['port'] ?? 3306),
                $cfg['database'] ?? '',
                $cfg['charset'] ?? 'utf8mb4'
            );
            $pdo = new PDO($dsn, (string) ($cfg['username'] ?? ''), (string) ($cfg['password'] ?? ''), $options);
            $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone = '+00:00'");
            return $pdo;
        }
        if ($driver === 'sqlite') {
            $path = (string) ($cfg['path'] ?? ROOT . '/storage/database.sqlite');
            $dir = dirname($path);
            if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
                throw new RuntimeException('Impossible de créer le dossier de la base SQLite : ' . $dir);
            }
            $pdo = new PDO('sqlite:' . $path, null, null, $options);
            $pdo->exec('PRAGMA foreign_keys = ON');
            $pdo->exec('PRAGMA busy_timeout = 5000');
            return $pdo;
        }
        throw new RuntimeException('Pilote de base de données inconnu : ' . $driver);
    }

    public static function setConnection(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    public static function driver(): string
    {
        return (string) self::pdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        foreach (array_values(array_is_list($params) ? $params : []) as $i => $value) {
            $stmt->bindValue($i + 1, $value, self::type($value));
        }
        if (!array_is_list($params)) {
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . ltrim((string) $key, ':'), $value, self::type($value));
            }
        }
        $stmt->execute();
        return $stmt;
    }

    private static function type(mixed $value): int
    {
        return match (true) {
            is_int($value) => PDO::PARAM_INT,
            is_bool($value) => PDO::PARAM_BOOL,
            $value === null => PDO::PARAM_NULL,
            default => PDO::PARAM_STR,
        };
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function value(string $sql, array $params = []): mixed
    {
        $v = self::run($sql, $params)->fetchColumn();
        return $v === false ? null : $v;
    }

    public static function insert(string $table, array $data): int
    {
        $cols = array_keys($data);
        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $table,
            implode(', ', array_map(static fn($c) => '`' . $c . '`', $cols)),
            implode(', ', array_fill(0, count($cols), '?'))
        );
        self::run($sql, array_values($data));
        return (int) self::pdo()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $params = []): int
    {
        $set = implode(', ', array_map(static fn($c) => '`' . $c . '` = ?', array_keys($data)));
        $stmt = self::run(sprintf('UPDATE `%s` SET %s WHERE %s', $table, $set, $where), array_merge(array_values($data), $params));
        return $stmt->rowCount();
    }

    public static function delete(string $table, string $where, array $params = []): int
    {
        return self::run(sprintf('DELETE FROM `%s` WHERE %s', $table, $where), $params)->rowCount();
    }

    public static function transaction(callable $fn): mixed
    {
        $pdo = self::pdo();
        $pdo->beginTransaction();
        try {
            $result = $fn();
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /** Opérateur LIKE insensible à la casse selon le pilote. */
    public static function like(): string
    {
        return 'LIKE';
    }
}
