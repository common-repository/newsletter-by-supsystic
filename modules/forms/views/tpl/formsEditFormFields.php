<table class="form-table nbsFormFieldsSettingsOptsTbl" style="width: 100%">
	<tr>
		<th scope="row">
			<?php _e('Field invalid error message', NBS_LANG_CODE)?>
			<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('By default our plugin will show standard browser error messages about invalid or empty fields values. But if you need - you can replace it here. Use [label] - to set field name in your error message. For example "Please fill out [label] field". You can just leave this field empty - to use standard browser messages.', NBS_LANG_CODE))?>"></i>
		</th>
		<td>
			<?php echo htmlNbs::text('params[tpl][field_error_invalid]', array(
				'value' => isset($this->form['params']['tpl']['field_error_invalid']) ? $this->form['params']['tpl']['field_error_invalid'] : ''
			))?>
		</td>
	</tr>
</table>
<div style="clear: both;"></div>
<a href="#" class="nbsAddFieldBtn button">
	<i class="fa fa-plus"></i>
	<?php _e('Add New Field', NBS_LANG_CODE)?>
</a>
<div style="clear: both;"></div>
<hr />
<div style="clear: both;"></div>
<div id="nbsFieldsEditShell"></div>
<a href="#" class="nbsMoveVFieldHandle" id="nbsMoveVFieldHandleExl" title="<?php _e('Move Up / Down', NBS_LANG_CODE)?>">
	<i class="fa fa-arrows-v" style="font-size: 24px;"></i>
</a>
<div id="nbsFieldShellEx" class="nbsFieldShell">
	<div class="nbsFieldShellBody button">
		<div class="nbsFieldPanel">
			<a href="#" class="nbsMoveHFieldHandle" title="<?php _e('Move Left / Right', NBS_LANG_CODE)?>">
				<i class="fa fa-arrows-h" style="font-size: 24px;"></i>
			</a>
			<a href="#" class="nbsAddTopBtn" title="<?php _e('Add New Field at the Top', NBS_LANG_CODE)?>">
				<i class="fa fa-arrow-up" style="position: absolute; bottom: 13px; right: 1px;"></i>
				<i class="fa fa-plus" style="font-size: 10px;"></i>
			</a>
			<a href="#" class="nbsAddRightBtn" title="<?php _e('Add New Field at the Right', NBS_LANG_CODE)?>">
				<i class="fa fa-plus" style="font-size: 10px; position: absolute; top: -7px; left: 3px;"></i>
				<i class="fa fa-arrow-right"></i>
			</a>
			<a href="#" class="nbsAddBottomBtn" title="<?php _e('Add New Field at the Bottom', NBS_LANG_CODE)?>">
				<i class="fa fa-plus" style="font-size: 10px; position: absolute; top: -7px; left: 3px;"></i>
				<i class="fa fa-arrow-down"></i>
			</a>
			<a href="#" class="nbsAddLeftBtn" title="<?php _e('Add New Field at the Left', NBS_LANG_CODE)?>">
				<i class="fa fa-arrow-left" style="position: absolute; bottom: 13px; right: 1px;"></i>
				<i class="fa fa-plus" style="font-size: 10px;"></i>
			</a>
			<a href="#" class="nbsFieldRemoveBtn" title="<?php _e('Remove', NBS_LANG_CODE)?>">
				<i class="fa fa-trash fa-2x"></i>
			</a>
		</div>
		<div class="csfFieldIcon"></div>
		<div class="csfFieldLabel"></div>
		<div class="csfFieldType"></div>
	</div>
	<?php echo htmlNbs::hidden('params[fields][][label]')?>
	<?php echo htmlNbs::hidden('params[fields][][placeholder]')?>
	<?php echo htmlNbs::hidden('params[fields][][html]')?>
	<?php echo htmlNbs::hidden('params[fields][][value]')?>
	<?php echo htmlNbs::hidden('params[fields][][mandatory]')?>
	<?php echo htmlNbs::hidden('params[fields][][name]')?>
	<?php echo htmlNbs::hidden('params[fields][][bs_class_id]')?>
	<?php echo htmlNbs::hidden('params[fields][][display]')?>
	
	<?php echo htmlNbs::hidden('params[fields][][min_size]')?>
	<?php echo htmlNbs::hidden('params[fields][][max_size]')?>
	<?php echo htmlNbs::hidden('params[fields][][add_classes]')?>
	<?php echo htmlNbs::hidden('params[fields][][add_styles]')?>
	<?php echo htmlNbs::hidden('params[fields][][add_attr]')?>
	
	<?php echo htmlNbs::hidden('params[fields][][vn_only_number]')?>
	<?php echo htmlNbs::hidden('params[fields][][vn_only_letters]')?>
	<?php echo htmlNbs::hidden('params[fields][][vn_pattern]')?>
