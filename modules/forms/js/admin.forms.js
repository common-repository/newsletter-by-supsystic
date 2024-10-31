var g_nbsPromoTplSelected = false;
jQuery(document).ready(function(){
	if(typeof(nbsOriginalForm) !== 'undefined') {	// Just changing template - for existing forms
		nbsInitChangeFormDialog();
	} else {			// Creating new forms
		nbsInitCreateFormDialog();
	}
	if(jQuery('.nbsTplPrevImg').length) {	// If on creation page
		nbsAdjustPreviewSize();
		jQuery(window).resize(function(){
			nbsAdjustPreviewSize();
		});
	}
});

function nbsAdjustPreviewSize() {
	var shellWidth = parseInt(jQuery('.forms-list').width())
	,	initialMaxWidth = 400
	,	startFrom = 860
	,	endFrom = 500;
	if(shellWidth < startFrom && shellWidth > endFrom) {
		jQuery('.nbsTplPrevImg').css('max-width', initialMaxWidth - Math.floor((startFrom - shellWidth) / 2));
	} else if(shellWidth < endFrom || shellWidth > startFrom) {
		jQuery('.nbsTplPrevImg').css('max-width', initialMaxWidth);
	}
}
function nbsInitChangeFormDialog() {
	// Pre-select current Form template
	jQuery('.forms-list-item[data-id="'+ nbsOriginalForm.original_id+ '"]').addClass('active');
	var $container = jQuery('#nbsChangeTplWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 460
	,	height: 180
	,	buttons:  {
			OK: function() {
				jQuery('#nbsChangeTplForm').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('.forms-list-item').click(function(){
		var id = parseInt(jQuery(this).data('id'));
		if(!id) {
			g_nbsPromoTplSelected = true;
			_nbsShowPromoFormForTpl( this );
			return;
		}
		g_nbsPromoTplSelected = false;
		if(nbsOriginalForm.original_id == id) {
			var dialog = jQuery('<div />').html(toeLangNbs('This is the same template that was used for Form before.')).dialog({
				modal:    true
			,	width: 480
			,	height: 180
			,	buttons: {
					OK: function() {
						dialog.dialog('close');
					}
				}
			,	close: function() {
					dialog.remove();
				}
			});
			return false;
		}
		jQuery('#nbsChangeTplForm').find('[name=id]').val( nbsOriginalForm.id );
		jQuery('#nbsChangeTplForm').find('[name=new_tpl_id]').val( id );
		jQuery('#nbsChangeTplNewLabel').html( jQuery(this).find('.nbsTplLabel').html() )
		jQuery('#nbsChangeTplMsg').html('');
		$container.dialog('open');
		return false;
	});
	jQuery('#nbsChangeTplForm').submit(function(){
		jQuery(this).sendFormNbs({
			msgElID: 'nbsChangeTplMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			} 
		});
		return false;
	});
}
function nbsInitCreateFormDialog() {
	jQuery('.forms-list-item').click(function(){
		var id = parseInt(jQuery(this).data('id'));
		jQuery('.forms-list-item').removeClass('active');
		jQuery(this).addClass('active');
		if(id) {
			jQuery('#nbsCreateFormFrm').find('[name=original_id]').val( jQuery(this).data('id') );
		}
		if(id) {
			g_nbsPromoTplSelected = false;
			return false;
		} else {
			g_nbsPromoTplSelected = true;
		}
	});
	jQuery('#nbsCreateFormFrm').submit(function(){
		if(g_nbsPromoTplSelected) {
			_nbsShowPromoFormForTpl();
			return false;
		}
		jQuery(this).sendFormNbs({
			btn: jQuery(this).find('button')
		,	msgElID: 'nbsCreateFormMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			} 
		});
		return false;
	});
}
function _nbsShowPromoFormForTpl( $tplItem ) {
	var $proOptWnd = nbsGetMainPromoForm()
	,	selectedTplHref = $tplItem 
			? jQuery($tplItem).find('a.nbsPromoTplBtn').attr('href') 
			: jQuery('.forms-list-item.active a.nbsPromoTplBtn').attr('href');
	jQuery('#nbsOptInProWnd a').attr('href', selectedTplHref);
	$proOptWnd.dialog('open');
	jQuery('#nbsOptWndTemplateTxt').show();
	jQuery('#nbsOptWndOptionTxt').hide();
}
function nbsFormRemoveRow(id, link) {
	var tblId = jQuery(link).parents('table.ui-jqgrid-btable:first').attr('id');
	if(confirm(toeLangNbs('Are you sure want to remove "'+ nbsGetGridColDataById(id, 'label', tblId)+ '" Pop-Up?'))) {
		jQuery.sendFormNbs({
			btn: link
		,	data: {mod: 'forms', action: 'remove', id: id}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#'+ tblId).trigger( 'reloadGrid' );
				}
			}
		});
	}
}