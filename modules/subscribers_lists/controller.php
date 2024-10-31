<?php
class subscribers_listsControllerNbs extends controllerNbs {
	protected function _prepareListForTbl($data) {
		if(!empty($data)) {
			foreach($data as $i => $v) {
				$data[ $i ]['label'] = '<a class="" href="'. $this->getModule()->getEditLink($data[ $i ]['id']). '">'. $data[ $i ]['label']. '&nbsp;<i class="fa fa-fw fa-pencil" style="margin-top: 2px;"></i></a>';
			}
		}
		return $data;
	}
	protected function _prepareTextLikeSearch($val) {
		$query = '(label LIKE "%'. $val. '%"';
		if(is_numeric($val)) {
			$query .= ' OR id LIKE "%'. (int) $val. '%"';
		}
		$query .= ')';
		return $query;
	}
	public function remove() {
		$res = new responseNbs();
		if($this->getModel()->remove(reqNbs::getVar('id', 'post'))) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function save() {
		$res = new responseNbs();
		if($this->getModel()->save( reqNbs::get('post') )) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function create() {
		$res = new responseNbs();
		if(($id = $this->getModel()->create( reqNbs::get('post') ))) {
			$res->addData('sub_list_url', $this->getModule()->getEditLink($id));
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function importFromTxt() {
		$res = new responseNbs();
		if(($importedCnt = $this->getModel()->importFromTxt( reqNbs::get('post') )) !== false) {
			$res->addMessage(sprintf(__('%s subscribers imported', NBS_LANG_CODE), $importedCnt['inserted'] + $importedCnt['updated']));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function importFromCsv() {
		$res = new responseNbs();
		$fileData = reqNbs::getVar('import_from_csv', 'files');
		if(empty($fileData)) {
			$res->pushError(__('Error upload file - it\'s empty!', NBS_LANG_CODE));
		}
		if(isset($fileData['error']) && $fileData['error']) {
			$res->pushError(sprintf(__('Error upload file with error code - %s', NBS_LANG_CODE), $fileData['error']));
		}
		if(!$res->error()) {
			$data = reqNbs::get('post');
			$data['id'] = isset($data['id']) ? $data['id'] : reqNbs::getVar('id');
			$data['file_path'] = $fileData['tmp_name'];
			if(($importedCnt = $this->getModel()->importFromCsv( $data )) !== false) {
				$res->addMessage(sprintf(__('%s subscribers imported', NBS_LANG_CODE), $importedCnt['inserted'] + $importedCnt['updated']));
			} else
				$res->pushError($this->getModel()->getErrors());
		}
		$res->ajaxExec();
	}
	public function exportToCsv() {
		if ($data = reqNbs::get('get')) {
			$data['lists'] = isset($data['lists']) ? $data['lists'] : '';
			$data['mode'] = isset($data['mode']) ? $data['mode'] : '';
			$this->_exportToCsv($data);
		}
		exit();
	}
	private function _exportToCsv($data) {
		if ( isset($data) && isset($data['mode']) && isset($data['lists']) ) {
			$sortArr = array();
			$unsortArr = array();
			if ($data['mode'] === 'lists') {
				$unsortArr = $this->getModel()->getSubscribersBySubscriptionList($data['lists']);
			} elseif ($data['mode'] === 'all') {
				$unsortArr = frameNbs::_()->getModule('subscribers')->getModel()->getAllSubscribers();
			}
			if (isset($unsortArr) && $unsortArr) {
				foreach ($unsortArr as $index=>$user) {
						$sortArr[$index]['email'] = $user['email'];
						$sortArr[$index]['username'] = $user['username'];
						$sortArr[$index]['date_created'] = $user['date_created'];
				}
			}
			if (isset($sortArr) && $sortArr) {
				header("HTTP/1.1 200 OK");
				header("Pragma: public");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private", false);
				header("Content-Type: Document");
				header("Content-Disposition: attachment; filename=\"subscribers.csv\"");
				header("Content-Transfer-Encoding: binary");
				$out = fopen('php://output', 'w'); // Open file
				fputcsv($out, array('email','username','date_created'));	// First row 'email','username','date_created'
				foreach($sortArr as $item) {		// Append  row with 'email','username','date_created'
					fputcsv($out, array($item['email'],$item['username'],$item['date_created']));
				}
				fclose($out);		// Close file and send to client
			}
		}
		return false;
	}
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array('getListForTbl', 'remove', 'removeGroup', 'clear', 'save', 'create', 'importFromTxt', 'importFromCsv', 'exportToCsv')
			),
		);
	}
}
