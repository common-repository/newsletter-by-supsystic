<?php
class subscribersModelNbs extends modelNbs {
	private $_statuses = array();

	private $_authCookieName = 'nbs_love_you';
	public function __construct() {
		$this->_setTbl('subscribers');
	}
	/**
	 * Exclude some data from list - to avoid memory overload
	 */
	public function getSimpleList($where = array(), $params = array()) {
		if($where)
			$this->setWhere ($where);
		return $this->setSelectFields('id, username, email')->getFromTbl( $params );
	}
	public function getStatuses() {
		if(empty($this->_statuses)) {
			$this->_statuses = array(
				'disabled' => array('id' => 0, 'label' => __('Disabled', NBS_LANG_CODE)),
				'enabled' => array('id' => 1, 'label' => __('Enabled', NBS_LANG_CODE)),
			);
		}
		return $this->_statuses;
	}
	public function getStatusByCode( $code ) {
		$this->getStatuses();
		return isset($this->_statuses[ $code ]) ? $this->_statuses[ $code ] : false;
	}
	/**
	 * Do not remove pre-set templates
	 */
	public function clear() {
		if(frameNbs::_()->getTable( $this->_tbl )->delete()) {
			return true;
		} else
			$this->pushError (__('Database error detected', NBS_LANG_CODE));
		return false;
	}
	public function save($d = array()) {
		$d['username'] = isset($d['username']) ? trim($d['username']) : false;
		$d['email'] = isset($d['email']) ? strtolower(trim($d['email'])) : false;
		$d['status'] = isset($d['status']) ? (int) $d['status'] : $this->_statuses['disabled']['id'];
		$update = isset($d['id']) && !empty($d['id']) ? (int) $d['id'] : false;
		if(empty($d['username'])
			&& isset($d['username_from_email'])
			&& $d['username_from_email']
			&& !empty($d['email'])
		) {
			$d['username'] = $this->getNameFromEmail($d['email']);
		}
		if(!empty($d['username']) || (isset($d['ignore_username']) && $d['ignore_username'])) {
			if(!empty($d['email'])) {
				$d = dbNbs::prepareHtmlIn($d);
				$data = array(
					'wp_id' => (isset($d['wp_id']) ? $d['wp_id'] : 0),
					'username' => $d['username'],
					'email' => $d['email'],
					'status' => $d['status'],
					'hash' => (isset($d['hash']) ? $d['hash'] : $this->_generateHash()),
					'all_data' => (isset($d['all_data']) ? utilsNbs::serialize($d['all_data']) : ''),
				);
				if(isset($d['form_id'])) {
					$data['form_id'] = (int) $d['form_id'];
				}
				$res = $update ? $this->updateById($data, $d['id']) : $this->insert($data);

				if($res) {
					$sid = $update ? $d['id'] : $res;
					$data['id'] = $sid;
					if(!isset($d['ignore_lists_bind'])) {
						// Re-bind subscribers to lists
						$this->_unbindFromLists( $sid );
						$slid = isset($d['slid']) ? $d['slid'] : false;
						if(!empty($slid)) {
							$this->_bindToLists( $sid, $slid );
						}
					}
					if(isset($d['send_confirm']) && $d['send_confirm']) {
						$this->sendConfirmEmail($data);
					}
					return $sid;
				}
			} else
				$this->pushError (__('Empty or invalid Email', NBS_LANG_CODE));
		} else
			$this->pushError (__('Empty or invalid Username', NBS_LANG_CODE));
		return false;
	}
	public function generateConfirmUrl( $subscriber ) {
		return uriNbs::mod('subscribers', 'confirmByUrl', array(
			'id' => $subscriber['id'],
			'phash' => $this->generatePubHash( $subscriber ),
		));
	}
	public function sendConfirmEmail( $subscriber ) {
		$subject = frameNbs::_()->getModule('options')->get('subscribers_confirm_email_sbj');
		$msg = frameNbs::_()->getModule('options')->get('subscribers_confirm_email_txt');
		$sitename = wp_specialchars_decode(get_bloginfo('name'));
		$variables = array(
			'subscriber_confirm_url' => $this->generateConfirmUrl( $subscriber ),
			'user_username' => $subscriber['username'],
			'user_email' => $subscriber['email'],
			'siteurl' => get_site_url(),
			'sitename' => $sitename,
		);
		if(!empty($subscriber['all_data'])) {
			foreach($subscriber['all_data'] as $sKey => $sVal) {
				$variables[ 'user_'. $sKey ] = $sVal;
			}
		}
		$subject = utilsNbs::replaceVariables($subject, $variables);
		$msg = utilsNbs::replaceVariables($msg, $variables);
		$email = $subscriber['email'];
		$sendParams = array();
		$sendParams['from_name'] = $sendParams['reply_to_name'] = $sitename;
		$sendParams['from_email'] = $sendParams['reply_to_email'] = get_bloginfo('admin_email');
		$res = frameNbs::_()->getModule('mail')->send(
			$email,
			$subject,
			$msg,
			$sendParams);
		if(!$res) {
			$this->pushError( frameNbs::_()->getModule('mail')->getMailErrors() );
		}
		return $res;
	}
	private function _generateHash() {
		return md5('egweg32g3h092g2#@'. mt_rand(1, 999999));
	}
	public function emailExists($email) {
		$email = strtolower($email);
		return dbNbs::get("SELECT id FROM @__subscribers WHERE email = '$email'", 'one');
	}
	public function batchImport($d = array()) {
		$this->getStatuses();
		$listId = isset($d['list_id']) ? $d['list_id'] : false;
		$insertedIds = array();
		$updatedIds = array();
		foreach($d['emails'] as $i => $email) {
			$email = strtolower(trim($email));
			if(empty($email)) continue;
			$id = $this->emailExists( $email );
			$update = $id ? true : false;
			$username = (isset($d['usernames']) && isset($d['usernames'][ $i ]))
					? $d['usernames'][ $i ]
					: $this->getNameFromEmail($email);
			$saveData = array(
					'username' => $username,
					'email' => $email,
					'status' => $this->_statuses['enabled']['id'],
					'ignore_lists_bind' => true,
					'all_data' => (isset($d['all_data'], $d['all_data'][ $i ]) ? $d['all_data'][ $i ] : array()),
					'wp_id' => (isset($d['wp_id'], $d['wp_id'][ $i ]) ? $d['wp_id'][ $i ] : 0),
				);
			if($id) {
				$saveData['id'] = $id;
			}
			$saveRes = $this->save( $saveData );
			if($saveRes) {
				if($update) {
					$updatedIds[] = $id;
				} else {
					$insertedIds[] = $saveRes;
				}
			}
		}
		if(!empty($listId) && !empty($insertedIds)) {
			frameNbs::_()->getModule('subscribers_lists')->getModel()->updateSubscribersCnt($listId, count($insertedIds));
		}
		if(!empty($listId) && (!empty($insertedIds) || !empty($updatedIds))) {
			$values = array();
			if(!empty($insertedIds)) {
				foreach($insertedIds as $sid) {
					$values[] = array('sid' => $sid, 'slid' => $listId);
				}
			}
			if(!empty($updatedIds)) {
				foreach($updatedIds as $sid) {
					$values[] = array('sid' => $sid, 'slid' => $listId);
				}
			}
			dbNbs::insertBatch('@__subscribers_to_lists', array(
				'sid' => 'num', 'slid' => 'num',
			), $values, "ON DUPLICATE KEY UPDATE sid = sid");
		}
		if(!empty($insertedIds) || !empty($updatedIds)) {
			return array('inserted' => count( $insertedIds ), 'updated' => count( $updatedIds ));
		}
		return false;
	}
	public function getNameFromEmail($email) {
		$nameEmail = explode('@', $email);
		return $nameEmail[ 0 ];
	}
	private function _bindToLists( $sid, $slid ) {
		if(!is_array($slid)) $slid = array( $slid );
		$values = array();
		foreach($slid as $listId) {
			$values[] = "$sid, $listId";
			frameNbs::_()->getModule('subscribers_lists')->getModel()->updateSubscribersCnt($listId, 1);
		}
		dbNbs::query("INSERT INTO `@__subscribers_to_lists` (sid, slid) VALUES (". implode("),(", $values). ")");
	}
	private function _unbindFromLists( $sid, $slid = false ) {
		if($slid && !is_array($slid)) $slid = array( $slid );
		// count this is our stats
		$prevLists = $slid ? $slid : $this->getListsIds( $sid );
		if(!empty($prevLists)) {
			foreach($prevLists as $listId) {
				frameNbs::_()->getModule('subscribers_lists')->getModel()->updateSubscribersCnt($listId, -1);
			}
			$listsWhere = "";
			if($slid) {
				$listsWhere = " AND slid IN (". implode(',', $slid). ")";
			}
			dbNbs::query("DELETE FROM `@__subscribers_to_lists` WHERE sid = $sid $listsWhere");
		}
	}
	protected function _afterRemove($ids = array()) {
		parent::_afterRemove($ids);
		if(!empty($ids)) {
			if(!is_array($ids)) $ids = array( $ids );
			foreach($ids as $id) {
				$this->_unbindFromLists( $id );
			}
			// Don't need to send them something anymore
			frameNbs::_()->getModule('queue')->getModel()->clearForSubscribers( $ids );
		}

	}
	public function getListsIds( $sid ) {
		return frameNbs::_()->getTable('subscribers_to_lists')->get('slid', array('sid' => $sid), '', 'col');
	}
	public function getPrevListsIds( $sid ) {
		return frameNbs::_()->getTable('subscribers_to_lists_prev')->get('slid', array('sid' => $sid), '', 'col');
	}
	public function getLists( $sid ) {
		return dbNbs::get("SELECT sl.* FROM @__subscribers_lists sl, @__subscribers_to_lists stl WHERE sl.id = stl.slid AND stl.sid = $sid");
	}
	public function getFullById( $id ) {
		$subscriber = $this->getById( $id );
		if($subscriber) {
			$subscriber['slid'] = $this->getListsIds( $id );
		}
		return $subscriber;
	}
	protected function _afterGetFromTbl($row) {
		$row = parent::_afterGetFromTbl($row);
		if(!empty($row['all_data']) && is_string($row['all_data'])) {
			$row['all_data'] = utilsNbs::unserialize($row['all_data']);
		}
		return $row;
	}
	public function duplicateFromWpUser( $wpUser, $params = array() ) {
		if(!isset($params['status_id'])) {
			$subscribedStatus = $this->getStatusByCode('enabled');
			$subscribedStatusId = $subscribedStatus['id'];
		} else {
			$subscribedStatusId = $params['status_id'];
		}
		$collectAllDataKeys = array('first_name', 'last_name', 'nickname', 'description');
		$allData = array();
		foreach($collectAllDataKeys as $dKey) {
			$allData[ $dKey ] = $wpUser->$dKey;
		}
		$update = false;
		$sid = false;
		$setData = array(
			'username' => $wpUser->display_name,
			'email' => $wpUser->user_email,
			'status' => $subscribedStatusId,
			'wp_id' => $wpUser->ID,
			'all_data' => $allData,
		);
		if(!isset($params['ignore_exist_check'])) {
			$sid = $this->setSelectFields('id')->setWhere(array('wp_id' => $wpUser->ID))->getFromTbl(array('return' => 'one'));
			if($sid) {
				$setData['id'] = $sid;
				$update = true;
			}
		}
		$res = $this->save($setData);
		if($res) {
			if(!$update) {
				$sid = $res;
			}
			if(!isset($params['ignore_list_bind']) && !$update) {
				$this->_bindToLists( $sid, frameNbs::_()->getModule('subscribers_lists')->getModel()->getWpListId() );
			}
			return $sid;
		}
		return false;
	}
	public function generatePubHash( $subscriber ) {
		if(is_numeric($subscriber)) {
			$subscriber = $this->getById( $subscriber );
		}
		return $subscriber ? md5($subscriber['hash']. ':'. $subscriber['id']) : false;
	}
	public function unsubscribeFromList( $sid, $slids ) {
		$this->_unbindFromLists( $sid, $slids );
		// This was manual user unsubscribe - remember his prev. subscription lists
		foreach($slids as $slid) {
			frameNbs::_()->getTable('subscribers_to_lists_prev')->insert(array(
				'sid' => $sid,
				'slid' => $slid,
			));
		}
		return true;
	}
	public function unsubscribe( $d = array() ) {
		$id = isset($d['id']) ? (int) $d['id'] : false;
		$nid = isset($d['nid']) ? (int) $d['nid'] : false;
		$phash = isset($d['phash']) ? trim($d['phash']) : false;
		if($id && $nid && $phash) {
			$subscriber = $this->getById( $id );
			if($subscriber && $this->generatePubHash( $subscriber ) == $phash) {
				$slids = frameNbs::_()->getModule('newsletters')->getModel()->getListsIds( $nid );
				if($slids && $this->unsubscribeFromList($id, $slids)) {
					$subscriber['unsubscribed_slids'] = $slids;
					return $subscriber;
				}
			} else
				$this->pushError (__('Invalid subscriber', NBS_LANG_CODE));
		} else
			$this->pushError (__('Some data is missing here', NBS_LANG_CODE));
		return false;
	}
	public function login( $subscriber ) {
		reqNbs::setVar($this->_authCookieName, $this->generatePubHash($subscriber). ':'. $subscriber['id'], 'cookie', array('expire' => 14 * DAY_IN_SECONDS));
	}
	public function generateLoginUrl( $subscriber ) {
		return uriNbs::mod('subscribers', 'loginByUrl', array(
			'id' => $subscriber['id'],
			'phash' => $this->generatePubHash( $subscriber ),
		));
	}
	public function sendLoginUrl( $d = array() ) {
		$email = isset($d['email']) ? strtolower(trim( $d['email'] )) : false;
		if($email) {
			$subscriber = $this->getOneBy('email', $email);
			if($subscriber) {
				$subject = frameNbs::_()->getModule('options')->get('subscribers_login_email_sbj');
				$msg = frameNbs::_()->getModule('options')->get('subscribers_login_email_txt');
				$variables = array(
					'subscriber_login_url' => $this->generateLoginUrl( $subscriber ),
					'user_username' => $subscriber['username'],
					'user_email' => $subscriber['email'],
				);
				if(!empty($subscriber['all_data'])) {
					foreach($subscriber['all_data'] as $sKey => $sVal) {
						$variables[ 'user_'. $sKey ] = $sVal;
					}
				}
				$subject = utilsNbs::replaceVariables($subject, $variables);
				$msg = utilsNbs::replaceVariables($msg, $variables);
				$sendParams = array();
				$sendParams['from_name'] = $sendParams['reply_to_name'] = wp_specialchars_decode(get_bloginfo('name'));
				$sendParams['from_email'] = $sendParams['reply_to_email'] = get_bloginfo('admin_email');
				$res = frameNbs::_()->getModule('mail')->send(
					$email,
					$subject,
					$msg,
					$sendParams);
				if(!$res) {
					$this->pushError( frameNbs::_()->getModule('mail')->getMailErrors() );
				}
				return $res;
			} else
				$this->pushError(__('Can\'t find this Email', NBS_LANG_CODE));
		} else
			$this->pushError(__('Empty or invalid Email', NBS_LANG_CODE));
		return false;
	}
	public function isLoggedInId() {
		$pubHash = $this->getLoggedPubHashId();
		if($pubHash) {
			$hashId = explode(':', $pubHash);
			return isset($hashId[ 1 ]) ? $hashId[ 1 ] : false;
		}
		return false;
	}
	public function loggedInPubHash() {
		$pubHash = $this->getLoggedPubHashId();
		if($pubHash) {
			$hashId = explode(':', $pubHash);
			return isset($hashId[ 0 ]) ? $hashId[ 0 ] : false;
		}
		return false;
	}
	public function isLoggedIn() {
		$pubHash = $this->getLoggedPubHashId();
		return $pubHash ? true : false;
	}
	public function getLoggedPubHashId() {
		return reqNbs::getVar( $this->_authCookieName, 'cookie' );
	}
	public function updateProfile( $d = array() ) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : false;
		$fields = isset($d['fields']) ? $d['fields'] : false;
		if($d['id'] && $fields) {
			$fields['email'] = isset($fields['email']) ? strtolower(trim($fields['email'])) : false;
			if(!empty($fields['email'])) {
				$data = array(
					'email' => $fields['email'],
				);
				$res = $this->updateById($data, $d['id']);
				if($res) {
					$slids = isset($d['slid']) ? array_keys( $d['slid'] ) : false;
					$prevSlid = $this->getListsIds( $d['id'] );
					if($prevSlid != $slids) {
						$unsubscribeSlids = $subscribeSlids = array();
						if($prevSlid) {
							foreach($prevSlid as $slid) {
								if(!in_array($slid, $slids)) {
									$unsubscribeSlids[] = $slid;
								}
							}
						}
						if($slids) {
							foreach($slids as $slid) {
								if(!in_array($slid, $prevSlid)) {
									$subscribeSlids[] = $slid;
								}
							}
						}
						if($unsubscribeSlids) {
							$this->unsubscribeFromList($d['id'], $unsubscribeSlids);
						}
						if($subscribeSlids) {
							$this->_bindToLists($d['id'], $subscribeSlids);
						}
					}
					return true;
				}
			} else
				$this->pushError (__('Empty or invalid Email', NBS_LANG_CODE));

		} else
			$this->pushError (__('Empty or invalid ID', NBS_LANG_CODE));
		return false;
	}
	public function loginByUrl( $d = array() ) {
		$id = isset($d['id']) ? (int) $d['id'] : false;
		$phash = isset($d['phash']) ? trim($d['phash']) : false;
		if($id && $phash) {
			$subscriber = $this->getById( $id );
			if($subscriber && $this->generatePubHash( $subscriber ) == $phash) {
				$this->login( $subscriber );
				return true;
			} else
				$this->pushError (__('Invalid subscriber', NBS_LANG_CODE));
		} else
			$this->pushError (__('Some data is missing here', NBS_LANG_CODE));
		return false;
	}
	public function confirmByUrl( $d = array() ) {
		$id = isset($d['id']) ? (int) $d['id'] : false;
		$phash = isset($d['phash']) ? trim($d['phash']) : false;
		if($id && $phash) {
			$subscriber = $this->getById( $id );
			if($subscriber && $this->generatePubHash( $subscriber ) == $phash) {
				$this->confirm( $subscriber );
				return true;
			} else
				$this->pushError (__('Invalid subscriber', NBS_LANG_CODE));
		} else
			$this->pushError (__('Some data is missing here', NBS_LANG_CODE));
		return false;
	}
	public function confirm( $subscriber ) {
		$this->getStatuses();
		return $this->updateById(array(
			'status' => $this->_statuses['enabled']['id'],
		), $subscriber['id']);
	}
	public function getAllSubscribers() {
		return $this->setSelectFields('email, username, date_created')->getFromTbl();
	}
}
