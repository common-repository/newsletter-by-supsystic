jQuery(document).ready(function(){
	jQuery('.nbsSubLoginFrm').submit(function(){
		jQuery(this).sendFormNbs({
			msgElID: jQuery(this).find('.nbsSubLoginMsg')
		,	onSuccess: function( res ) {
				if(!res.error) {
					// Hide form after login
					var $frm = jQuery('.nbsSubLoginFrm');
					$frm.parent().append( $frm.find('.nbsSubLoginMsg') );
					jQuery('.nbsSubLoginFrm').slideUp(g_nbsAnimationSpeed, function(){
						jQuery(this).remove();
					});
				}
			}
		});
		return false;
	});
});