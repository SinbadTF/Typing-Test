CREATE TABLE premium_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('books', 'lyrics', 'coding', 'knowledge') NOT NULL,
    language ENUM('en', 'my', 'jp') NOT NULL,
    lesson_number INT NOT NULL,
    difficulty_level ENUM('easy', 'medium', 'hard') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_lesson (category, language, lesson_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data for English books
INSERT INTO premium_books (title, author, content, category, language, lesson_number, difficulty_level, description) VALUES
('Harry Potter', 'J.K. Rowling', 'Mr. and Mrs. Dursley, of number four, Privet Drive, were proud to say that they were perfectly normal, thank you very much.', 'books', 'en', 1, 'easy', 'Practice typing with Harry Potter and the Philosopher\'s Stone'),

('The Lord of the Rings', 'J.R.R. Tolkien', 'When Mr. Bilbo Baggins of Bag End announced that he would shortly be celebrating his eleventy-first birthday with a party of special magnificence, there was much talk and excitement in Hobbiton.', 'books', 'en', 2, 'medium', 'Type along with The Fellowship of the Ring'),

('Pride and Prejudice', 'Jane Austen', 'It is a truth universally acknowledged, that a single man in possession of a good fortune, must be in want of a wife.', 'books', 'en', 3, 'medium', 'Practice with classic literature'),

('The Great Gatsby', 'F. Scott Fitzgerald', 'In my younger and more vulnerable years my father gave me some advice that I\'ve been turning over in my mind ever since.', 'books', 'en', 4, 'medium', 'Type through the American classic'),

('To Kill a Mockingbird', 'Harper Lee', 'When he was nearly thirteen, my brother Jem got his arm badly broken at the elbow.', 'books', 'en', 5, 'medium', 'Practice with Harper Lee\'s masterpiece'),

('1984', 'George Orwell', 'It was a bright cold day in April, and the clocks were striking thirteen.', 'books', 'en', 6, 'hard', 'Type through Orwell\'s dystopian novel'),

('The Hobbit', 'J.R.R. Tolkien', 'In a hole in the ground there lived a hobbit. Not a nasty, dirty, wet hole, filled with the ends of worms and an oozy smell, nor yet a dry, bare, sandy hole with nothing in it to sit down on or to eat: it was a hobbit-hole, and that means comfort.', 'books', 'en', 7, 'medium', 'Practice with The Hobbit'),

('The Chronicles of Narnia', 'C.S. Lewis', 'Once there were four children whose names were Peter, Susan, Edmund and Lucy.', 'books', 'en', 8, 'easy', 'Type along with The Lion, the Witch and the Wardrobe'),

('Little Women', 'Louisa May Alcott', 'Christmas won\'t be Christmas without any presents, grumbled Jo, lying on the rug.', 'books', 'en', 9, 'medium', 'Practice typing with this beloved classic');

-- Create table for all premium lessons
CREATE TABLE premium_lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('books', 'lyrics', 'coding', 'knowledge') NOT NULL,
    language ENUM('en', 'my', 'jp') NOT NULL,
    lesson_number INT NOT NULL,
    level_name VARCHAR(255) NOT NULL DEFAULT 'Basic Level',
    difficulty_level ENUM('easy', 'medium', 'hard') NOT NULL,
    description TEXT,
    is_locked BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_lesson (category, language, lesson_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data for Basic Level typing lessons
INSERT INTO premium_lessons (title, content, category, language, lesson_number, level_name, difficulty_level, description, is_locked) VALUES
('Home Row Keys', 'asdf jkl;', 'lyrics', 'en', 1, 'Basic Level', 'easy', 'Learn the home row keys', FALSE),
('Home Row Practice', 'asdf ;lkj', 'lyrics', 'en', 2, 'Basic Level', 'easy', 'Practice home row keys', TRUE),
('E and I Keys', 'asef ;lki', 'lyrics', 'en', 3, 'Basic Level', 'easy', 'Learn E and I keys', TRUE),
('Simple Words', 'the and for', 'lyrics', 'en', 4, 'Basic Level', 'medium', 'Practice simple words', TRUE),
('G and H Keys', 'asdfg ;lkjh', 'lyrics', 'en', 5, 'Basic Level', 'medium', 'Learn G and H keys', TRUE),
('R and U Keys', 'asdfr ;lkju', 'lyrics', 'en', 6, 'Basic Level', 'medium', 'Learn R and U keys', TRUE),
('Common Words', 'they have been', 'lyrics', 'en', 7, 'Basic Level', 'hard', 'Practice common words', TRUE),
('Speed Practice', 'quick typing test', 'lyrics', 'en', 8, 'Basic Level', 'hard', 'Speed typing practice', TRUE),
('Full Review', 'complete review', 'lyrics', 'en', 9, 'Basic Level', 'hard', 'Review all lessons', TRUE); 