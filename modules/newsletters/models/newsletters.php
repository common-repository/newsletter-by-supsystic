<?php
class newslettersModelNbs extends modelNbs {
	private $_sendOn = array();
	private $_statuses = array();
	private $_statusesById = array();

	private $_currNid = false;
	private $_currSid = false;

	public function __construct() {
		$this->_setTbl('newsletters');
	}
	/**
	 * Exclude some data from list - to avoid memory overload
	 */
	public function getSimpleList($where = array(), $params = array()) {
		if($where)
			$this->setWhere ($where);
		return $this->setSelectFields('id, oid, label')->getFromTbl( $params );
	}
	protected function _afterGetFromTbl($row) {
		if(isset($row['params'])) {
			$row['params'] = empty($row['params']) ? array() : utilsNbs::decodeArrayTxt($row['params']);
			// Set some defaults here
			if(empty($row['params']['main'])) {
				$row['params']['main'] = array('send_on' => 'immediately');
			}
			if(!empty($row['params']) && isset($row['params']['send']) && !empty($row['params']['send'])) {
				foreach($row['params']['send'] as $k => $v) {
					if(is_string($v)) {
						$row['params']['send'][$k] = stripslashes($v);
					}
				}
			}
		}
		return $row;
	}
	protected function _dataSave($data, $update = false) {
		if(isset($data['params']))
			$data['params'] = utilsNbs::encodeArrayTxt( $data['params'] );
		return $data;
	}
	public function create($d = array()) {
		$d['label'] = isset($d['label']) ? trim($d['label']) : '';
		$d['oid'] = isset($d['oid']) ? (int) $d['oid'] : 0;
		if(!empty($d['label'])) {
			if(!empty($d['oid'])) {
				if(!empty($d['slid'])) {
					$d = dbNbs::prepareHtmlIn($d);
					$octoModel = frameNbs::_()->getModule('octo')->getModel();
					$originalOcto = $octoModel->getById($d['oid']);
					$original = !empty($d['original']) ? $d['original'] : array();
					$oid = $octoModel->copy($originalOcto, array(), $original);
					if(!empty($oid)) {
						$params = !empty($d['original']['params']) ? $d['original']['params'] : array();
						$nid = $this->insert(array('label' => $d['label'], 'oid' => $oid, 'params' => $params));
						if($nid) {
							frameNbs::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('create_from_tpl.'. strtolower(str_replace(' ', '-', $originalOcto['label'])));
							$this->_bindToLists($nid, $d['slid']);
							return $nid;
						}

					} else
						$this->pushError(array_merge(array(__('Can not copy template data', NBS_LANG_CODE)), $octoModel->getErrors()));
				} else
					$this->pushError (__('Please select Newsletter Subscribe List(s)', NBS_LANG_CODE));
			} else
				$this->pushError (__('Please select Newsletter template from list below', NBS_LANG_CODE));
		} else
			$this->pushError (__('Please enter Name', NBS_LANG_CODE), 'label');
		return false;
	}
	private function _bindToLists($nid, $slid) {
		if(!is_array($slid)) $slid = array( $slid );
		$values = array();
		foreach($slid as $listId) {
			$values[] = "$nid, $listId";
			frameNbs::_()->getModule('subscribers_lists')->getModel()->updateNewslettersCnt($listId, 1);
		}
		dbNbs::query("INSERT INTO `@__newsletters_to_lists` (nid, slid) VALUES (". implode("),(", $values). ")");
		dbNbs::query("UPDATE @__newsletters "
			. "SET subscribers_cnt = ". frameNbs::_()->getModule('subscribers_lists')->getModel()->getSubscribersCnt( $slid ). " "
			. "WHERE id = $nid");
	}
	private function _unbindFromLists($nid) {
		// count this is our stats
		$prevLists = $this->getListsIds( $nid );
		if(!empty($prevLists)) {
			foreach($prevLists as $listId) {
				frameNbs::_()->getModule('subscribers_lists')->getModel()->updateNewslettersCnt($listId, -1);
			}
		}
		dbNbs::query("DELETE FROM `@__newsletters_to_lists` WHERE nid = $nid");
	}
	protected function _afterRemove($ids = array()) {
		parent::_afterRemove($ids);
		if(!empty($ids)) {
			// Don't need to send them something anymore
			frameNbs::_()->getModule('queue')->getModel()->clearForNewsletters( $ids );
		}
	}
	/**
	 * Do not remove pre-set templates
	 */
	public function clear() {
		if(frameNbs::_()->getTable( $this->_tbl )->delete(array('additionalCondition' => 'original_id != 0'))) {
			return true;
		} else
			$this->pushError (__('Database error detected', NBS_LANG_CODE));
		return false;
	}

