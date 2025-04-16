
--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11)  UNSIGNED NOT NULL AUTO_INCREMENT,
  `content_id` int(10) UNSIGNED NOT NULL,
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL,
  `upload_date` int(10) UNSIGNED NOT NULL,
  `filename` varchar(100) NOT NULL,
  `file_size` int(10) UNSIGNED NOT NULL,
  `file_hash` varchar(50) NOT NULL,
  `attach_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `content_type` varchar(10) NOT NULL DEFAULT 'post',
  `topicid` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

--
-- Table structure for table `bans`
--

CREATE TABLE `bans` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `added` datetime DEFAULT NULL,
  `addedby` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `comment` varchar(200) NOT NULL DEFAULT '',
  `first` varchar(39) DEFAULT NULL,
  `last` varchar(39) DEFAULT NULL,
  `type` varchar(39) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `first_last` (`first`,`last`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `bonus`
--

CREATE TABLE `bonus` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `cost` decimal(10,1) UNSIGNED NOT NULL DEFAULT 0.0,
  `value` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `descr` varchar(100) NOT NULL,
  `type` enum('traffic','invite','VIP','other','HnR') NOT NULL DEFAULT 'other',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


INSERT INTO `bonus` (`id`, `title`, `cost`, `value`, `descr`, `type`) VALUES
(1, '1GB Uploaded', '70.0', 1073741824, 'Exchange 70 points for 1 GB upload.', 'traffic'),
(2, '2 GB Uploaded', '100.0', 2147483648, 'Exchange 100 points for  GB upload.', 'traffic'),
(3, '5 GB Uploaded', '250.0', 5368709120, 'Exchange 250 points for 5 GB upload.', 'traffic'),
(4, '10 GB Uploaded', '500.0', 10737418240, 'Exchange 500 points for 10 GB upload.', 'traffic'),
(5, 'Invitations', '1000.0', 2, 'Exchange 1000 points to receive 2 Invites.', 'invite');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `sort_index` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `image` varchar(200) NOT NULL DEFAULT '',
  `subcat` char(3) NOT NULL DEFAULT 'no',
  `parent_cat` varchar(15) NOT NULL DEFAULT '',
  `sub_sort` char(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `parent_cat-name` (`parent_cat`,`name`)
) ENGINE=MyISAM;

INSERT INTO `categories` (`id`, `name`, `sort_index`, `image`, `subcat`, `parent_cat`, `sub_sort`) VALUES 
(1, 'Film', 1, 'fa-film', 'no', 'Film', ''),
(2, 'TV', 2, 'fa-television', 'no', 'TV', ''),
(3, 'Games', 3, 'fa-gamepad', 'no', 'Games', ''),
(4, 'Music', 4, 'fa-music', 'no', 'Music', ''),
(5, 'App', 5, 'fa-windows', 'no', 'App', ''),
(6, 'All', 6, 'fa-venus-mars', 'no', 'XXX', ''),
(7, 'All', 7, 'fa-book', 'no', 'Other', '');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `agent_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `agent_name` varchar(200) NOT NULL DEFAULT '',
  `hits` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ins_date` datetime DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `peer_id` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`agent_id`),
  UNIQUE KEY `agent_name` (`agent_name`) USING HASH
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `added` datetime DEFAULT NULL,
  `text` text DEFAULT NULL,
  `news` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `req` int(11) DEFAULT NULL,
  `ori_text` text DEFAULT NULL,
  `editedby` text DEFAULT NULL,
  `editedat` datetime DEFAULT NULL,
  `type` VARCHAR(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `torrent` (`torrent`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `flagpic` varchar(50) DEFAULT NULL,
  `domain` char(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;


INSERT INTO `countries` (`id`, `name`, `flagpic`, `domain`) VALUES
(1, 'Sweden', 'sweden.webp', 'SE'),
(2, 'United States of America', 'usa.webp', 'US'),
(3, 'Russia', 'russia.webp', 'RU'),
(4, 'Finland', 'finland.webp', 'FI'),
(5, 'Canada', 'canada.webp', 'CA'),
(6, 'France', 'france.webp', 'FR'),
(7, 'Germany', 'germany.webp', 'DE'),
(8, 'China', 'china.webp', 'CN'),
(9, 'Italy', 'italy.webp', 'IT'),
(10, 'Denmark', 'denmark.webp', 'DK'),
(11, 'Norway', 'norway.webp', 'NO'),
(12, 'United Kingdom', 'england.webp', 'UK'),
(13, 'Ireland', 'ireland.webp', 'IE'),
(14, 'Poland', 'poland.webp', 'PL'),
(15, 'Netherlands', 'netherlands.webp', 'NL'),
(16, 'Belgium', 'belgium.webp', 'BE'),
(17, 'Japan', 'japan.webp', 'JP'),
(18, 'Brazil', 'brazil.webp', 'BR'),
(19, 'Argentina', 'argentina.webp', 'AR'),
(20, 'Australia', 'australia.webp', 'AU'),
(21, 'New Zealand', 'newzealand.webp', 'NZ'),
(23, 'Spain', 'spain.webp', 'ES'),
(24, 'Portugal', 'portugal.webp', 'PT'),
(25, 'Mexico', 'mexico.webp', 'MX'),
(26, 'Singapore', 'singapore.webp', 'SG'),
(29, 'South Africa', 'southafrica.webp', 'ZA'),
(30, 'South Korea', 'southkorea.webp', 'KR'),
(31, 'Jamaica', 'jamaica.webp', 'JM'),
(32, 'Luxembourg', 'luxembourg.webp', 'LU'),
(33, 'Hong Kong', 'hongkong.webp', 'HK'),
(34, 'Belize', 'belize.webp', 'BZ'),
(35, 'Algeria', 'algeria.webp', 'DZ'),
(36, 'Angola', 'angola.webp', 'AO'),
(37, 'Austria', 'austria.webp', 'AT'),
(38, 'Yugoslavia', 'yugoslavia.webp', 'YU'),
(39, 'Western Samoa', 'westernsamoa.webp', 'WS'),
(40, 'Malaysia', 'malaysia.webp', 'MY'),
(41, 'Dominican Republic', 'dominicanrep.webp', 'DO'),
(42, 'Greece', 'greece.webp', 'GR'),
(43, 'Guatemala', 'guatemala.webp', 'GT'),
(44, 'Israel', 'israel.webp', 'IL'),
(45, 'Pakistan', 'pakistan.webp', 'PK'),
(46, 'Czech Republic', 'czechrep.webp', 'CZ'),
(47, 'Serbia', 'serbia.webp', 'YU'),
(48, 'Seychelles', 'seychelles.webp', 'SC'),
(49, 'Taiwan', 'taiwan.webp', 'TW'),
(50, 'Puerto Rico', 'puertorico.webp', 'PR'),
(51, 'Chile', 'chile.webp', 'CL'),
(52, 'Cuba', 'cuba.webp', 'CU'),
(53, 'Congo', 'congo.webp', 'CG'),
(54, 'Afghanistan', 'afghanistan.webp', 'AF'),
(55, 'Turkey', 'turkey.webp', 'TR'),
(56, 'Uzbekistan', 'uzbekistan.webp', 'UZ'),
(57, 'Switzerland', 'switzerland.webp', 'CH'),
(58, 'Kiribati', 'kiribati.webp', 'KI'),
(59, 'Philippines', 'philippines.webp', 'PH'),
(60, 'Burkina Faso', 'burkinafaso.webp', 'BF'),
(61, 'Nigeria', 'nigeria.webp', 'NG'),
(62, 'Iceland', 'iceland.webp', 'IS'),
(63, 'Nauru', 'nauru.webp', 'NR'),
(64, 'Slovenia', 'slovenia.webp', 'SI'),
(65, 'Albania', 'albania.webp', 'AL'),
(66, 'Turkmenistan', 'turkmenistan.webp', 'TM'),
(67, 'Bosnia Herzegovina', 'bosniaherzegovina.webp', 'BA'),
(68, 'Andorra', 'andorra.webp', 'AD'),
(69, 'Lithuania', 'lithuania.webp', 'LT'),
(70, 'India', 'india.webp', 'IN'),
(71, 'Netherlands Antilles', 'nethantilles.webp', 'AN'),
(72, 'Ukraine', 'ukraine.webp', 'UA'),
(73, 'Venezuela', 'venezuela.webp', 'VE'),
(74, 'Hungary', 'hungary.webp', 'HU'),
(75, 'Romania', 'romania.webp', 'RO'),
(76, 'Vanuatu', 'vanuatu.webp', 'VU'),
(77, 'Vietnam', 'vietnam.webp', 'VN'),
(78, 'Trinidad &amp; Tobago', 'trinidadandtobago.webp', 'TT'),
(79, 'Honduras', 'honduras.webp', 'HN'),
(80, 'Kyrgyzstan', 'kyrgyzstan.webp', 'KG'),
(81, 'Ecuador', 'ecuador.webp', 'EC'),
(82, 'Bahamas', 'bahamas.webp', 'BS'),
(83, 'Peru', 'peru.webp', 'PE'),
(84, 'Cambodia', 'cambodia.webp', 'KH'),
(85, 'Barbados', 'barbados.webp', 'BB'),
(86, 'Bangladesh', 'bangladesh.webp', 'BD'),
(87, 'Laos', 'laos.webp', 'LA'),
(88, 'Uruguay', 'uruguay.webp', 'UY'),
(89, 'Antigua Barbuda', 'antiguabarbuda.webp', 'AG'),
(90, 'Paraguay', 'paraguay.webp', 'PY'),
(92, 'Union of Soviet Socialist Republics', 'ussr.webp', 'SU'),
(93, 'Thailand', 'thailand.webp', 'TH'),
(94, 'Senegal', 'senegal.webp', 'SN'),
(95, 'Togo', 'togo.webp', 'TG'),
(96, 'North Korea', 'northkorea.webp', 'KP'),
(97, 'Croatia', 'croatia.webp', 'HR'),
(98, 'Estonia', 'estonia.webp', 'EE'),
(99, 'Colombia', 'colombia.webp', 'CO'),
(100, 'England', 'england.webp', 'GB'),
(101, 'Egypt', 'egypt.webp', 'EG'),
(102, 'Bulgaria', 'bulgaria.webp', 'BG');

-- --------------------------------------------------------

--
-- Table structure for table `forumcats`
--

CREATE TABLE `forumcats` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `forum_forums`
--

CREATE TABLE `forum_forums` (
  `sort` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `description` varchar(200) DEFAULT NULL,
  `minclassread` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `minclasswrite` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `guest_read` enum('yes','no') DEFAULT 'yes',
  `category` tinyint(2) NOT NULL DEFAULT 0,
  `sub` int(250) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `topicid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `added` datetime DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `editedby` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `editedat` datetime DEFAULT NULL,
  `attachments` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `topicid` (`topicid`),
  KEY `userid` (`userid`),
  FULLTEXT KEY `body` (`body`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `forum_readposts`
--

CREATE TABLE `forum_readposts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `topicid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `lastpostread` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `userid` (`id`),
  KEY `topicid` (`topicid`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `forum_topics`
--

CREATE TABLE `forum_topics` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `subject` varchar(90) DEFAULT NULL,
  `locked` enum('yes','no') NOT NULL DEFAULT 'no',
  `forumid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `lastpost` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `moved` enum('yes','no') NOT NULL DEFAULT 'no',
  `sticky` enum('yes','no') NOT NULL DEFAULT 'no',
  `views` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `subject` (`subject`),
  KEY `lastpost` (`lastpost`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
 `group_id` int(10) NOT NULL AUTO_INCREMENT,
 `level` varchar(50) NOT NULL DEFAULT '',
 `Color` varchar(7) NOT NULL DEFAULT '#856A00',
 `control_panel` enum('yes','no') NOT NULL DEFAULT 'no',
 `staff_page` enum('yes','no') NOT NULL DEFAULT 'no',
 `staff_public` enum('yes','no') NOT NULL DEFAULT 'no',
 `staff_sort` tinyint UNSIGNED NOT NULL DEFAULT '0',
 `maxslots` int NOT NULL DEFAULT '0',
 `group_role_id` int NOT NULL DEFAULT '1',
 UNIQUE KEY `base` (`group_id`)
) ENGINE=MyISAM;

INSERT INTO `groups` (`group_id`, `level`, `Color`, `control_panel`, `staff_page`, `staff_public`, `staff_sort`, `maxslots`, `group_role_id`) VALUES
(1, 'Member', 'd1c815', 'no', 'no', 'no', 8, 2, 1),
(2, 'Power User', '0000ff', 'no', 'no', 'no', 7, 4, 1),
(3, 'VIP', 'FFff00', 'no', 'no', 'no', 5, 6, 1),
(4, 'Uploader', '00ABA6', 'no', 'no', 'no', 4, 8, 1),
(5, 'Moderator', '007F00', 'no', 'yes', 'yes', 3, 10, 2),
(6, 'Super Moderator', '00C000', 'yes', 'yes', 'yes', 2, 12, 2),
(7, 'Administrator', '#FF0000', 'yes', 'yes', 'yes', 1, 14, 3);
-- --------------------------------------------------------

--
-- Table structure for table `guests`
--

CREATE TABLE `guests` (
  `ip` varchar(39) NOT NULL DEFAULT '',
  `time` decimal(20,0) UNSIGNED NOT NULL DEFAULT 0,
  UNIQUE KEY `IP` (`ip`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uri` varchar(200) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

INSERT INTO `languages` (`id`, `uri`, `name`) VALUES 
(1, 'english', 'english');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `liked` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `added` datetime DEFAULT NULL,
  `reaction` text NOT NULL,
  `type` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `liked` (`liked`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `added` datetime DEFAULT NULL,
  `txt` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `receiver` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `added` datetime DEFAULT NULL,
  `msg` text DEFAULT NULL,
  `unread` enum('yes','no') NOT NULL DEFAULT 'yes',
  `poster` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `subject` text NOT NULL DEFAULT '',
  `location` enum('in','out','both','draft','template') NOT NULL DEFAULT 'in',
  `history` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `receiver` (`receiver`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL DEFAULT 0,
  `added` datetime DEFAULT NULL,
  `title` varchar(200) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `added` (`added`)
) ENGINE=MyISAM;

INSERT INTO `news` (`id`, `userid`, `added`, `title`, `body`) VALUES 
(1, 1, '2023-03-08 09:29:22', 'Welcome', 'Welcome To M-BiTsy');

-- --------------------------------------------------------

--
-- Table structure for table `peers`
--

CREATE TABLE `peers` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `peer_id` varchar(40) NOT NULL DEFAULT '',
  `ip` varchar(64) NOT NULL DEFAULT '',
  `port` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `to_go` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `seeder` enum('yes','no') NOT NULL DEFAULT 'no',
  `started` datetime DEFAULT NULL,
  `last_action` datetime DEFAULT NULL,
  `connectable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `client` varchar(60) NOT NULL DEFAULT '',
  `userid` varchar(32) NOT NULL DEFAULT '',
  `passkey` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `torrent_peer_id` (`torrent`,`peer_id`),
  KEY `torrent` (`torrent`),
  KEY `torrent_seeder` (`torrent`,`seeder`),
  KEY `last_action` (`last_action`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(10) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(40) NOT NULL DEFAULT 'no name',
  `view_torrents` enum('yes','no') NOT NULL DEFAULT 'yes',
  `edit_torrents` enum('yes','no') NOT NULL DEFAULT 'no',
  `delete_torrents` enum('yes','no') NOT NULL DEFAULT 'no',
  `view_users` enum('yes','no') NOT NULL DEFAULT 'yes',
  `edit_users` enum('yes','no') NOT NULL DEFAULT 'no',
  `delete_users` enum('yes','no') NOT NULL DEFAULT 'no',
  `view_news` enum('yes','no') NOT NULL DEFAULT 'yes',
  `edit_news` enum('yes','no') NOT NULL DEFAULT 'no',
  `delete_news` enum('yes','no') NOT NULL DEFAULT 'no',
  `can_upload` enum('yes','no') NOT NULL DEFAULT 'no',
  `can_download` enum('yes','no') NOT NULL DEFAULT 'yes',
  `view_forum` enum('yes','no') NOT NULL DEFAULT 'yes',
  `edit_forum` enum('yes','no') NOT NULL DEFAULT 'yes',
  `delete_forum` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`permission_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

INSERT INTO `permissions` (`permission_id`, `permission_name`, `view_torrents`, `edit_torrents`, `delete_torrents`, `view_users`, `edit_users`, `delete_users`, `view_news`, `edit_news`, `delete_news`, `can_upload`, `can_download`, `view_forum`, `edit_forum`, `delete_forum`) VALUES
(1, 'Default Access', 'yes', 'no', 'no', 'yes', 'no', 'no', 'yes', 'no', 'no', 'yes', 'yes', 'yes', 'no', 'no'),
(2, 'Mod Access', 'yes', 'yes', 'no', 'yes', 'yes', 'no', 'yes', 'yes', 'no', 'yes', 'yes', 'yes', 'yes', 'no'),
(3, 'Full Access', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `pollanswers`
--

CREATE TABLE `pollanswers` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pollid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `userid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `selection` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `pollid` (`pollid`),
  KEY `selection` (`selection`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `added` datetime DEFAULT NULL,
  `question` varchar(200) NOT NULL DEFAULT '',
  `option0` varchar(40) NOT NULL DEFAULT '',
  `option1` varchar(40) NOT NULL DEFAULT '',
  `option2` varchar(40) NOT NULL DEFAULT '',
  `option3` varchar(40) NOT NULL DEFAULT '',
  `option4` varchar(40) NOT NULL DEFAULT '',
  `option5` varchar(40) NOT NULL DEFAULT '',
  `option6` varchar(40) NOT NULL DEFAULT '',
  `option7` varchar(40) NOT NULL DEFAULT '',
  `option8` varchar(40) NOT NULL DEFAULT '',
  `option9` varchar(40) NOT NULL DEFAULT '',
  `option10` varchar(40) NOT NULL DEFAULT '',
  `option11` varchar(40) NOT NULL DEFAULT '',
  `option12` varchar(40) NOT NULL DEFAULT '',
  `option13` varchar(40) NOT NULL DEFAULT '',
  `option14` varchar(40) NOT NULL DEFAULT '',
  `option15` varchar(40) NOT NULL DEFAULT '',
  `option16` varchar(40) NOT NULL DEFAULT '',
  `option17` varchar(40) NOT NULL DEFAULT '',
  `option18` varchar(40) NOT NULL DEFAULT '',
  `option19` varchar(40) NOT NULL DEFAULT '',
  `sort` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

INSERT INTO `polls` (`id`, `added`, `question`, `option0`, `option1`, `option2`, `option3`, `option4`, `option5`, `option6`, `option7`, `option8`, `option9`, `option10`, `option11`, `option12`, `option13`, `option14`, `option15`, `option16`, `option17`, `option18`, `option19`, `sort`) VALUES 
(1, NULL, 'Do You Like This Site', 'Excellent!', 'Its OK', 'Its my first visit', 'Don''t Like It', 'Im Off', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `torrent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `rating` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `added` datetime DEFAULT NULL,
  PRIMARY KEY (`torrent`,`user`),
  KEY `user` (`user`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `addedby` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `votedfor` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `votedfor_xtra` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` enum('torrent','user','forum','comment','other','req') NOT NULL DEFAULT 'torrent',
  `reason` varchar(200) NOT NULL DEFAULT '',
  `dealtby` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `dealtwith` tinyint(1) NOT NULL DEFAULT 0,
  `complete` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE `rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `public` enum('yes','no') NOT NULL DEFAULT 'yes',
  `class` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

INSERT INTO `rules` (`id`, `title`, `text`, `public`, `class`) VALUES 
(1, 'General rules - Breaking these rules can and will get you banned!', '- We are a English only site, so please only talk in english! \r\n\r\n- Keep your overall ratio at or above 0.5 at all times! \r\n\r\n- Do not defy the moderators expressed wishes! ', 'yes', 0),
(2, 'General Forum Guidelines', '- No aggressive behaviour or flaming in the forums. \r\n- No trashing of other peoples topics (i.e. SPAM). \r\n- No language other than English in the forums. \r\n- No links to warez or crack sites in the forums. \r\n- No serials, CD keys, passwords or cracks in the forums. \r\n- No requesting if the release is over 7 days old. \r\n- No bumping... (All bumped threads will be deleted.) \r\n- No double posting. If you wish to post again, and yours is the last post in the thread please use the EDIT function,instead of posting a double.\r\n- Please ensure all questions are posted in the correct section!', 'yes', 0),
(3, 'Moderating Rules', '- The most important rule!; Use your better judgement! \r\n- Don''t defy another mod in public, instead send a PM or make a post in the "Site admin". \r\n- Be tolerant! give the user(s) a chance to reform. \r\n- Don''t act prematurely, Let the users make their mistake and THEN correct them. \r\n- Try correcting any "off topics" rather then closing the thread. \r\n- Move topics rather than locking / deleting them. \r\n- Be tolerant when moderating the Chit-chat section. (give them some slack) \r\n- If you lock a topic, Give a brief explanation as to why you''re locking it. \r\n- Before banning a user, Send him/her a PM and If they reply, put them on a 2 week trial. \r\n- Don''t ban a user until he or she has been a member for at least 4 weeks. \r\n- Always state a reason (in the user comment box) as to why the user is being banned. \r\n', 'no', 5);

-- --------------------------------------------------------

--
-- Table structure for table `shoutbox`
--

CREATE TABLE `shoutbox` (
  `msgid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL DEFAULT '0',
  `message` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `userid` int(8) UNSIGNED NOT NULL DEFAULT 0,
  `staff` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`msgid`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `snatched`
--

CREATE TABLE `snatched` (
  `sid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tid` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `start_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `upload_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `last_time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `upload` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `download` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `completed` enum('no','yes') NOT NULL DEFAULT 'no',
  `hnr` enum('no','yes') NOT NULL DEFAULT 'no',
  `done` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`sid`),
  UNIQUE KEY `tid_uid` (`tid`,`uid`),
  KEY `completed` (`completed`),
  KEY `tid` (`tid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL DEFAULT '',
  `colour` varchar(20) NOT NULL DEFAULT '#000',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

INSERT INTO `tags` (`id`, `name`, `colour`) VALUES
(1, 'horror', '#000'),
(2, 'action', '#000'),
(3, 'adventure', '#000'),
(4, 'thriller', '#000'),
(5, 'comedy', '#000');

-- --------------------------------------------------------

--
-- Table structure for table `tagtorrent`
--

CREATE TABLE `tagtorrent` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `torrentid` int(10) DEFAULT NULL,
  `type1` varchar(40) DEFAULT NULL,
  `type2` varchar(40) DEFAULT NULL,
  `type3` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task` varchar(20) NOT NULL DEFAULT '',
  `last_time` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`task`)
) ENGINE=MyISAM;

INSERT INTO `tasks` (`task`, `last_time`) VALUES 
('cleanup', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tmdb`
--

CREATE TABLE `tmdb` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `id_tmdb` int(20) NOT NULL DEFAULT 0,
  `title` varchar(200) DEFAULT NULL,
  `genre` varchar(200) DEFAULT NULL,
  `plot` text DEFAULT NULL,
  `actor` longtext DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `image` varchar(150) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `trailer` varchar(150) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `producer` varchar(200) DEFAULT NULL,
  `season` int(10) DEFAULT NULL,
  `episodes` int(10) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `creator` varchar(200) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `torrentid` int(50) DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM;

--
-- Table structure for table `torrentlang`
--

CREATE TABLE `torrentlang` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `image` varchar(200) NOT NULL DEFAULT '',
  `sort_index` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

INSERT INTO `torrentlang` (`id`, `name`, `image`, `sort_index`) VALUES
(1, 'English', 'england.webp', 1),
(2, 'French', 'france.webp', 2),
(3, 'German', 'germany.webp', 3),
(4, 'Italian', 'italy.webp', 4),
(5, 'Japanese', 'japan.webp', 5),
(6, 'Spanish', 'spain.webp', 6),
(7, 'Russian', 'russia.webp', 7);

-- --------------------------------------------------------

--
-- Table structure for table `torrents`
--

CREATE TABLE `torrents` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `info_hash` varchar(40) DEFAULT NULL,
  `name` varchar(200) NOT NULL DEFAULT '',
  `filename` varchar(200) NOT NULL DEFAULT '',
  `save_as` varchar(200) NOT NULL DEFAULT '',
  `descr` text NOT NULL,
  `image1` text DEFAULT NULL,
  `image2` text DEFAULT NULL,
  `poster` mediumtext DEFAULT NULL,
  `category` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `size` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `added` datetime DEFAULT NULL,
  `type` enum('single','multi') NOT NULL DEFAULT 'single',
  `numfiles` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `comments` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `hits` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `times_completed` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `leechers` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `seeders` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_action` datetime DEFAULT NULL,
  `visible` enum('yes','no') NOT NULL DEFAULT 'yes',
  `banned` enum('yes','no') NOT NULL DEFAULT 'no',
  `owner` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `anon` enum('yes','no') NOT NULL DEFAULT 'no',
  `numratings` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ratingsum` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `nfo` enum('yes','no') NOT NULL DEFAULT 'no',
  `announce` varchar(200) NOT NULL DEFAULT '',
  `external` enum('yes','no') NOT NULL DEFAULT 'no',
  `torrentlang` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `freeleech` enum('0','1') NOT NULL DEFAULT '0',
  `tube` varchar(100) DEFAULT NULL,
  `tmdb` varchar(100) DEFAULT NULL,
  `imdb` varchar(100) DEFAULT NULL,
  `uplreq` enum('yes','no') DEFAULT 'no',
  `sticky` enum('yes','no') DEFAULT 'no',
  `vip` enum('yes','no') DEFAULT 'no',
  `announcelist` longtext DEFAULT NULL,
  `filelist` longtext DEFAULT NULL,
  `info_hash2` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `info_hash` (`info_hash`(20)),
  KEY `owner` (`owner`),
  KEY `visible` (`visible`),
  KEY `category_visible` (`category`,`visible`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL DEFAULT '',
  `password` varchar(200) NOT NULL DEFAULT '',
  `secret` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(80) NOT NULL DEFAULT '',
  `status` enum('pending','confirmed') NOT NULL DEFAULT 'pending',
  `added` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_access` datetime DEFAULT NULL,
  `editsecret` varchar(20) NOT NULL DEFAULT '',
  `privacy` enum('strong','normal','low') NOT NULL DEFAULT 'normal',
  `stylesheet` varchar(20) NOT NULL DEFAULT 'inc',
  `language` varchar(20) NOT NULL DEFAULT 'english',
  `info` text DEFAULT NULL,
  `acceptpms` enum('yes','no') NOT NULL DEFAULT 'yes',
  `ip` varchar(39) NOT NULL DEFAULT '',
  `class` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `role_id` int(10) NOT NULL DEFAULT 1,
  `avatar` varchar(100) NOT NULL DEFAULT '',
  `uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `title` varchar(30) NOT NULL DEFAULT '',
  `donated` int(5) UNSIGNED NOT NULL DEFAULT 0,
  `country` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `notifs` varchar(100) NOT NULL DEFAULT '',
  `enabled` varchar(10) NOT NULL DEFAULT 'yes',
  `modcomment` text DEFAULT NULL,
  `gender` varchar(6) NOT NULL DEFAULT '',
  `client` varchar(25) NOT NULL DEFAULT '',
  `age` int(3) NOT NULL DEFAULT 0,
  `warned` char(3) NOT NULL DEFAULT 'no',
  `signature` varchar(200) NOT NULL DEFAULT '',
  `last_browse` int(11) NOT NULL DEFAULT 0,
  `forumbanned` char(3) NOT NULL DEFAULT 'no',
  `invited_by` int(10) NOT NULL DEFAULT 0,
  `invitees` varchar(100) NOT NULL DEFAULT '',
  `invites` smallint(5) NOT NULL DEFAULT 0,
  `invitedate` datetime DEFAULT NULL,
  `commentpm` enum('yes','no') NOT NULL DEFAULT 'yes',
  `passkey` varchar(32) NOT NULL DEFAULT '',
  `page` text DEFAULT NULL,
  `team` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tzoffset` int(4) DEFAULT 0,
  `hideshoutbox` enum('yes','no') NOT NULL DEFAULT 'no',
  `token` varchar(200) NOT NULL DEFAULT '',
  `seedbonus` decimal(10,1) NOT NULL DEFAULT 0.0,
  `vipuntil` datetime DEFAULT NULL,
  `oldclass` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `shoutboxpos` char(3) NOT NULL DEFAULT 'no',
  `downloadbanned` char(3) NOT NULL DEFAULT 'no',
  `hitruns` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `message_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `status_added` (`status`,`added`),
  KEY `ip` (`ip`),
  KEY `uploaded` (`uploaded`),
  KEY `downloaded` (`downloaded`),
  KEY `country` (`country`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

--
-- Table structure for table `warnings`
--

CREATE TABLE `warnings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userid` int(10) NOT NULL DEFAULT 0,
  `reason` varchar(200) NOT NULL DEFAULT '',
  `added` datetime DEFAULT NULL,
  `expiry` datetime DEFAULT NULL,
  `warnedby` int(10) NOT NULL DEFAULT 0,
  `type` varchar(10) NOT NULL DEFAULT '',
  `active` enum('yes','no') DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;
