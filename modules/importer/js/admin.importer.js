var g_nbsImpFrame = {
	_source: ''
,	_$sourceRows: null
,	_$listsRow: null
,	_$form: null
,	_initFormActions: {mod: '', action: ''}
,	setForm: function( form ) {
		this._$form = jQuery( form );
	}
,	setSource: function( source ) {
		this._source = source;
		return this;
	}
,	getSource: function() {
		return this._source;
	}
,	switchSourceRows: function() {
		if(!this._$sourceRows) {
			this._$sourceRows = jQuery('.nbsImpSourceSetRow');
		}
		this._$sourceRows.hide().filter('[data-source="'+ this._source+ '"]').show();
		return this;
	}
,	checGetLists: function() {
		this._$listsRow = this._$sourceRows.filter('[data-for="lists"]');
		if(this._$listsRow && this._$listsRow.length) {
			this.getLists();
		}
		return this;
	}
,	getLists: function() {
		if(this._$listsRow) {
			// Check if all required fields was filled-in
			var $requiredFields = this._$sourceRows.find('[data-required-for="lists"]')
			,	requiredFieldsFilled = true;
			if($requiredFields && $requiredFields.length) {
				$requiredFields.each(function(){
					var value = jQuery.trim( jQuery(this).val() );
					if(!value) {
						requiredFieldsFilled = false;
						return false;
					}
				});
			}
			var $listsShell = this._$listsRow.find('.nbsImpListsShell')
			,	$noFiledDataErrorMsg = this._$listsRow.find('.nbsImpNoDataForListsError');
			if(requiredFieldsFilled) {
				$noFiledDataErrorMsg.hide();
				var self = this
				,	$listsSelect = $listsShell.find('select')
				this._saveInitFormActions();
				this._$form.find('[name="mod"]').val('api_loader');
				this._$form.find('[name="action"]').val('getLists');
				this._$form.sendFormNbs({
					msgElID: this._$listsRow.find('.nbsImpListsMsg')
				,	onSuccess: function( res ) {
						self._restoreInitFormActions();
						if(!res.error) {
							$listsSelect.html('');
							var selectedListsIds = self.getSet(self._source, 'lists');
							for(var i in res.data.lists) {
								var listId = res.data.lists[ i ].id
								,	listName = res.data.lists[ i ].name
								,	selected = (selectedListsIds && toeInArrayNbs(listId, selectedListsIds)) ? 'selected="selected"' : '';
								$listsSelect.append('<option '+ selected+ ' value="'+ listId+ '">'+ listName+ '</option>');
							}
							$listsShell.show();
							$listsSelect.chosen().trigger('chosen:updated');
						} else {
							$listsShell.hide();
						}
					}
				});
			} else {
				$noFiledDataErrorMsg.show();
				$listsShell.hide();
			}
		}
		return this;
	}
,	_saveInitFormActions: function() {
		this._initFormActions.mod = this._$form.find('[name="mod"]').val();
		this._initFormActions.action = this._$form.find('[name="action"]').val();
	}
,	_restoreInitFormActions: function() {
		this._$form.find('[name="mod"]').val( this._initFormActions.mod );
		this._$form.find('[name="action"]').val( this._initFormActions.action );
	}
,	getSet: function() {
		if(typeof(nbsApiSets) === 'undefined') return false;
		var setVal = false
		,	keys = arguments;
		if(keys && keys.length) {
			for(var i = 0; i < keys.length; i++) {
				var k = keys[ i ];
				if(i) {
					setVal = (setVal && setVal[ k ]) ? setVal[ k ] : false;
				} else {
					setVal = nbsApiSets[ k ];
				}
			}
		}
		return setVal;
	}
};
jQuery(document).ready(function(){
	g_nbsImpFrame.setForm('#nbsImpForm');
	nbsInitCustomSelects('.chosen', {
		autoWidth: true
	});
	jQuery('#nbsImpForm').submit(function(){
		jQuery(this).sendFormNbs({
			btn: jQuery(this).find('button')
		,	msgElID: 'nbsImpMsg'
		});
		return false;
	});
	jQuery('#nbsImpForm [name="sets[source]"]').change(function(){
		var source = jQuery(this).val();
		g_nbsImpFrame.setSource( source ).switchSourceRows().checGetLists();
	}).change();
	jQuery('#nbsImpForm [data-required-for="lists"]').change(function(){
		var source = jQuery(this).parents('.nbsImpSourceSetRow:first').data('source');
		if(source == g_nbsImpFrame.getSource()) {
			g_nbsImpFrame.checGetLists();
		}
	});
	jQuery('#nbsImpForm [name="sets[import_with_lists]"]').change(function(){
		var enabled = jQuery(this).prop('checked');
		if(enabled) {
			jQuery('.nbsImpToListsShell').hide( g_nbsAnimationSpeed );
			jQuery('.nbsImpIgnoreSameShell').show( g_nbsAnimationSpeed );
		} else {
			jQuery('.nbsImpToListsShell').show( g_nbsAnimationSpeed );
			jQuery('.nbsImpIgnoreSameShell').hide( g_nbsAnimationSpeed );
		}
	}).change();
	// Check if we came from Subscribe List
	var hashParams = toeGetHashParams()
	,	openForListId = 0;
	if(hashParams && hashParams.length) {
		for(var i = 0; i < hashParams.length; i++) {
			if(hashParams[ i ]) {
				openForListId = parseInt(nbs_str_replace( hashParams[ i ], 'slid', '' ));
				if(openForListId) break;
			}
		}
	}
	if(openForListId) {
		nbsCheckUpdate( jQuery('#nbsImpForm [name="sets[import_with_lists]"]').removeProp('checked', 'checked').change() );
		nbsUpdateCustomSelects( jQuery('#nbsImpForm [name="sets[import_to_list]"]').val( openForListId ) );
	}
});