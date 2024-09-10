

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
  `messenger_bot_table_id` int(11) NOT NULL,
  `bot_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_template` enum('0','1') COLLATE utf

`template_jsoncode` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_for` enum('reply_message','unsubscribe','resubscribe','email-quick-reply','phone-quick-reply','location-quick-reply','birthday-quick-reply','chat-with-human','chat-with-bot') COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_id` int(11) NOT NULL,
  `inherit_from_template` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL,
  `broadcaster_labels` tinytext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'comma separated',
  `drip_campaign_id` int(11) NOT NULL,
  `visual_flow_type` enum('flow','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `postback_type` enum('main','sub') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sub',
  `media_type` enum('fb','ig') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fb',
  `visual_flow_campaign_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`postback_id`,`page_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ecommerce_config
CREATE TABLE IF NOT EXISTS `ecommerce_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `paypal_email` varchar(100) NOT NULL,
  `paypal_mode` enum('live','sandbox') NOT NULL DEFAULT 'live',
  `stripe_billing_address` enum('0','1') NOT NULL DEFAULT '0',
  `stripe_secret_key` text NOT NULL,
  `stripe_publishable_key` text NOT NULL,
  `paystack_secret_key` text NOT NULL,
  `paystack_public_key` text NOT NULL,
  `razorpay_key_id` text NOT NULL,
  `razorpay_key_secret` text NOT NULL,
  `mollie_api_key` text NOT NULL,
  `mercadopago_public_key` text NOT NULL,
  `mercadopago_access_token` text NOT NULL,
  `marcadopago_country` varchar(5) NOT NULL,
  `sslcommerz_store_id` text NOT NULL,
  `sslcommerz_store_password` text NOT NULL,
  `sslcommerz_mode` enum('sandbox','live') NOT NULL DEFAULT 'live',
  `senangpay_merchent_id` text NOT NULL,
  `senangpay_secret_key` text NOT NULL,
  `senangpay_mode` enum('sandbox','live') NOT NULL DEFAULT 'live',
  `instamojo_api_key` text NOT NULL,
  `instamojo_auth_token` text NOT NULL,
  `instamojo_mode` enum('sandbox','live') NOT NULL DEFAULT 'live',
  `xendit_secret_api_key` text NOT NULL,
  `manual_payment` enum('0','1') NOT NULL DEFAULT '0',
  `manual_payment_instruction` text NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `currency_position` enum('left','right') NOT NULL DEFAULT 'left',
  `decimal_point` tinyint(4) NOT NULL DEFAULT '2',
  `thousand_comma` enum('0','1') NOT NULL DEFAULT '0',
  `store_pickup_title` varchar(100) NOT NULL DEFAULT 'Store Pickup',
  `buy_button_title` varchar(100) NOT NULL DEFAULT 'Buy Now',
  `is_store_pickup` enum('0','1') NOT NULL DEFAULT '0',
  `is_home_delivery` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_country` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_state` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_city` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_zip` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_email` enum('0','1') NOT NULL DEFAULT '1',
  `is_checkout_phone` enum('0','1') NOT NULL DEFAULT '1',
  `is_delivery_note` enum('0','1') NOT NULL DEFAULT '1',
  `is_preparation_time` enum('0','1') NOT NULL DEFAULT '0',
  `preparation_time` varchar(20) NOT NULL,
  `preparation_time_unit` enum('minutes','hours','days') NOT NULL,
  `is_order_schedule` enum('0','1') NOT NULL DEFAULT '0',
  `order_schedule` enum('today','tomorrow','week','any') NOT NULL DEFAULT 'any',
  `font` text NOT NULL,
  `is_category_wise_product_view` enum('0','1') NOT NULL DEFAULT '0',
  `product_sort` enum('name','new','price','sale','random') NOT NULL DEFAULT 'name',
  `product_sort_order` enum('asc','desc') NOT NULL DEFAULT 'asc',
  `product_listing` enum('list','grid') NOT NULL DEFAULT 'list',
  `theme_color` varchar(10) NOT NULL DEFAULT '#ff8342',
  `hide_add_to_cart` enum('0','1') NOT NULL DEFAULT '0',
  `hide_buy_now` enum('0','1') NOT NULL DEFAULT '0',
  `whatsapp_send_order_button` enum('0','1') NOT NULL DEFAULT '0',
  `whatsapp_phone_number` varchar(30) NOT NULL,
  `whatsapp_send_order_text` text NOT NULL,
  `is_guest_login` enum('0','1') NOT NULL DEFAULT '0',
  `updated_at` datetime NOT NULL,
  `paymaya_public_key` text NOT NULL,
  `myfatoorah_api_key` text NOT NULL,
  `myfatoorah_mode` varchar(30) NOT NULL,
  `toyyibpay_secret_key` text NOT NULL,
  `paymaya_secret_key` varchar(100) NOT NULL,
  `paymaya_mode` varchar(30) NOT NULL,
  `toyyibpay_category_code` varchar(100) NOT NULL,
  `toyyibpay_mode` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ecommerce_product
CREATE TABLE IF NOT EXISTS `ecommerce_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` text NOT NULL,
  `product_video_id` varchar(100) NOT NULL,
  `original_price` float NOT NULL,
  `sell_price` float NOT NULL,
  `taxable` enum('0','1') NOT NULL DEFAULT '0',
  `stock_item` int(11) NOT NULL,
  `stock_display` enum('0','1') NOT NULL DEFAULT '0',
  `stock_prevent_purchase` enum('0','1') NOT NULL DEFAULT '0',
  `attribute_ids` varchar(255) NOT NULL,
  `preparation_time` varchar(20) NOT NULL,
  `preparation_time_unit` enum('minutes','hours','days') NOT NULL,
  `purchase_note` text NOT NULL,
  `thumbnail` text NOT NULL,
  `featured_images` text NOT NULL,
  `digital_product_file` text NOT NULL,
  `category_id` int(11) NOT NULL,
  `sales_count` int(11) NOT NULL,
  `visit_count` int(11) NOT NULL,
  `updated_at` datetime NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  `woocommerce_product_id` int(11) DEFAULT NULL,
  `woocommerce_price_html` text NOT NULL,
  `related_product_ids` varchar(255) NOT NULL,
  `upsell_product_id` int(11) NOT NULL,
  `downsell_product_id` int(11) NOT NULL,
  `is_featured` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `woocommerce_product_id` (`store_id`,`woocommerce_product_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- messenger_bot_drip_campaign
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


-- messenger_bot_drip_campaign_assign
CREATE TABLE IF NOT EXISTS `messenger_bot_drip_campaign_assign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `subscribe_id` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL COMMENT 'auto_id',
  `drip_campaign_id` int(11) NOT NULL,
  `drip_type` enum('default','messenger_bot_engagement_checkbox','messenger_bot_engagement_send_to_msg','messenger_bot_engagement_mme','messenger_bot_engagement_messenger_codes','messenger_bot_engagement_2way_chat_plugin','custom') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `campaign_type` enum('messenger','email','sms') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'messenger',
  `engagement_table_id` int(11) NOT NULL,
  `is_sent` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_seen` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_opened` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_clicked` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `sent_at` datetime NOT NULL,
  `seen_at` datetime NOT NULL,
  `opened_at` datetime NOT NULL,
  `clicked_at` datetime NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `is_unsubscribed` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `unsubscribed_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `drip_campaign_id` (`drip_campaign_id`),
  KEY `user_id` (`user_id`,`subscribe_id`,`page_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- messenger_bot_drip_report
CREATE TABLE IF NOT EXISTS `messenger_bot_drip_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subscribe_id` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL COMMENT 'auto_id',
  `report_type` enum('sent','delivered','opened','clicked','unsubscribe') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `is_sent` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_delivered` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_opened` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_clicked` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `is_unsubscribed` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `sent_at` datetime NOT NULL,
  `delivered_at` datetime NOT NULL,
  `opened_at` datetime NOT NULL,
  `clicked_at` datetime NOT NULL,
  `unsubscribed_at` datetime NOT NULL,
  `last_updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`,`user_id`,`page_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;