-- ตาราง messenger_bot_thirdparty_webhook
CREATE TABLE IF NOT EXISTS `messenger_bot_thirdparty_webhook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL, 
  `page_id` varchar(200) NOT NULL,  
  `name` varchar(255) NOT NULL, -- ชื่อของ webhook connection
  `webhook_url` text NOT NULL, -- URL ของ webhook ปลายทาง
  `platform` enum('facebook', 'line', 'line_notify', 'line_group') NOT NULL, -- แพลตฟอร์มที่เชื่อมต่อ
  `ai_model` varchar(255) DEFAULT NULL, -- โมเดล AI ที่ใช้ (ถ้ามี)
  `trigger_events` text NOT NULL, -- เหตุการณ์ที่ trigger webhook (JSON)
  `sending_data` text NOT NULL, -- ข้อมูลที่จะส่งไปยัง webhook (JSON)
  `created_at` datetime NOT NULL, 
  `last_triggered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

-- messenger_bot_thirdparty_webhook_activity
CREATE TABLE IF NOT EXISTS `messenger_bot_thirdparty_webhook_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webhook_id` int(11) NOT NULL, 
  `trigger_event` varchar(255) NOT NULL, -- เหตุการณ์ที่ trigger
  `request_data` text, -- ข้อมูลที่ส่งไปยัง webhook
  `response_data` text, -- ข้อมูลที่ได้รับกลับมาจาก webhook
  `status_code` int(11) DEFAULT NULL, -- HTTP status code ของการตอบกลับ
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`webhook_id`) REFERENCES `messenger_bot_thirdparty_webhook` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;