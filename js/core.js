if(typeof(NBS_DATA) == 'undefined')
	var NBS_DATA = {};
if(isNumber(NBS_DATA.animationSpeed)) 
    NBS_DATA.animationSpeed = parseInt(NBS_DATA.animationSpeed);
else if(jQuery.inArray(NBS_DATA.animationSpeed, ['fast', 'slow']) == -1)
    NBS_DATA.animationSpeed = 'fast';
NBS_DATA.showSubscreenOnCenter = parseInt(NBS_DATA.showSubscreenOnCenter);
var sdLoaderImgNbs = '<img src="'+ NBS_DATA.loader+ '" />';
var g_nbsAnimationSpeed = 300;

jQuery.fn.showLoaderNbs = function() {
    return jQuery(this).html( sdLoaderImgNbs );
};
jQuery.fn.appendLoaderNbs = function() {
    jQuery(this).append( sdLoaderImgNbs );
};
jQuery.sendFormNbs = function(params) {
	// Any html element can be used here
	return jQuery('<br />').sendFormNbs(params);
};
jQuery.fn.setBtnLoadNbs = function() {
	jQuery(this).attr('disabled', 'disabled');
	// Font awesome usage
	var $btnIconElement = jQuery(this).find('.fa');
	if($btnIconElement && $btnIconElement.length) {
		$btnIconElement
			.data('prev-class', $btnIconElement.attr('class'))
			.attr('class', 'fa fa-spinner fa-spin');
	}
};
jQuery.fn.backBtnLoadNbs = function() {
	jQuery(this).removeAttr('disabled');
	var $btnIconElement = jQuery(this).find('.fa').length ? jQuery(this).find('.fa') : jQuery(this);
	$btnIconElement.attr('class', $btnIconElement.data('prev-class'));
};
/**
 * Send form or just data to server by ajax and route response
 * @param string params.fid form element ID, if empty - current element will be used
 * @param string params.msgElID element ID to store result messages, if empty - element with ID "msg" will be used. Can be "noMessages" to not use this feature
 * @param function params.onSuccess funstion to do after success receive response. Be advised - "success" means that ajax response will be success
 * @param array params.data data to send if You don't want to send Your form data, will be set instead of all form data
 * @param array params.appendData data to append to sending request. In contrast to params.data will not erase form data
 * @param string params.inputsWraper element ID for inputs wraper, will be used if it is not a form
 * @param string params.clearMsg clear msg element after receive data, if is number - will use it to set time for clearing, else - if true - will clear msg element after 5 seconds
 */
jQuery.fn.sendFormNbs = function(params) {
    var form = null;
    if(!params)
        params = {fid: false, msgElID: false, onSuccess: false};

    if(params.fid)
        form = jQuery('#'+ fid);
    else
        form = jQuery(this);
    
    /* This method can be used not only from form data sending, it can be used just to send some data and fill in response msg or errors*/
    var sentFromForm = (jQuery(form).tagName() == 'FORM');
    var data = new Array();
    if(params.data)
        data = params.data;
    else if(sentFromForm)
        data = jQuery(form).serialize();
    
    if(params.appendData) {
		var dataIsString = typeof(data) == 'string';
		var addStrData = [];
        for(var i in params.appendData) {
			if(dataIsString) {
				addStrData.push(i+ '='+ params.appendData[i]);
			} else
            data[i] = params.appendData[i];
        }
		if(dataIsString)
			data += '&'+ addStrData.join('&');
    }
    var msgEl = null;
    if(params.msgElID) {
        if(params.msgElID == 'noMessages')
            msgEl = false;
        else if(typeof(params.msgElID) == 'object')
           msgEl = params.msgElID;
       else
            msgEl = jQuery('#'+ params.msgElID);
    }
	if(typeof(params.inputsWraper) == 'string') {
		form = jQuery('#'+ params.inputsWraper);
		sentFromForm = true;
	}
	if(sentFromForm && form) {
        jQuery(form).find('*').removeClass('nbsInputError');
    }
	if(msgEl) {
		jQuery(msgEl)
			.removeClass('nbsSuccessMsg')
			.removeClass('nbsErrorMsg');
		if(!params.btn) {
			jQuery(msgEl).showLoaderNbs();
		}
	} 
	if(params.btn) {
		jQuery(params.btn).setBtnLoadNbs();
	}
    var url = '';
	if(typeof(params.url) != 'undefined')
		url = params.url;
    else if(typeof(ajaxurl) == 'undefined')
        url = NBS_DATA.ajaxurl;
    else
        url = ajaxurl;
    
    jQuery('.nbsErrorForField').hide(NBS_DATA.animationSpeed);
	var dataType = params.dataType ? params.dataType : 'json';
	// Set plugin orientation
	if(typeof(data) == 'string') {
		data += '&pl='+ NBS_DATA.NBS_CODE;
		data += '&reqType=ajax';
	} else {
		data['pl'] = NBS_DATA.NBS_CODE;
		data['reqType'] = 'ajax';
	}
	
    jQuery.ajax({
        url: url,
        data: data,
        type: 'POST',
        dataType: dataType,
        success: function(res) {
            toeProcessAjaxResponseNbs(res, msgEl, form, sentFromForm, params);
			if(params.clearMsg) {
				setTimeout(function(){
					if(msgEl)
						jQuery(msgEl).animateClear();
				}, typeof(params.clearMsg) == 'boolean' ? 5000 : params.clearMsg);
			}
        }
    });
};
/**
 * Hide content in element and then clear it
 */
