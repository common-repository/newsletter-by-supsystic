jQuery(document).ready(function(){
	var tblId = 'nbsNewsletterTbl';
	jQuery('#'+ tblId).jqGrid({ 
		url: nbsTblDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangNbs('ID'), toeLangNbs('Label'), toeLangNbs('Subscribers'), toeLangNbs('Lists'), toeLangNbs('Status'), toeLangNbs('Action')]
	,	colModel:[
			{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '50', align: 'center'}
		,	{name: 'label', index: 'label', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'subscribers_cnt', index: 'subscribers_cnt', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'lists_desc', index: 'lists_desc', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'status_desc', index: 'status_desc', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'action_btns', index: 'action_btns', searchoptions: {sopt: ['eq']}, align: 'center'}
		]
	,	postData: {
			search: {
				text_like: jQuery('#'+ tblId+ 'SearchTxt').val()
			}
		}
	,	rowNum:10
	,	rowList:[10, 20, 30, 1000]
	,	pager: '#'+ tblId+ 'Nav'
	,	sortname: 'id'
	,	viewrecords: true
	,	sortorder: 'desc'
	,	jsonReader: { repeatitems : false, id: '0' }
	,	caption: toeLangNbs('Current Newsletter')
	,	height: '100%' 
	,	emptyrecords: toeLangNbs('You have no Newsletters for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, e) {
			var tblId = jQuery(this).attr('id')
			,	selectedRowIds = jQuery('#'+ tblId).jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#'+ tblId).getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(totalRowsSelected) {
				jQuery('#nbsNewsletterRemoveGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_'+ tblId).prop('indeterminate', false);
					jQuery('#cb_'+ tblId).attr('checked', 'checked');
				} else {
					jQuery('#cb_'+ tblId).prop('indeterminate', true);
				}
			} else {
				jQuery('#nbsNewsletterRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_'+ tblId).prop('indeterminate', false);
				jQuery('#cb_'+ tblId).removeAttr('checked');
			}
			nbsCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			nbsCheckUpdate('#cb_'+ tblId);
		}
	,	gridComplete: function(a, b, c) {
			var tblId = jQuery(this).attr('id');
			jQuery('#nbsNewsletterRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#cb_'+ tblId).prop('indeterminate', false);
			jQuery('#cb_'+ tblId).removeAttr('checked');
			// Custom checkbox manipulation
			nbsInitCustomCheckRadio('#'+ jQuery(this).attr('id') );
			nbsCheckUpdate('#cb_'+ jQuery(this).attr('id'));
			var $sendingRowsIndicators = jQuery('#'+ tblId).find('input[name=sending_row][value=1]');
			if($sendingRowsIndicators && $sendingRowsIndicators.length) {
				$sendingRowsIndicators.each(function(){
					jQuery(this).parents('tr:first').addClass('nbsNlSendingInProgress');
				});
			}
		}
	,	loadComplete: function() {
			var tblId = jQuery(this).attr('id');
			if (this.p.reccount === 0) {
				jQuery(this).hide();
				jQuery('#'+ tblId+ 'EmptyMsg').show();
			} else {
				jQuery(this).show();
				jQuery('#'+ tblId+ 'EmptyMsg').hide();
			}
		}
	});
	jQuery('#'+ tblId+ 'NavShell').append( jQuery('#'+ tblId+ 'Nav') );
	jQuery('#'+ tblId+ 'Nav').find('.ui-pg-selbox').insertAfter( jQuery('#'+ tblId+ 'Nav').find('.ui-paging-info') );
	jQuery('#'+ tblId+ 'Nav').find('.ui-pg-table td:first').remove();
	// Make navigation tabs to be with our additional buttons - in one row
	jQuery('#'+ tblId+ 'Nav_center').prepend( jQuery('#'+ tblId+ 'NavBtnsShell') ).css({
		'width': '80%'
	,	'white-space': 'normal'
	,	'padding-top': '8px'
	});
	jQuery('#'+ tblId+ 'SearchTxt').keyup(function(){
		var searchVal = jQuery.trim( jQuery(this).val() );
		if(searchVal && searchVal != '') {
			nbsGridDoListSearch({
				text_like: searchVal
			}, tblId);
		}
	});
	
	jQuery('#'+ tblId+ 'EmptyMsg').insertAfter(jQuery('#'+ tblId+ '').parent());
	jQuery('#'+ tblId+ '').jqGrid('navGrid', '#'+ tblId+ 'Nav', {edit: false, add: false, del: false});
	jQuery('#cb_'+ tblId+ '').change(function(){
		if(jQuery(this).attr('checked')) {
			jQuery('#nbsNewsletterRemoveGroupBtn').removeAttr('disabled')
		} else {
			jQuery('#nbsNewsletterRemoveGroupBtn').attr('disabled', 'disabled');
		}
	});
	jQuery('#nbsNewsletterRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#nbsNewsletterTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#nbsNewsletterTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		var newslettersLabel = '';
		if(listIds.length == 1) {	// In table label cell there can be some additional links
			var labelCellData = nbsGetGridColDataById(listIds[0], 'label', 'nbsNewsletterTbl');
			newslettersLabel = jQuery(labelCellData).text();
		}
		var confirmMsg = listIds.length > 1
			? toeLangNbs('Are you sur want to remove '+ listIds.length+ ' Newsletters?')
			: toeLangNbs('Are you sure want to remove "'+ newslettersLabel+ '" Newsletter?')
		if(confirm(confirmMsg)) {
			jQuery.sendFormNbs({
				btn: this
			,	data: {mod: 'newsletters', action: 'removeGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#nbsNewsletterTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	nbsInitCustomCheckRadio('#'+ tblId+ '_cb');
});
function nbsTryingEditSendingNewsletterClk( btn ) {
	var editLink = jQuery(btn).attr('href');
	jQuery('#nbsEditSendingNewlsetterWnd').dialog({
		modal:    true
	,	width: 460
	,	buttons:  {
			OK: function() {
				toeRedirect( editLink );
			}
		,	Cancel: function() {
				jQuery('#nbsEditSendingNewlsetterWnd').dialog('close');
			}
		}
	});
	return false;
}
