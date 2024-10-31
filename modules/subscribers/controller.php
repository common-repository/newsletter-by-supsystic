<?php
class subscribersControllerNbs extends controllerNbs {
	protected function _prepareListForTbl($data) {
		if(!empty($data)) {
			foreach($data as $i => $v) {
				$lists = $this->getModel()->getLists( $data[ $i ]['id'] );
				$data[ $i ]['email'] = '<a href="'. $this->getModule()->getEditLink( $data[ $i ]['id'] ). '">'. $data[ $i ]['email']. '&nbsp;<i class="fa fa-fw fa-pencil" style="margin-top: 2px;"></i></a>';
				$data[ $i ]['lists_desc'] = '';
				if($lists) {
					$data[ $i ]['lists_desc'] = frameNbs::_()->getModule('subscribers_lists')->generateListsDesc( $lists );
				}
			}
		}
		return $data;
	}
	protected function _prepareModelBeforeListSelect($model, $search) {
		$listId = isset($search['search_list']) ? (int) $search['search_list'] : 0;
		if($listId) {	// Bad, bad code ...........
			$model->addWhere('id IN (SELECT sid FROM @__subscribers_to_lists WHERE slid = '. $listId. ')');
		}
		return $model;
	}
	protected function _prepareTextLikeSearch($val) {
		$query = '(email LIKE "%'. $val. '%" OR username LIKE "%'. $val. '%"';
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
		$data = reqNbs::get('post');
		$update = isset($data['id']) && !empty($data['id']);
		if(($saveRes = $this->getModel()->save( $data ))) {
			if(!$update) {
				$res->addData('subscriber', $this->getModel()->getFullById( $saveRes ));
			}
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function unsubscribe() {
		$res = new responseNbs();
		$redirectUrl = NBS_SITE_URL;
		$data = dbNbs::prepareHtml( reqNbs::get('get') );
		if(($subscriber = $this->getModel()->unsubscribe( $data ))) {
			$res->addMessage(sprintf(__('You unsubscribed from list(s) %s', NBS_LANG_CODE), $subscriber['unsubscribed_slids']));
			// Make subscriber logged-in into his profile
			$this->getModel()->login( $subscriber );
			$redirectUrl = $this->getModule()->getSubscribersPageUrl();
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->mainRedirect( $redirectUrl );
	}
	public function updateProfile() {
		$res = new responseNbs();
		$data = dbNbs::prepareHtml( reqNbs::get('post') );
		$id = isset($data['id']) ? (int) $data['id'] : 0;
		$nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : reqNbs::getVar('_wpnonce');
		if(!wp_verify_nonce($nonce, 'profile-'. $id)) {
			die('Some error with your request.........');
		}
		// TODO: Add here cookie validation too
		if(($saveRes = $this->getModel()->updateProfile( $data ))) {
			$res->addMessage(__('Done', NBS_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function sendLoginUrl() {
		$res = new responseNbs();
		$data = dbNbs::prepareHtml( reqNbs::get('post') );
		$nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : reqNbs::getVar('_wpnonce');
		if(!wp_verify_nonce($nonce, 'send-login-url')) {
			die('Some error with your request.........');
		}
		// No errors for now - because this can be used by hackers to check email existance in our database
		$this->getModel()->sendLoginUrl( $data );
		$res->addMessage(__('Login URL was sent to your email. Check it!', NBS_LANG_CODE));
		$res->ajaxExec();
	}
	public function loginByUrl() {
		$res = new responseNbs();
		$redirectUrl = NBS_SITE_URL;
		$data = dbNbs::prepareHtml( reqNbs::get('get') );
		if(($subscriber = $this->getModel()->loginByUrl( $data ))) {
			$res->addMessage(__('Welcome back!', NBS_LANG_CODE));
			$redirectUrl = $this->getModule()->getSubscribersPageUrl();
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->mainRedirect( $redirectUrl );
	}
	public function confirmByUrl() {
		$res = new responseNbs();
		$redirectUrl = NBS_SITE_URL;
		$data = dbNbs::prepareHtml( reqNbs::get('get') );
		if(($subscriber = $this->getModel()->confirmByUrl( $data ))) {
			$this->getModel()->loginByUrl( $data );
			$res->addMessage(__('Greetings!', NBS_LANG_CODE));
			$redirectUrl = $this->getModule()->getSubscribersPageUrl();
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->mainRedirect( $redirectUrl );
	}
	public function getPermissions() {
		return array(
			NBS_USERLEVELS => array(
				NBS_ADMIN => array('getListForTbl', 'remove', 'removeGroup', 'clear', 'save')
			),
		);
	}
}

