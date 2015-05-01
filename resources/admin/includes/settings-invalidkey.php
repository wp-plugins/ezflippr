<h1>ezFlippr <?php _e('Settings', __EZFLIPPR_PLUGIN_SLUG__);?></h1>

<div class="wrap">
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
<table class="ezf-settings">
    <tr>
        <th><?php _e('Do you have an access key?', __EZFLIPPR_PLUGIN_SLUG__);?></th>
        <td>
            <table>
                <tr><td>
                    <input type="radio" name="ezflippr-field-havekey" id="ezflippr-field-havekey-no" value="-1" checked>
                    <label for="ezflippr-field-havekey-no"><?php _e('No, I don\'t have one', __EZFLIPPR_PLUGIN_SLUG__);?></label>
                </td></tr>
                <tr><td>
                    <input type="radio" name="ezflippr-field-havekey" id="ezflippr-field-havekey-forgot" value="0">
                    <label for="ezflippr-field-havekey-forgot"><?php _e('Yes, but I don\'t remember what it is!', __EZFLIPPR_PLUGIN_SLUG__);?></label>
                </td></tr>
                <tr><td>
                    <input type="radio" name="ezflippr-field-havekey" id="ezflippr-field-havekey-yes" value="1">
                    <label for="ezflippr-field-havekey-yes"><?php _e('Yes, and I remember it!', __EZFLIPPR_PLUGIN_SLUG__);?></label>
                </td></tr>
            </table>
        </td>
    </tr>
    <tr id="tr-email">
        <th><label for="ezflippr-field-email"><?php _e('Enter the email your created you flipbooks with', __EZFLIPPR_PLUGIN_SLUG__);?></label></th>
        <td>
            <input type="email" name="ezflippr-field-email" id="ezflippr-field-email" value="<?php echo $email;?>">
            <input type="submit" name="ezflippr-submit" id="ezflippr-submit" class="button-primary" value="<?php _e('Send it to me by email', __EZFLIPPR_PLUGIN_SLUG__);?>">
        </td>
    </tr>
    <tr id="tr-key">
        <th><label for="ezflippr-field-key"><?php _e('Enter your access key', __EZFLIPPR_PLUGIN_SLUG__);?></label></th>
        <td>
            <input type="text" name="ezflippr-field-key" id="ezflippr-field-key">
            <input type="submit" name="ezflippr-submit" id="ezflippr-submit" class="button-primary" value="<?php _e('Verify', __EZFLIPPR_PLUGIN_SLUG__);?>">
        </td>
    </tr>
</table>

<input type="hidden" name="action" value="<?php echo $formAction;?>">
</form>
</div>