-- SQLite database schema for tests
-- Categories table
CREATE TABLE IF NOT EXISTS category (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    auth_key VARCHAR(32) NOT NULL,
    access_token VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
-- Expenses table
CREATE TABLE IF NOT EXISTS expenses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    category_id INTEGER NOT NULL,
    description VARCHAR(255) NOT NULL,
    value DECIMAL(10, 2) NOT NULL,
    date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE RESTRICT
);
-- Insert default categories
INSERT
    OR IGNORE INTO category (id, name)
VALUES (1, 'Food'),
    (2, 'Transport'),
    (3, 'Entertainment'),
    (4, 'Healthcare'),
    (5, 'Shopping'),
    (6, 'Education'),
    (7, 'Bills'),
    (8, 'Other');
-- Insert test user
INSERT
    OR IGNORE INTO users (
        id,
        name,
        email,
        password_hash,
        auth_key,
        access_token,
        created_at,
        updated_at
    )
VALUES (
        1,
        'tester@example.com',
        'tester@example.com',
        '$2y$13$GrlKPJU3hBiTc/CkEGxL.OXLJfMnyFOyaJaOxRnz5QbXs1a8xV/l6',
        'test_auth_key_123456789012345',
        'test_access_token',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP
    );
-- Password is: ABCdef123!@#