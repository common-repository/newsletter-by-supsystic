<section>
	<div class="supsystic-item supsystic-panel" style="padding-left: 10px;">
		<div id="containerWrapper">
			<form id="nbsSubListsFrm">
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e('Name', NBS_LANG_CODE)?></th>
						<td>
							<?php echo htmlNbs::text('label', array(
								'value' => ($this->subList ? $this->subList['label'] : ''),
								'required' => true,
							))?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e('Description', NBS_LANG_CODE)?></th>
						<td>
							<?php echo htmlNbs::textarea('description', array(
								'value' => ($this->subList ? $this->subList['description'] : ''),
								'required' => true,
							))?>
						</td>
					</tr>
				</table>
				<?php echo htmlNbs::hidden('mod', array('value' => 'subscribers_lists'))?>
				<?php echo htmlNbs::hidden('action', array('value' => 'save'))?>
				<?php echo htmlNbs::hidden('id', array('value' => ($this->subList ? $this->subList['id'] : 0)))?>
			</form>
			<hr style="clear: both;" />
			<form id="nbsSubListImportFromTxtFrm">
				<table class="form-table">
					<tr>
						<th scope="row">
							<?php _e('Import Subscribers from Text', NBS_LANG_CODE)?>
							<div class="description"><?php _e('Enter here list of subscribers email addresses, divided by coma symbol - ",", click "Import" button - and they will be imported into this list', NBS_LANG_CODE)?></div>
						</th>
						<td>
							<?php echo htmlNbs::textarea('emails')?><br />
							<button class="button button-primary" id="nbsSubListImportFromTxtBtn">
								<i class="fa fa-upload"></i>
								<?php _e('Import', NBS_LANG_CODE)?>
							</button>
							<div id="nbsSubListImportFromTxtMsg"></div>
							<?php echo htmlNbs::hidden('mod', array('value' => 'subscribers_lists'))?>
							<?php echo htmlNbs::hidden('action', array('value' => 'importFromTxt'))?>
							<?php echo htmlNbs::hidden('id', array('value' => ($this->subList ? $this->subList['id'] : 0)))?>
						</td>
					</tr>
				</table>
			</form>
			<hr style="clear: both;" />
			<table class="form-table">
				<tr>
					<th scope="row">
						<?php _e('Import Subscribers from CSV', NBS_LANG_CODE)?>
						<div class="description"><?php _e('Just select a file here with emails - and they will be added into your List', NBS_LANG_CODE)?></div>
					</th>
					<td>
						<table id="nbsSlCsvOptsTbl">
							<tr>
								<th><?php _e('Delimiter', NBS_LANG_CODE)?></th>
								<td><?php echo htmlNbs::text('csv_delimiter', array(
									'value' => esc_html(trim(json_encode( $this->csvGenerator->getDelimiter() ), '"')),
								))?></td>
							</tr>
							<tr>
								<th><?php _e('Enclosure', NBS_LANG_CODE)?></th>
								<td><?php echo htmlNbs::text('csv_enclosure', array(
									'value' => esc_html(trim(json_encode( $this->csvGenerator->getEnclosure() ), '"')),
								))?></td>
							</tr>
							<tr>
								<th><?php _e('Escape', NBS_LANG_CODE)?></th>
								<td><?php echo htmlNbs::text('csv_escape', array(
									'value' => esc_html( $this->csvGenerator->getEscape() ),
								))?></td>
							</tr>
						</table>
						<?php echo htmlNbs::ajaxfile('import_from_csv', array(
							'url' => uriNbs::mod('subscribers_lists', 'importFromCsv', array('reqType' => 'ajax', 'id' => ($this->subList ? $this->subList['id'] : 0))),
							'onSubmit' => 'nbsSubListCsvImportSubmitClb',
							'onComplete' => 'nbsSubListCsvImportCompleteClb',
							'buttonName' => '<i class="fa fa-upload"></i> '. __('Upload CSV', NBS_LANG_CODE),
							'data' => 'g_nbsSubListCsvImportData',
						))?>
						<div id="nbsSubListImportFromCsvMsg"></div>
					</td>
				</tr>
			</table>
			<hr style="clear: both;" />
			<table class="form-table">
				<tr>
					<th scope="row">
						<?php _e('Import Subscribers from other services or plugins', NBS_LANG_CODE)?>
						<div class="description"><?php _e('MailChimp for example', NBS_LANG_CODE)?></div>
					</th>
					<td>
						<p><?php printf(__('Go to global <a href="%s" target="_blank" class="button"><i class="fa fa-upload"></i>&nbsp;Import Tool</a>', NBS_LANG_CODE), frameNbs::_()->getModule('options')->getTabUrl('importer', 'slid'. $this->subList['id']))?></p>
					</td>
				</tr>
			</table>
			<hr style="clear: both;" />
				<table class="form-table">
					<tr>
						<th scope="row">
							<?php _e('Export Subscribers to CSV file', NBS_LANG_CODE)?>
							<div class="description"><?php _e('Choose subscription list to export. Hold CTRL to select multiple.', NBS_LANG_CODE)?></div>
						</th>
						<td>
							<?php echo htmlNbs::radiobutton('nbsSubListExportRadiobutton[]',array('attrs' => 'id="nbsSubListExportRadiobutton1" class="nbsSubListExportRadiobutton" ', 'value'=>'all' )) ?>
							<label for="nbsSubListExportRadiobutton1"><?php _e('Export all subscribers', NBS_LANG_CODE)?></label><br />
							<?php echo htmlNbs::radiobutton('nbsSubListExportRadiobutton[]',array('attrs' => 'id="nbsSubListExportRadiobutton2" class="nbsSubListExportRadiobutton" ', 'value'=>'lists','checked'=>'checked')) ?>
							<label for="nbsSubListExportRadiobutton2"><?php _e('Export subscribers from selected lists', NBS_LANG_CODE)?></label><br /><br />
							<?php echo htmlNbs::subscriptionList('nbsSubListExportSubscriptionLists',array('attrs' => 'id="nbsSubListExportSubscriptionLists"  ','value'=>$this->subList ? $this->subList['id'] : 0)) ?><br /><br />
							<a class="button button-primary" id="nbsSubListExportSubmit" basehref="<?php echo uriNbs::mod('subscribers_lists', 'exportToCsv')?>" href="">
								<i class="fa fa-upload"></i>
								<?php _e('Export to CSV', NBS_LANG_CODE)?>
							</a>
						</td>
					</tr>
			</table>

		</div>
	</div>
</section>
