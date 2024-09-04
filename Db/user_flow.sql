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