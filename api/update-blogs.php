<?php
/**
 * LAKUM Artspace - Update Blogs Script
 * Inserts additional blogs to match all filter categories
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db.php';

try {
    $db = Database::getInstance();
    
    if (!$db->isConnected()) {
        throw new Exception('Database connection failed');
    }
    
    // Clear existing blogs to start fresh
    $db->getConnection()->query('DELETE FROM blogs');
    
    $blogs = [
        // Art & Culture (4 blogs)
        [
            'title' => 'The Art of Contemporary Expression',
            'excerpt' => 'Exploring modern artistic movements and their impact on Saudi culture',
            'content' => 'Contemporary art has undergone significant transformations over the past decades, reflecting the evolving values and perspectives of society. In this article, we explore how modern artistic movements have shaped cultural discourse and influenced creative expression worldwide.',
            'author' => 'LAKUM Team',
            'category' => 'Art & Culture',
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
            'title' => 'The Evolution of Contemporary Art',
            'excerpt' => 'Discover how contemporary art has evolved over the past decades',
            'content' => 'Contemporary art has undergone significant transformations, reflecting the evolving values and perspectives of society. This comprehensive guide explores the major movements and artists that have shaped modern art.',
            'author' => 'Sarah Johnson',
            'category' => 'Art & Culture',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Understanding Modern Art Movements',
            'excerpt' => 'A deep dive into the major art movements of the 20th and 21st centuries',
            'content' => 'From Cubism to Abstract Expressionism, modern art movements have revolutionized how we perceive and create art. Learn about the key figures and philosophies behind these transformative movements.',
            'author' => 'Emma Rodriguez',
            'category' => 'Art & Culture',
            'cover_image' => 'assest/img-4.png'
        ],
        
        // Exhibition (4 blogs)
        [
            'title' => 'Latest Exhibition Highlights',
            'excerpt' => 'Discover the most talked-about artworks from our recent exhibitions',
            'content' => 'Our recent exhibitions have featured groundbreaking works from both established and emerging artists. This article highlights the most impactful pieces and explores the themes that resonated with our visitors.',
            'author' => 'LAKUM Team',
            'category' => 'Exhibition',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'Digital Art Tools and Software',
            'excerpt' => 'A comprehensive guide to the best digital art tools available',
            'content' => 'The digital art landscape has evolved dramatically with new tools and software emerging constantly. This guide explores the most popular and effective digital art tools available today.',
            'author' => 'Emma Rodriguez',
            'category' => 'Exhibition',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Upcoming Exhibition Schedule',
            'excerpt' => 'Mark your calendars for our exciting upcoming exhibitions',
            'content' => 'LAKUM Artspace is thrilled to announce an exciting lineup of exhibitions for the coming months. From contemporary art to traditional crafts, there is something for everyone.',
            'author' => 'LAKUM Team',
            'category' => 'Exhibition',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'Exhibition Review: Contemporary Visions',
            'excerpt' => 'A critical look at the Contemporary Visions exhibition',
            'content' => 'The Contemporary Visions exhibition showcased innovative works that challenged traditional perspectives on art. This review explores the key themes and standout pieces.',
            'author' => 'James Wilson',
            'category' => 'Exhibition',
            'cover_image' => 'assest/img-3.JPG'
        ],
        
        // Community (4 blogs)
        [
            'title' => 'LAKUM Impact on Local Artists',
            'excerpt' => 'How our space has become a catalyst for artistic growth',
            'content' => 'LAKUM Artspace has become a vibrant hub for local artists, providing them with the resources, platform, and community support they need to thrive. Through exhibitions, workshops, and collaborative projects, we continue to foster artistic growth.',
            'author' => 'Mohammed Al-Rashid',
            'category' => 'Community',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Community Spotlight: Local Artists',
            'excerpt' => 'Celebrating the talented artists in our community',
            'content' => 'Our community is home to incredibly talented artists working across various mediums and styles. In this spotlight, we celebrate their contributions and explore what drives their creative practice.',
            'author' => 'Fatima Al-Dosari',
            'category' => 'Community',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'Building Connections Through Art',
            'excerpt' => 'How art brings our community together',
            'content' => 'Art has the power to connect people across cultures and backgrounds. LAKUM Artspace serves as a gathering place where community members can share their passion for creativity and cultural expression.',
            'author' => 'LAKUM Team',
            'category' => 'Community',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Artist Residency Program',
            'excerpt' => 'Opportunities for artists to develop their practice',
            'content' => 'Our artist residency program provides emerging and established artists with dedicated studio space, resources, and mentorship to develop their artistic practice and create new work.',
            'author' => 'Sarah Al-Mansour',
            'category' => 'Community',
            'cover_image' => 'assest/img-4.png'
        ],
        
        // News (4 blogs)
        [
            'title' => 'Art News: Global Trends',
            'excerpt' => 'Stay updated with the latest developments in the art world',
            'content' => 'The global art scene continues to evolve with new movements, technologies, and perspectives shaping contemporary practice. Stay informed about the latest trends and developments that are influencing artists worldwide.',
            'author' => 'James Wilson',
            'category' => 'News',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'LAKUM Announces New Partnerships',
            'excerpt' => 'Exciting collaborations with international art institutions',
            'content' => 'LAKUM Artspace is proud to announce new partnerships with leading international art institutions. These collaborations will bring world-class exhibitions and programs to our community.',
            'author' => 'LAKUM Team',
            'category' => 'News',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'Art Market Insights',
            'excerpt' => 'Understanding trends in the contemporary art market',
            'content' => 'The contemporary art market continues to evolve with new collectors, platforms, and investment strategies emerging. This article provides insights into current market trends and what they mean for artists.',
            'author' => 'Michael Chen',
            'category' => 'News',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Technology Transforms Art',
            'excerpt' => 'How digital innovation is reshaping the art world',
            'content' => 'Technology is revolutionizing how art is created, displayed, and experienced. From virtual galleries to AI-generated art, explore how digital innovation is transforming the art world.',
            'author' => 'Emma Rodriguez',
            'category' => 'News',
            'cover_image' => 'assest/img-4.png'
        ],
        
        // Tutorial (4 blogs)
        [
            'title' => 'Photography Tips for Beginners',
            'excerpt' => 'Essential photography techniques to help you capture stunning images',
            'content' => 'Photography is an art form that requires practice and understanding of fundamental principles. In this tutorial, we cover essential techniques including composition, lighting, and post-processing.',
            'author' => 'Michael Chen',
            'category' => 'Tutorial',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'Building Your Art Portfolio',
            'excerpt' => 'Tips and strategies for creating a compelling art portfolio',
            'content' => 'Your art portfolio is your professional calling card. In this article, we provide practical tips and strategies for building a compelling portfolio that showcases your best work.',
            'author' => 'Lisa Thompson',
            'category' => 'Tutorial',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Getting Started with Digital Art',
            'excerpt' => 'A beginner\'s guide to digital art creation',
            'content' => 'Digital art offers endless possibilities for creative expression. This tutorial covers the basics of digital art, including software selection, tools, and fundamental techniques to get you started.',
            'author' => 'David Martinez',
            'category' => 'Tutorial',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'Mastering Color Theory',
            'excerpt' => 'Understanding color relationships and their impact on art',
            'content' => 'Color theory is fundamental to creating visually compelling artwork. Learn about color relationships, harmony, contrast, and how to use color effectively in your artistic practice.',
            'author' => 'Sarah Johnson',
            'category' => 'Tutorial',
            'cover_image' => 'assest/img-3.JPG'
        ],
        
        // Behind the Scenes (4 blogs)
        [
            'title' => 'Behind the Scenes: Curating an Exhibition',
            'excerpt' => 'A look into the meticulous process of bringing art to life',
            'content' => 'Curating an exhibition is a complex and rewarding process that requires careful planning, artistic vision, and attention to detail. From selecting artworks to designing the gallery layout, every element plays a crucial role.',
            'author' => 'Sarah Al-Mansour',
            'category' => 'Behind the Scenes',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'The Art Installation Process',
            'excerpt' => 'How we transform spaces into immersive art experiences',
            'content' => 'Installing an art exhibition requires careful planning, technical expertise, and creative vision. This behind-the-scenes look explores the process of transforming gallery spaces into immersive art experiences.',
            'author' => 'LAKUM Team',
            'category' => 'Behind the Scenes',
            'cover_image' => 'assest/img-3.JPG'
        ],
        [
            'title' => 'Meet the LAKUM Team',
            'excerpt' => 'Get to know the people behind LAKUM Artspace',
            'content' => 'LAKUM Artspace is powered by a dedicated team of art professionals, curators, and creative minds. Meet the people who work tirelessly to bring art and culture to our community.',
            'author' => 'LAKUM Team',
            'category' => 'Behind the Scenes',
            'cover_image' => 'assest/img-4.png'
        ],
        [
            'title' => 'Conservation and Preservation',
            'excerpt' => 'How we care for and preserve artworks',
            'content' => 'Proper conservation and preservation are essential for maintaining the integrity and longevity of artworks. Learn about the techniques and practices we use to care for the pieces in our collection.',
            'author' => 'James Wilson',
            'category' => 'Behind the Scenes',
            'cover_image' => 'assest/img-3.JPG'
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
        'total_blogs' => count($blogs),
        'breakdown' => [
            'Art & Culture' => 4,
            'Exhibition' => 4,
            'Community' => 4,
            'News' => 4,
            'Tutorial' => 4,
            'Behind the Scenes' => 4
        ],
        'errors' => $errors
    ]);
    
} catch (Exception $e) {
    error_log('Update Blogs Error: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

