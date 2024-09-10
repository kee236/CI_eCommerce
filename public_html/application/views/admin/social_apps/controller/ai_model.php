-- ตาราง ai_responses
CREATE TABLE IF NOT EXISTS `ai_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL, -- Foreign key to ai_requests table
  `response_type` enum('text', 'image', 'other') NOT NULL,
  `response_data` longtext NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`request_id`) REFERENCES `ai_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ตาราง ai_requests
CREATE TABLE IF NOT EXISTS `ai_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ai_model_id` int(11) NOT NULL, -- Foreign key to ai_models table
  `request_data` longtext NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`ai_model_id`) REFERENCES `ai_models` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ตาราง ai_models
CREATE TABLE IF NOT EXISTS `ai_models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_name` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `parameters` text, 
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- เพิ่มคอลัมน์ในตาราง messenger_bot
ALTER TABLE `messenger_bot` 
ADD COLUMN `is_ai_generated` enum('0','1') NOT NULL DEFAULT '0',
ADD COLUMN `ai_model_id` int(11) DEFAULT NULL,
ADD FOREIGN KEY (`ai_model_id`) REFERENCES `ai_models` (`id`) ON DELETE SET NULL;

-- เพิ่มคอลัมน์ในตาราง user_input_flow_questions
ALTER TABLE `user_input_flow_questions` 
ADD COLUMN `ai_enabled` enum('0','1') NOT NULL DEFAULT '0',
ADD COLUMN `ai_model_id` int(11) DEFAULT NULL,
ADD FOREIGN KEY (`ai_model_id`) REFERENCES `ai_models` (`id`) ON DELETE SET NULL;