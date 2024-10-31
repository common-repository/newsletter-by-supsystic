<?php
class tableStatisticsNbs extends tableNbs {
    public function __construct() {
        $this->_table = '@__statistics';
        $this->_alias = 'sup_statistics';
        $this->_addField('nid', 'hidden', 'int')
			->_addField('type', 'text', 'int')
			->_addField('actions', 'text', 'int')
			->_addField('date_created', 'text', 'varchar');
    }
}