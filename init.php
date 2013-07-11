<?php
/*
Plugin Name: Inverse Paradox Client Dashboard
Description: Display Twitter, Blog Feed, and Custom Messages on dashboard widgets.
Version: 0.1.0
Author: Inverse Paradox
*/

	require_once 'class-ip-abstract.php';
	require_once 'class-ip-api.php';
	require_once 'class-ip-log.php';

	define('API_STATUS_RESPONSE_DEVELOPMENT', 100);
	define('API_STATUS_RESPONSE_ENABLED', 200);
	define('API_STATUS_RESPONSE_DISABLED_TEMP', 300);
	define('API_STATUS_RESPONSE_DISABLED_FULL', 400);
	
	add_action('init', 'ip_load_app');

	function ip_load_app()
	{
		global $Ip_Api, $Ip_Log;
		$Ip_Api = new Ip_Api('api_data');
		$Ip_Log = new Ip_Log('api_data');
		add_action('admin_init', 	array($Ip_Api, 'install_table'));
		add_action('pre_get_posts', array($Ip_Api, 'request_handler'));
		set_exception_handler(		array($Ip_Log, 'exception_handler'));
		set_error_handler(			array($Ip_Log, 'error_handler'));
		register_shutdown_function(	array($Ip_Log, 'shutdown'));
	}

	add_action('wp_dashboard_setup', 'ip_add_dashboard_widgets');

	function ip_add_dashboard_widgets()
	{
		$dashboard_widgets = array(
			'general' => array(
				'title' => 'Inverse Paradox: Keep in touch!',
				'template' => 'dashboard/general.php'
			),
			'blog' => array(
				'title' => 'Inverse Paradox Blog Feed',
				'template' => 'dashboard/blog.php'
			),
			'twitter' => array(
				'title' => 'Inverse Paradox Twitter Feed',
				'template' => 'dashboard/twitter.php'
			),
		);
		foreach($dashboard_widgets as $widget_id => $widget){
			wp_add_dashboard_widget(
				$widget_id, 
				$widget['title'], 
				create_function('', 'include "' . $widget['template'] . '";')
			);
		}	
	}

	function ip_api_get_config($id)
	{
		global $Ip_Api;
		return $Ip_Api->getData($id, false);
	}

	function ip_api_fetch_feed($url, $num)
	{
		include_once ABSPATH . WPINC . '/rss.php';
		$rss = fetch_rss($url);
		$items = array_slice($rss->items, 0, $num);
		$feed = json_decode(json_encode($items));
		return $feed;
	}
