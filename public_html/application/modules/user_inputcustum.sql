CREATE TABLE `user_input_custom_fields` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reply_type` varchar(20) NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `create_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_input_custom_fields`
--

INSERT INTO `user_input_custom_fields` (`id`, `user_id`, `name`, `reply_type`, `media_type`, `create_time`) VALUES
(1, 6, 'Phone_user', 'Phone', 'fb', '2024-09-16 23:04:09');

-- --------------------------------------------------------

--
-- Table structure for table `user_input_custom_fields_assaign`
--

CREATE TABLE `user_input_custom_fields_assaign` (
  `id` int(11) NOT NULL,
  `subscriber_id` varchar(50) NOT NULL,
  `page_id` varchar(50) NOT NULL,
  `custom_field_id` int(11) NOT NULL,
  `custom_field_value` text NOT NULL,
  `assaign_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_input_flow_campaign`
--

CREATE TABLE `user_input_flow_campaign` (
  `id` int(11) NOT NULL,
  `flow_name` varchar(250) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `postback_id` varchar(255) NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `unique_id` varchar(255) NOT NULL,
  `visual_flow_type` enum('flow','general') NOT NULL DEFAULT 'general',
  `visual_flow_campaign_id` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_input_flow_questions`
--

CREATE TABLE `user_input_flow_questions` (
  `id` int(11) NOT NULL,
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
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_input_flow_questions_answer`
--

CREATE TABLE `user_input_flow_questions_answer` (
  `id` int(11) NOT NULL,
  `flow_campaign_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` varchar(50) NOT NULL,
  `subscribe_id` varchar(50) NOT NULL,
  `answer` text NOT NULL,
  `entry_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

