jQuery(document).ready(function(){
	var tblId = 'nbsFormTbl';
	jQuery('#'+ tblId).jqGrid({ 
		url: nbsTblDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangNbs('ID'), toeLangNbs('Label'), toeLangNbs('Views'), toeLangNbs('Unique Views'), toeLangNbs('Actions'), toeLangNbs('Conversion')]
	,	colModel:[
			{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '50', align: 'center'}
		,	{name: 'label', index: 'label', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'views', index: 'views', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'unique_views', index: 'unique_views', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'actions', index: 'actions', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'conversion', index: 'conversion', searchoptions: {sopt: ['eq']}, align: 'center'}
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
	,	caption: toeLangNbs('Current Form')
	,	height: '100%' 
	,	emptyrecords: toeLangNbs('You have no Forms for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, e) {
			var tblId = jQuery(this).attr('id')
			,	selectedRowIds = jQuery('#'+ tblId).jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#'+ tblId).getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(totalRowsSelected) {
				jQuery('#nbsFormRemoveGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_'+ tblId).prop('indeterminate', false);
					jQuery('#cb_'+ tblId).attr('checked', 'checked');
				} else {
					jQuery('#cb_'+ tblId).prop('indeterminate', true);
				}
			} else {
				jQuery('#nbsFormRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_'+ tblId).prop('indeterminate', false);
				jQuery('#cb_'+ tblId).removeAttr('checked');
			}
			nbsCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			nbsCheckUpdate('#cb_'+ tblId);
		}
	,	gridComplete: function(a, b, c) {
			var tblId = jQuery(this).attr('id');
			jQuery('#nbsFormRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#cb_'+ tblId).prop('indeterminate', false);
			jQuery('#cb_'+ tblId).removeAttr('checked');
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
		jQuery(this).attr('checked') 
			? jQuery('#nbsFormRemoveGroupBtn').removeAttr('disabled')
			: jQuery('#nbsFormRemoveGroupBtn').attr('disabled', 'disabled');
	});
	jQuery('#nbsFormRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#nbsFormTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#nbsFormTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		var formsLabel = '';
		if(listIds.length == 1) {	// In table label cell there can be some additional links
			var labelCellData = nbsGetGridColDataById(listIds[0], 'label', 'nbsFormTbl');
			formsLabel = jQuery(labelCellData).text();
		}
		var confirmMsg = listIds.length > 1
			? toeLangNbs('Are you sur want to remove '+ listIds.length+ ' Forms?')
			: toeLangNbs('Are you sure want to remove "'+ formsLabel+ '" Form?')
		if(confirm(confirmMsg)) {
			jQuery.sendFormNbs({
				btn: this
			,	data: {mod: 'forms', action: 'removeGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#nbsFormTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	nbsInitCustomCheckRadio('#'+ tblId+ '_cb');
});
