<?php

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
    exit();

defined("__EZFLIPPR_PLUGIN_NAME__") or define("__EZFLIPPR_PLUGIN_NAME__", "ezFlippr");
defined("__EZFLIPPR_PLUGIN_SLUG__") or define("__EZFLIPPR_PLUGIN_SLUG__", "ezflippr__");
defined("__EZFLIPPR_DIR__") or define( '__EZFLIPPR_DIR__', plugin_dir_path( __FILE__) );

require_once __DIR__ . "/resources/Util.php";

$opts   = array('accesskey', 'accesskey-lastcheck', 'books', 'lastupdate', 'email');
foreach($opts as $opt){
    delete_option(__EZFLIPPR_PLUGIN_SLUG__ . $opt);
}

$wpUploads  = wp_upload_dir();
$dir        = $wpUploads['basedir'] . DIRECTORY_SEPARATOR  . __EZFLIPPR_PLUGIN_SLUG__;

try{
    @Util::cleanDir($dir);
}catch(Exception $e){
    // ignore
}