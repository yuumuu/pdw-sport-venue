CREATE TABLE IF NOT EXISTS fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    sport VARCHAR(50) NOT NULL,
    capacity VARCHAR(50) NOT NULL,
    price_per_hour DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
    FOREIGN KEY (field_id) REFERENCES fields(id) ON DELETE CASCADE,
    UNIQUE KEY unique_booking_slot (field_id, booking_date, start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) NOT NULL DEFAULT 'Admin',
    role VARCHAR(20) NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO fields (id, name, sport, capacity, price_per_hour, description) VALUES
(1, 'Lapangan A', 'Futsal', '10 pemain', 150000, 'Lapangan futsal standar, tersedia dengan lampu malam.'),
(2, 'Lapangan B', 'Basket', '10 pemain', 180000, 'Lapangan basket reguler dengan ring profesional.'),
(3, 'Lapangan C', 'Badminton', '4 pemain', 100000, 'Lapangan badminton indoor, bersih dan nyaman.'),
(4, 'Lapangan D', 'Mini Soccer', '20 pemain', 250000, 'Lapangan mini soccer rumput sintetis ukuran penuh.'),
(5, 'Lapangan E', 'Futsal', '10 pemain', 135000, 'Lapangan futsal outdoor dengan pencahayaan malam.');

INSERT IGNORE INTO users (username, password_hash, display_name, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin SportVenue', 'admin');
