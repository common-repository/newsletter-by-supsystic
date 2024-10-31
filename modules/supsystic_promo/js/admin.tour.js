var g_nbsCurrTour = null
,	g_nbsTourOpenedWithTab = false
,	g_nbsAdminTourDissmissed = false;
jQuery(document).ready(function(){
	setTimeout(function(){
		if(typeof(nbsAdminTourData) !== 'undefined' && nbsAdminTourData.tour) {
			jQuery('body').append( nbsAdminTourData.html );
			nbsAdminTourData._$ = jQuery('#supsystic-admin-tour');
			for(var tourId in nbsAdminTourData.tour) {
				if(nbsAdminTourData.tour[ tourId ].points) {
					for(var pointId in nbsAdminTourData.tour[ tourId ].points) {
						_nbsOpenPointer(tourId, pointId);
						break;	// Open only first one
					}
				}
			}
			for(var tourId in nbsAdminTourData.tour) {
				if(nbsAdminTourData.tour[ tourId ].points) {
					for(var pointId in nbsAdminTourData.tour[ tourId ].points) {
						if(nbsAdminTourData.tour[ tourId ].points[ pointId ].sub_tab) {
							var subTab = nbsAdminTourData.tour[ tourId ].points[ pointId ].sub_tab;
							jQuery('a[href="'+ subTab+ '"]')
								.data('tourId', tourId)
								.data('pointId', pointId);
							var tabChangeEvt = str_replace(subTab, '#', '')+ '_tabSwitch';
							jQuery(document).bind(tabChangeEvt, function(event, selector) {
								if(!g_nbsTourOpenedWithTab && !g_nbsAdminTourDissmissed) {
									var $clickTab = jQuery('a[href="'+ selector+ '"]');
									_nbsOpenPointer($clickTab.data('tourId'), $clickTab.data('pointId'));
								}
							});
						}
					}
				}
			}
		}
	}, 500);
});

function _nbsOpenPointerAndFormTab(tourId, pointId, tab) {
	g_nbsTourOpenedWithTab = true;
	jQuery('#nbsFormEditTabs').wpTabs('activate', tab);
	_nbsOpenPointer(tourId, pointId);
	g_nbsTourOpenedWithTab = false;
}
function _nbsOpenPointer(tourId, pointId) {
	var pointer = nbsAdminTourData.tour[ tourId ].points[ pointId ];
	var $content = nbsAdminTourData._$.find('#supsystic-'+ tourId+ '-'+ pointId);
	if(!jQuery(pointer.target) || !jQuery(pointer.target).length)
		return;
	if(g_nbsCurrTour) {
		_nbsTourSendNext(g_nbsCurrTour._tourId, g_nbsCurrTour._pointId);
		g_nbsCurrTour.element.pointer('close');
		g_nbsCurrTour = null;
	}
	if(pointer.sub_tab && jQuery('#nbsFormEditTabs').wpTabs('getActiveTab') != pointer.sub_tab) {
		return;
	}
	var options = jQuery.extend( pointer.options, {
		content: $content.find('.supsystic-tour-content').html()
	,	pointerClass: 'wp-pointer supsystic-pointer'
	,	close: function() {
			//console.log('closed');
		}
	,	buttons: function(event, t) {
			g_nbsCurrTour = t;
			g_nbsCurrTour._tourId = tourId;
			g_nbsCurrTour._pointId = pointId;
			var $btnsShell = $content.find('.supsystic-tour-btns')
			,	$closeBtn = $btnsShell.find('.close')
			,	$finishBtn = $btnsShell.find('.supsystic-tour-finish-btn');

			if($finishBtn && $finishBtn.length) {
				$finishBtn.click(function(e){
					e.preventDefault();
					jQuery.sendFormNbs({
						msgElID: 'noMessages'
					,	data: {mod: 'supsystic_promo', action: 'addTourFinish', tourId: tourId, pointId: pointId}
					});
					g_nbsCurrTour.element.pointer('close');
				});
			}
			if($closeBtn && $closeBtn.length) {
				$closeBtn.bind( 'click.pointer', function(e) {
					e.preventDefault();
					jQuery.sendFormNbs({
						msgElID: 'noMessages'
					,	data: {mod: 'supsystic_promo', action: 'closeTour', tourId: tourId, pointId: pointId}
					});
					t.element.pointer('close');
					g_nbsAdminTourDissmissed = true;
				});
			}
			return $btnsShell;
		}
	});
	jQuery(pointer.target).pointer( options ).pointer('open');
	var minTop = 10
	,	pointerTop = parseInt(g_nbsCurrTour.pointer.css('top'));
	if(!isNaN(pointerTop) && pointerTop < minTop) {
		g_nbsCurrTour.pointer.css('top', minTop+ 'px');
	}
}
function _nbsTourSendNext(tourId, pointId) {
	jQuery.sendFormNbs({
		msgElID: 'noMessages'
	,	data: {mod: 'supsystic_promo', action: 'addTourStep', tourId: tourId, pointId: pointId}
	});
}