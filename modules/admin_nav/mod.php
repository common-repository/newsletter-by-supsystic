<?php
class admin_navNbs extends moduleNbs {
	public function getBreadcrumbsList() {
		$res = array(
			array('label' => NBS_WP_PLUGIN_NAME, 'url' => frameNbs::_()->getModule('adminmenu')->getMainLink()),
		);
		// Try to get current tab breadcrumb
		$activeTab = frameNbs::_()->getModule('options')->getActiveTab();
		if(!empty($activeTab) && $activeTab != 'main_page') {
			$tabs = frameNbs::_()->getModule('options')->getTabs();
			if(!empty($tabs) && isset($tabs[ $activeTab ])) {
				if(isset($tabs[ $activeTab ]['add_bread']) && !empty($tabs[ $activeTab ]['add_bread'])) {
					if(!is_array($tabs[ $activeTab ]['add_bread']))
						$tabs[ $activeTab ]['add_bread'] = array( $tabs[ $activeTab ]['add_bread'] );
					foreach($tabs[ $activeTab ]['add_bread'] as $addForBread) {
						$res[] = array(
							'label' => $tabs[ $addForBread ]['label'], 'url' => $tabs[ $addForBread ]['url'],
						);
					}
				}
				$res[] = array(
					'label' => $tabs[ $activeTab ]['label'], 'url' => $tabs[ $activeTab ]['url'],
				);
			}
		}
		return $res;
	}
}

