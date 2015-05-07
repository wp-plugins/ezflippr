<?php
/**
* Plugin Name: ezFlippr
* Plugin URI: http://www.nuagelab.com/wordpress-plugins/ezflippr
* Description: Adds rich flipbooks made from PDF through ezFlippr.com
* Version: 1.1.3
* Author: NuageLab <wordpress-plugins@nuagelab.com>
* Author URI: http://www.nuagelab.com/wordpress-plugins
* License: GPL2
*/
/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define("__EZFLIPPR_PLUGIN_NAME__", "ezFlippr");
define("__EZFLIPPR_PLUGIN_SLUG__", "ezflippr");
define( '__EZFLIPPR_VERSION__', 1.0);
define( '__EZFLIPPR_DIR__', plugin_dir_path( __FILE__) );
define( '__EZFLIPPR_URL__', plugin_dir_url( __FILE__) );
define( '__EZFLIPPR_ROOT__', trailingslashit( plugins_url( '', __FILE__ ) ) );
define( '__EZFLIPPR_RESOURCES__', __EZFLIPPR_ROOT__ . 'resources/' );
define( '__EZFLIPPR_IMAGES__', __EZFLIPPR_RESOURCES__ . 'images/' );
define("__EZFLIPPR_DEBUG__", false);
define("__EZFLIPPR_TEST__", false);
define("__EZFLIPPR_STAGING__", false);

require_once __DIR__ . "/resources/Util.php";

if (__EZFLIPPR_DEBUG__) {
    @error_reporting(E_ALL);
    @ini_set("display_errors", "1");
}

if (class_exists('ezFlippr', false)) {
	//die(__('ERROR: It looks like you have more than one instance of ' . __EZFLIPPR_PLUGIN_NAME__ . ' installed. Please remove additional instances for this plugin to work again.', __EZFLIPPR_PLUGIN_SLUG__));
}

/**
 * Abort loading if WordPress is upgrading
 */
if (defined('WP_INSTALLING') && WP_INSTALLING) return;

class ezFlippr{

    const API_ENDPOINT  = 'https://ezflippr.com/api/';
    const API_TIMEOUT   = 400;  // seconds

    private $error;
    private $notice;

    public function __construct() {
        // all hooks and actions
        add_action( 'init', array( $this, 'ezflippr_register' ) );
        register_activation_hook( __FILE__ , array( $this, 'ezflippr_activate' ) );
        register_deactivation_hook( __FILE__ , array( $this, 'ezflippr_deactivate' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'ezflippr_includeResources_user' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'ezflippr_includeResources_admin' ) );
        add_action('plugins_loaded', array( $this, 'ezflippr_i18n' ) );
	    add_shortcode('flipbook', array( $this, 'ezflippr_shortcode' ));
	    add_filter('single_template', array( $this, 'ezflippr_single_template' ));
    }

