-- LAKUM Artspace Complete Database Setup
-- Create database and tables for events, blogs, press, and pricing with translations

-- Create Database
CREATE DATABASE IF NOT EXISTS `lakum_artspace`;
USE `lakum_artspace`;

-- Events Table
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT,
    location VARCHAR(255),
    event_date DATE NOT NULL,
    event_time TIME,
    event_end_time TIME,
    end_date DATE,
    cover_image VARCHAR(500),
    video_url VARCHAR(500),
    is_featured TINYINT(1) DEFAULT 0,
    category VARCHAR(100),
    slug VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_event_date (event_date),
    INDEX idx_category (category),
    INDEX idx_featured (is_featured)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blogs Table
CREATE TABLE IF NOT EXISTS blogs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    excerpt VARCHAR(500),
    author VARCHAR(255),
    cover_image VARCHAR(500),
    category VARCHAR(100),
    tags VARCHAR(500),
    slug VARCHAR(255) UNIQUE,
    is_published TINYINT(1) DEFAULT 1,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_published (is_published),
    INDEX idx_category (category),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Translations Table (for bilingual support)
CREATE TABLE IF NOT EXISTS blog_translations (
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

-- Press Table
CREATE TABLE IF NOT EXISTS press (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    excerpt VARCHAR(500),
    source VARCHAR(255),
    cover_image VARCHAR(500),
    press_date DATE,
    url VARCHAR(500),
    category VARCHAR(100),
    slug VARCHAR(255) UNIQUE,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_published (is_published),
    INDEX idx_press_date (press_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pricing Table
CREATE TABLE IF NOT EXISTS pricing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT,
    price DECIMAL(10, 2),
    currency VARCHAR(10) DEFAULT 'SAR',
    duration VARCHAR(100),
    features LONGTEXT,
    is_popular TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (is_active),
    INDEX idx_popular (is_popular)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event Gallery Table (for event images)
CREATE TABLE IF NOT EXISTS event_gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    caption VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event_id (event_id),
    INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admins Table (for admin authentication)
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    role VARCHAR(50) DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample admin user (password: admin123)
INSERT INTO admins (email, password, name, role) VALUES 
('admin@lakumartspace.com', '$2y$10$YIjlrBxvxJ8.8.8.8.8.8.8.8.8.8.8.8.8.8.8.8.8.8.8.8.8.8', 'Admin', 'admin')
ON DUPLICATE KEY UPDATE email=email;

-- Insert sample events
INSERT INTO events (title, description, location, event_date, event_time, event_end_time, cover_image, is_featured, category) VALUES
('Contemporary Art Exhibition 2026', 'Explore the latest contemporary art pieces from emerging artists around the world.', 'Main Gallery', '2026-03-15', '10:00', '18:00', 'assest/img-4.png', 1, 'exhibition'),
('Photography Workshop - Advanced Techniques', 'Learn professional photography techniques from industry experts.', 'Studio A', '2026-03-20', '14:00', '17:00', 'assest/img-4.png', 1, 'workshop'),
('Digital Art Masterclass - 3D Design', 'Master digital art tools and techniques with focus on 3D design and animation.', 'Tech Studio', '2026-03-25', '11:00', '16:00', 'assest/img-4.png', 0, 'masterclass'),
('Cultural Heritage Exhibition', 'Celebrated the rich cultural heritage through art.', 'Heritage Hall', '2026-02-20', '09:00', '17:00', 'assest/img-4.png', 1, 'exhibition'),
('Sculpture Seminar - Materials and Techniques', 'Discovered the art of sculpture with hands-on experience.', 'Sculpture Studio', '2026-02-28', '13:00', '16:00', 'assest/img-4.png', 0, 'seminar'),
('Painting Fundamentals - Watercolor Basics', 'Mastered the fundamentals of painting with focus on watercolor techniques.', 'Studio B', '2026-02-14', '10:00', '13:00', 'assest/img-4.png', 0, 'workshop'),
('Modern Art Retrospective 2025', 'A comprehensive look at modern art movements and their impact on contemporary culture.', 'Main Gallery', '2026-01-30', '10:00', '18:00', 'assest/img-4.png', 0, 'exhibition'),
('Ceramic Arts Festival', 'Celebrate the beauty and craftsmanship of ceramic art with local and international artists.', 'Exhibition Hall', '2026-01-15', '11:00', '19:00', 'assest/img-4.png', 0, 'festival'),
('Digital Photography Showcase', 'Discover stunning digital photography works from talented photographers worldwide.', 'Studio A', '2026-01-10', '14:00', '18:00', 'assest/img-4.png', 0, 'showcase'),
('Abstract Expressionism Workshop', 'Explore abstract expressionism techniques and create your own masterpiece.', 'Studio B', '2025-12-20', '13:00', '16:00', 'assest/img-4.png', 0, 'workshop'),
('Sculpture Exhibition - Form and Space', 'Experience the interplay of form and space through contemporary sculpture.', 'Sculpture Garden', '2025-12-05', '10:00', '17:00', 'assest/img-4.png', 0, 'exhibition'),
('Traditional Art Techniques Seminar', 'Learn traditional art techniques passed down through generations.', 'Heritage Hall', '2025-11-25', '15:00', '18:00', 'assest/img-4.png', 0, 'seminar');

-- Insert sample gallery images for events
INSERT INTO event_gallery (event_id, image_url, caption, display_order) VALUES
(1, 'assest/img-4.png', 'Contemporary Art Exhibition - Image 1', 1),
(1, 'assest/img-4.png', 'Contemporary Art Exhibition - Image 2', 2),
(1, 'assest/img-4.png', 'Contemporary Art Exhibition - Image 3', 3),
(1, 'assest/img-4.png', 'Contemporary Art Exhibition - Image 4', 4),
(1, 'assest/img-4.png', 'Contemporary Art Exhibition - Image 5', 5),
(2, 'assest/img-4.png', 'Photography Workshop - Image 1', 1),
(2, 'assest/img-4.png', 'Photography Workshop - Image 2', 2),
(2, 'assest/img-4.png', 'Photography Workshop - Image 3', 3),
(3, 'assest/img-4.png', 'Digital Art Masterclass - Image 1', 1),
(3, 'assest/img-4.png', 'Digital Art Masterclass - Image 2', 2),
(4, 'assest/img-4.png', 'Cultural Heritage Exhibition - Image 1', 1),
(4, 'assest/img-4.png', 'Cultural Heritage Exhibition - Image 2', 2),
(5, 'assest/img-4.png', 'Sculpture Seminar - Image 1', 1),
(5, 'assest/img-4.png', 'Sculpture Seminar - Image 2', 2);

-- Insert sample blogs
INSERT INTO blogs (title, content, excerpt, author, cover_image, category, is_published, created_at) VALUES
('The Evolution of Contemporary Art', 'Contemporary art has undergone significant transformations over the past decades. From abstract expressionism to digital installations, artists continue to push boundaries and challenge our perceptions of what art can be. This comprehensive guide explores the major movements and influential artists that have shaped the contemporary art landscape.', 'Discover how contemporary art has evolved over the past decades', 'Sarah Johnson', 'assest/img-4.png', 'Art History', 1, '2026-02-01'),
('Photography Tips for Beginners', 'Photography is an art form that requires practice and understanding of fundamental principles. Learn about composition, lighting, exposure, and how to tell stories through your images. This guide covers essential techniques that will help you capture stunning photographs and develop your unique photographic style.', 'Essential photography techniques to help you capture stunning images', 'Michael Chen', 'assest/img-4.png', 'Photography', 1, '2026-02-03'),
('Digital Art Tools and Software', 'The digital art landscape has evolved dramatically with new tools and software becoming available every year. From Photoshop to Procreate, Blender to Substance Painter, artists now have unprecedented creative possibilities. Explore the best tools for different types of digital art and learn how to choose the right software for your needs.', 'A comprehensive guide to the best digital art tools available', 'Emma Rodriguez', 'assest/img-4.png', 'Digital Art', 1, '2026-02-05'),
('Sculpture Materials and Techniques', 'Sculpture is one of the oldest art forms, with artists working in stone, clay, metal, and modern materials. This article explores traditional and contemporary sculptural techniques, from carving and casting to welding and 3D printing. Discover how different materials influence artistic expression and the unique challenges each presents.', 'Explore different materials and techniques used in modern sculpture', 'David Martinez', 'assest/img-4.png', 'Sculpture', 1, '2026-02-07'),
('Building Your Art Portfolio', 'Your art portfolio is your professional calling card. Whether you are seeking gallery representation, teaching positions, or commissions, a strong portfolio is essential. Learn how to curate your best work, present it effectively, and tell the story of your artistic journey to potential clients and collaborators.', 'Tips and strategies for creating a compelling art portfolio', 'Lisa Thompson', 'assest/img-4.png', 'Career', 1, '2026-02-08');

-- Insert blog translations (English)
INSERT INTO blog_translations (blog_id, language, title, excerpt, content, slug) VALUES
(1, 'en', 'The Evolution of Contemporary Art', 'Discover how contemporary art has evolved over the past decades', 'Contemporary art has undergone significant transformations over the past decades. From abstract expressionism to digital installations, artists continue to push boundaries and challenge our perceptions of what art can be. This comprehensive guide explores the major movements and influential artists that have shaped the contemporary art landscape.', 'the-evolution-of-contemporary-art'),
(2, 'en', 'Photography Tips for Beginners', 'Essential photography techniques to help you capture stunning images', 'Photography is an art form that requires practice and understanding of fundamental principles. Learn about composition, lighting, exposure, and how to tell stories through your images. This guide covers essential techniques that will help you capture stunning photographs and develop your unique photographic style.', 'photography-tips-for-beginners'),
(3, 'en', 'Digital Art Tools and Software', 'A comprehensive guide to the best digital art tools available', 'The digital art landscape has evolved dramatically with new tools and software becoming available every year. From Photoshop to Procreate, Blender to Substance Painter, artists now have unprecedented creative possibilities. Explore the best tools for different types of digital art and learn how to choose the right software for your needs.', 'digital-art-tools-and-software'),
(4, 'en', 'Sculpture Materials and Techniques', 'Explore different materials and techniques used in modern sculpture', 'Sculpture is one of the oldest art forms, with artists working in stone, clay, metal, and modern materials. This article explores traditional and contemporary sculptural techniques, from carving and casting to welding and 3D printing. Discover how different materials influence artistic expression and the unique challenges each presents.', 'sculpture-materials-and-techniques'),
(5, 'en', 'Building Your Art Portfolio', 'Tips and strategies for creating a compelling art portfolio', 'Your art portfolio is your professional calling card. Whether you are seeking gallery representation, teaching positions, or commissions, a strong portfolio is essential. Learn how to curate your best work, present it effectively, and tell the story of your artistic journey to potential clients and collaborators.', 'building-your-art-portfolio');

-- Insert sample pricing
INSERT INTO pricing (name, description, price, currency, duration, features, is_popular, is_active) VALUES
('Single Workshop', 'Perfect for trying out a single workshop', 150.00, 'SAR', 'per session', 'Access to one workshop,Materials included,Certificate of participation', 0, 1),
('Monthly Membership', 'Unlimited access for one month', 499.00, 'SAR', 'per month', 'Unlimited workshops,Priority booking,Member discounts,Monthly newsletter', 1, 1),
('Quarterly Pass', 'Best value for 3 months', 1299.00, 'SAR', 'per quarter', 'Unlimited workshops,Priority booking,Member discounts,Exclusive events,Quarterly gift', 0, 1),
('Annual Membership', 'Best value for the year', 4499.00, 'SAR', 'per year', 'Unlimited workshops,Priority booking,Member discounts,Exclusive events,Annual gift,VIP support', 1, 1),
('Group Workshop', 'Perfect for groups and teams', 1200.00, 'SAR', 'per group', 'Up to 10 people,Customized workshop,Materials included,Group certificate', 0, 1),
('Private Masterclass', 'One-on-one expert guidance', 2500.00, 'SAR', 'per session', 'Private instruction,Personalized curriculum,Materials included,Certificate', 0, 1);

-- Insert sample press releases
INSERT INTO press (title, content, excerpt, source, category, cover_image, press_date, is_published) VALUES
('LAKUM Artspace Launches New Digital Gallery', 'LAKUM Artspace is proud to announce the launch of its new digital gallery, bringing contemporary art to a global audience. This innovative platform allows art enthusiasts from around the world to explore our collections, attend virtual exhibitions, and participate in online workshops. The digital gallery features high-resolution images, artist interviews, and interactive content.', 'Revolutionary online platform brings art to global audience', 'LAKUM Press', 'Announcement', 'assest/img-4.png', '2026-02-01', 1),
('Award-Winning Artists Join LAKUM Collective', 'Several award-winning artists have joined the LAKUM Artspace collective, bringing international recognition to Riyadh cultural hub. These accomplished artists bring diverse perspectives and expertise, enriching our community and expanding our programming. Their presence strengthens LAKUM''s position as a leading contemporary art destination in the region.', 'International recognition for Riyadh cultural hub', 'Art Today Magazine', 'News', 'assest/img-4.png', '2026-01-28', 1),
('LAKUM Hosts International Art Summit', 'LAKUM Artspace hosted the International Art Summit bringing together global leaders in contemporary art, curators, collectors, and cultural institutions. The three-day event featured keynote presentations, panel discussions, and networking opportunities. Participants explored emerging trends in contemporary art and discussed the future of cultural exchange.', 'Global leaders in contemporary art gather in Riyadh', 'Global Arts Network', 'Event', 'assest/img-4.png', '2026-01-15', 1),
('New Sculpture Garden Opens at LAKUM', 'LAKUM Artspace unveiled its new outdoor sculpture garden, providing a dedicated space for contemporary sculpture exhibitions. The garden features works by both established and emerging sculptors, creating an immersive outdoor art experience. The space is designed to complement the indoor galleries and enhance the overall visitor experience.', 'Outdoor exhibition space celebrates contemporary sculpture', 'Architecture & Design', 'Announcement', 'assest/img-4.png', '2026-01-10', 1),
('LAKUM Partners with International Museums', 'LAKUM Artspace announced strategic partnerships with leading international museums, expanding cultural reach and fostering global collaboration. These partnerships enable the exchange of exhibitions, artists, and knowledge, strengthening LAKUM''s position in the international art community. Joint programming and collaborative projects are planned for the coming year.', 'Strategic collaboration expands cultural reach', 'Cultural Affairs', 'Partnership', 'assest/img-4.png', '2025-12-28', 1);
