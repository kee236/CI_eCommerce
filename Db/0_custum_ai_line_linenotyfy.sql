-- ตาราง ai_models
CREATE TABLE IF NOT EXISTS `ai_models` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `model_name` varchar(255) NOT NULL, 
    `provider` varchar(255) NOT NULL, -- เช่น OpenAI, Azure, etc.
    `description` text, 
    `capabilities` text, -- JSON array of supported features
    `api_key` varchar(255), 
    `status` enum('0','1') NOT NULL DEFAULT '1',
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ตาราง line_accounts
CREATE TABLE IF NOT EXISTS `line_accounts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `channel_id` varchar(255) NOT NULL,
    `channel_secret` varchar(255) NOT NULL,
    `channel_access_token` text NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ตาราง line_notify_tokens
CREATE TABLE IF NOT EXISTS `line_notify_tokens` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `token` text NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ตาราง line_group_ids
CREATE TABLE IF NOT EXISTS `line_group_ids` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `group_id` varchar(255) NOT NULL,
    `created_at` datetime NOT NULL,
    `updated_at` datetime NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ตาราง messenger_bot_thirdparty_webhook
ALTER TABLE `messenger_bot_thirdparty_webhook`
ADD COLUMN `line_account_id` int(11) DEFAULT NULL, -- สำหรับเชื่อมโยงกับ Line Account
ADD COLUMN `line_notify_token_id` int(11) DEFAULT NULL, -- สำหรับเชื่อมโยงกับ Line Notify Token
ADD COLUMN `line_group_id` int(11) DEFAULT NULL, -- สำหรับเชื่อมโยงกับ Line Group ID
ADD FOREIGN KEY (`line_account_id`) REFERENCES `line_accounts` (`id`) ON DELETE SET NULL,
ADD FOREIGN KEY (`line_notify_token_id`) REFERENCES `line_notify_tokens` (`id`) ON DELETE SET NULL,
ADD FOREIGN KEY (`line_group_id`) REFERENCES `line_group_ids` (`id`) ON DELETE SET NULL;

ALTER TABLE `ai_models`
ADD COLUMN `line_channel_id` VARCHAR(255) DEFAULT NULL, 
ADD COLUMN `line_channel_secret` VARCHAR(255) DEFAULT NULL,
ADD COLUMN `line_channel_access_token` TEXT DEFAULT NULL,
ADD COLUMN `line_notify_token` TEXT DEFAULT NULL;