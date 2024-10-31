var nbsFormSaveTimeout = null
,	nbsFormIsSaving = false
,	nbsTinyMceEditorUpdateBinded = false
,	nbsSaveWithoutPreviewUpdate = false
,	nbsOneLineEditors = ['#nbsFormFieldWrapperEditor'];
jQuery(document).ready(function(){
	jQuery('#nbsFormEditTabs').wpTabs({
		uniqId: 'nbsFormEditTabs'
	,	change: function(selector) {
			if(selector == '#nbsFormEditors') {
				jQuery(selector).find('textarea').each(function(i, el){
					if(typeof(this.CodeMirrorEditor) !== 'undefined') {
						this.CodeMirrorEditor.refresh();
					}
				});
			} else if(selector == '#nbsFormStatistics' && typeof(nbsRefreshCharts) === 'function') {
				nbsRefreshCharts();
			}
			if(selector == '#nbsFormStatistics') {	// Hide preview for statistics tab
				jQuery('#nbsFormPreview').hide();
			} else {
				jQuery('#nbsFormPreview').show();
			}
			var tabChangeEvt = str_replace(selector, '#', '')+ '_tabSwitch';
			jQuery(document).trigger( tabChangeEvt, selector );
		}
	});
	jQuery('.nbsFormSaveBtn').click(function(){
		jQuery('#nbsFormEditForm').submit();
		return false;
	});
	
	jQuery('#nbsFormEditForm').submit(function(){
		if(!nbsValidateFormSave()) {
			return false;
		}
		// Don't save if form isalready submitted
		if(nbsFormIsSaving) {
			nbsMakeAutoUpdate();
			return false;
		}
		if(!nbsSaveWithoutPreviewUpdate)
			nbsShowPreviewUpdating();
		nbsFormIsSaving = true;
		var addData = {};
		if(nbsForm.params.opts_attrs.txt_block_number) {
			for(var i = 0; i < nbsForm.params.opts_attrs.txt_block_number; i++) {
				var textId = 'params_tpl_txt_'+ i
				,	sendValKey = 'params_tpl_txt_val_'+ i;
				addData[ sendValKey ] = encodeURIComponent( nbsGetTxtEditorVal( textId ) );
			}
		}
		var cssEditor = jQuery('#nbsFormCssEditor').get(0).CodeMirrorEditor
		,	htmlEditor = jQuery('#nbsFormHtmlEditor').get(0).CodeMirrorEditor
		,	cssSet = false
		,	htmlSet = false;
		
		if(cssEditor) {
			if(cssEditor._nbsChanged) {
				jQuery('#nbsFormCssEditor').val( cssEditor.getValue() );
				cssEditor._nbsChanged = false;
			} else {
				jQuery('#nbsFormCssEditor').val('');
				cssSet = cssEditor.getValue();
			}
		}
		if(htmlEditor) {
			if(htmlEditor._nbsChanged) {
				jQuery('#nbsFormHtmlEditor').val( htmlEditor.getValue() );
				htmlEditor._nbsChanged = false;
			} else {
				jQuery('#nbsFormHtmlEditor').val('');
				htmlSet = htmlEditor.getValue();
			}
		}
		for(var i = 0; i < nbsOneLineEditors.length; i++) {
			var $currEditField = jQuery(nbsOneLineEditors[ i ])
			,	mirrorEditor = $currEditField.get(0).CodeMirrorEditor;
			if(!mirrorEditor) continue
			$currEditField.val( mirrorEditor.getValue() );
		}
		jQuery(this).sendFormNbs({
			btn: jQuery('.nbsFormSaveBtn')
		,	appendData: addData
		,	onSuccess: function(res) {
				nbsFormIsSaving = false;
				if(!res.error) {
					if(!nbsSaveWithoutPreviewUpdate)
						nbsRefreshPreview();
				}
				nbsSaveWithoutPreviewUpdate = false;
				if(cssSet && cssEditor) {
					jQuery('#nbsFormCssEditor').val( cssEditor.getValue() );
				}
				if(htmlSet && htmlEditor) {
					jQuery('#nbsFormHtmlEditor').val( htmlEditor.getValue() );
				}
			}
		});
		return false;
	});
	
	jQuery('.nbsBgTypeSelect').change(function(){
		var iter = jQuery(this).data('iter');
		jQuery('.nbsBgTypeShell_'+ iter).hide();
		switch(jQuery(this).val()) {
			case 'img':
				jQuery('.nbsBgTypeImgShell_'+ iter).show();
				break;
			case 'color':
				jQuery('.nbsBgTypeColorShell_'+ iter).show();
				break;
		}
	}).change();
	// Fallback for case if library was not loaded
	if(typeof(CodeMirror) !== 'undefined') {
		var cssEditor = CodeMirror.fromTextArea(jQuery('#nbsFormCssEditor').get(0), {
			mode: 'css'
		,	lineWrapping: true
		,	lineNumbers: true
		,	matchBrackets: true
		,	autoCloseBrackets: true
		});
		jQuery('#nbsFormCssEditor').get(0).CodeMirrorEditor = cssEditor;
		if(cssEditor.on && typeof(cssEditor.on) == 'function') {
			cssEditor.on('change', function( editor ){
				editor._nbsChanged = true;
				nbsMakeAutoUpdate( 3000 );
			});
		}
		var htmlEditor = CodeMirror.fromTextArea(jQuery('#nbsFormHtmlEditor').get(0), {
			mode: 'text/html'
		,	lineWrapping: true
		,	lineNumbers: true
		,	matchBrackets: true
		,	autoCloseBrackets: true
		});
		jQuery('#nbsFormHtmlEditor').get(0).CodeMirrorEditor = htmlEditor;
		if(htmlEditor.on && typeof(htmlEditor.on) == 'function') {
			htmlEditor.on('change', function( editor ){
				editor._nbsChanged = true;
				nbsMakeAutoUpdate( 3000 );
			});
		}
		for(var i = 0; i < nbsOneLineEditors.length; i++) {
			var $currEditField = jQuery(nbsOneLineEditors[ i ])
			,	currEditor = CodeMirror.fromTextArea($currEditField.get(0), {
					mode: 'text/html'
				,	lineWrapping: true
				,	lineNumbers: true
				,	matchBrackets: true
				,	autoCloseBrackets: true
			});
			$currEditField.get(0).CodeMirrorEditor = currEditor;
			currEditor.on('keydown', function(mirror, e) {
				if(e.keyCode == 13) {	// Enter
					this._nbsCancelEvent = true;
				}
			});
			currEditor.on('beforeChange', function(mirror, changeObj) {
				if(this._nbsCancelEvent) {
					changeObj.cancel()
					this._nbsCancelEvent = false;
				}
			});
			jQuery(currEditor.getWrapperElement()).addClass('nbsCodeMirrorOneLine');
		}
	}
	// Shortcodes example switch
	jQuery('#nbsFormShortcodeExampleSel').change(function(){
		jQuery('.nbsFormWhereShowBlock').hide().filter('[data-for="'+ jQuery(this).val()+ '"]').show();
		/*var showId = ''
		,	showFor = jQuery(this).val();
		switch(jQuery(this).val()) {
			case 'shortcode':
				showId = 'nbsFormShortcodeShell';
				break;
			case 'php_code':
				showId = 'nbsFormPhpCodeShell';
				break;
			case 'widget':
				showId = 'nbsFormWidgetShell';
				break;
			case 'popup':
				showId = 'nbsFormPopupShell';
				break;
		}
		jQuery('#'+ showId).show();*/
	}).trigger('change');
	nbsInitCustomSelects('.chosen', {
		checkResponsive: true
	});
	
	jQuery('.nbsFormPreviewBtn').click(function(){
		nbsSaveFormChanges();
		jQuery('html, body').animate({
			scrollTop: jQuery("#nbsFormPreview").offset().top
		}, 1000);
		return false;
	});
	// Delete btn init
	jQuery('.nbsFormRemoveBtn').click(function(){
		if(confirm(toeLangNbs('Are you sure want to remove this Form?'))) {
			jQuery.sendFormNbs({
				btn: this
			,	data: {mod: 'forms', action: 'remove', id: nbsForm.id}
			,	onSuccess: function(res) {
					if(!res.error) {
						toeRedirect( nbsAddNewUrl );
					}
				}
			});
		}
		return false;
	});
	// Don't allow users to set more then 100% width
	jQuery('#nbsFormEditForm').find('[name="params[tpl][width]"]').keyup(function(){
		var measureType = jQuery('#nbsFormEditForm').find('[name="params[tpl][width_measure]"]:checked').val();
		if(measureType == '%') {
			var currentValue = parseInt( jQuery(this).val() );
			if(currentValue > 100) {
				jQuery(this).val( 100 );
			}
		}
	});
	jQuery('#nbsFormEditForm').find('[name="params[tpl][width_measure]"]').change(function(){
		if(!jQuery(this).attr('checked'))
			return;
		var widthInput = jQuery('#nbsFormEditForm').find('[name="params[tpl][width]"]');
		if(jQuery(this).val() == '%') {
			var currentWidth = parseInt(widthInput.val());
			if(currentWidth > 100) {
				widthInput.data('prev-width', currentWidth);
				widthInput.val(100);
			}
		} else if(widthInput.data('prev-width')) {
			widthInput.val( widthInput.data('prev-width') );
		}
	});
	// Init Save as Copy function
	nbsFormInitSaveAsCopyDlg();
	jQuery(window).resize(function(){
		nbsAdjustFormsEditTabs();
	});
	// Switch Off/Onn button
	// It's working from shortcode or widget only - so no need to switch it's active status
	/*nbsFormCheckSwitchActiveBtn();
	jQuery('.nbsFormSwitchActive').click(function(){
		var newActive = parseInt(nbsForm.active) ? 0 : 1;
		jQuery.sendFormNbs({
			btn: this
		,	data: {mod: 'forms', action: 'switchActive', id: nbsForm.id, active: newActive}
		,	onSuccess: function(res) {
				if(!res.error) {
					nbsForm.active = newActive;
					nbsFormCheckSwitchActiveBtn();
				}
			}
		});
		return false;
	});*/
	jQuery('#supsystic-breadcrumbs').bind('startSticky', function(){
		var currentPadding = parseInt(jQuery('#nbsFormMainControllsShell').css('padding-right'));
		jQuery('#nbsFormMainControllsShell').css('padding-right', currentPadding + 200).attr('data-padding-changed', 'padding is changed in admin.forms.edit.js');
	});
	jQuery('#supsystic-breadcrumbs').bind('stopSticky', function(){
		var currentPadding = parseInt(jQuery('#nbsFormMainControllsShell').css('padding-right'));
		jQuery('#nbsFormMainControllsShell').css('padding-right', currentPadding - 200);
	});
	// Editable Form title
	jQuery('#nbsFormEditableLabelShell').click(function(){
		var isEdit = jQuery(this).data('edit-on');
		if(!isEdit) {
			var $labelHtml = jQuery('#nbsFormEditableLabel')
			,	$labelTxt = jQuery('#nbsFormEditableLabelTxt');
			$labelTxt.val( $labelHtml.text() );
			$labelHtml.hide( g_nbsAnimationSpeed );
			$labelTxt.show( g_nbsAnimationSpeed, function(){
				jQuery(this).data('ready', 1);
			});
			jQuery(this).data('edit-on', 1);
		}
	});
	// Edit Form Label
	jQuery('#nbsFormEditableLabelTxt').blur(function(){
		nbsFinishEditFormLabel( jQuery(this).val() );
	}).keydown(function(e){
		if(e.keyCode == 13) {	// Enter pressed
			nbsFinishEditFormLabel( jQuery(this).val() );
		}
	});
	// Save contacts data change
	jQuery('#nbsFormEditForm [name="params[tpl][save_contacts]"]').change(function(){
		if(jQuery(this).prop('checked')) {
			jQuery('.nbsContactExportNbsBtnShell').slideDown( g_nbsAnimationSpeed );
		} else {
			jQuery('.nbsContactExportNbsBtnShell').slideUp( g_nbsAnimationSpeed );
		}
	}).change();
	// Show/hide whole blocks after it's enable/disable by special attribute - data-switch-block
	jQuery('input[type=checkbox][data-switch-block]').change(function(){
		var blockToSwitch = jQuery(this).data('switch-block');
		if(jQuery(this).prop('checked')) {
			jQuery('[data-block-to-switch='+ blockToSwitch+ ']').slideDown( g_nbsAnimationSpeed );
		} else {
			jQuery('[data-block-to-switch='+ blockToSwitch+ ']').slideUp( g_nbsAnimationSpeed );
		}
	}).change();
	// Email attach settings
	/*jQuery('.nbsFormAddEmailAttachBtn').click(function(){
		nbsAddEmailAttach({
			$parentShell: jQuery(this).parents('.nbsFormAttachFilesShell:first')
		});
		return false;
	});
	jQuery('.nbsFormAttachFilesShell').each(function(){
		var $this = jQuery(this)
		,	key = $this.data('key')
		,	filesKey = 'sub_attach_'+ key;
		if(nbsForm.params 
			&& nbsForm.params.tpl 
			&& nbsForm.params.tpl[ filesKey ]
		) {
			for(var i in nbsForm.params.tpl[ filesKey ]) {
				if(nbsForm.params.tpl[ filesKey ][ i ] && nbsForm.params.tpl[ filesKey ][ i ] != '') {
					nbsAddEmailAttach({
						$parentShell: $this
					,	file: nbsForm.params.tpl[ filesKey ][ i ]
					});
				}
			}
		}
	});*/
	// Submit main form by Enter key
	jQuery('#nbsFormEditForm input[type=text]').keypress(function(e){
		if (e.which == 13) {
			e.preventDefault();
			jQuery('#nbsFormEditForm').submit();
		}
	});
});
function nbsValidateFormSave() {
	if(!g_nbsFieldsFrame.haveSubmitField()) {
		_nbsShowSaveFormErrorWnd('submit_btn');
		return false;
	}
	return true;
}
function _nbsShowSaveFormErrorWnd( code ) {
	if(!this._wnd) {
		var self = this;
		this._wnd = jQuery('#nbsSaveFormErrorWnd').dialog({
			modal:    true
		,	autoOpen: false
		,	width: 460
		,	height: 180
		,	buttons:  {
				OK: function() {
					self._wnd.dialog('close');
				}
			}
		});
	}
	this._wnd.find('.nbsSaveFormErrorEx').hide().filter('[data-code="'+ code+ '"]').show();
	this._wnd.dialog('open');
}
/*function nbsAddEmailAttach(params) {
	var $parent = params.$parentShell
	,	$newShell = jQuery('#nbsFormAttachShell').clone().removeAttr('id')
	,	$input = $newShell.find('[name="params[tpl][sub_attach][]"]').removeAttr('disabled')
	,	$fileName = $newShell.find('.nbsFormAttachFile')
	,	key = $parent.data('key');
	$parent.append( $newShell );
	$input.attr('name', 'params[tpl][sub_attach_'+ key+ '][]');
	var _setFileClb = function( url ) {
		$input.val( url );
		$fileName.html( url );
	};
	$newShell.find('.nbsFormAttachBtn').click(function(){
		var button = jQuery(this);
		_custom_media = true;
		wp.media.editor.send.attachment = function(props, attachment){
			if ( _custom_media ) {
				_setFileClb( attachment.url );
			} else {
				return _orig_send_attachment.apply( this, [props, attachment] );
			};
		};
		wp.media.editor.open(button);
		return false;
	});
	$newShell.find('.nbsFormAttachRemoveBtn').click(function(){
		$newShell.remove();
		return false;
	});
	if(params.file) {
		_setFileClb( params.file );
	}
}*/
jQuery(window).load(function(){
	nbsAdjustFormsEditTabs();
});
function nbsFinishEditFormLabel(label) {
	if(jQuery('#nbsFormEditableLabelShell').data('sending')) return;
	if(!jQuery('#nbsFormEditableLabelTxt').data('ready')) return;
	jQuery('#nbsFormEditableLabelShell').data('sending', 1);
	jQuery.sendFormNbs({
		btn: jQuery('#nbsFormEditableLabelShell')
	,	data: {mod: 'forms', action: 'updateLabel', label: label, id: nbsForm.id}
	,	onSuccess: function(res) {
			if(!res.error) {
				var $labelHtml = jQuery('#nbsFormEditableLabel')
				,	$labelTxt = jQuery('#nbsFormEditableLabelTxt');
				$labelHtml.html( jQuery.trim($labelTxt.val()) );
				$labelTxt.hide( g_nbsAnimationSpeed ).data('ready', 0);
				$labelHtml.show( g_nbsAnimationSpeed );
				jQuery('#nbsFormEditableLabelShell').data('edit-on', 0);
			}
			jQuery('#nbsFormEditableLabelShell').data('sending', 0);
		}
	});
}
/**
 * Make forms edit tabs - responsive
 * @param {bool} requring is function - called in requring way
 */
