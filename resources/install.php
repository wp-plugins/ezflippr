<?php
require_once __DIR__ . "/../../../../wp-load.php";
require_once __DIR__ . "/../ezflippr.php";

$postID     = $_GET['post'];
if ($_GET['action'] == 'reinstall') {
	ezFlippr::installFlipbook($postID, false);
	ezFlippr::installFlipbook($postID, true);
} else {
	ezFlippr::installFlipbook($postID, $_GET['action'] == 'install');
}
wp_safe_redirect(admin_url( 'edit.php?post_type=ezflippr_flipbook' ));
