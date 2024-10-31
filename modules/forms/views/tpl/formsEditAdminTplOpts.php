<a href="<?php echo $this->nbsAddNewUrl. '&change_for='. $this->form['id']?>" class="button button-primary nbsFormSelectTpl">
	<?php _e('Change Form Template', NBS_LANG_CODE)?>
</a>
<div style="clear: both;"></div>
<table class="form-table" style="width: auto;">
	<tr>
		<th scope="row" class="col-w-1perc">
			<?php _e('Width', NBS_LANG_CODE)?>
		</th>
		<td class="col-w-1perc">
			<?php echo htmlNbs::text('params[tpl][width]', array('value' => $this->form['params']['tpl']['width']))?>
		</td>
		<td class="col-w-1perc" colspan="3">
			<label style="margin-right: 10px;" class="supsystic-tooltip" title="<?php _e('Max width for percentage - is 100', NBS_LANG_CODE)?>">
				<?php echo htmlNbs::radiobutton('params[tpl][width_measure]', array('value' => '%', 'checked' => htmlNbs::checkedOpt($this->form['params']['tpl'], 'width_measure', '%')))?>
				<?php _e('Percents', NBS_LANG_CODE)?>
			</label>
			<label>
				<?php echo htmlNbs::radiobutton('params[tpl][width_measure]', array('value' => 'px', 'checked' => htmlNbs::checkedOpt($this->form['params']['tpl'], 'width_measure', 'px')))?>
				<?php _e('Pixels', NBS_LANG_CODE)?>
			</label>
		</td>
	</tr>
	<?php for($i = 0; $i < $this->form['params']['opts_attrs']['bg_number']; $i++) { ?>
		<tr class="nbsBgRowShell">
			<th scope="row" class="col-w-1perc">
				<?php 
					$bgNumTitle = $this->form['params']['opts_attrs']['bg_number'] == 1 ? __('Background', NBS_LANG_CODE) : sprintf(__('Background %d', NBS_LANG_CODE), $i + 1);
					if($this->bgNames && isset($this->bgNames[ $i ]) && !empty($this->bgNames[ $i ])) {
						echo $this->bgNames[ $i ]. '<div class="description">'. $bgNumTitle. '</div>';
					} else {
						echo $bgNumTitle;
					}
				?>
			</th>
			<td class="col-w-1perc">
				<?php echo htmlNbs::selectbox('params[tpl][bg_type_'. $i. ']', array('options' => $this->bgTypes, 'value' => $this->form['params']['tpl']['bg_type_'. $i], 'attrs' => 'data-iter="'. $i. '" class="nbsBgTypeSelect"'))?>
			</td>
			<td class="col-w-1perc nbsBgTypeShell nbsBgTypeShell_<?php echo $i?> nbsBgTypeImgShell_<?php echo $i?>">
				<?php echo htmlNbs::imgGalleryBtn('params[tpl][bg_img_'. $i. ']', array('onChange' => 'nbsShowImgPrev', 'attrs' => 'data-iter="'. $i. '" class="button button-sup-small"', 'value' => $this->form['params']['tpl']['bg_img_'. $i]))?>
			</td>
			<td class="col-w-1perc nbsBgTypeShell nbsBgTypeShell_<?php echo $i?> nbsBgTypeImgShell_<?php echo $i?>" style="padding-top: 10px; min-width: 100px;">
				<img src="" style="max-width: 300px; max-height: 200px;" class="nbsBgImgPrev_<?php echo $i?>" />
			</td>
			<td class="col-w-1perc nbsBgTypeShell nbsBgTypeShell_<?php echo $i?> nbsBgTypeColorShell_<?php echo $i?>" style="line-height: 40px;">
				<?php echo htmlNbs::colorpicker('params[tpl][bg_color_'. $i. ']', array('value' => $this->form['params']['tpl']['bg_color_'. $i]))?>
			</td>
		</tr>
	<?php }?>
	<?php /*TODO: add this to PRO maybe?>
		<tr>
			<th scope="row" class="col-w-1perc">
				<?php _e('Label Font style', NBS_LANG_CODE)?>
				<?php if(!$this->isPro) {?>
					<span class="nbsProOptMiniLabel"><a target="_blank" href="<?php echo $this->mainLink. '?utm_source=plugin&utm_medium=font_label&utm_campaign=forms';?>"><?php _e('PRO option', NBS_LANG_CODE)?></a></span>
				<?php }?>
			</th>
			<td class="col-w-1perc">
				<?php echo htmlNbs::fontsList('params[tpl][font_label]', array(
					'attrs' => 'class="nbsProOpt"',
					'value' => isset($this->form['params']['tpl']['font_label']) ? $this->form['params']['tpl']['font_label'] : NBS_DEFAULT,
					'default' => __('Default', NBS_LANG_CODE),
				))?>
			</td>
			<td class="col-w-1perc">
				<?php echo htmlNbs::colorpicker('params[tpl][label_font_color]', array(
					'attrs' => 'class="nbsProOpt"',
					'value' => isset($this->form['params']['tpl']['label_font_color']) ? $this->form['params']['tpl']['label_font_color'] : '#000000',
				))?>
			</td
		</tr>
	<?php */?>
</table>
