<?php
/**
 * FORENSIC-X Database PDO Provider
 * Supports MySQL / MariaDB and automatic zero-configuration SQLite fallback
 */

class Database {
    private static ?PDO $instance = null;
    private static string $activeDriver = 'mysql';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require BASE_PATH . '/config/database.php';
            $driver = $config['driver'] ?? 'mysql';

            // Explicit SQLite or fallback
            if ($driver === 'sqlite') {
                self::$instance = self::initSqlite();
                self::$activeDriver = 'sqlite';
                return self::$instance;
            }

            // Attempt MySQL connection
            $dsn = sprintf(
                "mysql:host=%s;port=%d;dbname=%s;charset=%s",
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
                self::$activeDriver = 'mysql';
            } catch (PDOException $e) {
                error_log("MySQL connection error: " . $e->getMessage());

                // On cloud hosts like Render where no external MySQL is configured (default host 127.0.0.1),
                // automatically fallback to zero-config SQLite so the site works immediately!
                if ($config['host'] === '127.0.0.1' || $config['host'] === 'localhost' || getenv('AUTO_SQLITE_FALLBACK') !== 'false') {
                    error_log("Falling back to built-in SQLite engine...");
                    self::$instance = self::initSqlite();
                    self::$activeDriver = 'sqlite';
                    return self::$instance;
                }

                // If user specified an explicit remote host that failed, give clear diagnostic guidance
                die("
                    <div style='font-family: system-ui, sans-serif; background: #070B14; color: #F8FAFC; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem;'>
                        <div style='max-width: 600px; background: #0F172A; border: 1px solid #EF4444; border-radius: 8px; padding: 2rem; box-shadow: 0 10px 40px rgba(0,0,0,0.7);'>
                            <h2 style='color: #EF4444; margin-top: 0;'>Database Connection Error</h2>
                            <p style='color: #94A3B8; font-size: 0.9rem;'>FORENSIC-X was unable to connect to your configured MySQL server at <code>" . htmlspecialchars($config['host']) . ":" . htmlspecialchars((string)$config['port']) . "</code>.</p>
                            <div style='background: #070B14; padding: 1rem; border-radius: 4px; font-family: monospace; font-size: 0.8rem; color: #F87171; margin: 1rem 0; word-break: break-all;'>
                                " . htmlspecialchars($e->getMessage()) . "
                            </div>
                            <div style='font-size: 0.85rem; color: #94A3B8; line-height: 1.6;'>
                                <strong>How to Resolve:</strong>
                                <ul style='margin-top: 0.5rem; padding-left: 1.25rem;'>
                                    <li>Check your Render Environment Variables (<code>DB_HOST</code>, <code>DB_USERNAME</code>, <code>DB_PASSWORD</code>, <code>DB_DATABASE</code>, <code>DB_PORT</code>).</li>
                                    <li>Or add <code>DB_DRIVER=sqlite</code> in Render Environment Variables to use the zero-configuration built-in database.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                ");
            }
        }
        return self::$instance;
    }

    private static function initSqlite(): PDO {
        $storageDir = STORAGE_PATH;
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0777, true);
        }
        $dbFile = $storageDir . '/forensic_x.sqlite';
        $needsInit = !file_exists($dbFile) || filesize($dbFile) === 0;

        $pdo = new PDO("sqlite:" . $dbFile, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        $pdo->exec("PRAGMA foreign_keys = ON;");

        if ($needsInit) {
            $schemaFile = BASE_PATH . '/database/sqlite_schema.sql';
            $seedFile   = BASE_PATH . '/database/sqlite_seed.sql';

            if (file_exists($schemaFile)) {
                $pdo->exec(file_get_contents($schemaFile));
            }
            if (file_exists($seedFile)) {
                $pdo->exec(file_get_contents($seedFile));
            }
        }

        return $pdo;
    }

    public static function getDriver(): string {
        return self::$activeDriver;
    }

    public static function beginTransaction(): bool {
        return self::getConnection()->beginTransaction();
    }

    public static function commit(): bool {
        return self::getConnection()->commit();
    }

    public static function rollBack(): bool {
        return self::getConnection()->rollBack();
    }
}
