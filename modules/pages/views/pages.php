<?php
class pagesViewNbs extends viewNbs {
    public function displayDeactivatePage() {
        $this->assign('GET', reqNbs::get('get'));
        $this->assign('POST', reqNbs::get('post'));
        $this->assign('REQUEST_METHOD', strtoupper(reqNbs::getVar('REQUEST_METHOD', 'server')));
        $this->assign('REQUEST_URI', basename(reqNbs::getVar('REQUEST_URI', 'server')));
        parent::display('deactivatePage');
    }
}

