<?php
/*
Plugin Name: CloudFlare Rocket Loader Ignore
Plugin URI: http://www.jimmyscode.com/wordpress/cloudflare-rocket-loader-ignore/
Description: Tell CloudFlare Rocket Loader to ignore certain scripts.
Version: 0.0.6
Author: Jimmy Pe&ntilde;a
Author URI: http://www.jimmyscode.com/
License: GPLv2 or later
*/
if (!defined('CFRLI_PLUGIN_NAME')) {
	// plugin constants
	define('CFRLI_PLUGIN_NAME', 'CloudFlare Rocket Loader Ignore');
	define('CFRLI_VERSION', '0.0.6');
	define('CFRLI_SLUG', 'cloudflare-rocket-loader-ignore');
	define('CFRLI_LOCAL', 'cfrli');
	define('CFRLI_OPTION', 'cfrli');
	define('CFRLI_OPTIONS_NAME', 'cfrli_options');
	define('CFRLI_PERMISSIONS_LEVEL', 'manage_options');
	define('CFRLI_PATH', plugin_basename(dirname(__FILE__)));
	/* default values */
	define('CFRLI_DEFAULT_ENABLED', true);
	define('CFRLI_DEFAULT_NAMES', '');
	define('CFRLI_DEFAULT_SENTIMENT', false);
	/* option array member names */
	define('CFRLI_DEFAULT_ENABLED_NAME', 'enabled');
	define('CFRLI_DEFAULT_NAMES_NAME', 'fnames');
	define('CFRLI_DEFAULT_SENTIMENT_NAME', 'sentiment');
}
// oh no you don't
if (!defined('ABSPATH')) {
	wp_die(__('Do not access this file directly.', cfrli_get_local()));
}



/*


// "borrowed" from https://wordpress.org/plugins/html-cleanup/
if (!is_admin()) {
	ob_start("cfrli_inline_and_combined_js_tagging");
}
function cfrli_inline_and_combined_js_tagging($buffer) {
	$options = cfrli_getpluginoptions();
	$enabled = $options[CFRLI_DEFAULT_ENABLED_NAME];

	if ($enabled) {
		$scripttags = array(
			'<script type="text/javascript" src="',
			'<script type=\'text/javascript\' src="'
		);
		$listoffiles = explode("\n", $options[CFRLI_DEFAULT_NAMES_NAME]);
		
		if (!empty($listoffiles)) { // proceed only if there are filenames to be processed
			// array_filter to remove empty array elements
			$listoffiles = array_filter(array_map('sanitize_text_field', $listoffiles));
			$sentiment = $options[CFRLI_DEFAULT_SENTIMENT_NAME];
			
			if ($sentiment) { // we only want the NON-included scripts to be "data-cfasync=false"

				// compile a list of all script tags in HTML source
				$js_tags_array = array();
				
				foreach ($scripttags as $scripttag) {
					preg_match('(' . $scripttag . '.*\.js\"></script>)', $buffer, $matches, PREG_OFFSET_CAPTURE);
					if (!empty($matches)) { // match was found
						$buffer .= '<!-- ' . var_export($matches, true) . ' -->';
						foreach ($matches as $match) { // push onto main array
							$js_tags_array[] = $match[0];
						}
					}
				}
//				$buffer .= '<!-- ' . var_export($js_tags_array, true) . ' -->';

				if (!empty($js_tags_array)) {
					// match each filename in plugin settings textbox with each array member
					for ($i = 0; $i <= count($listoffiles); $i++) {
						for ($j = 0; $j <= count($js_tags_array); $j++) {
							if (strpos($js_tags_array[$j], $listoffiles[$i]) !== false) {
								$matchfound = true;
								$matchpos = $j;
							}
						}
						if (!$matchfound) {
							$updatedfilename = str_replace('type="text/javascript"', 'data-cfasync="false"', $js_tags_array[$matchpos]);
							$buffer = str_replace($js_tags_array[$matchpos], $updatedfilename, $buffer);
						}
					}
				}
				*/
