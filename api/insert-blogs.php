<?php
/**
 * LAKUM Artspace - Insert Blogs Script
 * Inserts sample blogs with specific categories into the database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Clear existing blogs (optional - comment out if you want to keep existing blogs)
    // $db->getConnection()->query('DELETE FROM blogs');
    
    $blogs = [
        [
            'title' => 'The Art of Contemporary Expression',
            'excerpt' => 'Exploring modern artistic movements and their impact on Saudi culture',
            'content' => 'Contemporary art has undergone significant transformations over the past decades, reflecting the evolving values and perspectives of society. In this article, we explore how modern artistic movements have shaped cultural discourse and influenced creative expression worldwide.',
            'author' => 'LAKUM Team',
            'category' => 'Art & Culture',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Behind the Scenes: Curating an Exhibition',
            'excerpt' => 'A look into the meticulous process of bringing art to life',
            'content' => 'Curating an exhibition is a complex and rewarding process that requires careful planning, artistic vision, and attention to detail. From selecting artworks to designing the gallery layout, every element plays a crucial role in creating a memorable experience for visitors.',
            'author' => 'Sarah Al-Mansour',
            'category' => 'Behind the Scenes',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'LAKUM Impact on Local Artists',
            'excerpt' => 'How our space has become a catalyst for artistic growth',
            'content' => 'LAKUM Artspace has become a vibrant hub for local artists, providing them with the resources, platform, and community support they need to thrive. Through exhibitions, workshops, and collaborative projects, we continue to foster artistic growth and cultural exchange.',
            'author' => 'Mohammed Al-Rashid',
            'category' => 'Community',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Photography Tips for Beginners',
            'excerpt' => 'Essential photography techniques to help you capture stunning images',
            'content' => 'Photography is an art form that requires practice and understanding of fundamental principles. In this tutorial, we cover essential techniques including composition, lighting, and post-processing to help you capture stunning images.',
            'author' => 'Michael Chen',
            'category' => 'Tutorial',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'Digital Art Tools and Software',
            'excerpt' => 'A comprehensive guide to the best digital art tools available',
            'content' => 'The digital art landscape has evolved dramatically with new tools and software emerging constantly. This guide explores the most popular and effective digital art tools available today, helping artists choose the right software for their creative needs.',
            'author' => 'Emma Rodriguez',
            'category' => 'Exhibition',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Sculpture Materials and Techniques',
            'excerpt' => 'Explore different materials and techniques used in modern sculpture',
            'content' => 'Sculpture is one of the oldest art forms, and modern sculptors continue to push boundaries with innovative materials and techniques. From traditional stone and clay to contemporary mixed media, explore the diverse approaches to sculptural art.',
            'author' => 'David Martinez',
            'category' => 'Art & Culture',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'Building Your Art Portfolio',
            'excerpt' => 'Tips and strategies for creating a compelling art portfolio',
            'content' => 'Your art portfolio is your professional calling card. In this article, we provide practical tips and strategies for building a compelling portfolio that showcases your best work and attracts opportunities in the art world.',
            'author' => 'Lisa Thompson',
            'category' => 'Tutorial',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Latest Exhibition Highlights',
            'excerpt' => 'Discover the most talked-about artworks from our recent exhibitions',
            'content' => 'Our recent exhibitions have featured groundbreaking works from both established and emerging artists. This article highlights the most impactful pieces and explores the themes that resonated with our visitors.',
            'author' => 'LAKUM Team',
            'category' => 'Exhibition',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'Art News: Global Trends',
            'excerpt' => 'Stay updated with the latest developments in the art world',
            'content' => 'The global art scene continues to evolve with new movements, technologies, and perspectives shaping contemporary practice. Stay informed about the latest trends and developments that are influencing artists worldwide.',
            'author' => 'James Wilson',
            'category' => 'News',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Community Spotlight: Local Artists',
            'excerpt' => 'Celebrating the talented artists in our community',
            'content' => 'Our community is home to incredibly talented artists working across various mediums and styles. In this spotlight, we celebrate their contributions and explore what drives their creative practice.',
            'author' => 'Fatima Al-Dosari',
            'category' => 'Community',
            'cover_image' => 'assest/img-4.png'
        ]
    ];
    
    $inserted = 0;
    $errors = [];
    
    foreach ($blogs as $blog) {
        $query = 'INSERT INTO blogs (title, excerpt, content, author, category, cover_image, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())';
        
        $stmt = $db->prepare($query);
        if (!$stmt) {
            $errors[] = 'Prepare failed for: ' . $blog['title'];
            continue;
        }
        
        $stmt->bind_param('ssssss', $blog['title'], $blog['excerpt'], $blog['content'], $blog['author'], $blog['category'], $blog['cover_image']);
        
        if ($stmt->execute()) {
            $inserted++;
        } else {
            $errors[] = 'Insert failed for: ' . $blog['title'] . ' - ' . $stmt->error;
        }
        
        $stmt->close();
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Successfully inserted $inserted blogs",
        'inserted' => $inserted,
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    error_log('Insert Blogs Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
