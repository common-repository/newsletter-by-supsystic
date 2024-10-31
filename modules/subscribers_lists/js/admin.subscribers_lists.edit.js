var g_nbsSubListCsvImportData = {};
jQuery(document).ready(function(){
	jQuery('#nbsSubListsFrm').submit(function(){
		jQuery(this).sendFormNbs({
			btn: jQuery('#nbsSubListSaveBtn')
		,	onSuccess: function(res) {

			}
		});
		return false;
	});
	jQuery('#nbsSubListSaveBtn').click(function(){
		jQuery('#nbsSubListsFrm').submit();
		return false;
	});
	jQuery('#nbsSubListImportFromTxtFrm').submit(function(){
		var $form = jQuery(this);
		$form.sendFormNbs({
			btn: $form.find('#nbsSubListImportFromTxtBtn')
		,	msgElID: 'nbsSubListImportFromTxtMsg'
		,	onSuccess: function(res) {
				if(!res.error) {
					// Clear emails list in textarea after successful import
					$form.find('[name="emails"]').val('');
				}
			}
		});
		return false;
	});
	// Correct sticky navbar
	jQuery('#supsystic-breadcrumbs').bind('startSticky', function(){
		var currentPadding = parseInt(jQuery('#nbsSubListMainControllsShell').css('padding-right'));
		jQuery('#nbsSubListMainControllsShell').css('padding-right', currentPadding + 200).attr('data-padding-changed', 'padding is changed in admin.subscribers_lists.edit.js');
	});
	jQuery('#supsystic-breadcrumbs').bind('stopSticky', function(){
		var currentPadding = parseInt(jQuery('#nbsSubListMainControllsShell').css('padding-right'));
		jQuery('#nbsSubListMainControllsShell').css('padding-right', currentPadding - 200);
	});
	// Bind CSV params inputs change
	var dataKeys = ['csv_delimiter', 'csv_enclosure', 'csv_escape'];
	for(var i = 0; i < dataKeys.length; i++) {
		var $input = jQuery('#nbsSlCsvOptsTbl [name="'+ dataKeys[i]+ '"]');
		g_nbsSubListCsvImportData[ dataKeys[i] ] = $input.val();
		$input.change(function(){
			g_nbsSubListCsvImportData[ jQuery(this).attr('name') ] = jQuery(this).val();
		});
	}
	// Set GET params to URL
	$("#nbsSubListExportSubmit").click(function(e){
		e.preventDefault();
		var selectedLists = $('#nbsSubListExportSubscriptionLists').val();
		var mode = $(".nbsSubListExportRadiobutton:checked").val();
		var href = $(this).attr("basehref");
		href += '&lists=' + selectedLists;
		href += '&mode=' + mode;
		$(this).attr("href", href);
		window.location.assign(href);
	});
	// Bind CSV export RadioButtons
	$("input[name='nbsSubListExportRadiobutton[]']").change(function(){
	 var mode =	$(this).val();
	 switch(mode) {
			 case 'all':
				 $("#nbsSubListExportSubscriptionLists").attr('disabled','disabled');
				 break;
			 case 'lists':
				 $("#nbsSubListExportSubscriptionLists").removeAttr('disabled');
				break;
		 }
	});
});
function nbsSubListCsvImportSubmitClb(a, b, c, d) {
	jQuery('#toeUploadbut_import_from_csv').setBtnLoadNbs();
	jQuery('#nbsSubListImportFromCsvMsg').html('');
}
function nbsSubListCsvImportCompleteClb(file, res) {
	jQuery('#toeUploadbut_import_from_csv').backBtnLoadNbs();
	toeProcessAjaxResponseNbs(res, 'nbsSubListImportFromCsvMsg');
}
