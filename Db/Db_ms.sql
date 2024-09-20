CREATE TABLE IF NOT EXISTS `messenger_bot_broadcast_serial` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `broadcast_type` enum('Non Promo','24H Promo','OTN') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Non Promo',
  `message_tag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NON_PROMOTIONAL_SUBSCRIPTION',
  `page_id` int(11) NOT NULL,
  `fb_page_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `excluded_label_ids` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `label_names` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_gender` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_time_zone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_locale` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_type` enum('text','image','audio','video','file','quick reply','text with buttons','generic template','carousel','list','media') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `message` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `buttons` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `audio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `campaign_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postback_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otn_postback_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'otn_postback table id',
  `created_at` datetime NOT NULL,
  `schedule_type` enum('now','later') COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule_time` datetime NOT NULL,
  `timezone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_count` int(11) NOT NULL,
  `posting_status` enum('0','1','2','3','4') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_try_again` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `last_try_error_count` int(11) NOT NULL,
  `total_thread` int(11) NOT NULL,
  `successfully_sent` int(11) NOT NULL,
  `successfully_delivered` int(11) NOT NULL,
  `successfully_opened` int(11) NOT NULL,
  `successfully_clicked` int(11) NOT NULL,
  `completed_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`page_id`),
  KEY `posting_status` (`posting_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `messenger_bot_broadcast_serial` ADD `social_media` ENUM('fb','ig') NOT NULL DEFAULT 'fb' AFTER `completed_at`;

CREATE TABLE IF NOT EXISTS `messenger_bot_broadcast_serial_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `subscribe_id` varchar(255) NOT NULL,
  `otn_token` varchar(255) NOT NULL,
  `subscriber_auto_id` int(11) NOT NULL,
  `subscriber_name` varchar(255) NOT NULL,
  `subscriber_lastname` varchar(200) NOT NULL,
  `sent_time` datetime NOT NULL,
  `delivered` enum('0','1') NOT NULL DEFAULT '0',
  `delivery_time` datetime NOT NULL,
  `opened` enum('0','1') NOT NULL DEFAULT '0',
  `open_time` datetime NOT NULL,
  `clicked` enum('0','1') NOT NULL DEFAULT '0',
  `click_ref` varchar(200) NOT NULL,
  `click_time` datetime NOT NULL,
  `processed` enum('0','1') NOT NULL DEFAULT '0',
  `error_message` tinytext NOT NULL,
  `message_sent_id` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`,`processed`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_drip_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_name` varchar(250) NOT NULL,
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message_content` text NOT NULL,
  `message_content_hourly` text NOT NULL,
  `created_at` datetime NOT NULL,
  `last_sent_at` datetime NOT NULL,
  `drip_type` enum('default','messenger_bot_engagement_checkbox','messenger_bot_engagement_send_to_msg','messenger_bot_engagement_mme','messenger_bot_engagement_messenger_codes','messenger_bot_engagement_2way_chat_plugin','custom') NOT NULL DEFAULT 'default',
  `campaign_type` enum('messenger','email','sms') NOT NULL DEFAULT 'messenger',
  `engagement_table_id` int(11) NOT NULL,
  `between_start` varchar(50) NOT NULL DEFAULT '00:00',
  `between_end` varchar(50) NOT NULL DEFAULT '23:59',
  `timezone` varchar(250) NOT NULL,
  `message_tag` varchar(255) NOT NULL,
  `total_unsubscribed` int(11) NOT NULL,
  `last_unsubscribed_at` datetime NOT NULL,
  `external_sequence_sms_api_id` varchar(50) NOT NULL,
  `external_sequence_email_api_id` varchar(50) NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `visual_flow_campaign_id` int(11) NOT NULL,
  `visual_flow_sequence_id` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`,`user_id`),
  KEY `xengagementtable_type` (`engagement_table_id`,`drip_type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_drip_campaign_assign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `subscribe_id` varchar(30) NOT NULL COMMENT 'fb id',
  `messenger_bot_drip_campaign_id` int(11) NOT NULL,
  `drip_type` enum('default','messenger_bot_engagement_checkbox','messenger_bot_engagement_send_to_msg','messenger_bot_engagement_mme','messenger_bot_engagement_messenger_codes','messenger_bot_engagement_2way_chat_plugin','custom') NOT NULL DEFAULT 'default',
  `messenger_bot_drip_last_completed_day` int(11) NOT NULL,
  `messenger_bot_drip_last_completed_hour` float NOT NULL,
  `messenger_bot_drip_is_toatally_complete` enum('0','1') NOT NULL DEFAULT '0',
  `messenger_bot_drip_is_toatally_complete_hourly` enum('0','1') NOT NULL DEFAULT '0',
  `messenger_bot_drip_last_sent_at` datetime NOT NULL,
  `messenger_bot_drip_initial_date` datetime NOT NULL,
  `last_processing_started_at` datetime NOT NULL,
  `last_processing_started_at_hourly` datetime NOT NULL,
  `messenger_bot_drip_processing_status` enum('0','1') NOT NULL DEFAULT '0',
  `messenger_bot_drip_processing_status_hourly` enum('0','1') NOT NULL DEFAULT '0',
  `is_unsubscribed` enum('0','1') NOT NULL DEFAULT '0',
  `unsubscribed_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscribe_id` (`subscribe_id`,`messenger_bot_drip_campaign_id`),
  KEY `xis_completed_hour` (`messenger_bot_drip_is_toatally_complete_hourly`),
  KEY `xis_completed_day` (`messenger_bot_drip_is_toatally_complete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messenger_bot_drip_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `messenger_bot_drip_campaign_id` int(11) NOT NULL,
  `messenger_bot_subscriber_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subscribe_id` varchar(250) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `last_completed_day` int(11) NOT NULL,
  `last_completed_hour` int(11) NOT NULL,
  `is_sent` enum('0','1') NOT NULL DEFAULT '1',
  `is_opened` enum('0','1') NOT NULL DEFAULT '0',
  `is_delivered` enum('0','1') NOT NULL DEFAULT '0',
  `sent_at` datetime NOT NULL,
  `delivered_at` datetime NOT NULL,
  `opened_at` datetime NOT NULL,
  `sent_response` tinytext NOT NULL,
  `delivered_response` tinytext NOT NULL,
  `last_updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `messenger_bot_drip_campaign_id` (`messenger_bot_drip_campaign_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `otn_optin_subscriber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `otn_id` int(11) NOT NULL,
  `subscriber_id` varchar(255) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `otn_token` varchar(255) NOT NULL,
  `optin_time` datetime NOT NULL,
  `is_sent` enum('0','1') NOT NULL DEFAULT '0',
  `sent_time` datetime NOT NULL,
  `sent_response` text NOT NULL,
  `deleted` enum('0','1') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `otn_id_subscriber_id` (`otn_id`,`subscriber_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `otn_postback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `otn_postback_id` varchar(191) NOT NULL,
  `reply_postback_id` varchar(255) NOT NULL,
  `label_id` varchar(191) NOT NULL,
  `drip_campaign_id` varchar(191) NOT NULL,
  `visual_flow_campaign_id` int(11) NOT NULL,
  `flow_type` enum('general','flow') NOT NULL DEFAULT 'flow',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `custom_page_builder`
(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `page_name` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `page_description` longtext NOT NULL,
    `url` int(11) NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `FX_USER_ID` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `body` longtext NOT NULL,
  `category_id` int(11) NOT NULL,
  `tags` text,
  `status` enum('0','1','2') NOT NULL DEFAULT '0',
  `views` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `tags` (`tags`(255)),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `user_input_custom_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_type` varchar(20) NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `custom field` (`user_id`,`reply_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_input_custom_fields_assaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` varchar(50) NOT NULL,
  `page_id` varchar(50) NOT NULL,
  `custom_field_id` int(11) NOT NULL,
  `custom_field_value` text CHARACTER SET utf8mb4 NOT NULL,
  `assaign_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriber_id` (`subscriber_id`,`page_id`,`custom_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `messenger_bot_subscriber_extra_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` varchar(50) NOT NULL,
  `page_id` varchar(50) NOT NULL,
  `input_flow_campaign_id` int(10) NOT NULL,
  `last_question_sent_id` int(10) NOT NULL,
  `last_question_sent_time` datetime NOT NULL,
  `email_quick_reply_button_id` varchar(30) NOT NULL,
  `phone_quick_reply_button_id` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriber_id` (`subscriber_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `instagram_reply_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auto_reply_campaign_name` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `reply_type` varchar(200) NOT NULL,
  `multiple_reply` enum('no','yes') NOT NULL,
  `nofilter_word_found_text` longtext NOT NULL,
  `auto_reply_text` longtext NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `error_message` text NOT NULL,
  `hide_comment_after_comment_reply` enum('no','yes') NOT NULL,
  `is_delete_offensive` enum('hide','delete') NOT NULL,
  `offensive_words` longtext NOT NULL,
  `private_message_offensive_words` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `ig_username` varchar(200) NOT NULL,
  `trigger_matching_type` enum('exact','string') NOT NULL DEFAULT 'exact' COMMENT 'exact keyword or string match',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `visual_flow_builder_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `unique_id` varchar(50) NOT NULL,
  `reference_name` text NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `json_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_system` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'system defined template or not',
  `action_type` enum('reply','UNSUBSCRIBE_QUICK_BOXER','RESUBSCRIBE_QUICK_BOXER','YES_START_CHAT_WITH_BOT','YES_START_CHAT_WITH_HUMAN','QUICK_REPLY_BIRTHDAY_REPLY_BOT','QUICK_REPLY_LOCATION_REPLY_BOT','QUICK_REPLY_PHONE_REPLY_BOT','QUICK_REPLY_EMAIL_REPLY_BOT','get-started','no match','STORY_MENTION','STORY_PRIVATE_REPLY','MESSAGE_UNSEND_PRIVATE_REPLY') NOT NULL DEFAULT 'reply',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `messenger_bot_subscribers_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_group_id` int(11) NOT NULL,
  `subscriber_table_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_group_id_2` (`contact_group_id`,`subscriber_table_id`),
  KEY `contact_group_id` (`contact_group_id`),
  KEY `subscriber_table_id` (`subscriber_table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `messenger_bot_message_sent_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` varchar(30) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `message_unique_id` varchar(100) NOT NULL,
  `message_type` enum('message','postback') NOT NULL DEFAULT 'message',
  `no_sent_click` int(12) NOT NULL,
  `error_count` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriber_id` (`subscriber_id`,`message_unique_id`,`page_table_id`),
  KEY `message_unique_id` (`message_unique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `visual_flow_campaign_unique_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_table_id` int(11) NOT NULL,
  `visual_flow_campaign_id` int(11) NOT NULL,
  `element_unique_id` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_table_id` (`page_table_id`,`visual_flow_campaign_id`,`element_unique_id`),
  KEY `visual_flow_campaign_id` (`visual_flow_campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;