    /**
     * Initializes the locale
     */
    public function ezflippr_i18n()
    {
        $pluginDirName  = dirname( plugin_basename( __FILE__ ) );
        $domain         = __EZFLIPPR_PLUGIN_SLUG__;
        $locale         = apply_filters('plugin_locale', get_locale(), $domain);
        load_textdomain($domain, WP_LANG_DIR . '/' . $pluginDirName . "/languages/" . $domain . '-' . $locale . '.mo');
        //load_plugin_textdomain( $domain, '', $pluginDirName . '/languages/' );
	    load_plugin_textdomain($domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
    }

    /**
     * Initializes the admin menu
     */
    public function ezflippr_add_menu()
    {
        global $submenu;
        unset($submenu['edit.php?post_type=ezflippr_flipbook'][10]); // Removes 'Add New'
        
        add_menu_page(__EZFLIPPR_PLUGIN_NAME__, __EZFLIPPR_PLUGIN_NAME__, 'manage_options', __EZFLIPPR_PLUGIN_SLUG__, array($this, 'ezflippr_settings'), __EZFLIPPR_IMAGES__ . 'favicon.png');
    }

	/**
	 * Add style to admin header
	 */
	public function ezflippr_admin_head()
	{
		if ('ezflippr_flipbook' == get_post_type()) {
			echo '<style type="text/css">'.PHP_EOL;
            echo '.add-new-h2{display:none;}'.PHP_EOL;
            echo '</style>'.PHP_EOL;
			echo '<script>'.PHP_EOL;
			echo 'ez_str_installing = "'.__('Installing...',__EZFLIPPR_PLUGIN_SLUG__).'";'.PHP_EOL;
			echo 'ez_str_reinstalling = "'.__('Reinstalling...',__EZFLIPPR_PLUGIN_SLUG__).'";'.PHP_EOL;
			echo 'ez_str_uninstalling = "'.__('Uninstalling...',__EZFLIPPR_PLUGIN_SLUG__).'";'.PHP_EOL;
			echo 'ez_str_please_wait = "'.__('Please wait...',__EZFLIPPR_PLUGIN_SLUG__).'";'.PHP_EOL;
			echo '</script>'.PHP_EOL;
		}
	}

	/**
	 * Add the modified notice if necessary
	 */
	public function ezflippr_add_modified_notice()
	{
		$modified = @unserialize(self::getOption('modified'));
		$dismissed = @unserialize(self::getOption('dismissed'));
		if (!is_array($modified)) $modified = array();
		if (!is_array($dismissed)) $dismissed = array();

		foreach ($modified as $fb) {
			if ((array_key_exists($fb->ID, $dismissed)) && ($dismissed[$fb->ID] >= $fb->time_remote)) {
				continue;
			}
			if ((array_key_exists('dismiss-ezflippr-modified', $_GET)) && ($_GET['dismiss-ezflippr-modified'] == $fb->ID)) {
				$dismissed[$fb->ID] = $fb->time_remote;
				continue;
			}

			if ($fb->modified_reason == 'bought') $str = __('Your flipbook named "<a target="_blank" href="%4$s">%1$s</a>" has been modified.', __EZFLIPPR_PLUGIN_SLUG__);
			else $str = __('Your flipbook named "<a target="_blank" href="%4$s">%1$s</a>" has been purchased.', __EZFLIPPR_PLUGIN_SLUG__);
			$str .= ' '.__('<a href="%2$s">Click here</a> to reinstall it, or <a href="%3$s">here to dismiss this notice</a>.', __EZFLIPPR_PLUGIN_SLUG__);

			echo '<div class="update-nag">';
			printf($str,
				esc_html($fb->post_title),
				__EZFLIPPR_RESOURCES__ . 'install.php?post=' . $fb->ID . '&action=reinstall',
				add_query_arg('dismiss-ezflippr-modified',$fb->ID),
				get_permalink($fb->ID)
			);
			echo '</div>';
		}
		self::setOption('dismissed', serialize($dismissed));

		// Set admin notice if not access key has been set
		$accessKey = array_key_exists('ezflippr-field-key', $_POST) ? $_POST['ezflippr-field-key'] : null;
		if (!$this->verifyAccessKey($accessKey)) {
			echo '<div class="update-nag">';
			printf(__('The ezFlippr plugin needs to be set up in order to access your flipbooks. <a href="%1$s">Do it now</a>.',__EZFLIPPR_PLUGIN_SLUG__),
				'/wp-admin/admin.php?page=ezflippr'
			);
			echo '</div>';
		}
	}

    /**
     * Saves settings from the settings screen
     */
    public function ezflippr_settings()
    {
        $accessKey  = NULL;
        if (isset($_POST['ezflippr-submit']) && wp_verify_nonce($_POST['nonce'], $_POST['action'])) {
	        self::saveSettings();
	        $accessKey  = $_POST['ezflippr-field-key'];

	        if ((@$_POST['ezflippr-field-havekey'] != 1) && (isset($_POST['ezflippr-field-email']))) {
                $this->sendAccessKey($_POST['ezflippr-field-email']);
            }
        } else if (isset($_POST['ezflippr-refresh']) && wp_verify_nonce($_POST['nonce'], $_POST['action'])) {
            $this->refreshList();
        }

        if (!$this->verifyAccessKey($accessKey)) {
            if ($accessKey) $this->error = __("The access key you entered is invalid", __EZFLIPPR_PLUGIN_SLUG__);
            include_once __EZFLIPPR_DIR__ . "resources/admin/includes/settings-invalidkey.php";
        }else{
            include_once __EZFLIPPR_DIR__ . "resources/admin/includes/settings-validkey.php";
        }
    }

    /**
     * Loads the JS and CSS resources
     */
    function ezflippr_includeResources_user()
    {
        wp_enqueue_script("jquery");

        wp_register_script("ezflippr", __EZFLIPPR_RESOURCES__ . "js/ezflippr.js");
        wp_enqueue_script("ezflippr");

        wp_register_style("ezflippr", __EZFLIPPR_RESOURCES__ . "css/ezflippr.css");
        wp_enqueue_style("ezflippr");

	    wp_register_script("swfobject", __EZFLIPPR_RESOURCES__ . "static/assets/swfobject.js");
    }

    /**
     * Loads the JS and CSS resources
     */
    public function ezflippr_includeResources_admin()
    {
        wp_enqueue_script("jquery");

        wp_register_script("ezflippr", __EZFLIPPR_RESOURCES__ . "js/ezflippr.js");
        wp_enqueue_script("ezflippr");

        wp_register_style("ezflippr", __EZFLIPPR_RESOURCES__ . "css/ezflippr.css");
        wp_enqueue_style("ezflippr");
    }

    /**
     * Register the custom post type ezflippr_flipbook
     */
    public function ezflippr_register()
    {
		// Create custom post type
		register_post_type( 'ezflippr_flipbook',
			array(
                    'labels' => array(
                        'name' 					=>	__( 'Flipbooks', __EZFLIPPR_PLUGIN_SLUG__ ),
                        'singular_name' 		=> 	__( 'Flipbook', __EZFLIPPR_PLUGIN_SLUG__ ),
                        'edit' 					=> 	__( 'Edit', __EZFLIPPR_PLUGIN_SLUG__ ),
                        'edit_item' 			=> 	__( 'Edit Flipbook', __EZFLIPPR_PLUGIN_SLUG__ ),
                        'view' 					=> 	__( 'View', __EZFLIPPR_PLUGIN_SLUG__ ),
                        'view_item' 			=> 	__( 'View Flipbook', __EZFLIPPR_PLUGIN_SLUG__ ),
                        'not_found' 			=> 	__( 'No Flipbooks found', __EZFLIPPR_PLUGIN_SLUG__ ),
                        'not_found_in_trash'	=> 	__( 'No Flipbooks found in Trash', __EZFLIPPR_PLUGIN_SLUG__ )
                    ),

                    'label'						=>	__( 'Flipbooks', __EZFLIPPR_PLUGIN_SLUG__ ),
                    'public' 					=>	true,
                    'publicly_queryable'        =>	true,
                    'show_ui'                   =>	true,
                    'show_in_nav_menus'         =>	true,
                    'show_in_menu'              =>	true,
                    'query_var' 				=>	true,
                    'exclude_from_search' 		=>	true,
                    'has_archive' 				=>	true,
                    'map_meta_cap' 				=>	true,
                    'hierarchical' 				=>	false,
                    'can_export' 				=>	false,
                    'supports'                  =>  array('title', 'thumbnail'),
                    'menu_icon'                 => 'dashicons-book-alt',
                    'rewrite'                   => array('slug'=> 'flipbook'),
                    'description'               => __('Flipbooks imported from ezFlippr.com', __EZFLIPPR_PLUGIN_SLUG__),
            )
        );

	    if (is_admin()) {
		    // Add menu
		    add_action( 'admin_menu', array( $this, 'ezflippr_add_menu' ) );

		    // Change visual
		    add_action( 'admin_head', array( $this, 'ezflippr_admin_head' ) );

		    // Modify menu
		    add_filter('post_row_actions', array($this, 'remove_row_actions'), 10, 2);
		    add_filter('manage_edit-ezflippr_flipbook_columns', array($this, 'add_flipbook_columns'));
		    add_action('manage_ezflippr_flipbook_posts_custom_column', array($this,'manage_flipbook_columns'), 10, 2);

		    // Update every 4 hours
		    if (self::getOption('lastupdate')+4*3600 < time()) {
			    $this->refreshList();
		    }

		    // Check if some books have been modified
		    add_action('admin_notices', array(&$this, 'ezflippr_add_modified_notice'));
	    }
    }

    /**
     * Removes the Quick Edit etc. actions in the posts summary screen
     * 
     * @return array
     */
	public function remove_row_actions($actions, $post)
	{
        if ($post->post_type !== 'ezflippr_flipbook') return $actions;

        unset( $actions['edit'] );
        unset( $actions['view'] );
        //unset( $actions['trash'] );
        unset( $actions['inline hide-if-no-js'] );

        return $actions;
    }

    /**
     * Adds custom colums in the posts summary screen
     * 
     * @return array
     */
	public function add_flipbook_columns($columns)
	{
		$new_columns['cb'] = '<input type="checkbox" />';
		$new_columns['title'] = _x('Title', 'column name');
		$new_columns['status'] = __('Status', __EZFLIPPR_PLUGIN_SLUG__);
		$new_columns['actions'] = __('Actions', __EZFLIPPR_PLUGIN_SLUG__);
		$new_columns['date'] = _x('Date', 'column name');
	 
		return $new_columns;
	}

    /**
     * Adds colum values for the custom columns in the posts summary screen
     */
	public function manage_flipbook_columns($column_name, $id)
	{
		switch ($column_name) {
			case 'status':
				if (self::getPostMeta($id, 'installed') == 1) {
					_e('Installed', __EZFLIPPR_PLUGIN_SLUG__);
					echo '<br/>';
					echo '<code>[flipbook id="'.$id.'" width="100%" height="500"]</code>';
				} else {
					if (self::getPostMeta($id, 'status') < 90) {
						_e( 'Not installed', __EZFLIPPR_PLUGIN_SLUG__ );
					} else {
						_e( 'Expired', __EZFLIPPR_PLUGIN_SLUG__ );
					}
				}
				break;
			case 'actions':
				printf('<a href="%1$s">%2$s</a>', '/wp-admin/post.php?post='.$id.'&action=edit', __('Edit', __EZFLIPPR_PLUGIN_SLUG__));

				if (self::getPostMeta($id, 'installed') == 1) {
					echo ' | ';
					printf('<a href="%1$s">%2$s</a>', get_permalink($id), __('View', __EZFLIPPR_PLUGIN_SLUG__));
					echo ' | ';
					printf('<a class="ez-btn-uninstall" href="%1$s">%2$s</a>', __EZFLIPPR_RESOURCES__ . 'install.php?post=' . $id . '&action=uninstall', __('Uninstall', __EZFLIPPR_PLUGIN_SLUG__));
					if (self::getPostMeta($id, 'status') < 90) {
						echo ' | ';
						printf( '<a class="ez-btn-reinstall" href="%1$s">%2$s</a>', __EZFLIPPR_RESOURCES__ . 'install.php?post=' . $id . '&action=reinstall', __( 'Reinstall', __EZFLIPPR_PLUGIN_SLUG__ ) );
					}
				} else {
					if (self::getPostMeta($id, 'status') < 90) {
						echo ' | ';
						printf( '<a class="ez-btn-install" href="%1$s">%2$s</a>', __EZFLIPPR_RESOURCES__ . 'install.php?post=' . $id . '&action=install', __( 'Install', __EZFLIPPR_PLUGIN_SLUG__ ) );
					}
                }

				if (self::getPostMeta($id, 'status') < 90) {
					echo ' | ';
					printf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://ezflippr.com/book/' . self::getPostMeta( $id, 'uuid' ), __( 'Customize', __EZFLIPPR_PLUGIN_SLUG__ ) );
					if ( ! self::getPostMeta( $id, 'date_bought' ) ) {
						echo ' | ';
						printf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://ezflippr.com/book/' . self::getPostMeta( $id, 'uuid' ) . '#buy', __( 'Buy', __EZFLIPPR_PLUGIN_SLUG__ ) );
					}
				}
				break;
			default:
				break;
		}
	}

    /**
     * Activate the plugin
     */
    public function ezflippr_activate()
    {
	    flush_rewrite_rules();
    }

    /**
     * Deactivate the plugin
     */
    public function ezflippr_deactivate()
    {
        if (__EZFLIPPR_TEST__ || __EZFLIPPR_STAGING__) {
            define("WP_UNINSTALL_PLUGIN", true);
            include_once __EZFLIPPR_DIR__ . "/uninstall.php";
        }
    }

	public function ezflippr_single_template($single_template)
	{
        global $post;
        if ($post->post_type == 'ezflippr_flipbook') {
            // find out if a single template for this exists in the theme
            // if it does, let WP handle it
            // if not, include our copy
            if (!file_exists(get_stylesheet_directory() . "/single-ezflippr_flipbook.php")) {
                $single_template = __EZFLIPPR_DIR__ . "resources/templates/single-ezflippr_flipbook.php";
            }
        }
        return $single_template;
    }

	public function ezflippr_shortcode($atts, $content)
	{
        $styleAtts  = array(
            "width"     => "100%",
            "height"    => "500px",
        );
		$atts = shortcode_atts( array_merge($styleAtts, array(
			'id'=> '',
		)), $atts, 'flipbook' );

		if (empty($atts['id'])) return __('No ID', __EZFLIPPR_PLUGIN_SLUG__);

        $style      = "";
        foreach($styleAtts as $name=>$value) {
            $style  .= empty($atts[$name]) ? "" : ($name . ":" . str_replace("%", "%%", $atts[$name])) . ";";
        }

        $post = get_post($atts['id']);
		if (!$post) return 'nopost';

		$uuid       = $this->getPostMeta($atts['id'], 'uuid');
		$wpUploads  = wp_upload_dir();
		$dir        = $wpUploads['baseurl'] . DIRECTORY_SEPARATOR  . __EZFLIPPR_PLUGIN_SLUG__ . DIRECTORY_SEPARATOR . md5($uuid);

		wp_enqueue_script("swfobject");

		return sprintf('<div id="ezflippr-flipbook-%1$s" class="ezflippr-flipbook-container" style="' . $style . '">'.
		       '<div class="ezflippr-flipbook-content"><noscript>%4%s</noscript></div>'.
               '<script src="%2$s"></script>'.
               '<script src="%3$s"></script>'.
		       '</div>',
			md5($uuid),
			$dir . '/book/definition.js',
			__EZFLIPPR_URL__.'resources/static/assets/flipbook-wp.js',
			__('Please enable JavaScript', __EZFLIPPR_PLUGIN_SLUG__)
		);
	}

    /****************************************** Util functions ******************************************/

    /**
     * Writes to the file /tmp/log.log if DEBUG is on
     */
    public static function writeDebug($msg) {
        if (__EZFLIPPR_DEBUG__) file_put_contents(__EZFLIPPR_DIR__ . "/tmp/log.log", date('F j, Y H:i:s') . " - " . $msg."\n", FILE_APPEND);
    }

    /**
     * Custom wrapper for the get_option function
     * 
     * @return string
     */
    public static function getOption($field, $clean=false) {
        $val = get_option(__EZFLIPPR_PLUGIN_SLUG__ . '_' . $field);
        return $clean ? htmlspecialchars($val) : $val;
    }

    /**
     * Custom wrapper for the update_option function
     * 
     * @return mixed
     */
    public static function setOption($field, $value) {
        return update_option(__EZFLIPPR_PLUGIN_SLUG__ . '_' . $field, $value);
    }

    /**
     * Custom wrapper for the get_post_meta function
     * 
     * @return mixed
     */
    public static function getPostMeta($postID, $name, $single = true) {
        return get_post_meta($postID, __EZFLIPPR_PLUGIN_SLUG__ . '_' . $name, $single);
    }

    /**
     * Custom wrapper for the update_post_meta function
     */
    public static function setPostMeta($postID, $name, $value) {
        update_post_meta($postID, __EZFLIPPR_PLUGIN_SLUG__ . '_' . $name, $value);
    }

    /**
     * The API helper
     * 
     * @return array
     */
    static function callAPI($func, $params=null) {
        $method = NULL;
        $error  = -1;

        $url    = self::API_ENDPOINT . $func;
        if (function_exists('curl_exec')) {
            $method = "cURL";
            $conn = curl_init($url);
            curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($conn, CURLOPT_FRESH_CONNECT,  true);
            curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($conn, CURLOPT_TIMEOUT, self::API_TIMEOUT);
			if ($params !== null) {
				curl_setopt($conn, CURLOPT_POST, count($params));
				curl_setopt($conn, CURLOPT_POSTFIELDS, $params);
			}
            try{
                $response = curl_exec($conn);
                $error = curl_getinfo($conn, CURLINFO_HTTP_CODE);
            }catch(Exception $e) {
            }
            if (curl_errno($conn)) {
                self::writeDebug("curl_errno ".curl_error($conn));
            }
            curl_close($conn);
        }elseif (function_exists('file_get_contents')) {
            $method = "file_get_contents";
            $response = file_get_contents($url);
        }elseif (function_exists('fopen') && function_exists('stream_get_contents')) {
            $method = "fopen";
            $handle = fopen ($url, "r");
            $response = stream_get_contents($handle);
        }else{
            $response = false;
        }

        self::writeDebug("Calling ".$url. " with ".$method." response = ".$response);

        if ($response !== false) {
			$response   = json_decode($response);
			return array($error, $response);
        }else{
            return array(500, __('No communication methods supported. Install php_curl', __EZFLIPPR_PLUGIN_SLUG__));
        }
    }

    /****************************************** API functions ******************************************/

    /**
     * Send the access key on registered email
     */
	private function sendAccessKey($email)
	{
        list($http,$result) = self::callAPI(
            'send_accesskey',
            array(
                'email'=>$email,
	            'lang'=>substr(get_locale(),0,2),
            )
        );
        if ($http >= 400) {
            $this->error = $result['message'];
        }else{
            $this->notice   = __('Check your email, your access key has been sent!', __EZFLIPPR_PLUGIN_SLUG__);
        }
    }

    /**
     * Validate the access key provided by the user
     */
	private function verifyAccessKey($key = NULL)
	{
		$update     = true;
		$force      = false;

        if ($key) {
			$force  = true;
			$update = false;
		}else{
            $key    = self::getOption('accesskey');
        }

		if (!$key) return false;

		if (!$force) {
			$lastCheck  = self::getOption('accesskey-lastcheck');
			if ($lastCheck + 86400 > time()) return true;
		}

		list($http, $result) = self::callAPI(
			'verify_accesskey',
			array(
				'accesskey' => $key,
			)
		);
		if ($http < 400) {
            if ($force) self::setOption('accesskey', $key);
			if ($update) self::setOption('accesskey-lastcheck', time());
			return true;
		} else {
			if ($update) self::setOption('accesskey-lastcheck', 0);
			return false;
		}
	}

    /**
     * Get the list of flipbooks
     */
    public function refreshList()
    {
		list($http, $result) = self::callAPI(
			'get_flipbooks',
			array(
				'accesskey' => self::getOption('accesskey')
			)
		);

	    $modified = array();

		if ($http < 400) {
			$store = @get_object_vars(json_decode(self::getOption('books')));
			if (!is_array($store)) $store = array();
            self::writeDebug("books " . print_r($store,true));

			foreach ($result->flipbooks as $flipbook) {
				if (!isset($store[$flipbook->uuid])) {
					if ($flipbook->status >= 90) continue;

					// New! Add it.
					$post = array(
						'comment_status' => 'closed',
						'ping_status'    => 'open',
						'post_title'	 => preg_replace('/.pdf$/i','',$flipbook->filename),
						'post_status'    => 'draft',
						'post_type'      => 'ezflippr_flipbook',
					);
					$wp_error = false;
					$post_id = wp_insert_post( $post, $wp_error );
					$store[$flipbook->uuid] = $post_id;
				} else {
					$post_id = $store[$flipbook->uuid];

					// Check if installed and modified since install
					if (self::getPostMeta($post_id, 'installed')) {
						$time_local = strtotime(self::getPostMeta($post_id, 'installedDate'));
						$time_remote = max(strtotime($flipbook->date_create), strtotime($flipbook->date_modify));
						$time_remote = max($time_remote, strtotime($flipbook->date_bought));
						if ($time_remote > $time_local) {
							$fb = get_post($post_id);
							$fb->modified_reason = ((strtotime($flipbook->date_bought) == $time_remote) ? 'bought' : 'modified');
							$fb->time_local = $time_local;
							$fb->time_remote = $time_remote;
							$modified[] = $fb;
						}
					}
				}

				// Set metas
				foreach ($flipbook as $k=>$v) {
					self::setPostMeta($post_id, $k, $v);
				}

				if ((!self::getPostMeta($post_id, 'installed')) && ($flipbook->status >= 90)) {
					// Expired/erroneous and not installed? Delete.
					wp_delete_post($post_id, true);
				}
			}

			// Save store
			self::setOption('books', json_encode($store));
			self::setOption('lastupdate', time());
			self::setOption('modified', serialize($modified));
			return true;
		} else {
			return false;
		}
    }

    /**
     * Download a specific flipbook or uninstall it, depending on the option chosen
     */
    public static function installFlipbook($postID, $install)
    {
	    global $ezFlippr;

        $uuid       = self::getPostMeta($postID, 'uuid');

        $wpUploads  = wp_upload_dir();
        $dir        = $wpUploads['basedir'] . DIRECTORY_SEPARATOR  . __EZFLIPPR_PLUGIN_SLUG__ . DIRECTORY_SEPARATOR . md5($uuid);

        if ($install) {
	        set_time_limit(0);
            list($http,$result) = self::callAPI(
                'get_flipbook/' . $uuid,
                array(
                    'accesskey' => self::getOption('accesskey'),
                )
            );

            if ($http < 400) {
                try{
                    @mkdir($dir, 0755, true);
                    foreach ($result->files as $name=>$file) {
                        set_time_limit(2 * 60);
                        @mkdir(dirname($dir . DIRECTORY_SEPARATOR . $name));
                        file_put_contents($dir . DIRECTORY_SEPARATOR . $name, file_get_contents($file));
                    }
	                $time = max(strtotime($result->flipbook->date_create), strtotime($result->flipbook->date_modify));
	                $time = max($time, strtotime($result->flipbook->date_bought));
                    self::setPostMeta($postID, 'installed', 1);
	                self::setPostMeta($postID, 'installedDate', gmdate('r', $time));
                    $post   = get_post($postID);
                    $post->post_status  = "publish";
                    wp_update_post($post);
                }catch(Exception $e) {
                    die("Couldn't install: ".$e->getMessage());
                }
            }
        }else{
            Util::cleanDir($dir);
            self::setPostMeta($postID, 'installed', 0);
            $post   = get_post($postID);
            $post->post_status  = "draft";
            wp_update_post($post);
        }

	    $ezFlippr->refreshList();
    }

    /****************************************** UI functions ******************************************/

    private function getLastUpdate()
    {
        $time   = self::getOption('lastupdate');
        if ($time) {
            if (!is_numeric($time)) {
                $time = strtotime($time);
            }

            $last = sprintf(__('%d years ago','ezflippr'), $time/31536000);

            $time = time()-$time;
            if ($time < 31536000) $last = sprintf(__('%d months ago','ezflippr'), $time/2592000);
            if ($time < 9072000) $last = sprintf(__('%d weeks ago','ezflippr'), $time/604800);
            if ($time < 1209600) $last = sprintf(__('%d days ago','ezflippr'), $time/86400);
            if ($time < 129600) $last = sprintf(__('%d hours ago','ezflippr'), $time/3600);
            if ($time < 5400) $last = sprintf(__('%d minutes ago','ezflippr'), $time/60);
            if ($time < 90) $last = __('moments ago','ezflippr');
        }else{
            $last   = __('Never', __EZFLIPPR_PLUGIN_SLUG__);
        }
        return sprintf (__('Your flipbooks list was last refreshed %1$s.', __EZFLIPPR_PLUGIN_SLUG__), $last);
    }

    private function saveSettings()
    {
        if (isset($_POST['ezflippr-field-email'])) {
            self::setOption("email", $_POST['ezflippr-field-email']);
        }
    }

}

$ezFlippr = new ezFlippr();
