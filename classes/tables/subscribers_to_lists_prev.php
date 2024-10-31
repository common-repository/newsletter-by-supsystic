<?php
class tableSubscribers_to_lists_prevNbs extends tableNbs {
    public function __construct() {
        $this->_table = '@__subscribers_to_lists_prev';
        $this->_alias = 'sup_subscribers_to_lists_prev';
        $this->_addField('sid', 'text', 'int')
			->_addField('slid', 'text', 'int')
			->_addField('date_created', 'text', 'varchar');
    }
}