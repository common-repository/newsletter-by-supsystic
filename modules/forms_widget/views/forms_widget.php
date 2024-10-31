<?php
class forms_widgetViewNbs extends viewNbs {
	public function displayForm($data, $widget) {
		$formsList = array();
		$forms = frameNbs::_()->getModule('forms')->getModel()->getSimpleList('original_id != 0');
		if($forms) {
			foreach($forms as $f) {
				$formsList[ $f['id'] ] = $f['label'];
			}
		}
		$this->assign('formsList', $formsList);
		$this->assign('createFormUrl', frameNbs::_()->getModule('options')->getTabUrl('forms_add_new'));
		$this->assign('data', $data);
        $this->assign('widget', $widget);
		parent::display('formsWidgetForm');
	}
	public function displayWidget($args, $instance) {
		$title = empty( $instance['title'] ) ? '' : $instance['title'];

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		if(isset($instance['id']) && !empty($instance['id'])) {
			echo frameNbs::_()->getModule('forms')->getView()->showForm(array('id' => $instance['id']));
		}

		echo $args['after_widget'];
	}
}
