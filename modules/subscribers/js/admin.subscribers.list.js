var g_nbsSubscribersTblBulk = {
	_$: null
,	init: function() {
		this._$ = jQuery('#nbsSubscribersTblBulk');
	}
,	show: function() {
		//this._$.slideDown( g_nbsAnimationSpeed );
	}
,	hide: function() {
		//this._$.slideUp( g_nbsAnimationSpeed );
	}
};
jQuery(document).ready(function(){
	g_nbsSubscribersTblBulk.init();
	var tblId = 'nbsSubscribersTbl';
	jQuery('#'+ tblId).jqGrid({ 
		url: nbsTblDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangNbs('ID'), toeLangNbs('Email'), toeLangNbs('Subscribed To'), toeLangNbs('Joined On')]
	,	colModel:[
			{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '50', align: 'center'}
		,	{name: 'email', index: 'email', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'lists_desc', index: 'lists_desc', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'date_created', index: 'date_created', searchoptions: {sopt: ['eq']}, align: 'center'}
		]
	,	postData: {
			search: {
				text_like: jQuery('#'+ tblId+ 'SearchTxt').val()
			,	search_list: jQuery('#'+ tblId+ 'SearchList').val()
			}
		}
	,	rowNum: 25
	,	rowList: [25, 100, 250, 1000, 10000]
	,	pager: '#'+ tblId+ 'Nav'
	,	sortname: 'id'
	,	viewrecords: true
	,	sortorder: 'desc'
	,	jsonReader: { repeatitems : false, id: '0' }
	,	caption: toeLangNbs('Current Subscriber')
	,	height: '100%' 
	,	emptyrecords: toeLangNbs('You have no Subscribers for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, e) {
			var tblId = jQuery(this).attr('id')
			,	selectedRowIds = jQuery('#'+ tblId).jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#'+ tblId).getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(totalRowsSelected) {
				jQuery('#nbsSubscribersRemoveGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_'+ tblId).prop('indeterminate', false);
					jQuery('#cb_'+ tblId).attr('checked', 'checked');
				} else {
					jQuery('#cb_'+ tblId).prop('indeterminate', true);
				}
				g_nbsSubscribersTblBulk.show();
			} else {
				jQuery('#nbsSubscribersRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_'+ tblId).prop('indeterminate', false);
				jQuery('#cb_'+ tblId).removeAttr('checked');
				g_nbsSubscribersTblBulk.hide();
			}
			nbsCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			nbsCheckUpdate('#cb_'+ tblId);
		}
	,	gridComplete: function(a, b, c) {
			var tblId = jQuery(this).attr('id');
			jQuery('#nbsSubscribersRemoveGroupBtn').attr('disabled', 'disabled');
			g_nbsSubscribersTblBulk.hide();
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
	jQuery('#'+ tblId+ 'NavShell').append( jQuery('#'+ tblId+ 'Bulk') );
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
		//if(searchVal && searchVal != '') {
			nbsGridDoListSearch({
				text_like: searchVal
			}, tblId);
		//}
	});
	jQuery('#'+ tblId+ 'SearchList').change(function(){
		nbsGridDoListSearch({
			search_list: jQuery(this).val()
		}, tblId);
	});
	jQuery('#'+ tblId+ 'EmptyMsg').insertAfter(jQuery('#'+ tblId+ '').parent());
	jQuery('#'+ tblId+ '').jqGrid('navGrid', '#'+ tblId+ 'Nav', {edit: false, add: false, del: false});
	jQuery('#cb_'+ tblId+ '').change(function(){
		if(jQuery(this).attr('checked')) {
			jQuery('#nbsSubscribersRemoveGroupBtn').removeAttr('disabled')
			g_nbsSubscribersTblBulk.show();
		} else {
			jQuery('#nbsSubscribersRemoveGroupBtn').attr('disabled', 'disabled');
			g_nbsSubscribersTblBulk.hide();
		}
	});
	jQuery('#nbsSubscribersRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#nbsSubscribersTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#nbsSubscribersTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		var subscribersLabel = '';
		if(listIds.length == 1) {	// In table label cell there can be some additional links
			var labelCellData = nbsGetGridColDataById(listIds[0], 'email', 'nbsSubscribersTbl');
			subscribersLabel = jQuery(labelCellData).text();
		}
		var confirmMsg = listIds.length > 1
			? toeLangNbs('Are you sur want to remove '+ listIds.length+ ' Subscriber?')
			: toeLangNbs('Are you sure want to remove "'+ subscribersLabel+ '" Subscribers?')
		if(confirm(confirmMsg)) {
			jQuery.sendFormNbs({
				btn: this
			,	data: {mod: 'subscribers', action: 'removeGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#nbsSubscribersTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	nbsInitCustomCheckRadio('#'+ tblId+ '_cb');
	nbsInitCustomSelects('.chosen');
	
});
