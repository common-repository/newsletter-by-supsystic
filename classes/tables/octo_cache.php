<?php
class tableOcto_cacheNbs extends tableNbs {
    public function __construct() {
        $this->_table = '@__octo_cache';
        $this->_alias = 'sup_octo_cache';
        $this->_addField('oid', 'text', 'int')
				->_addField('data', 'text', 'text')
				->_addField('date_created', 'text', 'varchar');
    }
}