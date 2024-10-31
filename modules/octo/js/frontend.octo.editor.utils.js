var nbsUtils = {
	slidesEditWnd: null
,	addMenuItemWnd: null
,	addMenuItemWndBlock: null
,	subSettingsWnd: null
,	subSettingsWndBlock: null
,	subAddFieldWnd: null
,	subAddFieldWndBlock: null
,	iconsLibWnd: null
,	iconsLibWndElement: null
//,	addColElWnd: null
//,	addColElElement: null
,	badgesLibWnd: null
,	badgesLibWndElement: null
,	paddingsWnd: null
,	paddingWndBlock: null

,	_dynConentWnd: null
,	_dynContentWndBlock: null
,	_dynContentSyncKeys: null
,	_colResizer: null
,	_rowResizer: null
,	_resizeFrame: null
,	showSlidesEditWnd: function(block) {
		var self = this;
		if(!this.slidesEditWnd) {
			this.slidesEditWnd = jQuery('#nbsManageSlidesWnd').modal({
				show: false
			});
			this.slidesEditWnd.find('.nbsManageSlidesSaveBtn').click(function(){
				block.beforeSave();
				var listPrev = self.slidesEditWnd.find('.nbsSlidesListPrev')
				,	slides = block.getSlides()
				,	sliderShell = block.getSliderShell()
				,	tmpDiv = jQuery('<div style="display: none;" />').appendTo('body');
				listPrev.find('.nbsSlideManageItem').each(function(){
					var slideId = jQuery(this).data('slide-id');
					slides.each(function(){
						if(jQuery(this).data('slide-id') == slideId) {
							tmpDiv.append( jQuery(this) );
							return false;
						}
					});
				});
				sliderShell.html('').append(tmpDiv.find(':data(slide-id)'));
				tmpDiv.remove();
				block.afterSave();
				_nbsSaveCanvas();
				self.slidesEditWnd.modal('hide');
				return false;
			});
			this.slidesEditWnd.find('.nbsSlideManageAddBtn').click(function(){
				// Simulate click on Add slide menu btn
				block._clickMenuItem_add_slide({}, {clb: function(){
					self.showSlidesEditWnd(block);
				}});
				if(this.slidesEditWnd)
					this.slidesEditWnd.modal('hide');
				return false;
			});
		}
		var listPrev = this.slidesEditWnd.find('.nbsSlidesListPrev');
		listPrev.find('*:not(.nbsSlideManageAddBtn)').remove();
		var slides = block.getSlides();
		if(slides && slides.length) {
			slides.each(function(){
				var newItem = jQuery('#nbsSlideManageItemExl').clone().removeAttr('id');
				newItem.find('img:first').attr('src', jQuery(this).find('.nbsSlideImg').attr('src'));
				newItem.data('slide-id', jQuery(this).data('slide-id'));
				listPrev.prepend( newItem );
				newItem.find('.nbsSlideManageItemRemove').click(function(){
					//if(confirm(toeLangNbs('Are you sure want to remove this slide?'))) {
						jQuery(this).parents('.nbsSlideManageItem:first').hide(g_nbsAnimationSpeed, function(){
							jQuery(this).remove();
						});
					//}
					return false;
				});
			});
			listPrev.sortable({
				revert: true
			,	items: '.nbsSlideManageItem'
			,	placeholder: 'ui-state-highlight'
			//,	axis: 'x'
			});
			listPrev.find('*').disableSelection();
		} else {
			listPrev.prepend( '<div>'+ toeLangNbs('You have no slides for now - try to add them at first.')+ '</div>' );
		}
		this.slidesEditWnd.modal('show');
	}
,	_getEllIconsLibHtml: function() {
		return this.iconsLibWnd.find('.nbsIconsLibList .nbsIconLibItem');
	}
,	_showAllIconsLib: function() {
		this._getEllIconsLibHtml().show();
	}
,	initIconsLibWnd: function() {
		var self = this;
		this.iconsLibWnd = jQuery('#nbsIconsLibWnd').modal({
			show: false
		});
		this.iconsLibWnd.find('.nbsIconsLibSearchTxt').keyup(function(){
			var value = jQuery.trim( jQuery(this).val() );
			if(value && value != '') {
				var keys = jQuery(this).val().split(' ')
				,	allFoundIcons = self._getEllIconsLibHtml()
				,	initialSize = allFoundIcons.length;
				allFoundIcons.show();
				for(var i = 0; i < keys.length; i++) {
					allFoundIcons = allFoundIcons.not('[data-icon*="'+ keys[i]+ '"]');
				}
				allFoundIcons.hide();
				if(initialSize == allFoundIcons.length) {	// Anything was found
					self._showNothingFoundIconsLib( value );
				}
			} else {
				self._hideNothingFoundIconsLib();
				self._showAllIconsLib();
			}
			return false;
		});
		this.iconsLibWnd.find('.nbsIconsLibSaveBtn').click(function(){
			nbsUtils.iconsLibWnd.modal('hide');
			return false;
		});
		var allIcons = this.getFaIconsList()
		,	iconsShell = this.iconsLibWnd.find('.nbsIconsLibList');
		iconsShell.html('');
		for(var i = 0; i < allIcons.length; i++) {
			var iconName = this._faIconClassToName(allIcons[i]);
			iconsShell.append('<div class="nbsIconLibItem col-md-3 col-sm-4" onclick="nbsUtils.selectFaIconFromLib(this); return false;" data-icon="'+ allIcons[i]+ '" data-name="'+ iconName+ '">'
				+ '<i class="nbsIconLibPrev fa '+ allIcons[i]+ '"></i>'
				+ '<span class="nbsIconLibTitle">'+ iconName+ '</span>'
			+'</div>');
		}
	}
,	selectFaIconFromLib: function(clickIcon) {
		if(this.iconsLibWndElement) {
			var prevClass = this.iconsLibWndElement.get('icon')
			,	newClass = jQuery(clickIcon).data('icon');
			this.iconsLibWndElement._getEditArea().removeClass( prevClass ).addClass( newClass );
			this.iconsLibWndElement.set('icon', newClass);
			_nbsSaveCanvas();
		} else
			console.error('Can not find element for icon apply!!!');
		this.iconsLibWnd.modal('hide');
	}
,	_faIconClassToName: function(str) {
		return str.substr(3);
	}
,	_showNothingFoundIconsLib: function(keys) {
		var msgEl = this.iconsLibWnd.find('.nbsIconsLibEmptySearch');
		if(keys) {
			msgEl.find('.nbsNothingFoundKeys').html( keys );
		}
		msgEl.slideDown( g_nbsAnimationSpeed );
	}
,	_hideNothingFoundIconsLib: function() {
		this.iconsLibWnd.find('.nbsIconsLibEmptySearch').hide();
	}
,	showIconsLibWnd: function(element) {
		if(!this.iconsLibWnd) {
			this.initIconsLibWnd();
		}
		this.iconsLibWndElement = element;
		this._showAllIconsLib();
		this._hideNothingFoundIconsLib();
		this.iconsLibWnd.find('.nbsIconsLibSearchTxt').val('');
		this.iconsLibWnd.modal('show');
	}
,	converUrl: function(url) {
		if(url.indexOf('http') !== 0) {
			url = 'http://'+ url;
		}
		return url;
	}
,	urlToVideoSrc: function(url) {
		var src = '';
		if((src = url.replace(/.*www\.youtube\.com\/watch\?v\=(.+)/gi, '$1')) !== url) {
			return 'https://www.youtube.com/embed/'+ src;
		} else if((src = url.replace(/.*vimeo\.com.*(\d+)/gi, '$1')) !== url) {
			return 'https://player.vimeo.com/video/'+ src+ '?badge=0';
		}
		return url;
	}
,	getFaIconsList: function() {
		return ['fa-adjust','fa-adn','fa-align-center','fa-align-justify','fa-align-left','fa-align-right','fa-ambulance','fa-anchor','fa-android','fa-angellist','fa-angle-double-down','fa-angle-double-left','fa-angle-double-right','fa-angle-double-up','fa-angle-down','fa-angle-left','fa-angle-right','fa-angle-up','fa-apple','fa-archive','fa-area-chart','fa-arrow-circle-down','fa-arrow-circle-left','fa-arrow-circle-o-down','fa-arrow-circle-o-left','fa-arrow-circle-o-right','fa-arrow-circle-o-up','fa-arrow-circle-right','fa-arrow-circle-up','fa-arrow-down','fa-arrow-left','fa-arrow-right','fa-arrow-up','fa-arrows','fa-arrows-alt','fa-arrows-h','fa-arrows-v','fa-asterisk','fa-at','fa-automobile(alias)','fa-backward','fa-ban','fa-bank(alias)','fa-bar-chart','fa-bar-chart-o(alias)','fa-barcode','fa-bars','fa-bed','fa-beer','fa-behance','fa-behance-square','fa-bell','fa-bell-o','fa-bell-slash','fa-bell-slash-o','fa-bicycle','fa-binoculars','fa-birthday-cake','fa-bitbucket','fa-bitbucket-square','fa-bitcoin(alias)','fa-bold','fa-bolt','fa-bomb','fa-book','fa-bookmark','fa-bookmark-o','fa-briefcase','fa-btc','fa-bug','fa-building','fa-building-o','fa-bullhorn','fa-bullseye','fa-bus','fa-buysellads','fa-cab(alias)','fa-calculator','fa-calendar','fa-calendar-o','fa-camera','fa-camera-retro','fa-car','fa-caret-down','fa-caret-left','fa-caret-right','fa-caret-square-o-down','fa-caret-square-o-left','fa-caret-square-o-right','fa-caret-square-o-up','fa-caret-up','fa-cart-arrow-down','fa-cart-plus','fa-cc','fa-cc-amex','fa-cc-discover','fa-cc-mastercard','fa-cc-paypal','fa-cc-stripe','fa-cc-visa','fa-certificate','fa-chain(alias)','fa-chain-broken','fa-check','fa-check-circle','fa-check-circle-o','fa-check-square','fa-check-square-o','fa-chevron-circle-down','fa-chevron-circle-left','fa-chevron-circle-right','fa-chevron-circle-up','fa-chevron-down','fa-chevron-left','fa-chevron-right','fa-chevron-up','fa-child','fa-circle','fa-circle-o','fa-circle-o-notch','fa-circle-thin','fa-clipboard','fa-clock-o','fa-close(alias)','fa-cloud','fa-cloud-download','fa-cloud-upload','fa-cny(alias)','fa-code','fa-code-fork','fa-codepen','fa-coffee','fa-cog','fa-cogs','fa-columns','fa-comment','fa-comment-o','fa-comments','fa-comments-o','fa-compass','fa-compress','fa-connectdevelop','fa-copy(alias)','fa-copyright','fa-credit-card','fa-crop','fa-crosshairs','fa-css3','fa-cube','fa-cubes','fa-cut(alias)','fa-cutlery','fa-dashboard(alias)','fa-dashcube','fa-database','fa-dedent(alias)','fa-delicious','fa-desktop','fa-deviantart','fa-diamond','fa-digg','fa-dollar(alias)','fa-dot-circle-o','fa-download','fa-dribbble','fa-dropbox','fa-drupal','fa-edit(alias)','fa-eject','fa-ellipsis-h','fa-ellipsis-v','fa-empire','fa-envelope','fa-envelope-o','fa-envelope-square','fa-eraser','fa-eur','fa-euro(alias)','fa-exchange','fa-exclamation','fa-exclamation-circle','fa-exclamation-triangle','fa-expand','fa-external-link','fa-external-link-square','fa-eye','fa-eye-slash','fa-eyedropper','fa-facebook','fa-facebook-f(alias)','fa-facebook-official','fa-facebook-square','fa-fast-backward','fa-fast-forward','fa-fax','fa-female','fa-fighter-jet','fa-file','fa-file-archive-o','fa-file-audio-o','fa-file-code-o','fa-file-excel-o','fa-file-image-o','fa-file-movie-o(alias)','fa-file-o','fa-file-pdf-o','fa-file-photo-o(alias)','fa-file-picture-o(alias)','fa-file-powerpoint-o','fa-file-sound-o(alias)','fa-file-text','fa-file-text-o','fa-file-video-o','fa-file-word-o','fa-file-zip-o(alias)','fa-files-o','fa-film','fa-filter','fa-fire','fa-fire-extinguisher','fa-flag','fa-flag-checkered','fa-flag-o','fa-flash(alias)','fa-flask','fa-flickr','fa-floppy-o','fa-folder','fa-folder-o','fa-folder-open','fa-folder-open-o','fa-font','fa-forumbee','fa-forward','fa-foursquare','fa-frown-o','fa-futbol-o','fa-gamepad','fa-gavel','fa-gbp','fa-ge(alias)','fa-gear(alias)','fa-gears(alias)','fa-genderless(alias)','fa-gift','fa-git','fa-git-square','fa-github','fa-github-alt','fa-github-square','fa-gittip(alias)','fa-glass','fa-globe','fa-google','fa-google-plus','fa-google-plus-square','fa-google-wallet','fa-graduation-cap','fa-gratipay','fa-group(alias)','fa-h-square','fa-hacker-news','fa-hand-o-down','fa-hand-o-left','fa-hand-o-right','fa-hand-o-up','fa-hdd-o','fa-header','fa-headphones','fa-heart','fa-heart-o','fa-heartbeat','fa-history','fa-home','fa-hospital-o','fa-hotel(alias)','fa-html5','fa-ils','fa-image(alias)','fa-inbox','fa-indent','fa-info','fa-info-circle','fa-inr','fa-instagram','fa-institution(alias)','fa-ioxhost','fa-italic','fa-joomla','fa-jpy','fa-jsfiddle','fa-key','fa-keyboard-o','fa-krw','fa-language','fa-laptop','fa-lastfm','fa-lastfm-square','fa-leaf','fa-leanpub','fa-legal(alias)','fa-lemon-o','fa-level-down','fa-level-up','fa-life-bouy(alias)','fa-life-buoy(alias)','fa-life-ring','fa-life-saver(alias)','fa-lightbulb-o','fa-line-chart','fa-link','fa-linkedin','fa-linkedin-square','fa-linux','fa-list','fa-list-alt','fa-list-ol','fa-list-ul','fa-location-arrow','fa-lock','fa-long-arrow-down','fa-long-arrow-left','fa-long-arrow-right','fa-long-arrow-up','fa-magic','fa-magnet','fa-mail-forward(alias)','fa-mail-reply(alias)','fa-mail-reply-all(alias)','fa-male','fa-map-marker','fa-mars','fa-mars-double','fa-mars-stroke','fa-mars-stroke-h','fa-mars-stroke-v','fa-maxcdn','fa-meanpath','fa-medium','fa-medkit','fa-meh-o','fa-mercury','fa-microphone','fa-microphone-slash','fa-minus','fa-minus-circle','fa-minus-square','fa-minus-square-o','fa-mobile','fa-mobile-phone(alias)','fa-money','fa-moon-o','fa-mortar-board(alias)','fa-motorcycle','fa-music','fa-navicon(alias)','fa-neuter','fa-newspaper-o','fa-openid','fa-outdent','fa-pagelines','fa-paint-brush','fa-paper-plane','fa-paper-plane-o','fa-paperclip','fa-paragraph','fa-paste(alias)','fa-pause','fa-paw','fa-paypal','fa-pencil','fa-pencil-square','fa-pencil-square-o','fa-phone','fa-phone-square','fa-photo(alias)','fa-picture-o','fa-pie-chart','fa-pied-piper','fa-pied-piper-alt','fa-pinterest','fa-pinterest-p','fa-pinterest-square','fa-plane','fa-play','fa-play-circle','fa-play-circle-o','fa-plug','fa-plus','fa-plus-circle','fa-plus-square','fa-plus-square-o','fa-power-off','fa-print','fa-puzzle-piece','fa-qq','fa-qrcode','fa-question','fa-question-circle','fa-quote-left','fa-quote-right','fa-ra(alias)','fa-random','fa-rebel','fa-recycle','fa-reddit','fa-reddit-square','fa-refresh','fa-remove(alias)','fa-renren','fa-reorder(alias)','fa-repeat','fa-reply','fa-reply-all','fa-retweet','fa-rmb(alias)','fa-road','fa-rocket','fa-rotate-left(alias)','fa-rotate-right(alias)','fa-rouble(alias)','fa-rss','fa-rss-square','fa-rub','fa-ruble(alias)','fa-rupee(alias)','fa-save(alias)','fa-scissors','fa-search','fa-search-minus','fa-search-plus','fa-sellsy','fa-send(alias)','fa-send-o(alias)','fa-server','fa-share','fa-share-alt','fa-share-alt-square','fa-share-square','fa-share-square-o','fa-shekel(alias)','fa-sheqel(alias)','fa-shield','fa-ship','fa-shirtsinbulk','fa-shopping-cart','fa-sign-in','fa-sign-out','fa-signal','fa-simplybuilt','fa-sitemap','fa-skyatlas','fa-skype','fa-slack','fa-sliders','fa-slideshare','fa-smile-o','fa-soccer-ball-o(alias)','fa-sort','fa-sort-alpha-asc','fa-sort-alpha-desc','fa-sort-amount-asc','fa-sort-amount-desc','fa-sort-asc','fa-sort-desc','fa-sort-down(alias)','fa-sort-numeric-asc','fa-sort-numeric-desc','fa-sort-up(alias)','fa-soundcloud','fa-space-shuttle','fa-spinner','fa-spoon','fa-spotify','fa-square','fa-square-o','fa-stack-exchange','fa-stack-overflow','fa-star','fa-star-half','fa-star-half-empty(alias)','fa-star-half-full(alias)','fa-star-half-o','fa-star-o','fa-steam','fa-steam-square','fa-step-backward','fa-step-forward','fa-stethoscope','fa-stop','fa-street-view','fa-strikethrough','fa-stumbleupon','fa-stumbleupon-circle','fa-subscript','fa-subway','fa-suitcase','fa-sun-o','fa-superscript','fa-support(alias)','fa-table','fa-tablet','fa-tachometer','fa-tag','fa-tags','fa-tasks','fa-taxi','fa-tencent-weibo','fa-terminal','fa-text-height','fa-text-width','fa-th','fa-th-large','fa-th-list','fa-thumb-tack','fa-thumbs-down','fa-thumbs-o-down','fa-thumbs-o-up','fa-thumbs-up','fa-ticket','fa-times','fa-times-circle','fa-times-circle-o','fa-tint','fa-toggle-down(alias)','fa-toggle-left(alias)','fa-toggle-off','fa-toggle-on','fa-toggle-right(alias)','fa-toggle-up(alias)','fa-train','fa-transgender','fa-transgender-alt','fa-trash','fa-trash-o','fa-tree','fa-trello','fa-trophy','fa-truck','fa-try','fa-tty','fa-tumblr','fa-tumblr-square','fa-turkish-lira(alias)','fa-twitch','fa-twitter','fa-twitter-square','fa-umbrella','fa-underline','fa-undo','fa-university','fa-unlink(alias)','fa-unlock','fa-unlock-alt','fa-unsorted(alias)','fa-upload','fa-usd','fa-user','fa-user-md','fa-user-plus','fa-user-secret','fa-user-times','fa-users','fa-venus','fa-venus-double','fa-venus-mars','fa-viacoin','fa-video-camera','fa-vimeo-square','fa-vine','fa-vk','fa-volume-down','fa-volume-off','fa-volume-up','fa-warning(alias)','fa-wechat(alias)','fa-weibo','fa-weixin','fa-whatsapp','fa-wheelchair','fa-wifi','fa-windows','fa-won(alias)','fa-wordpress','fa-wrench','fa-xing','fa-xing-square','fa-yahoo','fa-yelp','fa-yen(alias)','fa-youtube','fa-youtube-play','fa-youtube-square'];
	}
,	extractBootstrapColsClasses: function(element) {
		var	currClasses = jQuery.map(jQuery(element).attr('class').split(' '), jQuery.trim)
		,	newClasses = [];
		for(var i = 0; i < currClasses.length; i++) {
			if(currClasses[ i ] == 'col' || currClasses[ i ].match(/col\-\w{2}\-\d{1,2}/)) {
				newClasses.push( currClasses[ i ] );
			}
		}
		return newClasses;
	}
,	initBadgesLibWnd: function() {
		var self = this;
		this.badgesLibWnd = jQuery('#nbsBadgesLibWnd').modal({
			show: false
		});
		this.badgesLibWnd.find('.nbsBadgesLibSaveBtn').click(function(){
			self.badgesLibWndElement._setBadge( self.getBadgesData() );
			nbsUtils.badgesLibWnd.modal('hide');
			return false;
		});
		this.badgesLibWnd.find('input[name=badge_name]').change(function(){
			self.updateBadgePrevLib();
		});
		var colorInputs = [
			{key: 'badge_bg_color', def: 'rgb(255, 99, 99)'}
		,	{key: 'badge_txt_color', def: '#333'}
		];
		for(var i = 0; i < colorInputs.length; i++) {
			var colorInp = this.badgesLibWnd.find('.nbsColorpickerInput[name='+ colorInputs[ i ].key+ ']');
			colorInp.ColorPickerSliders({
				placement: 'bottom'
			,	color: colorInputs[ i ].def
			,	order: {
					hsl: 1
				,	opacity: 2
				}
			,	customswatches: 'different-swatches-groupname'
			,	swatches: ['rgb(255, 0, 0)', 'rgb(0, 255, 0)', 'blue']
			,	labels: {
					hslhue: 'color tone'
				,	hslsaturation: 'saturation'
				,	hsllightness: 'brightness'
				,	opacity: 'alfa'
				}
			,	onchangefinish: function(container, color) {
					self.updateBadgePrevLib();
				}
			});
		}
		this.badgesLibWnd.find('.nbsTableBadgePosition').click(function(){
			self.badgesLibWnd.find('.nbsTableBadgePosition').removeClass('active');
			jQuery(this).addClass('active');
			self.badgesLibWnd.find('input[name=badge_pos]').val( jQuery(this).data('pos') );
			self.updateBadgePrevLib();
		});
	}
,	showBadgesLibWnd: function( element ) {
		if(!this.badgesLibWnd) {
			this.initBadgesLibWnd();
		}
		this.badgesLibWndElement = element;
		this.fillInBadgeLibData( this.badgesLibWndElement._getBadgeData() );
		this.badgesLibWnd.modal('show');
		var self = this;
		setTimeout(function(){
			self.updateBadgePrevLib();
		}, 500);	// 500 is for transition for popup show
	}
,	fillInBadgeLibData: function(data) {
		if(data.badge_name) {
			this.badgesLibWnd.find('input[name=badge_name]').val( data.badge_name );
		}
		if(data.badge_bg_color) {
			this.badgesLibWnd.find('.nbsColorpickerInput[name=badge_bg_color]').val( data.badge_bg_color );
		}
		if(data.badge_txt_color) {
			this.badgesLibWnd.find('.nbsColorpickerInput[name=badge_txt_color]').val( data.badge_txt_color );
		}
		if(data.badge_pos) {
			this.badgesLibWnd.find('.nbsTableBadgePosition[data-pos="'+ data.badge_pos+ '"]').click();
		}
	}
,	updateBadgePrevLib: function($badge, data) {
		$badge = $badge ? $badge : jQuery('#nbsTableBadgePrev');
		data = data ? data : this.getBadgesData();
		var $prevContent = $badge.find('.nbsColBadgeContent');
		$badge
			.attr({
				'class': 'nbsColBadge nbsColBadge-'+ data.badge_pos
			,	'style': ''
			});
		$prevContent
			.html( data.badge_name )
			.css({
				'background-color': data.badge_bg_color
			,	'color': data.badge_txt_color
			,	'width': 'auto'
			,	'display': 'inline-block'
			});
		var contW = $prevContent.outerWidth()
		,	contH = $prevContent.outerHeight()
		,	w = $badge.outerWidth()
		,	h = $badge.outerHeight()
		,	contAfterStyles = { 'display': 'block' }
		,	afterStyles = {}
		,	newContentWidth = $prevContent.width();
		switch(data.badge_pos) {
			case 'right': case 'left':
				afterStyles[ data.badge_pos ] = Math.ceil((contH - contW) / 2);
				afterStyles.top = Math.floor((contW - contH) / 2);
				break;
			case 'left-top': case 'right-top':
				var posKey = data.badge_pos == 'left-top' ? 'left' : 'right';
				newContentWidth += 500;
				//afterStyles[ posKey ] = -1 * (newContentWidth - contW) / 2;//-1 * Math.floor((newContentWidth / 2) * Math.sin(Math.PI / 4));
				// TODO: Correct calculations here
				afterStyles[ posKey ] = -1 * (newContentWidth / 2) + contH;
				afterStyles.top = contW / 2;
				break;
		}
		$prevContent.width( newContentWidth ).css( contAfterStyles );
		$badge.css( afterStyles );
	}
,	getBadgesData: function() {
		var data = this.badgesLibWnd.find('#nbsBadgesLibForm').serializeAssoc();
		return data;
	}
,	 _getMailchimpKey: function() {
		return jQuery.trim( nbsUtils.subSettingsWnd.find('[name="sub_mailchimp_api_key"]').val() );
	}
,	subUpdateMailchimpLists: function() {
		if(nbsUtils.subSettingsWnd.find('[name="sub_dest"]').val() == 'mailchimp') {
			var key = this._getMailchimpKey();
			if(key && key != '') {
				jQuery('#nbsMailchimpListsShell').hide();
				jQuery('#nbsMailchimpNoApiKey').hide();
				jQuery.sendFormNbs({
					msgElID: 'nbsMailchimpMsg'
				,	data: {mod: 'subscribe', action: 'getMailchimpLists', key: key}
				,	onSuccess: function(res) {
						if(!res.error) {
							jQuery('#nbsMailchimpLists').html('');
							var selectedListsIds = nbsUtils.subSettingsWndBlock.getParam('sub_mailchimp_lists');
							if(!selectedListsIds)
								selectedListsIds = [];
							for(var listId in res.data.lists) {
								var selected = toeInArrayNbs(listId, selectedListsIds) ? 'selected="selected"' : '';
								jQuery('#nbsMailchimpLists').append('<option '+ selected+ ' value="'+ listId+ '">'+ res.data.lists[ listId ]+ '</option>');
							}
							jQuery('#nbsMailchimpListsShell').show();
						}
					}
				});
			} else {
				jQuery('#nbsMailchimpNoApiKey').show();
				jQuery('#nbsMailchimpListsShell').hide();
			}
		}
	}
,	showDynContentSetsWnd: function( block ) {
		if(!this._dynConentWnd) {
			this._initDynContentWnd();
		}
		this._dynContentWndBlock = block;
		this._fillInDynContentData();
		this._dynConentWnd.modal('show');
	}
,	_initDynContentWnd: function() {
		var self = this;
		this._dynContentSyncKeys = ['posts_cnt', 'posts_type', 'enb_title_link', 'enb_img_link', 'more_btn_txt',
		'enb_title', 'enb_img', 'enb_excerpt', 'enb_more_btn', 'img_width', 'img_width_units'];
		this._dynConentWnd = jQuery('#nbsDynContentSetsWnd').modal({
			show: false
		});
		nbsInitCustomCheckRadio(this._dynConentWnd);
		this._dynConentWnd.find('.nbsDynContentLibSaveBtn').click(function(){
			for(var i = 0; i < self._dynContentSyncKeys.length; i++) {
				var value = false
				,	$input = self._dynConentWnd.find('[name="'+ self._dynContentSyncKeys[ i ]+ '"]');
				switch($input.attr('type')) {
					case 'checkbox':
						value = $input.prop('checked') ? 1 : 0;
						break;
					default:
						value = $input.val();
						break;
				}				
				self._dynContentWndBlock.setParam(
					self._dynContentSyncKeys[ i ], 
					value);
			}
			self._dynContentWndBlock.updateDynContent();
			self._dynConentWnd.modal('hide');
			return false;
		});
	}
,	_fillInDynContentData: function() {
		for(var i = 0; i < this._dynContentSyncKeys.length; i++) {
			var value = this._dynContentWndBlock.getParam(this._dynContentSyncKeys[ i ])
			,	$input = this._dynConentWnd.find('[name="'+ this._dynContentSyncKeys[ i ]+ '"]');
			switch($input.attr('type')) {
				case 'checkbox':
					parseInt(value) ? $input.attr('checked', 'checked') : $input.removeAttr('checked');
					break;
				default:
					$input.val( this._dynContentWndBlock.getParam(this._dynContentSyncKeys[ i ]) );
					break;
			}
		}
		this._enableDynContentControls();
		nbsCheckUpdateArea(this._dynConentWnd);
	}
,	_enableDynContentControls: function() {
		var showUnitsSelectBox = this._dynContentWndBlock.isParamDefined('img_width_units');
		this._dynConentWnd.find('[name="img_width_units"]').prop('hidden', !showUnitsSelectBox);
		this._dynConentWnd.find('.nbsImgWidthUnitPx').prop('hidden', showUnitsSelectBox);
	}
,	setResizable: function( $elements, params ) {
		params = params || {};
		var events = ['create', 'resize', 'start', 'stop'];
		for(var i in events) {
			if(typeof(params[ events[i] ]) !== 'undefined') {
				params[ events[i]+ 'Cust' ] = params[ events[i] ];
				if(toeInArrayNbs(events[i], ['stop'])) {	// It will be called directly in our custom callback bellow
					delete params[ events[i] ];
				} else {
					params[ events[i] ] = function( event, ui ) {
						params[ nbs_str_replace(event.type, 'resize', '')+ 'Cust' ].apply(this, arguments);
					};
				}
			}
		}
		params = jQuery.extend({
			aspectRatio: true
		,	grid: [ 10, 10 ]
		,	stop: function( event, ui ) {
				if(ui.originalElement) {
					var width = Math.floor(ui.size.width)
					,	height = Math.floor(ui.size.height);
					ui.originalElement
						.attr('width', width)
						.attr('height', height)
						.attr('data-size', width+ 'x'+ height)
						.attr('data-resized', 1);
				}
				if(typeof(params.stopCust) === 'function') {
					// Add normalized width and height to arguments callbacks
					var mainArguments = Array.prototype.slice.call(arguments);
					mainArguments.push( width );
					mainArguments.push( height );
					params.stopCust.apply(this, mainArguments);
				}
			}
		}, (params || {}));
		var sizeSet = $elements.attr('data-size');
		$elements.resizable( params );
		if(!sizeSet) {	// If size was not set - remove resizable size setting
			$elements.css({
				'width': ''
			,	'height': ''
			}).parents('.ui-wrapper:first').css({
				'width': ''
			,	'height': ''
			});
		}
	}
,	destroyResizable: function( $elements ) {
		$elements.resizable('destroy');
	}
,	resetResizableData: function( $elements ) {
		this.destroyResizable( $elements );
		$elements.removeAttr('width').removeAttr('height').removeAttr('data-size').removeAttr('data-resized').css({
			'width': '100%'
		,	'height': 'auto'
		});
	}
/*,	initAddColElWnd: function() {
		this.addColElWnd = jQuery('#nbsAddColElLibWnd').modal({
			show: false
		});
		this.addColElWnd.find('.nbsAddColElLibSaveBtn').click(function(){
			nbsUtils.addColElWnd.modal('hide');
			return false;
		});
		this.addColElWnd.find('.nbsAddColElBtn').click(function(){
			var elCode = jQuery(this).attr('href');
			var $el = nbsUtils.addColElWnd.find('.nbsAddColElEx[data-el="'+ elCode+ '"]').clone().removeClass('nbsAddColElEx');
			nbsUtils.addColElWndElement.$().append( $el );
			nbsUtils.addColElWndElement.getBlock()._initElementsForArea( $el );
			nbsUtils.addColElWnd.modal('hide');
			return false;
		});
	}
,	showAddColElWnd: function( element ) {
		if(!this.addColElWnd) {
			this.initAddColElWnd();
		}
		this.addColElWndElement = element;
		this.addColElWnd.modal('show');
	}*/
,	isResizeInProgress: function() {
		var colResizer = this.getColResizer();
		if(colResizer.isCaptured()) {
			return true;
		}
		var rowResizer = this.getRowResizer();
		if(rowResizer.isCaptured()) {
			return true;
		}
		return false;
	}
,	getColResizer: function() {
		this._initColResizer();
		return this._colResizer;
	}
,	_initColResizer: function() {
		if(!this._colResizer) {
			this._colResizer = new nbsColResizer();
		}
		this._initResizeFrame();
	}
,	getRowResizer: function() {
		this._initRowResizer();
		return this._rowResizer;
	}
,	_initRowResizer: function() {
		if(!this._rowResizer) {
			this._rowResizer = new nbsRowResizer();
		}
		this._initResizeFrame();
	}
,	_initResizeFrame: function() {
		if(!this._resizeFrame) {
			this._resizeFrame = new nbsResizeFrame();
		}
	}
,	showPaddingsWnd: function( block ) {
		if(!this.paddingsWnd) {
			this.initPaddingsWnd();
		}
		this.paddingWndBlock = block;
		this._fillInPaddingWndData();
		this.paddingsWnd.modal('show');
	}
,	_fillInPaddingWndData: function() {
		var paddings = ['top', 'left', 'right', 'bottom'];
		for(var i = 0; i < paddings.length; i++) {
			var padKey = paddings[ i ]
			,	$enbCheck = this.paddingsWnd.find('[name="enb_padding_'+ padKey+ '"]')
			,	$paddTxt = this.paddingsWnd.find('[name="padding_'+ padKey+ '"]');
			if(parseInt(this.paddingWndBlock.getParam('enb_padding_'+ padKey))) {
				$enbCheck.prop('checked', 'checked');
			} else {
				$enbCheck.removeAttr('checked');
			}
			$paddTxt.val( this.paddingWndBlock.getParam('padding_'+ padKey) );
		}
		nbsCheckUpdateArea( this.paddingsWnd );
	}
,	_savePaddingWndData: function() {
		var paddings = ['top', 'left', 'right', 'bottom'];
		for(var i = 0; i < paddings.length; i++) {
			var padKey = paddings[ i ]
			,	$enbCheck = this.paddingsWnd.find('[name="enb_padding_'+ padKey+ '"]')
			,	$paddTxt = this.paddingsWnd.find('[name="padding_'+ padKey+ '"]');
			if($enbCheck.data('changed')) {
				this.paddingWndBlock.switchPadding( padKey, $enbCheck.prop('checked') );
			}
			if($paddTxt.data('changed')) {
				this.paddingWndBlock.setPaddingSize( padKey, $paddTxt.val() );
			}
		}
		this.paddingsWnd.find(':input').data('changed', 0);
	}
,	initPaddingsWnd: function() {
		this.paddingsWnd = jQuery('#nbsPaddingsWnd').modal({
			show: false
		});
		this.paddingsWnd.find(':input').change(function(){
			jQuery(this).data('changed', 1);
		});
		nbsInitCustomCheckRadio( this.paddingsWnd );
		this.paddingsWnd.find('.nbsPaddingsSaveBtn').click(function(){
			nbsUtils._savePaddingWndData();
			nbsUtils.paddingsWnd.modal('hide');
			return false;
		});
	}
};