</div>
<div id="nbsFieldsAddWnd" title="<?php _e('Click on required elements from list bellow', NBS_LANG_CODE)?>">
	<div class="nbsFieldsAddWndElementsShell">
		<?php foreach($this->fieldTypes as $ftCode => $ft) { ?>
		<?php $pro = (isset($ft['pro']) && !$this->isPro && !empty($ft['pro'])) ? $ft['pro'] : false;?>
		<div class="nbsFieldWndElement button" 
			 data-html="<?php echo $ftCode;?>"
			 <?php if($pro) { ?>
				 data-pro="1"
			 <?php }?>
		>
			<i class="fa <?php echo $ft['icon']?>"></i>
			<span class="nbsFieldWndElementLabel"><?php echo $ft['label']?></span>
			<?php if($pro) { ?>
				<span class="nbsProOptMiniLabel">
					<a href="<?php echo $pro;?>" target="_blank"><?php _e('PRO', NBS_LANG_CODE)?></a>
				</span>
			<?php }?>
		</div>
		<?php }?>
	</div>
</div>
<div id="nbsFieldsEditWnd" title="<?php _e('Edit field settings', NBS_LANG_CODE)?>">
	<h3 class="nav-tab-wrapper">
		<a class="nav-tab" href="#nbsFormFieldBaseSettings">
			<i class="fa fa-cog"></i>
			<?php _e('Basic Settings', NBS_LANG_CODE)?>
		</a>
		<a class="nav-tab" href="#nbsFormFieldAdvancedSettings">
			<i class="fa fa-cogs"></i>
			<?php _e('Advanced', NBS_LANG_CODE)?>
		</a>
		<a class="nav-tab" href="#nbsFormFieldValidation">
			<i class="fa fa-wrench"></i>
			<?php _e('Field Validation', NBS_LANG_CODE)?>
		</a>
	</h3>
	<div id="nbsFormFieldBaseSettings" class="nbsTabContent">
		<table class="form-table">
			<tr class="nbsFieldParamRow" data-not-for="checkboxsubscribe">
				<th>
					<?php _e('Name', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Name attribute for your field. You can use here latin letters, numbers or symdols "-", "_".', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('name')?>
				</td>
			</tr>
			<tr class="nbsFieldEditErrorRow" data-for="name">
				<td colspan="2" class="description">
					<?php _e('Please fill-in Name for your field, and make sure that it contains only latin letters, numbers or symdols "-", "_".', NBS_LANG_CODE)?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow">
				<th>
					<?php _e('Label', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Field label - that your users will see on your Form right near your field.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('label')?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-not-for="selectlist,selectbox,checkbox,checkboxlist,radiobutton,radiobuttons,countryList,countryListMultiple,recaptcha,checkboxsubscribe,button,submit,reset">
				<th>
					<?php _e('Placeholder', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Field placeholder - will be printed in your field as a tip.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('placeholder')?>
				</td>
			</tr>
			<tr class="nbsFieldEditErrorRow" data-for="label-placeholder">
				<td colspan="2" class="description">
					<?php _e('Please fill-in Label or Placeholder for your field - it\'s required for users to know - what field in Form that are filling-in.', NBS_LANG_CODE)?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-not-for="file,recaptcha,button,submit,reset">
				<th>
					<?php _e('Default Value', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can set default value for your field, and one it appear on your site - field will be pre-filled with this value.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('value')?><br />
					<?php echo htmlNbs::selectbox('value_preset', array(
						'options' => array(
							'' => __('or select preset', NBS_LANG_CODE),
							'user_ip' => __('User IP', NBS_LANG_CODE), 
							'user_country_code' => __('User Country code', NBS_LANG_CODE),
							'user_country_label' => __('User Country name', NBS_LANG_CODE),
						),
						'attrs' => 'class="wnd-chosen"',
					))?><i class="fa fa-question supsystic-tooltip" style="float: none; margin-left: 5px;" title="<?php echo esc_html(__('Allow to insert some pre-defined values, like current user IP addres, or his country - to send you this data.', NBS_LANG_CODE))?>"></i>
					<?php if(!$this->isPro) { ?>
						<span class="nbsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=value_preset&utm_campaign=forms';?>"><?php _e('PRO option', NBS_LANG_CODE)?></a></span>
					<?php }?>
				</td>
			</tr>
			<tr class="nbsFieldsEditForCheckRadioLists nbsFieldParamRow" data-for="radiobuttons,checkboxlist">
				<th><?php _e('Display as', NBS_LANG_CODE)?></th>
				<td>
					<?php echo htmlNbs::selectbox('display', array('options' => array(
						'row' => __('In row', NBS_LANG_CODE),
						'col' => __('In column', NBS_LANG_CODE),
					)))?>
				</td>
				<?php echo htmlNbs::hidden('params[fields][][display]')?>
			</tr>
			<tr class="nbsFieldsEditForLists nbsFieldParamRow" style="display: none;">
				<th colspan="2">
					<?php _e('Select Options', NBS_LANG_CODE)?>
					<a class="button button-small nbsFieldsAddListOpt">
						<i class="fa fa-plus"></i>
					</a>
				</th>
			</tr>
			<tr class="nbsFieldsEditForLists nbsFieldParamRow" style="display: none; height: auto;">
				<td colspan="2" style="padding: 0;">
					<div id="nbsFieldsListOptsShell">
						<div id="nbsFieldListOptShellExl" class="nbsFieldListOptShell">
							<i class="fa fa-arrows-v lcsMoveHandle"></i>
							<?php echo htmlNbs::text('options[][name]', array(
								'placeholder' => __('Name', NBS_LANG_CODE),
								'disabled' => true,
							))?>
							<?php echo htmlNbs::text('options[][label]', array(
								'placeholder' => __('Label', NBS_LANG_CODE),
								'disabled' => true,
							))?>
							<a href="#" class="button button-small nbsFieldsListOptRemoveBtn" title="<?php _e('Remove', NBS_LANG_CODE)?>">
								<i class="fa fa-trash-o"></i>
							</a>
						</div>
					</div>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-for="checkbox,radiobutton,checkboxsubscribe">
				<th><?php _e('Checked by Default', NBS_LANG_CODE)?></th>
				<td>
					<?php echo htmlNbs::checkbox('def_checked', array(
						'value' => 1,
					))?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-not-for="recaptcha,button,submit,reset">
				<th><?php _e('Required', NBS_LANG_CODE)?></th>
				<td>
					<?php echo htmlNbs::checkbox('mandatory', array(
						'value' => 1,
					))?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-for="recaptcha">
				<th>
					<?php _e('Site Key', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('Your site key, generated on <a href="%s" target="_blank">%s</a>. To get more info - check <a href="%s" target="_blank">our tutorial.</a>', NBS_LANG_CODE), 'https://www.google.com/recaptcha/admin#list', 'https://www.google.com/recaptcha/admin#list', 'http://supsystic.com/create-recaptcha-field/'))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('recap-sitekey')?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-for="recaptcha">
				<th>
					<?php _e('Secret Key', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('Your secret key, generated on <a href="%s" target="_blank">%s</a>. To get more info - check <a href="%s" target="_blank">our tutorial.</a>', NBS_LANG_CODE), 'https://www.google.com/recaptcha/admin#list', 'https://www.google.com/recaptcha/admin#list', 'http://supsystic.com/create-recaptcha-field/'))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('recap-secret')?>
				</td>
			</tr>
		</table>
	</div>
	<div id="nbsFormFieldAdvancedSettings" class="nbsTabContent">
		<table class="form-table">
			<tr class="nbsFieldParamRow" data-for="recaptcha">
				<th>
					<?php _e('reCapthca Theme', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(sprintf(__('The color theme. You can select from themes, provided by Google, for your reCaptcha. To get more info - check <a href="%s" target="_blank">our tutorial.</a>', NBS_LANG_CODE), 'http://supsystic.com/create-recaptcha-field/'))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::selectbox('recap-theme', array(
						'options' => array('light' => __('Light', NBS_LANG_CODE), 'dark' => __('Dark', NBS_LANG_CODE)),
						'value' => 'light',
					))?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-for="recaptcha">
				<th>
					<?php _e('reCapthca Type', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('The type of CAPTCHA to serve.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::selectbox('recap-type', array(
						'options' => array('audio' => __('Audio', NBS_LANG_CODE), 'image' => __('Image', NBS_LANG_CODE)),
						'value' => 'image',
					))?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-for="recaptcha">
				<th>
					<?php _e('reCapthca Size', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('The size of the CAPTCHA widget.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::selectbox('recap-size', array(
						'options' => array('compact' => __('Compact', NBS_LANG_CODE), 'normal' => __('Normal', NBS_LANG_CODE)),
						'value' => 'normal',
					))?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-not-for="recaptcha">
				<th>
					<?php _e('Additional classes', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can specify here additinal CSS classes for your field.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('add_classes')?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-not-for="recaptcha">
				<th>
					<?php _e('Additional styles', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can specify here additinal CSS styles, that will be included in "style" tag, for your field.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('add_styles')?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-not-for="recaptcha">
				<th>
					<?php _e('Additional HTML attributes', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can specify here additinal HTML attributes, such as "id", or other, for your field.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('add_attr')?>
				</td>
			</tr>
		</table>
	</div>
	<div id="nbsFormFieldValidation" class="nbsTabContent">
		<table class="form-table">
			<tr class="nbsFieldParamRow" data-for="text,email,textarea,number,date,month,week,time,color,range,url,file">
				<th>
					<?php _e('Minimum length', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Possibility to bound field minimum length.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('min_size')?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-for="text,email,textarea,number,date,month,week,time,color,range,url,file">
				<th>
					<?php _e('Maximum length', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Possibility to bound field maximum length. For Files fields types - this is restriction for file size, in Mb.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('max_size')?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-for="text,textarea,email,url,date,time,number">
				<th>
					<?php _e('Only numbers', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Allow users to enter in this field - only numeric values.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::checkbox('vn_only_number')?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-for="text,textarea,email,url,date,time,number">
				<th>
					<?php _e('Only letters', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('Only letters will be allowed.', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::checkbox('vn_only_letters')?>
				</td>
			</tr>
			<tr class="nbsFieldParamRow" data-for="text,textarea,email,url,date,time,number,file">
				<th>
					<?php _e('Validation Pattern', NBS_LANG_CODE)?>
					<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html(__('You can modify or set here your custom patters. Edit this ONLY if you know how to modify regular expression patterns! For Files fields types you can set here file extensions, separated by comma - ",".', NBS_LANG_CODE))?>"></i>
				</th>
				<td>
					<?php echo htmlNbs::text('vn_pattern', array('attrs' => 'style="width: 100%;"'))?>
				</td>
			</tr>
		</table>
	</div>
	<?php echo htmlNbs::hidden('html')?>
</div>
<div id="nbsFormFieldHtmlInpWnd" style="display: none;" title="<?php _e('HTML / Text / Images / etc.', NBS_LANG_CODE)?>">
	<?php wp_editor('', 'nbs_html_field_editor')?>
</div>
<div id="nbsFormFieldGoogleMapsWnd" style="display: none;" title="<?php _e('Select desired Map', NBS_LANG_CODE)?>">
	<?php if($this->isGoogleMapsAvailable) { ?>
		<?php if(!empty($this->allGoogleMapsForSelect)) { ?>
			<label><?php _e('Select Map')?>: <?php echo htmlNbs::selectbox('nbs_gmap_sel', array(
				'options' => $this->allGoogleMapsForSelect,
				'attrs' => 'id="nbsFieldGoogleMapsSel"',
			))?></label>
		<?php } else { ?>
			<div class="description"><p><?php printf(__('You have no Google Maps for now. <a href="%s" target="_blank" class="button">Create Maps</a> at first, then you will be able to select it here and past into your form', NBS_LANG_CODE), frameGmp::_()->getModule('options')->getTabUrl('gmap_add_new'))?></p></div>
		<?php } ?>
	<?php } else { ?>
		<div class="description"><p><?php printf(__('To use this field type you need to have installed and activated <a href="%s" target="_blank">Google Maps Easy</a> plugin - it\'s Free! Just install it <a class="button" target="_blank" href="%s">here.</a>', NBS_LANG_CODE), 'https://wordpress.org/plugins/google-maps-easy/', admin_url('plugin-install.php?tab=search&s=Google+Maps+Easy+Supsystic'))?></p></div>
	<?php } ?>
</div>
