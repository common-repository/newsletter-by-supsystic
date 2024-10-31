<?php
class queueModelNbs extends modelNbs {
	public function __construct() {
		$this->_setTbl('queue');
	}
	public function check() {
		$maxSend = (int) frameNbs::_()->getModule('options')->get('emails_per_queue');
		$allQueue = $this->getCurrentQueue(array('limit' => $maxSend));
		$queueCnt = $allQueue ? count($allQueue) : 0;
		frameNbs::_()->getModule('log')->addLine("max-send $maxSend");
		frameNbs::_()->getModule('log')->addLine("total-send $queueCnt");
		if(!empty($allQueue)) {
			$clearFor = array();
			$sendCnt = $queueCnt > $maxSend ? $maxSend : $queueCnt;
			$nids = array();
			for($i = 0; $i < $sendCnt; $i++) {
				$nids[ $allQueue[ $i ]['nid'] ] = 1;
			}
			$nids = array_keys( $nids );
			$newslettersModel = frameNbs::_()->getModule('newsletters')->getModel();
			$newsletters = $newslettersModel->getFullWithComposedTheme( $nids, true );
			$sendSuccessCnt = array();
			
			for($i = 0; $i < $sendCnt; $i++) {
				if(isset($allQueue[ $i ]['all_data'])) {
					$allQueue[ $i ]['all_data'] = empty($allQueue[ $i ]['all_data']) ? array() : utilsNbs::unserialize($allQueue[ $i ]['all_data']);
				}
				// Detect required newsletter for send
				$nid = $allQueue[ $i ]['nid'];
				$newsletter = array();
				foreach($newsletters as $n) {
					if($n['id'] == $nid) {
						$newsletter = $n;
						break;
					}
				}
				if(!isset($sendSuccessCnt[ $nid ])) {
					$sendSuccessCnt[ $nid ] = 0;
				}
				$sendRes = $newslettersModel->sendOne($allQueue[ $i ]['email'], $newsletter, $allQueue[ $i ]);
				frameNbs::_()->getModule('log')->addLine("sent to {$allQueue[ $i ]['email']} - [{$newsletter['label']}] - res:". ($sendRes ? 1 : 0));
				if($sendRes) {
					$clearFor[] = array('sid' => $allQueue[ $i ]['id'], 'nid' => $allQueue[ $i ]['nid']);
					$sendSuccessCnt[ $nid ]++;
				}
			}
			if(!empty($clearFor)) {
				$this->checkClear( $clearFor );
				frameNbs::_()->getModule('log')->addLine("queue cleared for ". count($clearFor). " records");
				// Check if there are some emails left for each newsletter
				$sendOn = $newslettersModel->getSendOn();
				foreach($nids as $nid) {
					$emailsLeft = (int) $this->setWhere(array('nid' => $nid))->getCount();
					if(!$emailsLeft) {	// Newsletter done it's current send queue
						$newsletter = array();
						foreach($newsletters as $n) {	// Find required newsletter
							if($n['id'] == $nid) {
								$newsletter = $n;
								break;
							}
						}
						$completeStatus = null;
						switch($newsletter['send_on']) {
							case $sendOn['immediately']['id']:
								$completeStatus = $newslettersModel->getStatusByCode('complete');	// It's done
								break;
							case $sendOn['new_content']['id']:
								$completeStatus = $newslettersModel->getStatusByCode('waiting');	// Continue waiting for new posts
								break;
						}
						if($completeStatus) {
							$newslettersModel->updateById(array('status' => $completeStatus['id']), $nid);
						}
					}
					// Update statistics for sent data
					if(isset($sendSuccessCnt[ $nid ]) && !empty($sendSuccessCnt[ $nid ])) {
						dbNbs::query("UPDATE @__newsletters SET "
							. "`sent_cnt` = `sent_cnt` + {$sendSuccessCnt[ $nid ]}, "
							. "`last_sent` = NOW() "
							. "WHERE id = $nid;");
					}
				}
			}
		}
	}
	public function getCurrentQueue($d = array()) {
		$limit = isset($d['limit']) ? "LIMIT {$d['limit']}" : "";
		$sendingStatusIds = array(frameNbs::_()->getModule('newsletters')->getModel()->getStatusByCode('sending', 'id'),
			frameNbs::_()->getModule('newsletters')->getModel()->getStatusByCode('waiting', 'id'));
		return dbNbs::get("SELECT s.*, q.nid FROM @__queue q "
			. "INNER JOIN @__subscribers s ON s.id = q.sid "
			. "INNER JOIN @__newsletters n ON n.id = q.nid "
			. "WHERE q.date_send <= NOW() AND n.status IN (". implode(',', $sendingStatusIds). ") "
			. "$limit");
	}
	public function clearForNewsletters( $nids = array() ) {
		if(!is_array($nids)) $nids = array( $nids );
		return dbNbs::query("DELETE FROM @__queue WHERE nid IN (". implode(', ', $nids). ")");
	}
	public function clearForSubscribers( $sids = array() ) {
		if(!is_array($sids)) $sids = array( $sids );
		return dbNbs::query("DELETE FROM @__queue WHERE sid IN (". implode(', ', $sids). ")");
	}
	public function clearForSubscribersLists( $slids = array() ) {
		if(!is_array($slids)) $sids = array( $slids );
		return dbNbs::query("DELETE FROM @__queue WHERE sid IN (SELECT sid FROM @__subscribers_to_lists WHERE slid IN (". implode(', ', $slids). "))");
	}
	public function addNewsletter( $nid, $time = NULL ) {
		$nid = (int) $nid;
		// Clear prev. saved stats - before adding new queue
		frameNbs::_()->getModule('statistics')->getModel()->clearCurrentForNewsletter( $nid );
		if($time) {
			// If this is literraly time - not some sql fnction - check it and take it into ""
			if(preg_match('/^\d{4}-\d{1,2}-\d{1,2} \d{2}:\d{2}:\d{2}$/', $time)) {
				$time = '"'. $time. '"';
			}
		} else {
			$time = 'NOW()';
		}
		$sids = dbNbs::get("SELECT stl.sid FROM @__subscribers_to_lists stl "
			. "INNER JOIN @__newsletters_to_lists ntl ON ntl.slid = stl.slid "
			. "WHERE ntl.nid = $nid", 'col');
		if(!empty($sids)) {
			$values = array();
			foreach($sids as $sid) {
				$values[] = array(
					'sid' => $sid, 'nid' => $nid, 'date_send' => $time,
				);
				//$values[] = "($sid, $nid, $time)";
			}
			// Update current queued count
			frameNbs::_()->getModule('newsletters')->getModel()->updateById(array(
				'queued' => count( $sids ),
			), $nid);
			// Add subscribers from newsletter - to queue
			return dbNbs::insertBatch('@__queue', array(
				'sid' => 'num', 'nid' => 'num', 'date_send' => 'func',
			), $values, "ON DUPLICATE KEY UPDATE date_send = VALUES(date_send)");
			/*return dbNbs::query("INSERT INTO @__queue (sid, nid, date_send) VALUES ". implode(', ', $values). " "
				. "ON DUPLICATE KEY UPDATE date_send = VALUES(date_send)");*/
		} else
			$this->pushError(__('Can not find subscribers for this Newsletter', NBS_LANG_CODE));
		return false;
	}
	public function checkClear($clearFor) {
		$values = array();
		foreach($clearFor as $cf) {
			$values[] = "(sid = {$cf['sid']} AND nid = {$cf['nid']})";
		}
		return dbNbs::query("DELETE FROM @__queue WHERE ". implode(" OR ", $values));
	}
}