<?php
class forms_widgetNbs extends moduleNbs {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('formsWidgetWpNbs');
    }
}
/**
 * Forms Widget class
 */
class formsWidgetWpNbs extends WP_Widget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'formsWidgetWpNbs', 
            'description' => __('Display Supsystic Subscribe Form', NBS_LANG_CODE)
        );
        $control_ops = array(
            'id_base' => 'formsWidgetWpNbs'
        );
		parent::__construct( 'formsWidgetWpNbs', __('Subscribe Form', NBS_LANG_CODE), $widgetOps );
    }
    public function widget($args, $instance) {
        frameNbs::_()->getModule('forms_widget')->getView()->displayWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frameNbs::_()->getModule('forms_widget')->getView()->displayForm($instance, $this);
    }
}

