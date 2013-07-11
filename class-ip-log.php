<?php

class Ip_Log extends Ip_Abstract
{

	protected $expiration = 605000;// 1 week = 605,000 seconds

	public function exception_handler($exception)
	{
		$this->error_append('exception', $exception->getMessage());
	}

	public function error_handler($errno, $errstr, $errfile, $errline)
	{
		$error_codes = array(
			E_ERROR => 'E_ERROR', 
	        E_WARNING => 'E_WARNING',
	        E_PARSE => 'E_PARSE',
	        E_NOTICE => 'E_NOTICE', 
	        E_CORE_ERROR => 'E_CORE_ERROR',
	        E_CORE_WARNING => 'E_CORE_WARNING',
	        E_CORE_ERROR => 'E_COMPILE_ERROR',
	        E_CORE_WARNING => 'E_COMPILE_WARNING',
	        E_USER_ERROR => 'E_USER_ERROR',
	        E_USER_WARNING => 'E_USER_WARNING',
	        E_USER_NOTICE => 'E_USER_NOTICE',
	        E_STRICT => 'E_STRICT',
	        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
	        E_DEPRECATED => 'E_DEPRECATED',
	        E_USER_DEPRECATED => 'E_USER_DEPRECATED'
	    );
	    $message = $errstr.' on line '.$errline.' in file '.$errfile;
		$this->error_append($error_codes[$errno], $message);
		return true;
	}

	public function shutdown()
	{
		if($error = error_get_last()){
			$this->error_handler($error['type'], $error['message'], $error['file'], $error['line']);
		}
	}

	public function error_append($code, $error)
	{
		$now = current_time('timestamp');
		$time = date(DATE_ATOM, $now);
		$php_errors = $this->getData('php_errors');
		if(!$this->duplicate_error($time, $error, $php_errors)){
			$php_errors[] = array(
				'occurences' => 1,
				'time' => $time,
				'code' => $code,
				'error' => $error
			);
		}
		$this->clear_old_errors($php_errors, $now - $this->expiration);
		$this->setData('php_errors', $php_errors);
	}

	public function clear_old_errors(&$php_errors, $expiration)
	{
		foreach($php_errors as $key => $php_error){
			$event = strtotime($php_error['time']);
			if($event < $expiration){
				unset($php_errors[$key]);
			}
		}
	}

	public function duplicate_error($time, $new_error, &$saved_errors)
	{
		foreach($saved_errors as &$saved_error){
			if($saved_error['error'] == $new_error){
				$saved_error['time'] = $time;
				$saved_error['occurences'] +=1;
				return true; 
			}
		}
		return false;
	}

}