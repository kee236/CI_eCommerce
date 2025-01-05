CREATE TABLE `google_business_locations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_account_id` int(11) DEFAULT NULL,
  `location_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `location_display_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `google_services`
--

CREATE TABLE `google_services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `project_id` varchar(255) NOT NULL,
  `credentials_json` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `google_services`
--

INSERT INTO `google_services` (`id`, `service_name`, `project_id`, `credentials_json`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Dialogflow', 'my-dialogflow-project', '{ ... json data ... }', 'inactive', '2024-08-31 07:35:09', '2024-08-31 07:35:09');

-- --------------------------------------------------------

--
-- Table structure for table `google_user_account`
--

CREATE TABLE `google_user_account` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `google_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `access_token` text NOT NULL,
  `refresh_token` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
