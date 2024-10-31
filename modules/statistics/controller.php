<?php
class statisticsControllerNbs extends controllerNbs {
	public function openImg() {}
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array()
			),
		);
	}
}
