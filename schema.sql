-- =====================================================================
-- Tournament Scoring System 
-- File: schema.sql
-- Purpose: Production-ready database schema (structure only, no data)
-- Engine: InnoDB    Charset: utf8mb4
-- Tested target: MySQL 8.0+ / MariaDB 10.4+ (XAMPP / phpMyAdmin)
-- =====================================================================

-- ---------------------------------------------------------------------
-- 0. Create and select the database
-- ---------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `college_competition`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;

USE `college_competition`;

-- ---------------------------------------------------------------------
-- Clean rebuild: drop existing objects in dependency order
-- ---------------------------------------------------------------------
DROP VIEW  IF EXISTS `individual_rankings`;
DROP VIEW  IF EXISTS `team_rankings`;
DROP TABLE IF EXISTS `individual_event_participation`;
DROP TABLE IF EXISTS `team_event_participation`;
DROP TABLE IF EXISTS `team_members`;
DROP TABLE IF EXISTS `individuals`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `teams`;
DROP TABLE IF EXISTS `admins`;

-- =====================================================================
-- 1. teams
--    One row per competing team.
-- =====================================================================
CREATE TABLE `teams` (
    `team_id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `team_name`  VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`team_id`),
    UNIQUE KEY `uq_team_name` (`team_name`),
    CONSTRAINT `chk_team_name_not_blank` CHECK (CHAR_LENGTH(TRIM(`team_name`)) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- 2. team_members
--    A team can have many members (replaces the old Member_1..Member_5).
-- =====================================================================
CREATE TABLE `team_members` (
    `member_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `team_id`     INT UNSIGNED NOT NULL,
    `member_name` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`member_id`),
    KEY `idx_member_team` (`team_id`),
    CONSTRAINT `fk_member_team`
        FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `chk_member_name_not_blank` CHECK (CHAR_LENGTH(TRIM(`member_name`)) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- 3. individuals
--    One row per solo competitor.
-- =====================================================================
CREATE TABLE `individuals` (
    `individual_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(100) NOT NULL,
    `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`individual_id`),
    UNIQUE KEY `uq_individual_name` (`name`),
    CONSTRAINT `chk_individual_name_not_blank` CHECK (CHAR_LENGTH(TRIM(`name`)) > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- 4. events
--    The list of competition events. event_type separates team events
--    from individual events. max_points is the highest score allowed
--    for that event.
-- =====================================================================
CREATE TABLE `events` (
    `event_id`   INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `event_name` VARCHAR(100) NOT NULL,
    `event_type` ENUM('team','individual') NOT NULL,
    `max_points` INT UNSIGNED NOT NULL DEFAULT 10,
    PRIMARY KEY (`event_id`),
    UNIQUE KEY `uq_event_name_type` (`event_name`, `event_type`),
    CONSTRAINT `chk_max_points_positive` CHECK (`max_points` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- 5. team_event_participation
--    Links a team to a team-event and stores the points it earned in
--    that event. A row means "this team takes part in this event".
--    points default to 0 until the admin enters a score.
-- =====================================================================
CREATE TABLE `team_event_participation` (
    `participation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `team_id`          INT UNSIGNED NOT NULL,
    `event_id`         INT UNSIGNED NOT NULL,
    `points`           INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`participation_id`),
    UNIQUE KEY `uq_team_event` (`team_id`, `event_id`),
    KEY `idx_tep_event` (`event_id`),
    CONSTRAINT `fk_tep_team`
        FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_tep_event`
        FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- 6. individual_event_participation
--    Links an individual to an individual-event and stores the points.
-- =====================================================================
CREATE TABLE `individual_event_participation` (
    `participation_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `individual_id`    INT UNSIGNED NOT NULL,
    `event_id`         INT UNSIGNED NOT NULL,
    `points`           INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`participation_id`),
    UNIQUE KEY `uq_individual_event` (`individual_id`, `event_id`),
    KEY `idx_iep_event` (`event_id`),
    CONSTRAINT `fk_iep_individual`
        FOREIGN KEY (`individual_id`) REFERENCES `individuals` (`individual_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_iep_event`
        FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- 7. admins
--    Stores the organizer account(s) that may enter scores.
--    Passwords must be stored as a hash (password_hash() in PHP),
--    never as plain text.
-- =====================================================================
CREATE TABLE `admins` (
    `admin_id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(50)  NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`admin_id`),
    UNIQUE KEY `uq_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- 8. VIEWS - automatic totals and automatic rankings
--    These calculate the totals from the event points, so the totals
--    are always correct and never typed in by hand.
-- =====================================================================

-- Team totals and ranking.
-- Tie-breaking rules, applied in order:
--   1. Highest total_score wins (rank 1 is the highest total).
--   2. If totals are equal, the team with the higher single best event
--      score (best_event) ranks higher.
--   3. If still equal, they share the same rank and are then listed
--      alphabetically by name (handled when the data is read).
CREATE OR REPLACE VIEW `team_rankings` AS
SELECT
    t.`team_id`,
    t.`team_name`,
    COALESCE(SUM(p.`points`), 0)      AS `total_score`,
    COALESCE(MAX(p.`points`), 0)      AS `best_event`,
    COUNT(p.`participation_id`)       AS `events_played`,
    RANK() OVER (
        ORDER BY COALESCE(SUM(p.`points`), 0) DESC,
                 COALESCE(MAX(p.`points`), 0) DESC
    ) AS `team_rank`
FROM `teams` t
LEFT JOIN `team_event_participation` p ON p.`team_id` = t.`team_id`
GROUP BY t.`team_id`, t.`team_name`;

-- Individual totals and ranking (same tie-breaking rules as teams).
CREATE OR REPLACE VIEW `individual_rankings` AS
SELECT
    i.`individual_id`,
    i.`name`,
    COALESCE(SUM(p.`points`), 0)      AS `total_score`,
    COALESCE(MAX(p.`points`), 0)      AS `best_event`,
    COUNT(p.`participation_id`)       AS `events_played`,
    RANK() OVER (
        ORDER BY COALESCE(SUM(p.`points`), 0) DESC,
                 COALESCE(MAX(p.`points`), 0) DESC
    ) AS `individual_rank`
FROM `individuals` i
LEFT JOIN `individual_event_participation` p ON p.`individual_id` = i.`individual_id`
GROUP BY i.`individual_id`, i.`name`;

-- =====================================================================
-- 9. TRIGGERS - business rules that a plain CHECK cannot enforce
--    (rules that need to look at another table, or count rows).
--    Rules enforced:
--      - a competitor may join at most 5 events (the task limit);
--      - a team may only join 'team' events, an individual only
--        'individual' events;
--      - points entered may not be more than the event's max_points.
-- =====================================================================
DELIMITER $$

-- ---- Team participation: before INSERT ----
CREATE TRIGGER `trg_tep_before_insert`
BEFORE INSERT ON `team_event_participation`
FOR EACH ROW
BEGIN
    DECLARE v_type ENUM('team','individual');
    DECLARE v_max  INT UNSIGNED;
    DECLARE v_count INT;

    SELECT `event_type`, `max_points` INTO v_type, v_max
    FROM `events` WHERE `event_id` = NEW.`event_id`;

    IF v_type <> 'team' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'A team can only join team events.';
    END IF;

    SELECT COUNT(*) INTO v_count
    FROM `team_event_participation` WHERE `team_id` = NEW.`team_id`;
    IF v_count >= 5 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'A team cannot join more than 5 events.';
    END IF;

    IF NEW.`points` > v_max THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Points cannot be greater than the event max points.';
    END IF;
END$$

-- ---- Team participation: before UPDATE (re-check points only) ----
CREATE TRIGGER `trg_tep_before_update`
BEFORE UPDATE ON `team_event_participation`
FOR EACH ROW
BEGIN
    DECLARE v_max INT UNSIGNED;
    SELECT `max_points` INTO v_max FROM `events` WHERE `event_id` = NEW.`event_id`;
    IF NEW.`points` > v_max THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Points cannot be greater than the event max points.';
    END IF;
END$$

-- ---- Individual participation: before INSERT ----
CREATE TRIGGER `trg_iep_before_insert`
BEFORE INSERT ON `individual_event_participation`
FOR EACH ROW
BEGIN
    DECLARE v_type ENUM('team','individual');
    DECLARE v_max  INT UNSIGNED;
    DECLARE v_count INT;

    SELECT `event_type`, `max_points` INTO v_type, v_max
    FROM `events` WHERE `event_id` = NEW.`event_id`;

    IF v_type <> 'individual' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'An individual can only join individual events.';
    END IF;

    SELECT COUNT(*) INTO v_count
    FROM `individual_event_participation` WHERE `individual_id` = NEW.`individual_id`;
    IF v_count >= 5 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'An individual cannot join more than 5 events.';
    END IF;

    IF NEW.`points` > v_max THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Points cannot be greater than the event max points.';
    END IF;
END$$

-- ---- Individual participation: before UPDATE (re-check points only) ----
CREATE TRIGGER `trg_iep_before_update`
BEFORE UPDATE ON `individual_event_participation`
FOR EACH ROW
BEGIN
    DECLARE v_max INT UNSIGNED;
    SELECT `max_points` INTO v_max FROM `events` WHERE `event_id` = NEW.`event_id`;
    IF NEW.`points` > v_max THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Points cannot be greater than the event max points.';
    END IF;
END$$

DELIMITER ;

-- =====================================================================
-- End of schema.sql
-- =====================================================================
