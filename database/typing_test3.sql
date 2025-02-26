-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 22, 2025 at 04:36 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `typing_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(2, 'ChawSuThwe', '$2y$12$xeXMXQpuSs7ZnlbT8NQuX.rSSVGGgrLEbztuj2bgYM7kvKKd.udWi');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `certificate_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `wpm` int(11) NOT NULL,
  `accuracy` decimal(5,2) NOT NULL,
  `issue_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `japanese_lessons`
--

CREATE TABLE `japanese_lessons` (
  `id` int(11) NOT NULL,
  `level` varchar(50) NOT NULL,
  `lesson_number` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `japanese_lessons`
--

INSERT INTO `japanese_lessons` (`id`, `level`, `lesson_number`, `title`, `description`, `content`, `created_at`) VALUES
(46, 'basic', 1, 'ひらがな - あいうえお', 'Basic vowels in Hiragana', 'あ い う え お', '2025-02-03 05:12:53'),
(47, 'basic', 2, 'ひらがな - かきくけこ', 'K-row characters', 'か き く け こ', '2025-02-03 05:12:53'),
(48, 'basic', 3, 'ひらがな - さしすせそ', 'S-row characters', 'さ し す せ そ', '2025-02-03 05:12:53'),
(49, 'basic', 4, 'ひらがな - たちつてと', 'T-row characters', 'た ち つ て と', '2025-02-03 05:12:53'),
(50, 'basic', 5, 'ひらがな - なにぬねの', 'N-row characters', 'な に ぬ ね の', '2025-02-03 05:12:53'),
(51, 'basic', 6, 'ひらがな - はひふへほ', 'H-row characters', 'は ひ ふ へ ほ', '2025-02-03 05:12:53'),
(52, 'basic', 7, 'ひらがな - まみむめも', 'M-row characters', 'ま み む め も', '2025-02-03 05:12:53'),
(53, 'basic', 8, 'ひらがな - やゆよ', 'Y-row characters', 'や ゆ よ', '2025-02-03 05:12:53'),
(54, 'basic', 9, 'ひらがな - らりるれろ', 'R-row characters', 'ら り る れ ろ', '2025-02-03 05:12:53'),
(55, 'basic', 10, 'ひらがな - わをん', 'W-row and N', 'わ を ん', '2025-02-03 05:12:53'),
(56, 'intermediate', 1, 'カタカナ - 基本母音', 'Basic Katakana vowels', 'ア イ ウ エ オ', '2025-02-03 05:12:53'),
(57, 'intermediate', 2, 'カタカナ - K行', 'K-row in Katakana', 'カ キ ク ケ コ', '2025-02-03 05:12:53'),
(58, 'intermediate', 3, 'カタカナ - S行', 'S-row in Katakana', 'サ シ ス セ ソ', '2025-02-03 05:12:53'),
(59, 'intermediate', 4, '挨拶表現', 'Greeting expressions', 'おはようございます こんにちは こんばんは', '2025-02-03 05:12:53'),
(60, 'intermediate', 5, '基本単語 1', 'Basic words part 1', 'わたし あなた これ それ あれ', '2025-02-03 05:12:53'),
(61, 'intermediate', 6, '基本単語 2', 'Basic words part 2', 'いぬ ねこ とり さかな うま', '2025-02-03 05:12:53'),
(62, 'intermediate', 7, '数字', 'Numbers in Japanese', 'いち に さん よん ご ろく なな はち きゅう じゅう', '2025-02-03 05:12:53'),
(63, 'intermediate', 8, '曜日', 'Days of the week', 'げつようび かようび すいようび もくようび きんようび どようび にちようび', '2025-02-03 05:12:53'),
(64, 'intermediate', 9, '月', 'Months of the year', 'いちがつ にがつ さんがつ しがつ ごがつ ろくがつ', '2025-02-03 05:12:53'),
(65, 'intermediate', 10, '時間表現', 'Time expressions', 'じ ぷん あさ ひる よる きょう あした', '2025-02-03 05:12:53'),
(66, 'advanced', 1, '漢字基礎 1', 'Basic Kanji set 1', '日本語 学校 先生 学生', '2025-02-03 05:12:53'),
(67, 'advanced', 2, '漢字基礎 2', 'Basic Kanji set 2', '山川木火水金土', '2025-02-03 05:12:53'),
(68, 'advanced', 3, '自己紹介', 'Self-introduction', '私は田中です。日本語を勉強しています。', '2025-02-03 05:12:53'),
(69, 'advanced', 4, 'ビジネス用語 1', 'Business terms 1', '会社 仕事 会議 取引 契約', '2025-02-03 05:12:53'),
(70, 'advanced', 5, 'ビジネス用語 2', 'Business terms 2', '部長 課長 社長 営業部 総務部', '2025-02-03 05:12:53'),
(71, 'advanced', 6, '敬語表現 1', 'Honorific expressions 1', 'お疲れ様です よろしくお願いします ありがとうございます', '2025-02-03 05:12:53'),
(72, 'advanced', 7, '敬語表現 2', 'Honorific expressions 2', 'いらっしゃいませ お待たせいたしました 申し訳ございません', '2025-02-03 05:12:53'),
(73, 'advanced', 8, '複文練習 1', 'Complex sentences 1', '私は日本語の勉強が好きです。毎日練習しています。', '2025-02-03 05:12:53'),
(74, 'advanced', 9, '複文練習 2', 'Complex sentences 2', '来週の月曜日に会議があります。準備をしてください。', '2025-02-03 05:12:53'),
(75, 'advanced', 10, 'メール文章', 'Email writing', 'お世話になっております。ご連絡ありがとうございます。', '2025-02-03 05:12:53');

-- --------------------------------------------------------

--
-- Table structure for table `japanese_lesson_progress`
--

CREATE TABLE `japanese_lesson_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `status` enum('started','completed') NOT NULL,
  `wpm` float DEFAULT NULL,
  `accuracy` float DEFAULT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `level` varchar(20) NOT NULL,
  `lesson_number` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `target_wpm` int(11) DEFAULT 30,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `level`, `lesson_number`, `title`, `content`, `target_wpm`, `created_at`) VALUES
