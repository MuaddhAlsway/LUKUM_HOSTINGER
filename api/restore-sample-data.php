<?php
/**
 * Restore sample data to database
 */

header('Content-Type: application/json');

try {
    $conn = new mysqli('localhost', 'root', '', 'lakum_artspace');
    
    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }
    
    // Insert sample events
    $events_sql = "
    INSERT INTO events (title, description, location, event_date, event_time, event_end_time, cover_image, is_featured, category, slug) VALUES
    ('Contemporary Art Exhibition 2026', 'Explore the latest contemporary art pieces from emerging artists around the world.', 'Main Gallery', '2026-03-15', '10:00', '18:00', 'assest/img-4.png', 1, 'exhibition', 'contemporary-art-exhibition-2026'),
    ('Photography Workshop - Advanced Techniques', 'Learn professional photography techniques from industry experts.', 'Studio A', '2026-03-20', '14:00', '17:00', 'assest/img-4.png', 1, 'workshop', 'photography-workshop-advanced-techniques'),
    ('Digital Art Masterclass - 3D Design', 'Master digital art tools and techniques with focus on 3D design and animation.', 'Tech Studio', '2026-03-25', '11:00', '16:00', 'assest/img-4.png', 0, 'masterclass', 'digital-art-masterclass-3d-design'),
    ('Cultural Heritage Exhibition', 'Celebrated the rich cultural heritage through art.', 'Heritage Hall', '2026-02-20', '09:00', '17:00', 'assest/img-4.png', 1, 'exhibition', 'cultural-heritage-exhibition'),
    ('Sculpture Seminar - Materials and Techniques', 'Discovered the art of sculpture with hands-on experience.', 'Sculpture Studio', '2026-02-28', '13:00', '16:00', 'assest/img-4.png', 0, 'seminar', 'sculpture-seminar-materials-and-techniques')
    ";
    
    if (!$conn->query($events_sql)) {
        throw new Exception('Insert events failed: ' . $conn->error);
    }
    
    $events_inserted = $conn->affected_rows;
    
    // Insert sample blogs
    $blogs_sql = "
    INSERT INTO blogs (title, content, excerpt, author, cover_image, category, is_published, created_at) VALUES
    ('The Evolution of Contemporary Art', 'Contemporary art has undergone significant transformations over the past decades. From abstract expressionism to digital installations, artists continue to push boundaries and challenge our perceptions of what art can be. This comprehensive guide explores the major movements and influential artists that have shaped the contemporary art landscape.', 'Discover how contemporary art has evolved over the past decades', 'Sarah Johnson', 'assest/img-4.png', 'Art History', 1, '2026-02-01'),
    ('Photography Tips for Beginners', 'Photography is an art form that requires practice and understanding of fundamental principles. Learn about composition, lighting, exposure, and how to tell stories through your images. This guide covers essential techniques that will help you capture stunning photographs and develop your unique photographic style.', 'Essential photography techniques to help you capture stunning images', 'Michael Chen', 'assest/img-4.png', 'Photography', 1, '2026-02-03'),
    ('Digital Art Tools and Software', 'The digital art landscape has evolved dramatically with new tools and software becoming available every year. From Photoshop to Procreate, Blender to Substance Painter, artists now have unprecedented creative possibilities. Explore the best tools for different types of digital art and learn how to choose the right software for your needs.', 'A comprehensive guide to the best digital art tools available', 'Emma Rodriguez', 'assest/img-4.png', 'Digital Art', 1, '2026-02-05'),
    ('Sculpture Materials and Techniques', 'Sculpture is one of the oldest art forms, with artists working in stone, clay, metal, and modern materials. This article explores traditional and contemporary sculptural techniques, from carving and casting to welding and 3D printing. Discover how different materials influence artistic expression and the unique challenges each presents.', 'Explore different materials and techniques used in modern sculpture', 'David Martinez', 'assest/img-4.png', 'Sculpture', 1, '2026-02-07'),
    ('Building Your Art Portfolio', 'Your art portfolio is your professional calling card. Whether you are seeking gallery representation, teaching positions, or commissions, a strong portfolio is essential. Learn how to curate your best work, present it effectively, and tell the story of your artistic journey to potential clients and collaborators.', 'Tips and strategies for creating a compelling art portfolio', 'Lisa Thompson', 'assest/img-4.png', 'Career', 1, '2026-02-08')
    ";
    
    if (!$conn->query($blogs_sql)) {
        throw new Exception('Insert blogs failed: ' . $conn->error);
    }
    
    $blogs_inserted = $conn->affected_rows;
    
    // Insert blog translations (English)
    $trans_sql = "
    INSERT INTO blog_translations (blog_id, language, title, excerpt, content, slug) VALUES
    (1, 'en', 'The Evolution of Contemporary Art', 'Discover how contemporary art has evolved over the past decades', 'Contemporary art has undergone significant transformations over the past decades. From abstract expressionism to digital installations, artists continue to push boundaries and challenge our perceptions of what art can be. This comprehensive guide explores the major movements and influential artists that have shaped the contemporary art landscape.', 'the-evolution-of-contemporary-art'),
    (2, 'en', 'Photography Tips for Beginners', 'Essential photography techniques to help you capture stunning images', 'Photography is an art form that requires practice and understanding of fundamental principles. Learn about composition, lighting, exposure, and how to tell stories through your images. This guide covers essential techniques that will help you capture stunning photographs and develop your unique photographic style.', 'photography-tips-for-beginners'),
    (3, 'en', 'Digital Art Tools and Software', 'A comprehensive guide to the best digital art tools available', 'The digital art landscape has evolved dramatically with new tools and software becoming available every year. From Photoshop to Procreate, Blender to Substance Painter, artists now have unprecedented creative possibilities. Explore the best tools for different types of digital art and learn how to choose the right software for your needs.', 'digital-art-tools-and-software'),
    (4, 'en', 'Sculpture Materials and Techniques', 'Explore different materials and techniques used in modern sculpture', 'Sculpture is one of the oldest art forms, with artists working in stone, clay, metal, and modern materials. This article explores traditional and contemporary sculptural techniques, from carving and casting to welding and 3D printing. Discover how different materials influence artistic expression and the unique challenges each presents.', 'sculpture-materials-and-techniques'),
    (5, 'en', 'Building Your Art Portfolio', 'Tips and strategies for creating a compelling art portfolio', 'Your art portfolio is your professional calling card. Whether you are seeking gallery representation, teaching positions, or commissions, a strong portfolio is essential. Learn how to curate your best work, present it effectively, and tell the story of your artistic journey to potential clients and collaborators.', 'building-your-art-portfolio')
    ";
    
    if (!$conn->query($trans_sql)) {
        throw new Exception('Insert translations failed: ' . $conn->error);
    }
    
    $trans_inserted = $conn->affected_rows;
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Sample data restored successfully',
        'events_inserted' => $events_inserted,
        'blogs_inserted' => $blogs_inserted,
        'translations_inserted' => $trans_inserted
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

