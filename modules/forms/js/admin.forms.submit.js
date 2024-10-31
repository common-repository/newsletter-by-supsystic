jQuery(document).ready(function(){
	// Test email functionality
	jQuery('.nbsTestEmailFuncBtn').click(function(){
		jQuery.sendFormNbs({
			btn: this
		,	data: {mod: 'mail', action: 'testEmail', test_email: jQuery('#nbsFormEditForm input[name="params[tpl][test_email]"]').val()}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('.nbsTestEmailWasSent').slideDown( g_nbsAnimationSpeed );
				}
			}
		});
		return false;
	});
});