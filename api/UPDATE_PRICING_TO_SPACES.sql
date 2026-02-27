-- Update pricing table with correct space rental pricing
-- This replaces the old workshop pricing with actual space rental rates

-- Clear existing pricing
DELETE FROM pricing WHERE is_active = 1;

-- Insert correct space rental pricing
INSERT INTO pricing (title, content, price, price_unit, price_sec, vat_note, display_order, is_active) VALUES
('Hall 1', 'Main exhibition hall with full technical support', 12000, 'SAR', 'per day', '*(excluding VAT)', 1, 1),
('Hall 2', 'Secondary exhibition hall with projector and screen', 7200, 'SAR', 'per day', '*(excluding VAT)', 2, 1),
('Hourly Rate', 'Hourly booking for short-format experiences', 1000, 'SAR', 'per hour', '*(excluding VAT)', 3, 1),
('Set up/Dismantle Day', 'Dedicated day for setup or dismantling', 3400, 'SAR', 'per day', '*(excluding VAT)', 4, 1),
('Café', 'Café rental with redeemable credit', 3400, 'SAR', 'per day', '*(excluding VAT)', 5, 1),
('Meeting Room', 'Private meeting room with refreshments and tech', 60, 'SAR', 'per hour', '*(excluding VAT)', 6, 1);
