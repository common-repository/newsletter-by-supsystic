<?php
class mailViewNbs extends viewNbs {
	public function getTabContent() {
		frameNbs::_()->getModule('templates')->loadJqueryUi();
		frameNbs::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		
		$this->assign('options', frameNbs::_()->getModule('options')->getCatOpts( $this->getCode() ));
		$this->assign('testEmail', frameNbs::_()->getModule('options')->get('notify_email'));
		return parent::getContent('mailAdmin');
	}
}
