<?php
class logNbs extends moduleNbs {
	private $_logFilePath = '';
	private $_logPath = '';
	private $_enb = false;	// Set this to "true" to enable debug log
	private $_out = false;
	private $_outStr = "";
	public function addLine($str) {
		if($this->_enb) {
			$this->getLogFilePath();
			$time = '['. date('H:i:s'). ']';
			if(is_array($str)) $str = implode(NBS_EOL. $time, $str);
			$str = $time. $str. NBS_EOL;
			file_put_contents($this->_logFilePath, $str, FILE_APPEND);
			if($this->_out) {
				$this->_outStr .= $str;
			} 
		}
	}
	public function getLogFilePath() {
		if(empty($this->_logFilePath)) {
			$this->getLogPath();
			$this->_logFilePath = $this->_logPath. date('m-d-Y'). '.log';
		}
		return $this->_logFilePath;
	}
	public function getLogPath() {
		if(empty($this->_logPath)) {
			$uploadsDir = wp_upload_dir(null, false);
			$this->_logPath = str_replace('/', DS, $uploadsDir['basedir']). DS. NBS_CODE. '-log'. DS;
			if(!is_dir($this->_logPath)) {
				mkdir($this->_logPath);
			}
		}
		return $this->_logPath;
	}
	public function setOut( $val ) {
		$this->_out = $val;
	}
	public function getOut() {
		return $this->_outStr;
	}
}

