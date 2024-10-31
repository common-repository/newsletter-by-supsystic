<?php
class admin_navControllerNbs extends controllerNbs {
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array()
			),
		);
	}
}