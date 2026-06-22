USE smart_travel_planner;

INSERT INTO admins (name, email, password, role)
VALUES ('System Admin', 'admin@roamify.test', '$2y$10$lzq.pFpLi2Ua.qs9OWIFceXxD5a/TprnC72ilskMrvrdLmTe4hnkG', 'super_admin')
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO users (name, email, password, role, status)
VALUES ('Demo Traveler', 'traveler@roamify.test', '$2y$10$lzq.pFpLi2Ua.qs9OWIFceXxD5a/TprnC72ilskMrvrdLmTe4hnkG', 'user', 'active')
ON DUPLICATE KEY UPDATE email = VALUES(email);

INSERT INTO destinations
  (id, title, country, city, category, description, location, latitude, longitude, estimated_cost, best_time, travel_tips, featured)
VALUES
  (1, 'Hunza Valley Escape', 'Pakistan', 'Hunza', 'Mountain', 'A scenic northern valley known for dramatic peaks, forts, apricot orchards, and peaceful village stays. Ideal for photography, hiking, and slow cultural travel.', 'Hunza Valley, Gilgit-Baltistan', 36.3167, 74.6500, 85000, 'April - October', 'Carry warm layers even in summer, keep cash for local markets, and plan road travel with buffer time.', 1),
  (2, 'Skardu Lakes Circuit', 'Pakistan', 'Skardu', 'Adventure', 'A high-altitude gateway to lakes, deserts, mountains, and trekking routes. The destination is excellent for groups seeking adventure with strong visual appeal.', 'Skardu, Gilgit-Baltistan', 35.2971, 75.6333, 95000, 'May - September', 'Book jeeps early for remote lakes, hydrate frequently, and check road conditions before departure.', 1),
  (3, 'Lahore Heritage Walk', 'Pakistan', 'Lahore', 'Heritage', 'A cultural city experience with Mughal architecture, food streets, museums, gardens, and old city lanes packed with history.', 'Walled City, Lahore', 31.5204, 74.3587, 42000, 'November - March', 'Start early for historical sites, dress modestly for mosques, and reserve evenings for food streets.', 1),
  (4, 'Dubai Urban Getaway', 'United Arab Emirates', 'Dubai', 'City', 'A modern city break with beaches, shopping, architecture, desert activities, and polished tourist infrastructure.', 'Dubai Marina and Downtown Dubai', 25.2048, 55.2708, 180000, 'November - February', 'Use metro where possible, pre-book popular attractions, and avoid outdoor activities during peak afternoon heat.', 1),
  (5, 'Bali Nature Retreat', 'Indonesia', 'Ubud', 'Nature', 'A tropical retreat centered on rice terraces, waterfalls, temples, wellness stays, and relaxed cultural exploration.', 'Ubud, Bali', -8.5069, 115.2625, 210000, 'April - October', 'Rent transport from trusted vendors, respect temple rules, and keep rain protection in your day bag.', 1),
  (6, 'Istanbul Crossroads Tour', 'Turkey', 'Istanbul', 'Heritage', 'A layered city where historic mosques, bazaars, ferries, neighborhoods, and modern dining meet across two continents.', 'Sultanahmet and Bosphorus', 41.0082, 28.9784, 160000, 'April - June, September - November', 'Buy museum passes where useful, carry comfortable shoes, and use public ferries for affordable views.', 1),
  (7, 'Swat Valley Weekend', 'Pakistan', 'Mingora', 'Family', 'A green valley option for families with riverside views, mountain drives, local markets, and accessible hotel stays.', 'Swat Valley, Khyber Pakhtunkhwa', 34.7717, 72.3600, 55000, 'March - October', 'Check weather before mountain drives and keep extra time for weekend traffic.', 0),
  (8, 'Kuala Lumpur City Break', 'Malaysia', 'Kuala Lumpur', 'City', 'A convenient international city trip with skyline views, food markets, cultural neighborhoods, and easy transit.', 'Kuala Lumpur City Centre', 3.1390, 101.6869, 145000, 'May - July, December - February', 'Use rail for central movement and keep rain-friendly footwear for sudden showers.', 0)
ON DUPLICATE KEY UPDATE title = VALUES(title);

DELETE FROM destination_images WHERE destination_id BETWEEN 1 AND 8;

INSERT INTO destination_images (destination_id, image_path, is_primary)
VALUES
  (1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fd/View_of_Rakaposhi_from_Hunza_valley.jpg/1280px-View_of_Rakaposhi_from_Hunza_valley.jpg', 1),
  (2, 'https://images.unsplash.com/photo-1595815771614-ade9d652a65d?auto=format&fit=crop&w=1200&q=80', 1),
  (3, 'https://images.unsplash.com/photo-1587474260584-136574528ed5?auto=format&fit=crop&w=1200&q=80', 1),
  (4, 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=1200&q=80', 1),
  (5, 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=80', 1),
  (6, 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200?auto=format&fit=crop&w=1200&q=80', 1),
  (7, 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1200&q=80', 1),
  (8, 'https://images.unsplash.com/photo-1508964942454-1a56651d54ac?auto=format&fit=crop&w=1200&q=80', 1);
