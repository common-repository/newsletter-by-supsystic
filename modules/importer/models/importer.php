<?php
class importerModelNbs extends modelNbs {
	public function import( $d = array() ) {
		$sets = isset($d['sets']) ? $d['sets'] : frameNbs::_()->getModule('api_loader')->getSets();
		$source = $sets['source'];
		$wrapper = frameNbs::_()->getModule('api_loader')->getWrapper( $source );
		if(($importCnt = $wrapper->import( $sets[ $source ] )) !== false) {
			return $importCnt;
		} else
			$this->pushError ($wrapper->getErrors());
		return false;
	}
}
