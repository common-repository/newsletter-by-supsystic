<?php
class api_loaderControllerNbs extends controllerNbs {
	public function getLists() {
		$res = new responseNbs();
		$this->getModule()->saveSets( reqNbs::getVar('sets') );
		if(($lists = $this->getModel()->getLists()) != false) {
			$res->addData('lists', $lists);
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array('getLists')
			),
		);
	}
}

