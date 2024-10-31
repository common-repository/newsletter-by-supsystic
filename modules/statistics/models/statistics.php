<?php
class statisticsModelNbs extends modelNbs {
	public function getSidViews() {
		return get_option( NBS_CODE. '_newsletter_views', array() );
	}
	public function setSidViews( $allSidViews ) {
		update_option( NBS_CODE. '_newsletter_views', $allSidViews );
	}
	public function clearCurrentForNewsletter( $nid ) {
		$allSidViews = $this->getSidViews();
		if(isset($allSidViews[ $nid ])) {
			unset( $allSidViews[ $nid ] );
			$this->setSidViews( $allSidViews );
		}
		frameNbs::_()->getModule('newsletters')->getModel()->updateById(array(
			'open' => 0, 'uniq_open' => 0, 'click' => 0, 'queued' => 0, 'sent_cnt' => 0,
		), $nid);
	}
	public function add( $d = array() ) {
		$type = isset($d['type']) ? $d['type'] : false;
		$nid = isset($d['nid']) ? (int) $d['nid'] : false;
		$sid = isset($d['sid']) ? (int) $d['sid'] : false;
		
		if($type && $nid && $sid) {
			$this->_insertAction( $nid, $type );
			
			if($type == 'open') {
				$allSidViews = $this->getSidViews( $nid );
				if(!isset($allSidViews[ $nid ], $allSidViews[ $nid ][ $sid ])) {	// First time opened
					$this->_insertAction( $nid, 'uniq_open' );
					if(!isset($allSidViews[ $nid ])) {
						$allSidViews[ $nid ] = array();
					}
					$allSidViews[ $nid ][ $sid ] = 1;
					$this->setSidViews( $allSidViews );
				}
			}
		} else
			$this->pushError(__('Please specify type, newsletter and subscriber', NBS_LANG_CODE));
		return false;
	}
	private function _insertAction( $nid, $type ) {
		$typeId = $this->getModule()->getTypeByCode( $type, 'id' );
		if($typeId) {
			// Update current newsletter stats
			dbNbs::query("UPDATE @__newsletters SET `$type` = `$type` + 1 WHERE id = $nid;");
			if(frameNbs::_()->getModule('supsystic_promo')->isPro() && frameNbs::_()->getModule('stats_pro')) {
				frameNbs::_()->getModule('stats_pro')->getModel()->insertAction( $nid, $typeId );
			}
			return true;
		}
		return false;
	}
}