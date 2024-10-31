jQuery(document).ready(function(){
	jQuery('.nbsSubUpdateProfileFrm').submit(function(){
		jQuery(this).sendFormNbs({
			msgElID: jQuery(this).find('.nbsSubUpdateProfileMsg')
		});
		return false;
	});
});