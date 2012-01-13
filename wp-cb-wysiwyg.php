<?php
/*
Plugin Name: WPCB Wysiwyg module plugin 
Plugin URI:  http://positivesum.ca/
Description: WP plugin to Add The Wordpress Wysiwyg CB Module
Version: 0.1
Author: Alexander Yachmenev
Author URI: http://www.odesk.com/users/~~94ca72c849152a57
*/
if ( !class_exists( 'wp_cb_wysiwyg' ) ) {
	class wp_cb_wysiwyg {
		public $html;		
		/**
		 * Initializes plugin variables and sets up wordpress hooks/actions.
		 *
		 * @return void
		 */
		function __construct( ) {
			$this->pluginDir		= basename(dirname(__FILE__));
			$this->pluginPath		= WP_PLUGIN_DIR . '/' . $this->pluginDir;
			$this->pluginUrl 		= WP_PLUGIN_URL.'/'.$this->pluginDir;	
			add_action('admin_init', array(&$this, 'wp_cb_wysiwyg_admin_init'));
			add_action('cfct-modules-loaded',  array(&$this, 'wp_cb_wysiwyg_modules_loaded'));				
		}

		function wp_cb_wysiwyg_modules_loaded() {
			require_once($this->pluginPath . "/wysiwyg.php");
		}	
		
		function wp_cb_wysiwyg_admin_init() {
			wp_enqueue_script('wp-cb-wysiwyg', $this->pluginUrl . '/js/wp-cb-wysiwyg.js', array('jquery'), '1.0'); //
			add_action('wp_ajax_wp_cb_wysiwyg', array(&$this, "wp_cb_wysiwyg_ajax"));	
			add_action('pre_post_update', array(&$this, 'wp_cb_wysiwyg_pre_post_update'));			
		}

		function wp_cb_wysiwyg_pre_post_update( $post_id ) {
			$post = get_post($post_id);
			if ($post->post_type == "page") {
				if (isset($_REQUEST["module_id"])) {
					$module_id = $_REQUEST["module_id"];
					$post_data = get_post_meta($post_id, CFCT_BUILD_POSTMETA, true);
					if (isset($post_data['data']['modules'])) {
						$post_data['data']['modules'][$module_id]['cfct-wysiwyg-content'] = $post->post_content;
						update_post_meta($post_id, CFCT_BUILD_POSTMETA, $post_data);
					}
				}
			}
		}
		
		function wp_cb_wysiwyg_ajax() {
			$response = array();
			// Allowed actions: add, update, delete
			$action = isset( $_REQUEST['operation'] ) ? $_REQUEST['operation'] : 'get';
			switch ( $action ) {
				case 'get':	
				if (is_admin()) {
					$post_id = intval($_REQUEST['post_id']);
					$post_data = get_post_meta($post_id, CFCT_BUILD_POSTMETA, true);
					$data = $post_data['data']['modules'][$_REQUEST['module_id']]['cfct-wysiwyg-content'];
					if ($data == '') {
						foreach ($post_data['data']['modules'] as $module) {
							if ($module['module_type'] == 'cfct-rich-text-module') {
								$data = $module['cfct-rich-text-content'];
								break;
							}
						}
					}
					$result = $data;
				}				
				break;
			}
			$response['result'] = $result;
			echo (json_encode($response));
			die();				
		}
		
	}
	$wp_cb_wysiwyg = new wp_cb_wysiwyg();	
}
