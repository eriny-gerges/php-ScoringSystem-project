-- =====================================================================
-- Tournament Scoring System 
-- File: sample_data.sql
-- Purpose: Realistic sample data for testing the schema.
-- Run AFTER schema.sql.
-- Matches the task scenario: 4 teams x 5 members, 20 individuals,
-- 5 team events and 5 individual events.
-- All points respect the event max_points (10) and the 5-event limit,
-- so the triggers in schema.sql accept this data.
-- =====================================================================

USE `college_competition`;

-- Start clean (children first because of foreign keys)
DELETE FROM `individual_event_participation`;
DELETE FROM `team_event_participation`;
DELETE FROM `team_members`;
DELETE FROM `individuals`;
DELETE FROM `events`;
DELETE FROM `teams`;
DELETE FROM `admins`;

-- ---------------------------------------------------------------------
-- 1. Admin account (username: admin, password: admin123).
--    The password is stored as a bcrypt hash, verified with password_verify().
-- ---------------------------------------------------------------------
INSERT INTO `admins` (`admin_id`, `username`, `password_hash`) VALUES
(1, 'admin', '$2y$10$.cymg15UDHjLsa5OzAKYhu..gAqf.1daqPnzG1HQFZVKGSEb822Iu');

-- ---------------------------------------------------------------------
-- 2. Events  (ids 1-5 = team events, ids 6-10 = individual events)
-- ---------------------------------------------------------------------
INSERT INTO `events` (`event_id`, `event_name`, `event_type`, `max_points`) VALUES
(1,  'Soccer',       'team',       10),
(2,  'Volleyball',   'team',       10),
(3,  'Basketball',   'team',       10),
(4,  'Handball',     'team',       10),
(5,  'Cricket',      'team',       10),
(6,  'Chess',        'individual', 10),
(7,  'Running',      'individual', 10),
(8,  'Table Tennis', 'individual', 10),
(9,  'Tennis',       'individual', 10),
(10, 'Swimming',     'individual', 10);

-- ---------------------------------------------------------------------
-- 3. Teams
-- ---------------------------------------------------------------------
INSERT INTO `teams` (`team_id`, `team_name`) VALUES
(1, 'Alpha Team'),
(2, 'Beta Team'),
(3, 'Gamma Team'),
(4, 'Delta Team');

-- ---------------------------------------------------------------------
-- 4. Team members (5 per team)
-- ---------------------------------------------------------------------
INSERT INTO `team_members` (`team_id`, `member_name`) VALUES
(1, 'Adam Hassan'),   (1, 'Bishoy Adel'),  (1, 'Karim Nabil'),  (1, 'Marco Samir'),  (1, 'Youssef Tarek'),
(2, 'Mina Fady'),     (2, 'Omar Khaled'),  (2, 'Peter Wagih'),  (2, 'Ramy Sobhy'),   (2, 'Seif Amr'),
(3, 'George Maher'),  (3, 'Hany Sameh'),   (3, 'Ibrahim Ali'),  (3, 'John Magdy'),   (3, 'Kirollos Emad'),
(4, 'Mark Ashraf'),   (4, 'Nader Fouad'),  (4, 'Osama Reda'),   (4, 'Pavly Nashaat'),(4, 'Tony Gamal');

-- ---------------------------------------------------------------------
-- 5. Individuals (20 solo competitors)
-- ---------------------------------------------------------------------
INSERT INTO `individuals` (`individual_id`, `name`) VALUES
(1,  'Eriny Gerges'),  (2,  'Sara Adel'),    (3,  'Mariam Nabil'), (4,  'Nada Tarek'),
(5,  'Habiba Sameh'),  (6,  'Farida Hany'),  (7,  'Aya Mostafa'),  (8,  'Salma Wael'),
(9,  'Rana Fady'),     (10, 'Mai Sobhy'),    (11, 'Dina Ashraf'),  (12, 'Yara Emad'),
(13, 'Reem Khaled'),   (14, 'Hana Magdy'),   (15, 'Nourhan Reda'), (16, 'Engy Ali'),
(17, 'Mirna Samir'),   (18, 'Carol Nashaat'),(19, 'Doaa Gamal'),   (20, 'Basant Fouad');

-- ---------------------------------------------------------------------
-- 6. Team event participation and scores
--    Each team joins all 5 team events. points are within 0..10.
-- ---------------------------------------------------------------------
INSERT INTO `team_event_participation` (`team_id`, `event_id`, `points`) VALUES
-- Alpha Team
(1, 1, 9), (1, 2, 7), (1, 3, 8), (1, 4, 6), (1, 5, 10),
-- Beta Team
(2, 1, 6), (2, 2, 9), (2, 3, 7), (2, 4, 8), (2, 5, 5),
-- Gamma Team
(3, 1, 8), (3, 2, 6), (3, 3, 9), (3, 4, 7), (3, 5, 7),
-- Delta Team
(4, 1, 7), (4, 2, 8), (4, 3, 6), (4, 4, 10), (4, 5, 9);

-- ---------------------------------------------------------------------
-- 7. Individual event participation and scores
--    Flexible participation: some join 1 event, some join up to 5.
--    Individual events have ids 6..10.
-- ---------------------------------------------------------------------
INSERT INTO `individual_event_participation` (`individual_id`, `event_id`, `points`) VALUES
-- joins all 5 events
(1, 6, 9), (1, 7, 8), (1, 8, 10), (1, 9, 7), (1, 10, 6),
-- joins 3 events
(2, 6, 7), (2, 7, 9), (2, 10, 8),
-- joins 1 event
(3, 7, 10),
-- joins 4 events
(4, 6, 6), (4, 8, 7), (4, 9, 9), (4, 10, 8),
-- joins 2 events
(5, 7, 8), (5, 9, 6),
-- joins 5 events
(6, 6, 8), (6, 7, 7), (6, 8, 9), (6, 9, 8), (6, 10, 7),
-- joins 1 event
(7, 10, 9),
-- joins 3 events
(8, 6, 5), (8, 8, 8), (8, 9, 7),
-- joins 2 events
(9, 7, 6), (9, 10, 10),
-- joins 4 events
(10, 6, 7), (10, 7, 8), (10, 8, 6), (10, 9, 9),
-- joins 1 event
(11, 8, 7),
-- joins 2 events
(12, 9, 8), (12, 10, 9),
-- joins 3 events
(13, 6, 9), (13, 7, 7), (13, 8, 8),
-- joins 1 event
(14, 6, 6),
-- joins 5 events
(15, 6, 7), (15, 7, 9), (15, 8, 7), (15, 9, 8), (15, 10, 9),
-- joins 2 events
(16, 7, 8), (16, 9, 7),
-- joins 3 events
(17, 8, 9), (17, 9, 6), (17, 10, 8),
-- joins 1 event
(18, 9, 10),
-- joins 2 events
(19, 6, 8), (19, 10, 7),
-- joins 4 events
(20, 6, 7), (20, 7, 6), (20, 8, 8), (20, 9, 9);

-- =====================================================================
-- Quick checks (optional - run manually to confirm the data):
--   SELECT * FROM team_rankings;
--   SELECT * FROM individual_rankings;
-- =====================================================================
-- End of sample_data.sql
-- =====================================================================
