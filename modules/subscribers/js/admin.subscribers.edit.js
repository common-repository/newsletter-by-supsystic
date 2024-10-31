var g_nbsSubscriber = typeof(nbsSubscriber) === 'undefined' ? false : nbsSubscriber;
jQuery(document).ready(function(){
	jQuery('#nbsSubFrm').submit(function(){
		jQuery(this).sendFormNbs({
			btn: jQuery('#nbsSubSaveBtn')
		,	onSuccess: function(res) {
				if(!res.error) {
					// Subscriber was just added - fill required data
					if(!g_nbsSubscriber) {
						g_nbsSubscriber = res.data.subscriber;
						jQuery('#nbsSubFrm').find('[name="id"]').val( g_nbsSubscriber.id );
						jQuery('#nbsSubRemoveBtn').show( g_nbsAnimationSpeed );
					}
				}
			}
		});
		return false;
	});
	jQuery('#nbsSubSaveBtn').click(function(){
		jQuery('#nbsSubFrm').submit();
		return false;
	});
	jQuery('#nbsSubRemoveBtn').click(function(){
		if(confirm(toeLangNbs('Are you sure want to remove current Subscriber?'))) {
			jQuery.sendFormNbs({
				btn: this
			,	data: {mod: 'subscribers', action: 'remove', id: g_nbsSubscriber.id}
			,	onSuccess: function(res) {
					if(!res.error) {
						toeRedirect( nbsAddNewUrl );
					}
				}
			});
		}
		return false;
	});
	// Correct sticky navbar
	jQuery('#supsystic-breadcrumbs').bind('startSticky', function(){
		var currentPadding = parseInt(jQuery('#nbsSubMainControllsShell').css('padding-right'));
		jQuery('#nbsSubMainControllsShell').css('padding-right', currentPadding + 200).attr('data-padding-changed', 'padding is changed in admin.subscribers.edit.js');
	});
	jQuery('#supsystic-breadcrumbs').bind('stopSticky', function(){
		var currentPadding = parseInt(jQuery('#nbsSubMainControllsShell').css('padding-right'));
		jQuery('#nbsSubMainControllsShell').css('padding-right', currentPadding - 200);
	});
	nbsInitCustomSelects();
});