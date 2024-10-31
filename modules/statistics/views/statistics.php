<?php
class statisticsViewNbs extends viewNbs {
	public function showForNewsletter( $nid ) {
		$newsletter = frameNbs::_()->getModule('newsletters')->getModel()->getById( $nid );
		if(empty($newsletter)) {
			return __('Cannot find required Newsletter', NBS_LANG_CODE);
		}
		frameNbs::_()->getModule('templates')->loadJqGrid();
		frameNbs::_()->getModule('templates')->loadGoogleCharts();
		frameNbs::_()->getModule('templates')->loadDatePicker();
		frameNbs::_()->addScript('admin.stats.newsletters', $this->getModule()->getModPath(). 'js/admin.stats.newsletters.js');
		frameNbs::_()->addJSVar('admin.stats.newsletters', 'nbsNewsletter', $newsletter);
		$proStatsHtml = '';
		if(frameNbs::_()->getModule('supsystic_promo')->isPro() && frameNbs::_()->getModule('stats_pro')) {
			$proStatsHtml = frameNbs::_()->getModule('stats_pro')->getView()->getNewsletterGraphs( $nid );
		}
		$this->assign('proStatsHtml', $proStatsHtml);
		$this->assign('newsletter', $newsletter);
		return parent::getContent('statNewsletter');
	}
}