	public function save($d = array()) {
		$this->getSendOn();
		$d['send_on'] = isset($d['params']['main']['send_on']) ? $this->_sendOn[ $d['params']['main']['send_on'] ]['id'] : 1;

		$res = $this->updateById($d);
		if($res) {
			$nid = $d['id'];
			// Re-bind newsletter to lists
			$this->_unbindFromLists( $nid );
			$slid = isset($d['slid']) ? $d['slid'] : false;
			if(!empty($slid)) {
				$this->_bindToLists( $nid, $slid );
			}
			dispatcherNbs::doAction('afterFormUpdate', $d);
		}
		return $res;
	}
	public function getListsIds( $nid ) {
		return frameNbs::_()->getTable('newsletters_to_lists')->get('slid', array('nid' => $nid), '', 'col');
	}
	public function getLists( $nid ) {
		return dbNbs::get("SELECT sl.* FROM @__subscribers_lists sl, @__newsletters_to_lists ntl WHERE sl.id = ntl.slid AND ntl.nid = $nid");
	}
	public function getFullById( $id ) {
		$newsletter = $this->getById( $id );
		if($newsletter) {
			$newsletter['slid'] = $this->getListsIds( $id );
		}
		return $newsletter;
	}
	public function getFullWithComposedTheme( $id, $clearThemeCache = false ) {
		$getOne = !is_array($id);
		if($getOne) $id = array($id);
		$newsletters = $this->setWhere('id IN ('. implode(', ', $id). ')')->getFromTbl();
		if(!empty($newsletters)) {
			$octoModel = frameNbs::_()->getModule('octo')->getModel();
			if($clearThemeCache) {
				$oids = array();
				foreach($newsletters as $n) {
					$oids[] = $n['oid'];
				}
				$octoModel->removeCache( $oids );
			}
			foreach($newsletters as $i => $n) {
				$newsletters[ $i ]['theme'] = $octoModel->generateInline( $n['oid'] );
			}
			return $getOne ? $newsletters[ 0 ] : $newsletters;
		}
		return false;
	}
	private function _sendGenSubLists( $subscriber ) {
		return dbNbs::get("SELECT GROUP_CONCAT(sl.label SEPARATOR ', ') AS all_lists_str "
			. "FROM @__subscribers_lists sl, @__subscribers_to_lists stl "
			. "WHERE sl.id = stl.slid AND stl.sid = ". (int) $subscriber['id']. "", 'one');
	}
	private function _sendGenUnsubscribeUrl( $subscriber, $nid ) {
		return uriNbs::mod('subscribers', 'unsubscribe', array(
			'id' => $subscriber['id'],
			'phash' => frameNbs::_()->getModule('subscribers')->getModel()->generatePubHash( $subscriber ),
			'nid' => $nid,
		));
	}
	/*public function generateOpenTrackerTest( $nid, $sid ) {
		return $this->_generateOpenTracker( $nid, $sid );
	}*/
	private function _generateOpenTracker( $nid, $sid ) {
		//return '<img src="'. uriNbs::mod('statistics', 'openImg', array('nid' => $nid, 'sid' => $sid, 'img' => '.jpg')). '" width="1" height="1" style="width: 1px; height: 1px; display: none;" />';
	}
	private function _generateClickTracker( $nid, $sid, $content ) {
		// Find all links and replace their href by adding tracking parameters
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		$this->_currNid = $nid;
		$this->_currSid = $sid;
		return preg_replace_callback("/$regexp/siU", array($this, 'replaceClickTrackerClb'), $content);
	}
	public function replaceClickTrackerClb( $matches ) {
		$url = isset($matches[ 2 ]) && !empty($matches[ 2 ]) ? trim($matches[ 2 ]) : false;
		if($url
			&& strpos($url, 'unsubscribe_url') === false	// Don't do this for unsubscribe urls
			&& strpos($url, 'subscriber_account_url') === false	// Don't do this for subscriber account urls
		) {
			$urlParams = array(
				'baseUrl' => $url,
				NBS_CODE. '_nid' => $this->_currNid,
				NBS_CODE. '_sid' => $this->_currSid,
				NBS_CODE. '_track_click' => 1,
			);
			$isExternal = strpos($url, NBS_SITE_ROOT_URL) === false;
			if($isExternal) {
				$urlParams['baseUrl'] = NBS_SITE_ROOT_URL;
				$urlParams[ NBS_CODE. '_ext_go' ] = str_replace('&amp;', '&', $url);
			}
			$newUrl = uriNbs::_( $urlParams );
			$matches[ 0 ] = str_replace( $url, $newUrl, $matches[ 0 ] );
		}
		return $matches[ 0 ];
	}
	/*public function testClickTracker( $nid, $sid ) {
		$newsletter = $this->getFullWithComposedTheme( $nid );
		$newsletter['theme'] = $this->_generateClickTracker( $newsletter['id'], $sid, $newsletter['theme'] );
		//var_dump($newsletter['theme']);
	}*/
	public function sendOne( $to, $newsletter, $subscriber = false ) {
		$subject = isset($newsletter['params']['send']['subject']) && !empty($newsletter['params']['send']['subject'])
			? trim($newsletter['params']['send']['subject'])
			: false;
		if(empty($subject)) {	// Maybe it contains only spaces - who know?)
			$subject = trim( $newsletter['label'] );
		}
		if(!empty($subscriber)) {	// Make all replacements for subscriber
			if(isset($newsletter['params']['main']['enb_open_track'])
				&& !empty($newsletter['params']['main']['enb_open_track'])
			) {
				$newsletter['theme'] .= $this->_generateOpenTracker( $newsletter['id'], $subscriber['id'] );
			}
			if(isset($newsletter['params']['main']['enb_click_track'])
				&& !empty($newsletter['params']['main']['enb_click_track'])
			) {
				$newsletter['theme'] = $this->_generateClickTracker( $newsletter['id'], $subscriber['id'], $newsletter['theme'] );
			}
			$variables = array(
				'subscribed_to_lists' => $this->_sendGenSubLists( $subscriber ),
				'unsubscribe_url' => $this->_sendGenUnsubscribeUrl( $subscriber, $newsletter['id'] ),
				'subscriber_account_url' => frameNbs::_()->getModule('subscribers')->getModel()->generateLoginUrl( $subscriber ),
				'user_username' => $subscriber['username'],
				'user_email' => $subscriber['email'],
			);
			if(!empty($subscriber['all_data'])) {
				foreach($subscriber['all_data'] as $sKey => $sVal) {
					$variables[ 'user_'. $sKey ] = $sVal;
				}
			}
			$newsletter['theme'] = utilsNbs::replaceVariables($newsletter['theme'], $variables);
			$subject = utilsNbs::replaceVariables($subject, $variables);
			frameNbs::_()->getModule('log')->addLine("subject: $subject to {$variables['user_username']}");
		}
		$sendParams = array();
		$reAssignSendKeys = array('from_name', 'from_email', 'reply_to_name', 'reply_to_email', 'return_path_email');
		foreach($reAssignSendKeys as $rKey) {
			if(isset($newsletter['params']['send'][ $rKey ])) {
				$newsletter['params']['send'][ $rKey ] = trim( $newsletter['params']['send'][ $rKey ] );
				if(!empty($newsletter['params']['send'][ $rKey ])) {
					$sendParams[ $rKey ] = trim($newsletter['params']['send'][ $rKey ]);
				}
			}
		}

		$res = frameNbs::_()->getModule('mail')->send(
			$to,
			$subject,
			$newsletter['theme'],
			$sendParams);
		if(!$res) {
			$this->pushError( frameNbs::_()->getModule('mail')->getMailErrors() );
		}
		return $res;
	}
	public function updateParamsById($d) {
		foreach($d as $k => $v) {
			if(!in_array($k, array('id', 'params')))
				unset($d[ $k ]);
		}
		return $this->updateById($d);
	}
	public function changeTpl($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		$d['new_tpl_id'] = isset($d['new_tpl_id']) ? (int) $d['new_tpl_id'] : 0;
		if($d['id'] && $d['new_tpl_id']) {
			$octoModel = frameNbs::_()->getModule('octo')->getModel();
			$originalOcto = $octoModel->getById($d['new_tpl_id']);
			$oid = $octoModel->copy($originalOcto);
			if(!empty($oid)) {
				// TODO: We need to do something with old templates here, but I didn't deside for now - what exactly: delete them (bad solution),
				// or rename - like "Theme Name Old 1" - and leave as it is in database (good as for me)
				frameNbs::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('change_to_tpl.'. strtolower(str_replace(' ', '-', $originalOcto['label'])));
				return $this->updateById(array('oid' => $oid), $d['id']);
			} else
				$this->pushError(array_merge(array(__('Can not copy template data', NBS_LANG_CODE)), $octoModel->getErrors()));
		} else
			$this->pushError (__('Provided data was corrupted', NBS_LANG_CODE));
		return false;
	}
	private function _assignKeyArr($from, &$to, $key) {
		$subKeys = explode('.', $key);
		// Yeah, hardcode, I know.............
		switch(count($subKeys)) {
			case 4:
				if(isset( $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ] ))
					$to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ] = $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ];
				else
					unset($to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ][ $subKeys[3] ]);
				break;
			case 3:
				if(isset( $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ] ))
					$to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ] = $from[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ];
				else
					unset($to[ $subKeys[0] ][ $subKeys[1] ][ $subKeys[2] ]);
				break;
			case 2:
				if(isset( $from[ $subKeys[0] ][ $subKeys[1] ] ))
					$to[ $subKeys[0] ][ $subKeys[1] ] = $from[ $subKeys[0] ][ $subKeys[1] ];
				else
					unset($to[ $subKeys[0] ][ $subKeys[1] ]);
				break;
			case 1:
				if(isset( $from[ $subKeys[0] ] ))
					$to[ $subKeys[0] ] = $from[ $subKeys[0] ];
				else
					unset( $to[ $subKeys[0] ] );
				break;
		}
	}
	public function getDifferences($newsletters, $original) {
		$difsFromOriginal = $this->_computeDifferences($newsletters, $original);
		$difsOfOriginal = $this->_computeDifferences($original, $newsletters);	// Some options may be present in original, but not present in current newsletters
		if(!empty($difsFromOriginal) && empty($difsOfOriginal)) {
			return $difsFromOriginal;
		} elseif(empty($difsFromOriginal) && !empty($difsOfOriginal)) {
			return $difsOfOriginal;
		} else {
			$difs = array_merge($difsFromOriginal, $difsOfOriginal);
			return array_unique($difs);
		}
	}
	private function _computeDifferences($newsletters, $original, $key = '', $keysImplode = array()) {
		$difs = array();
		if(is_array($newsletters)) {
			$excludeKey = array('id', 'label', 'active', 'original_id', 'img_preview',
				'date_created', 'view_id', 'img_preview_url', 'show_on', 'show_to', 'show_pages');
			if(!empty($key))
				$keysImplode[] = $key;
			foreach($newsletters as $k => $v) {
				if(in_array($k, $excludeKey) && empty($key)) continue;
				if(!isset($original[ $k ])) {
					$difs[] = $this->_prepareDiffKeys($k, $keysImplode);
					continue;
				}
				$currDifs = $this->_computeDifferences($newsletters[ $k ], $original[ $k ], $k, $keysImplode);
				if(!empty($currDifs)) {
					$difs = array_merge($difs, $currDifs);
				}
			}
		} else {
			if($newsletters != $original) {
				$difs[] = $this->_prepareDiffKeys($key, $keysImplode);
			}
		}
		return $difs;
	}
	private function _prepareDiffKeys($key, $keysImplode) {
		return empty($keysImplode) ? $key : implode('.', $keysImplode). '.'. $key;
	}
	public function saveAsCopy($d = array()) {
		$d['copy_label'] = isset($d['copy_label']) ? trim($d['copy_label']) : '';
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if(!empty($d['copy_label'])) {
			if(!empty($d['id'])) {
				$original = $this->getById( $d['id'] );
				$originalOctoId = frameNbs::_()->getModule('octo')->getModel()->getOriginalOctoId( $original['oid'] );;
				return $this->create(array(
					'label' => $d['copy_label'],
					'oid' => $originalOctoId,
					'slid' => $this->getListsIds( $d['id'] ),
                    'original' => $original
				));
			} else
				$this->pushError (__('Invalid ID', NBS_LANG_CODE));
		} else
			$this->pushError (__('Please enter Name', NBS_LANG_CODE), 'copy_label');
		return false;
	}
	public function switchActive($d = array()) {
		$d['active'] = isset($d['active']) ? (int)$d['active'] : 0;
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if(!empty($d['id'])) {
			$tbl = $this->getTbl();
			return frameNbs::_()->getTable($tbl)->update(array(
				'active' => $d['active'],
			), array(
				'id' => $d['id'],
			));
		} else
			$this->pushError (__('Invalid ID', NBS_LANG_CODE));
		return false;
	}
	public function updateLabel($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if(!empty($d['id'])) {
			$d['label'] = isset($d['label']) ? trim($d['label']) : '';
			if(!empty($d['label'])) {
				return $this->updateById(array(
					'label' => $d['label']
				), $d['id']);
			} else
				$this->pushError (__('Name can not be empty', NBS_LANG_CODE));
		} else
			$this->pushError (__('Invalid ID', NBS_LANG_CODE));
		return false;
	}
	public function setSimpleGetFields() {
		//$this->setSelectFields('id, label, date_created, sort_order, status');
		return parent::setSimpleGetFields();
	}
	public function getSendOn() {
		if(empty($this->_sendOn)) {
			$this->_sendOn = array(
				'immediately' => array('id' => 1),
				'new_content' => array('id' => 2),
				'new_subscriber' => array('id' => 3),
			);
		}
		return $this->_sendOn;
	}
	public function getSendOnByCode( $code, $key = false ) {
		$this->getSendOn();
		if(isset($this->_sendOn[ $code ])) {
			return $key ? $this->_sendOn[ $code ][ $key ] : $this->_sendOn[ $code ];
		}
		return false;
	}
	public function getStatuses() {
		if(empty($this->_statuses)) {
			$this->_statuses = array(
				'disabled' => array('id' => 0, 'label' => __('Disabled', NBS_LANG_CODE)),
				'activated' => array('id' => 1, 'label' => __('Setup in Progress', NBS_LANG_CODE)),
				'sending' => array('id' => 2, 'label' => __('Sending', NBS_LANG_CODE)),
				'pause' => array('id' => 3, 'label' => __('Paused', NBS_LANG_CODE)),
				'waiting' => array('id' => 4, 'label' => __('Waiting', NBS_LANG_CODE)),
				'complete' => array('id' => 5, 'label' => __('Complete', NBS_LANG_CODE)),
			);
		}
		return $this->_statuses;
	}
	public function getStatusByCode( $code, $key = false ) {
		$this->getStatuses();
		if(isset($this->_statuses[ $code ])) {
			return $key ? $this->_statuses[ $code ][ $key ] : $this->_statuses[ $code ];
		}
		return false;
	}
	public function getStatusById( $id ) {
		$this->getStatuses();
		if(empty($this->_statusesById)) {
			foreach($this->_statuses as $c => $s) {
				$this->_statusesById[ $s['id'] ] = array_merge($s, array('code' => $c));
			}
		}
		return isset($this->_statusesById[ $id ]) ? $this->_statusesById[ $id ] : false;
	}
	public function startSend($d = array()) {
		$id = isset($d['id']) ? (int) $d['id'] : false;
		if($id) {
			$newsletter = $this->getById( $id );
			$this->getSendOn();
			$sendStatus = null;
			switch($newsletter['send_on']) {
				case $this->_sendOn['immediately']['id']:
					$queueModel = frameNbs::_()->getModule('queue')->getModel();
					if(!$queueModel->addNewsletter( $id )) {
						$this->pushError( $queueModel->getErrors() );
					}
					$sendStatus = $this->getStatusByCode('sending');
					break;
				case $this->_sendOn['new_content']['id']:
					// Nothing to do for now - it will be added by actions when posting new content
					$sendStatus = $this->getStatusByCode('waiting');
					break;
			}
			// Sending in progress
			$this->updateById(array('status' => $sendStatus['id']), $id);
			frameNbs::_()->getModule('log')->addLine("newsletter $id started sending");
			if(!$this->haveErrors()) {
				return true;
			} else {
                //$this->pushError($queueModel->getErrors());
            }
		} else
			$this->pushError(__('Send me some data pls', NBS_LANG_CODE));
		return false;
	}
	public function setPause( $id ) {
		frameNbs::_()->getModule('log')->addLine("newsletter $id paused");
		$this->updateById(array('status' => $this->getStatusByCode('pause', 'id')), $id);
		frameNbs::_()->getModule('queue')->getModel()->clearForNewsletters( $id );
	}
	public function sendTest( $d = array() ) {
		$id = isset($d['id']) ? (int) $d['id'] : false;
		if($id) {
			$newsletter = $this->getFullWithComposedTheme( $id );
			if($newsletter) {
				return $this->sendOne( $newsletter['params']['tpl']['send_test'], $newsletter );
			} else
				$this->pushError (__('Can not find required Newsletter', NBS_LANG_CODE));
		} else
			$this->pushError (__('Invalid ID', NBS_LANG_CODE));
		return false;
	}
}
