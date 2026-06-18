<?php

require_once __DIR__ . '/../src/config/database.php';

echo "SportVenue — Database Initialization\n";
echo str_repeat('=', 40) . "\n";

try {
    $driver = getenv('DB_DRIVER') ?: 'mysql';
    echo "Driver: $driver\n";

    if ($driver === 'sqlite') {
        initSqlite();
    } else {
        initMysql();
    }

    echo "\n✓ Database initialized successfully!\n";
} catch (Exception $e) {
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

function initMysql()
{
    $host = getenv('DB_MYSQL_HOST') ?: '127.0.0.1';
    $port = getenv('DB_MYSQL_PORT') ?: '3306';
    $name = getenv('DB_MYSQL_NAME') ?: 'pemesanan_lapangan';
    $user = getenv('DB_MYSQL_USER') ?: 'root';
    $pass = getenv('DB_MYSQL_PASS') ?: '';

    $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "Creating database '$name'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$name`");

    runMysqlSchema($pdo);
}

function runMysqlSchema($pdo)
{
    echo "Creating tables...\n";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS fields (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            sport VARCHAR(50) NOT NULL,
            capacity VARCHAR(50) NOT NULL,
            price_per_hour DECIMAL(10,2) NOT NULL,
            description TEXT,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bookings (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ratings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            booking_id INT NOT NULL,
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            review TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            display_name VARCHAR(100) NOT NULL DEFAULT 'Admin',
            role VARCHAR(20) NOT NULL DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    seedFields($pdo, 'mysql');
    seedUsers($pdo, 'mysql');
    echo "Seed data inserted.\n";
}

function initSqlite()
{
    $path = getenv('DB_SQLITE_PATH') ?: __DIR__ . '/data.db';
    echo "SQLite path: $path\n";

    $isNew = !file_exists($path);

    $pdo = new PDO("sqlite:$path", null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec("PRAGMA journal_mode=WAL");
    $pdo->exec("PRAGMA foreign_keys=ON");

    if (!$isNew) {
        echo "Database already exists. Skipping.\n";
        return;
    }

    echo "Creating tables...\n";

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
        CREATE TABLE IF NOT EXISTS ratings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            booking_id INTEGER NOT NULL,
            rating INTEGER NOT NULL CHECK(rating >= 1 AND rating <= 5),
            review TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
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

    seedFields($pdo, 'sqlite');
    seedUsers($pdo, 'sqlite');
    echo "Seed data inserted.\n";
}

function seedFields($pdo, $driver)
{
    $count = $pdo->query("SELECT COUNT(*) FROM fields")->fetchColumn();
    if ($count > 0) {
        echo "Fields already seeded ($count records). Skipping.\n";
        return;
    }

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

function seedUsers($pdo, $driver)
{
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count > 0) {
        echo "Users already seeded. Skipping.\n";
        return;
    }

    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO users (username, password_hash, display_name, role) VALUES (?, ?, ?, ?)")
        ->execute(['admin', $hash, 'Admin SportVenue', 'admin']);
    echo "Admin user created (admin / admin123)\n";
}
