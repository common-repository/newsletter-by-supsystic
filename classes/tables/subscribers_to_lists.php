<?php
class tableSubscribers_to_listsNbs extends tableNbs {
    public function __construct() {
        $this->_table = '@__subscribers_to_lists';
        $this->_alias = 'sup_subscribers_to_lists';
        $this->_addField('sid', 'text', 'int')
			->_addField('slid', 'text', 'int');
    }
}