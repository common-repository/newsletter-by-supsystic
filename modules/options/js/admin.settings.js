jQuery(document).ready(function(){
	jQuery('#nbsSettingsSaveBtn').click(function(){
		nbsSaveSettings();
		return false;
	});
	jQuery('#nbsSettingsForm').submit(function(){
		nbsSaveSettings();
		return false;
	});
	/*Connected options: some options need to be visible  only if in other options selected special value (e.g. if send engine SMTP - show SMTP options)*/
	var $connectOpts = jQuery('#nbsSettingsForm').find('[data-connect]');
	if($connectOpts && $connectOpts.length) {
		var $connectedTo = {};
		$connectOpts.each(function(){
			var connectToData = jQuery(this).data('connect').split(':')
			,	$connectTo = jQuery('#nbsSettingsForm').find('[name="opt_values['+ connectToData[ 0 ]+ ']"]')
			,	connected = $connectTo.data('connected');
			if(!connected) connected = {};
			if(!connected[ connectToData[1] ]) connected[ connectToData[1] ] = [];
			connected[ connectToData[1] ].push( this );
			$connectTo.data('connected', connected);
			if(!$connectTo.data('binded')) {
				$connectTo.change(function(){
					var connected = jQuery(this).data('connected')
					,	value = jQuery(this).val();
					if(connected) {
						for(var connectVal in connected) {
							if(connected[ connectVal ] && connected[ connectVal ].length) {
								var show = connectVal == value;
								for(var i = 0; i < connected[ connectVal ].length; i++) {
									show 
									? jQuery(connected[ connectVal ][ i ]).show() 
									: jQuery(connected[ connectVal ][ i ]).hide();
								}
							}
						}
					}
				});
				$connectTo.data('binded', 1);
			}
			$connectedTo[ connectToData[ 0 ] ] = $connectTo;
		});
		for(var connectedName in $connectedTo) {
			$connectedTo[ connectedName ].change();
		}
	}
	/*Test email sending engine*/
	jQuery('#nbsSendTestMailBtn').click(function(){
		var $btn = jQuery( this );
		$btn.setBtnLoadNbs();
		nbsSaveSettings(function(){
			$btn.backBtnLoadNbs();
			jQuery.sendFormNbs({
				btn: $btn
			,	msgElID: 'nbsSendTestMailBtnMsg'
			,	data: {mod: 'mail', action: 'testEmail', test_email: jQuery('#nbsSettingsForm [name="opt_values[send_engine_test]"]').val()}
			,	onSuccess: function( res ) {
					
				}
			});
		});
		return false;
	});
});
function nbsSaveSettings( clb ) {
	jQuery('#nbsSettingsForm').sendFormNbs({
		btn: jQuery('#nbsSettingsSaveBtn')
	,	onSuccess: function( res ) {
			if(clb && typeof(clb) === 'function') {
				clb( res );
			}
		}
	});
}