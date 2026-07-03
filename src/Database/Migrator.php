<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use RuntimeException;

/**
 * Runs numbered SQL migration files in order, tracking applied migrations.
 */
final class Migrator
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly string $migrationsPath,
    ) {
    }

    public function run(): void
    {
        $this->ensureMigrationsTable();
        $applied = $this->getAppliedMigrations();

        $files = glob($this->migrationsPath . '/*.sql');
        if ($files === false) {
            throw new RuntimeException('Cannot read migrations directory.');
        }

        sort($files);

        foreach ($files as $file) {
            $name = basename($file);

            if ($name === '000_create_migrations_table.sql' || in_array($name, $applied, true)) {
                continue;
            }

            $sql = file_get_contents($file);
            if ($sql === false) {
                throw new RuntimeException("Cannot read migration: {$name}");
            }

            $this->pdo->exec($sql);
            $stmt = $this->pdo->prepare('INSERT INTO migrations (migration) VALUES (:migration)');
            $stmt->execute(['migration' => $name]);

            echo "Applied: {$name}\n";
        }
    }

    private function ensureMigrationsTable(): void
    {
        $file = $this->migrationsPath . '/000_create_migrations_table.sql';
        $sql = file_get_contents($file);
        if ($sql === false) {
            throw new RuntimeException('Cannot read migrations table SQL.');
        }

        $this->pdo->exec($sql);
    }

    /**
     * @return list<string>
     */
    private function getAppliedMigrations(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT migration FROM migrations ORDER BY id');
            $rows = $stmt->fetchAll();

            return array_column($rows, 'migration');
        } catch (\PDOException) {
            return [];
        }
    }
}
