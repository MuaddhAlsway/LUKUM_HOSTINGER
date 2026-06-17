-- Create Site Settings Table
CREATE TABLE IF NOT EXISTS `site_settings` (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings if they don't exist
INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES
('booking_link', ''),
('shop_link', '');
