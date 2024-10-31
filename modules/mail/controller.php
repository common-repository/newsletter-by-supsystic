<?php
class mailControllerNbs extends controllerNbs {
	public function testEmail() {
		$res = new responseNbs();
		$email = reqNbs::getVar('test_email', 'post');
		if($this->getModel()->testEmail($email)) {
			$res->addMessage(__('Now check your email inbox / spam folders for test mail.'));
		} else 
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function saveMailTestRes() {
		$res = new responseNbs();
		$result = (int) reqNbs::getVar('result', 'post');
		frameNbs::_()->getModule('options')->getModel()->save('mail_function_work', $result);
		$res->ajaxExec();
	}
	public function saveOptions() {
		$res = new responseNbs();
		$optsModel = frameNbs::_()->getModule('options')->getModel();
		$submitData = reqNbs::get('post');
		if($optsModel->saveGroup($submitData)) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError ($optsModel->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array('testEmail', 'saveMailTestRes', 'saveOptions')
			),
		);
	}
}
