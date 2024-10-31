<?php
class tableNewslettersNbs extends tableNbs {
    public function __construct() {
        $this->_table = '@__newsletters';
        $this->_id = 'id';
        $this->_alias = 'sup_newsletters';
        $this->_addField('id', 'text', 'int')
				->_addField('oid', 'text', 'int')
				->_addField('label', 'text', 'varchar')
				->_addField('status', 'text', 'int')

				->_addField('params', 'text', 'text')
				
				->_addField('open', 'text', 'int')
				->_addField('uniq_open', 'text', 'int')
				->_addField('click', 'text', 'int')
				->_addField('queued', 'text', 'int')
				->_addField('sent_cnt', 'text', 'int')
				->_addField('subscribers_cnt', 'text', 'int')
			
				->_addField('send_on', 'text', 'int')
			
				->_addField('sort_order', 'text', 'int')
				->_addField('ab_id', 'text', 'int')

				->_addField('last_sent', 'text', 'text')
				->_addField('date_created', 'text', 'text');
    }
}