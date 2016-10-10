<?php

if(!defined('TT_PREFIX'))
	define ('TT_PREFIX', 'TT_');
if(!defined('TT_LOW'))
	define ('TT_LOW', 'tt_'); //prefix lowered
if(!defined('TT_THEME_DIR'))
	define ('TT_THEME_DIR', get_template_directory());
if(!defined('TT_THEME_URI'))
	define ('TT_THEME_URI', get_template_directory_uri());
if(!defined('TT_STYLE_DIR'))
	define ('TT_STYLE_DIR', get_stylesheet_directory());
if(!defined('TT_STYLE_URI'))
	define ('TT_STYLE_URI', get_stylesheet_directory_uri());

if(!defined('TT_FW'))
	define ('TT_FW',TT_THEME_URI . '/tesla_framework');
if(!defined('TT_FW_DIR'))
	define ('TT_FW_DIR',TT_THEME_DIR . '/tesla_framework');
if(!defined('TT_FW_VERSION'))
	define ('TT_FW_VERSION', '1.9.8');

$tt_theme = wp_get_theme();
if(!defined('THEME_FOLDER_NAME'))
	define ('THEME_FOLDER_NAME', $tt_theme->template);

$tt_parent_theme = wp_get_theme(THEME_FOLDER_NAME);
if(!defined('THEME_VERSION'))
	define ('THEME_VERSION', $tt_parent_theme->version);