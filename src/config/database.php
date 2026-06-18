<?php

class Database
{
    private static $pdo = null;

    private static function loadEnv()
    {
        $envFile = __DIR__ . '/../../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#') continue;
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }

    public static function connect()
    {
        if (self::$pdo !== null) return self::$pdo;

        self::loadEnv();
        $driver = getenv('DB_DRIVER') ?: 'mysql';

        if ($driver === 'sqlite') {
            self::$pdo = self::connectSqlite();
        } else {
            self::$pdo = self::connectMysql();
        }

        return self::$pdo;
    }

    private static function connectMysql()
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $name = getenv('DB_NAME') ?: 'pemesanan_lapangan';
        $user = getenv('DB_USERNAME') ?: 'root';
        $pass = getenv('DB_PASSWORD') ?: '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=$charset";

        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Koneksi database gagal: ' . $e->getMessage());
        }

        self::ensureMysqlSchema($pdo);
        return $pdo;
    }

    private static function connectSqlite()
    {
        $baseDir = dirname(__DIR__, 2);
        $path = getenv('DB_SQLITE_PATH') ?: $baseDir . '/database/data.db';

        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $isNew = !file_exists($path);

        try {
            $pdo = new PDO("sqlite:$path", null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            $pdo->exec("PRAGMA journal_mode=WAL");
            $pdo->exec("PRAGMA foreign_keys=ON");
        } catch (PDOException $e) {
            if (file_exists($path)) unlink($path);
            $isNew = true;
            $pdo = new PDO("sqlite:$path", null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $pdo->exec("PRAGMA journal_mode=WAL");
            $pdo->exec("PRAGMA foreign_keys=ON");
        }

        if ($isNew) {
            self::initSqliteSchema($pdo);
        }

        return $pdo;
    }

    private static function ensureMysqlSchema($pdo)
    {
        $pdo->exec("CREATE TABLE IF NOT EXISTS fields (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            sport VARCHAR(50) NOT NULL,
            capacity VARCHAR(50) NOT NULL,
            price_per_hour DECIMAL(10,2) NOT NULL,
            description TEXT,
            image VARCHAR(255) DEFAULT NULL,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            field_id INT NOT NULL,
            customer_name VARCHAR(120) NOT NULL,
            customer_email VARCHAR(150) NOT NULL,
            customer_phone VARCHAR(30) NOT NULL,
            booking_date DATE NOT NULL,
            start_time TIME NOT NULL,
            duration_hours INT NOT NULL,
            total_price DECIMAL(10,2) NOT NULL,
            status VARCHAR(30) NOT NULL DEFAULT 'pending',
            payment_status VARCHAR(30) NOT NULL DEFAULT 'waiting',
            payment_proof VARCHAR(255) DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (field_id) REFERENCES fields(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            display_name VARCHAR(100) NOT NULL DEFAULT 'Admin',
            role VARCHAR(20) NOT NULL DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users (username, password_hash, display_name, role) VALUES (?, ?, ?, ?)")
                ->execute(['admin', $hash, 'Admin SportVenue', 'admin']);
        }
    }

    private static function initSqliteSchema($pdo)
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS fields (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                sport VARCHAR(50) NOT NULL,
                capacity VARCHAR(50) NOT NULL,
                price_per_hour DECIMAL(10,2) NOT NULL,
                description TEXT,
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                field_id INTEGER NOT NULL,
                customer_name VARCHAR(120) NOT NULL,
                customer_email VARCHAR(150) NOT NULL,
                customer_phone VARCHAR(30) NOT NULL,
                booking_date DATE NOT NULL,
                start_time TIME NOT NULL,
                duration_hours INTEGER NOT NULL,
                total_price DECIMAL(10,2) NOT NULL,
                status VARCHAR(30) NOT NULL DEFAULT 'pending',
                payment_status VARCHAR(30) NOT NULL DEFAULT 'waiting',
                payment_proof VARCHAR(255) DEFAULT NULL,
                notes TEXT DEFAULT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (field_id) REFERENCES fields(id) ON DELETE CASCADE
            )
        ");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(50) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                display_name VARCHAR(100) NOT NULL DEFAULT 'Admin',
                role VARCHAR(20) NOT NULL DEFAULT 'admin',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $count = $pdo->query("SELECT COUNT(*) FROM fields")->fetchColumn();
        if ($count == 0) {
            $fields = [
                ['Lapangan A', 'Futsal', '10 pemain', 150000, 'Lapangan futsal standar, tersedia dengan lampu malam.'],
                ['Lapangan B', 'Basket', '10 pemain', 180000, 'Lapangan basket reguler dengan ring profesional.'],
                ['Lapangan C', 'Badminton', '4 pemain', 100000, 'Lapangan badminton indoor, bersih dan nyaman.'],
                ['Lapangan D', 'Mini Soccer', '20 pemain', 250000, 'Lapangan mini soccer rumput sintetis ukuran penuh.'],
                ['Lapangan E', 'Futsal', '10 pemain', 135000, 'Lapangan futsal outdoor dengan pencahayaan malam.'],
            ];
            $stmt = $pdo->prepare("INSERT INTO fields (name, sport, capacity, price_per_hour, description) VALUES (?, ?, ?, ?, ?)");
            foreach ($fields as $f) $stmt->execute($f);
        }

        $ucount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($ucount == 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users (username, password_hash, display_name, role) VALUES (?, ?, ?, ?)")
                ->execute(['admin', $hash, 'Admin SportVenue', 'admin']);
        }
    }
}
