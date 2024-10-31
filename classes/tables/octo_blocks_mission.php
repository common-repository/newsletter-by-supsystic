<?php
class tableOcto_blocks_missionNbs extends tableNbs {
    public function __construct() {
        $this->_table = '@__octo_blocks_mission';
        $this->_id = 'id';
        $this->_alias = 'sup_octo_blocks_mission';
		
        $this->_addField('code', 'text', 'text')
			->_addField('label', 'text', 'text');
    }
}