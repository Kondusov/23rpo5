-- Schema for simple messenger (users, chats, memberships, messages)
-- MySQL 5.7+ compatible

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS chat_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE chat_app;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Chats (group or direct)
CREATE TABLE IF NOT EXISTS chats (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NULL,
  is_direct TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Chat members
CREATE TABLE IF NOT EXISTS chat_members (
  chat_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (chat_id, user_id),
  KEY idx_chat_members_user (user_id),
  CONSTRAINT fk_chat_members_chat FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE,
  CONSTRAINT fk_chat_members_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Messages
CREATE TABLE IF NOT EXISTS messages (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  chat_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  content TEXT NULL,
  type ENUM('text','image') NOT NULL DEFAULT 'text',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_messages_chat (chat_id, id),
  KEY idx_messages_user (user_id),
  CONSTRAINT fk_messages_chat FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE,
  CONSTRAINT fk_messages_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Demo data
INSERT IGNORE INTO users (id, username, password_hash) VALUES
  (1, 'demo', '$2y$10$7i2yYvS3gZI3oB6Q8sE0JOF1g4nQPLm7nS0n8QFvZC7m8v1eZ9QdC'); -- password: demo1234

INSERT IGNORE INTO chats (id, name, is_direct) VALUES
  (1, 'General', 0);

INSERT IGNORE INTO chat_members (chat_id, user_id) VALUES
  (1, 1);

INSERT IGNORE INTO messages (id, chat_id, user_id, content, type) VALUES
  (1, 1, 1, 'Welcome to the chat!', 'text');


