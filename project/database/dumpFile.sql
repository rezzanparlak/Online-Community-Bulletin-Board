-- phpMyAdmin SQL Dump
-- version 4.9.2
-- Generation Time: Dec 12, 2024 at 02:43 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
                                       `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
    `password` varchar(255) COLLATE utf8mb4_turkish_ci NOT NULL,
    `email` varchar(100) COLLATE utf8mb4_turkish_ci NOT NULL,
    `type` enum('admin','content_creator','editor') COLLATE utf8mb4_turkish_ci NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `type`) VALUES
                                                                        (1, 'john', '$2y$10$lN.KSTr2BJJ73qFTCHpRyunhSBL4n5K0i7yWGRhgNKZpChvMuXS2C', 'john.smith@university.edu', 'admin'),
                                                                        (2, 'emily', '$2y$10$Fe3x9q0h9WGonqxogC8dAO6lzLeq1c3g32k8/rD4i7L.hdun05zPK', 'emily.miller@university.edu', 'content_creator'),
                                                                        (3, 'robert', '$2y$10$yh/sf7cCS7kMrJG4WV4xTOfGLv.VnJdNCkG.ibMvJvx28SaGOlMYq', 'robert.wilson@university.edu', 'editor'),
                                                                        (4, 'alice', '$2y$10$JzFYpoz8FTTxL132FFYwku4usjzYR1JU4H6OHy6VSrRLfdn.liQFa', 'alice.lee@university.edu', 'content_creator'),
                                                                        (5, 'miguel', '$2y$10$D.qTS1KozsLZLb/dpufhauTPcSb85Jxy0den1HC3aOADkP6QN9g5C', 'miguel.garcia@university.edu', 'editor'),
                                                                        (6, 'sarah', '$2y$10$SRyAP3qevWxXehcuPIOAk.FCXfXK8LxKxjZzZFaqKPCWmM7v9hpki', 'sarah.patel@university.edu', 'admin'),
                                                                        (7, 'james', '$2y$10$sRsuNMceFEdcgLjIycBag.PeOtk6o9rV3JEmNHGvdUVLZsbF3qEw2', 'james.chen@university.edu', 'content_creator'),
                                                                        (8, 'liu', '$2y$10$B.q/NAG4H57jiQtXKcF3pO2JrlDxawL6vtyZT7.H0FZWUrbmbOhMm', 'liu.zhang@university.edu', 'content_creator'),
                                                                        (9, 'karen', '$2y$10$rcPe.XfJSopFHS1xZXKdkeWSshTQd8FWZWUvRzweUEOaITltr14Li', 'karen.thomas@university.edu', 'editor'),
                                                                        (10, 'david', '$2y$10$NMxaRtXkV7lfrKs1vh0KNO2DeOEqQBWUfm.AwyJpfyfeaZ9ooTsa6', 'david.kim@university.edu', 'content_creator');
-- --------------------------------------------------------

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `content`;
CREATE TABLE IF NOT EXISTS `content` (
                                         `id` int(11) NOT NULL AUTO_INCREMENT,
    `creator_id` int(11) NOT NULL,
    `title` varchar(200) COLLATE utf8mb4_turkish_ci NOT NULL,
    `description` text COLLATE utf8mb4_turkish_ci NOT NULL,
    `image_path` varchar(255) COLLATE utf8mb4_turkish_ci NOT NULL,
    `img_category` varchar(50) COLLATE utf8mb4_turkish_ci NOT NULL,
    `status` enum('pending','approved','rejected') COLLATE utf8mb4_turkish_ci NOT NULL DEFAULT 'pending',
    PRIMARY KEY (`id`),
    KEY `creator_id` (`creator_id`),
    CONSTRAINT `content_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `creator_id`, `title`, `description`, `image_path`, `img_category`, `status`) VALUES
                                                                                                               (1, 2, 'Annual Science Fair 2025', 'Join us for the biggest science event of the year! Present your projects and win exciting prizes. Registration deadline: January 15, 2025. Open to all departments.', './posters/science_fair.jpg', 'events', 'approved'),
                                                                                                               (2, 4, 'Basketball Tournament Sign-ups', 'Inter-department basketball tournament starting February 2025. Form your teams now! Maximum 8 players per team. Registration fee: $20 per team.', './posters/basketball_signup.jpg', 'sports', 'approved'),
                                                                                                               (3, 7, 'Extended Library Hours', 'Library will remain open 24/7 during final exam week (Dec 15-22). Additional study rooms available on reservation basis.', './posters/library_hours.jpg', 'announcement', 'approved'),
                                                                                                               (4, 8, 'Guest Lecture: AI Ethics', 'Distinguished Prof. Sarah Johnson discussing "Ethics in AI" on Dec 20, 2024. Venue: Main Auditorium, 3:00 PM. All students welcome.', './posters/guest_lecture.jpg', 'academic', 'pending'),
                                                                                                               (5, 2, 'Student Council Elections', 'Cast your vote for Student Council 2025! Voting opens Dec 18-20. Student ID required. Make your voice heard!', './posters/election.jpg', 'announcement', 'approved'),
                                                                                                               (6, 10, 'Winter Music Festival', 'Three days of music, food, and fun! December 22-24. Featured performances by university bands. Free entry with student ID.', './posters/music_fest.jpg', 'events', 'approved'),
                                                                                                               (7, 7, 'New Database Subscriptions', 'Library now offers access to Scientific Journal Database and Historical Archives. Workshop on usage: Dec 16, 2:00 PM.', './posters/database_access.jpg', 'academic', 'pending'),
                                                                                                               (8, 4, 'Yoga Classes Schedule', 'Free yoga classes every Monday and Wednesday, 7:00 AM at the Sports Complex. Bring your own mat!', './posters/yoga_classes.jpg', 'sports', 'approved'),
                                                                                                               (9, 8, 'Research Funding Available', 'Applications open for undergraduate research grants. Deadline: January 10, 2025. Maximum funding: $5000 per project.', './posters/research_funding.jpg', 'academic', 'approved'),
                                                                                                               (10, 10, 'Career Fair 2025', 'Over 50 companies recruiting! January 25, 2025, 9:00 AM - 4:00 PM. Bring your resume. Professional dress required.', './posters/career_fair.jpg', 'events', 'pending'),
                                                                                                               (11, 2, 'Campus Sustainability Initiative', 'Join the Green Campus Movement! Workshop on recycling and sustainability. December 19, 1:00 PM, Room 301.', './posters/sustainability.jpg', 'announcement', 'approved'),
                                                                                                               (12, 4, 'Swimming Pool Maintenance', 'Pool closed for maintenance Dec 25-27. Regular schedule resumes Dec 28.', './posters/pool_maintenance.jpg', 'sports', 'approved'),
                                                                                                               (13, 7, 'Book Donation Drive', 'Donate your used textbooks! Collection point: Library entrance. Dec 15-30. Help make education accessible to all.', './posters/book_donation.jpg', 'announcement', 'pending'),
                                                                                                               (14, 8, 'Chemistry Lab Safety Training', 'Mandatory safety training for all chemistry students. Dec 17, 10:00 AM. Certification provided.', './posters/lab_safety.jpg', 'academic', 'approved'),
                                                                                                               (15, 10, 'Photography Contest', 'Theme: "Campus Life". Submit entries by Jan 5, 2025. First prize: New DSLR camera!', './posters/photo_contest.jpg', 'events', 'approved'),
                                                                                                               (16, 2, 'Mental Health Awareness Week', 'Free counseling sessions, workshops, and support groups. Dec 18-22. Your mental health matters!', './posters/mental_health.jpg', 'announcement', 'approved'),
                                                                                                               (17, 4, 'Intramural Sports Schedule', 'Updated schedule for winter sports. Basketball, volleyball, and badminton courts available for booking.', './posters/intramural.jpg', 'sports', 'pending'),
                                                                                                               (18, 7, 'New Study Room Booking System', 'Online booking system launched for library study rooms. Maximum 4 hours per booking.', './posters/study_rooms.jpg', 'announcement', 'approved'),
                                                                                                               (19, 8, 'Research Symposium Call', 'Submit your abstracts for Annual Research Symposium. Deadline: Jan 20, 2025. All disciplines welcome.', './posters/symposium.jpg', 'academic', 'approved'),
                                                                                                               (20, 10, 'International Food Festival', 'Celebrate diversity through food! Dec 21, 12:00-3:00 PM, Student Center. Register to set up a food stall.', './posters/food_festival.jpg', 'events', 'pending'),
                                                                                                                (21, 2, 'Coding Hackathon 2025', 'Build innovative solutions in 24 hours! Teams of 2-4 members. Prizes worth $2000. Starting Jan 30, 2025. Register by Jan 15.', './posters/hackathon.jpg', 'academic', 'approved'),
                                                                                                                (22, 4, 'Chess Tournament', 'Annual chess championship starting Jan 5, 2025. Individual and team categories. Registration fee: $10. Limited spots available.', './posters/chess_tournament.jpg', 'sports', 'approved'),
                                                                                                                (23, 7, 'Digital Resources Workshop', 'Learn to utilize new online research tools and databases. Dec 28, 11:00 AM. Remote attendance option available.', './posters/digital_workshop.jpg', 'academic', 'approved'),
                                                                                                                (24, 8, 'Environmental Film Screening', 'Documentary screening followed by panel discussion. Jan 8, 2025, 6:00 PM. Main Theater. Free admission.', './posters/env_film.jpg', 'events', 'approved'),
                                                                                                                (25, 10, 'Resume Building Workshop', 'Learn how to craft the perfect resume! Jan 12, 2025, 2:00 PM. Bring your laptop and current resume for review.', './posters/resume_workshop.jpg', 'academic', 'approved'),
                                                                                                                (26, 2, 'Campus Art Exhibition', 'Student artwork showcase. Theme: "Future Forward". Submissions open until Jan 15, 2025. All mediums welcome.', './posters/art_exhibit.jpg', 'events', 'approved'),
                                                                                                                (27, 4, 'Table Tennis Tournament', 'Singles and doubles categories. Jan 18-19, 2025. Sports Complex. Registration deadline: Jan 10.', './posters/table_tennis.jpg', 'sports', 'approved'),
                                                                                                                (28, 7, 'Language Exchange Program', 'Practice conversation skills with international students. Weekly meetings starting Jan 5, 2025. All languages welcome.', './posters/language_exchange.jpg', 'academic', 'approved'),
                                                                                                                (29, 8, 'Technology Innovation Fair', 'Showcase your tech projects! Feb 1, 2025. Cash prizes for top innovations. Registration opens Jan 1.', './posters/tech_fair.jpg', 'events', 'approved'),
                                                                                                                (30, 10, 'Wellness Workshop Series', 'Topics include stress management, nutrition, and fitness. Starting Jan 15, 2025. Weekly sessions, 5:00 PM.', './posters/wellness.jpg', 'announcement', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
    `content_id` int(11) NOT NULL,
    `editor_id` int(11) NOT NULL,
    `comment` text COLLATE utf8mb4_turkish_ci NOT NULL,
    PRIMARY KEY (`id`),
    KEY `content_id` (`content_id`),
    KEY `editor_id` (`editor_id`),
    CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE CASCADE,
    CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`editor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `content_id`, `editor_id`, `comment`) VALUES
                                                                        (1, 1, 3, 'Please add registration link and contact information for queries.'),
                                                                        (2, 2, 5, 'Include information about team uniform requirements.'),
                                                                        (3, 4, 9, 'Add directions to the auditorium and livestream link.'),
                                                                        (4, 7, 5, 'Specify if laptops will be provided for the workshop.'),
                                                                        (5, 10, 3, 'List participating companies and add dress code guidelines.'),
                                                                        (6, 13, 5, 'Include list of most-needed textbooks and subjects.'),
                                                                        (7, 14, 9, 'Add information about make-up sessions if available.'),
                                                                        (8, 15, 3, 'Specify image format requirements and submission process.'),
                                                                        (9, 17, 5, 'Add equipment rental information and costs.'),
                                                                        (10, 20, 9, 'Include food safety guidelines for participants.');

-- Table structure for table `user_permissions`
DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE IF NOT EXISTS `user_permissions` (
                                                  `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `can_search_own_content` BOOLEAN NOT NULL DEFAULT TRUE,
    `can_view_others_content` BOOLEAN NOT NULL DEFAULT FALSE,
    `can_add_content` BOOLEAN NOT NULL DEFAULT FALSE,
    `can_edit_content` BOOLEAN NOT NULL DEFAULT FALSE,
    `can_delete_content` BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- Sample data for user_permissions
INSERT INTO `user_permissions` (`user_id`, `can_search_own_content`, `can_view_others_content`, `can_add_content`, `can_edit_content`, `can_delete_content`)
VALUES
    (2, TRUE, TRUE, TRUE, True, TRUE), -- Content Creator Emily
    (4, False, False, False, FALSE, False), -- Content Creator Alice
    (7, TRUE, TRUE, TRUE, FALSE, TRUE), -- Content Creator James
    (8, TRUE, TRUE, TRUE, FALSE, TRUE), -- Content Creator Liu
    (10, TRUE, TRUE, TRUE, FALSE, TRUE); -- Content Creator David


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;