(1, 'Basic', 1, 'Home Row Keys', 'aaaa jjjjjjj ssssss', 20, '2025-02-01 09:36:51'),
(2, 'basic', 2, 'Home Row Practice', 'asdf ;lkj asdf ;lkj', 25, '2025-02-01 09:36:51'),
(3, 'basic', 3, 'E and I Keys', 'asdef jkli', 25, '2025-02-01 09:36:51'),
(4, 'basic', 4, 'Simple Words', 'sale fail deal life', 30, '2025-02-01 09:36:51'),
(5, 'basic', 5, 'G and H Keys', 'asdfg jklh;', 30, '2025-02-01 09:36:51'),
(6, 'basic', 6, 'R and U Keys', 'asdfgr jklhu;', 35, '2025-02-01 09:36:51'),
(7, 'basic', 7, 'W and O Keys', 'wasdfgr jklhu;o', 35, '2025-02-01 09:36:51'),
(8, 'basic', 8, 'Q and P Keys', 'qwasdfgr jklhu;op', 40, '2025-02-01 09:36:51'),
(9, 'basic', 9, 'Short Sentences', 'The quick brown fox jumps over the lazy dog.', 40, '2025-02-01 09:36:51'),
(10, 'basic', 10, 'Basic Level Test', 'A quick test of all the keys you have learned so far.', 45, '2025-02-01 09:36:51'),
(11, 'intermediate', 1, 'Numbers Row', '1234567890', 30, '2025-02-01 09:36:51'),
(12, 'intermediate', 2, 'Symbols', '!@#$%^&*()', 35, '2025-02-01 09:36:51'),
(13, 'intermediate', 3, 'Capital Letters', 'The Quick Brown Fox', 40, '2025-02-01 09:36:51'),
(14, 'intermediate', 4, 'Common Words', 'there their they\'re where were we\'re', 45, '2025-02-01 09:36:51'),
(15, 'intermediate', 5, 'Punctuation', 'Hello, world! How are you today?', 45, '2025-02-01 09:36:51'),
(16, 'intermediate', 6, 'Email Format', 'user@example.com, admin@test.com', 50, '2025-02-01 09:36:51'),
(17, 'intermediate', 7, 'Code Syntax', 'if (condition) { return true; }', 50, '2025-02-01 09:36:51'),
(18, 'intermediate', 8, 'Technical Terms', 'JavaScript Python Ruby PHP MySQL', 55, '2025-02-01 09:36:51'),
(19, 'intermediate', 9, 'Mixed Practice', 'Test your skills with mixed content!', 55, '2025-02-01 09:36:51'),
(20, 'intermediate', 10, 'Speed Challenge', 'Can you type this at 60 WPM?', 60, '2025-02-01 09:36:51'),
(21, 'Advanced', 1, 'Speed Drills', 'I look up from the ground to see your sad and teary eyes\nYou look away from me and I see there\'s something you\'re trying to hide\nAnd I reach for your hand, but it\'s cold, you pull away again\nAnd I wonder what\'s on your mind\nAnd then you say to me you made a dumb mistake\nYou start to tremble and your voice begins to break\nYou say the cigarettes on the counter weren\'t your friend\'s\nThey were my mate\'s and I feel the colour draining from my face\nAnd my friend said\n\"I know you love her, but it\'s over, mate\nIt doesn\'t matter, put the phone away\nIt\'s never easy to walk away, let her go\nIt\'ll be alright\"', 60, '2025-02-01 09:36:51'),
(22, 'advanced', 2, 'Complex Words', 'Supercalifragilisticexpialidocious', 65, '2025-02-01 09:36:51'),
(23, 'advanced', 3, 'Code Blocks', 'function example() { return \"Hello World\"; }', 65, '2025-02-01 09:36:51'),
(24, 'advanced', 4, 'Technical Writing', 'Implementation of binary search algorithm', 70, '2025-02-01 09:36:51'),
(25, 'advanced', 5, 'Numbers & Symbols', '123 + 456 = 579; 789 * 2 = 1578', 70, '2025-02-01 09:36:51'),
(26, 'advanced', 6, 'Mixed Case Text', 'CamelCase snake_case PascalCase', 75, '2025-02-01 09:36:51'),
(27, 'advanced', 7, 'Paragraph Typing', 'A full paragraph with various punctuation marks.', 75, '2025-02-01 09:36:51'),
(28, 'advanced', 8, 'Code Comments', '// This is a comment /* Multi-line comment */', 80, '2025-02-01 09:36:51'),
(29, 'advanced', 9, 'Advanced Symbols', '€£¥§©®™≠≈∞∑∏π', 80, '2025-02-01 09:36:51'),
(30, 'advanced', 10, 'Master Test', 'Final test combining all advanced concepts.', 85, '2025-02-01 09:36:51');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `wpm` int(11) NOT NULL,
  `accuracy` float NOT NULL,
  `status` varchar(20) DEFAULT 'completed',
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lesson_progress`
--

INSERT INTO `lesson_progress` (`id`, `user_id`, `lesson_id`, `wpm`, `accuracy`, `status`, `completed_at`) VALUES
(1, 3, 1, 26, 84, 'completed', '2025-02-06 13:42:38'),
(2, 3, 2, 44, 95, 'completed', '2025-02-03 03:46:45'),
(3, 3, 3, 54, 100, 'completed', '2025-02-01 10:47:48'),
(4, 3, 4, 49, 95, 'completed', '2025-02-01 09:46:30'),
(5, 3, 16, 50, 91, 'completed', '2025-02-01 10:48:14'),
(6, 3, 22, 39, 82, 'completed', '2025-02-01 10:48:34'),
(7, 5, 1, 20, 89, 'completed', '2025-02-03 03:19:49'),
(8, 3, 11, 26, 100, 'completed', '2025-02-03 04:25:45'),
(9, 3, 5, 41, 100, 'completed', '2025-02-03 04:59:22'),
(10, 3, 21, 55, 100, 'completed', '2025-02-03 05:03:14'),
(11, 3, 56, 20, 100, 'completed', '2025-02-04 15:15:09'),
(12, 3, 46, 18, 100, 'completed', '2025-02-04 17:44:48'),
(13, 6, 46, 8, 100, 'completed', '2025-02-06 13:55:50'),
(14, 6, 1, 38, 89, 'completed', '2025-02-06 14:01:14'),
(15, 7, 21, 54, 96, 'completed', '2025-02-16 08:53:00');

-- --------------------------------------------------------

--
-- Table structure for table `premium_lessons`
--

CREATE TABLE `premium_lessons` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `level` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `target_wpm` int(11) DEFAULT 0,
  `completion_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `premium_lessons`
