CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(100) NOT NULL,
  type ENUM('income','expense') NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  category_id INT NULL,
  type ENUM('income','expense') NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  note VARCHAR(255),
  date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);
INSERT INTO users (name, email, password) VALUES ('Test User','test@example.com', '$2y$10$zjQ3pJm7q2oJ1sVjh6qS7e2qgBQ0SxQ2Qe0zNnq3S8UoYbP1h9b6C');
INSERT INTO categories (user_id, name, type) VALUES (1,'Gaji','income'),(1,'Penjualan','income'),(1,'Makan','expense'),(1,'Transport','expense');
INSERT INTO transactions (user_id, category_id, type, amount, note, date) VALUES (1,1,'income',5000000,'Gaji bulan Mei','2025-05-01'),(1,3,'expense',50000,'Makan siang','2025-05-02'),(1,4,'expense',20000,'Ojek','2025-05-03');