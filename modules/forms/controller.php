<?php
class formsControllerNbs extends controllerNbs {
	private $_prevFormId = 0;
	public function createFromTpl() {
		$res = new responseNbs();
		if(($id = $this->getModel()->createFromTpl(reqNbs::get('post'))) != false) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
			$res->addData('edit_link', $this->getModule()->getEditLink( $id ));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	protected function _prepareListForTbl($data) {
		if(!empty($data)) {
			foreach($data as $i => $v) {
				$data[ $i ]['label'] = '<a class="" href="'. $this->getModule()->getEditLink($data[ $i ]['id']). '">'. $data[ $i ]['label']. '&nbsp;<i class="fa fa-fw fa-pencil" style="margin-top: 2px;"></i></a>';
				$conversion = 0;
				if(!empty($data[ $i ]['unique_views']) && !empty($data[ $i ]['actions'])) {
					$conversion = number_format( ((int) $data[ $i ]['actions'] / (int) $data[ $i ]['unique_views']), 3);
				}
				$data[ $i ]['conversion'] = $conversion;
				$data[ $i ]['active'] = $data[ $i ]['active'] ? '<span class="alert alert-success">'. __('Yes', NBS_LANG_CODE). '</span>' : '<span class="alert alert-danger">'. __('No', NBS_LANG_CODE). '</span>';
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
	protected function _prepareModelBeforeListSelect($model, $search) {
		$where = 'original_id != 0';
		$abTestCondAdded = false;
		if(frameNbs::_()->getModule('ab_testing')) {
			$abBaseId = frameNbs::_()->getModule('ab_testing')->getListForBaseId();
			if(!empty($abBaseId)) {
				$where .= ' AND ab_id = '. $abBaseId;
				$abTestCondAdded = true;
			}
		}
		if(!$abTestCondAdded) {
			$where .= ' AND ab_id = 0';
		}
		$model->addWhere( $where );
		dispatcherNbs::doAction('formsModelBeforeGetList', $model);
		return $model;
	}
	protected function _prepareSortOrder($sortOrder) {
		if($sortOrder == 'conversion') {
			$sortOrder = '(actions / unique_views)';	// Conversion in real-time calculation
		}
		return $sortOrder;
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
	public function getPreviewHtml() {
		$this->_prevFormId = (int) reqNbs::getVar('id', 'get');
		$this->outPreviewHtml();
		//add_action('init', array($this, 'outPreviewHtml'));
	}
	public function outPreviewHtml() {
		if($this->_prevFormId) {
			$form = $this->getModel()->getById( $this->_prevFormId );
			$formContent = $this->getView()->generateHtml( $form );
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<html dir="'. (function_exists('is_rtl') && is_rtl() ? 'rtl' : 'ltr'). '"><head>'
			. '<meta content="'. get_option('html_type'). '; charset='. get_option('blog_charset'). '" http-equiv="Content-Type">'
			//. '<link rel="stylesheet" href="'. get_stylesheet_uri(). '" type="text/css" media="all" />'
			. $this->_generateRecaptchaAssetsForPrev( $form )
			. $this->_generateGoogleMapsAssetsForPrev( $form )
			. $this->getModule()->getAssetsforPrevStr()
			. '<style type="text/css">
				html { overflow: visible !important; }
				.nbsFormShell {
					display: block;
					position: static;
				}
				</style>'
			. '</head>';
			//wp_head();
			echo '<body>';
			echo $formContent;
			//wp_footer();
			echo '<body></html>';
		}
		exit();
	}
	private function _generateRecaptchaAssetsForPrev( $form ) {
		// check if there are recaptcha field in fields list
		if(!empty($form['params']['fields'])) {
			foreach($form['params']['fields'] as $f) {
				if($f['html'] == 'recaptcha') {
					return '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
				}
			}
		}
		return '';
	}
	private function _generateGoogleMapsAssetsForPrev( $form ) {
		// check if there are google maps field in fields list
		$res = '';
		if(!empty($form['params']['fields'])) {
			$setAssets = array();
			foreach($form['params']['fields'] as $f) {
				if($f['html'] == 'googlemap') {
					if(class_exists('frameGmp') && defined('GMP_VERSION_PLUGIN')) {
						$scripts = frameGmp::_()->getScripts();
						if(!empty($scripts)) {
							frameGmp::_()->getModule('gmap')->getView()->addMapDataToJs();
							$res .= $this->_connectMainJsLibsForPrev();
							$scVars = frameGmp::_()->getJSVars();
							foreach($scripts as $s) {
								if(isset($s['src']) && !empty($s['src']) && !in_array($s['handle'], $setAssets)) {
									if($scVars && isset($scVars[ $s['handle'] ]) && !empty($scVars[ $s['handle'] ])) {
										$res .= "<script type='text/javascript'>"; // CDATA and type='text/javascript' is not needed for HTML 5
										$res .= "/* <![CDATA[ */";
										foreach($scVars[ $s['handle'] ] as $name => $value) {
											if($name == 'dataNoJson' && !is_array($value)) {
												$res .= $value;
											} else {
												$res .= "var $name = ". utilsGmp::jsonEncode($value). ";";
											}
										}
										$res .= "/* ]]> */";
										$res .= "</script>";
									}
									$res .= '<script type="text/javascript" src="'. $s['src']. '"></script>';
									$setAssets[] = $s['handle'];
								}
							}
						}
						$styles = frameGmp::_()->getStyles();
						if(!empty($styles)) {
							foreach($styles as $s) {
								if(isset($s['src']) && !empty($s['src']) && !in_array($s['handle'], $setAssets)) {
									$res .= '<link rel="stylesheet" type="text/css" href="'. $s['src']. '" />';
									$setAssets[] = $s['handle'];
								}
							}
						}
					}
				}
			}
		}
		return $res;
	}
	public function changeTpl() {
		$res = new responseNbs();
		if($this->getModel()->changeTpl(reqNbs::get('post'))) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
			$id = (int) reqNbs::getVar('id', 'post');
			// Redirect after change template - to Design tab, as change tpl btn is located there - so, user was at this tab before changing tpl
			$res->addData('edit_link', $this->getModule()->getEditLink( $id, 'nbsFormTpl' ));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function exportForDb() {
		$eol = "\r\n";

		$forPro = (int) reqNbs::getVar('for_pro', 'get');
		$tblsCols = array(
			'@__forms' => array('unique_id','label','active','original_id','params','html','css','sort_order','date_created','is_pro','img_preview'),
		);
		if($forPro) {
			echo 'db_install=>';
			foreach($tblsCols as $tbl => $cols) {
				echo $this->_makeExportQueriesLogicForPro($tbl, $cols);
			}
		} else {
			foreach($tblsCols as $tbl => $cols) {
				echo "if(function_exists('base64_encode')) {". $eol;
				echo $this->_makeExportQueriesLogic($tbl, $cols);
				echo "} else {	//--not-base64--". $eol;
				echo $this->_makeExportQueriesLogic($tbl, $cols, true);
				echo "}";
			}
		}
		exit();
	}
	private function _makeExportQueriesLogicForPro($table, $cols) {
		$octoList = $this->_getExportData($table, $cols, true);
		$res = array();

		foreach($octoList as $octo) {
			$uId = '';
			$rowData = array();
			foreach($octo as $k => $v) {
				if(!in_array($k, $cols)) continue;
				$val = mysql_real_escape_string($v);
				if($k == 'unique_id') $uId = $val;
				$rowData[ $k ] = $val;

			}
			$res[ $uId ] = $rowData;
		}
		echo str_replace(array('@__'), '', $table). '|'. base64_encode( utilsNbs::serialize($res) );
	}
	private function _getExportData($table, $cols, $forPro = false) {
		return dbNbs::get('SELECT '. implode(',', $cols). ' FROM '. $table. ' WHERE original_id = 0 and is_pro = '. ($forPro ? '1' : '0'));;
	}
	/**
	 * new usage
	 */
	private function _makeExportQueriesLogic($table, $cols, $forceOrd = false) {
		$eol = "\r\n";
		$tab = "\t";
		$octoList = $this->_getExportData($table, $cols);
		$valuesArr = array();
		$allKeys = array();
		$uidIndx = 0;
		$i = 0;
		foreach($octoList as $octo) {
			$arr = array();
			$addToKeys = empty($allKeys);
			$i = 0;
			foreach($octo as $k => $v) {
				$value = $v;
				if(!in_array($k, $cols)) continue;
				if($addToKeys) {
					$allKeys[] = $k;
					if($k == 'unique_id') {
						$uidIndx = $i;
					}
				}
				if($k == 'params' && $forceOrd) {
					$value = utilsNbs::encodeArrayTxt( utilsNbs::decodeArrayTxt( $value ), true );
				}
				$arr[] = ''. mysql_real_escape_string($value). '';
				$i++;
			}
			$valuesArr[] = $arr;
		}
		$out = '';
		//$out .= "\$cols = array('". implode("','", $allKeys). "');". $eol;
		$out .= "\$data = array(". $eol;
		foreach($valuesArr as $row) {
			$uid = str_replace(array('"'), '', $row[ $uidIndx ]);
			$installData = array();
			foreach($row as $i => $v) {
				$installData[] = "'{$allKeys[ $i ]}' => '{$v}'";
			}
			$out .= $tab. "'$uid' => array(". implode(',', $installData). "),". $eol;
		}
		$out .= ");". $eol;
		return $out;
	}
	public function saveAsCopy() {
		$res = new responseNbs();
		if(($id = $this->getModel()->saveAsCopy(reqNbs::get('post'))) != false) {
			$res->addMessage(__('Done, redirecting to new Form...', NBS_LANG_CODE));
			$res->addData('edit_link', $this->getModule()->getEditLink( $id ));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function switchActive() {
		$res = new responseNbs();
		if($this->getModel()->switchActive(reqNbs::get('post'))) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function updateLabel() {
		$res = new responseNbs();
		if($this->getModel()->updateLabel(reqNbs::get('post'))) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function updateNonce() {
		$res = new responseNbs();
		$getFor = reqNbs::getVar('get_for', 'post');
		$id = (int) reqNbs::getVar('id', 'post');
		$updateFor = array();
		if(!empty($getFor) && !empty($id)) {
			$generateKeys = array(
				'nbsSubscribeForm' => 'subscribe-'. $id,
				'nbsLoginForm' => 'login-'. $id,
				'nbsRegForm' => 'register-'. $id,
			);
			foreach($getFor as $gf) {
				if(isset($generateKeys[ $gf ])) {
					$updateFor[ $gf ] = wp_create_nonce( $generateKeys[ $gf ] );
				}
			}
		}
		if(!empty($updateFor)) {
			$res->addData('update_for', $updateFor);
		}
		return $res->ajaxExec();
	}
	public function subscribe() {
		$res = new responseNbs();
		$data = reqNbs::get('post');
		$id = isset($data['id']) ? (int) $data['id'] : 0;
		$nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : reqNbs::getVar('_wpnonce');
		if(!wp_verify_nonce($nonce, 'subscribe-'. $id)) {
			die('Some error with your request.........');
		}
		// Add some statistics
		frameNbs::_()->getModule('statistics')->getModel()->add(array('id' => $id, 'type' => 'submit'));
		if($this->getModel()->subscribe( $data )) {
			$lastForm = $this->getModel()->getLastForm();
			$successMsg = isset($lastForm['params']['tpl']['form_sent_msg'])
					? $lastForm['params']['tpl']['form_sent_msg'] :
					__('Thank you for subscribing!', NBS_LANG_CODE);
			$successMsg = dispatcherNbs::applyFilters('subscribeSuccessMsg', $successMsg, $lastForm);
			$res->addMessage( $successMsg );
			$redirectUrl = isset($lastForm['params']['tpl']['redirect_on_submit']) && !empty($lastForm['params']['tpl']['redirect_on_submit'])
					? $lastForm['params']['tpl']['redirect_on_submit']
					: false;
			$redirectUrl = dispatcherNbs::applyFilters('subscribeSuccessRedirectUrl', $redirectUrl, $lastForm);
			if(!empty($redirectUrl)) {
				$res->addData('redirect', uriNbs::normal($redirectUrl));
			}

		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		frameNbs::_()->getModule('statistics')->getModel()->add(array(
			'id' => $id,
			'type' => $res->error() ? 'submit_error' : 'submit_success',
		));
		return $res->ajaxExec();
	}
	public function exportCsv() {
		$id = (int) reqNbs::getVar('id');
		$form = $this->getModel()->getById( $id );

		importClassNbs('filegeneratorNbs');
		importClassNbs('csvgeneratorNbs');

		$fileTitle = sprintf(__('Contacts from %s', NBS_LANG_CODE), htmlspecialchars( $form['label'] ));
		$csvGenerator = new csvgeneratorNbs( $fileTitle );
		$labels = array();
		// Add additional subscribe fields
		if(isset($form['params']['fields']) && !empty($form['params']['fields'])) {
			foreach($form['params']['fields'] as $f) {
				$labels[ 'user_field_'. $f['name'] ] = $f['label'];
			}
		}
		$labels = array_merge($labels, array(
			'ip' => __('IP', NBS_LANG_CODE),
			'url' => __('URL', NBS_LANG_CODE),
			'form_id' => __('Form ID', NBS_LANG_CODE),
			'date_created' => __('Date Created', NBS_LANG_CODE),
		));
		$contacts = $this->getModel()->getContactsForForm( $id );

		$row = $cell = 0;
		foreach($labels as $l) {
			$csvGenerator->addCell($row, $cell, $l);
			$cell++;
		}
		$row = 1;
		if(!empty($contacts)) {
			foreach($contacts as $c) {
				$cell = 0;
				foreach($labels as $k => $l) {
					$getKey = $k;
					if(strpos($getKey, 'user_field_') === 0) {
						$getKey = str_replace('user_field_', '', $getKey);
						$value = isset($c['fields'][ $getKey ]) ? $c['fields'][ $getKey ] : '';
					} else {
						$value = $c[ $getKey ];
					}
					$csvGenerator->addCell($row, $cell, $value);
					$cell++;
				}
				$row++;
			}
		} else {
			$cell = 0;
			$noUsersMsg = __('There are no Contacts for now', NBS_LANG_CODE);
			$csvGenerator->addCell($row, $cell, $noUsersMsg);
		}
		$csvGenerator->generate();
	}
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array('createFromTpl', 'getListForTbl', 'remove', 'removeGroup', 'clear',
					'save', 'getPreviewHtml', 'exportForDb', 'changeTpl', 'saveAsCopy', 'switchActive',
					'outPreviewHtml', 'updateLabel', 'exportCsv')
			),
		);
	}
	public function getNoncedMethods() {
		return array('save');
	}
}
