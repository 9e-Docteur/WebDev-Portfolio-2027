CREATE TABLE IF NOT EXISTS page_views (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    path VARCHAR(255) NOT NULL,
    referrer VARCHAR(255) NULL,
    device VARCHAR(20) NOT NULL,
    browser VARCHAR(30) NOT NULL,
    visitor CHAR(16) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX page_views_created_at (created_at),
    INDEX page_views_path (path)
);
