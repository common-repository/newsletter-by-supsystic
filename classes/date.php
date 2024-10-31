<?php
class dateNbs {
	static public function _($time = NULL) {
		if(is_null($time)) {
			$time = time();
		}
		return date(NBS_DATE_FORMAT_HIS, $time);
	}
}