`user_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_type` varchar(20) NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `create_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `custom field` (`user_id`,`reply_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- user_input_custom_fields_assaign
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

-- messenger_bot_subscriber_extra_info
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

-- instagram_reply_template
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

-- messenger_bot_subscribers_label
CREATE TABLE IF NOT EXISTS `messenger_bot_subscribers_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_group_id` int(11) NOT NULL,
  `subscriber_table_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_group_id_2` (`contact_group_id`,`subscriber_table_id`),
  KEY `contact_group_id` (`contact_group_id`),
  KEY `subscriber_table_id` (`subscriber_table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- messenger_bot_message_sent_stat
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

-- visual_flow_campaign_unique_ids
CREATE TABLE IF NOT EXISTS `visual_flow_campaign_unique_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_table_id` int(11) NOT NULL,
  `visual_flow_campaign_id` int(11) NOT NULL,
  `element_unique_id` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_table_id` (`page_table_id`,`visual_flow_campaign_id`,`element_unique_id`),
  KEY `visual_flow_campaign_id` (`visual_flow_campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- messenger_bot_engagement_checkbox
CREATE TABLE IF NOT EXISTS `messenger_bot_engagement_checkbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_code` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL COMMENT 'auto id',
  `domain_name` varchar(255) NOT NULL,
  `btn_size` enum('small','medium','large','xlarge') NOT NULL DEFAULT 'medium',
  `skin` enum('light','dark') NOT NULL DEFAULT 'light' COMMENT 'light=black, dark=white',
  `center_align` enum('true','false') NOT NULL DEFAULT 'true',
  `button_click_success_message` tinytext NOT NULL,
  `validation_error` tinytext NOT NULL,
  `label_ids` varchar(250) NOT NULL COMMENT 'comma seperated,messenger_bot_broadcast_contact_group.id',
  `reference` varchar(250) NOT NULL,
  `template_id` int(11) NOT NULL COMMENT 'messenger_bot_postback.id',
  `language` varchar(200) NOT NULL DEFAULT 'en_US',
  `created_at` datetime NOT NULL,
  `redirect` enum('0','1') NOT NULL DEFAULT '0',
  `add_button_with_message` enum('0','1') NOT NULL DEFAULT '0',
  `button_with_message_content` tinytext NOT NULL COMMENT 'json',
  `success_redirect_url` tinytext NOT NULL,
  `for_woocommerce` enum('0','1') NOT NULL DEFAULT '0',
  `visual_flow_campaign_id` int(11) NOT NULL,
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain_code` (`domain_code`),
  KEY `user_id` (`user_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- messenger_bot_engagement_mme
CREATE TABLE IF NOT EXISTS `messenger_bot_engagement_mme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL COMMENT 'auto id',
  `domain_name` varchar(255) NOT NULL,
  `link_code` varchar(100) NOT NULL,
  `btn_size` enum('small','medium','large','xlarge') NOT NULL DEFAULT 'medium',
  `new_button_bg_color` varchar(100) NOT NULL,
  `new_button_bg_color_hover` varchar(100) NOT NULL,
  `new_button_color` varchar(100) NOT NULL,
  `new_button_color_hover` varchar(100) NOT NULL,
  `new_button_display` enum('show','hide') NOT NULL DEFAULT 'show',
  `label_ids` varchar(250) NOT NULL COMMENT 'comma seperated,messenger_bot_broadcast_contact_group.id',
  `reference` varchar(250) NOT NULL,
  `template_id` int(11) NOT NULL COMMENT 'messenger_bot_postback.id',
  `created_at` datetime NOT NULL,
  `visual_flow_campaign_id` int(11) NOT NULL,
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
  PRIMARY KEY (`id`),
  UNIQUE KEY `link_code` (`link_code`),
  KEY `page_id` (`page_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- messenger_bot_engagement_2way_chat_plugin
CREATE TABLE IF NOT EXISTS `messenger_bot_engagement_2way_chat_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_code` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_auto_id` int(11) NOT NULL,
  `facebook_rx_fb_user_info_id` int(11) NOT NULL,
  `domain_name` varchar(255) NOT NULL,
  `language` varchar(200) NOT NULL DEFAULT 'en_US',
  `minimized` enum('hide','show','fade') NOT NULL DEFAULT 'show',
  `logged_in` tinytext NOT NULL,
  `logged_out` tinytext NOT NULL,
  `color` varchar(100) NOT NULL,
  `label_ids` varchar(250) NOT NULL COMMENT 'comma seperated,messenger_bot_broadcast_contact_group.id',
  `reference` varchar(250) NOT NULL,
  `template_id` int(11) NOT NULL COMMENT 'messenger_bot_postback.id',
  `delay` int(11) NOT NULL DEFAULT 5,
  `donot_show_if_not_login` enum('0','1') NOT NULL DEFAULT '0',
  `add_date` datetime NOT NULL,
  `visual_flow_campaign_id` int(11) NOT NULL,
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain_code` (`domain_code`),
  KEY `user_id` (`user_id`,`page_auto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- messenger_bot_engagement_messenger_codes
CREATE TABLE IF NOT EXISTS `messenger_bot_engagement_messenger_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qr_code_id` int(11) NOT NULL,
  `visual_flow_campaign_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL COMMENT 'messenger_bot_postback.id',
  `page_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `scan_limit` varchar(255) NOT NULL DEFAULT 'unlimited',
  `label_ids` varchar(255) NOT NULL COMMENT 'comma seperated, messenger_bot_broadcast_contact_group.id',
  `reference` varchar(255) NOT NULL,
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
  `created_at` datetime NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- user_input_flow_campaign
CREATE TABLE IF NOT EXISTS `user_input_flow_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flow_name` varchar(250) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `postback_id` varchar(255) NOT NULL,
  `media_type` enum('fb', 'ig') NOT NULL DEFAULT 'fb',
  `unique_id` varchar(255) NOT NULL,
  `visual_flow_type` enum('flow', 'general') NOT NULL DEFAULT 'general',
  `visual_flow_campaign_id` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- user_input_flow_questions
CREATE TABLE IF NOT EXISTS `user_input_flow_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `flow_campaign_id` int(11) NOT NULL,
  `serial_no` int(11) NOT NULL,
  `unique_id` varchar(250) NOT NULL,
  `question` text NOT NULL,
  `reply_type` varchar(20) NOT NULL,
  `options` longtext NOT NULL,
  `sequence_id` int(11) NOT NULL,
  `fb_label_ids` varchar(255) NOT NULL,
  `email_phone_label_ids` varchar(255) NOT NULL,
  `skip_button_text` varchar(255) NOT NULL,
  `type` enum('custom','system') NOT NULL DEFAULT 'custom',
  `media_type` enum('fb', 'ig') NOT NULL DEFAULT 'fb',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- user_input_flow_questions_answer
CREATE TABLE IF NOT EXISTS `user_input_flow_questions_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flow_campaign_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` varchar(50) NOT NULL,
  `subscribe_id` varchar(50) NOT NULL,
  `answer` text NOT NULL,
  `entry_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;