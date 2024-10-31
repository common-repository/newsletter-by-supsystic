var nbsNewsletterSaveTimeout = null
,	nbsNewsletterIsSaving = false
,	nbsTinyMceEditorUpdateBinded = false
,	nbsSaveWithoutPreviewUpdate = true	// No preview update here after saving for now
,	nbsAfterSaveClb = null;
jQuery(document).ready(function(){
	jQuery('#nbsNewsletterEditTabs').wpTabs({
		uniqId: 'nbsNewsletterEditTabs'
	,	change: function(selector) {
			var tabChangeEvt = str_replace(selector, '#', '')+ '_tabSwitch';
			jQuery(document).trigger( tabChangeEvt, selector );
		}
	});
	jQuery('.nbsNewsletterSaveBtn').click(function(){
		jQuery('#nbsNewsletterEditNewsletter').submit();
		return false;
	});

	jQuery('#nbsNewsletterEditNewsletter').submit(function(){
		// Don't save if form isalready submitted
		if(nbsNewsletterIsSaving) {
			nbsMakeAutoUpdate();
			return false;
		}
		nbsNewsletterIsSaving = true;
		var addData = {};
		jQuery(this).sendFormNbs({
			btn: jQuery('.nbsNewsletterSaveBtn')
		,	appendData: addData
		,	onSuccess: function(res) {
				nbsNewsletterIsSaving = false;
				if(nbsAfterSaveClb && typeof(nbsAfterSaveClb) === 'function') {
					nbsAfterSaveClb();
					nbsAfterSaveClb = null;
				}
			}
		});
		return false;
	});
	nbsInitCustomSelects('.chosen', {
		checkResponsive: true
	});
	nbsInitCustomSelects('.time-choosen', {
		attrs: {
			width: '90px'
		}
	});
	jQuery('.nbsNewsletterPreviewBtn').click(function(){
		jQuery('html, body').animate({
			scrollTop: jQuery("#nbsNewsletterPreview").offset().top
		}, 1000);
		return false;
	});
	// Delete btn init
	jQuery('.nbsNewsletterRemoveBtn').click(function(){
		if(confirm(toeLangNbs('Are you sure want to remove this Newsletter?'))) {
			jQuery.sendFormNbs({
				btn: this
			,	data: {mod: 'newsletters', action: 'remove', id: nbsNewsletter.id}
			,	onSuccess: function(res) {
					if(!res.error) {
						toeRedirect( nbsAddNewUrl );
					}
				}
			});
		}
		return false;
	});
	// Init Save as Copy function
	nbsNewsletterInitSaveAsCopyDlg();
	jQuery(window).resize(function(){
		nbsAdjustNewslettersEditTabs();
	});
	// Correct sticky navbar
	jQuery('#supsystic-breadcrumbs').bind('startSticky', function(){
		var currentPadding = parseInt(jQuery('#nbsNewsletterMainControllsShell').css('padding-right'));
		jQuery('#nbsNewsletterMainControllsShell').css('padding-right', currentPadding + 200).attr('data-padding-changed', 'padding is changed in admin.newsletters.edit.js');
	});
	jQuery('#supsystic-breadcrumbs').bind('stopSticky', function(){
		var currentPadding = parseInt(jQuery('#nbsNewsletterMainControllsShell').css('padding-right'));
		jQuery('#nbsNewsletterMainControllsShell').css('padding-right', currentPadding - 200);
	});
	// Editable Newsletter title
	jQuery('#nbsNewsletterEditableLabelShell').click(function(){
		var isEdit = jQuery(this).data('edit-on');
		if(!isEdit) {
			var $labelHtml = jQuery('#nbsNewsletterEditableLabel')
			,	$labelTxt = jQuery('#nbsNewsletterEditableLabelTxt');
			$labelTxt.val( $labelHtml.text() );
			$labelHtml.hide( g_nbsAnimationSpeed );
			$labelTxt.show( g_nbsAnimationSpeed, function(){
				jQuery(this).data('ready', 1);
			});
			jQuery(this).data('edit-on', 1);
		}
	});
	// Edit Newsletter Label
	jQuery('#nbsNewsletterEditableLabelTxt').blur(function(){
		nbsFinishEditNewsletterLabel( jQuery(this).val() );
	}).keydown(function(e){
		if(e.keyCode == 13) {	// Enter pressed
			nbsFinishEditNewsletterLabel( jQuery(this).val() );
		}
	});
	// Save contacts data change
	jQuery('#nbsNewsletterEditNewsletter [name="params[tpl][save_contacts]"]').change(function(){
		if(jQuery(this).prop('checked')) {
			jQuery('.nbsContactExportNbsBtnShell').slideDown( g_nbsAnimationSpeed );
		} else {
			jQuery('.nbsContactExportNbsBtnShell').slideUp( g_nbsAnimationSpeed );
		}
	}).change();
	// Show/hide whole blocks after it's enable/disable by special attribute - data-switch-block
	jQuery('input[type=checkbox],input[type=radio]').filter('[data-switch-block]').change(function(){
		var blockToSwitch = jQuery(this).data('switch-block');
		if(jQuery(this).prop('checked')) {
			jQuery('[data-block-to-switch='+ blockToSwitch+ ']').slideDown( g_nbsAnimationSpeed );
		} else {
			jQuery('[data-block-to-switch='+ blockToSwitch+ ']').slideUp( g_nbsAnimationSpeed );
		}
	}).change();
	// Submit main form by Enter key
	jQuery('#nbsNewsletterEditNewsletter input[type=text]').keypress(function(e){
		if (e.which == 13) {
			e.preventDefault();
			jQuery('#nbsNewsletterEditNewsletter').submit();
		}
	});
	// Send btn function
	jQuery('.nbsNewsletterSendBtn').click(function(){
		if(confirm(jQuery(this).data('start-txt'))) {
			var $btn = jQuery(this);
			$btn.setBtnLoadNbs();
			nbsSaveNewsletterChanges(function(){
				$btn.backBtnLoadNbs();
				jQuery.sendFormNbs({
					btn: $btn
				,	data: {mod: 'newsletters', action: 'startSend', id: nbsNewsletter.id}
				,	onSuccess: function(res) {
						if(!res.error && res.data.list_url) {
							// Do not allow to edit newsletter when t is in Sending status
							toeRedirect( res.data.list_url );
						}
					}
				});
			});

		}
		return false;
	});
	// Send test btn
	jQuery('.nbsSendTestBtn').click(function(){
		var $btn = jQuery(this);
		$btn.setBtnLoadNbs();
		nbsSaveNewsletterChanges(function(){
			$btn.backBtnLoadNbs();
			jQuery.sendFormNbs({
				btn: $btn
			,	data: {mod: 'newsletters', action: 'sendTest', id: nbsNewsletter.id}
			,	msgElID: jQuery('.nbsSendTestMsg')
			,	onSuccess: function(res) {

				}
			});
		});
		return false;
	});
	// Recipients count manipulations - just informative data
	nbsUpdateRecipientsCnt();
	jQuery('#nbsNewsletterEditNewsletter [name="slid[]"]').change(function(){
		nbsUpdateRecipientsCnt();
	});
});
function _nbsShowSaveNewsletterErrorWnd( code ) {
	if(!this._wnd) {
		var self = this;
		this._wnd = jQuery('#nbsSaveNewsletterErrorWnd').dialog({
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
	this._wnd.find('.nbsSaveNewsletterErrorEx').hide().filter('[data-code="'+ code+ '"]').show();
	this._wnd.dialog('open');
}
/*function nbsAddEmailAttach(params) {
	var $parent = params.$parentShell
	,	$newShell = jQuery('#nbsNewsletterAttachShell').clone().removeAttr('id')
	,	$input = $newShell.find('[name="params[tpl][sub_attach][]"]').removeAttr('disabled')
	,	$fileName = $newShell.find('.nbsNewsletterAttachFile')
	,	key = $parent.data('key');
	$parent.append( $newShell );
	$input.attr('name', 'params[tpl][sub_attach_'+ key+ '][]');
	var _setFileClb = function( url ) {
		$input.val( url );
		$fileName.html( url );
	};
	$newShell.find('.nbsNewsletterAttachBtn').click(function(){
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
	$newShell.find('.nbsNewsletterAttachRemoveBtn').click(function(){
		$newShell.remove();
		return false;
	});
	if(params.file) {
		_setFileClb( params.file );
	}
}*/
jQuery(window).ready(function(){
	nbsAdjustNewslettersEditTabs();
});
function nbsFinishEditNewsletterLabel(label) {
	if(jQuery('#nbsNewsletterEditableLabelShell').data('sending')) return;
	if(!jQuery('#nbsNewsletterEditableLabelTxt').data('ready')) return;
	jQuery('#nbsNewsletterEditableLabelShell').data('sending', 1);
	jQuery.sendFormNbs({
		btn: jQuery('#nbsNewsletterEditableLabelShell')
	,	data: {mod: 'newsletters', action: 'updateLabel', label: label, id: nbsNewsletter.id}
	,	onSuccess: function(res) {
			if(!res.error) {
				var $labelHtml = jQuery('#nbsNewsletterEditableLabel')
				,	$labelTxt = jQuery('#nbsNewsletterEditableLabelTxt');
				$labelHtml.html( jQuery.trim($labelTxt.val()) );
				$labelTxt.hide( g_nbsAnimationSpeed ).data('ready', 0);
				$labelHtml.show( g_nbsAnimationSpeed );
				jQuery('#nbsNewsletterEditableLabelShell').data('edit-on', 0);
			}
			jQuery('#nbsNewsletterEditableLabelShell').data('sending', 0);
		}
	});
}
/**
 * Make newsletters edit tabs - responsive
 * @param {bool} requring is function - called in requring way
 */
function nbsAdjustNewslettersEditTabs(requring) {
	jQuery('#nbsNewsletterEditTabs .supsystic-always-top')
			.outerWidth( jQuery('#nbsNewsletterEditTabs').width() )
			.attr('data-code-tip', 'Width was set in admin.newsletters.edit.js - nbsAdjustNewslettersEditTabs()');

	var checkTabsNavs = ['#nbsNewsletterEditTabs .nav-tab-wrapper:first'];
	for(var i = 0; i < checkTabsNavs.length; i++) {
		var tabs = jQuery(checkTabsNavs[i])
		,	delta = 10
		,	lineWidth = tabs.width() + delta
		,	fullCurrentWidth = 0
		,	currentState = '';	//full, text, icons

		if(!tabs.find('.nbs-edit-icon').is(':visible')) {
			currentState = 'text';
		} else if(!tabs.find('.nbsNewsletterTabTitle').is(':visible')) {
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
					nbsAdjustNewslettersEditTabs(true);	// Maybe we will require to make it more smaller
					break;
				case 'text':
					tabs.find('.nbs-edit-icon').show().end().find('.nbsNewsletterTabTitle').hide();
					break;
				default:
					// Nothing can do - all that can be hidden - is already hidden
					break;
			}
		} else if(fullCurrentWidth < lineWidth && (lineWidth - fullCurrentWidth > 400) && !requring) {
			switch(currentState) {
				case 'icons':
					tabs.find('.nbs-edit-icon').hide().end().find('.nbsNewsletterTabTitle').show();
					break;
				case 'text':
					tabs.find('.nbs-edit-icon').show().end().find('.nbsNewsletterTabTitle').show();
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
function nbsSaveNewsletterChanges(afterSaveClb) {
	if(afterSaveClb) {
		nbsAfterSaveClb = afterSaveClb;
	}
	jQuery('.nbsNewsletterSaveBtn').click();
}
function nbsRefreshPreview() {
	document.getElementById('nbsNewsletterPreviewFrame').contentWindow.location.reload();
}
function nbsMakeAutoUpdate(delay) {
	if(parseInt(toeOptionNbs('disable_autosave'))) {
		return;	// Autosave disabled in admin area
	}
	delay = delay ? delay : 1500;
	if(nbsNewsletterSaveTimeout)
		clearTimeout( nbsNewsletterSaveTimeout );
	nbsNewsletterSaveTimeout = setTimeout(nbsSaveNewsletterChanges, delay);
}
function nbsShowPreviewUpdating() {
	this._posSet;
	if(!this._posSet) {
		this._posSet = true;
		jQuery('#nbsNewsletterPreviewUpdatingMsg').css({
			'left': 'calc(50% - '+ (jQuery('#nbsNewsletterPreviewUpdatingMsg').width() / 2)+ 'px)'
		});
	}
	jQuery('#nbsNewsletterPreviewFrame').css({
		'opacity': 0.5
	});
	jQuery('#nbsNewsletterPreviewUpdatingMsg').slideDown( g_nbsAnimationSpeed );
}
function nbsHidePreviewUpdating() {
	jQuery('#nbsNewsletterPreviewFrame').show().css({
		'opacity': 1
	});
	jQuery('#nbsNewsletterPreviewUpdatingMsg').slideUp( 100 );
}
function nbsNewsletterInitSaveAsCopyDlg() {
	var $container = jQuery('#nbsNewsletterSaveAsCopyWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 460
	,	height: 180
	,	buttons:  {
			OK: function() {
				jQuery('#nbsNewsletterSaveAsCopyNewsletter').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('#nbsNewsletterSaveAsCopyNewsletter').submit(function(){
		jQuery(this).sendFormNbs({
			msgElID: 'nbsNewsletterSaveAsCopyMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link, true );
				}
			}
		});
		return false;
	});
	jQuery('.nbsNewsletterCloneBtn').click(function(){
		$container.dialog('open');
		return false;
	});
}
function nbsUpdateRecipientsCnt() {
	var $slidSelect = jQuery('#nbsNewsletterEditNewsletter [name="slid[]"]')
	,	selected = $slidSelect.val()
	,	res = 0;
	if(selected && selected.length) {
		for(var i = 0; i < selected.length; i++) {
			var listLabelWithNum = $slidSelect.find('option[value="'+ selected[ i ]+ '"]').html()
			,	cntMatch = listLabelWithNum ? listLabelWithNum.match(/.+ \((\d+)\)/) : false;
			if(cntMatch && cntMatch[ 1 ] && parseInt(cntMatch[ 1 ])) {
				res += parseInt( cntMatch[ 1 ] );
			}
		}
	}
	jQuery('.nbsNlRecipientsCnt').html( res );
}
