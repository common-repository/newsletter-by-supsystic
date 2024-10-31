<?php
class mailpoet_extApiWrapperNbs extends extApiWrapperNbs {
	public function isSupported() {
		if(!class_exists('WYSIJA')) {
			$this->pushNotSupportError(sprintf(__('<a target="_blank" href="%s">MailPoet plugin</a> was not found on your site', NBS_LANG_CODE), admin_url('plugin-install.php?tab=search&s=MailPoet')));
			return false;
		}
		return true;
	}
	public function getOpts() {
		return array(
			array(
				'key' => 'lists',
				'label' => __('Lists', NBS_LANG_CODE), 
				'desc' => __('Your MailPoet Subscription lists to import Subscribers from.', NBS_LANG_CODE),
				'html' => 'selectlist',
				'attrs' => 'class="chosen" data-placeholder="'. __('Select Lists', NBS_LANG_CODE). '"',
				'lists_error' => __('No lists found', NBS_LANG_CODE),
				'is_lists' => true,
			),
		);
	}
	public function getLists() {
		$mailPoetLists = WYSIJA::get('list', 'model')->get(array('name', 'list_id'), array('is_enabled' => 1));
		if(!empty($mailPoetLists)) {
			$listsDta = array();
			foreach($mailPoetLists as $list) {
				$listsDta[] = array('id' => $list['list_id'], 'name' => $list['name']);;
			}
			return $listsDta;
		} else
			$this->pushError(__('Empty Lists', NBS_LANG_CODE));
		return false;
	}
	public function import() {
		$lists = $this->getSet('lists');
		if(isset($lists) && !empty($lists)) {
			$mailPoetUsersModel = WYSIJA::get('user', 'model');
			$maiPoetListsModel = WYSIJA::get('list', 'model');
			$importData = array(
				'emails' => array(), 
				'all_data' => array(),
			);
			$wrapperCode = $this->getCode();
			$importWithLists = (int) $this->getModule()->getSet('import_with_lists');
			if($importWithLists) {
				$ignoreSameListsNames = (int) $this->getModule()->getSet('ignore_same_lists_names');
			} else {
				$importToList = $this->getModule()->getSet('import_to_list');
			}
			$subListModel = frameNbs::_()->getModule('subscribers_lists')->getModel();
			$subModel = frameNbs::_()->getModule('subscribers')->getModel();
			$importedCntData = array('inserted' => 0, 'updated' => 0);
			$subI = 0;
			foreach ($lists as $lid) {
				$apiRes = $mailPoetUsersModel->get_subscribers(array('*'), array('equal' => array('list_id' => $lid)));
				if($apiRes && is_array($apiRes)) {
					foreach($apiRes as $subsData) {
						$allDataCurr = array('sup_from' => $wrapperCode);
						foreach($subsData as $k => $v) {
							$allDataCurr[ $k ] = $v;
						}
						$importData['emails'][ $subI ] = $subsData['email'];
						$importData['all_data'][ $subI ] = $allDataCurr;
						$importData['wp_id'][ $subI ] = isset($subsData['wpuser_id']) && !empty($subsData['wpuser_id']) ? $subsData['wpuser_id'] : 0;
						$subI++;
					}
				}
				// Import subscribers with lists data
				if($importWithLists && !empty($importData['emails'])) {
					$listDataRes = $maiPoetListsModel->get_one_list( $lid );
					if($listDataRes && is_array($listDataRes)) {
						$importToListId = $ignoreSameListsNames
							? $subListModel->createIfNotExists(array('label' => $listDataRes['name']))
							: $subListModel->getModel()->create(array('label' => $listDataRes['name']));
						if($importToListId) {
							$importData['list_id'] = $importToListId;
							$importRes = $subModel->batchImport( $importData );
							if($importRes) {
								$importedCntData['inserted'] += $importRes['inserted'];
								$importedCntData['updated'] += $importRes['updated'];
							}
						} else
							$this->pushError($subListModel->getErrors());
					}
					// Clear before start update new one
					$importData['emails'] = $importData['all_data'] = array();
					$subI = 0;
				}
			}
			if(!$importWithLists && !empty($importData['emails'])) {
				if($importToList) {
					$importData['list_id'] = $importToList;
					$importRes = $subModel->batchImport( $importData );
					if($importRes) {
						$importedCntData['inserted'] += $importRes['inserted'];
						$importedCntData['updated'] += $importRes['updated'];
					}
				} else
					$this->pushError(__('Select Lists to Import at first', NBS_LANG_CODE));
			}
			if(!empty($importedCntData['inserted']) || !empty($importedCntData['updated'])) {
				return $importedCntData;
			}
			// Some error occured, but was not initialized
			if(!$this->haveErrors()) {
				$this->pushError(__('Nothing to import', NBS_LANG_CODE));
			}
		} else
			$this->pushError(__('Select Lists at first', NBS_LANG_CODE));

		return false;
	}
}