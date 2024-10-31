<section>
	<div class="supsystic-item supsystic-panel">
		<div id="containerWrapper">
			<form id="nbsImpForm">
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e('Import Subscribers from', NBS_LANG_CODE)?></th>
						<td><?php
							if(!empty($this->importSourceForSelect)) {
								echo htmlNbs::selectbox('sets[source]', array(
									'options' => $this->importSourceForSelect,
									'attrs' => 'class="chosen"',
									'value' => $this->getSet('source'),
								));
							} else {
								_e('No import engines is supported on your site', NBS_LANG_CODE);
							}
						?></td>
					</tr>
					<?php /*Sources options*/ ?>
					<?php if(!empty($this->importSourceForSelect)) {?>
						<?php foreach($this->importSourceForSelect as $source => $sourceLabel) {
							$wrapper = frameNbs::_()->getModule('api_loader')->getWrapper( $source );
							$isSupported = $wrapper->isSupported();
							if($isSupported) {
								$sourceOpts = $wrapper->getOpts();
								if(!empty($sourceOpts)) {
									foreach($sourceOpts as $sOpt) {
										?>
										<tr class="nbsImpSourceSetRow" 
											data-source="<?php echo $source;?>"
											<?php if(isset($sOpt['is_lists']) && $sOpt['is_lists']) {?>
												data-for="lists"
											<?php }?>
										>
											<th scope="row">
												<?php echo $sOpt['label'];?>
												<i class="fa fa-question supsystic-tooltip" title="<?php echo esc_html( $sOpt['desc'] );?>"></i>
											</th>
											<td><?php
												$htmlParams = array(
													'value' => $wrapper->getSet( $sOpt['key'] ),
												);
												if(isset($sOpt['attrs'])) {
													$htmlParams['attrs'] = $sOpt['attrs'];
												}
												$htmlMethod = $sOpt['html'];

												$htmlInput = htmlNbs::$htmlMethod('sets['. $source. ']['. $sOpt['key']. ']', $htmlParams);
												if(isset($sOpt['is_lists']) && $sOpt['is_lists']) {
													$htmlInput = '<div class="nbsImpListsShell" data-source="'. $source. '">'. $htmlInput. '</div>
														<span class="nbsImpNoDataForListsError" data-source="'. $source. '">'. $sOpt['lists_error']. '</span>
														<span class="nbsImpListsMsg" data-source="'. $source. '"></span>';
												}
												echo $htmlInput;
											?></td>
										</tr>
										<?php
									}
								}
							} else { ?>
								<tr class="nbsImpSourceSetRow" data-source="<?php echo $source;?>">
									<th scope="row">
										<?php printf(__('%s is not supported for now', NBS_LANG_CODE), $sourceLabel)?>
									</th>
									<td><?php echo implode('<br />', $wrapper->getErrors())?></td>
								</tr>
							<?php }
						}?>
					<?php }?>
					<tr>
						<th scope="row"><?php _e('Import with Lists', NBS_LANG_CODE)?></th>
						<td><?php
							echo htmlNbs::checkbox('sets[import_with_lists]', array(
								'checked' => $this->getSet('import_with_lists'),
							));
						?></td>
					</tr>
					<tr style="display: none;" class="nbsImpToListsShell">
						<th scope="row"><?php _e('Import to List', NBS_LANG_CODE)?></th>
						<td><?php
							echo htmlNbs::selectbox('sets[import_to_list]', array(
								'options' => $this->listsForSelect,
								'attrs' => 'class="chosen"',
								'value' => $this->getSet('import_to_list'),
							));
						?></td>
					</tr>
					<tr class="nbsImpIgnoreSameShell">
						<th scope="row"><?php _e('Do not duplicate Lists names', NBS_LANG_CODE)?></th>
						<td><?php
							echo htmlNbs::checkbox('sets[ignore_same_lists_names]', array(
								'checked' => $this->getSet('ignore_same_lists_names'),
							));
						?></td>
					</tr>
				</table>
				<?php echo htmlNbs::hidden('mod', array('value' => 'importer'))?>
				<?php echo htmlNbs::hidden('action', array('value' => 'import'))?>
				<button class="button button-primary">
					<i class="fa fa-fw fa-cloud-upload"></i>
					<?php _e('Start Import', NBS_LANG_CODE)?>
				</button>
				<div id="nbsImpMsg"></div>
			</form>
		</div>
		<div style="clear: both;"></div>
	</div>
</section>