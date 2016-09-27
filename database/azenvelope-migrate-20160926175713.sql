# WordPress MySQL database migration
#
# Generated: Monday 26. September 2016 17:57 UTC
# Hostname: localhost
# Database: `azenvelope`
# --------------------------------------------------------

/*!40101 SET NAMES utf8mb4 */;

SET sql_mode='NO_AUTO_VALUE_ON_ZERO';



#
# Delete any existing table `aze_commentmeta`
#

DROP TABLE IF EXISTS `aze_commentmeta`;


#
# Table structure of table `aze_commentmeta`
#

CREATE TABLE `aze_commentmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_commentmeta`
#

#
# End of data contents of table `aze_commentmeta`
# --------------------------------------------------------



#
# Delete any existing table `aze_comments`
#

DROP TABLE IF EXISTS `aze_comments`;


#
# Table structure of table `aze_comments`
#

CREATE TABLE `aze_comments` (
  `comment_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `comment_author` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `comment_author_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text COLLATE utf8_unicode_ci NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_comments`
#
INSERT INTO `aze_comments` ( `comment_ID`, `comment_post_ID`, `comment_author`, `comment_author_email`, `comment_author_url`, `comment_author_IP`, `comment_date`, `comment_date_gmt`, `comment_content`, `comment_karma`, `comment_approved`, `comment_agent`, `comment_type`, `comment_parent`, `user_id`) VALUES
(1, 1, 'A WordPress Commenter', 'wapuu@wordpress.example', 'https://wordpress.org/', '', '2016-09-12 21:29:17', '2016-09-12 21:29:17', 'Hi, this is a comment.\nTo get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.\nCommenter avatars come from <a href="https://gravatar.com">Gravatar</a>.', 0, '1', '', '', 0, 0) ;

#
# End of data contents of table `aze_comments`
# --------------------------------------------------------



#
# Delete any existing table `aze_links`
#

DROP TABLE IF EXISTS `aze_links`;


#
# Table structure of table `aze_links`
#

CREATE TABLE `aze_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link_target` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link_visible` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `link_owner` bigint(20) unsigned NOT NULL DEFAULT '1',
  `link_rating` int(11) NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link_notes` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `link_rss` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_links`
#

#
# End of data contents of table `aze_links`
# --------------------------------------------------------



#
# Delete any existing table `aze_options`
#

DROP TABLE IF EXISTS `aze_options`;


#
# Table structure of table `aze_options`
#

CREATE TABLE `aze_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `option_value` longtext COLLATE utf8_unicode_ci NOT NULL,
  `autoload` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB AUTO_INCREMENT=697 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_options`
#
INSERT INTO `aze_options` ( `option_id`, `option_name`, `option_value`, `autoload`) VALUES
(1, 'siteurl', 'http://azenvelope.loc', 'yes'),
(2, 'home', 'http://azenvelope.loc', 'yes'),
(3, 'blogname', 'Arizona Envelope', 'yes'),
(4, 'blogdescription', 'Just another WordPress site', 'yes'),
(5, 'users_can_register', '0', 'yes'),
(6, 'admin_email', 'andres.castillo@oktara.com', 'yes'),
(7, 'start_of_week', '1', 'yes'),
(8, 'use_balanceTags', '0', 'yes'),
(9, 'use_smilies', '1', 'yes'),
(10, 'require_name_email', '1', 'yes'),
(11, 'comments_notify', '1', 'yes'),
(12, 'posts_per_rss', '10', 'yes'),
(13, 'rss_use_excerpt', '0', 'yes'),
(14, 'mailserver_url', 'mail.example.com', 'yes'),
(15, 'mailserver_login', 'login@example.com', 'yes'),
(16, 'mailserver_pass', 'password', 'yes'),
(17, 'mailserver_port', '110', 'yes'),
(18, 'default_category', '1', 'yes'),
(19, 'default_comment_status', 'open', 'yes'),
(20, 'default_ping_status', 'open', 'yes'),
(21, 'default_pingback_flag', '1', 'yes'),
(22, 'posts_per_page', '10', 'yes'),
(23, 'date_format', 'F j, Y', 'yes'),
(24, 'time_format', 'g:i a', 'yes'),
(25, 'links_updated_date_format', 'F j, Y g:i a', 'yes'),
(26, 'comment_moderation', '0', 'yes'),
(27, 'moderation_notify', '1', 'yes'),
(28, 'permalink_structure', '/%year%/%monthnum%/%day%/%postname%/', 'yes'),
(29, 'rewrite_rules', 'a:196:{s:24:"^wc-auth/v([1]{1})/(.*)?";s:63:"index.php?wc-auth-version=$matches[1]&wc-auth-route=$matches[2]";s:22:"^wc-api/v([1-3]{1})/?$";s:51:"index.php?wc-api-version=$matches[1]&wc-api-route=/";s:24:"^wc-api/v([1-3]{1})(.*)?";s:61:"index.php?wc-api-version=$matches[1]&wc-api-route=$matches[2]";s:11:"products/?$";s:27:"index.php?post_type=product";s:41:"products/feed/(feed|rdf|rss|rss2|atom)/?$";s:44:"index.php?post_type=product&feed=$matches[1]";s:36:"products/(feed|rdf|rss|rss2|atom)/?$";s:44:"index.php?post_type=product&feed=$matches[1]";s:28:"products/page/([0-9]{1,})/?$";s:45:"index.php?post_type=product&paged=$matches[1]";s:11:"^wp-json/?$";s:22:"index.php?rest_route=/";s:14:"^wp-json/(.*)?";s:33:"index.php?rest_route=/$matches[1]";s:47:"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?category_name=$matches[1]&feed=$matches[2]";s:42:"category/(.+?)/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?category_name=$matches[1]&feed=$matches[2]";s:23:"category/(.+?)/embed/?$";s:46:"index.php?category_name=$matches[1]&embed=true";s:35:"category/(.+?)/page/?([0-9]{1,})/?$";s:53:"index.php?category_name=$matches[1]&paged=$matches[2]";s:32:"category/(.+?)/wc-api(/(.*))?/?$";s:54:"index.php?category_name=$matches[1]&wc-api=$matches[3]";s:17:"category/(.+?)/?$";s:35:"index.php?category_name=$matches[1]";s:44:"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?tag=$matches[1]&feed=$matches[2]";s:39:"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?tag=$matches[1]&feed=$matches[2]";s:20:"tag/([^/]+)/embed/?$";s:36:"index.php?tag=$matches[1]&embed=true";s:32:"tag/([^/]+)/page/?([0-9]{1,})/?$";s:43:"index.php?tag=$matches[1]&paged=$matches[2]";s:29:"tag/([^/]+)/wc-api(/(.*))?/?$";s:44:"index.php?tag=$matches[1]&wc-api=$matches[3]";s:14:"tag/([^/]+)/?$";s:25:"index.php?tag=$matches[1]";s:45:"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?post_format=$matches[1]&feed=$matches[2]";s:40:"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?post_format=$matches[1]&feed=$matches[2]";s:21:"type/([^/]+)/embed/?$";s:44:"index.php?post_format=$matches[1]&embed=true";s:33:"type/([^/]+)/page/?([0-9]{1,})/?$";s:51:"index.php?post_format=$matches[1]&paged=$matches[2]";s:15:"type/([^/]+)/?$";s:33:"index.php?post_format=$matches[1]";s:55:"product-category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?product_cat=$matches[1]&feed=$matches[2]";s:50:"product-category/(.+?)/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?product_cat=$matches[1]&feed=$matches[2]";s:31:"product-category/(.+?)/embed/?$";s:44:"index.php?product_cat=$matches[1]&embed=true";s:43:"product-category/(.+?)/page/?([0-9]{1,})/?$";s:51:"index.php?product_cat=$matches[1]&paged=$matches[2]";s:25:"product-category/(.+?)/?$";s:33:"index.php?product_cat=$matches[1]";s:52:"product-tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?product_tag=$matches[1]&feed=$matches[2]";s:47:"product-tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?product_tag=$matches[1]&feed=$matches[2]";s:28:"product-tag/([^/]+)/embed/?$";s:44:"index.php?product_tag=$matches[1]&embed=true";s:40:"product-tag/([^/]+)/page/?([0-9]{1,})/?$";s:51:"index.php?product_tag=$matches[1]&paged=$matches[2]";s:22:"product-tag/([^/]+)/?$";s:33:"index.php?product_tag=$matches[1]";s:35:"product/[^/]+/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:45:"product/[^/]+/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:65:"product/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:60:"product/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:60:"product/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:41:"product/[^/]+/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:24:"product/([^/]+)/embed/?$";s:40:"index.php?product=$matches[1]&embed=true";s:28:"product/([^/]+)/trackback/?$";s:34:"index.php?product=$matches[1]&tb=1";s:48:"product/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:46:"index.php?product=$matches[1]&feed=$matches[2]";s:43:"product/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:46:"index.php?product=$matches[1]&feed=$matches[2]";s:36:"product/([^/]+)/page/?([0-9]{1,})/?$";s:47:"index.php?product=$matches[1]&paged=$matches[2]";s:43:"product/([^/]+)/comment-page-([0-9]{1,})/?$";s:47:"index.php?product=$matches[1]&cpage=$matches[2]";s:33:"product/([^/]+)/wc-api(/(.*))?/?$";s:48:"index.php?product=$matches[1]&wc-api=$matches[3]";s:39:"product/[^/]+/([^/]+)/wc-api(/(.*))?/?$";s:51:"index.php?attachment=$matches[1]&wc-api=$matches[3]";s:50:"product/[^/]+/attachment/([^/]+)/wc-api(/(.*))?/?$";s:51:"index.php?attachment=$matches[1]&wc-api=$matches[3]";s:32:"product/([^/]+)(?:/([0-9]+))?/?$";s:46:"index.php?product=$matches[1]&page=$matches[2]";s:24:"product/[^/]+/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:34:"product/[^/]+/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:54:"product/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:49:"product/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:49:"product/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:30:"product/[^/]+/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:45:"product_variation/[^/]+/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:55:"product_variation/[^/]+/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:75:"product_variation/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:70:"product_variation/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:70:"product_variation/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:51:"product_variation/[^/]+/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:34:"product_variation/([^/]+)/embed/?$";s:50:"index.php?product_variation=$matches[1]&embed=true";s:38:"product_variation/([^/]+)/trackback/?$";s:44:"index.php?product_variation=$matches[1]&tb=1";s:46:"product_variation/([^/]+)/page/?([0-9]{1,})/?$";s:57:"index.php?product_variation=$matches[1]&paged=$matches[2]";s:53:"product_variation/([^/]+)/comment-page-([0-9]{1,})/?$";s:57:"index.php?product_variation=$matches[1]&cpage=$matches[2]";s:43:"product_variation/([^/]+)/wc-api(/(.*))?/?$";s:58:"index.php?product_variation=$matches[1]&wc-api=$matches[3]";s:49:"product_variation/[^/]+/([^/]+)/wc-api(/(.*))?/?$";s:51:"index.php?attachment=$matches[1]&wc-api=$matches[3]";s:60:"product_variation/[^/]+/attachment/([^/]+)/wc-api(/(.*))?/?$";s:51:"index.php?attachment=$matches[1]&wc-api=$matches[3]";s:42:"product_variation/([^/]+)(?:/([0-9]+))?/?$";s:56:"index.php?product_variation=$matches[1]&page=$matches[2]";s:34:"product_variation/[^/]+/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:44:"product_variation/[^/]+/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:64:"product_variation/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:59:"product_variation/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:59:"product_variation/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:40:"product_variation/[^/]+/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:45:"shop_order_refund/[^/]+/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:55:"shop_order_refund/[^/]+/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:75:"shop_order_refund/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:70:"shop_order_refund/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:70:"shop_order_refund/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:51:"shop_order_refund/[^/]+/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:34:"shop_order_refund/([^/]+)/embed/?$";s:50:"index.php?shop_order_refund=$matches[1]&embed=true";s:38:"shop_order_refund/([^/]+)/trackback/?$";s:44:"index.php?shop_order_refund=$matches[1]&tb=1";s:46:"shop_order_refund/([^/]+)/page/?([0-9]{1,})/?$";s:57:"index.php?shop_order_refund=$matches[1]&paged=$matches[2]";s:53:"shop_order_refund/([^/]+)/comment-page-([0-9]{1,})/?$";s:57:"index.php?shop_order_refund=$matches[1]&cpage=$matches[2]";s:43:"shop_order_refund/([^/]+)/wc-api(/(.*))?/?$";s:58:"index.php?shop_order_refund=$matches[1]&wc-api=$matches[3]";s:49:"shop_order_refund/[^/]+/([^/]+)/wc-api(/(.*))?/?$";s:51:"index.php?attachment=$matches[1]&wc-api=$matches[3]";s:60:"shop_order_refund/[^/]+/attachment/([^/]+)/wc-api(/(.*))?/?$";s:51:"index.php?attachment=$matches[1]&wc-api=$matches[3]";s:42:"shop_order_refund/([^/]+)(?:/([0-9]+))?/?$";s:56:"index.php?shop_order_refund=$matches[1]&page=$matches[2]";s:34:"shop_order_refund/[^/]+/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:44:"shop_order_refund/[^/]+/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:64:"shop_order_refund/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:59:"shop_order_refund/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:59:"shop_order_refund/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:40:"shop_order_refund/[^/]+/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:12:"robots\\.txt$";s:18:"index.php?robots=1";s:48:".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$";s:18:"index.php?feed=old";s:20:".*wp-app\\.php(/.*)?$";s:19:"index.php?error=403";s:18:".*wp-register.php$";s:23:"index.php?register=true";s:32:"feed/(feed|rdf|rss|rss2|atom)/?$";s:27:"index.php?&feed=$matches[1]";s:27:"(feed|rdf|rss|rss2|atom)/?$";s:27:"index.php?&feed=$matches[1]";s:8:"embed/?$";s:21:"index.php?&embed=true";s:20:"page/?([0-9]{1,})/?$";s:28:"index.php?&paged=$matches[1]";s:27:"comment-page-([0-9]{1,})/?$";s:39:"index.php?&page_id=40&cpage=$matches[1]";s:17:"wc-api(/(.*))?/?$";s:29:"index.php?&wc-api=$matches[2]";s:41:"comments/feed/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?&feed=$matches[1]&withcomments=1";s:36:"comments/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?&feed=$matches[1]&withcomments=1";s:17:"comments/embed/?$";s:21:"index.php?&embed=true";s:26:"comments/wc-api(/(.*))?/?$";s:29:"index.php?&wc-api=$matches[2]";s:44:"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:40:"index.php?s=$matches[1]&feed=$matches[2]";s:39:"search/(.+)/(feed|rdf|rss|rss2|atom)/?$";s:40:"index.php?s=$matches[1]&feed=$matches[2]";s:20:"search/(.+)/embed/?$";s:34:"index.php?s=$matches[1]&embed=true";s:32:"search/(.+)/page/?([0-9]{1,})/?$";s:41:"index.php?s=$matches[1]&paged=$matches[2]";s:29:"search/(.+)/wc-api(/(.*))?/?$";s:42:"index.php?s=$matches[1]&wc-api=$matches[3]";s:14:"search/(.+)/?$";s:23:"index.php?s=$matches[1]";s:47:"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?author_name=$matches[1]&feed=$matches[2]";s:42:"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?author_name=$matches[1]&feed=$matches[2]";s:23:"author/([^/]+)/embed/?$";s:44:"index.php?author_name=$matches[1]&embed=true";s:35:"author/([^/]+)/page/?([0-9]{1,})/?$";s:51:"index.php?author_name=$matches[1]&paged=$matches[2]";s:32:"author/([^/]+)/wc-api(/(.*))?/?$";s:52:"index.php?author_name=$matches[1]&wc-api=$matches[3]";s:17:"author/([^/]+)/?$";s:33:"index.php?author_name=$matches[1]";s:69:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$";s:80:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]";s:64:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$";s:80:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]";s:45:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$";s:74:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true";s:57:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$";s:81:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]";s:54:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/wc-api(/(.*))?/?$";s:82:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&wc-api=$matches[5]";s:39:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$";s:63:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]";s:56:"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$";s:64:"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]";s:51:"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$";s:64:"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]";s:32:"([0-9]{4})/([0-9]{1,2})/embed/?$";s:58:"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true";s:44:"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$";s:65:"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]";s:41:"([0-9]{4})/([0-9]{1,2})/wc-api(/(.*))?/?$";s:66:"index.php?year=$matches[1]&monthnum=$matches[2]&wc-api=$matches[4]";s:26:"([0-9]{4})/([0-9]{1,2})/?$";s:47:"index.php?year=$matches[1]&monthnum=$matches[2]";s:43:"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$";s:43:"index.php?year=$matches[1]&feed=$matches[2]";s:38:"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$";s:43:"index.php?year=$matches[1]&feed=$matches[2]";s:19:"([0-9]{4})/embed/?$";s:37:"index.php?year=$matches[1]&embed=true";s:31:"([0-9]{4})/page/?([0-9]{1,})/?$";s:44:"index.php?year=$matches[1]&paged=$matches[2]";s:28:"([0-9]{4})/wc-api(/(.*))?/?$";s:45:"index.php?year=$matches[1]&wc-api=$matches[3]";s:13:"([0-9]{4})/?$";s:26:"index.php?year=$matches[1]";s:58:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:68:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:88:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:83:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:83:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:64:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:53:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/embed/?$";s:91:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&embed=true";s:57:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/trackback/?$";s:85:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&tb=1";s:77:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:97:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]";s:72:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:97:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]";s:65:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/page/?([0-9]{1,})/?$";s:98:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&paged=$matches[5]";s:72:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/comment-page-([0-9]{1,})/?$";s:98:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&cpage=$matches[5]";s:62:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/wc-api(/(.*))?/?$";s:99:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&wc-api=$matches[6]";s:62:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/wc-api(/(.*))?/?$";s:51:"index.php?attachment=$matches[1]&wc-api=$matches[3]";s:73:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/wc-api(/(.*))?/?$";s:51:"index.php?attachment=$matches[1]&wc-api=$matches[3]";s:61:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)(?:/([0-9]+))?/?$";s:97:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&page=$matches[5]";s:47:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:57:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:77:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:72:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:72:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:53:"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:64:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$";s:81:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&cpage=$matches[4]";s:51:"([0-9]{4})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$";s:65:"index.php?year=$matches[1]&monthnum=$matches[2]&cpage=$matches[3]";s:38:"([0-9]{4})/comment-page-([0-9]{1,})/?$";s:44:"index.php?year=$matches[1]&cpage=$matches[2]";s:27:".?.+?/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:37:".?.+?/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:57:".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:52:".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:52:".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:33:".?.+?/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:16:"(.?.+?)/embed/?$";s:41:"index.php?pagename=$matches[1]&embed=true";s:20:"(.?.+?)/trackback/?$";s:35:"index.php?pagename=$matches[1]&tb=1";s:40:"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$";s:47:"index.php?pagename=$matches[1]&feed=$matches[2]";s:35:"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$";s:47:"index.php?pagename=$matches[1]&feed=$matches[2]";s:28:"(.?.+?)/page/?([0-9]{1,})/?$";s:48:"index.php?pagename=$matches[1]&paged=$matches[2]";s:35:"(.?.+?)/comment-page-([0-9]{1,})/?$";s:48:"index.php?pagename=$matches[1]&cpage=$matches[2]";s:25:"(.?.+?)/wc-api(/(.*))?/?$";s:49:"index.php?pagename=$matches[1]&wc-api=$matches[3]";s:28:"(.?.+?)/order-pay(/(.*))?/?$";s:52:"index.php?pagename=$matches[1]&order-pay=$matches[3]";s:33:"(.?.+?)/order-received(/(.*))?/?$";s:57:"index.php?pagename=$matches[1]&order-received=$matches[3]";s:25:"(.?.+?)/orders(/(.*))?/?$";s:49:"index.php?pagename=$matches[1]&orders=$matches[3]";s:29:"(.?.+?)/view-order(/(.*))?/?$";s:53:"index.php?pagename=$matches[1]&view-order=$matches[3]";s:28:"(.?.+?)/downloads(/(.*))?/?$";s:52:"index.php?pagename=$matches[1]&downloads=$matches[3]";s:31:"(.?.+?)/edit-account(/(.*))?/?$";s:55:"index.php?pagename=$matches[1]&edit-account=$matches[3]";s:31:"(.?.+?)/edit-address(/(.*))?/?$";s:55:"index.php?pagename=$matches[1]&edit-address=$matches[3]";s:34:"(.?.+?)/payment-methods(/(.*))?/?$";s:58:"index.php?pagename=$matches[1]&payment-methods=$matches[3]";s:32:"(.?.+?)/lost-password(/(.*))?/?$";s:56:"index.php?pagename=$matches[1]&lost-password=$matches[3]";s:34:"(.?.+?)/customer-logout(/(.*))?/?$";s:58:"index.php?pagename=$matches[1]&customer-logout=$matches[3]";s:37:"(.?.+?)/add-payment-method(/(.*))?/?$";s:61:"index.php?pagename=$matches[1]&add-payment-method=$matches[3]";s:40:"(.?.+?)/delete-payment-method(/(.*))?/?$";s:64:"index.php?pagename=$matches[1]&delete-payment-method=$matches[3]";s:45:"(.?.+?)/set-default-payment-method(/(.*))?/?$";s:69:"index.php?pagename=$matches[1]&set-default-payment-method=$matches[3]";s:31:".?.+?/([^/]+)/wc-api(/(.*))?/?$";s:51:"index.php?attachment=$matches[1]&wc-api=$matches[3]";s:42:".?.+?/attachment/([^/]+)/wc-api(/(.*))?/?$";s:51:"index.php?attachment=$matches[1]&wc-api=$matches[3]";s:24:"(.?.+?)(?:/([0-9]+))?/?$";s:47:"index.php?pagename=$matches[1]&page=$matches[2]";}', 'yes'),
(30, 'hack_file', '0', 'yes'),
(31, 'blog_charset', 'UTF-8', 'yes'),
(32, 'moderation_keys', '', 'no'),
(33, 'active_plugins', 'a:6:{i:0;s:29:"gravityforms/gravityforms.php";i:1;s:34:"advanced-custom-fields-pro/acf.php";i:2;s:47:"better-search-replace/better-search-replace.php";i:3;s:23:"http-auth/http-auth.php";i:4;s:27:"woocommerce/woocommerce.php";i:5;s:31:"wp-migrate-db/wp-migrate-db.php";}', 'yes'),
(34, 'category_base', '', 'yes'),
(35, 'ping_sites', 'http://rpc.pingomatic.com/', 'yes'),
(36, 'comment_max_links', '2', 'yes'),
(37, 'gmt_offset', '0', 'yes'),
(38, 'default_email_category', '1', 'yes'),
(39, 'recently_edited', '', 'no'),
(40, 'template', 'arizona-envelope', 'yes'),
(41, 'stylesheet', 'arizona-envelope', 'yes'),
(42, 'comment_whitelist', '1', 'yes'),
(43, 'blacklist_keys', '', 'no'),
(44, 'comment_registration', '0', 'yes'),
(45, 'html_type', 'text/html', 'yes'),
(46, 'use_trackback', '0', 'yes'),
(47, 'default_role', 'subscriber', 'yes'),
(48, 'db_version', '37965', 'yes'),
(49, 'uploads_use_yearmonth_folders', '1', 'yes'),
(50, 'upload_path', '', 'yes'),
(51, 'blog_public', '0', 'yes'),
(52, 'default_link_category', '2', 'yes'),
(53, 'show_on_front', 'page', 'yes'),
(54, 'tag_base', '', 'yes'),
(55, 'show_avatars', '1', 'yes'),
(56, 'avatar_rating', 'G', 'yes'),
(57, 'upload_url_path', '', 'yes'),
(58, 'thumbnail_size_w', '150', 'yes'),
(59, 'thumbnail_size_h', '150', 'yes'),
(60, 'thumbnail_crop', '1', 'yes'),
(61, 'medium_size_w', '300', 'yes'),
(62, 'medium_size_h', '300', 'yes'),
(63, 'avatar_default', 'mystery', 'yes'),
(64, 'large_size_w', '1024', 'yes'),
(65, 'large_size_h', '1024', 'yes'),
(66, 'image_default_link_type', 'none', 'yes'),
(67, 'image_default_size', '', 'yes'),
(68, 'image_default_align', '', 'yes'),
(69, 'close_comments_for_old_posts', '0', 'yes'),
(70, 'close_comments_days_old', '14', 'yes'),
(71, 'thread_comments', '1', 'yes'),
(72, 'thread_comments_depth', '5', 'yes'),
(73, 'page_comments', '0', 'yes'),
(74, 'comments_per_page', '50', 'yes'),
(75, 'default_comments_page', 'newest', 'yes'),
(76, 'comment_order', 'asc', 'yes'),
(77, 'sticky_posts', 'a:0:{}', 'yes'),
(78, 'widget_categories', 'a:2:{i:2;a:4:{s:5:"title";s:0:"";s:5:"count";i:0;s:12:"hierarchical";i:0;s:8:"dropdown";i:0;}s:12:"_multiwidget";i:1;}', 'yes'),
(79, 'widget_text', 'a:3:{i:1;a:0:{}i:2;a:3:{s:5:"title";s:0:"";s:4:"text";s:104:"<a href="/">\r\n<img src="/wp-content/uploads/2016/09/arizona-envelope-logo-placeholder.png" alt="">\r\n</a>";s:6:"filter";b:0;}s:12:"_multiwidget";i:1;}', 'yes'),
(80, 'widget_rss', 'a:2:{i:1;a:0:{}s:12:"_multiwidget";i:1;}', 'yes'),
(81, 'uninstall_plugins', 'a:0:{}', 'no'),
(82, 'timezone_string', '', 'yes'),
(83, 'page_for_posts', '45', 'yes'),
(84, 'page_on_front', '40', 'yes'),
(85, 'default_post_format', '0', 'yes'),
(86, 'link_manager_enabled', '0', 'yes'),
(87, 'finished_splitting_shared_terms', '1', 'yes'),
(88, 'site_icon', '0', 'yes'),
(89, 'medium_large_size_w', '768', 'yes'),
(90, 'medium_large_size_h', '0', 'yes'),
(91, 'initial_db_version', '37965', 'yes'),
(92, 'aze_user_roles', 'a:7:{s:13:"administrator";a:2:{s:4:"name";s:13:"Administrator";s:12:"capabilities";a:131:{s:13:"switch_themes";b:1;s:11:"edit_themes";b:1;s:16:"activate_plugins";b:1;s:12:"edit_plugins";b:1;s:10:"edit_users";b:1;s:10:"edit_files";b:1;s:14:"manage_options";b:1;s:17:"moderate_comments";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:12:"upload_files";b:1;s:6:"import";b:1;s:15:"unfiltered_html";b:1;s:10:"edit_posts";b:1;s:17:"edit_others_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:10:"edit_pages";b:1;s:4:"read";b:1;s:8:"level_10";b:1;s:7:"level_9";b:1;s:7:"level_8";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:17:"edit_others_pages";b:1;s:20:"edit_published_pages";b:1;s:13:"publish_pages";b:1;s:12:"delete_pages";b:1;s:19:"delete_others_pages";b:1;s:22:"delete_published_pages";b:1;s:12:"delete_posts";b:1;s:19:"delete_others_posts";b:1;s:22:"delete_published_posts";b:1;s:20:"delete_private_posts";b:1;s:18:"edit_private_posts";b:1;s:18:"read_private_posts";b:1;s:20:"delete_private_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"read_private_pages";b:1;s:12:"delete_users";b:1;s:12:"create_users";b:1;s:17:"unfiltered_upload";b:1;s:14:"edit_dashboard";b:1;s:14:"update_plugins";b:1;s:14:"delete_plugins";b:1;s:15:"install_plugins";b:1;s:13:"update_themes";b:1;s:14:"install_themes";b:1;s:11:"update_core";b:1;s:10:"list_users";b:1;s:12:"remove_users";b:1;s:13:"promote_users";b:1;s:18:"edit_theme_options";b:1;s:13:"delete_themes";b:1;s:6:"export";b:1;s:18:"manage_woocommerce";b:1;s:24:"view_woocommerce_reports";b:1;s:12:"edit_product";b:1;s:12:"read_product";b:1;s:14:"delete_product";b:1;s:13:"edit_products";b:1;s:20:"edit_others_products";b:1;s:16:"publish_products";b:1;s:21:"read_private_products";b:1;s:15:"delete_products";b:1;s:23:"delete_private_products";b:1;s:25:"delete_published_products";b:1;s:22:"delete_others_products";b:1;s:21:"edit_private_products";b:1;s:23:"edit_published_products";b:1;s:20:"manage_product_terms";b:1;s:18:"edit_product_terms";b:1;s:20:"delete_product_terms";b:1;s:20:"assign_product_terms";b:1;s:15:"edit_shop_order";b:1;s:15:"read_shop_order";b:1;s:17:"delete_shop_order";b:1;s:16:"edit_shop_orders";b:1;s:23:"edit_others_shop_orders";b:1;s:19:"publish_shop_orders";b:1;s:24:"read_private_shop_orders";b:1;s:18:"delete_shop_orders";b:1;s:26:"delete_private_shop_orders";b:1;s:28:"delete_published_shop_orders";b:1;s:25:"delete_others_shop_orders";b:1;s:24:"edit_private_shop_orders";b:1;s:26:"edit_published_shop_orders";b:1;s:23:"manage_shop_order_terms";b:1;s:21:"edit_shop_order_terms";b:1;s:23:"delete_shop_order_terms";b:1;s:23:"assign_shop_order_terms";b:1;s:16:"edit_shop_coupon";b:1;s:16:"read_shop_coupon";b:1;s:18:"delete_shop_coupon";b:1;s:17:"edit_shop_coupons";b:1;s:24:"edit_others_shop_coupons";b:1;s:20:"publish_shop_coupons";b:1;s:25:"read_private_shop_coupons";b:1;s:19:"delete_shop_coupons";b:1;s:27:"delete_private_shop_coupons";b:1;s:29:"delete_published_shop_coupons";b:1;s:26:"delete_others_shop_coupons";b:1;s:25:"edit_private_shop_coupons";b:1;s:27:"edit_published_shop_coupons";b:1;s:24:"manage_shop_coupon_terms";b:1;s:22:"edit_shop_coupon_terms";b:1;s:24:"delete_shop_coupon_terms";b:1;s:24:"assign_shop_coupon_terms";b:1;s:17:"edit_shop_webhook";b:1;s:17:"read_shop_webhook";b:1;s:19:"delete_shop_webhook";b:1;s:18:"edit_shop_webhooks";b:1;s:25:"edit_others_shop_webhooks";b:1;s:21:"publish_shop_webhooks";b:1;s:26:"read_private_shop_webhooks";b:1;s:20:"delete_shop_webhooks";b:1;s:28:"delete_private_shop_webhooks";b:1;s:30:"delete_published_shop_webhooks";b:1;s:27:"delete_others_shop_webhooks";b:1;s:26:"edit_private_shop_webhooks";b:1;s:28:"edit_published_shop_webhooks";b:1;s:25:"manage_shop_webhook_terms";b:1;s:23:"edit_shop_webhook_terms";b:1;s:25:"delete_shop_webhook_terms";b:1;s:25:"assign_shop_webhook_terms";b:1;}}s:6:"editor";a:2:{s:4:"name";s:6:"Editor";s:12:"capabilities";a:34:{s:17:"moderate_comments";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:12:"upload_files";b:1;s:15:"unfiltered_html";b:1;s:10:"edit_posts";b:1;s:17:"edit_others_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:10:"edit_pages";b:1;s:4:"read";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:17:"edit_others_pages";b:1;s:20:"edit_published_pages";b:1;s:13:"publish_pages";b:1;s:12:"delete_pages";b:1;s:19:"delete_others_pages";b:1;s:22:"delete_published_pages";b:1;s:12:"delete_posts";b:1;s:19:"delete_others_posts";b:1;s:22:"delete_published_posts";b:1;s:20:"delete_private_posts";b:1;s:18:"edit_private_posts";b:1;s:18:"read_private_posts";b:1;s:20:"delete_private_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"read_private_pages";b:1;}}s:6:"author";a:2:{s:4:"name";s:6:"Author";s:12:"capabilities";a:10:{s:12:"upload_files";b:1;s:10:"edit_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:4:"read";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:12:"delete_posts";b:1;s:22:"delete_published_posts";b:1;}}s:11:"contributor";a:2:{s:4:"name";s:11:"Contributor";s:12:"capabilities";a:5:{s:10:"edit_posts";b:1;s:4:"read";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:12:"delete_posts";b:1;}}s:10:"subscriber";a:2:{s:4:"name";s:10:"Subscriber";s:12:"capabilities";a:2:{s:4:"read";b:1;s:7:"level_0";b:1;}}s:8:"customer";a:2:{s:4:"name";s:8:"Customer";s:12:"capabilities";a:1:{s:4:"read";b:1;}}s:12:"shop_manager";a:2:{s:4:"name";s:12:"Shop Manager";s:12:"capabilities";a:110:{s:7:"level_9";b:1;s:7:"level_8";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:4:"read";b:1;s:18:"read_private_pages";b:1;s:18:"read_private_posts";b:1;s:10:"edit_users";b:1;s:10:"edit_posts";b:1;s:10:"edit_pages";b:1;s:20:"edit_published_posts";b:1;s:20:"edit_published_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"edit_private_posts";b:1;s:17:"edit_others_posts";b:1;s:17:"edit_others_pages";b:1;s:13:"publish_posts";b:1;s:13:"publish_pages";b:1;s:12:"delete_posts";b:1;s:12:"delete_pages";b:1;s:20:"delete_private_pages";b:1;s:20:"delete_private_posts";b:1;s:22:"delete_published_pages";b:1;s:22:"delete_published_posts";b:1;s:19:"delete_others_posts";b:1;s:19:"delete_others_pages";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:17:"moderate_comments";b:1;s:15:"unfiltered_html";b:1;s:12:"upload_files";b:1;s:6:"export";b:1;s:6:"import";b:1;s:10:"list_users";b:1;s:18:"manage_woocommerce";b:1;s:24:"view_woocommerce_reports";b:1;s:12:"edit_product";b:1;s:12:"read_product";b:1;s:14:"delete_product";b:1;s:13:"edit_products";b:1;s:20:"edit_others_products";b:1;s:16:"publish_products";b:1;s:21:"read_private_products";b:1;s:15:"delete_products";b:1;s:23:"delete_private_products";b:1;s:25:"delete_published_products";b:1;s:22:"delete_others_products";b:1;s:21:"edit_private_products";b:1;s:23:"edit_published_products";b:1;s:20:"manage_product_terms";b:1;s:18:"edit_product_terms";b:1;s:20:"delete_product_terms";b:1;s:20:"assign_product_terms";b:1;s:15:"edit_shop_order";b:1;s:15:"read_shop_order";b:1;s:17:"delete_shop_order";b:1;s:16:"edit_shop_orders";b:1;s:23:"edit_others_shop_orders";b:1;s:19:"publish_shop_orders";b:1;s:24:"read_private_shop_orders";b:1;s:18:"delete_shop_orders";b:1;s:26:"delete_private_shop_orders";b:1;s:28:"delete_published_shop_orders";b:1;s:25:"delete_others_shop_orders";b:1;s:24:"edit_private_shop_orders";b:1;s:26:"edit_published_shop_orders";b:1;s:23:"manage_shop_order_terms";b:1;s:21:"edit_shop_order_terms";b:1;s:23:"delete_shop_order_terms";b:1;s:23:"assign_shop_order_terms";b:1;s:16:"edit_shop_coupon";b:1;s:16:"read_shop_coupon";b:1;s:18:"delete_shop_coupon";b:1;s:17:"edit_shop_coupons";b:1;s:24:"edit_others_shop_coupons";b:1;s:20:"publish_shop_coupons";b:1;s:25:"read_private_shop_coupons";b:1;s:19:"delete_shop_coupons";b:1;s:27:"delete_private_shop_coupons";b:1;s:29:"delete_published_shop_coupons";b:1;s:26:"delete_others_shop_coupons";b:1;s:25:"edit_private_shop_coupons";b:1;s:27:"edit_published_shop_coupons";b:1;s:24:"manage_shop_coupon_terms";b:1;s:22:"edit_shop_coupon_terms";b:1;s:24:"delete_shop_coupon_terms";b:1;s:24:"assign_shop_coupon_terms";b:1;s:17:"edit_shop_webhook";b:1;s:17:"read_shop_webhook";b:1;s:19:"delete_shop_webhook";b:1;s:18:"edit_shop_webhooks";b:1;s:25:"edit_others_shop_webhooks";b:1;s:21:"publish_shop_webhooks";b:1;s:26:"read_private_shop_webhooks";b:1;s:20:"delete_shop_webhooks";b:1;s:28:"delete_private_shop_webhooks";b:1;s:30:"delete_published_shop_webhooks";b:1;s:27:"delete_others_shop_webhooks";b:1;s:26:"edit_private_shop_webhooks";b:1;s:28:"edit_published_shop_webhooks";b:1;s:25:"manage_shop_webhook_terms";b:1;s:23:"edit_shop_webhook_terms";b:1;s:25:"delete_shop_webhook_terms";b:1;s:25:"assign_shop_webhook_terms";b:1;}}}', 'yes'),
(93, 'widget_search', 'a:2:{i:2;a:1:{s:5:"title";s:0:"";}s:12:"_multiwidget";i:1;}', 'yes'),
(94, 'widget_recent-posts', 'a:2:{i:2;a:2:{s:5:"title";s:0:"";s:6:"number";i:5;}s:12:"_multiwidget";i:1;}', 'yes'),
(95, 'widget_recent-comments', 'a:2:{i:2;a:2:{s:5:"title";s:0:"";s:6:"number";i:5;}s:12:"_multiwidget";i:1;}', 'yes'),
(96, 'widget_archives', 'a:2:{i:2;a:3:{s:5:"title";s:0:"";s:5:"count";i:0;s:8:"dropdown";i:0;}s:12:"_multiwidget";i:1;}', 'yes'),
(97, 'widget_meta', 'a:2:{i:2;a:1:{s:5:"title";s:0:"";}s:12:"_multiwidget";i:1;}', 'yes'),
(98, 'sidebars_widgets', 'a:4:{s:19:"wp_inactive_widgets";a:0:{}s:15:"sidebar-primary";a:6:{i:0;s:8:"search-2";i:1;s:14:"recent-posts-2";i:2;s:17:"recent-comments-2";i:3;s:10:"archives-2";i:4;s:12:"categories-2";i:5;s:6:"meta-2";}s:14:"sidebar-footer";a:2:{i:0;s:6:"text-2";i:1;s:10:"nav_menu-2";}s:13:"array_version";i:3;}', 'yes'),
(99, 'widget_pages', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(100, 'widget_calendar', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes') ;
INSERT INTO `aze_options` ( `option_id`, `option_name`, `option_value`, `autoload`) VALUES
(101, 'widget_tag_cloud', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(102, 'widget_nav_menu', 'a:2:{i:2;a:1:{s:8:"nav_menu";i:13;}s:12:"_multiwidget";i:1;}', 'yes'),
(103, 'cron', 'a:9:{i:1474914851;a:1:{s:32:"woocommerce_cancel_unpaid_orders";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:2:{s:8:"schedule";b:0;s:4:"args";a:0:{}}}}i:1474925357;a:3:{s:16:"wp_version_check";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}s:17:"wp_update_plugins";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}s:16:"wp_update_themes";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}}i:1474925369;a:1:{s:19:"wp_scheduled_delete";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}i:1474925614;a:2:{s:28:"woocommerce_cleanup_sessions";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}s:30:"woocommerce_tracker_send_event";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}i:1474925698;a:1:{s:30:"wp_scheduled_auto_draft_delete";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}i:1474934400;a:1:{s:27:"woocommerce_scheduled_sales";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}i:1474953047;a:1:{s:17:"gravityforms_cron";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}i:1475539200;a:1:{s:25:"woocommerce_geoip_updater";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:7:"monthly";s:4:"args";a:0:{}s:8:"interval";i:2635200;}}}s:7:"version";i:2;}', 'yes'),
(116, 'can_compress_scripts', '1', 'no'),
(133, 'theme_mods_twentysixteen', 'a:1:{s:16:"sidebars_widgets";a:2:{s:4:"time";i:1473715945;s:4:"data";a:2:{s:19:"wp_inactive_widgets";a:0:{}s:9:"sidebar-1";a:6:{i:0;s:8:"search-2";i:1;s:14:"recent-posts-2";i:2;s:17:"recent-comments-2";i:3;s:10:"archives-2";i:4;s:12:"categories-2";i:5;s:6:"meta-2";}}}}', 'yes'),
(134, 'current_theme', 'Sage Starter Theme', 'yes'),
(135, 'theme_mods_arizona-envelope', 'a:2:{i:0;b:0;s:18:"nav_menu_locations";a:3:{s:18:"primary_navigation";i:6;s:14:"top_navigation";i:7;s:17:"footer_navigation";i:13;}}', 'yes'),
(136, 'theme_switched', '', 'yes'),
(137, 'recently_activated', 'a:0:{}', 'yes'),
(142, 'woocommerce_default_country', 'US:AZ', 'yes'),
(143, 'woocommerce_allowed_countries', 'all', 'yes'),
(144, 'woocommerce_all_except_countries', '', 'yes'),
(145, 'woocommerce_specific_allowed_countries', '', 'yes'),
(146, 'woocommerce_ship_to_countries', '', 'yes'),
(147, 'woocommerce_specific_ship_to_countries', '', 'yes'),
(148, 'woocommerce_default_customer_address', 'geolocation', 'yes'),
(149, 'woocommerce_calc_taxes', 'yes', 'yes'),
(150, 'woocommerce_demo_store', 'no', 'yes'),
(151, 'woocommerce_demo_store_notice', 'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.', 'no'),
(152, 'woocommerce_currency', 'USD', 'yes'),
(153, 'woocommerce_currency_pos', 'left', 'yes'),
(154, 'woocommerce_price_thousand_sep', ',', 'yes'),
(155, 'woocommerce_price_decimal_sep', '.', 'yes'),
(156, 'woocommerce_price_num_decimals', '2', 'yes'),
(157, 'woocommerce_weight_unit', 'lbs', 'yes'),
(158, 'woocommerce_dimension_unit', 'in', 'yes'),
(159, 'woocommerce_enable_review_rating', 'yes', 'yes'),
(160, 'woocommerce_review_rating_required', 'yes', 'no'),
(161, 'woocommerce_review_rating_verification_label', 'yes', 'no'),
(162, 'woocommerce_review_rating_verification_required', 'no', 'no'),
(163, 'woocommerce_shop_page_id', '4', 'yes'),
(164, 'woocommerce_shop_page_display', '', 'yes'),
(165, 'woocommerce_category_archive_display', '', 'yes'),
(166, 'woocommerce_default_catalog_orderby', 'menu_order', 'yes'),
(167, 'woocommerce_cart_redirect_after_add', 'no', 'yes'),
(168, 'woocommerce_enable_ajax_add_to_cart', 'yes', 'yes'),
(169, 'shop_catalog_image_size', 'a:3:{s:5:"width";s:3:"300";s:6:"height";s:3:"300";s:4:"crop";i:1;}', 'yes'),
(170, 'shop_single_image_size', 'a:3:{s:5:"width";s:3:"600";s:6:"height";s:3:"600";s:4:"crop";i:1;}', 'yes'),
(171, 'shop_thumbnail_image_size', 'a:3:{s:5:"width";s:3:"180";s:6:"height";s:3:"180";s:4:"crop";i:1;}', 'yes'),
(172, 'woocommerce_enable_lightbox', 'yes', 'yes'),
(173, 'woocommerce_manage_stock', 'yes', 'yes'),
(174, 'woocommerce_hold_stock_minutes', '60', 'no'),
(175, 'woocommerce_notify_low_stock', 'yes', 'no'),
(176, 'woocommerce_notify_no_stock', 'yes', 'no'),
(177, 'woocommerce_stock_email_recipient', 'andres.castillo@oktara.com', 'no'),
(178, 'woocommerce_notify_low_stock_amount', '2', 'no'),
(179, 'woocommerce_notify_no_stock_amount', '0', 'yes'),
(180, 'woocommerce_hide_out_of_stock_items', 'no', 'yes'),
(181, 'woocommerce_stock_format', '', 'yes'),
(182, 'woocommerce_file_download_method', 'force', 'no'),
(183, 'woocommerce_downloads_require_login', 'no', 'no'),
(184, 'woocommerce_downloads_grant_access_after_payment', 'yes', 'no'),
(185, 'woocommerce_prices_include_tax', 'no', 'yes'),
(186, 'woocommerce_tax_based_on', 'shipping', 'yes'),
(187, 'woocommerce_shipping_tax_class', '', 'yes'),
(188, 'woocommerce_tax_round_at_subtotal', 'no', 'yes'),
(189, 'woocommerce_tax_classes', 'Reduced Rate\nZero Rate', 'yes'),
(190, 'woocommerce_tax_display_shop', 'excl', 'yes'),
(191, 'woocommerce_tax_display_cart', 'excl', 'no'),
(192, 'woocommerce_price_display_suffix', '', 'yes'),
(193, 'woocommerce_tax_total_display', 'itemized', 'no'),
(194, 'woocommerce_enable_shipping_calc', 'yes', 'no'),
(195, 'woocommerce_shipping_cost_requires_address', 'no', 'no'),
(196, 'woocommerce_ship_to_destination', 'billing', 'no'),
(197, 'woocommerce_enable_coupons', 'yes', 'yes'),
(198, 'woocommerce_calc_discounts_sequentially', 'no', 'no'),
(199, 'woocommerce_enable_guest_checkout', 'yes', 'no'),
(200, 'woocommerce_force_ssl_checkout', 'no', 'yes'),
(201, 'woocommerce_unforce_ssl_checkout', 'no', 'yes'),
(202, 'woocommerce_cart_page_id', '5', 'yes'),
(203, 'woocommerce_checkout_page_id', '6', 'yes'),
(204, 'woocommerce_terms_page_id', '', 'no'),
(205, 'woocommerce_checkout_pay_endpoint', 'order-pay', 'yes'),
(206, 'woocommerce_checkout_order_received_endpoint', 'order-received', 'yes'),
(207, 'woocommerce_myaccount_add_payment_method_endpoint', 'add-payment-method', 'yes'),
(208, 'woocommerce_myaccount_delete_payment_method_endpoint', 'delete-payment-method', 'yes'),
(209, 'woocommerce_myaccount_set_default_payment_method_endpoint', 'set-default-payment-method', 'yes'),
(210, 'woocommerce_myaccount_page_id', '7', 'yes'),
(211, 'woocommerce_enable_signup_and_login_from_checkout', 'yes', 'no'),
(212, 'woocommerce_enable_myaccount_registration', 'no', 'no'),
(213, 'woocommerce_enable_checkout_login_reminder', 'yes', 'no'),
(214, 'woocommerce_registration_generate_username', 'yes', 'no'),
(215, 'woocommerce_registration_generate_password', 'no', 'no'),
(216, 'woocommerce_myaccount_orders_endpoint', 'orders', 'yes'),
(217, 'woocommerce_myaccount_view_order_endpoint', 'view-order', 'yes'),
(218, 'woocommerce_myaccount_downloads_endpoint', 'downloads', 'yes'),
(219, 'woocommerce_myaccount_edit_account_endpoint', 'edit-account', 'yes'),
(220, 'woocommerce_myaccount_edit_address_endpoint', 'edit-address', 'yes'),
(221, 'woocommerce_myaccount_payment_methods_endpoint', 'payment-methods', 'yes'),
(222, 'woocommerce_myaccount_lost_password_endpoint', 'lost-password', 'yes'),
(223, 'woocommerce_logout_endpoint', 'customer-logout', 'yes'),
(224, 'woocommerce_email_from_name', 'Arizona Envelope', 'no'),
(225, 'woocommerce_email_from_address', 'andres.castillo@oktara.com', 'no'),
(226, 'woocommerce_email_header_image', '', 'no'),
(227, 'woocommerce_email_footer_text', 'Arizona Envelope - Powered by WooCommerce', 'no'),
(228, 'woocommerce_email_base_color', '#557da1', 'no'),
(229, 'woocommerce_email_background_color', '#f5f5f5', 'no'),
(230, 'woocommerce_email_body_background_color', '#fdfdfd', 'no'),
(231, 'woocommerce_email_text_color', '#505050', 'no'),
(232, 'woocommerce_api_enabled', 'yes', 'yes') ;
INSERT INTO `aze_options` ( `option_id`, `option_name`, `option_value`, `autoload`) VALUES
(236, 'woocommerce_db_version', '2.6.4', 'yes'),
(237, 'woocommerce_version', '2.6.4', 'yes'),
(238, 'woocommerce_admin_notices', 'a:1:{i:1;s:13:"theme_support";}', 'yes'),
(241, 'widget_woocommerce_widget_cart', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(242, 'widget_woocommerce_layered_nav_filters', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(243, 'widget_woocommerce_layered_nav', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(244, 'widget_woocommerce_price_filter', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(245, 'widget_woocommerce_product_categories', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(246, 'widget_woocommerce_product_search', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(247, 'widget_woocommerce_product_tag_cloud', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(248, 'widget_woocommerce_products', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(249, 'widget_woocommerce_rating_filter', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(250, 'widget_woocommerce_recent_reviews', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(251, 'widget_woocommerce_recently_viewed_products', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(252, 'widget_woocommerce_top_rated_products', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(256, 'woocommerce_meta_box_errors', 'a:0:{}', 'yes'),
(264, 'woocommerce_paypal-braintree_settings', 'a:1:{s:7:"enabled";s:2:"no";}', 'yes'),
(265, 'woocommerce_stripe_settings', 'a:1:{s:7:"enabled";s:2:"no";}', 'yes'),
(266, 'woocommerce_paypal_settings', 'a:2:{s:7:"enabled";s:2:"no";s:5:"email";s:26:"andres.castillo@oktara.com";}', 'yes'),
(267, 'woocommerce_cheque_settings', 'a:1:{s:7:"enabled";s:2:"no";}', 'yes'),
(268, 'woocommerce_bacs_settings', 'a:1:{s:7:"enabled";s:2:"no";}', 'yes'),
(269, 'woocommerce_cod_settings', 'a:1:{s:7:"enabled";s:2:"no";}', 'yes'),
(270, 'woocommerce_allow_tracking', 'no', 'yes'),
(279, 'acf_version', '5.4.5', 'yes'),
(282, 'nav_menu_options', 'a:2:{i:0;b:0;s:8:"auto_add";a:0:{}}', 'yes'),
(283, 'options_site_logo', '126', 'no'),
(284, '_options_site_logo', 'field_57d7213d834ed', 'no'),
(285, 'options_retina_site_logo', '', 'no'),
(286, '_options_retina_site_logo', 'field_57d72146834ee', 'no'),
(287, 'options_toll_free_phone', '(800) 540-6883', 'no'),
(288, '_options_toll_free_phone', 'field_57d7223b5b971', 'no'),
(289, 'options_regular_phone_number', '(480) 839-7676', 'no'),
(290, '_options_regular_phone_number', 'field_57d7224d5b972', 'no'),
(291, 'options_fax_number', '(480) 897-6824', 'no'),
(292, '_options_fax_number', 'field_57d722585b973', 'no'),
(293, 'options_copyright_information', 'Copyright 2016 Arizona Envelope Company - All Rights Reserved', 'no'),
(294, '_options_copyright_information', 'field_57d72202d4357', 'no'),
(330, 'acf_pro_license', 'YToyOntzOjM6ImtleSI7czo3MjoiYjNKa1pYSmZhV1E5TlRrek1qWjhkSGx3WlQxa1pYWmxiRzl3WlhKOFpHRjBaVDB5TURFMUxUQTNMVEExSURBeE9qRTRPalE0IjtzOjM6InVybCI7czoyMToiaHR0cDovL2F6ZW52ZWxvcGUubG9jIjt9', 'yes'),
(406, 'widget_gform_widget', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(407, 'gravityformsaddon_gravityformswebapi_version', '1.0', 'yes'),
(408, 'gform_pending_installation', '', 'yes'),
(409, 'gform_enable_background_updates', '1', 'yes'),
(410, 'rg_form_version', '2.0.7.4', 'yes'),
(419, 'rg_gforms_key', 'd82fac493bb49987505a214ca357d216', 'yes'),
(420, 'gform_enable_noconflict', '1', 'yes'),
(421, 'rg_gforms_enable_akismet', '1', 'yes'),
(422, 'rg_gforms_currency', 'USD', 'yes'),
(423, 'gform_enable_toolbar_menu', '1', 'yes'),
(434, 'category_children', 'a:0:{}', 'yes'),
(478, 'options_information_links_0_button_label', 'Terms & Conditions', 'no'),
(479, '_options_information_links_0_button_label', 'field_57e2349b1879e', 'no'),
(480, 'options_information_links_0_button_url', '/terms-and-conditions/', 'no'),
(481, '_options_information_links_0_button_url', 'field_57e234a21879f', 'no'),
(482, 'options_information_links_1_button_label', 'Privacy Policy', 'no'),
(483, '_options_information_links_1_button_label', 'field_57e2349b1879e', 'no'),
(484, 'options_information_links_1_button_url', '/privacy-policy/', 'no'),
(485, '_options_information_links_1_button_url', 'field_57e234a21879f', 'no'),
(486, 'options_information_links', '2', 'no'),
(487, '_options_information_links', 'field_57e2348f1879d', 'no'),
(494, 'product_cat_children', 'a:0:{}', 'yes'),
(671, 'http_auth_username', 'ltcom', 'yes'),
(672, 'http_auth_password', 'lt2016', 'yes'),
(673, 'http_auth_message', '', 'yes'),
(674, 'http_auth_apply', 'site', 'yes'),
(675, 'http_auth_activate', '', 'yes') ;

#
# End of data contents of table `aze_options`
# --------------------------------------------------------



#
# Delete any existing table `aze_postmeta`
#

DROP TABLE IF EXISTS `aze_postmeta`;


#
# Table structure of table `aze_postmeta`
#

CREATE TABLE `aze_postmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=1076 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_postmeta`
#
INSERT INTO `aze_postmeta` ( `meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(1, 2, '_wp_page_template', 'default'),
(2, 8, '_edit_last', '1'),
(3, 8, '_visibility', 'visible'),
(4, 8, '_stock_status', 'instock'),
(5, 8, 'total_sales', '0'),
(6, 8, '_downloadable', 'no'),
(7, 8, '_virtual', 'no'),
(8, 8, '_tax_status', 'taxable'),
(9, 8, '_tax_class', ''),
(10, 8, '_purchase_note', ''),
(11, 8, '_featured', 'no'),
(12, 8, '_weight', ''),
(13, 8, '_length', ''),
(14, 8, '_width', ''),
(15, 8, '_height', ''),
(16, 8, '_sku', ''),
(17, 8, '_product_attributes', 'a:0:{}'),
(18, 8, '_regular_price', ''),
(19, 8, '_sale_price', ''),
(20, 8, '_sale_price_dates_from', ''),
(21, 8, '_sale_price_dates_to', ''),
(22, 8, '_price', ''),
(23, 8, '_sold_individually', ''),
(24, 8, '_manage_stock', 'no'),
(25, 8, '_backorders', 'no'),
(26, 8, '_stock', ''),
(27, 8, '_upsell_ids', 'a:0:{}'),
(28, 8, '_crosssell_ids', 'a:0:{}'),
(29, 8, '_product_version', '2.6.4'),
(30, 8, '_product_image_gallery', ''),
(31, 8, '_edit_lock', '1473715963:1'),
(32, 9, '_edit_last', '1'),
(33, 9, '_edit_lock', '1474442640:1'),
(34, 18, '_edit_last', '1'),
(35, 18, '_edit_lock', '1474663140:1'),
(36, 2, '_wp_trash_meta_status', 'publish'),
(37, 2, '_wp_trash_meta_time', '1473717332'),
(38, 2, '_wp_desired_post_slug', 'sample-page'),
(39, 40, '_edit_last', '1'),
(40, 40, '_wp_page_template', 'template-homepage.php'),
(41, 40, '_edit_lock', '1474911502:2'),
(42, 4, '_edit_lock', '1473717220:1'),
(43, 4, '_edit_last', '1'),
(44, 4, '_wp_page_template', 'default'),
(45, 43, '_edit_last', '1'),
(46, 43, '_wp_page_template', 'default'),
(47, 43, '_edit_lock', '1473717416:1'),
(48, 45, '_edit_last', '1'),
(49, 45, '_wp_page_template', 'default'),
(50, 45, '_edit_lock', '1473717240:1'),
(51, 47, '_edit_last', '1'),
(52, 47, '_edit_lock', '1473717312:1'),
(53, 47, '_wp_page_template', 'default'),
(54, 49, '_edit_last', '1'),
(55, 49, '_wp_page_template', 'default'),
(56, 49, '_edit_lock', '1473717355:1'),
(84, 54, '_menu_item_type', 'post_type'),
(85, 54, '_menu_item_menu_item_parent', '0'),
(86, 54, '_menu_item_object_id', '47'),
(87, 54, '_menu_item_object', 'page'),
(88, 54, '_menu_item_target', ''),
(89, 54, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(90, 54, '_menu_item_xfn', ''),
(91, 54, '_menu_item_url', ''),
(93, 55, '_menu_item_type', 'post_type'),
(94, 55, '_menu_item_menu_item_parent', '0'),
(95, 55, '_menu_item_object_id', '49'),
(96, 55, '_menu_item_object', 'page'),
(97, 55, '_menu_item_target', ''),
(98, 55, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(99, 55, '_menu_item_xfn', ''),
(100, 55, '_menu_item_url', ''),
(120, 58, '_menu_item_type', 'post_type'),
(121, 58, '_menu_item_menu_item_parent', '0'),
(122, 58, '_menu_item_object_id', '4'),
(123, 58, '_menu_item_object', 'page'),
(124, 58, '_menu_item_target', ''),
(125, 58, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(126, 58, '_menu_item_xfn', ''),
(127, 58, '_menu_item_url', ''),
(129, 59, '_menu_item_type', 'post_type'),
(130, 59, '_menu_item_menu_item_parent', '0'),
(131, 59, '_menu_item_object_id', '45'),
(132, 59, '_menu_item_object', 'page'),
(133, 59, '_menu_item_target', ''),
(134, 59, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(135, 59, '_menu_item_xfn', ''),
(136, 59, '_menu_item_url', ''),
(138, 60, '_menu_item_type', 'post_type'),
(139, 60, '_menu_item_menu_item_parent', '0'),
(140, 60, '_menu_item_object_id', '43'),
(141, 60, '_menu_item_object', 'page'),
(142, 60, '_menu_item_target', ''),
(143, 60, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(144, 60, '_menu_item_xfn', ''),
(145, 60, '_menu_item_url', ''),
(147, 61, '_menu_item_type', 'post_type'),
(148, 61, '_menu_item_menu_item_parent', '0'),
(149, 61, '_menu_item_object_id', '5'),
(150, 61, '_menu_item_object', 'page') ;
INSERT INTO `aze_postmeta` ( `meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(151, 61, '_menu_item_target', ''),
(152, 61, '_menu_item_classes', 'a:1:{i:0;s:9:"cart-icon";}'),
(153, 61, '_menu_item_xfn', ''),
(154, 61, '_menu_item_url', ''),
(156, 62, '_menu_item_type', 'custom'),
(157, 62, '_menu_item_menu_item_parent', '0'),
(158, 62, '_menu_item_object_id', '62'),
(159, 62, '_menu_item_object', 'custom'),
(160, 62, '_menu_item_target', ''),
(161, 62, '_menu_item_classes', 'a:1:{i:0;s:10:"phone-icon";}'),
(162, 62, '_menu_item_xfn', ''),
(163, 62, '_menu_item_url', 'tel:+18005406883'),
(165, 63, '_edit_last', '1'),
(166, 63, '_wp_page_template', 'default'),
(167, 63, '_edit_lock', '1473717800:1'),
(168, 65, '_edit_last', '1'),
(169, 65, '_wp_page_template', 'default'),
(170, 65, '_edit_lock', '1473717789:1'),
(171, 7, '_edit_lock', '1473717779:1'),
(172, 69, '_menu_item_type', 'post_type'),
(173, 69, '_menu_item_menu_item_parent', '0'),
(174, 69, '_menu_item_object_id', '65'),
(175, 69, '_menu_item_object', 'page'),
(176, 69, '_menu_item_target', ''),
(177, 69, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(178, 69, '_menu_item_xfn', ''),
(179, 69, '_menu_item_url', ''),
(181, 70, '_menu_item_type', 'post_type'),
(182, 70, '_menu_item_menu_item_parent', '0'),
(183, 70, '_menu_item_object_id', '63'),
(184, 70, '_menu_item_object', 'page'),
(185, 70, '_menu_item_target', ''),
(186, 70, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(187, 70, '_menu_item_xfn', ''),
(188, 70, '_menu_item_url', ''),
(190, 71, '_menu_item_type', 'custom'),
(191, 71, '_menu_item_menu_item_parent', '0'),
(192, 71, '_menu_item_object_id', '71'),
(193, 71, '_menu_item_object', 'custom'),
(194, 71, '_menu_item_target', ''),
(195, 71, '_menu_item_classes', 'a:1:{i:0;s:15:"transparent-btn";}'),
(196, 71, '_menu_item_xfn', ''),
(197, 71, '_menu_item_url', '#'),
(199, 72, '_edit_last', '1'),
(200, 72, '_wp_page_template', 'default'),
(201, 72, '_edit_lock', '1473717865:1'),
(202, 74, '_edit_last', '1'),
(203, 74, '_wp_page_template', 'default'),
(204, 74, '_edit_lock', '1473717878:1'),
(207, 77, '_edit_last', '1'),
(208, 77, '_edit_lock', '1474301122:1'),
(209, 77, '_visibility', 'visible'),
(210, 77, '_stock_status', 'instock'),
(211, 77, 'total_sales', '0'),
(212, 77, '_downloadable', 'no'),
(213, 77, '_virtual', 'no'),
(214, 77, '_tax_status', 'taxable'),
(215, 77, '_tax_class', ''),
(216, 77, '_purchase_note', ''),
(217, 77, '_featured', 'no'),
(218, 77, '_weight', ''),
(219, 77, '_length', ''),
(220, 77, '_width', ''),
(221, 77, '_height', ''),
(222, 77, '_sku', ''),
(223, 77, '_product_attributes', 'a:0:{}'),
(224, 77, '_regular_price', ''),
(225, 77, '_sale_price', ''),
(226, 77, '_sale_price_dates_from', ''),
(227, 77, '_sale_price_dates_to', ''),
(228, 77, '_price', ''),
(229, 77, '_sold_individually', ''),
(230, 77, '_manage_stock', 'no'),
(231, 77, '_backorders', 'no'),
(232, 77, '_stock', ''),
(233, 77, '_upsell_ids', 'a:0:{}'),
(234, 77, '_crosssell_ids', 'a:0:{}'),
(235, 77, '_product_version', '2.6.4'),
(236, 77, '_product_image_gallery', ''),
(237, 78, '_edit_last', '1'),
(238, 78, '_edit_lock', '1474301149:1'),
(239, 78, '_visibility', 'visible'),
(240, 78, '_stock_status', 'instock'),
(241, 78, 'total_sales', '0'),
(242, 78, '_downloadable', 'no'),
(243, 78, '_virtual', 'no'),
(244, 78, '_tax_status', 'taxable'),
(245, 78, '_tax_class', ''),
(246, 78, '_purchase_note', ''),
(247, 78, '_featured', 'no'),
(248, 78, '_weight', ''),
(249, 78, '_length', ''),
(250, 78, '_width', ''),
(251, 78, '_height', ''),
(252, 78, '_sku', ''),
(253, 78, '_product_attributes', 'a:0:{}'),
(254, 78, '_regular_price', ''),
(255, 78, '_sale_price', ''),
(256, 78, '_sale_price_dates_from', ''),
(257, 78, '_sale_price_dates_to', '') ;
INSERT INTO `aze_postmeta` ( `meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(258, 78, '_price', ''),
(259, 78, '_sold_individually', ''),
(260, 78, '_manage_stock', 'no'),
(261, 78, '_backorders', 'no'),
(262, 78, '_stock', ''),
(263, 78, '_upsell_ids', 'a:0:{}'),
(264, 78, '_crosssell_ids', 'a:0:{}'),
(265, 78, '_product_version', '2.6.4'),
(266, 78, '_product_image_gallery', ''),
(267, 79, '_edit_last', '1'),
(268, 79, '_edit_lock', '1474301175:1'),
(269, 79, '_visibility', 'visible'),
(270, 79, '_stock_status', 'instock'),
(271, 79, 'total_sales', '0'),
(272, 79, '_downloadable', 'no'),
(273, 79, '_virtual', 'no'),
(274, 79, '_tax_status', 'taxable'),
(275, 79, '_tax_class', ''),
(276, 79, '_purchase_note', ''),
(277, 79, '_featured', 'no'),
(278, 79, '_weight', ''),
(279, 79, '_length', ''),
(280, 79, '_width', ''),
(281, 79, '_height', ''),
(282, 79, '_sku', ''),
(283, 79, '_product_attributes', 'a:0:{}'),
(284, 79, '_regular_price', ''),
(285, 79, '_sale_price', ''),
(286, 79, '_sale_price_dates_from', ''),
(287, 79, '_sale_price_dates_to', ''),
(288, 79, '_price', ''),
(289, 79, '_sold_individually', ''),
(290, 79, '_manage_stock', 'no'),
(291, 79, '_backorders', 'no'),
(292, 79, '_stock', ''),
(293, 79, '_upsell_ids', 'a:0:{}'),
(294, 79, '_crosssell_ids', 'a:0:{}'),
(295, 79, '_product_version', '2.6.4'),
(296, 79, '_product_image_gallery', ''),
(297, 80, '_edit_last', '1'),
(298, 80, '_edit_lock', '1474301230:1'),
(299, 80, '_visibility', 'visible'),
(300, 80, '_stock_status', 'instock'),
(301, 80, 'total_sales', '0'),
(302, 80, '_downloadable', 'no'),
(303, 80, '_virtual', 'no'),
(304, 80, '_tax_status', 'taxable'),
(305, 80, '_tax_class', ''),
(306, 80, '_purchase_note', ''),
(307, 80, '_featured', 'no'),
(308, 80, '_weight', ''),
(309, 80, '_length', ''),
(310, 80, '_width', ''),
(311, 80, '_height', ''),
(312, 80, '_sku', ''),
(313, 80, '_product_attributes', 'a:0:{}'),
(314, 80, '_regular_price', ''),
(315, 80, '_sale_price', ''),
(316, 80, '_sale_price_dates_from', ''),
(317, 80, '_sale_price_dates_to', ''),
(318, 80, '_price', ''),
(319, 80, '_sold_individually', ''),
(320, 80, '_manage_stock', 'no'),
(321, 80, '_backorders', 'no'),
(322, 80, '_stock', ''),
(323, 80, '_upsell_ids', 'a:0:{}'),
(324, 80, '_crosssell_ids', 'a:0:{}'),
(325, 80, '_product_version', '2.6.4'),
(326, 80, '_product_image_gallery', ''),
(327, 81, '_edit_last', '1'),
(328, 81, '_edit_lock', '1474301322:1'),
(329, 81, '_visibility', 'visible'),
(330, 81, '_stock_status', 'instock'),
(331, 81, 'total_sales', '0'),
(332, 81, '_downloadable', 'no'),
(333, 81, '_virtual', 'no'),
(334, 81, '_tax_status', 'taxable'),
(335, 81, '_tax_class', ''),
(336, 81, '_purchase_note', ''),
(337, 81, '_featured', 'no'),
(338, 81, '_weight', ''),
(339, 81, '_length', ''),
(340, 81, '_width', ''),
(341, 81, '_height', ''),
(342, 81, '_sku', ''),
(343, 81, '_product_attributes', 'a:0:{}'),
(344, 81, '_regular_price', ''),
(345, 81, '_sale_price', ''),
(346, 81, '_sale_price_dates_from', ''),
(347, 81, '_sale_price_dates_to', ''),
(348, 81, '_price', ''),
(349, 81, '_sold_individually', ''),
(350, 81, '_manage_stock', 'no'),
(351, 81, '_backorders', 'no'),
(352, 81, '_stock', ''),
(353, 81, '_upsell_ids', 'a:0:{}'),
(354, 81, '_crosssell_ids', 'a:0:{}'),
(355, 81, '_product_version', '2.6.4'),
(356, 81, '_product_image_gallery', ''),
(357, 40, 'hero_banner', '1') ;
INSERT INTO `aze_postmeta` ( `meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(358, 40, '_hero_banner', 'field_57d722a984db3'),
(359, 40, 'panel_background_image', '84'),
(360, 40, '_panel_background_image', 'field_57d72351a1be1'),
(361, 40, 'panel_title', 'Send a file'),
(362, 40, '_panel_title', 'field_57d7235ea1be2'),
(363, 40, 'panel_text', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor viverra sollicitudin. Nam quis massa nec quam tempus tristique. Ut molestie egestas aliquet. Nunc non orci rutrum, vehicula sapien nec, vulputate erat. Aliquam lacinia magna vel lobortis blandit. Suspendisse potenti. Ut laoreet urna eu ipsum elementum, vel rutrum sapien feugiat. Fusce urna ex, dictum vitae libero sed, pulvinar commodo nunc. Aliquam non justo vitae metus luctus accumsan eu et lorem.'),
(364, 40, '_panel_text', 'field_57d72363a1be3'),
(365, 40, 'panel_button_label', 'Submit'),
(366, 40, '_panel_button_label', 'field_57d72378a1be4'),
(367, 40, 'panel_button_url', '#'),
(368, 40, '_panel_button_url', 'field_57d7237fa1be5'),
(369, 40, 'contact_us_title', 'Contact Us'),
(370, 40, '_contact_us_title', 'field_57d723ebd36aa'),
(371, 40, 'contact_address_information', '7248 S. Harl Avenue <br> Tempe, AZ 85283'),
(372, 40, '_contact_address_information', 'field_57d723f7d36ab'),
(373, 40, 'contact_us_form_title', 'Do you have a question, comment or suggestion to pass along to us?'),
(374, 40, '_contact_us_form_title', 'field_57d7241338658'),
(375, 40, 'contact_form_shortcode', '[gravityform id=1]'),
(376, 40, '_contact_form_shortcode', 'field_57d7243138659'),
(397, 40, 'hero_banner_0_background_image', '94'),
(398, 40, '_hero_banner_0_background_image', 'field_57d722c184db4'),
(399, 40, 'hero_banner_0_hero_title', 'Your source for envelope manufacturing & printing'),
(400, 40, '_hero_banner_0_hero_title', 'field_57d722d184db5'),
(401, 40, 'hero_banner_0_hero_subtitle', 'Our goal is to meet the expedient and exacting demands of our customers'),
(402, 40, '_hero_banner_0_hero_subtitle', 'field_57d722e184db6'),
(403, 40, 'hero_banner_0_hero_buttons_0_button_label', 'Shop Now'),
(404, 40, '_hero_banner_0_hero_buttons_0_button_label', 'field_57d72305f3c70'),
(405, 40, 'hero_banner_0_hero_buttons_0_button_url', '#'),
(406, 40, '_hero_banner_0_hero_buttons_0_button_url', 'field_57d7230bf3c71'),
(407, 40, 'hero_banner_0_hero_buttons_0_color', 'orange'),
(408, 40, '_hero_banner_0_hero_buttons_0_color', 'field_57d723aee0d94'),
(409, 40, 'hero_banner_0_hero_buttons_1_button_label', 'Request a Quote'),
(410, 40, '_hero_banner_0_hero_buttons_1_button_label', 'field_57d72305f3c70'),
(411, 40, 'hero_banner_0_hero_buttons_1_button_url', '#'),
(412, 40, '_hero_banner_0_hero_buttons_1_button_url', 'field_57d7230bf3c71'),
(413, 40, 'hero_banner_0_hero_buttons_1_color', 'transparent'),
(414, 40, '_hero_banner_0_hero_buttons_1_color', 'field_57d723aee0d94'),
(415, 40, 'hero_banner_0_hero_buttons', '2'),
(416, 40, '_hero_banner_0_hero_buttons', 'field_57d722eef3c6f'),
(457, 84, '_wp_attached_file', '2016/09/submit-file-background-image.jpg'),
(458, 84, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:1200;s:6:"height";i:300;s:4:"file";s:40:"2016/09/submit-file-background-image.jpg";s:5:"sizes";a:7:{s:9:"thumbnail";a:4:{s:4:"file";s:40:"submit-file-background-image-150x150.jpg";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:10:"image/jpeg";}s:6:"medium";a:4:{s:4:"file";s:39:"submit-file-background-image-300x75.jpg";s:5:"width";i:300;s:6:"height";i:75;s:9:"mime-type";s:10:"image/jpeg";}s:12:"medium_large";a:4:{s:4:"file";s:40:"submit-file-background-image-768x192.jpg";s:5:"width";i:768;s:6:"height";i:192;s:9:"mime-type";s:10:"image/jpeg";}s:5:"large";a:4:{s:4:"file";s:41:"submit-file-background-image-1024x256.jpg";s:5:"width";i:1024;s:6:"height";i:256;s:9:"mime-type";s:10:"image/jpeg";}s:14:"shop_thumbnail";a:4:{s:4:"file";s:40:"submit-file-background-image-180x180.jpg";s:5:"width";i:180;s:6:"height";i:180;s:9:"mime-type";s:10:"image/jpeg";}s:12:"shop_catalog";a:4:{s:4:"file";s:40:"submit-file-background-image-300x300.jpg";s:5:"width";i:300;s:6:"height";i:300;s:9:"mime-type";s:10:"image/jpeg";}s:11:"shop_single";a:4:{s:4:"file";s:40:"submit-file-background-image-600x300.jpg";s:5:"width";i:600;s:6:"height";i:300;s:9:"mime-type";s:10:"image/jpeg";}}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"1";s:8:"keywords";a:0:{}}}'),
(683, 94, '_wp_attached_file', '2016/09/header-hero-placeholder.jpg'),
(684, 94, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:1180;s:6:"height";i:641;s:4:"file";s:35:"2016/09/header-hero-placeholder.jpg";s:5:"sizes";a:7:{s:9:"thumbnail";a:4:{s:4:"file";s:35:"header-hero-placeholder-150x150.jpg";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:10:"image/jpeg";}s:6:"medium";a:4:{s:4:"file";s:35:"header-hero-placeholder-300x163.jpg";s:5:"width";i:300;s:6:"height";i:163;s:9:"mime-type";s:10:"image/jpeg";}s:12:"medium_large";a:4:{s:4:"file";s:35:"header-hero-placeholder-768x417.jpg";s:5:"width";i:768;s:6:"height";i:417;s:9:"mime-type";s:10:"image/jpeg";}s:5:"large";a:4:{s:4:"file";s:36:"header-hero-placeholder-1024x556.jpg";s:5:"width";i:1024;s:6:"height";i:556;s:9:"mime-type";s:10:"image/jpeg";}s:14:"shop_thumbnail";a:4:{s:4:"file";s:35:"header-hero-placeholder-180x180.jpg";s:5:"width";i:180;s:6:"height";i:180;s:9:"mime-type";s:10:"image/jpeg";}s:12:"shop_catalog";a:4:{s:4:"file";s:35:"header-hero-placeholder-300x300.jpg";s:5:"width";i:300;s:6:"height";i:300;s:9:"mime-type";s:10:"image/jpeg";}s:11:"shop_single";a:4:{s:4:"file";s:35:"header-hero-placeholder-600x600.jpg";s:5:"width";i:600;s:6:"height";i:600;s:9:"mime-type";s:10:"image/jpeg";}}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"1";s:8:"keywords";a:0:{}}}'),
(725, 96, '_menu_item_type', 'post_type'),
(726, 96, '_menu_item_menu_item_parent', '0'),
(727, 96, '_menu_item_object_id', '74'),
(728, 96, '_menu_item_object', 'page'),
(729, 96, '_menu_item_target', ''),
(730, 96, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(731, 96, '_menu_item_xfn', ''),
(732, 96, '_menu_item_url', ''),
(733, 96, '_menu_item_orphaned', '1474396466'),
(734, 97, '_menu_item_type', 'post_type'),
(735, 97, '_menu_item_menu_item_parent', '0'),
(736, 97, '_menu_item_object_id', '72'),
(737, 97, '_menu_item_object', 'page'),
(738, 97, '_menu_item_target', ''),
(739, 97, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(740, 97, '_menu_item_xfn', ''),
(741, 97, '_menu_item_url', ''),
(742, 97, '_menu_item_orphaned', '1474396466'),
(743, 98, '_menu_item_type', 'post_type'),
(744, 98, '_menu_item_menu_item_parent', '0'),
(745, 98, '_menu_item_object_id', '4'),
(746, 98, '_menu_item_object', 'page'),
(747, 98, '_menu_item_target', ''),
(748, 98, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(749, 98, '_menu_item_xfn', ''),
(750, 98, '_menu_item_url', ''),
(752, 99, '_menu_item_type', 'post_type'),
(753, 99, '_menu_item_menu_item_parent', '0'),
(754, 99, '_menu_item_object_id', '49'),
(755, 99, '_menu_item_object', 'page'),
(756, 99, '_menu_item_target', ''),
(757, 99, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(758, 99, '_menu_item_xfn', ''),
(759, 99, '_menu_item_url', ''),
(761, 100, '_menu_item_type', 'custom'),
(762, 100, '_menu_item_menu_item_parent', '0'),
(763, 100, '_menu_item_object_id', '100'),
(764, 100, '_menu_item_object', 'custom'),
(765, 100, '_menu_item_target', ''),
(766, 100, '_menu_item_classes', 'a:1:{i:0;s:15:"transparent-btn";}'),
(767, 100, '_menu_item_xfn', ''),
(768, 100, '_menu_item_url', '#'),
(810, 40, 'contact_us_subtitle', 'Arizona Envelope Company'),
(811, 40, '_contact_us_subtitle', 'field_57e1d32d4effb'),
(812, 103, 'hero_banner', '1'),
(813, 103, '_hero_banner', 'field_57d722a984db3'),
(814, 103, 'panel_background_image', '84'),
(815, 103, '_panel_background_image', 'field_57d72351a1be1'),
(816, 103, 'panel_title', 'Send a file'),
(817, 103, '_panel_title', 'field_57d7235ea1be2'),
(818, 103, 'panel_text', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor viverra sollicitudin. Nam quis massa nec quam tempus tristique. Ut molestie egestas aliquet. Nunc non orci rutrum, vehicula sapien nec, vulputate erat. Aliquam lacinia magna vel lobortis blandit. Suspendisse potenti. Ut laoreet urna eu ipsum elementum, vel rutrum sapien feugiat. Fusce urna ex, dictum vitae libero sed, pulvinar commodo nunc. Aliquam non justo vitae metus luctus accumsan eu et lorem.'),
(819, 103, '_panel_text', 'field_57d72363a1be3'),
(820, 103, 'panel_button_label', 'Submit'),
(821, 103, '_panel_button_label', 'field_57d72378a1be4'),
(822, 103, 'panel_button_url', '#'),
(823, 103, '_panel_button_url', 'field_57d7237fa1be5'),
(824, 103, 'contact_us_title', 'Contact Us') ;
INSERT INTO `aze_postmeta` ( `meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(825, 103, '_contact_us_title', 'field_57d723ebd36aa'),
(826, 103, 'contact_address_information', '7248 S. Harl Avenue <br> Tempe, AZ 85283'),
(827, 103, '_contact_address_information', 'field_57d723f7d36ab'),
(828, 103, 'contact_us_form_title', 'Do you have a question, comment or suggestion to pass along to us?\r\nPlease send us a message!'),
(829, 103, '_contact_us_form_title', 'field_57d7241338658'),
(830, 103, 'contact_form_shortcode', ''),
(831, 103, '_contact_form_shortcode', 'field_57d7243138659'),
(832, 103, 'hero_banner_0_background_image', '94'),
(833, 103, '_hero_banner_0_background_image', 'field_57d722c184db4'),
(834, 103, 'hero_banner_0_hero_title', 'Your source for envelope manufacturing & printing'),
(835, 103, '_hero_banner_0_hero_title', 'field_57d722d184db5'),
(836, 103, 'hero_banner_0_hero_subtitle', 'Our goal is to meet the expedient and exacting demands of our customers'),
(837, 103, '_hero_banner_0_hero_subtitle', 'field_57d722e184db6'),
(838, 103, 'hero_banner_0_hero_buttons_0_button_label', 'Shop Now'),
(839, 103, '_hero_banner_0_hero_buttons_0_button_label', 'field_57d72305f3c70'),
(840, 103, 'hero_banner_0_hero_buttons_0_button_url', '#'),
(841, 103, '_hero_banner_0_hero_buttons_0_button_url', 'field_57d7230bf3c71'),
(842, 103, 'hero_banner_0_hero_buttons_0_color', 'orange'),
(843, 103, '_hero_banner_0_hero_buttons_0_color', 'field_57d723aee0d94'),
(844, 103, 'hero_banner_0_hero_buttons_1_button_label', 'Request a Quote'),
(845, 103, '_hero_banner_0_hero_buttons_1_button_label', 'field_57d72305f3c70'),
(846, 103, 'hero_banner_0_hero_buttons_1_button_url', '#'),
(847, 103, '_hero_banner_0_hero_buttons_1_button_url', 'field_57d7230bf3c71'),
(848, 103, 'hero_banner_0_hero_buttons_1_color', 'transparent'),
(849, 103, '_hero_banner_0_hero_buttons_1_color', 'field_57d723aee0d94'),
(850, 103, 'hero_banner_0_hero_buttons', '2'),
(851, 103, '_hero_banner_0_hero_buttons', 'field_57d722eef3c6f'),
(852, 103, 'contact_us_subtitle', 'Arizona Envelope Company'),
(853, 103, '_contact_us_subtitle', 'field_57e1d32d4effb'),
(854, 104, 'hero_banner', '1'),
(855, 104, '_hero_banner', 'field_57d722a984db3'),
(856, 104, 'panel_background_image', '84'),
(857, 104, '_panel_background_image', 'field_57d72351a1be1'),
(858, 104, 'panel_title', 'Send a file'),
(859, 104, '_panel_title', 'field_57d7235ea1be2'),
(860, 104, 'panel_text', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor viverra sollicitudin. Nam quis massa nec quam tempus tristique. Ut molestie egestas aliquet. Nunc non orci rutrum, vehicula sapien nec, vulputate erat. Aliquam lacinia magna vel lobortis blandit. Suspendisse potenti. Ut laoreet urna eu ipsum elementum, vel rutrum sapien feugiat. Fusce urna ex, dictum vitae libero sed, pulvinar commodo nunc. Aliquam non justo vitae metus luctus accumsan eu et lorem.'),
(861, 104, '_panel_text', 'field_57d72363a1be3'),
(862, 104, 'panel_button_label', 'Submit'),
(863, 104, '_panel_button_label', 'field_57d72378a1be4'),
(864, 104, 'panel_button_url', '#'),
(865, 104, '_panel_button_url', 'field_57d7237fa1be5'),
(866, 104, 'contact_us_title', 'Contact Us'),
(867, 104, '_contact_us_title', 'field_57d723ebd36aa'),
(868, 104, 'contact_address_information', '7248 S. Harl Avenue <br> Tempe, AZ 85283'),
(869, 104, '_contact_address_information', 'field_57d723f7d36ab'),
(870, 104, 'contact_us_form_title', 'Do you have a question, comment or suggestion to pass along to us?\r\nPlease send us a message!'),
(871, 104, '_contact_us_form_title', 'field_57d7241338658'),
(872, 104, 'contact_form_shortcode', '[gravityform id=1 title=false description=false ajax=true tabindex=49]'),
(873, 104, '_contact_form_shortcode', 'field_57d7243138659'),
(874, 104, 'hero_banner_0_background_image', '94'),
(875, 104, '_hero_banner_0_background_image', 'field_57d722c184db4'),
(876, 104, 'hero_banner_0_hero_title', 'Your source for envelope manufacturing & printing'),
(877, 104, '_hero_banner_0_hero_title', 'field_57d722d184db5'),
(878, 104, 'hero_banner_0_hero_subtitle', 'Our goal is to meet the expedient and exacting demands of our customers'),
(879, 104, '_hero_banner_0_hero_subtitle', 'field_57d722e184db6'),
(880, 104, 'hero_banner_0_hero_buttons_0_button_label', 'Shop Now'),
(881, 104, '_hero_banner_0_hero_buttons_0_button_label', 'field_57d72305f3c70'),
(882, 104, 'hero_banner_0_hero_buttons_0_button_url', '#'),
(883, 104, '_hero_banner_0_hero_buttons_0_button_url', 'field_57d7230bf3c71'),
(884, 104, 'hero_banner_0_hero_buttons_0_color', 'orange'),
(885, 104, '_hero_banner_0_hero_buttons_0_color', 'field_57d723aee0d94'),
(886, 104, 'hero_banner_0_hero_buttons_1_button_label', 'Request a Quote'),
(887, 104, '_hero_banner_0_hero_buttons_1_button_label', 'field_57d72305f3c70'),
(888, 104, 'hero_banner_0_hero_buttons_1_button_url', '#'),
(889, 104, '_hero_banner_0_hero_buttons_1_button_url', 'field_57d7230bf3c71'),
(890, 104, 'hero_banner_0_hero_buttons_1_color', 'transparent'),
(891, 104, '_hero_banner_0_hero_buttons_1_color', 'field_57d723aee0d94'),
(892, 104, 'hero_banner_0_hero_buttons', '2'),
(893, 104, '_hero_banner_0_hero_buttons', 'field_57d722eef3c6f'),
(894, 104, 'contact_us_subtitle', 'Arizona Envelope Company'),
(895, 104, '_contact_us_subtitle', 'field_57e1d32d4effb'),
(896, 105, 'hero_banner', '1'),
(897, 105, '_hero_banner', 'field_57d722a984db3'),
(898, 105, 'panel_background_image', '84'),
(899, 105, '_panel_background_image', 'field_57d72351a1be1'),
(900, 105, 'panel_title', 'Send a file'),
(901, 105, '_panel_title', 'field_57d7235ea1be2'),
(902, 105, 'panel_text', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor viverra sollicitudin. Nam quis massa nec quam tempus tristique. Ut molestie egestas aliquet. Nunc non orci rutrum, vehicula sapien nec, vulputate erat. Aliquam lacinia magna vel lobortis blandit. Suspendisse potenti. Ut laoreet urna eu ipsum elementum, vel rutrum sapien feugiat. Fusce urna ex, dictum vitae libero sed, pulvinar commodo nunc. Aliquam non justo vitae metus luctus accumsan eu et lorem.'),
(903, 105, '_panel_text', 'field_57d72363a1be3'),
(904, 105, 'panel_button_label', 'Submit'),
(905, 105, '_panel_button_label', 'field_57d72378a1be4'),
(906, 105, 'panel_button_url', '#'),
(907, 105, '_panel_button_url', 'field_57d7237fa1be5'),
(908, 105, 'contact_us_title', 'Contact Us'),
(909, 105, '_contact_us_title', 'field_57d723ebd36aa'),
(910, 105, 'contact_address_information', '7248 S. Harl Avenue <br> Tempe, AZ 85283'),
(911, 105, '_contact_address_information', 'field_57d723f7d36ab'),
(912, 105, 'contact_us_form_title', 'Do you have a question, comment or suggestion to pass along to us?\r\nPlease send us a message!'),
(913, 105, '_contact_us_form_title', 'field_57d7241338658'),
(914, 105, 'contact_form_shortcode', '[gravityform id=1]'),
(915, 105, '_contact_form_shortcode', 'field_57d7243138659'),
(916, 105, 'hero_banner_0_background_image', '94'),
(917, 105, '_hero_banner_0_background_image', 'field_57d722c184db4'),
(918, 105, 'hero_banner_0_hero_title', 'Your source for envelope manufacturing & printing'),
(919, 105, '_hero_banner_0_hero_title', 'field_57d722d184db5'),
(920, 105, 'hero_banner_0_hero_subtitle', 'Our goal is to meet the expedient and exacting demands of our customers'),
(921, 105, '_hero_banner_0_hero_subtitle', 'field_57d722e184db6'),
(922, 105, 'hero_banner_0_hero_buttons_0_button_label', 'Shop Now'),
(923, 105, '_hero_banner_0_hero_buttons_0_button_label', 'field_57d72305f3c70'),
(924, 105, 'hero_banner_0_hero_buttons_0_button_url', '#') ;
INSERT INTO `aze_postmeta` ( `meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(925, 105, '_hero_banner_0_hero_buttons_0_button_url', 'field_57d7230bf3c71'),
(926, 105, 'hero_banner_0_hero_buttons_0_color', 'orange'),
(927, 105, '_hero_banner_0_hero_buttons_0_color', 'field_57d723aee0d94'),
(928, 105, 'hero_banner_0_hero_buttons_1_button_label', 'Request a Quote'),
(929, 105, '_hero_banner_0_hero_buttons_1_button_label', 'field_57d72305f3c70'),
(930, 105, 'hero_banner_0_hero_buttons_1_button_url', '#'),
(931, 105, '_hero_banner_0_hero_buttons_1_button_url', 'field_57d7230bf3c71'),
(932, 105, 'hero_banner_0_hero_buttons_1_color', 'transparent'),
(933, 105, '_hero_banner_0_hero_buttons_1_color', 'field_57d723aee0d94'),
(934, 105, 'hero_banner_0_hero_buttons', '2'),
(935, 105, '_hero_banner_0_hero_buttons', 'field_57d722eef3c6f'),
(936, 105, 'contact_us_subtitle', 'Arizona Envelope Company'),
(937, 105, '_contact_us_subtitle', 'field_57e1d32d4effb'),
(938, 110, '_wp_attached_file', '2016/09/most-popular-products-image.png'),
(939, 110, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:279;s:6:"height";i:200;s:4:"file";s:39:"2016/09/most-popular-products-image.png";s:5:"sizes";a:2:{s:9:"thumbnail";a:4:{s:4:"file";s:39:"most-popular-products-image-150x150.png";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:9:"image/png";}s:14:"shop_thumbnail";a:4:{s:4:"file";s:39:"most-popular-products-image-180x180.png";s:5:"width";i:180;s:6:"height";i:180;s:9:"mime-type";s:9:"image/png";}}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(940, 111, '_wp_attached_file', '2016/09/number-9-10-envelopes-image.png'),
(941, 111, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:279;s:6:"height";i:200;s:4:"file";s:39:"2016/09/number-9-10-envelopes-image.png";s:5:"sizes";a:2:{s:9:"thumbnail";a:4:{s:4:"file";s:39:"number-9-10-envelopes-image-150x150.png";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:9:"image/png";}s:14:"shop_thumbnail";a:4:{s:4:"file";s:39:"number-9-10-envelopes-image-180x180.png";s:5:"width";i:180;s:6:"height";i:180;s:9:"mime-type";s:9:"image/png";}}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(942, 112, '_wp_attached_file', '2016/09/open-end-catalog-image.png'),
(943, 112, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:279;s:6:"height";i:200;s:4:"file";s:34:"2016/09/open-end-catalog-image.png";s:5:"sizes";a:2:{s:9:"thumbnail";a:4:{s:4:"file";s:34:"open-end-catalog-image-150x150.png";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:9:"image/png";}s:14:"shop_thumbnail";a:4:{s:4:"file";s:34:"open-end-catalog-image-180x180.png";s:5:"width";i:180;s:6:"height";i:180;s:9:"mime-type";s:9:"image/png";}}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(944, 113, '_wp_attached_file', '2016/09/open-side-booklet-image.png'),
(945, 113, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:279;s:6:"height";i:200;s:4:"file";s:35:"2016/09/open-side-booklet-image.png";s:5:"sizes";a:2:{s:9:"thumbnail";a:4:{s:4:"file";s:35:"open-side-booklet-image-150x150.png";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:9:"image/png";}s:14:"shop_thumbnail";a:4:{s:4:"file";s:35:"open-side-booklet-image-180x180.png";s:5:"width";i:180;s:6:"height";i:180;s:9:"mime-type";s:9:"image/png";}}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(946, 114, '_wp_attached_file', '2016/09/other-commercial-envelopes-image.png'),
(947, 114, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:279;s:6:"height";i:200;s:4:"file";s:44:"2016/09/other-commercial-envelopes-image.png";s:5:"sizes";a:2:{s:9:"thumbnail";a:4:{s:4:"file";s:44:"other-commercial-envelopes-image-150x150.png";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:9:"image/png";}s:14:"shop_thumbnail";a:4:{s:4:"file";s:44:"other-commercial-envelopes-image-180x180.png";s:5:"width";i:180;s:6:"height";i:180;s:9:"mime-type";s:9:"image/png";}}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(948, 115, '_wp_attached_file', '2016/09/speciality-envelopes-image.png'),
(949, 115, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:279;s:6:"height";i:200;s:4:"file";s:38:"2016/09/speciality-envelopes-image.png";s:5:"sizes";a:2:{s:9:"thumbnail";a:4:{s:4:"file";s:38:"speciality-envelopes-image-150x150.png";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:9:"image/png";}s:14:"shop_thumbnail";a:4:{s:4:"file";s:38:"speciality-envelopes-image-180x180.png";s:5:"width";i:180;s:6:"height";i:180;s:9:"mime-type";s:9:"image/png";}}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(950, 40, 'products_section_title', ''),
(951, 40, '_products_section_title', 'field_57e2b06ef5158'),
(952, 40, 'contact_us_form_subtitle', 'Please send us a message!'),
(953, 40, '_contact_us_form_subtitle', 'field_57e2c28791b17'),
(954, 119, 'hero_banner', '1'),
(955, 119, '_hero_banner', 'field_57d722a984db3'),
(956, 119, 'panel_background_image', '84'),
(957, 119, '_panel_background_image', 'field_57d72351a1be1'),
(958, 119, 'panel_title', 'Send a file'),
(959, 119, '_panel_title', 'field_57d7235ea1be2'),
(960, 119, 'panel_text', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor viverra sollicitudin. Nam quis massa nec quam tempus tristique. Ut molestie egestas aliquet. Nunc non orci rutrum, vehicula sapien nec, vulputate erat. Aliquam lacinia magna vel lobortis blandit. Suspendisse potenti. Ut laoreet urna eu ipsum elementum, vel rutrum sapien feugiat. Fusce urna ex, dictum vitae libero sed, pulvinar commodo nunc. Aliquam non justo vitae metus luctus accumsan eu et lorem.'),
(961, 119, '_panel_text', 'field_57d72363a1be3'),
(962, 119, 'panel_button_label', 'Submit'),
(963, 119, '_panel_button_label', 'field_57d72378a1be4'),
(964, 119, 'panel_button_url', '#'),
(965, 119, '_panel_button_url', 'field_57d7237fa1be5'),
(966, 119, 'contact_us_title', 'Contact Us'),
(967, 119, '_contact_us_title', 'field_57d723ebd36aa'),
(968, 119, 'contact_address_information', '7248 S. Harl Avenue <br> Tempe, AZ 85283'),
(969, 119, '_contact_address_information', 'field_57d723f7d36ab'),
(970, 119, 'contact_us_form_title', 'Do you have a question, comment or suggestion to pass along to us?'),
(971, 119, '_contact_us_form_title', 'field_57d7241338658'),
(972, 119, 'contact_form_shortcode', '[gravityform id=1]'),
(973, 119, '_contact_form_shortcode', 'field_57d7243138659'),
(974, 119, 'hero_banner_0_background_image', '94'),
(975, 119, '_hero_banner_0_background_image', 'field_57d722c184db4'),
(976, 119, 'hero_banner_0_hero_title', 'Your source for envelope manufacturing & printing'),
(977, 119, '_hero_banner_0_hero_title', 'field_57d722d184db5'),
(978, 119, 'hero_banner_0_hero_subtitle', 'Our goal is to meet the expedient and exacting demands of our customers'),
(979, 119, '_hero_banner_0_hero_subtitle', 'field_57d722e184db6'),
(980, 119, 'hero_banner_0_hero_buttons_0_button_label', 'Shop Now'),
(981, 119, '_hero_banner_0_hero_buttons_0_button_label', 'field_57d72305f3c70'),
(982, 119, 'hero_banner_0_hero_buttons_0_button_url', '#'),
(983, 119, '_hero_banner_0_hero_buttons_0_button_url', 'field_57d7230bf3c71'),
(984, 119, 'hero_banner_0_hero_buttons_0_color', 'orange'),
(985, 119, '_hero_banner_0_hero_buttons_0_color', 'field_57d723aee0d94'),
(986, 119, 'hero_banner_0_hero_buttons_1_button_label', 'Request a Quote'),
(987, 119, '_hero_banner_0_hero_buttons_1_button_label', 'field_57d72305f3c70'),
(988, 119, 'hero_banner_0_hero_buttons_1_button_url', '#'),
(989, 119, '_hero_banner_0_hero_buttons_1_button_url', 'field_57d7230bf3c71'),
(990, 119, 'hero_banner_0_hero_buttons_1_color', 'transparent'),
(991, 119, '_hero_banner_0_hero_buttons_1_color', 'field_57d723aee0d94'),
(992, 119, 'hero_banner_0_hero_buttons', '2'),
(993, 119, '_hero_banner_0_hero_buttons', 'field_57d722eef3c6f'),
(994, 119, 'contact_us_subtitle', 'Arizona Envelope Company'),
(995, 119, '_contact_us_subtitle', 'field_57e1d32d4effb'),
(996, 119, 'products_section_title', ''),
(997, 119, '_products_section_title', 'field_57e2b06ef5158'),
(998, 119, 'contact_us_form_subtitle', 'Please send us a message!'),
(999, 119, '_contact_us_form_subtitle', 'field_57e2c28791b17'),
(1000, 40, 'hero_banner_0_first_hero_button_label', 'Shop Now'),
(1001, 40, '_hero_banner_0_first_hero_button_label', 'field_57e5798e01d6a'),
(1002, 40, 'hero_banner_0_first_hero_button_url', '/shop/'),
(1003, 40, '_hero_banner_0_first_hero_button_url', 'field_57e579bf01d6b'),
(1004, 40, 'hero_banner_0_second_hero_button_label', 'Request a quote'),
(1005, 40, '_hero_banner_0_second_hero_button_label', 'field_57e579c901d6c'),
(1006, 40, 'hero_banner_0_second_hero_button_url', '/contact/'),
(1007, 40, '_hero_banner_0_second_hero_button_url', 'field_57e579d001d6d'),
(1008, 125, 'hero_banner', '1'),
(1009, 125, '_hero_banner', 'field_57d722a984db3'),
(1010, 125, 'panel_background_image', '84'),
(1011, 125, '_panel_background_image', 'field_57d72351a1be1'),
(1012, 125, 'panel_title', 'Send a file'),
(1013, 125, '_panel_title', 'field_57d7235ea1be2'),
(1014, 125, 'panel_text', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean auctor viverra sollicitudin. Nam quis massa nec quam tempus tristique. Ut molestie egestas aliquet. Nunc non orci rutrum, vehicula sapien nec, vulputate erat. Aliquam lacinia magna vel lobortis blandit. Suspendisse potenti. Ut laoreet urna eu ipsum elementum, vel rutrum sapien feugiat. Fusce urna ex, dictum vitae libero sed, pulvinar commodo nunc. Aliquam non justo vitae metus luctus accumsan eu et lorem.'),
(1015, 125, '_panel_text', 'field_57d72363a1be3'),
(1016, 125, 'panel_button_label', 'Submit'),
(1017, 125, '_panel_button_label', 'field_57d72378a1be4'),
(1018, 125, 'panel_button_url', '#'),
(1019, 125, '_panel_button_url', 'field_57d7237fa1be5'),
(1020, 125, 'contact_us_title', 'Contact Us'),
(1021, 125, '_contact_us_title', 'field_57d723ebd36aa'),
(1022, 125, 'contact_address_information', '7248 S. Harl Avenue <br> Tempe, AZ 85283'),
(1023, 125, '_contact_address_information', 'field_57d723f7d36ab'),
(1024, 125, 'contact_us_form_title', 'Do you have a question, comment or suggestion to pass along to us?') ;
INSERT INTO `aze_postmeta` ( `meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(1025, 125, '_contact_us_form_title', 'field_57d7241338658'),
(1026, 125, 'contact_form_shortcode', '[gravityform id=1]'),
(1027, 125, '_contact_form_shortcode', 'field_57d7243138659'),
(1028, 125, 'hero_banner_0_background_image', '94'),
(1029, 125, '_hero_banner_0_background_image', 'field_57d722c184db4'),
(1030, 125, 'hero_banner_0_hero_title', 'Your source for envelope manufacturing & printing'),
(1031, 125, '_hero_banner_0_hero_title', 'field_57d722d184db5'),
(1032, 125, 'hero_banner_0_hero_subtitle', 'Our goal is to meet the expedient and exacting demands of our customers'),
(1033, 125, '_hero_banner_0_hero_subtitle', 'field_57d722e184db6'),
(1034, 125, 'hero_banner_0_hero_buttons_0_button_label', 'Shop Now'),
(1035, 125, '_hero_banner_0_hero_buttons_0_button_label', 'field_57d72305f3c70'),
(1036, 125, 'hero_banner_0_hero_buttons_0_button_url', '#'),
(1037, 125, '_hero_banner_0_hero_buttons_0_button_url', 'field_57d7230bf3c71'),
(1038, 125, 'hero_banner_0_hero_buttons_0_color', 'orange'),
(1039, 125, '_hero_banner_0_hero_buttons_0_color', 'field_57d723aee0d94'),
(1040, 125, 'hero_banner_0_hero_buttons_1_button_label', 'Request a Quote'),
(1041, 125, '_hero_banner_0_hero_buttons_1_button_label', 'field_57d72305f3c70'),
(1042, 125, 'hero_banner_0_hero_buttons_1_button_url', '#'),
(1043, 125, '_hero_banner_0_hero_buttons_1_button_url', 'field_57d7230bf3c71'),
(1044, 125, 'hero_banner_0_hero_buttons_1_color', 'transparent'),
(1045, 125, '_hero_banner_0_hero_buttons_1_color', 'field_57d723aee0d94'),
(1046, 125, 'hero_banner_0_hero_buttons', '2'),
(1047, 125, '_hero_banner_0_hero_buttons', 'field_57d722eef3c6f'),
(1048, 125, 'contact_us_subtitle', 'Arizona Envelope Company'),
(1049, 125, '_contact_us_subtitle', 'field_57e1d32d4effb'),
(1050, 125, 'products_section_title', ''),
(1051, 125, '_products_section_title', 'field_57e2b06ef5158'),
(1052, 125, 'contact_us_form_subtitle', 'Please send us a message!'),
(1053, 125, '_contact_us_form_subtitle', 'field_57e2c28791b17'),
(1054, 125, 'hero_banner_0_first_hero_button_label', 'Shop Now'),
(1055, 125, '_hero_banner_0_first_hero_button_label', 'field_57e5798e01d6a'),
(1056, 125, 'hero_banner_0_first_hero_button_url', '/shop/'),
(1057, 125, '_hero_banner_0_first_hero_button_url', 'field_57e579bf01d6b'),
(1058, 125, 'hero_banner_0_second_hero_button_label', 'Request a quote'),
(1059, 125, '_hero_banner_0_second_hero_button_label', 'field_57e579c901d6c'),
(1060, 125, 'hero_banner_0_second_hero_button_url', '/contact/'),
(1061, 125, '_hero_banner_0_second_hero_button_url', 'field_57e579d001d6d'),
(1062, 126, '_wp_attached_file', '2016/09/arizona-envelope-logo-placeholder.png'),
(1063, 126, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:211;s:6:"height";i:110;s:4:"file";s:45:"2016/09/arizona-envelope-logo-placeholder.png";s:5:"sizes";a:2:{s:9:"thumbnail";a:4:{s:4:"file";s:45:"arizona-envelope-logo-placeholder-150x110.png";s:5:"width";i:150;s:6:"height";i:110;s:9:"mime-type";s:9:"image/png";}s:14:"shop_thumbnail";a:4:{s:4:"file";s:45:"arizona-envelope-logo-placeholder-180x110.png";s:5:"width";i:180;s:6:"height";i:110;s:9:"mime-type";s:9:"image/png";}}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(1064, 77, '_wc_rating_count', 'a:0:{}'),
(1065, 77, '_wc_average_rating', '0'),
(1066, 78, '_wc_rating_count', 'a:0:{}'),
(1067, 78, '_wc_average_rating', '0'),
(1068, 80, '_wc_rating_count', 'a:0:{}'),
(1069, 80, '_wc_average_rating', '0'),
(1070, 79, '_wc_rating_count', 'a:0:{}'),
(1071, 79, '_wc_average_rating', '0'),
(1072, 81, '_wc_rating_count', 'a:0:{}'),
(1073, 81, '_wc_average_rating', '0'),
(1074, 8, '_wc_rating_count', 'a:0:{}'),
(1075, 8, '_wc_average_rating', '0') ;

#
# End of data contents of table `aze_postmeta`
# --------------------------------------------------------



#
# Delete any existing table `aze_posts`
#

DROP TABLE IF EXISTS `aze_posts`;


#
# Table structure of table `aze_posts`
#

CREATE TABLE `aze_posts` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `post_title` text COLLATE utf8_unicode_ci NOT NULL,
  `post_excerpt` text COLLATE utf8_unicode_ci NOT NULL,
  `post_status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `post_password` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `post_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `to_ping` text COLLATE utf8_unicode_ci NOT NULL,
  `pinged` text COLLATE utf8_unicode_ci NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext COLLATE utf8_unicode_ci NOT NULL,
  `post_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT '0',
  `post_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `post_name` (`post_name`(191)),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_posts`
#
INSERT INTO `aze_posts` ( `ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES
(1, 1, '2016-09-12 21:29:17', '2016-09-12 21:29:17', 'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!', 'Hello world!', '', 'publish', 'open', 'open', '', 'hello-world', '', '', '2016-09-12 21:29:17', '2016-09-12 21:29:17', '', 0, 'http://azenvelope.loc/?p=1', 0, 'post', '', 1),
(2, 1, '2016-09-12 21:29:17', '2016-09-12 21:29:17', 'This is an example page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:\n\n<blockquote>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like pi&#241;a coladas. (And gettin\' caught in the rain.)</blockquote>\n\n...or something like this:\n\n<blockquote>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</blockquote>\n\nAs a new WordPress user, you should go to <a href="http://azenvelope.loc//wp-admin/">your dashboard</a> to delete this page and create new pages for your content. Have fun!', 'Sample Page', '', 'trash', 'closed', 'open', '', 'sample-page__trashed', '', '', '2016-09-12 21:55:32', '2016-09-12 21:55:32', '', 0, 'http://azenvelope.loc/?page_id=2', 0, 'page', '', 0),
(4, 1, '2016-09-12 21:33:41', '2016-09-12 21:33:41', '', 'Products', '', 'publish', 'closed', 'closed', '', 'products', '', '', '2016-09-12 21:55:58', '2016-09-12 21:55:58', '', 0, 'http://azenvelope.loc/shop/', 0, 'page', '', 0),
(5, 1, '2016-09-12 21:33:41', '2016-09-12 21:33:41', '[woocommerce_cart]', 'Cart', '', 'publish', 'closed', 'closed', '', 'cart', '', '', '2016-09-12 21:33:41', '2016-09-12 21:33:41', '', 0, 'http://azenvelope.loc/cart/', 0, 'page', '', 0),
(6, 1, '2016-09-12 21:33:41', '2016-09-12 21:33:41', '[woocommerce_checkout]', 'Checkout', '', 'publish', 'closed', 'closed', '', 'checkout', '', '', '2016-09-12 21:33:41', '2016-09-12 21:33:41', '', 0, 'http://azenvelope.loc/checkout/', 0, 'page', '', 0),
(7, 1, '2016-09-12 21:33:41', '2016-09-12 21:33:41', '[woocommerce_my_account]', 'My Account', '', 'publish', 'closed', 'closed', '', 'my-account', '', '', '2016-09-12 21:33:41', '2016-09-12 21:33:41', '', 0, 'http://azenvelope.loc/my-account/', 0, 'page', '', 0),
(8, 1, '2016-09-12 21:35:05', '2016-09-12 21:35:05', '', 'Test Product', '', 'publish', 'open', 'closed', '', 'test-product', '', '', '2016-09-12 21:35:05', '2016-09-12 21:35:05', '', 0, 'http://azenvelope.loc/?post_type=product&#038;p=8', 0, 'product', '', 0),
(9, 1, '2016-09-12 21:41:03', '2016-09-12 21:41:03', 'a:7:{s:8:"location";a:1:{i:0;a:1:{i:0;a:3:{s:5:"param";s:12:"options_page";s:8:"operator";s:2:"==";s:5:"value";s:22:"theme-general-settings";}}}s:8:"position";s:6:"normal";s:5:"style";s:7:"default";s:15:"label_placement";s:3:"top";s:21:"instruction_placement";s:5:"label";s:14:"hide_on_screen";s:0:"";s:11:"description";s:0:"";}', 'Site Settings', 'site-settings', 'publish', 'closed', 'closed', '', 'group_57d720e1ef760', '', '', '2016-09-21 07:26:21', '2016-09-21 07:26:21', '', 0, 'http://azenvelope.loc/?post_type=acf-field-group&#038;p=9', 0, 'acf-field-group', '', 0),
(10, 1, '2016-09-12 21:42:42', '2016-09-12 21:42:42', 'a:15:{s:4:"type";s:5:"image";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"return_format";s:3:"url";s:12:"preview_size";s:9:"thumbnail";s:7:"library";s:3:"all";s:9:"min_width";s:0:"";s:10:"min_height";s:0:"";s:8:"min_size";s:0:"";s:9:"max_width";s:0:"";s:10:"max_height";s:0:"";s:8:"max_size";s:0:"";s:10:"mime_types";s:0:"";}', 'Site Logo', 'site_logo', 'publish', 'closed', 'closed', '', 'field_57d7213d834ed', '', '', '2016-09-12 21:47:09', '2016-09-12 21:47:09', '', 9, 'http://azenvelope.loc/?post_type=acf-field&#038;p=10', 1, 'acf-field', '', 0),
(11, 1, '2016-09-12 21:42:42', '2016-09-12 21:42:42', 'a:15:{s:4:"type";s:5:"image";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"return_format";s:3:"url";s:12:"preview_size";s:6:"medium";s:7:"library";s:3:"all";s:9:"min_width";s:0:"";s:10:"min_height";s:0:"";s:8:"min_size";s:0:"";s:9:"max_width";s:0:"";s:10:"max_height";s:0:"";s:8:"max_size";s:0:"";s:10:"mime_types";s:0:"";}', 'Retina Site Logo', 'retina_site_logo', 'publish', 'closed', 'closed', '', 'field_57d72146834ee', '', '', '2016-09-12 21:47:09', '2016-09-12 21:47:09', '', 9, 'http://azenvelope.loc/?post_type=acf-field&#038;p=11', 2, 'acf-field', '', 0),
(12, 1, '2016-09-12 21:45:50', '2016-09-12 21:45:50', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Copyright Information', 'copyright_information', 'publish', 'closed', 'closed', '', 'field_57d72202d4357', '', '', '2016-09-21 07:20:41', '2016-09-21 07:20:41', '', 9, 'http://azenvelope.loc/?post_type=acf-field&#038;p=12', 9, 'acf-field', '', 0),
(13, 1, '2016-09-12 21:47:09', '2016-09-12 21:47:09', 'a:7:{s:4:"type";s:3:"tab";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:9:"placement";s:3:"top";s:8:"endpoint";i:0;}', 'General Site Elements', '', 'publish', 'closed', 'closed', '', 'field_57d7221a5b96f', '', '', '2016-09-12 21:47:09', '2016-09-12 21:47:09', '', 9, 'http://azenvelope.loc/?post_type=acf-field&p=13', 0, 'acf-field', '', 0),
(14, 1, '2016-09-12 21:47:09', '2016-09-12 21:47:09', 'a:7:{s:4:"type";s:3:"tab";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:9:"placement";s:3:"top";s:8:"endpoint";i:0;}', 'Contact Information', '', 'publish', 'closed', 'closed', '', 'field_57d7222f5b970', '', '', '2016-09-12 21:47:09', '2016-09-12 21:47:09', '', 9, 'http://azenvelope.loc/?post_type=acf-field&p=14', 3, 'acf-field', '', 0),
(15, 1, '2016-09-12 21:47:09', '2016-09-12 21:47:09', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Toll Free Phone', 'toll_free_phone', 'publish', 'closed', 'closed', '', 'field_57d7223b5b971', '', '', '2016-09-12 21:48:10', '2016-09-12 21:48:10', '', 9, 'http://azenvelope.loc/?post_type=acf-field&#038;p=15', 4, 'acf-field', '', 0),
(16, 1, '2016-09-12 21:47:09', '2016-09-12 21:47:09', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Regular Phone Number', 'regular_phone_number', 'publish', 'closed', 'closed', '', 'field_57d7224d5b972', '', '', '2016-09-12 21:48:10', '2016-09-12 21:48:10', '', 9, 'http://azenvelope.loc/?post_type=acf-field&#038;p=16', 5, 'acf-field', '', 0),
(17, 1, '2016-09-12 21:47:09', '2016-09-12 21:47:09', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Fax Number', 'fax_number', 'publish', 'closed', 'closed', '', 'field_57d722585b973', '', '', '2016-09-12 21:48:10', '2016-09-12 21:48:10', '', 9, 'http://azenvelope.loc/?post_type=acf-field&#038;p=17', 6, 'acf-field', '', 0),
(18, 1, '2016-09-12 21:49:29', '2016-09-12 21:49:29', 'a:7:{s:8:"location";a:1:{i:0;a:1:{i:0;a:3:{s:5:"param";s:13:"page_template";s:8:"operator";s:2:"==";s:5:"value";s:21:"template-homepage.php";}}}s:8:"position";s:6:"normal";s:5:"style";s:7:"default";s:15:"label_placement";s:3:"top";s:21:"instruction_placement";s:5:"label";s:14:"hide_on_screen";a:1:{i:0;s:11:"the_content";}s:11:"description";s:0:"";}', 'Home Page', 'home-page', 'publish', 'closed', 'closed', '', 'group_57d7229f96f1d', '', '', '2016-09-23 18:52:15', '2016-09-23 18:52:15', '', 0, 'http://azenvelope.loc/?post_type=acf-field-group&#038;p=18', 0, 'acf-field-group', '', 0),
(19, 1, '2016-09-12 21:49:29', '2016-09-12 21:49:29', 'a:10:{s:4:"type";s:8:"repeater";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:9:"collapsed";s:0:"";s:3:"min";s:0:"";s:3:"max";s:0:"";s:6:"layout";s:5:"block";s:12:"button_label";s:8:"Add Hero";}', 'Hero Banner', 'hero_banner', 'publish', 'closed', 'closed', '', 'field_57d722a984db3', '', '', '2016-09-12 21:52:06', '2016-09-12 21:52:06', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=19', 1, 'acf-field', '', 0),
(20, 1, '2016-09-12 21:49:29', '2016-09-12 21:49:29', 'a:15:{s:4:"type";s:5:"image";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"return_format";s:3:"url";s:12:"preview_size";s:6:"medium";s:7:"library";s:3:"all";s:9:"min_width";s:0:"";s:10:"min_height";s:0:"";s:8:"min_size";s:0:"";s:9:"max_width";s:0:"";s:10:"max_height";s:0:"";s:8:"max_size";s:0:"";s:10:"mime_types";s:0:"";}', 'Background Image', 'background_image', 'publish', 'closed', 'closed', '', 'field_57d722c184db4', '', '', '2016-09-12 21:49:29', '2016-09-12 21:49:29', '', 19, 'http://azenvelope.loc/?post_type=acf-field&p=20', 0, 'acf-field', '', 0),
(21, 1, '2016-09-12 21:49:29', '2016-09-12 21:49:29', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Hero Title', 'hero_title', 'publish', 'closed', 'closed', '', 'field_57d722d184db5', '', '', '2016-09-12 21:49:29', '2016-09-12 21:49:29', '', 19, 'http://azenvelope.loc/?post_type=acf-field&p=21', 1, 'acf-field', '', 0),
(22, 1, '2016-09-12 21:49:29', '2016-09-12 21:49:29', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Hero Subtitle', 'hero_subtitle', 'publish', 'closed', 'closed', '', 'field_57d722e184db6', '', '', '2016-09-12 21:49:29', '2016-09-12 21:49:29', '', 19, 'http://azenvelope.loc/?post_type=acf-field&p=22', 2, 'acf-field', '', 0),
(26, 1, '2016-09-12 21:52:06', '2016-09-12 21:52:06', 'a:7:{s:4:"type";s:3:"tab";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:9:"placement";s:3:"top";s:8:"endpoint";i:0;}', 'Hero Banner', '', 'publish', 'closed', 'closed', '', 'field_57d72323a1bdf', '', '', '2016-09-12 21:52:06', '2016-09-12 21:52:06', '', 18, 'http://azenvelope.loc/?post_type=acf-field&p=26', 0, 'acf-field', '', 0),
(27, 1, '2016-09-12 21:52:06', '2016-09-12 21:52:06', 'a:7:{s:4:"type";s:3:"tab";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:9:"placement";s:3:"top";s:8:"endpoint";i:0;}', 'Text with Background Panel', '', 'publish', 'closed', 'closed', '', 'field_57d72332a1be0', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=27', 4, 'acf-field', '', 0),
(28, 1, '2016-09-12 21:52:06', '2016-09-12 21:52:06', 'a:15:{s:4:"type";s:5:"image";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"return_format";s:3:"url";s:12:"preview_size";s:6:"medium";s:7:"library";s:3:"all";s:9:"min_width";s:0:"";s:10:"min_height";s:0:"";s:8:"min_size";s:0:"";s:9:"max_width";s:0:"";s:10:"max_height";s:0:"";s:8:"max_size";s:0:"";s:10:"mime_types";s:0:"";}', 'Panel Background Image', 'panel_background_image', 'publish', 'closed', 'closed', '', 'field_57d72351a1be1', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=28', 5, 'acf-field', '', 0),
(29, 1, '2016-09-12 21:52:06', '2016-09-12 21:52:06', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Panel Title', 'panel_title', 'publish', 'closed', 'closed', '', 'field_57d7235ea1be2', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=29', 6, 'acf-field', '', 0),
(30, 1, '2016-09-12 21:52:06', '2016-09-12 21:52:06', 'a:10:{s:4:"type";s:8:"textarea";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:9:"maxlength";s:0:"";s:4:"rows";s:0:"";s:9:"new_lines";s:0:"";}', 'Panel Text', 'panel_text', 'publish', 'closed', 'closed', '', 'field_57d72363a1be3', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=30', 7, 'acf-field', '', 0),
(31, 1, '2016-09-12 21:52:06', '2016-09-12 21:52:06', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Panel Button Label', 'panel_button_label', 'publish', 'closed', 'closed', '', 'field_57d72378a1be4', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=31', 8, 'acf-field', '', 0),
(32, 1, '2016-09-12 21:52:06', '2016-09-12 21:52:06', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Panel Button URL', 'panel_button_url', 'publish', 'closed', 'closed', '', 'field_57d7237fa1be5', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=32', 9, 'acf-field', '', 0),
(33, 1, '2016-09-12 21:52:26', '2016-09-12 21:52:26', 'a:7:{s:4:"type";s:3:"tab";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:9:"placement";s:3:"top";s:8:"endpoint";i:0;}', 'Contact Us Panel', '', 'publish', 'closed', 'closed', '', 'field_57d7238914b2c', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=33', 10, 'acf-field', '', 0),
(35, 1, '2016-09-12 21:54:15', '2016-09-12 21:54:15', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Contact Us Title', 'contact_us_title', 'publish', 'closed', 'closed', '', 'field_57d723ebd36aa', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=35', 11, 'acf-field', '', 0),
(36, 1, '2016-09-12 21:54:15', '2016-09-12 21:54:15', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Contact Address Information', 'contact_address_information', 'publish', 'closed', 'closed', '', 'field_57d723f7d36ab', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=36', 13, 'acf-field', '', 0),
(37, 1, '2016-09-12 21:55:10', '2016-09-12 21:55:10', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Contact Us Form Title', 'contact_us_form_title', 'publish', 'closed', 'closed', '', 'field_57d7241338658', '', '', '2016-09-21 17:25:47', '2016-09-21 17:25:47', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=37', 14, 'acf-field', '', 0),
(38, 1, '2016-09-12 21:55:10', '2016-09-12 21:55:10', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Contact Form Shortcode', 'contact_form_shortcode', 'publish', 'closed', 'closed', '', 'field_57d7243138659', '', '', '2016-09-21 17:25:47', '2016-09-21 17:25:47', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=38', 16, 'acf-field', '', 0),
(39, 1, '2016-09-12 21:55:32', '2016-09-12 21:55:32', 'This is an example page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:\n\n<blockquote>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like pi&#241;a coladas. (And gettin\' caught in the rain.)</blockquote>\n\n...or something like this:\n\n<blockquote>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</blockquote>\n\nAs a new WordPress user, you should go to <a href="http://azenvelope.loc//wp-admin/">your dashboard</a> to delete this page and create new pages for your content. Have fun!', 'Sample Page', '', 'inherit', 'closed', 'closed', '', '2-revision-v1', '', '', '2016-09-12 21:55:32', '2016-09-12 21:55:32', '', 2, 'http://azenvelope.loc/2016/09/12/2-revision-v1/', 0, 'revision', '', 0),
(40, 1, '2016-09-12 21:55:38', '2016-09-12 21:55:38', '', 'Home', '', 'publish', 'closed', 'closed', '', 'home', '', '', '2016-09-23 18:55:17', '2016-09-23 18:55:17', '', 0, 'http://azenvelope.loc/?page_id=40', 0, 'page', '', 0),
(42, 1, '2016-09-12 21:55:58', '2016-09-12 21:55:58', '', 'Products', '', 'inherit', 'closed', 'closed', '', '4-revision-v1', '', '', '2016-09-12 21:55:58', '2016-09-12 21:55:58', '', 4, 'http://azenvelope.loc/2016/09/12/4-revision-v1/', 0, 'revision', '', 0),
(43, 1, '2016-09-12 21:59:18', '2016-09-12 21:59:18', '', 'Samples', '', 'publish', 'closed', 'closed', '', 'samples', '', '', '2016-09-12 21:59:18', '2016-09-12 21:59:18', '', 0, 'http://azenvelope.loc/?page_id=43', 0, 'page', '', 0),
(44, 1, '2016-09-12 21:56:11', '2016-09-12 21:56:11', '', 'Samples', '', 'inherit', 'closed', 'closed', '', '43-revision-v1', '', '', '2016-09-12 21:56:11', '2016-09-12 21:56:11', '', 43, 'http://azenvelope.loc/2016/09/12/43-revision-v1/', 0, 'revision', '', 0),
(45, 1, '2016-09-12 21:56:22', '2016-09-12 21:56:22', '', 'Resources', '', 'publish', 'closed', 'closed', '', 'resources', '', '', '2016-09-12 21:56:22', '2016-09-12 21:56:22', '', 0, 'http://azenvelope.loc/?page_id=45', 0, 'page', '', 0),
(46, 1, '2016-09-12 21:56:22', '2016-09-12 21:56:22', '', 'Resources', '', 'inherit', 'closed', 'closed', '', '45-revision-v1', '', '', '2016-09-12 21:56:22', '2016-09-12 21:56:22', '', 45, 'http://azenvelope.loc/2016/09/12/45-revision-v1/', 0, 'revision', '', 0),
(47, 1, '2016-09-12 21:56:38', '2016-09-12 21:56:38', '', 'Company', '', 'publish', 'closed', 'closed', '', 'company', '', '', '2016-09-12 21:56:38', '2016-09-12 21:56:38', '', 0, 'http://azenvelope.loc/?page_id=47', 0, 'page', '', 0),
(48, 1, '2016-09-12 21:56:38', '2016-09-12 21:56:38', '', 'Company', '', 'inherit', 'closed', 'closed', '', '47-revision-v1', '', '', '2016-09-12 21:56:38', '2016-09-12 21:56:38', '', 47, 'http://azenvelope.loc/2016/09/12/47-revision-v1/', 0, 'revision', '', 0),
(49, 1, '2016-09-12 21:57:43', '2016-09-12 21:57:43', '', 'Contact Us', '', 'publish', 'closed', 'closed', '', 'contact-us', '', '', '2016-09-12 21:57:43', '2016-09-12 21:57:43', '', 0, 'http://azenvelope.loc/?page_id=49', 0, 'page', '', 0),
(50, 1, '2016-09-12 21:57:43', '2016-09-12 21:57:43', '', 'Contact Us', '', 'inherit', 'closed', 'closed', '', '49-revision-v1', '', '', '2016-09-12 21:57:43', '2016-09-12 21:57:43', '', 49, 'http://azenvelope.loc/2016/09/12/49-revision-v1/', 0, 'revision', '', 0),
(54, 1, '2016-09-12 21:58:26', '2016-09-12 21:58:26', ' ', '', '', 'publish', 'closed', 'closed', '', '54', '', '', '2016-09-12 21:59:37', '2016-09-12 21:59:37', '', 0, 'http://azenvelope.loc/?p=54', 4, 'nav_menu_item', '', 0),
(55, 1, '2016-09-12 21:58:26', '2016-09-12 21:58:26', ' ', '', '', 'publish', 'closed', 'closed', '', '55', '', '', '2016-09-12 21:59:37', '2016-09-12 21:59:37', '', 0, 'http://azenvelope.loc/?p=55', 5, 'nav_menu_item', '', 0),
(58, 1, '2016-09-12 21:58:26', '2016-09-12 21:58:26', ' ', '', '', 'publish', 'closed', 'closed', '', '58', '', '', '2016-09-12 21:59:37', '2016-09-12 21:59:37', '', 0, 'http://azenvelope.loc/?p=58', 1, 'nav_menu_item', '', 0),
(59, 1, '2016-09-12 21:58:26', '2016-09-12 21:58:26', ' ', '', '', 'publish', 'closed', 'closed', '', '59', '', '', '2016-09-12 21:59:37', '2016-09-12 21:59:37', '', 0, 'http://azenvelope.loc/?p=59', 3, 'nav_menu_item', '', 0),
(60, 1, '2016-09-12 21:59:37', '2016-09-12 21:59:37', ' ', '', '', 'publish', 'closed', 'closed', '', '60', '', '', '2016-09-12 21:59:37', '2016-09-12 21:59:37', '', 0, 'http://azenvelope.loc/?p=60', 2, 'nav_menu_item', '', 0),
(61, 1, '2016-09-12 22:03:14', '2016-09-12 22:03:14', ' ', '', '', 'publish', 'closed', 'closed', '', '61', '', '', '2016-09-19 23:05:59', '2016-09-19 23:05:59', '', 0, 'http://azenvelope.loc/?p=61', 4, 'nav_menu_item', '', 0),
(62, 1, '2016-09-12 22:03:14', '2016-09-12 22:03:14', '', '(800) 540-6883', '', 'publish', 'closed', 'closed', '', '800-540-6883', '', '', '2016-09-19 23:05:59', '2016-09-19 23:05:59', '', 0, 'http://azenvelope.loc/?p=62', 1, 'nav_menu_item', '', 0),
(63, 1, '2016-09-12 22:05:04', '2016-09-12 22:05:04', '[woocommerce_my_account]', 'Login', '', 'publish', 'closed', 'closed', '', 'login', '', '', '2016-09-12 22:05:40', '2016-09-12 22:05:40', '', 0, 'http://azenvelope.loc/?page_id=63', 0, 'page', '', 0),
(64, 1, '2016-09-12 22:05:05', '2016-09-12 22:05:05', '', 'Login', '', 'inherit', 'closed', 'closed', '', '63-revision-v1', '', '', '2016-09-12 22:05:05', '2016-09-12 22:05:05', '', 63, 'http://azenvelope.loc/2016/09/12/63-revision-v1/', 0, 'revision', '', 0),
(65, 1, '2016-09-12 22:05:15', '2016-09-12 22:05:15', '[woocommerce_my_account]', 'Create Account', '', 'publish', 'closed', 'closed', '', 'create-account', '', '', '2016-09-12 22:05:31', '2016-09-12 22:05:31', '', 0, 'http://azenvelope.loc/?page_id=65', 0, 'page', '', 0),
(66, 1, '2016-09-12 22:05:15', '2016-09-12 22:05:15', '', 'Create Account', '', 'inherit', 'closed', 'closed', '', '65-revision-v1', '', '', '2016-09-12 22:05:15', '2016-09-12 22:05:15', '', 65, 'http://azenvelope.loc/2016/09/12/65-revision-v1/', 0, 'revision', '', 0),
(67, 1, '2016-09-12 22:05:31', '2016-09-12 22:05:31', '[woocommerce_my_account]', 'Create Account', '', 'inherit', 'closed', 'closed', '', '65-revision-v1', '', '', '2016-09-12 22:05:31', '2016-09-12 22:05:31', '', 65, 'http://azenvelope.loc/2016/09/12/65-revision-v1/', 0, 'revision', '', 0),
(68, 1, '2016-09-12 22:05:40', '2016-09-12 22:05:40', '[woocommerce_my_account]', 'Login', '', 'inherit', 'closed', 'closed', '', '63-revision-v1', '', '', '2016-09-12 22:05:40', '2016-09-12 22:05:40', '', 63, 'http://azenvelope.loc/2016/09/12/63-revision-v1/', 0, 'revision', '', 0),
(69, 1, '2016-09-12 22:06:18', '2016-09-12 22:06:18', ' ', '', '', 'publish', 'closed', 'closed', '', '69', '', '', '2016-09-19 23:05:59', '2016-09-19 23:05:59', '', 0, 'http://azenvelope.loc/?p=69', 3, 'nav_menu_item', '', 0),
(70, 1, '2016-09-12 22:06:18', '2016-09-12 22:06:18', ' ', '', '', 'publish', 'closed', 'closed', '', '70', '', '', '2016-09-19 23:05:59', '2016-09-19 23:05:59', '', 0, 'http://azenvelope.loc/?p=70', 2, 'nav_menu_item', '', 0),
(71, 1, '2016-09-12 22:06:18', '2016-09-12 22:06:18', '', 'Request a Quote', '', 'publish', 'closed', 'closed', '', 'request-a-quote', '', '', '2016-09-19 23:05:59', '2016-09-19 23:05:59', '', 0, 'http://azenvelope.loc/?p=71', 5, 'nav_menu_item', '', 0),
(72, 1, '2016-09-12 22:06:47', '2016-09-12 22:06:47', '', 'Terms & Conditions', '', 'publish', 'closed', 'closed', '', 'terms-conditions', '', '', '2016-09-12 22:06:47', '2016-09-12 22:06:47', '', 0, 'http://azenvelope.loc/?page_id=72', 0, 'page', '', 0),
(73, 1, '2016-09-12 22:06:47', '2016-09-12 22:06:47', '', 'Terms & Conditions', '', 'inherit', 'closed', 'closed', '', '72-revision-v1', '', '', '2016-09-12 22:06:47', '2016-09-12 22:06:47', '', 72, 'http://azenvelope.loc/2016/09/12/72-revision-v1/', 0, 'revision', '', 0),
(74, 1, '2016-09-12 22:07:00', '2016-09-12 22:07:00', '', 'Privacy Policy', '', 'publish', 'closed', 'closed', '', 'privacy-policy', '', '', '2016-09-12 22:07:00', '2016-09-12 22:07:00', '', 0, 'http://azenvelope.loc/?page_id=74', 0, 'page', '', 0),
(75, 1, '2016-09-12 22:07:00', '2016-09-12 22:07:00', '', 'Privacy Policy', '', 'inherit', 'closed', 'closed', '', '74-revision-v1', '', '', '2016-09-12 22:07:00', '2016-09-12 22:07:00', '', 74, 'http://azenvelope.loc/2016/09/12/74-revision-v1/', 0, 'revision', '', 0),
(77, 1, '2016-09-19 16:07:43', '2016-09-19 16:07:43', '', 'Other test Product', '', 'publish', 'open', 'closed', '', 'other-test-product', '', '', '2016-09-19 16:07:43', '2016-09-19 16:07:43', '', 0, 'http://azenvelope.loc/?post_type=product&#038;p=77', 0, 'product', '', 0),
(78, 1, '2016-09-19 16:08:10', '2016-09-19 16:08:10', '', 'Commercial Envelope', '', 'publish', 'open', 'closed', '', 'commercial-envelope', '', '', '2016-09-19 16:08:10', '2016-09-19 16:08:10', '', 0, 'http://azenvelope.loc/?post_type=product&#038;p=78', 0, 'product', '', 0),
(79, 1, '2016-09-19 16:08:36', '2016-09-19 16:08:36', '', 'Open Side Booklet', '', 'publish', 'open', 'closed', '', 'open-side-booklet', '', '', '2016-09-19 16:08:36', '2016-09-19 16:08:36', '', 0, 'http://azenvelope.loc/?post_type=product&#038;p=79', 0, 'product', '', 0),
(80, 1, '2016-09-19 16:09:03', '2016-09-19 16:09:03', '', 'Open End Catalog', '', 'publish', 'open', 'closed', '', 'open-end-catalog', '', '', '2016-09-19 16:09:27', '2016-09-19 16:09:27', '', 0, 'http://azenvelope.loc/?post_type=product&#038;p=80', 0, 'product', '', 0),
(81, 1, '2016-09-19 16:10:05', '2016-09-19 16:10:05', '', 'Speciality Envelope', '', 'publish', 'open', 'closed', '', 'speciality-envelope', '', '', '2016-09-19 16:10:05', '2016-09-19 16:10:05', '', 0, 'http://azenvelope.loc/?post_type=product&#038;p=81', 0, 'product', '', 0),
(84, 1, '2016-09-19 18:06:54', '2016-09-19 18:06:54', '', 'submit-file-background-image', '', 'inherit', 'open', 'closed', '', 'submit-file-background-image', '', '', '2016-09-19 21:01:49', '2016-09-19 21:01:49', '', 40, 'http://azenvelope.loc/wp-content/uploads/2016/09/submit-file-background-image.jpg', 0, 'attachment', 'image/jpeg', 0),
(94, 1, '2016-09-19 23:03:15', '2016-09-19 23:03:15', '', 'header-hero-placeholder', '', 'inherit', 'open', 'closed', '', 'header-hero-placeholder', '', '', '2016-09-19 23:03:17', '2016-09-19 23:03:17', '', 40, 'http://azenvelope.loc/wp-content/uploads/2016/09/header-hero-placeholder.jpg', 0, 'attachment', 'image/jpeg', 0),
(96, 1, '2016-09-20 18:34:26', '0000-00-00 00:00:00', ' ', '', '', 'draft', 'closed', 'closed', '', '', '', '', '2016-09-20 18:34:26', '0000-00-00 00:00:00', '', 0, 'http://azenvelope.loc/?p=96', 1, 'nav_menu_item', '', 0),
(97, 1, '2016-09-20 18:34:26', '0000-00-00 00:00:00', ' ', '', '', 'draft', 'closed', 'closed', '', '', '', '', '2016-09-20 18:34:26', '0000-00-00 00:00:00', '', 0, 'http://azenvelope.loc/?p=97', 1, 'nav_menu_item', '', 0),
(98, 1, '2016-09-20 18:35:01', '2016-09-20 18:35:01', ' ', '', '', 'publish', 'closed', 'closed', '', '98', '', '', '2016-09-20 18:35:33', '2016-09-20 18:35:33', '', 0, 'http://azenvelope.loc/?p=98', 1, 'nav_menu_item', '', 0),
(99, 1, '2016-09-20 18:35:01', '2016-09-20 18:35:01', ' ', '', '', 'publish', 'closed', 'closed', '', '99', '', '', '2016-09-20 18:35:33', '2016-09-20 18:35:33', '', 0, 'http://azenvelope.loc/?p=99', 2, 'nav_menu_item', '', 0),
(100, 1, '2016-09-20 18:35:33', '2016-09-20 18:35:33', '', 'Request a quote', '', 'publish', 'closed', 'closed', '', 'request-a-quote-2', '', '', '2016-09-20 18:35:33', '2016-09-20 18:35:33', '', 0, 'http://azenvelope.loc/?p=100', 3, 'nav_menu_item', '', 0),
(102, 1, '2016-09-21 00:24:26', '2016-09-21 00:24:26', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Contact Us Subtitle', 'contact_us_subtitle', 'publish', 'closed', 'closed', '', 'field_57e1d32d4effb', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&#038;p=102', 12, 'acf-field', '', 0),
(103, 1, '2016-09-21 00:25:29', '2016-09-21 00:25:29', '', 'Home', '', 'inherit', 'closed', 'closed', '', '40-revision-v1', '', '', '2016-09-21 00:25:29', '2016-09-21 00:25:29', '', 40, 'http://azenvelope.loc/2016/09/21/40-revision-v1/', 0, 'revision', '', 0),
(104, 1, '2016-09-21 05:18:13', '2016-09-21 05:18:13', '', 'Home', '', 'inherit', 'closed', 'closed', '', '40-revision-v1', '', '', '2016-09-21 05:18:13', '2016-09-21 05:18:13', '', 40, 'http://azenvelope.loc/2016/09/21/40-revision-v1/', 0, 'revision', '', 0),
(105, 1, '2016-09-21 05:20:48', '2016-09-21 05:20:48', '', 'Home', '', 'inherit', 'closed', 'closed', '', '40-revision-v1', '', '', '2016-09-21 05:20:48', '2016-09-21 05:20:48', '', 40, 'http://azenvelope.loc/2016/09/21/40-revision-v1/', 0, 'revision', '', 0),
(106, 1, '2016-09-21 07:20:22', '2016-09-21 07:20:22', 'a:10:{s:4:"type";s:8:"repeater";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:9:"collapsed";s:0:"";s:3:"min";s:0:"";s:3:"max";s:0:"";s:6:"layout";s:5:"table";s:12:"button_label";s:20:"Add Information Link";}', 'Information Links', 'information_links', 'publish', 'closed', 'closed', '', 'field_57e2348f1879d', '', '', '2016-09-21 07:20:41', '2016-09-21 07:20:41', '', 9, 'http://azenvelope.loc/?post_type=acf-field&#038;p=106', 8, 'acf-field', '', 0),
(107, 1, '2016-09-21 07:20:22', '2016-09-21 07:20:22', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Button Label', 'button_label', 'publish', 'closed', 'closed', '', 'field_57e2349b1879e', '', '', '2016-09-21 07:20:22', '2016-09-21 07:20:22', '', 106, 'http://azenvelope.loc/?post_type=acf-field&p=107', 0, 'acf-field', '', 0),
(108, 1, '2016-09-21 07:20:22', '2016-09-21 07:20:22', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Button URL', 'button_url', 'publish', 'closed', 'closed', '', 'field_57e234a21879f', '', '', '2016-09-21 07:20:22', '2016-09-21 07:20:22', '', 106, 'http://azenvelope.loc/?post_type=acf-field&p=108', 1, 'acf-field', '', 0),
(109, 1, '2016-09-21 07:20:41', '2016-09-21 07:20:41', 'a:7:{s:4:"type";s:3:"tab";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:9:"placement";s:3:"top";s:8:"endpoint";i:0;}', 'Footer', '', 'publish', 'closed', 'closed', '', 'field_57e234b9a9518', '', '', '2016-09-21 07:20:41', '2016-09-21 07:20:41', '', 9, 'http://azenvelope.loc/?post_type=acf-field&p=109', 7, 'acf-field', '', 0),
(110, 1, '2016-09-21 08:01:06', '2016-09-21 08:01:06', '', 'most-popular-products-image', '', 'inherit', 'open', 'closed', '', 'most-popular-products-image', '', '', '2016-09-21 08:01:06', '2016-09-21 08:01:06', '', 0, 'http://azenvelope.loc/wp-content/uploads/2016/09/most-popular-products-image.png', 0, 'attachment', 'image/png', 0),
(111, 1, '2016-09-21 08:01:07', '2016-09-21 08:01:07', '', 'number-9-10-envelopes-image', '', 'inherit', 'open', 'closed', '', 'number-9-10-envelopes-image', '', '', '2016-09-21 08:01:07', '2016-09-21 08:01:07', '', 0, 'http://azenvelope.loc/wp-content/uploads/2016/09/number-9-10-envelopes-image.png', 0, 'attachment', 'image/png', 0),
(112, 1, '2016-09-21 08:01:08', '2016-09-21 08:01:08', '', 'open-end-catalog-image', '', 'inherit', 'open', 'closed', '', 'open-end-catalog-image', '', '', '2016-09-21 08:01:08', '2016-09-21 08:01:08', '', 0, 'http://azenvelope.loc/wp-content/uploads/2016/09/open-end-catalog-image.png', 0, 'attachment', 'image/png', 0),
(113, 1, '2016-09-21 08:01:08', '2016-09-21 08:01:08', '', 'open-side-booklet-image', '', 'inherit', 'open', 'closed', '', 'open-side-booklet-image', '', '', '2016-09-21 08:01:08', '2016-09-21 08:01:08', '', 0, 'http://azenvelope.loc/wp-content/uploads/2016/09/open-side-booklet-image.png', 0, 'attachment', 'image/png', 0),
(114, 1, '2016-09-21 08:01:09', '2016-09-21 08:01:09', '', 'other-commercial-envelopes-image', '', 'inherit', 'open', 'closed', '', 'other-commercial-envelopes-image', '', '', '2016-09-21 08:01:09', '2016-09-21 08:01:09', '', 0, 'http://azenvelope.loc/wp-content/uploads/2016/09/other-commercial-envelopes-image.png', 0, 'attachment', 'image/png', 0),
(115, 1, '2016-09-21 08:01:10', '2016-09-21 08:01:10', '', 'speciality-envelopes-image', '', 'inherit', 'open', 'closed', '', 'speciality-envelopes-image', '', '', '2016-09-21 08:01:10', '2016-09-21 08:01:10', '', 0, 'http://azenvelope.loc/wp-content/uploads/2016/09/speciality-envelopes-image.png', 0, 'attachment', 'image/png', 0),
(116, 1, '2016-09-21 16:49:21', '2016-09-21 16:49:21', 'a:7:{s:4:"type";s:3:"tab";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:9:"placement";s:3:"top";s:8:"endpoint";i:0;}', 'Products Panel', '', 'publish', 'closed', 'closed', '', 'field_57e2b059f5157', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&p=116', 2, 'acf-field', '', 0),
(117, 1, '2016-09-21 16:49:21', '2016-09-21 16:49:21', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Products Section Title', 'products_section_title', 'publish', 'closed', 'closed', '', 'field_57e2b06ef5158', '', '', '2016-09-21 16:49:21', '2016-09-21 16:49:21', '', 18, 'http://azenvelope.loc/?post_type=acf-field&p=117', 3, 'acf-field', '', 0),
(118, 1, '2016-09-21 17:25:47', '2016-09-21 17:25:47', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Contact Us Form Subtitle', 'contact_us_form_subtitle', 'publish', 'closed', 'closed', '', 'field_57e2c28791b17', '', '', '2016-09-21 17:25:47', '2016-09-21 17:25:47', '', 18, 'http://azenvelope.loc/?post_type=acf-field&p=118', 15, 'acf-field', '', 0),
(119, 1, '2016-09-21 17:28:22', '2016-09-21 17:28:22', '', 'Home', '', 'inherit', 'closed', 'closed', '', '40-revision-v1', '', '', '2016-09-21 17:28:22', '2016-09-21 17:28:22', '', 40, 'http://azenvelope.loc/2016/09/21/40-revision-v1/', 0, 'revision', '', 0),
(120, 1, '2016-09-23 17:59:12', '0000-00-00 00:00:00', '', 'Auto Draft', '', 'auto-draft', 'open', 'open', '', '', '', '', '2016-09-23 17:59:12', '0000-00-00 00:00:00', '', 0, 'http://azenvelope.loc/?p=120', 0, 'post', '', 0),
(121, 1, '2016-09-23 18:52:15', '2016-09-23 18:52:15', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'First Hero Button Label', 'first_hero_button_label', 'publish', 'closed', 'closed', '', 'field_57e5798e01d6a', '', '', '2016-09-23 18:52:15', '2016-09-23 18:52:15', '', 19, 'http://azenvelope.loc/?post_type=acf-field&p=121', 3, 'acf-field', '', 0),
(122, 1, '2016-09-23 18:52:15', '2016-09-23 18:52:15', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'First Hero Button Url', 'first_hero_button_url', 'publish', 'closed', 'closed', '', 'field_57e579bf01d6b', '', '', '2016-09-23 18:52:15', '2016-09-23 18:52:15', '', 19, 'http://azenvelope.loc/?post_type=acf-field&p=122', 4, 'acf-field', '', 0),
(123, 1, '2016-09-23 18:52:15', '2016-09-23 18:52:15', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Second Hero Button Label', 'second_hero_button_label', 'publish', 'closed', 'closed', '', 'field_57e579c901d6c', '', '', '2016-09-23 18:52:15', '2016-09-23 18:52:15', '', 19, 'http://azenvelope.loc/?post_type=acf-field&p=123', 5, 'acf-field', '', 0),
(124, 1, '2016-09-23 18:52:15', '2016-09-23 18:52:15', 'a:10:{s:4:"type";s:4:"text";s:12:"instructions";s:0:"";s:8:"required";i:0;s:17:"conditional_logic";i:0;s:7:"wrapper";a:3:{s:5:"width";s:0:"";s:5:"class";s:0:"";s:2:"id";s:0:"";}s:13:"default_value";s:0:"";s:11:"placeholder";s:0:"";s:7:"prepend";s:0:"";s:6:"append";s:0:"";s:9:"maxlength";s:0:"";}', 'Second Hero Button URL', 'second_hero_button_url', 'publish', 'closed', 'closed', '', 'field_57e579d001d6d', '', '', '2016-09-23 18:52:15', '2016-09-23 18:52:15', '', 19, 'http://azenvelope.loc/?post_type=acf-field&p=124', 6, 'acf-field', '', 0),
(125, 1, '2016-09-23 18:52:57', '2016-09-23 18:52:57', '', 'Home', '', 'inherit', 'closed', 'closed', '', '40-revision-v1', '', '', '2016-09-23 18:52:57', '2016-09-23 18:52:57', '', 40, 'http://azenvelope.loc/2016/09/23/40-revision-v1/', 0, 'revision', '', 0) ;
INSERT INTO `aze_posts` ( `ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES
(126, 1, '2016-09-23 20:41:47', '2016-09-23 20:41:47', '', 'arizona-envelope-logo-placeholder', '', 'inherit', 'open', 'closed', '', 'arizona-envelope-logo-placeholder', '', '', '2016-09-23 20:41:49', '2016-09-23 20:41:49', '', 0, 'http://azenvelope.loc/wp-content/uploads/2016/09/arizona-envelope-logo-placeholder.png', 0, 'attachment', 'image/png', 0),
(127, 2, '2016-09-26 17:29:00', '0000-00-00 00:00:00', '', 'Auto Draft', '', 'auto-draft', 'open', 'open', '', '', '', '', '2016-09-26 17:29:00', '0000-00-00 00:00:00', '', 0, 'http://azenvelope.loc/?p=127', 0, 'post', '', 0) ;

#
# End of data contents of table `aze_posts`
# --------------------------------------------------------



#
# Delete any existing table `aze_rg_form`
#

DROP TABLE IF EXISTS `aze_rg_form`;


#
# Table structure of table `aze_rg_form`
#

CREATE TABLE `aze_rg_form` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_trash` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_rg_form`
#
INSERT INTO `aze_rg_form` ( `id`, `title`, `date_created`, `is_active`, `is_trash`) VALUES
(1, 'Contact Us', '2016-09-21 05:12:44', 1, 0) ;

#
# End of data contents of table `aze_rg_form`
# --------------------------------------------------------



#
# Delete any existing table `aze_rg_form_meta`
#

DROP TABLE IF EXISTS `aze_rg_form_meta`;


#
# Table structure of table `aze_rg_form_meta`
#

CREATE TABLE `aze_rg_form_meta` (
  `form_id` mediumint(8) unsigned NOT NULL,
  `display_meta` longtext COLLATE utf8_unicode_ci,
  `entries_grid_meta` longtext COLLATE utf8_unicode_ci,
  `confirmations` longtext COLLATE utf8_unicode_ci,
  `notifications` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_rg_form_meta`
#
INSERT INTO `aze_rg_form_meta` ( `form_id`, `display_meta`, `entries_grid_meta`, `confirmations`, `notifications`) VALUES
(1, '{"title":"Contact Us","description":"Contact us main form for Arizona Envelope\'s website.","labelPlacement":"top_label","descriptionPlacement":"below","button":{"type":"text","text":"Submit","imageUrl":""},"fields":[{"type":"text","id":1,"label":"","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"Name","cssClass":"","inputName":"","adminOnly":false,"noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","failed_validation":"","productField":"","enablePasswordInput":"","maxLength":"","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"useRichTextEditor":false,"pageNumber":1,"displayOnly":""},{"type":"text","id":2,"label":"","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"Company","cssClass":"","inputName":"","adminOnly":false,"noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","failed_validation":"","productField":"","enablePasswordInput":"","maxLength":"","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"useRichTextEditor":false,"pageNumber":1,"displayOnly":""},{"type":"phone","id":4,"label":"","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"phoneFormat":"standard","formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"Phone","cssClass":"","inputName":"","adminOnly":false,"noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","form_id":"","failed_validation":"","productField":"","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"useRichTextEditor":false,"pageNumber":1,"displayOnly":""},{"type":"email","id":3,"label":"","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"Email","cssClass":"","inputName":"","adminOnly":false,"noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","failed_validation":"","productField":"","emailConfirmEnabled":"","multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"useRichTextEditor":false,"pageNumber":1,"displayOnly":""},{"type":"textarea","id":5,"label":"","adminLabel":"","isRequired":false,"size":"medium","errorMessage":"","inputs":null,"formId":1,"description":"","allowsPrepopulate":false,"inputMask":false,"inputMaskValue":"","inputType":"","labelPlacement":"","descriptionPlacement":"","subLabelPlacement":"","placeholder":"Message","cssClass":"","inputName":"","adminOnly":false,"noDuplicates":false,"defaultValue":"","choices":"","conditionalLogic":"","failed_validation":"","productField":"","form_id":"","useRichTextEditor":false,"multipleFiles":false,"maxFiles":"","calculationFormula":"","calculationRounding":"","enableCalculation":"","disableQuantity":false,"displayAllCategories":false,"pageNumber":1,"displayOnly":""}],"version":"2.0.7.4","id":1,"useCurrentUserAsAuthor":true,"postContentTemplateEnabled":false,"postTitleTemplateEnabled":false,"postTitleTemplate":"","postContentTemplate":"","lastPageButton":null,"pagination":null,"firstPageCssClass":null}', NULL, '{"57e216ccb25bb":{"id":"57e216ccb25bb","name":"Default Confirmation","isDefault":true,"type":"message","message":"Thanks for contacting us! We will get in touch with you shortly.","url":"","pageId":"","queryString":""}}', '{"57e216ccb225c":{"id":"57e216ccb225c","to":"{admin_email}","name":"Admin Notification","event":"form_submission","toType":"email","subject":"New submission from {form_title}","message":"{all_fields}"}}') ;

#
# End of data contents of table `aze_rg_form_meta`
# --------------------------------------------------------



#
# Delete any existing table `aze_rg_form_view`
#

DROP TABLE IF EXISTS `aze_rg_form_view`;


#
# Table structure of table `aze_rg_form_view`
#

CREATE TABLE `aze_rg_form_view` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` mediumint(8) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  `ip` char(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `count` mediumint(8) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `date_created` (`date_created`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_rg_form_view`
#
INSERT INTO `aze_rg_form_view` ( `id`, `form_id`, `date_created`, `ip`, `count`) VALUES
(1, 1, '2016-09-21 06:45:44', '127.0.0.1', 62),
(2, 1, '2016-09-23 15:24:49', '::1', 40),
(3, 1, '2016-09-26 15:33:32', '::1', 21) ;

#
# End of data contents of table `aze_rg_form_view`
# --------------------------------------------------------



#
# Delete any existing table `aze_rg_incomplete_submissions`
#

DROP TABLE IF EXISTS `aze_rg_incomplete_submissions`;


#
# Table structure of table `aze_rg_incomplete_submissions`
#

CREATE TABLE `aze_rg_incomplete_submissions` (
  `uuid` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `form_id` mediumint(8) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  `ip` varchar(39) COLLATE utf8_unicode_ci NOT NULL,
  `source_url` longtext COLLATE utf8_unicode_ci NOT NULL,
  `submission` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`uuid`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_rg_incomplete_submissions`
#

#
# End of data contents of table `aze_rg_incomplete_submissions`
# --------------------------------------------------------



#
# Delete any existing table `aze_rg_lead`
#

DROP TABLE IF EXISTS `aze_rg_lead`;


#
# Table structure of table `aze_rg_lead`
#

CREATE TABLE `aze_rg_lead` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` mediumint(8) unsigned NOT NULL,
  `post_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `is_starred` tinyint(1) NOT NULL DEFAULT '0',
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `ip` varchar(39) COLLATE utf8_unicode_ci NOT NULL,
  `source_url` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_agent` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `currency` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_status` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `payment_amount` decimal(19,2) DEFAULT NULL,
  `payment_method` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_fulfilled` tinyint(1) DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `transaction_type` tinyint(1) DEFAULT NULL,
  `status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_rg_lead`
#

#
# End of data contents of table `aze_rg_lead`
# --------------------------------------------------------



#
# Delete any existing table `aze_rg_lead_detail`
#

DROP TABLE IF EXISTS `aze_rg_lead_detail`;


#
# Table structure of table `aze_rg_lead_detail`
#

CREATE TABLE `aze_rg_lead_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(10) unsigned NOT NULL,
  `form_id` mediumint(8) unsigned NOT NULL,
  `field_number` float NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`),
  KEY `lead_id` (`lead_id`),
  KEY `lead_field_number` (`lead_id`,`field_number`),
  KEY `lead_field_value` (`value`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_rg_lead_detail`
#

#
# End of data contents of table `aze_rg_lead_detail`
# --------------------------------------------------------



#
# Delete any existing table `aze_rg_lead_detail_long`
#

DROP TABLE IF EXISTS `aze_rg_lead_detail_long`;


#
# Table structure of table `aze_rg_lead_detail_long`
#

CREATE TABLE `aze_rg_lead_detail_long` (
  `lead_detail_id` bigint(20) unsigned NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`lead_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_rg_lead_detail_long`
#

#
# End of data contents of table `aze_rg_lead_detail_long`
# --------------------------------------------------------



#
# Delete any existing table `aze_rg_lead_meta`
#

DROP TABLE IF EXISTS `aze_rg_lead_meta`;


#
# Table structure of table `aze_rg_lead_meta`
#

CREATE TABLE `aze_rg_lead_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `lead_id` bigint(20) unsigned NOT NULL,
  `meta_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `meta_key` (`meta_key`(191)),
  KEY `lead_id` (`lead_id`),
  KEY `form_id_meta_key` (`form_id`,`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_rg_lead_meta`
#

#
# End of data contents of table `aze_rg_lead_meta`
# --------------------------------------------------------



#
# Delete any existing table `aze_rg_lead_notes`
#

DROP TABLE IF EXISTS `aze_rg_lead_notes`;


#
# Table structure of table `aze_rg_lead_notes`
#

CREATE TABLE `aze_rg_lead_notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lead_id` int(10) unsigned NOT NULL,
  `user_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `value` longtext COLLATE utf8_unicode_ci,
  `note_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lead_id` (`lead_id`),
  KEY `lead_user_key` (`lead_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_rg_lead_notes`
#

#
# End of data contents of table `aze_rg_lead_notes`
# --------------------------------------------------------



#
# Delete any existing table `aze_term_relationships`
#

DROP TABLE IF EXISTS `aze_term_relationships`;


#
# Table structure of table `aze_term_relationships`
#

CREATE TABLE `aze_term_relationships` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_term_relationships`
#
INSERT INTO `aze_term_relationships` ( `object_id`, `term_taxonomy_id`, `term_order`) VALUES
(1, 1, 0),
(8, 2, 0),
(54, 6, 0),
(55, 6, 0),
(58, 6, 0),
(59, 6, 0),
(60, 6, 0),
(61, 7, 0),
(62, 7, 0),
(69, 7, 0),
(70, 7, 0),
(71, 7, 0),
(77, 2, 0),
(77, 8, 0),
(78, 2, 0),
(78, 9, 0),
(79, 2, 0),
(79, 10, 0),
(80, 2, 0),
(80, 11, 0),
(81, 2, 0),
(81, 12, 0),
(98, 13, 0),
(99, 13, 0),
(100, 13, 0) ;

#
# End of data contents of table `aze_term_relationships`
# --------------------------------------------------------



#
# Delete any existing table `aze_term_taxonomy`
#

DROP TABLE IF EXISTS `aze_term_taxonomy`;


#
# Table structure of table `aze_term_taxonomy`
#

CREATE TABLE `aze_term_taxonomy` (
  `term_taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_term_taxonomy`
#
INSERT INTO `aze_term_taxonomy` ( `term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(1, 1, 'category', '', 0, 1),
(2, 2, 'product_type', '', 0, 6),
(3, 3, 'product_type', '', 0, 0),
(4, 4, 'product_type', '', 0, 0),
(5, 5, 'product_type', '', 0, 0),
(6, 6, 'nav_menu', '', 0, 5),
(7, 7, 'nav_menu', '', 0, 5),
(8, 8, 'product_cat', '', 0, 1),
(9, 9, 'product_cat', '', 0, 1),
(10, 10, 'product_cat', '', 0, 1),
(11, 11, 'product_cat', '', 0, 1),
(12, 12, 'product_cat', '', 0, 1),
(13, 13, 'nav_menu', '', 0, 3),
(14, 14, 'product_cat', '', 0, 0) ;

#
# End of data contents of table `aze_term_taxonomy`
# --------------------------------------------------------



#
# Delete any existing table `aze_termmeta`
#

DROP TABLE IF EXISTS `aze_termmeta`;


#
# Table structure of table `aze_termmeta`
#

CREATE TABLE `aze_termmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_termmeta`
#
INSERT INTO `aze_termmeta` ( `meta_id`, `term_id`, `meta_key`, `meta_value`) VALUES
(1, 8, 'order', '0'),
(2, 8, 'product_count_product_cat', '1'),
(3, 9, 'order', '0'),
(4, 9, 'product_count_product_cat', '1'),
(5, 10, 'order', '0'),
(6, 10, 'product_count_product_cat', '1'),
(7, 11, 'order', '0'),
(8, 11, 'product_count_product_cat', '1'),
(9, 12, 'order', '0'),
(10, 12, 'product_count_product_cat', '1'),
(11, 8, 'display_type', ''),
(12, 8, 'thumbnail_id', '111'),
(13, 11, 'display_type', ''),
(14, 11, 'thumbnail_id', '112'),
(15, 10, 'display_type', ''),
(16, 10, 'thumbnail_id', '113'),
(17, 9, 'display_type', ''),
(18, 9, 'thumbnail_id', '114'),
(19, 12, 'display_type', ''),
(20, 12, 'thumbnail_id', '115'),
(21, 14, 'order', '0'),
(22, 14, 'display_type', ''),
(23, 14, 'thumbnail_id', '110') ;

#
# End of data contents of table `aze_termmeta`
# --------------------------------------------------------



#
# Delete any existing table `aze_terms`
#

DROP TABLE IF EXISTS `aze_terms`;


#
# Table structure of table `aze_terms`
#

CREATE TABLE `aze_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `slug` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `term_group` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_terms`
#
INSERT INTO `aze_terms` ( `term_id`, `name`, `slug`, `term_group`) VALUES
(1, 'Uncategorized', 'uncategorized', 0),
(2, 'simple', 'simple', 0),
(3, 'grouped', 'grouped', 0),
(4, 'variable', 'variable', 0),
(5, 'external', 'external', 0),
(6, 'Site Navigation', 'site-navigation', 0),
(7, 'Top Navigation', 'top-navigation', 0),
(8, 'No. 9 &amp; No. 10 Envelopes', 'no-9-no-10-envelopes', 0),
(9, 'Other Commercial Envelopes', 'other-commercial-envelopes', 0),
(10, 'Open Side Booklet', 'open-side-booklet', 0),
(11, 'Open End Catalog', 'open-end-catalog', 0),
(12, 'Speciality Envelopes', 'speciality-envelopes', 0),
(13, 'Top Footer Menu', 'top-footer-menu', 0),
(14, 'Most Popular Products', 'most-popular-products', 0) ;

#
# End of data contents of table `aze_terms`
# --------------------------------------------------------



#
# Delete any existing table `aze_usermeta`
#

DROP TABLE IF EXISTS `aze_usermeta`;


#
# Table structure of table `aze_usermeta`
#

CREATE TABLE `aze_usermeta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_usermeta`
#
INSERT INTO `aze_usermeta` ( `umeta_id`, `user_id`, `meta_key`, `meta_value`) VALUES
(1, 1, 'nickname', 'oktara'),
(2, 1, 'first_name', ''),
(3, 1, 'last_name', ''),
(4, 1, 'description', ''),
(5, 1, 'rich_editing', 'true'),
(6, 1, 'comment_shortcuts', 'false'),
(7, 1, 'admin_color', 'fresh'),
(8, 1, 'use_ssl', '0'),
(9, 1, 'show_admin_bar_front', 'true'),
(10, 1, 'aze_capabilities', 'a:1:{s:13:"administrator";b:1;}'),
(11, 1, 'aze_user_level', '10'),
(12, 1, 'dismissed_wp_pointers', ''),
(13, 1, 'show_welcome_panel', '1'),
(15, 1, 'aze_dashboard_quick_press_last_post_id', '120'),
(16, 1, 'manageedit-shop_ordercolumnshidden', 'a:1:{i:0;s:15:"billing_address";}'),
(17, 1, 'managenav-menuscolumnshidden', 'a:4:{i:0;s:11:"link-target";i:1;s:15:"title-attribute";i:2;s:3:"xfn";i:3;s:11:"description";}'),
(18, 1, 'metaboxhidden_nav-menus', 'a:6:{i:0;s:30:"woocommerce_endpoints_nav_link";i:1;s:21:"add-post-type-product";i:2;s:12:"add-post_tag";i:3;s:15:"add-post_format";i:4;s:15:"add-product_cat";i:5;s:15:"add-product_tag";}'),
(19, 1, 'nav_menu_recently_edited', '13'),
(20, 1, 'aze_user-settings', 'libraryContent=browse'),
(21, 1, 'aze_user-settings-time', '1473718298'),
(22, 1, 'closedpostboxes_toplevel_page_theme-general-settings', 'a:0:{}'),
(23, 1, 'metaboxhidden_toplevel_page_theme-general-settings', 'a:0:{}'),
(24, 1, 'acf_user_settings', 'a:0:{}'),
(25, 1, 'gform_recent_forms', 'a:1:{i:0;s:1:"1";}'),
(26, 1, 'session_tokens', 'a:3:{s:64:"685a5dc4f579078ca0d031b6d03f80af325e1bc6d3511344cb63b233f9ff4397";a:4:{s:10:"expiration";i:1475077035;s:2:"ip";s:3:"::1";s:2:"ua";s:121:"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36";s:5:"login";i:1474904235;}s:64:"42e3c4e1c852bed8fea56cc07c1de417a79820bdf422947a9f577d4251d59972";a:4:{s:10:"expiration";i:1475083144;s:2:"ip";s:14:"190.171.97.118";s:2:"ua";s:121:"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36";s:5:"login";i:1474910344;}s:64:"fcb4769e0277a10d3f6126b27e2407972c146cac6dfa8c485ed29e8a0b4a3478";a:4:{s:10:"expiration";i:1475083538;s:2:"ip";s:13:"68.15.190.115";s:2:"ua";s:121:"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36";s:5:"login";i:1474910738;}}'),
(27, 2, 'nickname', 'aze'),
(28, 2, 'first_name', 'Raj'),
(29, 2, 'last_name', 'Dubey'),
(30, 2, 'description', ''),
(31, 2, 'rich_editing', 'true'),
(32, 2, 'comment_shortcuts', 'false'),
(33, 2, 'admin_color', 'fresh'),
(34, 2, 'use_ssl', '0'),
(35, 2, 'show_admin_bar_front', 'true'),
(36, 2, 'aze_capabilities', 'a:1:{s:13:"administrator";b:1;}'),
(37, 2, 'aze_user_level', '10'),
(38, 2, 'dismissed_wp_pointers', ''),
(39, 2, 'session_tokens', 'a:1:{s:64:"68e78278d18a0fd020d7fbb1541d35a12b46afe8cef18ff727636dc356733eea";a:4:{s:10:"expiration";i:1475083740;s:2:"ip";s:13:"68.15.190.115";s:2:"ua";s:121:"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36";s:5:"login";i:1474910940;}}'),
(40, 2, 'manageedit-shop_ordercolumnshidden', 'a:1:{i:0;s:15:"billing_address";}'),
(41, 2, 'gform_recent_forms', 'a:1:{i:0;s:1:"1";}'),
(42, 2, 'aze_dashboard_quick_press_last_post_id', '127'),
(43, 2, 'acf_user_settings', 'a:0:{}') ;

#
# End of data contents of table `aze_usermeta`
# --------------------------------------------------------



#
# Delete any existing table `aze_users`
#

DROP TABLE IF EXISTS `aze_users`;


#
# Table structure of table `aze_users`
#

CREATE TABLE `aze_users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_pass` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_nicename` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_url` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '0',
  `display_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_users`
#
INSERT INTO `aze_users` ( `ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES
(1, 'oktara', '$P$BlAe9.aZWxJ3eNvfR0rZ1nKrn79cm1/', 'oktara', 'andres.castillo@oktara.com', '', '2016-09-12 21:29:17', '', 0, 'oktara'),
(2, 'aze', '$P$BXJRevImx8DP.3qpflYCp/TprF/gsa1', 'aze', 'raj.dubey@latererralever.com', '', '2016-09-26 17:27:06', '', 0, 'Raj Dubey') ;

#
# End of data contents of table `aze_users`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_api_keys`
#

DROP TABLE IF EXISTS `aze_woocommerce_api_keys`;


#
# Table structure of table `aze_woocommerce_api_keys`
#

CREATE TABLE `aze_woocommerce_api_keys` (
  `key_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci,
  `permissions` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `consumer_key` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `consumer_secret` char(43) COLLATE utf8_unicode_ci NOT NULL,
  `nonces` longtext COLLATE utf8_unicode_ci,
  `truncated_key` char(7) COLLATE utf8_unicode_ci NOT NULL,
  `last_access` datetime DEFAULT NULL,
  PRIMARY KEY (`key_id`),
  KEY `consumer_key` (`consumer_key`),
  KEY `consumer_secret` (`consumer_secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_api_keys`
#

#
# End of data contents of table `aze_woocommerce_api_keys`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_attribute_taxonomies`
#

DROP TABLE IF EXISTS `aze_woocommerce_attribute_taxonomies`;


#
# Table structure of table `aze_woocommerce_attribute_taxonomies`
#

CREATE TABLE `aze_woocommerce_attribute_taxonomies` (
  `attribute_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `attribute_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `attribute_label` longtext COLLATE utf8_unicode_ci,
  `attribute_type` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `attribute_orderby` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `attribute_public` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`attribute_id`),
  KEY `attribute_name` (`attribute_name`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_attribute_taxonomies`
#

#
# End of data contents of table `aze_woocommerce_attribute_taxonomies`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_downloadable_product_permissions`
#

DROP TABLE IF EXISTS `aze_woocommerce_downloadable_product_permissions`;


#
# Table structure of table `aze_woocommerce_downloadable_product_permissions`
#

CREATE TABLE `aze_woocommerce_downloadable_product_permissions` (
  `permission_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `download_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `order_id` bigint(20) NOT NULL DEFAULT '0',
  `order_key` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `downloads_remaining` varchar(9) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_granted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access_expires` datetime DEFAULT NULL,
  `download_count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`permission_id`),
  KEY `download_order_key_product` (`product_id`,`order_id`,`order_key`(191),`download_id`),
  KEY `download_order_product` (`download_id`,`order_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_downloadable_product_permissions`
#

#
# End of data contents of table `aze_woocommerce_downloadable_product_permissions`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_order_itemmeta`
#

DROP TABLE IF EXISTS `aze_woocommerce_order_itemmeta`;


#
# Table structure of table `aze_woocommerce_order_itemmeta`
#

CREATE TABLE `aze_woocommerce_order_itemmeta` (
  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_item_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`meta_id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_order_itemmeta`
#

#
# End of data contents of table `aze_woocommerce_order_itemmeta`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_order_items`
#

DROP TABLE IF EXISTS `aze_woocommerce_order_items`;


#
# Table structure of table `aze_woocommerce_order_items`
#

CREATE TABLE `aze_woocommerce_order_items` (
  `order_item_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_item_name` longtext COLLATE utf8_unicode_ci NOT NULL,
  `order_item_type` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `order_id` bigint(20) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_order_items`
#

#
# End of data contents of table `aze_woocommerce_order_items`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_payment_tokenmeta`
#

DROP TABLE IF EXISTS `aze_woocommerce_payment_tokenmeta`;


#
# Table structure of table `aze_woocommerce_payment_tokenmeta`
#

CREATE TABLE `aze_woocommerce_payment_tokenmeta` (
  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `payment_token_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`meta_id`),
  KEY `payment_token_id` (`payment_token_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_payment_tokenmeta`
#

#
# End of data contents of table `aze_woocommerce_payment_tokenmeta`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_payment_tokens`
#

DROP TABLE IF EXISTS `aze_woocommerce_payment_tokens`;


#
# Table structure of table `aze_woocommerce_payment_tokens`
#

CREATE TABLE `aze_woocommerce_payment_tokens` (
  `token_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `gateway_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`token_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_payment_tokens`
#

#
# End of data contents of table `aze_woocommerce_payment_tokens`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_sessions`
#

DROP TABLE IF EXISTS `aze_woocommerce_sessions`;


#
# Table structure of table `aze_woocommerce_sessions`
#

CREATE TABLE `aze_woocommerce_sessions` (
  `session_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `session_key` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `session_value` longtext COLLATE utf8_unicode_ci NOT NULL,
  `session_expiry` bigint(20) NOT NULL,
  PRIMARY KEY (`session_key`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_sessions`
#

#
# End of data contents of table `aze_woocommerce_sessions`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_shipping_zone_locations`
#

DROP TABLE IF EXISTS `aze_woocommerce_shipping_zone_locations`;


#
# Table structure of table `aze_woocommerce_shipping_zone_locations`
#

CREATE TABLE `aze_woocommerce_shipping_zone_locations` (
  `location_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `zone_id` bigint(20) NOT NULL,
  `location_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `location_type` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `location_id` (`location_id`),
  KEY `location_type` (`location_type`),
  KEY `location_type_code` (`location_type`,`location_code`(90))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_shipping_zone_locations`
#

#
# End of data contents of table `aze_woocommerce_shipping_zone_locations`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_shipping_zone_methods`
#

DROP TABLE IF EXISTS `aze_woocommerce_shipping_zone_methods`;


#
# Table structure of table `aze_woocommerce_shipping_zone_methods`
#

CREATE TABLE `aze_woocommerce_shipping_zone_methods` (
  `zone_id` bigint(20) NOT NULL,
  `instance_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `method_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `method_order` bigint(20) NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_shipping_zone_methods`
#

#
# End of data contents of table `aze_woocommerce_shipping_zone_methods`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_shipping_zones`
#

DROP TABLE IF EXISTS `aze_woocommerce_shipping_zones`;


#
# Table structure of table `aze_woocommerce_shipping_zones`
#

CREATE TABLE `aze_woocommerce_shipping_zones` (
  `zone_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `zone_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `zone_order` bigint(20) NOT NULL,
  PRIMARY KEY (`zone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_shipping_zones`
#

#
# End of data contents of table `aze_woocommerce_shipping_zones`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_tax_rate_locations`
#

DROP TABLE IF EXISTS `aze_woocommerce_tax_rate_locations`;


#
# Table structure of table `aze_woocommerce_tax_rate_locations`
#

CREATE TABLE `aze_woocommerce_tax_rate_locations` (
  `location_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `location_code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tax_rate_id` bigint(20) NOT NULL,
  `location_type` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `tax_rate_id` (`tax_rate_id`),
  KEY `location_type` (`location_type`),
  KEY `location_type_code` (`location_type`,`location_code`(90))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_tax_rate_locations`
#

#
# End of data contents of table `aze_woocommerce_tax_rate_locations`
# --------------------------------------------------------



#
# Delete any existing table `aze_woocommerce_tax_rates`
#

DROP TABLE IF EXISTS `aze_woocommerce_tax_rates`;


#
# Table structure of table `aze_woocommerce_tax_rates`
#

CREATE TABLE `aze_woocommerce_tax_rates` (
  `tax_rate_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tax_rate_country` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tax_rate_state` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tax_rate` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tax_rate_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tax_rate_priority` bigint(20) NOT NULL,
  `tax_rate_compound` int(1) NOT NULL DEFAULT '0',
  `tax_rate_shipping` int(1) NOT NULL DEFAULT '1',
  `tax_rate_order` bigint(20) NOT NULL,
  `tax_rate_class` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`tax_rate_id`),
  KEY `tax_rate_country` (`tax_rate_country`(191)),
  KEY `tax_rate_state` (`tax_rate_state`(191)),
  KEY `tax_rate_class` (`tax_rate_class`(191)),
  KEY `tax_rate_priority` (`tax_rate_priority`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


#
# Data contents of table `aze_woocommerce_tax_rates`
#
INSERT INTO `aze_woocommerce_tax_rates` ( `tax_rate_id`, `tax_rate_country`, `tax_rate_state`, `tax_rate`, `tax_rate_name`, `tax_rate_priority`, `tax_rate_compound`, `tax_rate_shipping`, `tax_rate_order`, `tax_rate_class`) VALUES
(1, 'US', 'AZ', '5.6000', 'State Tax', 1, 0, 0, 0, '') ;

#
# End of data contents of table `aze_woocommerce_tax_rates`
# --------------------------------------------------------

#
# Add constraints back in and apply any alter data queries.
#

