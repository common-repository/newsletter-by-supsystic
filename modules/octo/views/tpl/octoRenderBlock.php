<?php
	$alignClass = '';
	if($this->block && $this->block['params'] && isset($this->block['params']['align']) && !empty($this->block['params']['align']['val'])) {
		$alignClass = 'nbsAlign_'. $this->block['params']['align']['val'];
	}
?>
<tr id="{{block.view_id}}" class="nbsBlock <?php echo $alignClass?>" data-id="<?php echo $this->block ? $this->block['id'] : 0?>">
	<?php if(!$this->block || (isset($this->block['html']) && !empty($this->block['html']))) { ?>
		<td class="nbsBlockContent" width="100%" valign="top"><?php echo $this->block ? $this->block['html'] : ''?></td>
		<?php if($this->isEditMode) { ?>
		<td class="nbsBlockMenuShell" valign="top"></td>
		<?php }?>
	<?php }?>
</tr>
<?php if(!$this->block || (isset($this->block['css']) && !empty($this->block['css']))) { ?>
	<style type="text/css" class="nbsBlockStyle"><?php echo $this->block ? $this->block['css'] : ''?></style>
<?php }?>