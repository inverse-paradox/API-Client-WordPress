<?php

class Ip_Api extends Ip_Abstract
{

	public function request_handler()
	{
		if(isset($_POST)){
			$api = json_decode(file_get_contents("php://input"), true);
			if(isset($api['request']) && !isset($api['request']['error'])){
				foreach($api['request'] as $key => $val){
					$this->setData($key, $val);
				}
				$response['errors'] = $this->getData('php_errors');
				$response['platform'] = 'WordPress';
				$response['version'] = get_bloginfo('version');
				$response['plugins'] = $this->get_plugins();	
				$response['server'] = json_encode(array(
					'api' => PHP_SAPI,
					'php' => PHP_VERSION,
				));
				die(json_encode($response));
			}
		}
	}

	private function get_plugins()
	{
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/update.php';
		wp_update_plugins();
		$plugins = get_plugins();
		array_walk_recursive($plugins, array($this, 'stripquotes'));
		$results = array();
		$updates = get_site_transient('update_plugins');
		foreach($plugins as $source => $data){
			$update = isset($updates->response[$source]) ? $updates->response[$source] : null;
			array_walk_recursive($update, array($this, 'striptags'));
			$results[] = array(
				'name' => $data["Name"],
				'version' => $data["Version"],
				'active' => is_plugin_active($source),
				'update' => $update
			);
		}	
		return $results;
	}

	private function striptags(&$val, $key)
	{
		$val = strip_tags($val);
	}

	private function stripquotes(&$val, $key)
	{
		$val = str_replace('"','',$val);
	}

}