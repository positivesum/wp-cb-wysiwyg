<?php
if (!class_exists('cfct_module_wysiwyg')) {
	class cfct_module_wysiwyg extends cfct_build_module {
		/**
		 * Set up the module
		 */
		 
		public function __construct() {
			$this->pluginDir		= basename(dirname(__FILE__));
			$this->pluginPath		= WP_PLUGIN_DIR . '/' . $this->pluginDir;
			$this->pluginUrl 		= WP_PLUGIN_URL.'/'.$this->pluginDir;	
			
			$opts = array(
				'url' => $this->pluginUrl, 
				'view' => 'wp-cb-wysiwyg/wysiwyg-view.php',				
				'description' => __('Provides Wordpress Wysiwyg CB Module.', 'carrington-build'),
				'icon' => 'wp-cb-wysiwyg/wysiwyg-icon.png'
			);
			
			// use if this module is to have no user configurable options
			// Will suppress the module edit button in the admin module display
			# $this->editable = false 
			
			parent::__construct('cfct-wysiwyg', __('WP Wysiwyg', 'carrington-build'), $opts);
		}

		/**
		 * Display the module content in the Post-Content
		 * 
		 * @param array $data - saved module data
		 * @return array string HTML
		 */
		public function display($data) {
			global $cfct_build;			
		
			$cfct_build->loaded_modules[$this->basename] = $this->pluginPath;
			$cfct_build->module_paths[$this->basename] = $this->pluginPath;
			$cfct_build->module_urls[$this->basename] = $this->pluginUrl;

			$text = do_shortcode($data[$this->get_field_id('content')]);
			return $this->load_view($data, compact('text'));			
		}
	
		/**
		 * Build the admin form
		 * 
		 * @param array $data - saved module data
		 * @return string HTML
		 */
		public function admin_form($data) {
			$ret = 'To enter content into this WYSIWYG:<br/>
					1. Give this module a name <input onchange="cfct_module_wysiwyg_change();" type="text" name="'.$this->get_field_name('title').'" id="'.$this->get_field_id('title').'" value="'.(!empty($data[$this->get_field_name('title')]) ? esc_html($data[$this->get_field_name('title')]) : '').'" style="width: 150px;" /><br/>
					2. Click Save<br/>
					3. Update this page<br/>
					4. Edit module content via WYSIWYG tab named <span style="font-weight:bold;" id="cfct-module-wysiwyg-name">'.(!empty($data[$this->get_field_name('title')]) ? esc_html($data[$this->get_field_name('title')]) : '').'</span>';
			return $ret;
		}

		/**
		 * Return a textual representation of this module.
		 *
		 * @param array $data - saved module data
		 * @return string text
		 */
		public function text($data) {
			$title = null;
			if (!empty($data[$this->get_field_name('title')])) {
				$title = esc_html($data[$this->get_field_name('title')]);
			}
			return $title;
		}

		public function admin_js() {
			$js = '
				cfct_module_wysiwyg_change = function() {
					var name = jQuery("#'.$this->get_field_id('title').'").val();
					if (name != "") {
						jQuery("#cfct-module-wysiwyg-name").text(name);
					}
				}
			';
			return $js;
		}		
		
		/**
		 * Add custom css to the post/page admin
		 * OPTIONAL: omit this method if you're not using it
		 *
		 * @return string CSS
		 */
		public function admin_css() {
			return '';
		}		
		
	}
	
	// register the module with Carrington Build
	cfct_build_register_module('cfct-module-wysiwyg', 'cfct_module_wysiwyg');
}

?>