<?php
declare(strict_types=1);

namespace App;

/**
 * Définition du schéma de la base, générée pour MySQL/MariaDB ou SQLite.
 * Types abstraits : pk, str:N, text, longtext, int, bool, datetime.
 */
final class Schema
{
    public static function tables(): array
    {
        return [
            'users' => [
                'columns' => [
                    'id' => 'pk', 'name' => 'str:120', 'email' => 'str:190', 'password' => 'str:255',
                    'role' => 'str:20', 'last_login_at' => 'datetime', 'created_at' => 'datetime',
                ],
                'unique' => [['email']],
            ],
            'settings' => [
                'columns' => ['name' => 'str:100', 'value' => 'longtext'],
                'primary' => 'name',
            ],
            'property_categories' => [
                'columns' => [
                    'id' => 'pk', 'name' => 'str:120', 'slug' => 'str:150', 'description' => 'text',
                    'icon' => 'str:50', 'sort' => 'int',
                ],
                'unique' => [['slug']],
            ],
            'sites' => [
                'columns' => [
                    'id' => 'pk', 'name' => 'str:190', 'slug' => 'str:190', 'city' => 'str:120', 'area' => 'str:190',
                    'description' => 'text', 'status' => 'str:30', 'image' => 'str:255', 'image_remote' => 'str:500',
                    'map_query' => 'str:255', 'is_featured' => 'bool', 'sort' => 'int', 'created_at' => 'datetime',
                ],
                'unique' => [['slug']],
            ],
            'properties' => [
                'columns' => [
                    'id' => 'pk', 'title' => 'str:190', 'slug' => 'str:190', 'reference' => 'str:50',
                    'category_id' => 'int', 'site_id' => 'int', 'transaction' => 'str:20', 'status' => 'str:30',
                    'city' => 'str:120', 'address' => 'str:255', 'built_area' => 'int', 'land_area' => 'int',
                    'rooms' => 'int', 'bedrooms' => 'int', 'bathrooms' => 'int', 'living_rooms' => 'int',
                    'kitchens' => 'int', 'terraces' => 'int', 'garages' => 'int', 'floors' => 'int',
                    'year_built' => 'int', 'excerpt' => 'text', 'description' => 'longtext', 'features' => 'text',
                    'image' => 'str:255', 'image_remote' => 'str:500', 'thumb_remote' => 'str:500',
                    'video_url' => 'str:255', 'map_query' => 'str:255', 'is_featured' => 'bool',
                    'is_published' => 'bool', 'views' => 'int', 'sort' => 'int',
                    'created_at' => 'datetime', 'updated_at' => 'datetime',
                ],
                'unique' => [['slug']],
                'index' => [['category_id'], ['site_id'], ['is_published']],
            ],
            'property_images' => [
                'columns' => [
                    'id' => 'pk', 'property_id' => 'int', 'path' => 'str:255', 'remote_url' => 'str:500',
                    'remote_thumb' => 'str:500', 'caption' => 'str:255', 'sort' => 'int',
                ],
                'index' => [['property_id']],
            ],
            'property_plans' => [
                'columns' => [
                    'id' => 'pk', 'property_id' => 'int', 'title' => 'str:190', 'image' => 'str:255',
                    'image_remote' => 'str:500', 'description' => 'text', 'sort' => 'int',
                ],
                'index' => [['property_id']],
            ],
            'posts' => [
                'columns' => [
                    'id' => 'pk', 'title' => 'str:255', 'slug' => 'str:190', 'legacy_slug' => 'str:190',
                    'category' => 'str:120', 'tags' => 'str:255', 'author' => 'str:120', 'excerpt' => 'text',
                    'content' => 'longtext', 'image' => 'str:255', 'image_remote' => 'str:500',
                    'thumb_remote' => 'str:500', 'gallery' => 'longtext', 'is_published' => 'bool',
                    'is_featured' => 'bool', 'published_at' => 'datetime', 'views' => 'int',
                    'created_at' => 'datetime', 'updated_at' => 'datetime',
                ],
                'unique' => [['slug']],
                'index' => [['is_published', 'published_at'], ['legacy_slug']],
            ],
            'comments' => [
                'columns' => [
                    'id' => 'pk', 'post_id' => 'int', 'parent_id' => 'int', 'name' => 'str:120', 'email' => 'str:190',
                    'content' => 'text', 'status' => 'str:20', 'ip' => 'str:45', 'created_at' => 'datetime',
                ],
                'index' => [['post_id', 'status']],
            ],
            'faqs' => [
                'columns' => [
                    'id' => 'pk', 'question' => 'str:255', 'answer' => 'text', 'category' => 'str:100',
                    'sort' => 'int', 'is_published' => 'bool',
                ],
            ],
            'testimonials' => [
                'columns' => [
                    'id' => 'pk', 'name' => 'str:120', 'role' => 'str:190', 'content' => 'text', 'rating' => 'int',
                    'photo' => 'str:255', 'sort' => 'int', 'is_published' => 'bool',
                ],
            ],
            'partners' => [
                'columns' => [
                    'id' => 'pk', 'name' => 'str:150', 'logo' => 'str:255', 'logo_remote' => 'str:500',
                    'url' => 'str:255', 'sort' => 'int', 'is_published' => 'bool',
                ],
            ],
            'services' => [
                'columns' => [
                    'id' => 'pk', 'title' => 'str:190', 'slug' => 'str:190', 'icon' => 'str:50', 'excerpt' => 'text',
                    'content' => 'longtext', 'features' => 'text', 'image' => 'str:255', 'sort' => 'int',
                    'is_published' => 'bool', 'is_featured' => 'bool',
                ],
                'unique' => [['slug']],
            ],
            'slides' => [
                'columns' => [
                    'id' => 'pk', 'pre_title' => 'str:190', 'title' => 'str:255', 'text' => 'text', 'image' => 'str:255',
                    'button_text' => 'str:100', 'button_url' => 'str:255', 'button2_text' => 'str:100',
                    'button2_url' => 'str:255', 'sort' => 'int', 'is_published' => 'bool',
                ],
            ],
            'messages' => [
                'columns' => [
                    'id' => 'pk', 'type' => 'str:30', 'name' => 'str:150', 'email' => 'str:190', 'phone' => 'str:50',
                    'subject' => 'str:190', 'message' => 'text', 'property_id' => 'int', 'visit_date' => 'str:30',
                    'is_read' => 'bool', 'ip' => 'str:45', 'user_agent' => 'str:255', 'created_at' => 'datetime',
                ],
                'index' => [['is_read'], ['type']],
            ],
            'subscriptions' => [
                'columns' => [
                    'id' => 'pk', 'full_name' => 'str:150', 'email' => 'str:190', 'phone' => 'str:50',
                    'country' => 'str:100', 'city' => 'str:100', 'villa_type' => 'str:50', 'site' => 'str:150',
                    'payment_mode' => 'str:50', 'message' => 'text', 'status' => 'str:30', 'notes' => 'text',
                    'ip' => 'str:45', 'created_at' => 'datetime', 'updated_at' => 'datetime',
                ],
                'index' => [['status']],
            ],
            'newsletter' => [
                'columns' => ['id' => 'pk', 'email' => 'str:190', 'ip' => 'str:45', 'created_at' => 'datetime'],
                'unique' => [['email']],
            ],
            'rate_limits' => [
                'columns' => ['id' => 'pk', 'k' => 'str:190', 'created_at' => 'int'],
                'index' => [['k', 'created_at']],
            ],
        ];
    }

