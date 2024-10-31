<?php
class admin_navViewNbs extends viewNbs {
	public function getBreadcrumbs() {
		$this->assign('breadcrumbsList', dispatcherNbs::applyFilters('mainBreadcrumbs', $this->getModule()->getBreadcrumbsList()));
		return parent::getContent('adminNavBreadcrumbs');
	}
}
