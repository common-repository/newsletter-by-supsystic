var g_nbsPromoTplSelected = false;
jQuery(document).ready(function(){
	if(typeof(nbsOriginalNewsletter) !== 'undefined') {	// Just changing template - for existing newsletters
		nbsInitChangeNewsletterDialog();
	} else {			// Creating new newsletters
		nbsInitCreateNewsletterDialog();
		nbsInitCustomSelects();
	}
	if(jQuery('.nbsTplPrevImg').length) {	// If on creation page
		nbsAdjustPreviewSize();
		jQuery(window).resize(function(){
			nbsAdjustPreviewSize();
		});
	}
	var $tplsTabs = jQuery('#nbsTplsTabs');
	if($tplsTabs && $tplsTabs.length) {
			jQuery('#nbsTplsTabs').wpTabs({
				uniqId: 'nbsTplsTabs'
			,	change: function(selector) {

				}
			});
	}
});

function nbsAdjustPreviewSize() {
	var shellWidth = parseInt(jQuery('.newsletters-list').width())
	,	initialMaxWidth = 400
	,	startFrom = 860
	,	endFrom = 500;
	if(shellWidth < startFrom && shellWidth > endFrom) {
		jQuery('.nbsTplPrevImg').css('max-width', initialMaxWidth - Math.floor((startFrom - shellWidth) / 2));
	} else if(shellWidth < endFrom || shellWidth > startFrom) {
		jQuery('.nbsTplPrevImg').css('max-width', initialMaxWidth);
	}
}
function nbsInitChangeNewsletterDialog() {
	// Pre-select current Newsletter template
	jQuery('.newsletters-list-item[data-id="'+ nbsOriginalNewsletter.original_id+ '"]').addClass('active');
	var $container = jQuery('#nbsChangeTplWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 460
	,	height: 180
	,	buttons:  {
			OK: function() {
				jQuery('#nbsChangeTplNewsletter').submit();
			}
		,	Cancel: function() {
				$container.dialog('close');
			}
		}
	});
	jQuery('.newsletters-list-item').click(function(){
		var id = parseInt(jQuery(this).data('id'));
		if(!id) {
			g_nbsPromoTplSelected = true;
			_nbsShowPromoNewsletterForTpl( this );
			return;
		}
		g_nbsPromoTplSelected = false;
		if(nbsOriginalNewsletter.original_id == id) {
			var dialog = jQuery('<div />').html(toeLangNbs('This is the same template that was used for Newsletter before.')).dialog({
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
		jQuery('#nbsChangeTplNewsletter').find('[name=id]').val( nbsOriginalNewsletter.id );
		jQuery('#nbsChangeTplNewsletter').find('[name=new_tpl_id]').val( id );
		jQuery('#nbsChangeTplNewLabel').html( jQuery(this).find('.nbsTplLabel').html() )
		jQuery('#nbsChangeTplMsg').html('');
		$container.dialog('open');
		return false;
	});
	jQuery('#nbsChangeTplNewsletter').submit(function(){
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
function nbsInitCreateNewsletterDialog() {
	jQuery('.newsletters-list-item').click(function(){
		var id = parseInt(jQuery(this).data('id'));
		jQuery('.newsletters-list-item').removeClass('active');
		jQuery(this).addClass('active');
		if(id) {
			jQuery('#nbsCreateNewsletterFrm').find('[name=oid]').val( jQuery(this).data('id') );
		}
		if(id) {
			g_nbsPromoTplSelected = false;
			return false;
		} else {
			g_nbsPromoTplSelected = true;
		}
	});
	jQuery('#nbsCreateNewsletterFrm').submit(function(){
		if(g_nbsPromoTplSelected) {
			_nbsShowPromoNewsletterForTpl();
			return false;
		}
		jQuery(this).sendFormNbs({
			btn: jQuery(this).find('button')
		,	msgElID: 'nbsCreateNewsletterMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.edit_link) {
					toeRedirect( res.data.edit_link );
				}
			} 
		});
		return false;
	});
}
function _nbsShowPromoNewsletterForTpl( $tplItem ) {
	var $proOptWnd = nbsGetMainPromoNewsletter()
	,	selectedTplHref = $tplItem 
			? jQuery($tplItem).find('a.nbsPromoTplBtn').attr('href') 
			: jQuery('.newsletters-list-item.active a.nbsPromoTplBtn').attr('href');
	jQuery('#nbsOptInProWnd a').attr('href', selectedTplHref);
	$proOptWnd.dialog('open');
	jQuery('#nbsOptWndTemplateTxt').show();
	jQuery('#nbsOptWndOptionTxt').hide();
}
function nbsNewsletterRemoveRow(id, link) {
	var tblId = jQuery(link).parents('table.ui-jqgrid-btable:first').attr('id');
	if(confirm(toeLangNbs('Are you sure want to remove "'+ nbsGetGridColDataById(id, 'label', tblId)+ '" Pop-Up?'))) {
		jQuery.sendFormNbs({
			btn: link
		,	data: {mod: 'newsletters', action: 'remove', id: id}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#'+ tblId).trigger( 'reloadGrid' );
				}
			}
		});
	}
}