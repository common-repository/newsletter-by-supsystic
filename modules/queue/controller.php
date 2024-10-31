<?php
class queueControllerNbs extends controllerNbs {
	public function check() {
		frameNbs::_()->getModule('log')->setOut( true );
		$this->getModel()->check();
		echo frameNbs::_()->getModule('log')->getOut();
		exit();
	}
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array('check')
			),
		);
	}
}
