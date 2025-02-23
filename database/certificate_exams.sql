CREATE TABLE certificate_exams (
    exam_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    wpm FLOAT NOT NULL,
    accuracy FLOAT NOT NULL,
    special_char_accuracy FLOAT NOT NULL,
    programming_accuracy FLOAT NOT NULL,
    total_score FLOAT NOT NULL,
    passed BOOLEAN NOT NULL,
    exam_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
CREATE TABLE exam_texts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO exam_texts (section, content) VALUES

('mixed_content', 'The quick brown fox jumps over 13 lazy dogs');
CREATE TABLE IF NOT EXISTS certificate_exams (
    exam_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    wpm INT NOT NULL,
    accuracy FLOAT NOT NULL,
    passed BOOLEAN NOT NULL DEFAULT 0,
    exam_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id
    FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);