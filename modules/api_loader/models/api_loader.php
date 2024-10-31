<?php
class api_loaderModelNbs extends modelNbs {
	public function getLists() {
		$source = $this->getModule()->getSet('source');
		$wrapper = $this->getModule()->getWrapper( $source );
		$lists = $wrapper->getLists();
		if(!$lists) {
			$this->pushError( $wrapper->getErrors() );
		}
		return $lists;
	}
}