function nbsAdjustFormsEditTabs(requring) {
	jQuery('#nbsFormEditTabs .supsystic-always-top')
			.outerWidth( jQuery('#nbsFormEditTabs').width() )
			.attr('data-code-tip', 'Width was set in admin.forms.edit.js - nbsAdjustFormsEditTabs()');
	
	var checkTabsNavs = ['#nbsFormEditTabs .nav-tab-wrapper:first'];
	for(var i = 0; i < checkTabsNavs.length; i++) {
		var tabs = jQuery(checkTabsNavs[i])
		,	delta = 10
		,	lineWidth = tabs.width() + delta
		,	fullCurrentWidth = 0
		,	currentState = '';	//full, text, icons

		if(!tabs.find('.nbs-edit-icon').is(':visible')) {
			currentState = 'text';
		} else if(!tabs.find('.nbsFormTabTitle').is(':visible')) {
			currentState = 'icons';
		} else {
			currentState = 'full';
		}

		tabs.find('.nav-tab').each(function(){
			fullCurrentWidth += jQuery(this).outerWidth();
		});

		if(fullCurrentWidth > lineWidth) {
			switch(currentState) {
				case 'full':
					tabs.find('.nbs-edit-icon').hide();
					nbsAdjustFormsEditTabs(true);	// Maybe we will require to make it more smaller
					break;
				case 'text':
					tabs.find('.nbs-edit-icon').show().end().find('.nbsFormTabTitle').hide();
					break;
				default:
					// Nothing can do - all that can be hidden - is already hidden
					break;
			}
		} else if(fullCurrentWidth < lineWidth && (lineWidth - fullCurrentWidth > 400) && !requring) {
			switch(currentState) {
				case 'icons':
					tabs.find('.nbs-edit-icon').hide().end().find('.nbsFormTabTitle').show();
					break;
				case 'text':
					tabs.find('.nbs-edit-icon').show().end().find('.nbsFormTabTitle').show();
					break;
				default:
					// Nothing can do - all that can be hidden - is already hidden
					break;
			}
		}
	}
}
function nbsShowImgPrev(url, attach, buttonId) {
	var iter = jQuery('#'+ buttonId).data('iter');
	jQuery('.nbsBgImgPrev_'+ iter).attr('src', url);
}
function nbsSaveFormChanges(withoutPreviewUpdate) {
	// Triger save
	if(withoutPreviewUpdate)
		nbsSaveWithoutPreviewUpdate = true;
	jQuery('.nbsFormSaveBtn').click();
}
function nbsRefreshPreview() {
	document.getElementById('nbsFormPreviewFrame').contentWindow.location.reload();
}
function nbsMakeAutoUpdate(delay) {
	if(parseInt(toeOptionNbs('disable_autosave'))) {
		return;	// Autosave disabled in admin area
	}
	delay = delay ? delay : 1500;
	if(nbsFormSaveTimeout)
		clearTimeout( nbsFormSaveTimeout );
	nbsFormSaveTimeout = setTimeout(nbsSaveFormChanges, delay);
}
function nbsShowPreviewUpdating() {
	this._posSet;
	if(!this._posSet) {
		this._posSet = true;
		jQuery('#nbsFormPreviewUpdatingMsg').css({
			'left': 'calc(50% - '+ (jQuery('#nbsFormPreviewUpdatingMsg').width() / 2)+ 'px)'
		});
	}
	jQuery('#nbsFormPreviewFrame').css({
		'opacity': 0.5
	});
	jQuery('#nbsFormPreviewUpdatingMsg').slideDown( g_nbsAnimationSpeed );
}
function nbsHidePreviewUpdating() {
	jQuery('#nbsFormPreviewFrame').show().css({
		'opacity': 1
	});
	jQuery('#nbsFormPreviewUpdatingMsg').slideUp( 100 );
}
function nbsFormInitSaveAsCopyDlg() {
	var $container = jQuery('#nbsFormSaveAsCopyWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 460
	,	height: 180
	,	buttons:  {
			OK: function() {
				jQuery('#nbsFormSaveAsCopyForm').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('#nbsFormSaveAsCopyForm').submit(function(){
		jQuery(this).sendFormNbs({
			msgElID: 'nbsFormSaveAsCopyMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			}
		});
		return false;
	});
	jQuery('.nbsFormCloneBtn').click(function(){
		$container.dialog('open');
		return false;
	});
}
function nbsFormCheckSwitchActiveBtn() {
	if(parseInt(nbsForm.active)) {
		jQuery('.nbsFormSwitchActive .fa').removeClass('fa-toggle-on').addClass('fa-toggle-off');
		jQuery('.nbsFormSwitchActive span').html( jQuery('.nbsFormSwitchActive').data('txt-off') )
	} else {
		jQuery('.nbsFormSwitchActive .fa').removeClass('fa-toggle-off').addClass('fa-toggle-on');
		jQuery('.nbsFormSwitchActive span').html( jQuery('.nbsFormSwitchActive').data('txt-on') );	
	}
}
