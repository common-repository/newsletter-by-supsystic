<?php
class mailchimp_extApiWrapperNbs extends extApiWrapperNbs {
	private $_clients = array();
	
	public function isSupported() {
		if(!function_exists('curl_init')) {
			$this->pushNotSupportError(__('MailChimp requires CURL to be setup on your server. Please contact your hosting provider and ask them to setup CURL library for you. It\'s free.', NBS_LANG_CODE));
			return false;
		}
		return true;
	}
	protected function _getClient( $apiKey ) {
		if(!isset($this->_clients[ $apiKey ])) {
			if(!class_exists('mailChimpClientNbs')) {
				$this->_includLibFile('mailChimpClient.php');
			}
			$this->_clients[ $apiKey ] = new mailChimpClientNbs( $apiKey );
		}
		return $this->_clients[ $apiKey ];
	}
	public function getLists() {
		$apiKey = trim($this->getSet('api_key'));
		if(!empty($apiKey)) {
			$client = $this->_getClient( $apiKey );
			$apiRes = $client->call('lists/list', array('limit' => 100, 'sort_field' => 'web'));
			if($apiRes && is_array($apiRes) && isset($apiRes['data']) && !empty($apiRes['data'])) {
				$listsDta = array();
				foreach($apiRes['data'] as $list) {
					$listsDta[] = array('id' => $list['id'], 'name' => $list['name']);
				}
				return $listsDta;
			} else {
				$this->_parseResErrors( $apiRes );
			}
		} else
			$this->pushError(__('Empty API Key', NBS_LANG_CODE));
		return false;
	}
	private function _parseResErrors( $apiRes ) {
		if(isset($apiRes['errors']) && !empty($apiRes['errors'])) {
			$this->pushError($apiRes['errors']);
		} elseif(isset($apiRes['error']) && !empty($apiRes['error'])) {
			$this->pushError($apiRes['error']);
		} else {
			$this->pushError(__('There was some problem while trying to get your lists. Make sure that your API key is correct.', NBS_LANG_CODE));
		}
	}
	public function import() {
		$apiKey = trim($this->getSet('api_key'));
		if(!empty($apiKey)) {
			$lists = $this->getSet('lists');
			if(isset($lists) && !empty($lists)) {
				$perPage = 100;
				$client = $this->_getClient( $apiKey );
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
				foreach ($lists as $lid) {
					$page = 0;
					do {
						$haveMembers = false;
						$apiRes = $client->call('lists/members', array('id' => $lid, 'opts' => array(
							'start' => $page,
							'limit' => $perPage,
						)));
						if($apiRes && is_array($apiRes) && isset($apiRes['data']) && !empty($apiRes['data'])) {
							foreach($apiRes['data'] as $subsData) {
								$allDataCurr = array('sup_from' => $wrapperCode);
								if(isset($subsData['merges']) && !empty($subsData['merges'])) {
									foreach($subsData['merges'] as $k => $v) {
										if(in_array($k, array('GROUPINGS'))) continue;
										$allDataCurr[ $k ] = $v;
									}
								}
								$importData['emails'][] = $subsData['email'];
								$importData['all_data'][] = $allDataCurr;
							}
							$haveMembers = true;
							$page++;
						} else
							$this->_parseResErrors( $apiRes );
					} while( $haveMembers );
					// Import subscribers with lists data
					if($importWithLists && !empty($importData['emails'])) {
						$listDataRes = $client->call('lists/list', array('filters' => array('list_id' => $lid)));
						if($listDataRes && is_array($listDataRes) && isset($listDataRes['data']) && !empty($listDataRes['data'][ 0 ])) {
							$importToListId = $ignoreSameListsNames
								? $subListModel->createIfNotExists(array('label' => $listDataRes['data'][ 0 ]['name']))
								: $subListModel->create(array('label' => $listDataRes['data'][ 0 ]['name']));
							if($importToListId) {
								$importData['list_id'] = $importToListId;
								$importRes = $subModel->batchImport( $importData );
								if($importRes) {
									$importedCntData['inserted'] += $importRes['inserted'];
									$importedCntData['updated'] += $importRes['updated'];
								}
							} else
								$this->pushError($subListModel->getErrors());
						} else
							$this->_parseResErrors( $listDataRes );
						// Clear before start update new one
						$importData['emails'] = $importData['all_data'] = array();
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
		} else
			$this->pushError(__('Empty API Key', NBS_LANG_CODE));
		return false;
	}
	public function getOpts() {
		return array(
			array(
				'key' => 'api_key',
				'label' => __('MailChimp API key', NBS_LANG_CODE), 
				'desc' => sprintf(__('To find your MailChimp API Key login to your mailchimp account at <a href="%s" target="_blank">%s</a> then from the left main menu, click on your Username, then select "Account" in the flyout menu. From the account page select "Extras", "API Keys". Your API Key will be listed in the table labeled "Your API Keys". Copy / Paste your API key into "MailChimp API key" field here.', NBS_LANG_CODE), 'http://mailchimp.com', 'http://mailchimp.com'),
				'html' => 'text',
				'attrs' => 'data-required-for="lists" style="width: 100%;"',
			),
			array(
				'key' => 'lists',
				'label' => __('Lists', NBS_LANG_CODE), 
				'desc' => __('Your MailChimp Subscription lists to import Subscribers from.', NBS_LANG_CODE),
				'html' => 'selectlist',
				'attrs' => 'class="chosen" data-placeholder="'. __('Select Lists', NBS_LANG_CODE). '"',
				'lists_error' => __('Enter API key - and your list will appear here', NBS_LANG_CODE),
				'is_lists' => true,
			),
		);
	}
}