<?php
class importerControllerNbs extends controllerNbs {
	public function import() {
		$res = new responseNbs();
		frameNbs::_()->getModule('api_loader')->saveSets( reqNbs::getVar('sets') );
		if(($importedCnt = $this->getModel()->import( reqNbs::get('post') )) != false) {
			$res->addMessage(sprintf(__('%s subscribers imported', NBS_LANG_CODE), $importedCnt['inserted'] + $importedCnt['updated']));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array('import')
			),
		);
	}
}

