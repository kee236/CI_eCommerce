-- ตาราง add_ons 
CREATE TABLE IF NOT EXISTS `add_ons` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  `add_on_name` varchar(255) NOT NULL,
  `unique_name` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `installed_at` datetime NOT NULL,
  `update_at` datetime NOT NULL,
  `purchase_code` varchar(100) NOT NULL,
  `module_folder_name` varchar(255) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name` (`unique_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- ตาราง modules
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(250) DEFAULT NULL,
  `add_ons_id` int(11) NOT NULL,
  `extra_text` varchar(50) NOT NULL DEFAULT 'month',
  `limit_enabled` enum('0','1') NOT NULL DEFAULT '1',
  `bulk_limit_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`add_ons_id`) REFERENCES `add_ons` (`id`) ON DELETE CASCADE 
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ตาราง messenger_bot
CREATE TABLE IF NOT EXISTS `messenger_bot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_type` enum('text','image','audio','video','file','quick reply','text with buttons','generic template','carousel','media','One Time Notification','User Input Flow','Ecommerce') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `bot_type` enum('generic','keyword') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generic',
  `keyword_type` enum('reply','post-back','no match','get-started','email-quick-reply','phone-quick-reply','location-quick-reply','birthday-quick-reply','story-mention','story-private-reply','message-unsend-private-reply') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'reply',
  `keywords` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `conditions` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `message_condition_false` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `buttons` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `audio` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `video` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `bot_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `postback_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_replied_at` datetime DEFAULT NULL,
  `is_template` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `broadcaster_labels` tinytext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comma separated',
  `drip_campaign_id` int(11) NOT NULL,
  `visual_flow_type` enum('flow','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `media_type` enum('fb','ig') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fb',
  `visual_flow_campaign_id` int(11) NOT NULL,
  `trigger_matching_type` enum('exact','string') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'exact' COMMENT 'exact keyword or string match',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`page_id`),
  KEY `xbot_query` (`fb_page_id`(191),`keyword_type`,`postback_id`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ตาราง messenger_bot_broadcast_contact_group
CREATE TABLE IF NOT EXISTS `messenger_bot_broadcast_contact_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `label_id` varchar(250) DEFAULT NULL,
  `social_media` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `deleted` enum('0','1') DEFAULT '0',
  `unsubscribe` enum('0','1') NOT NULL DEFAULT '0',
  `invisible` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`,`group_name`,`social_media`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ตาราง messenger_bot_domain_whitelist
CREATE TABLE IF NOT EXISTS `messenger_bot_domain_whitelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `messenger_bot_user_info_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `domain` tinytext NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`page_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ตาราง messenger_bot_persistent_menu
CREATE TABLE IF NOT EXISTS `messenger_bot_persistent_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` varchar(100) NOT NULL,
  `locale` varchar(20) NOT NULL DEFAULT 'default',
  `item_json` longtext NOT NULL,
  `composer_input_disabled` enum('0','1') NOT NULL DEFAULT '0',
  `poskback_id_json` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`,`locale`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ตาราง messenger_bot_postback
CREATE TABLE IF NOT EXISTS `messenger_bot_postback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `postback_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `use_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `messenger_bot_table_ 1

