var nbsForms = [];
// Make some fields types adaption for HTML5 input types support
var g_nbsFieldsAdapt = {
	date: function( $input ) {
		this._initDatePicker($input, {dateFormat: 'mm/dd/yy'});
	}
,	month: function( $input ) {
		this._initDatePicker($input, {dateFormat: 'MM, yy'});
	}
,	week: function( $input ) {
		this._initDatePicker($input, {
			dateFormat: 'mm/dd/yy'
		,	showWeek: true
		,	onSelect: function(dateText, inst) {
				var date = new Date(dateText);
				jQuery(this).val("Week " + jQuery.datepicker.iso8601Week( date )+ ', '+ date.getFullYear());
			}
		});
	}
,	time: function( $input ) {
		$input.timepicker();
	}
,	_initDatePicker: function( $input, params ) {
		params = params || {};
		$input.datepicker( params );
	}
};
function nbsForm(params) {
	params = params || {};
	this._data = params;
	this._$ = null;
	this.init();
}
nbsForm.prototype.init = function() {
	// Init base $shell object
	this.getShell();
	// Make HTML5 input types support
	this._bindHtml5Support();
	// Check custom error messages from form settings
	this._bindCustomErrorMsgs();
	// Make basic form preparations
	this._bindLove();
	this._checkUpdateRecaptcha();
	this._bindSubmit();
	// Remember that we showed this form
	this._setActionDone('show');
};
nbsForm.prototype.getHtmlViewId = function() {
	return this._data.view_html_id;
};
nbsForm.prototype.getParam = function( key ) {
	var setVal = false
	,	keys = arguments;
	if(keys && keys.length) {
		for(var i = 0; i < keys.length; i++) {
			var k = keys[ i ];
			if(i) {
				setVal = (setVal && setVal[ k ]) ? setVal[ k ] : false;
			} else {
				setVal = this._data.params[ k ];
			}
		}
	}
	return setVal;
};
nbsForm.prototype.getParams = function() {
	return this.get('params');
};
nbsForm.prototype.get = function( key ) {
	return this._data && this._data[ key ] ? this._data[ key ] : false;
};
nbsForm.prototype.getFieldsByType = function( htmlType ) {
	var res = [];
	if(this._data.params.fields) {
		for(var i = 0; i < this._data.params.fields.length; i++) {
			if(this._data.params.fields[ i ].html == htmlType) {
				res.push( this._data.params.fields[ i ] );
			}
		}
	}
	return res && res.length ? res : false;
};
nbsForm.prototype._checkUpdateRecaptcha = function() {
	var reCaptchFields = this.getFieldsByType('recaptcha');
	if(reCaptchFields && reCaptchFields.length) {	// if reCapthca exists
		this._tryUpdateRecaptcha();
	}
};
nbsForm.prototype._tryUpdateRecaptcha = function() {
	//console.log('check!', this._$.find('.g-recaptcha'));
	nbsInitCaptcha( this._$.find('.g-recaptcha') );
	/*var $reCaptcha = this._$.find('.g-recaptcha');
	if($reCaptcha && $reCaptcha.length) {
		$reCaptcha.each(function(){
			if(!jQuery(this).find('iframe').length) {
				grecaptcha.reset();	// Reset them all on page - and just stop
				return false;
			}
		});
	}*/
};
nbsForm.prototype._bindHtml5Support = function() {
	var checkTypes = ['date', 'month', 'week', 'time'];
	for(var i = 0; i < checkTypes.length; i++) {
		var key = checkTypes[ i ];
		if(typeof(key) === 'string' && !ModernizrNbs.inputtypes[ key ]) {
			var $inputs = this._$.find('[type="'+ key+ '"]');
			if($inputs && $inputs.length) {
				g_nbsFieldsAdapt[ key ]( $inputs );
			}
		}
	}
};
nbsForm.prototype._bindCustomErrorMsgs = function() {
	var invalidError = this._data.params.tpl.field_error_invalid;
	if(invalidError && invalidError != '' && this._data.params.fields) {
		var self = this;
		for(var i = 0; i < this._data.params.fields.length; i++) {
			if(parseInt(this._data.params.fields[ i ].mandatory)) {
				var $field = this.getFieldHtml( this._data.params.fields[ i ].name );
				if($field 
					&& $field.get(0) 
					&& $field.get(0).validity	// check HTML5 validation methods existing
					&& $field.get(0).setCustomValidity
				) {
					var label = this._data.params.fields[ i ].label 
						? this._data.params.fields[ i ].label 
						: this._data.params.fields[ i ].placeholder
					,	msg = nbs_str_replace(invalidError, '[label]', label);
					$field.data('nbs-invalid-msg', msg);
					$field.get(0).oninvalid = function() {
						self._setFieldInvalidMsg( this );
					};
					$field.change(function(){
						this.setCustomValidity('');	// Clear validation error, if it need - it will be set in "oninvalid" clb
					});
				}
			}
		}
	}
};
nbsForm.prototype._setFieldInvalidMsg = function( fieldHtml ) {
	fieldHtml.setCustomValidity( jQuery(fieldHtml).data('nbs-invalid-msg') );
};
nbsForm.prototype.getFieldHtml = function( name ) {
	var $field = this._$.find('[name="fields['+ name+ ']"]');
	return $field && $field.length ? $field : false;
};
nbsForm.prototype._bindLove = function() {
	if(parseInt(toeOptionNbs('add_love_link'))) {
		this._$.append( toeOptionNbs('love_link_html') );
	}
};
nbsForm.prototype._addStat = function( action, isUnique ) {
	jQuery.sendFormNbs({
		msgElID: 'noMessages'
	,	data: {mod: 'forms_statistics', action: 'add', id: this._data.id, type: action, is_unique: isUnique, 'connect_hash': this._data.connect_hash}
	});
};
nbsForm.prototype.getShell = function( checkExists ) {
	if(!this._$ || (checkExists && !this._$.length)) {
		this._$ = jQuery('#'+ this._data.view_html_id);
	}
	return this._$;
};
nbsForm.prototype.getStyle = function() {
	if(!this._$style) {
		this._$style = jQuery('#'+ this._data.view_html_id+ '_style');
	}
	return this._$style;
};
nbsForm.prototype._bindSubmit = function() {
	var self = this;
	this._$.find('.nbsForm:not(.nbsSubmitBinded)').submit(function(){
		var $submitBtn = jQuery(this).find('input[type=submit]')
		,	$form = jQuery(this)
		,	$msgEl = jQuery(this).find('.nbsSubscribeMsg');
		$submitBtn.attr('disabled', 'disabled');
		self._setActionDone('submit', true);
		jQuery(this).sendFormNbs({
			msgElID: $msgEl
		,	appendData: {url: window.location.href}
		,	onSuccess: function(res){
				$form.find('input[type=submit]').removeAttr('disabled');
				if(!res.error) {
					var hideOnSubmit = self.getParam('tpl', 'hide_on_submit');
					if(hideOnSubmit === false || parseInt(hideOnSubmit)) {
						var $inPopup = $form.parents('.ppsPopupShell:first')
						,	afterRemoveClb = false;
						// If form is in PopUp - let's relocate it correctly after form html will be removed
						// so PopUp will be still in the center of the screen
						if($inPopup && $inPopup.length) {
							afterRemoveClb = function() {
								if(typeof(ppsGetPopupByViewId) === 'function') {
									_ppsPositionPopup({
										popup: ppsGetPopupByViewId( $inPopup.data('view-id') )
									});
								}
							};
						}
						self._setActionDone('submit_success', true);
						var $parentShell = jQuery($form).parents('.nbsFormShell');
						$msgEl.appendTo( $parentShell );
						var docScrollTop = jQuery('html,body').scrollTop()
						,	formShellTop = self._$.offset().top;
						if(docScrollTop > formShellTop) {	// If message will appear outside of user vision - let's scroll to it
							var scrollTo = formShellTop - $form.scrollTop() - 30;
							jQuery('html,body').animate({
								scrollTop: scrollTo
							}, g_nbsAnimationSpeed);
						}
						$form.animateRemoveNbs( g_nbsAnimationSpeed, afterRemoveClb );
					} else {
						$form.get(0).reset();	// Just clear form
					}
					if(res.data.redirect) {
						toeRedirect(res.data.redirect, parseInt(self._data.params.tpl.redirect_on_submit_new_wnd));
					}
				} else {
					self._setActionDone('submit_error', true);
				}
			}
		});
		return false;
	}).addClass('nbsSubmitBinded');
};
nbsForm.prototype._setActionDone = function( action, onlyClientSide ) {
	var actionsKey = 'nbs_actions_'+ this._data.id
	,	actions = getCookieNbs( actionsKey )
	,	isUnique = 0;
	if(!actions)
		actions = {};
	if(action == 'show' && !actions[ action ]) {
		isUnique = 1;
	}
	actions[ action ] = 1;
	var saveCookieTime = 30;
	saveCookieTime = isNaN(saveCookieTime) ? 30 : saveCookieTime;
	if(!saveCookieTime)
		saveCookieTime = null;	// Save for current session only
	setCookieNbs(actionsKey, actions, saveCookieTime);
	if(!onlyClientSide) {
		this._addStat( action, isUnique );
	}
	jQuery(document).trigger('nbsAfterFormsActionDone', this);
};
nbsForm.prototype.getId = function() {
	return this._data ? this._data.id : false;
};
// Form printing methods - maybe we will add this in future to print forms
nbsForm.prototype.printForm = function() {
	var title = 'Form Content';
	var printWnd = window.open('', title, 'height=400,width=600');
	printWnd.document.write('<html><head><title>'+ title+ '</title>');
	printWnd.document.write('</head><body >');
	printWnd.document.write( this.extractFormData() );
	printWnd.document.write('</body></html>');

	printWnd.document.close(); // necessary for IE >= 10
	printWnd.focus(); // necessary for IE >= 10

	printWnd.print();
	printWnd.close();
};
nbsForm.prototype.extractFormData = function() {
	var $chatBlock = this._$.find('.nbsForm').clone()
	,	$style = this.getStyle().clone()
	,	remove = ['.nbsInputShell', '.nbsFormFooter', '.nbsMessagesExShell', '.nbsOptBtnsShell'];
	for(var i = 0; i < remove.length; i++) {
		$chatBlock.find( remove[ i ] ).remove();
	}
	return jQuery('<div />').append( jQuery('<div id="'+ this._data.tpl.view_html_id+ '" />').append( $chatBlock ).append( $style ) ).html();
};
nbsForm.prototype.refresh = function() {
	this.getShell( true );
	this._bindSubmit();
	this._checkUpdateRecaptcha();
};
// End of form printing methods
var g_nbsForms = {
	_list: []
,	add: function(params) {
		this._list.push( new nbsForm(params) );
	}
,	getById: function( id ) {
		if(this._list && this._list.length) {
			for(var i = 0; i < this._list.length; i++) {
				if(this._list[ i ].getId() == id) {
					return this._list[ i ];
				}
			}
		}
		return false;
	}
,	getByViewHtmlId: function( viewHtmlId ) {
		if(this._list && this._list.length) {
			for(var i = 0; i < this._list.length; i++) {
				if(this._list[ i ].getHtmlViewId() == viewHtmlId) {
					return this._list[ i ];
				}
			}
		}
		return false;
	}
,	getFormDataByViewHtmlId: function( viewHtmlId ) {
		if(typeof(nbsForms) !== 'undefined' && nbsForms && nbsForms.length) {
			for(var i = 0; i < nbsForms.length; i++) {
				if(nbsForms[ i ].view_html_id == viewHtmlId) {
					return nbsForms[ i ];
				}
			}
		}
		return false;
	}
};
jQuery(document).ready(function(){
	if(typeof(nbsFormsRenderFormIter) !== 'undefined') {
		for(var i = 0; i <= nbsFormsRenderFormIter.lastIter; i++) {
			if(typeof(window['nbsForms_'+ i]) !== 'undefined') {
				nbsForms.push( window['nbsForms_'+ i] );
			}
		}
	}
	if(typeof(nbsForms) !== 'undefined' && nbsForms && nbsForms.length) {
		jQuery(document).trigger('nbsBeforeFormsInit', nbsForms);
		for(var i = 0; i < nbsForms.length; i++) {
			g_nbsForms.add( nbsForms[ i ] );
		}
		jQuery(document).trigger('nbsAfterFormsInit', nbsForms);
	}
});