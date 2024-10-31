<?php
class tableNewsletters_to_listsNbs extends tableNbs {
    public function __construct() {
        $this->_table = '@__newsletters_to_lists';
        $this->_alias = 'sup_newsletters_to_lists';
        $this->_addField('nid', 'text', 'int')
			->_addField('slid', 'text', 'int');
    }
}
