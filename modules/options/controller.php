<?php
class optionsControllerNbs extends controllerNbs {
	public function saveGroup() {
		$res = new responseNbs();
		if($this->getModel()->saveGroup(reqNbs::get('post'))) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function fullDbUninstall() {
		if(frameNbs::_()->getModule('user')->isAdmin()) {
			installerNbs::delete( true );
		}
	}
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array('saveGroup', 'fullDbUninstall')
			),
		);
	}
}

