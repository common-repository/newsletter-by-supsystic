jQuery(document).ready(function(){
	var tblId = 'nbsPagesTbl';
	jQuery('#'+ tblId).jqGrid({ 
		url: nbsTblDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangNbs('ID'), toeLangNbs('Label'), toeLangNbs('Action')]
	,	colModel:[
			{name: 'ID', index: 'ID', searchoptions: {sopt: ['eq']}, width: '50', align: 'center'}
		,	{name: 'post_title', index: 'post_title', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'actions', index: 'actions', searchoptions: {sopt: ['eq']}, align: 'center'}
		]
	,	postData: {
			search: {
				text_like: jQuery('#'+ tblId+ 'SearchTxt').val()
			}
		}
	,	rowNum:10
	,	rowList:[10, 20, 30, 1000]
	,	pager: '#'+ tblId+ 'Nav'
	,	sortname: 'ID'
	,	viewrecords: true
	,	sortorder: 'desc'
	,	jsonReader: { repeatitems : false, id: '0' }
	,	caption: toeLangNbs('Current Page')
	,	height: '100%' 
	,	emptyrecords: toeLangNbs('You have no Pages for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, e) {
			var tblId = jQuery(this).attr('id')
			,	selectedRowIds = jQuery('#'+ tblId).jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#'+ tblId).getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(totalRowsSelected) {
				jQuery('#nbsPagesRemoveGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_'+ tblId).prop('indeterminate', false);
					jQuery('#cb_'+ tblId).attr('checked', 'checked');
				} else {
					jQuery('#cb_'+ tblId).prop('indeterminate', true);
				}
			} else {
				jQuery('#nbsPagesRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_'+ tblId).prop('indeterminate', false);
				jQuery('#cb_'+ tblId).removeAttr('checked');
			}
			nbsCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			nbsCheckUpdate('#cb_'+ tblId);
		}
	,	gridComplete: function(a, b, c) {
			var tblId = jQuery(this).attr('id');
			jQuery('#nbsPagesRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#cb_'+ tblId).prop('indeterminate', false);
			jQuery('#cb_'+ tblId).removeAttr('checked');
			if(jQuery('#'+ tblId).jqGrid('getGridParam', 'records'))	// If we have at least one row - allow to clear whole list
				jQuery('#nbsPagesClearBtn').removeAttr('disabled');
			else
				jQuery('#nbsPagesClearBtn').attr('disabled', 'disabled');
			// Custom checkbox manipulation
			nbsInitCustomCheckRadio('#'+ jQuery(this).attr('id') );
			nbsCheckUpdate('#cb_'+ jQuery(this).attr('id'));
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
		jQuery(this).attr('checked') 
			? jQuery('#nbsPagesRemoveGroupBtn').removeAttr('disabled')
			: jQuery('#nbsPagesRemoveGroupBtn').attr('disabled', 'disabled');
	});
	jQuery('#nbsPagesRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#nbsPagesTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#nbsPagesTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.ID );
		}
		var popupLabel = '';
		if(listIds.length == 1) {	// In table label cell there can be some additional links
			var labelCellData = nbsGetGridColDataById(listIds[0], 'label', 'nbsPagesTbl');
			popupLabel = jQuery(labelCellData).text();
		}
		var confirmMsg = listIds.length > 1
			? toeLangNbs('Are you sur want to remove '+ listIds.length+ ' Pages?')
			: toeLangNbs('Are you sure want to remove "'+ popupLabel+ '" Page?')
		if(confirm(confirmMsg)) {
			jQuery.sendFormNbs({
				btn: this
			,	data: {mod: 'octo', action: 'removeGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#nbsPagesTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	jQuery('#nbsPagesClearBtn').click(function(){
		if(confirm(toeLangNbs('Clear whole pages list?'))) {
			jQuery.sendFormNbs({
				btn: this
			,	data: {mod: 'octo', action: 'clear'}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#nbsPagesTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	
	nbsInitCustomCheckRadio('#'+ tblId+ '_cb');
});
