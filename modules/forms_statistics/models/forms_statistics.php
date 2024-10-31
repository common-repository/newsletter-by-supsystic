<?php
class forms_statisticsModelNbs extends modelNbs {
	public function add($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		$d['type'] = isset($d['type']) ? $d['type'] : '';
		if(!empty($d['id']) && !empty($d['type'])) {
			$typeId = $this->getModule()->getTypeIdByCode( $d['type'] );
			$isUnique = 0;
			if(isset($d['is_unique']) && !empty($d['is_unique'])) {
				$isUnique = (int) 1;	// This is realy cool :)
			}
			$formModel = frameNbs::_()->getModule('forms')->getModel();
			if(in_array($d['type'], array('show'))) {
				$formModel->addViewed( $d['id'] );
				if($isUnique) {
					$formModel->addUniqueViewed( $d['id'] );
				}
			} else {	// Any action count here
				if(!in_array($d['type'], array('submit', 'submit_error'))) {	// Do not count empty submits here
					$formModel->addActionDone( $d['id'] );
				}
			}
			if(frameNbs::_()->getModule('supsystic_promo')->isPro() 
				&& frameNbs::_()->getModule('forms_stats_pro')
			) {
				frameNbs::_()->getModule('forms_stats_pro')->getModel()->add($d['id'], $typeId, $isUnique);
			}
			return true;
		} else
			$this->pushError(__('Send me some info, pls', NBS_LANG_CODE));
		return false;
	}
}