    /** Génère les requêtes CREATE TABLE / INDEX pour le pilote donné. */
    public static function statements(string $driver): array
    {
        $sql = [];
        foreach (self::tables() as $table => $def) {
            $cols = [];
            foreach ($def['columns'] as $name => $type) {
                $cols[] = '`' . $name . '` ' . self::type($type, $driver);
            }
            if (!empty($def['primary'])) {
                $cols[] = 'PRIMARY KEY (`' . $def['primary'] . '`)';
            }
            if ($driver === 'mysql') {
                foreach ($def['unique'] ?? [] as $u) {
                    $cols[] = 'UNIQUE KEY `uniq_' . $table . '_' . implode('_', $u) . '` (`' . implode('`, `', $u) . '`)';
                }
                foreach ($def['index'] ?? [] as $i) {
                    $cols[] = 'KEY `idx_' . $table . '_' . implode('_', $i) . '` (`' . implode('`, `', $i) . '`)';
                }
                $sql[] = 'CREATE TABLE IF NOT EXISTS `' . $table . "` (\n  " . implode(",\n  ", $cols)
                    . "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            } else {
                $sql[] = 'CREATE TABLE IF NOT EXISTS `' . $table . "` (\n  " . implode(",\n  ", $cols) . "\n)";
                foreach ($def['unique'] ?? [] as $u) {
                    $sql[] = 'CREATE UNIQUE INDEX IF NOT EXISTS `uniq_' . $table . '_' . implode('_', $u) . '` ON `' . $table . '` (`' . implode('`, `', $u) . '`)';
                }
                foreach ($def['index'] ?? [] as $i) {
                    $sql[] = 'CREATE INDEX IF NOT EXISTS `idx_' . $table . '_' . implode('_', $i) . '` ON `' . $table . '` (`' . implode('`, `', $i) . '`)';
                }
            }
        }
        return $sql;
    }

    private static function type(string $type, string $driver): string
    {
        [$base, $len] = array_pad(explode(':', $type), 2, null);
        $mysql = $driver === 'mysql';
        return match ($base) {
            'pk' => $mysql ? 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'str' => 'VARCHAR(' . ((int) $len ?: 255) . ') NULL',
            'text' => 'TEXT NULL',
            'longtext' => $mysql ? 'MEDIUMTEXT NULL' : 'TEXT NULL',
            'int' => $mysql ? 'INT NULL' : 'INTEGER NULL',
            'bool' => $mysql ? 'TINYINT(1) NOT NULL DEFAULT 0' : 'INTEGER NOT NULL DEFAULT 0',
            'datetime' => 'DATETIME NULL',
            default => 'TEXT NULL',
        };
    }

    /** Fichier SQL complet (MySQL) — fourni pour une importation manuelle via phpMyAdmin. */
    public static function mysqlDump(): string
    {
        return "-- GELPAZ IMMO — Schéma MySQL/MariaDB\n-- Généré automatiquement. Utilisez de préférence l'assistant /install.\n\n"
            . implode(";\n\n", self::statements('mysql')) . ";\n";
    }
}