jQuery.fn.animateClear = function() {
	var newContent = jQuery('<span>'+ jQuery(this).html()+ '</span>');
	jQuery(this).html( newContent );
	jQuery(newContent).hide(NBS_DATA.animationSpeed, function(){
		jQuery(newContent).remove();
	});
};
/**
 * Hide content in element and then remove it
 */
jQuery.fn.animateRemoveNbs = function(animationSpeed, onSuccess) {
	animationSpeed = animationSpeed == undefined ? NBS_DATA.animationSpeed : animationSpeed;
	jQuery(this).hide(animationSpeed, function(){
		jQuery(this).remove();
		if(typeof(onSuccess) === 'function')
			onSuccess();
	});
};
function toeProcessAjaxResponseNbs(res, msgEl, form, sentFromForm, params) {
    if(typeof(params) == 'undefined')
        params = {};
    if(typeof(msgEl) == 'string')
        msgEl = jQuery('#'+ msgEl);
    if(msgEl)
        jQuery(msgEl).html('');
	if(params.btn) {
		jQuery(params.btn).backBtnLoadNbs();
	}
    /*if(sentFromForm) {
        jQuery(form).find('*').removeClass('nbsInputError');
    }*/
    if(typeof(res) == 'object') {
        if(res.error) {
            if(msgEl) {
                jQuery(msgEl)
					.removeClass('nbsSuccessMsg')
					.addClass('nbsErrorMsg');
            }
			var errorsArr = [];
            for(var name in res.errors) {
                if(sentFromForm) {
					var inputError = jQuery(form).find('[name*="'+ name+ '"]');
                    inputError.addClass('nbsInputError');
					if(inputError.attr('placeholder')) {
						//inputError.attr('placeholder', res.errors[ name ]);
					}
					if(!inputError.data('keyup-error-remove-binded')) {
						inputError.keydown(function(){
							jQuery(this).removeClass('nbsInputError');
						}).data('keyup-error-remove-binded', 1);
					}
                }
                if(jQuery('.nbsErrorForField.toe_'+ nameToClassId(name)+ '').exists())
                    jQuery('.nbsErrorForField.toe_'+ nameToClassId(name)+ '').show().html(res.errors[name]);
                else if(msgEl)
                    jQuery(msgEl).append(res.errors[name]).append('<br />');
				else
					errorsArr.push( res.errors[name] );
            }
			if(errorsArr.length && params.btn && jQuery.fn.dialog && !msgEl) {
				jQuery('<div title="'+ toeLangNbs("Really small warning :)")+ '" />').html( errorsArr.join('<br />') ).appendTo('body').dialog({
					modal: true
				,	width: '500px'
				});
			}
        } else if(res.messages.length) {
            if(msgEl) {
                jQuery(msgEl)
					.removeClass('nbsErrorMsg')
					.addClass('nbsSuccessMsg');
                for(var i = 0; i < res.messages.length; i++) {
                    jQuery(msgEl).append(res.messages[i]).append('<br />');
                }
            }
        }
    }
    if(params.onSuccess && typeof(params.onSuccess) == 'function') {
        params.onSuccess(res);
    }
}

function getDialogElementNbs() {
	return jQuery('<div/>').appendTo(jQuery('body'));
}

function toeOptionNbs(key) {
	if(NBS_DATA.options && NBS_DATA.options[ key ])
		return NBS_DATA.options[ key ];
	return false;
}
function toeLangNbs(key) {
	if(NBS_DATA.siteLang && NBS_DATA.siteLang[key])
		return NBS_DATA.siteLang[key];
	return key;
}
function toePagesNbs(key) {
	if(typeof(NBS_DATA) != 'undefined' && NBS_DATA[key])
		return NBS_DATA[key];
	return false;;
}
/**
 * This function will help us not to hide desc right now, but wait - maybe user will want to select some text or click on some link in it.
 */
function toeOptTimeoutHideDescriptionNbs() {
	jQuery('#nbsOptDescription').removeAttr('toeFixTip');
	setTimeout(function(){
		if(!jQuery('#nbsOptDescription').attr('toeFixTip'))
			toeOptHideDescriptionNbs();
	}, 500);
}
/**
 * Show description for options
 */
