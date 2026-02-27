-- Create Blog Translations Table
CREATE TABLE IF NOT EXISTS `blog_translations` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    blog_id INT NOT NULL,
    language VARCHAR(5) NOT NULL,
    title VARCHAR(255),
    excerpt VARCHAR(500),
    content LONGTEXT,
    slug VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_blog_lang (blog_id, language),
    INDEX idx_language (language)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
