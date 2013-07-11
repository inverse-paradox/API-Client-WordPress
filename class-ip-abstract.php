<?php
	
class Ip_Abstract
{

	protected $_db;
	protected $_table;

	function __construct($table)
	{
		global $wpdb;
		$this->_db = $wpdb;
		$this->_table = $this->_db->prefix.$table;
	}

	public function setData($key, $val)
	{
		$val = json_encode($val);
		$sql = $this->_db->prepare( "
			INSERT INTO ".$this->_table." (id, val) 
			VALUES ('%s', '%s')
				ON DUPLICATE KEY UPDATE val = '%s';
			", $key, $val, $val);
		$this->_db->query($sql);
	}

	public function getData($key, $array = true)
	{
		$sql = "
			SELECT val
			FROM ".$this->_table."
			WHERE id='".$key."'
		";
		$val = $this->_db->get_var($sql);
		if($result = json_decode($val, $array)){
			return $result;
		}
		return array();
	}


	public function install_table()
	{
		if($this->_db->get_var("show tables like '".$this->_table."'") != $this->_table){
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta("
				CREATE TABLE `" . $this->_table . "` (
		    		`id` varchar(60) NOT NULL,
		    		`val` longtext DEFAULT '' NULL,
		    	PRIMARY KEY (`id`)
		    	);
			");
		}
	}

}