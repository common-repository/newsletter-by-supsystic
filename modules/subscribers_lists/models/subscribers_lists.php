<?php
class subscribers_listsModelNbs extends modelNbs {
	public function __construct() {
		$this->_setTbl('subscribers_lists');
	}
	/**
	 * Exclude some data from list - to avoid memory overload
	 */
	public function getSimpleList($where = array(), $params = array()) {
		if($where)
			$this->setWhere ($where);
		return $this->setSelectFields('id, label, unique_id, subscribers_cnt')->getFromTbl( $params );
	}
	public function save($d = array()) {
		$d = dbNbs::prepareHtmlIn($d);
		$res = $this->updateById($d);
		if($res) {
			dispatcherNbs::doAction('afterFormUpdate', $d);
		}
		return $res;
	}
	protected function _afterRemove($ids = array()) {
		parent::_afterRemove($ids);
		if(!empty($ids)) {
			// Don't need to send them something anymore
			frameNbs::_()->getModule('queue')->getModel()->clearForSubscribersLists( $ids );
		}
	}
	public function create($d = array()) {
		$d['label'] = isset($d['label']) ? trim($d['label']) : false;
		if($d['label']) {
			return $this->insert(array(
				'label' => $d['label'],
				'unique_id' => (isset($d['unique_id']) ? $d['unique_id'] : ''),
				'description' => (isset($d['description']) ? $d['description'] : ''),
			));
		} else
			$this->pushError (__('Please enter Name', NBS_LANG_CODE), 'label');
		return false;
	}
	public function createIfNotExists($d = array()) {
		$d['label'] = isset($d['label']) ? trim($d['label']) : false;
		if($d['label']) {
			// Check list with same name - in database
			$existsId = $this
				->setSelectFields('id')
				->setWhere(array('label' => $d['label']))
				->getFromTbl(array('return' => 'one'));
			if($existsId) {
				return $existsId;
			}
			return $this->create( $d );
		} else
			$this->pushError (__('Please enter Name', NBS_LANG_CODE), 'label');
		return false;
	}
	public function importFromTxt($d = array()) {
		$d['emails'] = isset($d['emails']) ? trim($d['emails']) : false;
		$id = isset($d['id']) ? (int) $d['id'] : false;
		if($id) {
			if($d['emails']) {
				$emails = array_map('trim', explode(',', $d['emails']));
				$subModel = frameNbs::_()->getModule('subscribers')->getModel();
				if(($importedCnt = $subModel->batchImport(array('emails' => $emails, 'list_id' => $id)))) {
					return $importedCnt;
				} else
					$this->pushError ($subModel->getErrors());
			} else
				$this->pushError (__('Empty emails list', NBS_LANG_CODE));
		} else
			$this->pushError (__('Provide list ID', NBS_LANG_CODE));
		return false;
	}
	public function importFromCsv( $d ) {
		$id = isset($d['id']) ? (int) $d['id'] : false;
		if(!empty($id)) {
			$filePath = isset($d['file_path']) ? $d['file_path'] : false;
			if(!empty($filePath)) {
				importClassNbs('csvgeneratorNbs');
				$csvGenerator = toeCreateObjNbs('csvgeneratorNbs', array(''));

				$fileArray = array();
				$handle = fopen($filePath, 'r');
				$csvParams['delimiter'] = isset($d['csv_delimiter']) && !empty($d['csv_delimiter'])
					? $d['csv_delimiter'] : $csvGenerator->getDelimiter();
				$csvParams['enclosure'] = isset($d['csv_enclosure']) && !empty($d['csv_enclosure'])
					? $d['csv_enclosure'] : $csvGenerator->getEnclosure();
				$csvParams['escape'] = isset($d['csv_escape']) && !empty($d['csv_escape'])
					? $d['csv_escape'] : $csvGenerator->getEscape();

				if(version_compare( phpversion(), '5.3.0' ) == -1) { //for PHP lower than 5.3.0 third parameter - escape - is not implemented
					while($row = @fgetcsv( $handle, 0, $csvParams['delimiter'], $csvParams['enclosure'] )) {
						$fileArray[] = $row;
					}
				} else {
					while($row = @fgetcsv( $handle, 0, $csvParams['delimiter'], $csvParams['enclosure'], $csvParams['escape'] )) {
						$fileArray[] = $row;
					}
				}
				if(!empty($fileArray)) {
					if(count($fileArray) > 1) {
						$keys = array_shift($fileArray);	// TODO: Here will be additional field parsed in future
						foreach($keys as $i => $k) {
							$keys[ $i ] = trim($keys[ $i ], '"');
						}
						$emails = array();
						$allDatas = array();
						$usernames = array();
						foreach($fileArray as $i => $row) {
							foreach($row as $j => $cell) {
								$val = trim(dbNbs::prepareHtmlIn( $cell ));
								switch( strtolower($keys[ $j ]) ) {
									case 'email': case 'user_email': case 'Email Address':
										$emails[ $i ] = $val;
										break;
									case 'username':
										$usernames[ $i ] = $val;
										break;
									default:
										if(!isset($allDatas[ $i ])) $allDatas[ $i ] = array();
										$allDatas[ $i ][ $keys[ $j ] ] = $val;
										break;
								}
							}
						}
						$subModel = frameNbs::_()->getModule('subscribers')->getModel();
						if(($importedCnt = $subModel->batchImport(array(
								'emails' => $emails,
								'usernames' => $usernames,
								'all_data' => $allDatas,
								'list_id' => $id,
						)))) {
							return $importedCnt;
						} else
							$this->pushError ($subModel->getErrors());
					} else
						$this->pushError (__('File should contain more then 1 row, at least 1 row should be for headers', NBS_LANG_CODE));
				} else
					$this->pushError (__('Empty data in file, or invalid file type', NBS_LANG_CODE));
			} else
				$this->pushError (__('Empty file path', NBS_LANG_CODE));
		} else
			$this->pushError (__('Provide list ID', NBS_LANG_CODE));
		return false;
	}
	private function _bindToSubscribers($sid, $slid) {
		if(!is_array($sid)) $sid = array( $sid );
		$values = array();
		foreach($sid as $subId) {
			$values[] = "$subId, $slid";
		}
		$this->updateSubscribersCnt( $slid, count($values) );
		dbNbs::query("INSERT INTO `@__subscribers_to_lists` (sid, slid) VALUES (". implode("),(", $values). ")");
	}
	public function addSubscriberToLists($sid, $slids) {
		if(!is_array($slids)) $slids = array( $slids );
		$sid = (int) $sid;
		if ($sid) {
			$values = array();
			foreach($slids as $subId) {
				$values[] = "$sid, $subId";
				$this->updateSubscribersCnt( $subId, 1 );
			}
			dbNbs::query("INSERT INTO `@__subscribers_to_lists` (sid, slid) VALUES (". implode("),(", $values). ")");
		}
	}
	public function updateSubscribersCnt($slid, $cnt) {
		$cnt = (int) $cnt;
		if($cnt) {
			$sign = $cnt > 0 ? "+" : "-";
			$absCnt = abs($cnt);
			dbNbs::query("UPDATE @__subscribers_lists SET subscribers_cnt = subscribers_cnt $sign $absCnt WHERE id = $slid");
		}
	}
	public function updateNewslettersCnt($slid, $cnt) {
		$cnt = (int) $cnt;
		if($cnt) {
			$sign = $cnt > 0 ? "+" : "-";
			dbNbs::query("UPDATE @__subscribers_lists SET newletters_cnt = newletters_cnt $sign $cnt WHERE id = $slid");
		}
	}
	public function createWpList() {
		return $this->create(array(
			'unique_id' => NBS_WP_SUB_LIST,
			'label' => __('WordPress Subscribers', NBS_LANG_CODE),
		));
	}
	public function importToWpList( $slid = 0 ) {
		if(!$slid) {
			$wpList = $this->getOneBy('unique_id', NBS_WP_SUB_LIST);
			if($wpList) {
				$slid = $wpList['id'];
			}
		}
		if($slid) {
			$wpSubscribers = get_users('orderby=nicename&role=subscriber');
			if($wpSubscribers && !is_wp_error($wpSubscribers)) {
				$subModel = frameNbs::_()->getModule('subscribers')->getModel();
				$addedIds = array();
				foreach($wpSubscribers as $sub) {
					$sid = $subModel->duplicateFromWpUser($sub, array(
						'ignore_list_bind' => true,	// We will bind them in batch
						'ignore_exist_check' => true,
					));
					if($sid) {
						$addedIds[] = $sid;
					}
				}
				if(!empty($addedIds)) {
					$this->_bindToSubscribers($addedIds, $slid);
				}
			}
		}
	}
	public function removeGroup( $ids ) {
		// Make sure that WP list will not be removed
		if(!empty($ids)) {
			$wpListId = $this->getWpListId();
			$wpListFound = array_search($wpListId, $ids);
			if($wpListFound !== false) {
				array_splice($ids, $wpListFound, 1);
			}
		}
		return parent::removeGroup( $ids );
	}
	public function getWpList() {
		return $this->getOneBy('unique_id', NBS_WP_SUB_LIST);
	}
	public function getWpListId() {
		return $this->setSelectFields('id')->getOneBy('unique_id', NBS_WP_SUB_LIST, array('return' => 'one'));
	}
	public function getSubscribersCnt( $ids ) {
		if(!is_array($ids)) $ids = array( $ids );
		return (int) dbNbs::get("SELECT SUM(subscribers_cnt) FROM @__subscribers_lists WHERE id IN (". implode(',', $ids). ")", 'one');
	}
	public function getSubscribersBySubscriptionList( $slids ) {
		$slids = (string) $slids;
		if(!empty($slids)) {
			return dbNbs::get("SELECT a.email, a.username, a.date_created FROM @__subscribers as a LEFT JOIN @__subscribers_to_lists as lists ON a.id = lists.sid WHERE lists.slid IN ($slids) GROUP BY a.email");
		}
	}
}
