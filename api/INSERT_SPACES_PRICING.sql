-- Insert Spaces Pricing Data from Public Spaces Page
-- This script inserts the 6 pricing options shown on spaces.html

USE `lakum-art`;

-- Clear existing pricing data (optional - comment out if you want to keep existing data)
-- DELETE FROM pricing;

-- Insert pricing data
INSERT INTO pricing (name, description, price, currency, duration, features, is_popular, is_active) VALUES

-- Hall 1
('Hall 1', 
'<h3>Hall 1</h3><p>Full day rental for Hall 1</p><h4>Services Included:</h4><ul><li><strong>Support Services:</strong> Provision of comprehensive logistical and technical support, including managing entry flow, on-site assistance staff, and professional cleaning services.</li><li><strong>Operational Services:</strong> Management of essential technical operations, covering lighting, sound systems, air conditioning, and reliable electrical supply.</li><li><strong>Custom Events Set Up:</strong> Provision of additional furniture and display items, available upon request and tailored to perfectly suit your event\'s specific requirements.</li></ul>',
12000.00,
'SAR',
'per day',
'Support Services, Operational Services, Custom Events Set Up',
0,
1),

-- Hall 2
('Hall 2',
'<h3>Hall 2</h3><p>Full day rental for Hall 2</p><h4>Services Included:</h4><ul><li><strong>Support Services:</strong> Provision of comprehensive logistical and technical support, including managing entry flow, on-site assistance staff, and professional cleaning services.</li><li><strong>Operational Services:</strong> Management of essential technical operations, covering lighting, sound systems, air conditioning, reliable electrical supply, and the provision of a projector and screen.</li><li><strong>Custom Events Set Up:</strong> Provision of additional furniture and display items, available upon request and tailored to perfectly suit your event\'s specific requirements.</li></ul>',
7200.00,
'SAR',
'per day',
'Support Services, Operational Services, Projector & Screen, Custom Events Set Up',
0,
1),

-- Hourly Rate
('Hourly Rate',
'<h3>Hourly Rate</h3><p>Short-format experiences and hourly bookings</p><h4>Available for:</h4><ul><li>Creative workshops and hands-on sessions</li><li>Talks, panels, and intimate discussions</li><li>Music lessons, rehearsals, or small performances</li><li>Yoga, wellness, and movement sessions</li><li>Training sessions or educational programs</li><li>Photoshoots and video filming</li><li>Other short gatherings or community-based activities</li></ul><p><strong>Note:</strong> Hourly rates apply only to short-duration events, typically lasting a few hours. This option is not available for full-day events, exhibitions, or large-scale productions.</p>',
NULL,
'SAR',
'Hall 1: 1,000/hour, Hall 2: 600/hour',
'Creative workshops, Talks & panels, Music lessons, Yoga & wellness, Training sessions, Photoshoots, Community activities',
0,
1),

-- Set up/Dismantle Day
('Set up/Dismantle Day',
'<h3>Set up/Dismantle Day</h3><p>Dedicated day for pre-event setup or post-event dismantling for multi-day events</p><p>This service is exclusively available for multi-day events that require a dedicated day for either pre-event setup or post-event dismantling. It provides essential access and support to ensure a smooth and efficient transition for your main event days.</p><p>We offer flexibility with the times and openings of the space to align precisely with the event organizer\'s needs.</p>',
3400.00,
'SAR',
'per day',
'Setup/Dismantle access, Flexible timing, Technical support',
0,
1),

-- Café
('Café',
'<h3>Café Rental</h3><p>Exclusive café rental when booking the entire space</p><p>This exclusive service is offered when a client chooses to rent the entire space, ensuring a fully private and uninterrupted experience.</p><p>The café can be booked in full, and the rental fee is fully redeemable, allowing the client to benefit from ordering beverages up to the same amount.</p>',
3400.00,
'SAR',
'per day',
'Full café rental, Fully redeemable credit for beverages, Private experience',
0,
1),

-- Meeting Room
('Meeting Room',
'<h3>Meeting Room</h3><p>Professional meeting space for up to 6 people</p><h4>Services Provided:</h4><ul><li><strong>Capacity:</strong> Up to six people.</li><li><strong>Inclusive Refreshments:</strong> Complimentary coffee of the day and water provided per person.</li><li><strong>Technology:</strong> Projector included and free high-speed Wi-Fi access.</li><li><strong>Supplies:</strong> Essential notepads and pens.</li></ul>',
60.00,
'SAR',
'per hour',
'Capacity up to 6 people, Complimentary coffee & water, Projector, Wi-Fi, Notepads & pens',
0,
1);

-- Verify the inserts
SELECT id, name, price, currency, duration FROM pricing ORDER BY id;
