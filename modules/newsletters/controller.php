<?php
class newslettersControllerNbs extends controllerNbs {
	private $_prevNewsletterId = 0;
	public function create() {
		$res = new responseNbs();
		if(($id = $this->getModel()->create(reqNbs::get('post'))) != false) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
			$res->addData('edit_link', $this->getModule()->getEditLink( $id ));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	protected function _prepareListForTbl($data) {
		if(!empty($data)) {
			foreach($data as $i => $v) {
				$statusDesc = $this->getModel()->getStatusById( $data[ $i ]['status'] );
				$haveStats = in_array($statusDesc['code'], array('sending', 'waiting', 'complete'));
				$inProgress = in_array($statusDesc['code'], array('sending', 'waiting'));
				$editLnk = $this->getModule()->getEditLink($data[ $i ]['id']);
				$cautionScript = ($inProgress ? 'onclick="return nbsTryingEditSendingNewsletterClk( this );" ' : '');
				$data[ $i ]['label'] = '<a '
					. $cautionScript
					. 'href="'. $editLnk. '">'. $data[ $i ]['label']. '&nbsp;<i class="fa fa-fw fa-pencil" style="margin-top: 2px;"></i></a>';
				$data[ $i ]['action_btns'] = '<a '
					. $cautionScript
					. 'class="button" href="'. $editLnk. '" title="'. __('Edit', NBS_LANG_CODE). '"><i class="fa fa-fw fa-pencil"></i></a>';
				$data[ $i ]['status_desc'] = $statusDesc['label'];
				if($haveStats) {
					$data[ $i ]['action_btns'] .= '<a '
						. 'class="button" href="'. frameNbs::_()->getModule('statistics')->getUrlForNewsletter( $data[ $i ]['id'] ). '" title="'. __('Check Statistics', NBS_LANG_CODE). '"><i class="fa fa-fw fa-line-chart"></i></a>';
				}
				if($inProgress) {
					// Just to indicate - that this is row for currently sending newsletter
					$data[ $i ]['action_btns'] .= htmlNbs::hidden('sending_row', array('value' => 1));
					$data[ $i ]['status_desc'] .= ':<br />'. $this->_generateStatsListTbl( $data[ $i ] );;
				}
				$lists = $this->getModel()->getLists( $data[ $i ]['id'] );
				if($lists) {
					$data[ $i ]['lists_desc'] = frameNbs::_()->getModule('subscribers_lists')->generateListsDesc( $lists );
				}
			}
		}
		return $data;
	}
	private function _generateStatsListTbl( $newsletter ) {
		return '<table class="nbsNlStatsListTbl">'
		. '<tr><td>'. __('Sent', NBS_LANG_CODE). ' '. $newsletter['sent_cnt'].' from '. $newsletter['queued']. '</td></tr>'
		. '<tr><td>'. __('Opened', NBS_LANG_CODE). ' '. $newsletter['open'].' from '. $newsletter['sent_cnt']. ' '
			. __('including', NBS_LANG_CODE). ' '. $newsletter['uniq_open']. ' '. __('unique views', NBS_LANG_CODE). '</td></tr>'
		. '<tr><td>'. __('Clicked', NBS_LANG_CODE). ' '. $newsletter['click'].' from '. $newsletter['sent_cnt']. '</td></tr>'
		. '</table>';
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
		$where = '';
		$abTestCondAdded = false;
		if(frameNbs::_()->getModule('ab_testing')) {
			$abBaseId = frameNbs::_()->getModule('ab_testing')->getListForBaseId();
			if(!empty($abBaseId)) {
				$where .= ' ab_id = '. $abBaseId;
				$abTestCondAdded = true;
			}
		}
		if(!$abTestCondAdded) {
			$where .= ' ab_id = 0';
		}
		$model->addWhere( $where );
		dispatcherNbs::doAction('newslettersModelBeforeGetList', $model);
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
		$this->_prevNewsletterId = (int) reqNbs::getVar('id', 'get');
		$this->outPreviewHtml();
		//add_action('init', array($this, 'outPreviewHtml'));
	}
	public function outPreviewHtml() {
		if($this->_prevNewsletterId) {
			frameNbs::_()->ignoreJs( true );
			$newsletter = $this->getModel()->getById( $this->_prevNewsletterId );
			frameNbs::_()->getModule('octo')->getView()->renderForPost( $newsletter['oid'], array('simple' => true) );
			frameNbs::_()->ignoreJs( false );
		}
		exit();
	}
	public function changeTpl() {
		$res = new responseNbs();
		if($this->getModel()->changeTpl(reqNbs::get('post'))) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
			$id = (int) reqNbs::getVar('id', 'post');
			// Redirect after change template - to Design tab, as change tpl btn is located there - so, user was at this tab before changing tpl
			$res->addData('edit_link', $this->getModule()->getEditLink( $id, 'nbsNewsletterTpl' ));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function saveAsCopy() {
		$res = new responseNbs();
		if(($id = $this->getModel()->saveAsCopy(reqNbs::get('post'))) != false) {
			$res->addMessage(__('Done, cloned Newsletter will be opened right now', NBS_LANG_CODE));
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
	public function startSend() {
		$res = new responseNbs();
		if($this->getModel()->startSend(reqNbs::get('post'))) {
			$res->addData('list_url', frameNbs::_()->getModule('options')->getTabUrl( $this->getCode() ));
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function sendTest() {
		$res = new responseNbs();
		if($this->getModel()->sendTest(reqNbs::get('post'))) {
			$res->addMessage(__('Done. Now check your email messages.', NBS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function testClickTracker() {
		$this->getModel()->testClickTracker( reqNbs::getVar('nid', 'get'), reqNbs::getVar('sid', 'get') );
		exit();
	}
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array('create', 'getListForTbl', 'remove', 'removeGroup', 'clear', 
					'save', 'getPreviewHtml', 'exportForDb', 'changeTpl', 'saveAsCopy', 'switchActive', 
					'outPreviewHtml', 'updateLabel', 'exportCsv', 'startSend', 'sendTest', 'testClickTracker')
			),
		);
	}
	public function getNoncedMethods() {
		return array('create', 'save');
	}
}

