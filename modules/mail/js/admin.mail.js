jQuery(document).ready(function(){
	jQuery('#nbsMailTestForm').submit(function(){
		jQuery(this).sendFormNbs({
			btn: jQuery(this).find('button:first')
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#nbsMailTestForm').slideUp( 300 );
					jQuery('#nbsMailTestResShell').slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('.nbsMailTestResBtn').click(function(){
		var result = parseInt(jQuery(this).data('res'));
		jQuery.sendFormNbs({
			btn: this
		,	data: {mod: 'mail', action: 'saveMailTestRes', result: result}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#nbsMailTestResShell').slideUp( 300 );
					jQuery('#'+ (result ? 'nbsMailTestResSuccess' : 'nbsMailTestResFail')).slideDown( 300 );
				}
			}
		});
		return false;
	});
	jQuery('#nbsMailSettingsForm').submit(function(){
		jQuery(this).sendFormNbs({
			btn: jQuery(this).find('button:first')
		});
		return false; 
	});
});