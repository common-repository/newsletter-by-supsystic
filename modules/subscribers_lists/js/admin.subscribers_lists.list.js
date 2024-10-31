jQuery(document).ready(function(){
	var tblId = 'nbsSubscribersListsTbl';
	jQuery('#'+ tblId).jqGrid({ 
		url: nbsTblDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangNbs('ID'), toeLangNbs('List Name'), toeLangNbs('Subscribers'), toeLangNbs('Unsubscribed'), toeLangNbs('Newsletters Used'), toeLangNbs('Created On')]
	,	colModel:[
			{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '50', align: 'center'}
		,	{name: 'label', index: 'label', searchoptions: {sopt: ['eq']}, align: 'center'}
		
		,	{name: 'subscribers_cnt', index: 'subscribers_cnt', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'unsubscribed_cnt', index: 'unsubscribed_cnt', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'newletters_cnt', index: 'newletters_cnt', searchoptions: {sopt: ['eq']}, align: 'center'}
		
		,	{name: 'date_created', index: 'date_created', searchoptions: {sopt: ['eq']}, align: 'center'}
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
	,	caption: toeLangNbs('Current Subscribers List')
	,	height: '100%' 
	,	emptyrecords: toeLangNbs('You have no Subscribers Lists for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, checked, event) {
			var tblId = jQuery(this).attr('id')
			,	selectedRowIds = jQuery('#'+ tblId).jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#'+ tblId).getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(checked && event && event.target) {
				var $checkbox = jQuery('#'+ tblId+ ' #'+ rowid).find('input[type="checkbox"]');
				if($checkbox && $checkbox.length && $checkbox.attr('disabled')) {
					jQuery("#"+ tblId).jqGrid("resetSelection");
					nbsCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
					nbsCheckUpdate('#cb_'+ tblId);
					return;
				}
			}
			if(totalRowsSelected) {
				jQuery('#nbsSubscribersListsRemoveGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_'+ tblId).prop('indeterminate', false);
					jQuery('#cb_'+ tblId).attr('checked', 'checked');
				} else {
					jQuery('#cb_'+ tblId).prop('indeterminate', true);
				}
			} else {
				jQuery('#nbsSubscribersListsRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_'+ tblId).prop('indeterminate', false);
				jQuery('#cb_'+ tblId).removeAttr('checked');
			}
			nbsCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			nbsCheckUpdate('#cb_'+ tblId);
		}
	,	ajaxGridOptions: {
			dataFilter: function(data) {
				jQuery('#'+ tblId).get(0)._nbsResData = JSON.parse(data);
				return data;
			}
		}
	,	gridComplete: function() {
			var tblId = jQuery(this).attr('id');
			jQuery('#nbsSubscribersListsRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#cb_'+ tblId).prop('indeterminate', false);
			jQuery('#cb_'+ tblId).removeAttr('checked');
			if(this._nbsResData && this._nbsResData.rows && this._nbsResData.rows.length) {
				for(var i = 0; i < this._nbsResData.rows.length; i++) {
					if(this._nbsResData.rows[ i ].unique_id == nbsWpListUniqueId) {
						jQuery('#'+ tblId+ ' #'+ (i + 1)).find('input[type="checkbox"]').attr('disabled', 'disabled');
					}
				}
			}
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
			? jQuery('#nbsSubscribersListsRemoveGroupBtn').removeAttr('disabled')
			: jQuery('#nbsSubscribersListsRemoveGroupBtn').attr('disabled', 'disabled');
	});
	jQuery('#nbsSubscribersListsRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#nbsSubscribersListsTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#nbsSubscribersListsTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		var subscribers_listsLabel = '';
		if(listIds.length == 1) {	// In table label cell there can be some additional links
			var labelCellData = nbsGetGridColDataById(listIds[0], 'label', 'nbsSubscribersListsTbl');
			subscribers_listsLabel = jQuery(labelCellData).text();
		}
		var confirmMsg = listIds.length > 1
			? toeLangNbs('Are you sur want to remove '+ listIds.length+ ' Subscribers Lists?')
			: toeLangNbs('Are you sure want to remove "'+ subscribers_listsLabel+ '" Subscribers List?')
		if(confirm(confirmMsg)) {
			jQuery.sendFormNbs({
				btn: this
			,	data: {mod: 'subscribers_lists', action: 'removeGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#nbsSubscribersListsTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	nbsInitCustomCheckRadio('#'+ tblId+ '_cb');
	// Add new button function
	_nbsInitAddSubListDlg();
});
function _nbsInitAddSubListDlg() {
	var $dlg = jQuery('#nbsAddSubListWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 460
	,	height: 180
	,	buttons:  {
			OK: function() {
				jQuery('#nbsAddSubListFrm').submit();
			}
		}
	});
	jQuery('.nbsAddSubListBtn').click(function(){
		$dlg.dialog('open');
		return false;
	});
	jQuery('#nbsAddSubListFrm').submit(function(){
		jQuery(this).sendFormNbs({
			msgElID: 'nbsAddSubListMsg'
		,	onSuccess: function(res) {
				if(!res.error && res.data.sub_list_url) {
					toeRedirect(res.data.sub_list_url);
				}
			}
		});
		return false;
	});
	if(toeInArrayNbs('nbsAddSubList', toeGetHashParams())) {
		jQuery('#nbsAddSubListBtn').click();
	}
}
