<?php
class tableQueueNbs extends tableNbs {
    public function __construct() {
        $this->_table = '@__queue';
        $this->_alias = 'sup_queue';
        $this->_addField('sid', 'hidden', 'int')
			->_addField('nid', 'text', 'varchar')
			->_addField('date_send', 'text', 'varchar')
			->_addField('date_created', 'text', 'varchar');
    }
}