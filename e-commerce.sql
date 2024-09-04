-- ecommerce_engagement_product
CREATE TABLE IF NOT EXISTS `ecommerce_engagement_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL COMMENT 'ecommerce_product.id',
  `visual_flow_campaign_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL COMMENT 'messenger_bot_postback.id',
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
  `created_at` datetime NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ecommerce_engagement_coupon
CREATE TABLE IF NOT EXISTS `ecommerce_engagement_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL COMMENT 'ecommerce_coupon.id',
  `visual_flow_campaign_id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL COMMENT 'messenger_bot_postback.id',
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
  `created_at` datetime NOT NULL,
  `expired_at` datetime NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;