<?php
require_once __DIR__ . "/../../../../wp-load.php";
require_once __DIR__ . "/../ezflippr.php";

$postID     = $_GET['post'];
if ($_GET['action'] == 'reinstall') {
	$res = ezFlippr::installFlipbook($postID, false);
	$res = ezFlippr::installFlipbook($postID, true);
} else {
	$res = ezFlippr::installFlipbook($postID, $_GET['action'] == 'install');
}

if ((array_key_exists('json', $_GET)) && ($_GET['json'])) {
	header('Content-Type: application/json');
	echo json_encode(array('result'=>$res));
} else {
	wp_safe_redirect( admin_url( 'edit.php?post_type=ezflippr_flipbook' ) );
}
