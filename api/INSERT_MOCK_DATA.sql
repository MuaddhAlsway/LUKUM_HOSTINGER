 -- LAKUM Artspace - Mock Data for Testing
-- Insert 3 Upcoming Events, 3 Previous Events, and 3 Blogs

-- ============================================
-- UPCOMING EVENTS (3)
-- ============================================

INSERT INTO events (title, description, location, event_date, event_time, event_end_time, end_date, cover_image, video_url, is_featured, category, created_at, updated_at) VALUES
(
    'Contemporary Art Exhibition 2025',
    'Explore the latest contemporary art pieces from emerging artists around the world. This exhibition showcases innovative approaches to modern art with interactive installations and multimedia presentations.',
    'Main Gallery',
    '2025-03-15',
    '10:00:00',
    '18:00:00',
    '2025-03-15',
    'assest/img-4.png',
    '',
    1,
    'exhibition',
    NOW(),
    NOW()
),
(
    'Photography Workshop - Advanced Techniques',
    'Learn professional photography techniques from industry experts. This hands-on workshop covers composition, lighting, post-processing, and portfolio building for aspiring photographers.',
    'Studio A',
    '2025-03-20',
    '14:00:00',
    '17:00:00',
    '2025-03-20',
    'assest/img-4.png',
    '',
    1,
    'workshop',
    NOW(),
    NOW()
),
(
    'Digital Art Masterclass - 3D Design',
    'Master digital art tools and techniques with focus on 3D design and animation. Learn industry-standard software and create your own digital artwork under expert guidance.',
    'Tech Studio',
    '2025-03-25',
    '11:00:00',
    '16:00:00',
    '2025-03-25',
    'assest/img-4.png',
    '',
    0,
    'masterclass',
    NOW(),
    NOW()
);

-- ============================================
-- PREVIOUS EVENTS (3)
-- ============================================

INSERT INTO events (title, description, location, event_date, event_time, event_end_time, end_date, cover_image, video_url, is_featured, category, created_at, updated_at) VALUES
(
    'Cultural Heritage Exhibition',
    'Celebrated the rich cultural heritage through art. This exhibition featured traditional and contemporary interpretations of Saudi cultural themes, bringing together artists from diverse backgrounds.',
    'Heritage Hall',
    '2025-01-20',
    '09:00:00',
    '17:00:00',
    '2025-01-20',
    'assest/img-4.png',
    '',
    1,
    'exhibition',
    NOW(),
    NOW()
),
(
    'Sculpture Seminar - Materials and Techniques',
    'Discovered the art of sculpture with hands-on experience. Participants learned about different materials, carving techniques, and modern sculptural approaches from renowned sculptors.',
    'Sculpture Studio',
    '2025-02-05',
    '13:00:00',
    '16:00:00',
    '2025-02-05',
    'assest/img-4.png',
    '',
    0,
    'seminar',
    NOW(),
    NOW()
),
(
    'Painting Fundamentals - Watercolor Basics',
    'Mastered the fundamentals of painting with focus on watercolor techniques. This beginner-friendly workshop covered color theory, brush techniques, and creating beautiful watercolor paintings.',
    'Studio B',
    '2025-02-10',
    '10:00:00',
    '13:00:00',
    '2025-02-10',
    'assest/img-4.png',
    '',
    0,
    'workshop',
    NOW(),
    NOW()
);

-- ============================================
-- BLOGS (3)
-- ============================================

INSERT INTO blogs (title, excerpt, content, author, category, cover_image, is_published, created_at, updated_at) VALUES
(
    'The Evolution of Contemporary Art',
    'Discover how contemporary art has evolved over the past decades and continues to shape our cultural landscape.',
    '<p>Contemporary art represents a dynamic and ever-evolving landscape that reflects the complexities of our modern world. From abstract expressionism to digital installations, contemporary artists push boundaries and challenge our perceptions of what art can be.</p><p>The evolution of contemporary art has been marked by significant movements and innovations. Artists today draw inspiration from technology, social issues, environmental concerns, and personal experiences to create works that resonate with diverse audiences.</p><p>Understanding contemporary art requires an open mind and willingness to engage with new ideas and perspectives. Whether you are a seasoned collector or a curious newcomer, the world of contemporary art offers endless opportunities for discovery and appreciation.</p>',
    'Sarah Johnson',
    'Art History',
    'assest/img-4.png',
    1,
    NOW(),
    NOW()
),
(
    'Photography Tips for Beginners',
    'Essential photography techniques to help you capture stunning images and develop your unique photographic style.',
    '<p>Photography is an art form that combines technical knowledge with creative vision. Whether you are using a smartphone or a professional camera, understanding the fundamentals of photography will help you capture better images.</p><p><strong>Key Photography Tips:</strong></p><ul><li><strong>Composition:</strong> Use the rule of thirds to create balanced and visually appealing images.</li><li><strong>Lighting:</strong> Master natural and artificial lighting to enhance your photographs.</li><li><strong>Focus:</strong> Understand depth of field and focus techniques to draw attention to your subject.</li><li><strong>Post-Processing:</strong> Learn basic editing techniques to enhance your images.</li></ul><p>Practice regularly and experiment with different techniques to develop your unique photographic style. Remember, the best camera is the one you have with you, so start capturing moments today!</p>',
    'Michael Chen',
    'Photography',
    'assest/img-4.png',
    1,
    NOW(),
    NOW()
),
(
    'Digital Art Tools and Software',
    'A comprehensive guide to the best digital art tools and software available for artists of all skill levels.',
    '<p>The digital art landscape has transformed dramatically over the past decade, offering artists unprecedented creative possibilities. Whether you are interested in digital painting, 3D modeling, or animation, there are powerful tools available to bring your vision to life.</p><p><strong>Popular Digital Art Software:</strong></p><ul><li><strong>Adobe Creative Suite:</strong> Industry-standard tools for digital painting, photo editing, and design.</li><li><strong>Procreate:</strong> Powerful iPad app designed specifically for digital painting and illustration.</li><li><strong>Blender:</strong> Free and open-source 3D modeling and animation software.</li><li><strong>Clip Studio Paint:</strong> Specialized software for manga, comic, and illustration creation.</li></ul><p>Choosing the right tool depends on your artistic goals, budget, and learning preferences. Many software options offer free trials, allowing you to experiment before making a purchase decision. Start with what resonates with you and gradually expand your toolkit as your skills develop.</p>',
    'Emma Rodriguez',
    'Digital Art',
    'assest/img-4.png',
    1,
    NOW(),
    NOW()
);

-- ============================================
-- NOTES:
-- ============================================
-- 1. Run this SQL file to populate your database with mock data
-- 2. You can now edit all events and blogs from the admin panel
-- 3. Change images, content, dates, and other details as needed
-- 4. The data will automatically appear on the frontend pages
-- 5. Upcoming events: March 15, 20, 25 (future dates)
-- 6. Previous events: January 20, February 5, 10 (past dates)
