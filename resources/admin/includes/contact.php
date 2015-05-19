<?php
$formAction = "ezflippr-contact+" . uniqid();
?>
<form method="post" name="" action="" class="ezflippr-settings">
	<h2><?php _e('Need help or have a suggestion?', __EZFLIPPR_PLUGIN_SLUG__); ?></h2>
	<p><?php _e('We\'re always happy to hear from you and to help out whether it\'s by giving you a walk through or by adding a feature. Don\'t be shy and get in touch.', __EZFLIPPR_PLUGIN_SLUG__); ?></p>

	<?php echo wp_nonce_field($formAction, 'nonce'); ?>
	<p>
		<label for="ezflippr-from"><?php _e('From:', __EZFLIPPR_PLUGIN_SLUG__); ?></label><br/>
		<input type="email" required class="required" name="from" value="<?php
		$email = false;
		$current_user = wp_get_current_user();
		if ($current_user) $email = $current_user->user_email;
		if (!$email) $email = ezFlippr::getOption('email');
		if (!$email) $email = get_bloginfo('admin_email');
		echo esc_attr($email);
		?>">
	</p>
	<p>
		<label for="ezflippr-subject"><?php _e('Subject:', __EZFLIPPR_PLUGIN_SLUG__); ?></label><br/>
		<input type="text" required class="required" name="subject">
	</p>
	<p>
		<label for="ezflippr-flipbook-uuid"><?php _e('Is this about a flipbook in particular?', __EZFLIPPR_PLUGIN_SLUG__); ?></label><br/>
		<select name="flipbook_uuid" id="ezflippr-flipbook-uuid">
			<option value=""><?php _e('No, it\'s not.', __EZFLIPPR_PLUGIN_SLUG__); ?></option>
			<?php
			$books = get_posts(array(
				'post_type'=>'ezflippr_flipbook',
				'posts_per_page'=>-1,
				'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
			));
			foreach ($books as $b) {
				printf('<option value="%1$s">Yes, titled "%2$s"</option>',
					self::getPostMeta($b->ID, 'uuid').'/'.$b->post_status.'/'.$b->ID .' '.get_permalink($b->ID),
					esc_html($b->post_title)
				);
			}
			?>
		</select>
	</p>
	<p>
		<label for="ezflippr-message"><?php _e('Message:', __EZFLIPPR_PLUGIN_SLUG__); ?>:</label><br/>
		<textarea required class="required" name="message"></textarea>
	</p>
	<p>
		<em><?php _e('Full disclosure: In order to help you out better and quicker, the plugin is going to send the following information with this message:', __EZFLIPPR_PLUGIN_SLUG__); ?></em>
	</p>
	<ol>
		<li><?php _e('Your PHP version: ', __EZFLIPPR_PLUGIN_SLUG__); ?><code><?php echo self::getPHPVersion(); ?></code></li>
		<li><?php _e('Your WordPress version: ', __EZFLIPPR_PLUGIN_SLUG__); ?><code><?php echo self::getWordPressVersion(); ?></code></li>
		<li><?php _e('Your ezFlippr plugin version: ', __EZFLIPPR_PLUGIN_SLUG__); ?><code><?php echo ezFlippr::getVersion(); ?></code></li>
		<li><?php _e('Your access key: ', __EZFLIPPR_PLUGIN_SLUG__); ?><code><?php echo ezFlippr::getOption('accesskey'); ?></code></li>
		<li><?php _e('Your site\'s language: ', __EZFLIPPR_PLUGIN_SLUG__); ?><code><?php echo get_locale(); ?></code></li>
		<li><?php _e('Your site\'s address: ', __EZFLIPPR_PLUGIN_SLUG__); ?><code><?php echo get_bloginfo('url'); ?></code></li>
	</ol>
	<p>
		<input type="checkbox" name="optout" value="1"> <?php _e('I do not want this information to be sent along with my message.', __EZFLIPPR_PLUGIN_SLUG__); ?>
	</p>

	<br>
	<p>
		<?php _e('If your server supports it (we\'re sorry, but we can\'t know for sure), you will receive a copy of this email.', __EZFLIPPR_PLUGIN_SLUG__); ?>
	</p>
	<p>
		<?php _e('In any case, we will get back to you shortly. Our target is that 99% of the messages we receive are answered within 8 open hours, and that all messages are answered within at most 3 days, open or not.', __EZFLIPPR_PLUGIN_SLUG__); ?>
	</p>
	<p>
		<?php _e('We think we are doing pretty good so far. You agree? Like us on <a href="%1%s" target="_blank">Facebook</a>. You don\'t? Please please please, pretty please, let us know what you think we can do better with as much details as possible, and we promise we\'ll read thoroughly and act upon it.', __EZFLIPPR_PLUGIN_SLUG__); ?>
	</p>
	<input type="submit" name="ezflippr-submit" id="ezflippr-submit" class="button-primary" value="<?php _e('Send', __EZFLIPPR_PLUGIN_SLUG__);?>">
	<input type="hidden" name="action" value="<?php echo $formAction;?>">
</form>
