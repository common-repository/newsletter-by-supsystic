<?php
class tableSubscribersNbs extends tableNbs {
    public function __construct() {
        $this->_table = '@__subscribers';
        $this->_id = 'id';
        $this->_alias = 'sup_subscribers';
        $this->_addField('id', 'hidden', 'int')
			->_addField('wp_id', 'hidden', 'int')
			->_addField('username', 'text', 'varchar')
			->_addField('email', 'text', 'varchar')
			->_addField('hash', 'text', 'varchar')
			->_addField('status', 'text', 'int')
			->_addField('date_created', 'text', 'varchar')
			->_addField('all_data', 'text', 'text')
			->_addField('form_id', 'hidden', 'int');
    }
}