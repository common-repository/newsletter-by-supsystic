<?php
class formsModelNbs extends modelNbs {
	private $_linksReplacement = array();
	private $_lastForm = null;
	public function __construct() {
		$this->_setTbl('forms');
	}
	public function getLastForm() {
		return $this->_lastForm;
	}
	public function subscribe( $d ) {
		$id = isset($d['id']) ? (int) $d['id'] : false;
		if($id) {
			$d = dbNbs::prepareHtmlIn($d);
			$form = $this->getById( $id );
			if($form) {
				if($this->validateFields($d['fields'], $form, $d)) {
					if($this->addSubscriber($d['fields'], $form)) {
						$this->_lastForm = $form;
						return true;
					}
				}
			} else
				$this->pushError(__('Can\'t find form', NBS_LANG_CODE));
		} else
			$this->pushError(__('Empty Form ID', NBS_LANG_CODE));
		return false;
	}
	public function getContactsForForm( $id ) {
		$contacts = frameNbs::_()->getTable('contacts')->get('*', array('form_id' => $id));
		if(!empty($contacts)) {
			foreach($contacts as $i => $c) {
				$contacts[ $i ] = $this->_contactAfterGetFromTbl( $c );
			}
		}
		return $contacts;
	}
	private function _contactAfterGetFromTbl( $contact ) {
		if(isset($contact['fields']) && !empty($contact['fields'])) {
			$contact['fields'] = utilsNbs::decodeArrayTxt($contact['fields']);
		}
		return $contact;
	}
	public function validateReCaptcha($field, $response) {
		$response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
			'body' => array(
				'secret' => $field['recap-secret'],
				'response' => $response,
				'remoteip' => utilsNbs::getIP(),
			),
		));
		if (!is_wp_error($response)) {
			if(isset($response['body']) && !empty($response['body']) && ($resArr = utilsNbs::jsonDecode($response['body']))) {
				if(isset($resArr['success']) && $resArr['success']) {
					return true;
				} else {
					$errorsDesc = array(
						'missing-input-secret' => __('reCaptcha: The secret parameter is missing.', NBS_LANG_CODE),
						'invalid-input-secret' => __('reCaptcha: The secret parameter is invalid or malformed.', NBS_LANG_CODE),
						'missing-input-response' => __('Please prove that you are not a robot - check reCaptcha.', NBS_LANG_CODE),
						'invalid-input-response' => __('reCaptcha: The response parameter is invalid or malformed.', NBS_LANG_CODE),
					);
					$errors = array();
					foreach($resArr['error-codes'] as $errCode) {
						if(isset($errorsDesc[ $errCode ])) {
							$errors[] = $errorsDesc[ $errCode ];
						}
					}
					$this->pushError(empty($errors) ? $resArr['error-codes'] : $errors);
				}
			} else
				$this->pushError(__('There was a problem with sending request to Google reCaptcha validation server. Please make sure that your server have possibility to send server-server requests. Ask your hosting provider about this.', NBS_LANG_CODE));
		} else
			$this->pushError( $response->get_error_message() );
		return false;
	}
	public function validateFields($fieldsData, $form, $d = array()) {
		if(isset($form['params']['fields']) && !empty($form['params']['fields'])) {
			$errors = array();
			$error = false;
			foreach($form['params']['fields'] as $f) {
				$htmlType = $f['html'];
				if(isset($f['mandatory']) && $f['mandatory']) {
					$k = $f['name'];
					$htmlType = $f['html'];
					$value = (isset($fieldsData[ $k ]) && !is_array($fieldsData[ $k ])) ? trim($fieldsData[ $k ]) : false;

					// Server-side multiple select require validation
					if (in_array($htmlType, array('selectlist', 'countryListMultiple', 'subscriptionList'))) {
						if($fieldsData[ $k ]) {
							$value = true;
						} else {
							$value = false;
						}
					}

					// Server-side email validation
					if($htmlType == 'email' && $value && !is_email($value)) {
						$value = false;
					} elseif ($htmlType == 'email' && $value && is_email($value) && frameNbs::_()->getModule('subscribers')->getModel()->emailExists($value)) {
						$value = false;
						$emailMatched = true;
					}

					if(empty($value)) {
						$errorMsg = '';
						$formInvalidError = isset($form['params']['tpl']['field_error_invalid']) && !empty($form['params']['tpl']['field_error_invalid'])
							? trim($form['params']['tpl']['field_error_invalid'])
							: false;
						if(!empty($formInvalidError)) {
							$formInvalidError = str_replace('[label]', '%s', $formInvalidError);
						}
						switch($f['html']) {
							case 'selectbox': case 'subscriptionList': case 'selectlist': case 'countryListMultiple':
								$errorMsg = empty($formInvalidError) ? __('Please select %s', NBS_LANG_CODE) : $formInvalidError;
								break;
							case 'email':
								if($value === false && isset($emailMatched)) {
									$errorMsg = empty($formInvalidError) ? __('Your email has already been added %s', NBS_LANG_CODE) : $formInvalidError;
								} elseif ($value === false && !isset($emailMatched)) {
									$errorMsg = empty($formInvalidError) ? __('Please enter valid email %s', NBS_LANG_CODE) : $formInvalidError;
								}
									break;
							case 'checkbox': case 'radiobutton':
								if($value === false) {
									$errorMsg = empty($formInvalidError) ? __('Please check %s', NBS_LANG_CODE) : $formInvalidError;
								}
								break;
							default:
								$errorMsg = empty($formInvalidError) ? __('Please enter %s', NBS_LANG_CODE) : $formInvalidError;
								break;
						}
						if(!empty($errorMsg)) {
							$errors[ $k ] = sprintf($errorMsg, $f['label']);
						}
					}
				}
				// Validate reCaptcha
				if($htmlType == 'recaptcha' && !$this->validateReCaptcha( $f, $d['g-recaptcha-response'] )) {
					$error = true;	// Errors was just pushed before, in validateReCaptcha() method
				}
				if(empty($errors[ $k ])) {	// Additional check in pro module
					$fieldTypeData = $this->getModule()->getFieldTypeByCode( $htmlType );
					if($fieldTypeData && isset($fieldTypeData['pro'])) {
						$addFieldsMod = frameNbs::_()->getModule('add_fields');
						if($addFieldsMod) {
							$invalidError = $addFieldsMod->validateField($htmlType, $f, $fieldsData[ $k ], $form);
							if($invalidError) {
								$errors[ $k ] = $invalidError;
							}
						}
					}
				}
			}
			if(!empty($errors) || $error) {
				if(!empty($errors))
					$this->pushError($errors);
				return false;
			}
		}
		return true;
	}
	public function addSubscriber($fieldsData, $form) {
		$subscribeData = array('all_data' => array(), 'form_id' => $form['id']);
		foreach($form['params']['fields'] as $f) {
			$subscribeData['all_data'][ $f['name'] ] = isset($fieldsData[ $f['name'] ]) ? $fieldsData[ $f['name'] ] : false;
			if ($f['html'] === 'subscriptionList') {
					$subscriptionLists = $subscribeData['all_data'][ $f['name'] ];
			}
		}
		if(isset($subscribeData['all_data']['email'])) {
			$subscribeData['email'] = $subscribeData['all_data']['email'];
			$userName = explode("@", $subscribeData['email']);
			$userName = $userName[0];
		}
		if(isset($subscribeData['all_data']['username']) && $subscribeData['all_data']['username']) {
			$subscribeData['username'] = $subscribeData['all_data']['username'];
		} elseif (!$subscribeData['all_data']['username']) {
			$subscribeData['username'] = $userName;
		}
		else {
			$subscribeData['username_from_email'] = true;
		}


		$subscribeData['slid'] = $form['params']['tpl']['lists'];

		if (isset($subscriptionLists) && $subscriptionLists) {
			if (!$subscribeData['slid']) {
				$subscribeData['slid'] = array();
			}
			$subscriptionLists = array_merge ($subscriptionLists, $subscribeData['slid']);
			$subscriptionLists = array_unique ($subscriptionLists);
			$subscribeData['slid'] = array();
		}

		$subscribeData['send_confirm'] = isset($form['params']['tpl']['send_confirm']) && $form['params']['tpl']['send_confirm'];

		if(frameNbs::_()->getModule('subscribers')->getModel()->save( $subscribeData )) {
				if (isset($subscriptionLists) && $subscriptionLists) {
					$sid = frameNbs::_()->getModule('subscribers')->getModel()->emailExists($subscribeData['email']);
				  	frameNbs::_()->getModule('subscribers_lists')->getModel()->addSubscriberToLists($sid, $subscriptionLists);
				}
				return true;
		} else
			  $this->pushError(frameNbs::_()->getModule('subscribers')->getModel()->getErrors());
		return false;
	}

	/**
	 * Exclude some data from list - to avoid memory overload
	 */
	public function getSimpleList($where = array(), $params = array()) {
		if($where)
			$this->setWhere ($where);
		return $this->setSelectFields('id, label, original_id, img_preview')->getFromTbl( $params );
	}
	protected function _prepareParamsAfterDb($params) {
		if(is_array($params)) {
			foreach($params as $k => $v) {
				$params[ $k ] = $this->_prepareParamsAfterDb( $v );
			}
		} else
			$params = stripslashes ($params);
		return $params;
	}
	private function _getLinksReplacement() {
		if(empty($this->_linksReplacement)) {
			$this->_linksReplacement = array(
				'modUrl' => array('url' => $this->getModule()->getModPath(), 'key' => 'NBS_MOD_URL'),
				'siteUrl' => array('url' => NBS_SITE_URL, 'key' => 'NBS_SITE_URL'),
				'assetsUrl' => array('url' => $this->getModule()->getAssetsUrl(), 'key' => 'NBS_ASSETS_URL'),
			);
		}
		return $this->_linksReplacement;
	}
	protected function _beforeDbReplace($data) {
		static $replaceFrom, $replaceTo;
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$data[ $k ] = $this->_beforeDbReplace($v);
			}
		} else {
			if(!$replaceFrom) {
				$this->_getLinksReplacement();
				foreach($this->_linksReplacement as $k => $rData) {
					$replaceFrom[] = $rData['url'];
					$replaceTo[] = '['. $rData['key']. ']';
				}
			}
			$data = str_replace($replaceFrom, $replaceTo, $data);
		}
		return $data;
	}
	protected function _afterDbReplace($data) {
		static $replaceFrom, $replaceTo;
		if(is_array($data)) {
			foreach($data as $k => $v) {
				$data[ $k ] = $this->_afterDbReplace($v);
			}
		} else {
			if(!$replaceFrom) {
				$this->_getLinksReplacement();
				foreach($this->_linksReplacement as $k => $rData) {
					$replaceFrom[] = '['. $rData['key']. ']';
					$replaceTo[] = $rData['url'];
				}
			}
			$data = str_replace($replaceFrom, $replaceTo, $data);
		}
		return $data;
	}
	protected function _afterGetFromTbl($row) {
		if(isset($row['params']))
			$row['params'] = $this->_prepareParamsAfterDb( utilsNbs::decodeArrayTxt($row['params']) );
		if(empty($row['img_preview'])) {
			$row['img_preview'] = str_replace(' ', '-', strtolower( trim($row['label']) )). '.jpg';
		}
		$row['img_preview_url'] = uriNbs::_($this->getModule()->getFormPrevUrl(). $row['img_preview']);
		$row['view_id'] = $row['id']. '_'. mt_rand(1, 999999);
		$row['view_html_id'] = 'nbsFormShell_'. $row['view_id'];
		$row = $this->_afterDbReplace($row);
		return $row;
	}
	protected function _dataSave($data, $update = false) {
		$data = $this->_beforeDbReplace($data);
		if(isset($data['params']))
			$data['params'] = utilsNbs::encodeArrayTxt( $data['params'] );
		return $data;
	}
	protected function _escTplData($data) {
		$data['label'] = dbNbs::prepareHtmlIn($data['label']);
		$data['html'] = dbNbs::escape($data['html']);
		$data['css'] = dbNbs::escape($data['css']);
		return $data;
	}
	public function createFromTpl($d = array()) {
		$d['label'] = isset($d['label']) ? trim($d['label']) : '';
		$d['original_id'] = isset($d['original_id']) ? (int) $d['original_id'] : 0;
		if(!empty($d['label'])) {
			if(!empty($d['original_id'])) {
				$original = $this->getById($d['original_id']);
				frameNbs::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('create_from_tpl.'. strtolower(str_replace(' ', '-', $original['label'])));
				unset($original['id']);
				$original['label'] = $d['label'];
				$original['original_id'] = $d['original_id'];
				return $this->insertFromOriginal( $original );
			} else
				$this->pushError (__('Please select Form template from list below', NBS_LANG_CODE));
		} else
			$this->pushError (__('Please enter Name', NBS_LANG_CODE), 'label');
		return false;
	}
	public function insertFromOriginal($original) {
		// Clear statistics data for new form
		$original['views'] = $original['unique_views'] = $original['actions'] = 0;
		$original = $this->_escTplData( $original );
		return $this->insert( $original );
	}
	public function remove($id) {
		$id = (int) $id;
		if($id) {
			if(frameNbs::_()->getTable( $this->_tbl )->delete(array('id' => $id))) {
				return true;
			} else
				$this->pushError (__('Database error detected', NBS_LANG_CODE));
		} else
			$this->pushError(__('Invalid ID', NBS_LANG_CODE));
		return false;
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
		$forms = $this->getById($d['id']);
		if(isset($d['params']['opts_attrs']['txt_block_number']) && !empty($d['params']['opts_attrs']['txt_block_number'])) {
			for($i = 0; $i < (int) $d['params']['opts_attrs']['txt_block_number']; $i++) {
				$sendValKey = 'params_tpl_txt_val_'. $i;
				if(isset($d[ $sendValKey ])) {
					$d['params']['tpl']['txt_'. $i] = urldecode( $d[ $sendValKey ] );
				}
			}
		}
		if(isset($d['params']['tpl']['use_sss_prj_id'])) {
			$oldSssProjId = isset($forms['params']['tpl']['use_sss_prj_id']) ? (int) $forms['params']['tpl']['use_sss_prj_id'] : 0;
			$newSssProjId = (int) $d['params']['tpl']['use_sss_prj_id'];
			if($oldSssProjId != $newSssProjId) {
				if(!$this->_updateSocSharingProject( $newSssProjId, $d['id'])) {	// For just changed Proj ID - set it, if it was set to 0 - clear prev. selected
					return false;	// Something wrong go there - let's try to detect thos issues for now
				}
			}
		}
		if(isset($d['css']) && empty($d['css'])) {
			unset($d['css']);
		}
		if(isset($d['html']) && empty($d['html'])) {
			unset($d['html']);
		}
		$res = $this->updateById($d);
		if($res) {
			dispatcherNbs::doAction('afterFormUpdate', $d);
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
			$currentForm = $this->getById( $d['id'] );
			$newTpl = $this->getById( $d['new_tpl_id'] );
			$originalForm = $this->getById( $currentForm['original_id'] );
			$diffFromOriginal = $this->getDifferences($currentForm, $originalForm);
			if(!empty($diffFromOriginal)) {
				if(isset($newTpl['params'])) {
					$keysForMove = array('params.tpl.label', 'params.tpl.anim_key', 'params.tpl.enb_foot_note', 'params.tpl.foot_note',
						'params.tpl.enb_sm',
						'params.tpl.enb_subscribe');
					foreach($diffFromOriginal as $k) {
						if(in_array($k, $keysForMove)
							|| strpos($k, 'params.tpl.enb_sm_') === 0
							|| strpos($k, 'params.tpl.sm_') === 0
							|| strpos($k, 'params.tpl.enb_sub_') === 0
							|| strpos($k, 'params.tpl.sub_') === 0
							|| strpos($k, 'params.tpl.enb_txt_') === 0
							|| strpos($k, 'params.tpl.txt_') === 0
						) {
							$this->_assignKeyArr($currentForm, $newTpl, $k);
						}
					}
				}
			}
			// Save main settings - as they should not influence for display settings
			$this->_assignKeyArr($currentForm, $newTpl, 'params.main');
			frameNbs::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('change_to_tpl.'. strtolower(str_replace(' ', '-', $newTpl['label'])));
			$newTpl['original_id'] = $newTpl['id'];	// It will be our new original
			$newTpl['id'] = $currentForm['id'];
			$newTpl['label'] = $currentForm['label'];
			$newTpl = dispatcherNbs::applyFilters('formsChangeTpl', $newTpl, $currentForm);
			$newTpl = $this->_escTplData( $newTpl );
			return $this->update( $newTpl, array('id' => $newTpl['id']) );
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
	public function getDifferences($forms, $original) {
		$difsFromOriginal = $this->_computeDifferences($forms, $original);
		$difsOfOriginal = $this->_computeDifferences($original, $forms);	// Some options may be present in original, but not present in current forms
		if(!empty($difsFromOriginal) && empty($difsOfOriginal)) {
			return $difsFromOriginal;
		} elseif(empty($difsFromOriginal) && !empty($difsOfOriginal)) {
			return $difsOfOriginal;
		} else {
			$difs = array_merge($difsFromOriginal, $difsOfOriginal);
			return array_unique($difs);
		}
	}
	private function _computeDifferences($forms, $original, $key = '', $keysImplode = array()) {
		$difs = array();
		if(is_array($forms)) {
			$excludeKey = array('id', 'label', 'active', 'original_id', 'img_preview',
				'date_created', 'view_id', 'img_preview_url', 'show_on', 'show_to', 'show_pages');
			if(!empty($key))
				$keysImplode[] = $key;
			foreach($forms as $k => $v) {
				if(in_array($k, $excludeKey) && empty($key)) continue;
				if(!isset($original[ $k ])) {
					$difs[] = $this->_prepareDiffKeys($k, $keysImplode);
					continue;
				}
				$currDifs = $this->_computeDifferences($forms[ $k ], $original[ $k ], $k, $keysImplode);
				if(!empty($currDifs)) {
					$difs = array_merge($difs, $currDifs);
				}
			}
		} else {
			if($forms != $original) {
				$difs[] = $this->_prepareDiffKeys($key, $keysImplode);
			}
		}
		return $difs;
	}
	private function _prepareDiffKeys($key, $keysImplode) {
		return empty($keysImplode) ? $key : implode('.', $keysImplode). '.'. $key;
	}
	public function clearCachedStats($id) {
		$tbl = $this->getTbl();
		$id = (int) $id;
		return dbNbs::query("UPDATE @__$tbl SET `views` = 0, `unique_views` = 0, `actions` = 0 WHERE `id` = $id");
	}
	public function addCachedStat($id, $statColumn) {
		$tbl = $this->getTbl();
		$id = (int) $id;
		return dbNbs::query("UPDATE @__$tbl SET `$statColumn` = `$statColumn` + 1 WHERE `id` = $id");
	}
	public function addViewed($id) {
		return $this->addCachedStat($id, 'views');
	}
	public function addUniqueViewed($id) {
		return $this->addCachedStat($id, 'unique_views');
	}
	public function addActionDone($id) {
		return $this->addCachedStat($id, 'actions');
	}
	public function saveAsCopy($d = array()) {
		$d['copy_label'] = isset($d['copy_label']) ? trim($d['copy_label']) : '';
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if(!empty($d['copy_label'])) {
			if(!empty($d['id'])) {
				$original = $this->getById($d['id']);
				unset($original['id']);
				unset($original['date_created']);
				$original['label'] = $d['copy_label'];
				$original['views'] = $original['unique_views'] = $original['actions'] = 0;
				//frameNbs::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('save_as_copy');
				return $this->insertFromOriginal( $original );
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
		$this->setSelectFields('id, label, active, views, unique_views, actions, date_created, sort_order');
		return parent::setSimpleGetFields();
	}
	/**
	 * Names of Background for each Form template - to not display standard "Background 1" etc. labels there
	 */
	public function getBgNames() {
		return array(
			'wefj2' => array(
				__('Form background', NBS_LANG_CODE),
				__('Inputs background', NBS_LANG_CODE),
				__('Submit buttons background', NBS_LANG_CODE),
				__('Reset buttons background', NBS_LANG_CODE),
			),
			'foe42k' => array(
				__('Form background', NBS_LANG_CODE),
				__('Inputs background', NBS_LANG_CODE),
				__('Submit buttons background', NBS_LANG_CODE),
				__('Reset buttons background', NBS_LANG_CODE),
			),
			'uwi23o' => array(
				__('Form background', NBS_LANG_CODE),
				__('Inputs and Buttons background', NBS_LANG_CODE),
			),
		);
	}
	public function getBgNamesForForm( $uniqueId ) {
		$bgNames = $this->getBgNames();
		return isset($bgNames[ $uniqueId ]) ? $bgNames[ $uniqueId ] : false;
	}
}
