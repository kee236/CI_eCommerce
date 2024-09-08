--
-- Table structure for table `visual_flow_builder_campaign`
--

CREATE TABLE `visual_flow_builder_campaign` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `unique_id` varchar(50) NOT NULL,
  `reference_name` text NOT NULL,
  `media_type` enum('fb','ig') NOT NULL DEFAULT 'fb',
  `json_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visual_flow_campaign_unique_ids`
--

CREATE TABLE `visual_flow_campaign_unique_ids` (
  `id` int(11) NOT NULL,
  `page_table_id` int(11) NOT NULL,
  `visual_flow_campaign_id` int(11) NOT NULL,
  `element_unique_id` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;