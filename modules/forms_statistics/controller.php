<?php
class forms_statisticsControllerNbs extends controllerNbs {
	public function add() {
		$res = new responseNbs();
		$connectHash = reqNbs::getVar('connect_hash', 'post');
		$id = reqNbs::getVar('id', 'post');
		if(md5(date('m-d-Y'). $id. NONCE_KEY) != $connectHash) {
			$res->pushError('Some undefined for now.....');
			$res->ajaxExec( true );
		}
		if($this->getModel()->add( reqNbs::get('post') )) {
			// Do nothing for now
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function clearForForm() {
		$res = new responseNbs();
		if($this->getModel()->clearForForm( reqNbs::get('post') )) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function getUpdatedStats() {
		$res = new responseNbs();
		if(($stats = $this->getModel()->getUpdatedStats( reqNbs::get('post') )) !== false) {
			$res->addData('stats', $stats);
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function getCsv() {
		if(($stats = $this->getModel()->getPreparedStats( reqNbs::get('get') )) !== false) {
			$id = (int) reqNbs::getVar('id');
			$form = frameNbs::_()->getModule('forms')->getModel()->getById( $id );
			importClassNbs('filegeneratorNbs');
			importClassNbs('csvgeneratorNbs');
			$csvGenerator = new csvgeneratorNbs(sprintf(__('Statistics for %s', NBS_LANG_CODE), htmlspecialchars( $form['label'] )));
			$labels = array(
				'date' => __('Date', NBS_LANG_CODE),
				'views' => __('Views', NBS_LANG_CODE),
				'unique_requests' => __('Unique Views', NBS_LANG_CODE),
				'actions' => __('Actions', NBS_LANG_CODE),
				'conversion' => __('Conversion', NBS_LANG_CODE),
			);
			$row = $cell = 0;
			foreach($labels as $l) {
				$csvGenerator->addCell($row, $cell, $l);
				$cell++;
			}
			$row = 1;
			foreach($stats as $s) {
				$cell = 0;
				foreach($labels as $k => $l) {
					$csvGenerator->addCell($row, $cell, $s[ $k ]);
					$cell++;
				}
				$row++;
			}
			$csvGenerator->generate();
		} else {
			echo implode('<br />', $this->getModel()->getErrors());
		}
		exit();
	}
	public function getStats() {
		$res = new responseNbs();
		$res->addData($this->getModel()->getStats( reqNbs::get('post') ));
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array('clearForForm', 'getUpdatedStats', 'getCsv', 'getStats')
			),
		);
	}
}
