<?php
class tableSubscribers_listsNbs extends tableNbs {
    public function __construct() {
        $this->_table = '@__subscribers_lists';
        $this->_id = 'id';
        $this->_alias = 'sup_subscribers_lists';
        $this->_addField('id', 'hidden', 'int')
			->_addField('label', 'text', 'varchar')
			->_addField('unique_id', 'text', 'varchar')
			->_addField('description', 'text', 'text')
			->_addField('subscribers_cnt', 'text', 'int')
			->_addField('unsubscribed_cnt', 'text', 'int')
			->_addField('newletters_cnt', 'text', 'int')
			
			->_addField('date_created', 'text', 'varchar');
    }
}