/*
			} else { // only put "false" for matching filenames
				foreach ($listoffiles as $lofname) {
					foreach ($scripttags as $scripttag) {
						preg_match('(' . $scripttag . '.*' . $lofname . '.*\.js\"></script>)', $buffer, $matches, PREG_OFFSET_CAPTURE);
						if (!empty($matches)) { // match was found
							// add data-cfasync attrib to matching filenames
							foreach ($matches as $match) {
								$updatedfilename = str_replace('type="text/javascript"', 'data-cfasync="false"', $match[0]);
								$buffer = str_replace($match[0], $updatedfilename, $buffer);
							}
						}
					}
				}
			}
		}
	}
	return $buffer;
}
register_shutdown_function('cfrli_flush_buffer');
function cfrli_flush_buffer() {
	ob_end_flush();
}

*/







	
	// localization to allow for translations
	add_action('init', 'cfrli_translation_file');
	function cfrli_translation_file() {
		$plugin_path = cfrli_get_path() . '/translations';
		load_plugin_textdomain(cfrli_get_local(), '', $plugin_path);
	}
	// tell WP that we are going to use new options
	// also, register the admin CSS file for later inclusion
	add_action('admin_init', 'cfrli_options_init');
	function cfrli_options_init() {
		register_setting(CFRLI_OPTIONS_NAME, cfrli_get_option(), 'cfrli_validation');
		register_cfrli_admin_style();
	}
	// validation function
	function cfrli_validation($input) {
		// validate all form fields
		if (!empty($input)) {
			$input[CFRLI_DEFAULT_ENABLED_NAME] = (bool)$input[CFRLI_DEFAULT_ENABLED_NAME];
			// $input[CFRLI_DEFAULT_NAMES_NAME] = $input[CFRLI_DEFAULT_NAMES_NAME]);
			$input[CFRLI_DEFAULT_SENTIMENT_NAME] = (bool)$input[CFRLI_DEFAULT_SENTIMENT_NAME];
		}
		return $input;
	}
	// add Settings sub-menu
	add_action('admin_menu', 'cfrli_plugin_menu');
	function cfrli_plugin_menu() {
		add_options_page(CFRLI_PLUGIN_NAME, CFRLI_PLUGIN_NAME, CFRLI_PERMISSIONS_LEVEL, cfrli_get_slug(), 'cfrli_page');
	}
	// plugin settings page
	// http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
	function cfrli_page() {
		// check perms
		if (!current_user_can(CFRLI_PERMISSIONS_LEVEL)) {
			wp_die(__('You do not have sufficient permission to access this page', cfrli_get_local()));
		}
		?>
		<div class="wrap">
			<h2 id="plugintitle"><img src="<?php echo cfrli_getimagefilename('redcloud.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php echo CFRLI_PLUGIN_NAME; _e(' by ', cfrli_get_local()); ?><a href="http://www.jimmyscode.com/">Jimmy Pe&ntilde;a</a></h2>
			<div><?php _e('You are running plugin version', cfrli_get_local()); ?> <strong><?php echo CFRLI_VERSION; ?></strong>.</div>

			<?php /* http://code.tutsplus.com/tutorials/the-complete-guide-to-the-wordpress-settings-api-part-5-tabbed-navigation-for-your-settings-page--wp-24971 */ ?>
			<?php $active_tab = (isset($_GET['tab']) ? $_GET['tab'] : 'settings'); ?>
			<h2 class="nav-tab-wrapper">
			  <a href="?page=<?php echo cfrli_get_slug(); ?>&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', cfrli_get_local()); ?></a>
				<a href="?page=<?php echo cfrli_get_slug(); ?>&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>"><?php _e('Support', cfrli_get_local()); ?></a>
			</h2>
			
			<form method="post" action="options.php">
				<?php settings_fields(CFRLI_OPTIONS_NAME); ?>
				<?php $options = cfrli_getpluginoptions(); ?>
				<?php update_option(cfrli_get_option(), $options); ?>
				<?php if ($active_tab == 'settings') { ?>
					<h3 id="settings"><img src="<?php echo cfrli_getimagefilename('settings.png'); ?>" title="" alt="" height="61" width="64" align="absmiddle" /> <?php _e('Plugin Settings', cfrli_get_local()); ?></h3>
					<table class="form-table" id="theme-options-wrap">
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Is plugin enabled? Uncheck this to turn it off temporarily.', cfrli_get_local()); ?>" for="<?php echo cfrli_get_option(); ?>[<?php echo CFRLI_DEFAULT_ENABLED_NAME; ?>]"><?php _e('Plugin enabled?', cfrli_get_local()); ?></label></strong></th>
							<td><input type="checkbox" id="<?php echo cfrli_get_option(); ?>[<?php echo CFRLI_DEFAULT_ENABLED_NAME; ?>]" name="<?php echo cfrli_get_option(); ?>[<?php echo CFRLI_DEFAULT_ENABLED_NAME; ?>]" value="1" <?php checked('1', cfrli_checkifset(CFRLI_DEFAULT_ENABLED_NAME, CFRLI_DEFAULT_ENABLED, $options)); ?> /></td>
						</tr>
						<?php cfrli_explanationrow(__('Is plugin enabled? Uncheck this to turn it off temporarily.', cfrli_get_local())); ?>
						<?php cfrli_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Enter script filenames, one per line', cfrli_get_local()); ?>" for="<?php echo cfrli_get_option(); ?>[<?php echo CFRLI_DEFAULT_NAMES_NAME; ?>]"><?php _e('Enter script filenames, one per line', cfrli_get_local()); ?></label></strong></th>
							<td><textarea rows="12" cols="75" id="<?php echo cfrli_get_option(); ?>[<?php echo CFRLI_DEFAULT_NAMES_NAME; ?>]" name="<?php echo cfrli_get_option(); ?>[<?php echo CFRLI_DEFAULT_NAMES_NAME; ?>]"><?php echo cfrli_checkifset(CFRLI_DEFAULT_NAMES_NAME, CFRLI_DEFAULT_NAMES, $options); ?></textarea></td>
						</tr>
						<?php cfrli_explanationrow(__('Enter the filenames of any .js files you want CloudFlare\'s Rocket Loader to ignore. <strong>One filename per line.</strong> Include the .js file extension for specific files, or exclude for filename matching. Ex: jquery-migrate.js to exclude only that file, or \'jquery\' to exclude any files with the word \'jquery\' in them.', cfrli_get_local())); ?>
						<?php cfrli_getlinebreak(); ?>
						<tr valign="top"><th scope="row"><strong><label title="<?php _e('Switch plugin sentiment from false to true', cfrli_get_local()); ?>" for="<?php echo cfrli_get_option(); ?>[<?php echo CFRLI_DEFAULT_SENTIMENT_NAME; ?>]"><?php _e('Switch plugin sentiment from false to true', cfrli_get_local()); ?></label></strong></th>
							<td><input type="checkbox" id="<?php echo cfrli_get_option(); ?>[<?php echo CFRLI_DEFAULT_SENTIMENT_NAME; ?>]" name="<?php echo cfrli_get_option(); ?>[<?php echo CFRLI_DEFAULT_SENTIMENT_NAME; ?>]" value="1" <?php checked('1', cfrli_checkifset(CFRLI_DEFAULT_SENTIMENT_NAME, CFRLI_DEFAULT_SENTIMENT, $options)); ?> /></td>
						</tr>
						<?php cfrli_explanationrow(__('Check this box and the <em>data-cfasync</em> attribute will only be added for NON-MATCHING JavaScript filenames.', cfrli_get_local())); ?>
						<?php cfrli_explanationrow(__('Ex: add \'jquery.js\' and check this box, then all other .js files (<strong>except jquery.js</strong>) will be ignored by Rocket Loader.', cfrli_get_local())); ?>
					</table>
					<?php submit_button(); ?>
				<?php } else { ?>
					<h3 id="support"><img src="<?php echo cfrli_getimagefilename('support.png'); ?>" title="" alt="" height="64" width="64" align="absmiddle" /> <?php _e('Support', cfrli_get_local()); ?></h3>
					<div class="support">
						<?php echo cfrli_getsupportinfo(cfrli_get_slug(), cfrli_get_local()); ?>
						<small><?php _e('Disclaimer: This plugin is not affiliated with or endorsed by CloudFlare.', cfrli_get_local()); ?></small>
					</div>
				<?php } ?>
			</form>
		</div>
		<?php }

	// main function and filter
	// based on http://wp-dreams.com/articles/2014/03/cloudflare-rocket-loader-for-wordpress/
	if (!is_admin()) {
		add_filter('clean_url', 'cfrli_add_cfasync', 11, 1);
		add_action('wp_print_scripts', 'cfrli_buffer_output', 1);
		add_action('print_head_scripts', 'cfrli_convert_head_links', 1);
		add_action('print_footer_scripts', 'cfrli_convert_footer_links', 1);
	}
	function cfrli_add_cfasync($url) {
		$options = cfrli_getpluginoptions();
		$enabled = $options[CFRLI_DEFAULT_ENABLED_NAME];
		
		if ($enabled) {
			$listoffiles = explode("\n", $options[CFRLI_DEFAULT_NAMES_NAME]);
			if (!empty($listoffiles)) { // proceed only if there are filenames to be processed
				if (strpos($url, '.js') !== false) { // its a javascript file being served
					// array_filter to remove empty array elements
					$listoffiles = array_filter(array_map('sanitize_text_field', $listoffiles)); // Chapter 6 Pro WordPress Plugin Development, run each filename through sanitize function
					$sentiment = $options[CFRLI_DEFAULT_SENTIMENT_NAME];
					if (strpos("?", basename($url)) !== false) { // check for query parameters and remove them -- is this even necessary since strpos will find a match anyway ??
						$fname = explode("?", basename($url));
						$thisfilename = $fname[0];
					} else {
						$thisfilename = basename($url);
					}
					if ($sentiment) { // we only want the NON-included scripts to be "data-cfasync=false"
						for ($i = 0; $i <= count($listoffiles); $i++) {
							if (strpos($thisfilename, $listoffiles[$i]) !== false) {
								$matchfound = true;
							}
						}
						if (!$matchfound) {
							return "{rocket-ignore}$url";
						}
					} else { // only put "false" for matching filenames
						foreach ($listoffiles as $lofname) {
							if (strpos($thisfilename, $lofname) !== false) {
								return "{rocket-ignore}$url";
							}
						}
					}
				}
			}
		}
		return $url;
	}
	function cfrli_buffer_output() {
		ob_start();
	}
	function cfrli_convert_head_links() {
		$options = cfrli_getpluginoptions();
		$enabled = $options[CFRLI_DEFAULT_ENABLED_NAME];

		$script_out = ob_get_clean();

		if ($enabled) {
			$script_out = str_replace("type='text/javascript' src='{rocket-ignore}", 
				'data-cfasync="false"' . " src='", $script_out);
		}
		print $script_out;
	}
	function cfrli_convert_footer_links() {
		$options = cfrli_getpluginoptions();
		$enabled = $options[CFRLI_DEFAULT_ENABLED_NAME];

		$script_out = ob_get_clean();

		if ($enabled) {
			$script_out = str_replace("type='text/javascript' src='{rocket-ignore}", 
				'data-cfasync="false"' . " src='", $script_out);
		}
		print $script_out;
	}
	
	// show admin messages to plugin user
	add_action('admin_notices', 'cfrli_showAdminMessages');
	function cfrli_showAdminMessages() {
		// http://wptheming.com/2011/08/admin-notices-in-wordpress/
		global $pagenow;
		if (current_user_can(CFRLI_PERMISSIONS_LEVEL)) { // user has privilege
			if ($pagenow == 'options-general.php') { // we are on Settings menu
				if (isset($_GET['page'])) {
					if ($_GET['page'] == cfrli_get_slug()) { // we are on this plugin's settings page
						$options = cfrli_getpluginoptions();
						if (!empty($options)) {
							$enabled = (bool)$options[CFRLI_DEFAULT_ENABLED_NAME];
							if (!$enabled) {
								echo '<div id="message" class="error">' . CFRLI_PLUGIN_NAME . ' ' . __('is currently disabled.', cfrli_get_local()) . '</div>';
							}
						}
					}
				}
			} // end page check
		} // end privilege check
	} // end admin msgs function
	// enqueue admin CSS if we are on the plugin options page
	add_action('admin_head', 'insert_cfrli_admin_css');
	function insert_cfrli_admin_css() {
		global $pagenow;
		if (current_user_can(CFRLI_PERMISSIONS_LEVEL)) { // user has privilege
			if ($pagenow == 'options-general.php') { // we are on Settings menu
				if (isset($_GET['page'])) {
					if ($_GET['page'] == cfrli_get_slug()) { // we are on this plugin's settings page
						cfrli_admin_styles();
					}
				}
			}
		}
	}
	// add helpful links to plugin page next to plugin name
	// http://bavotasan.com/2009/a-settings-link-for-your-wordpress-plugins/
	// http://wpengineer.com/1295/meta-links-for-wordpress-plugins/
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'cfrli_plugin_settings_link');
	add_filter('plugin_row_meta', 'cfrli_meta_links', 10, 2);
	
	function cfrli_plugin_settings_link($links) {
		return cfrli_settingslink($links, cfrli_get_slug(), cfrli_get_local());
	}
	function cfrli_meta_links($links, $file) {
		if ($file == plugin_basename(__FILE__)) {
			$links = array_merge($links,
			array(
				sprintf(__('<a href="http://wordpress.org/support/plugin/%s">Support</a>', cfrli_get_local()), cfrli_get_slug()),
				sprintf(__('<a href="http://wordpress.org/extend/plugins/%s/">Documentation</a>', cfrli_get_local()), cfrli_get_slug()),
				sprintf(__('<a href="http://wordpress.org/plugins/%s/faq/">FAQ</a>', cfrli_get_local()), cfrli_get_slug())
			));
		}
		return $links;	
	}
	// enqueue/register the admin CSS file
	function cfrli_admin_styles() {
		wp_enqueue_style('cfrli_admin_style');
	}
	function register_cfrli_admin_style() {
		wp_register_style('cfrli_admin_style',
			plugins_url(cfrli_get_path() . '/css/admin.css'),
			array(),
			CFRLI_VERSION . "_" . date('njYHis', filemtime(dirname(__FILE__) . '/css/admin.css')),
			'all');
	}
	// when plugin is activated, create options array and populate with defaults
	register_activation_hook(__FILE__, 'cfrli_activate');
	function cfrli_activate() {
		$options = cfrli_getpluginoptions();
		update_option(cfrli_get_option(), $options);
		
		// delete option when plugin is uninstalled
		register_uninstall_hook(__FILE__, 'uninstall_cfrli_plugin');
	}
	function uninstall_cfrli_plugin() {
		delete_option(cfrli_get_option());
	}
		
	// generic function that returns plugin options from DB
	// if option does not exist, returns plugin defaults
	function cfrli_getpluginoptions() {
		return get_option(cfrli_get_option(), 
			array(
				CFRLI_DEFAULT_ENABLED_NAME => CFRLI_DEFAULT_ENABLED, 
				CFRLI_DEFAULT_NAMES_NAME => CFRLI_DEFAULT_NAMES,
				CFRLI_DEFAULT_SENTIMENT_NAME => CFRLI_DEFAULT_SENTIMENT
			));
	}
	
	// encapsulate these and call them throughout the plugin instead of hardcoding the constants everywhere
	function cfrli_get_slug() { return CFRLI_SLUG; }
	function cfrli_get_local() { return CFRLI_LOCAL; }
	function cfrli_get_option() { return CFRLI_OPTION; }
	function cfrli_get_path() { return CFRLI_PATH; }

	function cfrli_settingslink($linklist, $slugname = '', $localname = '') {
		$settings_link = sprintf( __('<a href="options-general.php?page=%s">Settings</a>', $localname), $slugname);
		array_unshift($linklist, $settings_link);
		return $linklist;
	}
	function cfrli_getsupportinfo($slugname = '', $localname = '') {
		$output = __('Do you need help with this plugin? Check out the following resources:', $localname);
		$output .= '<ol>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/extend/plugins/%s/">Documentation</a>', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/plugins/%s/faq/">FAQ</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/support/plugin/%s">Support Forum</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://www.jimmyscode.com/wordpress/%s">Plugin Homepage / Demo</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/extend/plugins/%s/developers/">Development</a><br />', $localname), $slugname) . '</li>';
		$output .= '<li>' . sprintf( __('<a href="http://wordpress.org/plugins/%s/changelog/">Changelog</a><br />', $localname), $slugname) . '</li>';
		$output .= '</ol>';
		
		$output .= sprintf( __('If you like this plugin, please <a href="http://wordpress.org/support/view/plugin-reviews/%s/">rate it on WordPress.org</a>', $localname), $slugname);
		$output .= sprintf( __(' and click the <a href="http://wordpress.org/plugins/%s/#compatibility">Works</a> button. ', $localname), $slugname);
		$output .= '<br /><br /><br />';
		$output .= __('Your donations encourage further development and support. ', $localname);
		$output .= '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7EX9NB9TLFHVW"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate with PayPal" title="Support this plugin" width="92" height="26" /></a>';
		$output .= '<br /><br />';
		return $output;		
	}
	function cfrli_checkifset($optionname, $optiondefault, $optionsarr) {
		return (isset($optionsarr[$optionname]) ? $optionsarr[$optionname] : $optiondefault);
	}
	function cfrli_getlinebreak() {
	  echo '<tr valign="top"><td colspan="2"></td></tr>';
	}
	function cfrli_explanationrow($msg = '') {
		echo '<tr valign="top"><td></td><td><em>' . $msg . '</em></td></tr>';
	}
	function cfrli_getimagefilename($fname = '') {
		return plugins_url(cfrli_get_path() . '/images/' . $fname);
	}
?>