--

INSERT INTO `premium_lessons` (`id`, `type`, `level`, `title`, `description`, `content`, `target_wpm`, `completion_count`, `created_at`) VALUES
(1, 'speed', 1, 'Basic Speed Training', 'Master the fundamentals of fast typing', NULL, 30, 0, '2025-02-01 14:56:10'),
(2, 'speed', 2, 'Intermediate Pace', 'Build up your typing rhythm', NULL, 45, 0, '2025-02-01 14:56:10'),
(3, 'speed', 3, 'Advanced Speed', 'Push your limits with challenging exercises', NULL, 60, 0, '2025-02-01 14:56:10'),
(4, 'code', 1, 'Programming Basics', 'Learn to type common programming syntax', NULL, 25, 0, '2025-02-01 14:56:10');

-- --------------------------------------------------------

--
-- Table structure for table `sample_texts`
--

CREATE TABLE `sample_texts` (
  `text_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `difficulty_level` enum('easy','medium','hard') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sample_texts`
--

INSERT INTO `sample_texts` (`text_id`, `content`, `difficulty_level`, `created_at`) VALUES
(1, 'The quick brown fox jumps over the lazy dog.', 'easy', '2025-01-29 16:19:53'),
(2, 'Programming is the art of telling another human what one wants the computer to do.', 'medium', '2025-01-29 16:19:53'),
(3, 'Technology continues to evolve at an unprecedented rate, transforming the way we live.', 'medium', '2025-01-29 16:19:53');

-- --------------------------------------------------------

--
-- Table structure for table `test_results`
--

CREATE TABLE `test_results` (
  `result_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `wpm` int(11) NOT NULL,
  `accuracy` decimal(5,2) NOT NULL,
  `errors` int(11) NOT NULL,
  `test_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `screenshot_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `name`, `phone`, `amount`, `payment_method`, `status`, `created_at`, `screenshot_path`) VALUES
(1, 3, '', '', 10000.00, 'KBZ Pay', 'completed', '2025-02-01 13:37:40', 'uploads/transactions/679e23a4eef58_1738417060_kbzpay_logo.png'),
(2, 4, '', '', 10000.00, 'KBZ Pay', 'completed', '2025-02-01 13:46:12', 'uploads/transactions/679e25a450494_1738417572_ghost.jpg'),
(3, 7, 'Hein Htet Zaw', '09880177283', 10000.00, 'KBZ Pay', 'completed', '2025-02-15 17:39:51', 'uploads/transactions/67b0d16723c89_1739641191_image.png');

-- --------------------------------------------------------

--
-- Table structure for table `typing_results`
--

CREATE TABLE `typing_results` (
  `result_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `wpm` int(11) NOT NULL,
  `accuracy` int(11) NOT NULL,
  `mistakes` int(11) NOT NULL,
  `time_taken` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `typing_results`
--

INSERT INTO `typing_results` (`result_id`, `user_id`, `wpm`, `accuracy`, `mistakes`, `time_taken`, `created_at`) VALUES
(17, 5, 35, 97, 5, 60, '2025-02-02 17:44:22'),
(18, 5, 58, 93, 12, 38, '2025-02-03 05:55:46'),
(21, 7, 45, 92, 14, 44, '2025-02-06 14:32:47');

-- --------------------------------------------------------

--
-- Table structure for table `typing_texts`
--

CREATE TABLE `typing_texts` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `typing_texts`
--

INSERT INTO `typing_texts` (`id`, `content`, `difficulty`, `created_at`) VALUES
(5, 'To be yourself in a world that is constantly trying to make you something else is the greatest accomplishment.', 'hard', '2025-01-30 14:44:58'),
(10, 'Two things are infinite: the universe and human stupidity; and I am not sure about the universe.', 'hard', '2025-01-30 14:49:51'),
(11, 'You only live once, but if you do it right, once is enough.', 'medium', '2025-01-30 14:49:51'),
(15, 'The future belongs to those who believe in the beauty of their dreams.', 'medium', '2025-01-30 14:49:51'),
(16, 'The greatest glory in living lies not in never falling, but in rising every time we fall. Success is not final, failure is not fatal: it is the courage to continue that counts.', 'medium', '2025-01-30 15:20:05'),
(17, 'Life is like riding a bicycle. To keep your balance, you must keep moving forward. In the middle of difficulty lies opportunity. The only way to do great work is to love what you do.', 'medium', '2025-01-30 15:20:05'),
(18, 'Education is the most powerful weapon which you can use to change the world. The future belongs to those who believe in the beauty of their dreams and take action to make them reality.', 'hard', '2025-01-30 15:20:05'),
(19, 'Technology is best when it brings people together. Innovation distinguishes between a leader and a follower. Stay hungry, stay foolish, and never stop learning from your experiences.', 'hard', '2025-01-30 15:20:05'),
(20, 'The only limit to our realization of tomorrow will be our doubts of today. Let us make our future now, and let us make our dreams tomorrow\'s reality through perseverance and dedication.', 'medium', '2025-01-30 15:20:05'),
(21, 'Success usually comes to those who are too busy to be looking for it. The difference between ordinary and extraordinary is that little extra effort you put into everything you do.', 'medium', '2025-01-30 15:20:05'),
(22, 'Your time is limited, don\'t waste it living someone else\'s life. Don\'t be trapped by dogma, which is living with the results of other people\'s thinking and limiting your own potential.', 'hard', '2025-01-30 15:20:05'),
(23, 'The best preparation for tomorrow is doing your best today. What you do makes a difference, and you have to decide what kind of difference you want to make in this world.', 'medium', '2025-01-30 15:20:05'),
(24, 'It does not matter how slowly you go as long as you do not stop. Progress is not achieved by luck or accident, but by working on yourself daily to achieve your goals.', 'medium', '2025-01-30 15:20:05'),
(25, 'Everything you\'ve ever wanted is on the other side of fear. The only way to achieve the impossible is to believe it is possible and take consistent action towards your goals.', 'hard', '2025-01-30 15:20:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `profile_image` varchar(255) DEFAULT NULL,
  `is_premium` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `created_at`, `is_admin`, `profile_image`, `is_premium`) VALUES
(4, 'Htet', 'htetmonmyint@gamil.com', '$2y$12$XS4xckKsxreuKgaaz70hsOS4DXBj0tDWCAitHO9z6bHznFaqDi0a2', '2025-02-01 13:45:35', 0, NULL, 1),
(5, 'hannyi', 'hannyi123@gmail.com', '$2y$12$GazhmJWN8oeLHVjcYFlI/.l0RBpUPxMehXpf/g0qyIHXZV88IuHIa', '2025-02-02 17:41:16', 0, NULL, 0),
(6, 'HtetYupar', 'htethtet@gmail.com', '$2y$12$sZ9zTsean0gC7gbu4Tr8Eu1WAgMr10RsEwOXEn2LuKP96blk4XUw.', '2025-02-06 13:48:34', 0, NULL, 0),
(7, 'Sinbad The F', 'sinbad@gmail.com', '$2y$12$iovEhmxLknZY49ycrzngwOEvvX4nWfs2NoN63z1xNuGjFXG8PqEca', '2025-02-06 14:31:04', 0, NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`certificate_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `japanese_lessons`
--
ALTER TABLE `japanese_lessons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `japanese_lesson_progress`
--
ALTER TABLE `japanese_lesson_progress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `premium_lessons`
--
ALTER TABLE `premium_lessons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sample_texts`
--
ALTER TABLE `sample_texts`
  ADD PRIMARY KEY (`text_id`);

--
-- Indexes for table `test_results`
--
ALTER TABLE `test_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `typing_results`
--
ALTER TABLE `typing_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `typing_texts`
--
ALTER TABLE `typing_texts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `certificate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `japanese_lessons`
--
ALTER TABLE `japanese_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `japanese_lesson_progress`
--
ALTER TABLE `japanese_lesson_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `premium_lessons`
--
ALTER TABLE `premium_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sample_texts`
--
ALTER TABLE `sample_texts`
  MODIFY `text_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `test_results`
--
ALTER TABLE `test_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `typing_results`
--
ALTER TABLE `typing_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `typing_texts`
--
ALTER TABLE `typing_texts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `test_results`
--
ALTER TABLE `test_results`
  ADD CONSTRAINT `test_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `typing_results`
--
ALTER TABLE `typing_results`
  ADD CONSTRAINT `typing_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Create table for song lyrics
CREATE TABLE `premium_songs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `artist` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `difficulty` enum('Easy','Medium','Hard') DEFAULT 'Medium',
  `language` varchar(10) DEFAULT 'en',
  `lesson_number` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample songs
INSERT INTO `premium_songs` (`title`, `artist`, `content`, `difficulty`, `language`, `lesson_number`) VALUES
('Perfect', 'Ed Sheeran', 'I found a love for me\nDarling, just dive right in and follow my lead\nWell, I found a girl, beautiful and sweet\nOh, I never knew you were the someone waiting for me', 'Easy', 'en', 1),
('All of Me', 'John Legend', 'What would I do without your smart mouth\nDrawing me in, and you kicking me out\nYou got my head spinning, no kidding\nI can\'t pin you down', 'Medium', 'en', 2);