function toeOptShowDescriptionNbs(description, x, y, moveToLeft) {
    if(typeof(description) != 'undefined' && description != '') {
        if(!jQuery('#nbsOptDescription').length) {
            jQuery('body').append('<div id="nbsOptDescription"></div>');
        }
		if(moveToLeft)
			jQuery('#nbsOptDescription').css('right', jQuery(window).width() - (x - 10));	// Show it on left side of target
		else
			jQuery('#nbsOptDescription').css('left', x + 10);
        jQuery('#nbsOptDescription').css('top', y);
        jQuery('#nbsOptDescription').show(200);
        jQuery('#nbsOptDescription').html(description);
    }
}
/**
 * Hide description for options
 */
function toeOptHideDescriptionNbs() {
	jQuery('#nbsOptDescription').removeAttr('toeFixTip');
    jQuery('#nbsOptDescription').hide(200);
}
/*
 * Check if value is in array (object)
 * @param {type} needle Find for
 * @param {type} haystack Find where
 * @returns {Boolean} true if value found, else - false
 */
function toeInArrayNbs(needle, haystack) {
	if(haystack) {
		for(var i in haystack) {
			if(haystack[i] == needle)
				return true;
		}
	}
	return false;
}
function toeShowDialogCustomized(element, options) {
	options = jQuery.extend({
		resizable: false
	,	width: 500
	,	height: 300
	,	closeOnEscape: true
	,	open: function(event, ui) {
			jQuery('.ui-dialog-titlebar').css({
				'background-color': '#222222'
			,	'background-image': 'none'
			,	'border': 'none'
			,	'margin': '0'
			,	'padding': '0'
			,	'border-radius': '0'
			,	'color': '#CFCFCF'
			,	'height': '27px'
			});
			jQuery('.ui-dialog-titlebar-close').css({
				'background': 'url("'+ NBS_DATA.cssPath+ 'img/tb-close.png") no-repeat scroll 0 0 transparent'
			,	'border': '0'
			,	'width': '15px'
			,	'height': '15px'
			,	'padding': '0'
			,	'border-radius': '0'
			,	'margin': '7px 7px 0'
			}).html('');
			jQuery('.ui-dialog').css({
				'border-radius': '3px'
			,	'background-color': '#FFFFFF'
			,	'background-image': 'none'
			,	'padding': '1px'
			,	'z-index': '300000'
			,	'position': 'fixed'
			,	'top': '60px'
			});
			jQuery('.ui-dialog-buttonpane').css({
				'background-color': '#FFFFFF'
			});
			jQuery('.ui-dialog-title').css({
				'color': '#CFCFCF'
			,	'font': '12px sans-serif'
			,	'padding': '6px 10px 0'
			});
			if(options.openCallback && typeof(options.openCallback) == 'function') {
				options.openCallback(event, ui);
			}
			jQuery('.ui-widget-overlay').css({
				'z-index': jQuery( event.target ).parents('.ui-dialog:first').css('z-index') - 1
			,	'background-image': 'none'
			});
			if(options.modal && options.closeOnBg) {
				jQuery('.ui-widget-overlay').unbind('click').bind('click', function() {
					jQuery( element ).dialog('close');
				});
			}
		}
	}, options);
	return jQuery(element).dialog(options);
}
/**
 * @see html::slider();
 **/
function toeSliderMove(event, ui) {
    var id = jQuery(event.target).attr('id');
    jQuery('#toeSliderDisplay_'+ id).html( ui.value );
    jQuery('#toeSliderInput_'+ id).val( ui.value ).change();
}
function nbsCorrectJqueryUsed() {
	return (typeof(jQuery.fn.sendFormNbs) === 'function');
}
function nbsReloadCoreJs(clb, params) {
	var scriptsHtml = ''
	,	coreScripts = ['common.js', 'core.js'];
	for(var i = 0; i < coreScripts.length; i++) {
		scriptsHtml += '<script type="text/javascript" class="nbsReloadedScript" src="'+ NBS_DATA.jsPath+ coreScripts[ i ]+ '"></script>';
	}
	jQuery('head').append( scriptsHtml );
	if(clb) {
		_nbsRunClbAfterCoreReload( clb, params );
	}
}
function _nbsRunClbAfterCoreReload(clb, params) {
	if(nbsCorrectJqueryUsed()) {
		callUserFuncArray(clb, params);
		return;
	}
	setTimeout(function(){
		nbsCorrectJqueryUsed(clb, params);
	}, 500);
}
/**
 * Javascript extend functionality
 * @param {function/class} Child child elemnt for exnted
 * @param {function/class} Parent parent elemnt to extend from
 */
function extendNbs(Child, Parent) {
	var F = function() { };
	F.prototype = Parent.prototype;
	Child.prototype = new F();
	Child.prototype.constructor = Child;
	Child.superclass = Parent.prototype;
}