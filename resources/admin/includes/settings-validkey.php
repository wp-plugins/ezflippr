<h1>ezFlippr <?php _e('Settings', __EZFLIPPR_PLUGIN_SLUG__);?></h1>

<div class="wrap">
<?php
    $formAction = "ezflippr-settings+" . uniqid();
    if($this->error):
?>
    <div class="error"><?php echo $this->error;?></div>
<?php
    endif;
    $email      = get_option('admin_email');
?>
<form method="post" name="" action="">
<?php echo wp_nonce_field($formAction, 'nonce'); ?>
<input type="submit" name="ezflippr-refresh" id="ezflippr-refresh" class="button-primary" value="<?php _e('Refresh', __EZFLIPPR_PLUGIN_SLUG__);?>">
<input type="hidden" name="action" value="<?php echo $formAction;?>">
<p><?php echo $this->getLastUpdate();?></p>
</form>


	<?php
	$formAction = "ezflippr-settings+" . uniqid();
	if($this->error){
		?>
		<div class="error"><?php echo $this->error;?></div>
	<?php
	}
	if($this->notice){
		?>
		<div class="updated"><?php echo $this->notice;?></div>
	<?php
	}
	$email      = get_option('admin_email');
	?>
	<form method="post" name="" action="">
		<?php echo wp_nonce_field($formAction, 'nonce'); ?>

	<p><strong><?php _e('Your access key: '); ?></strong></p>
	<input style="width:250px;" type="text" name="ezflippr-field-key" id="ezflippr-field-key" value="<?php echo self::getOption('accesskey'); ?>">
	<input type="submit" name="ezflippr-submit" id="ezflippr-submit" class="button-primary" value="<?php _e('Verify', __EZFLIPPR_PLUGIN_SLUG__);?>">
	<input type="hidden" name="action" value="<?php echo $formAction;?>">
	</form>
</div>