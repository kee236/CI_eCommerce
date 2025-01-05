CREATE TABLE `messenger_bot_drip_campaign` (
  `id` int(11) NOT NULL,
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
  `visual_flow_sequence_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_bot_drip_campaign_assign`
--

CREATE TABLE `messenger_bot_drip_campaign_assign` (
  `id` int(11) NOT NULL,
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
  `unsubscribed_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_bot_drip_report`
--

CREATE TABLE `messenger_bot_drip_report` (
  `id` int(11) NOT NULL,
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
  `last_updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;








CREATE TABLE `messenger_bot_engagement_2way_chat_plugin` (
  `id` int(11) NOT NULL,
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
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_bot_engagement_checkbox`
--

CREATE TABLE `messenger_bot_engagement_checkbox` (
  `id` int(11) NOT NULL,
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
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_bot_engagement_checkbox_reply`
--

CREATE TABLE `messenger_bot_engagement_checkbox_reply` (
  `id` int(11) NOT NULL,
  `user_ref` varchar(20) NOT NULL,
  `checkbox_plugin_id` int(11) NOT NULL,
  `reference` varchar(200) NOT NULL,
  `optin_time` datetime NOT NULL,
  `for_woocommerce` enum('0','1') NOT NULL DEFAULT '0',
  `wc_session_unique_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_bot_engagement_messenger_codes`
--

CREATE TABLE `messenger_bot_engagement_messenger_codes` (
  `id` int(11) NOT NULL,
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
  `status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_bot_engagement_mme`
--

CREATE TABLE `messenger_bot_engagement_mme` (
  `id` int(11) NOT NULL,
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
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_bot_engagement_send_to_msg`
--

CREATE TABLE `messenger_bot_engagement_send_to_msg` (
  `id` int(11) NOT NULL,
  `domain_code` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL COMMENT 'auto id',
  `domain_name` varchar(255) NOT NULL,
  `btn_size` enum('small','medium','large','xlarge') NOT NULL DEFAULT 'medium',
  `skin` enum('light','dark') NOT NULL DEFAULT 'light' COMMENT 'light=black, dark=white',
  `button_click_success_message` tinytext NOT NULL,
  `label_ids` varchar(250) NOT NULL COMMENT 'comma seperated,messenger_bot_broadcast_contact_group.id',
  `reference` varchar(250) NOT NULL,
  `template_id` int(11) NOT NULL COMMENT 'messenger_bot_postback.id',
  `cta_text_option` varchar(25) NOT NULL DEFAULT 'SUBSCRIBE_NOW',
  `redirect` enum('0','1') NOT NULL DEFAULT '0',
  `language` varchar(200) NOT NULL DEFAULT 'en_US',
  `add_button_with_message` enum('0','1') NOT NULL DEFAULT '0',
  `button_with_message_content` tinytext NOT NULL COMMENT 'json',
  `success_redirect_url` tinytext NOT NULL,
  `created_at` datetime NOT NULL,
  `visual_flow_campaign_id` int(11) NOT NULL,
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messenger_bot_message_sent_stat`
--

CREATE TABLE `messenger_bot_message_sent_stat` (
  `id` int(11) NOT NULL,
  `subscriber_id` varchar(30) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `message_unique_id` varchar(100) NOT NULL,
  `message_type` enum('message','postback') NOT NULL DEFAULT 'message',
  `no_sent_click` int(12) NOT NULL,
  `error_count` int(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
