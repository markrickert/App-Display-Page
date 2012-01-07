<?php

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'adp_add_defaults');
register_uninstall_hook(__FILE__, 'adp_delete_plugin_options');
add_action('admin_init', 'adp_init' );
add_action('admin_menu', 'adp_add_options_page');
add_filter( 'plugin_action_links', 'adp_plugin_action_links', 10, 2 );

// Delete options table entries ONLY when plugin deactivated AND deleted
function adp_delete_plugin_options() {
	delete_option('adp_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'adp_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function adp_add_defaults() {
	$tmp = get_option('adp_options');
    if(!$tmp || !is_array($tmp)) {
		delete_option('adp_options');
		$arr = array(
						"icon_size" => "175",
						"ss_size" => "120",
						"cache_time_select_box" => (24*60*60),
						"cache_images_locally" => "1"
		);
		update_option('adp_options', $arr);
	}
}


// Init plugin options to white list our options
function adp_init(){
	$settings = get_option('adp_options');
	if(!$settings) adp_add_defaults();

	register_setting( 'adp_plugin_options', 'adp_options', 'adp_validate_options' );
}

// Add menu page
function adp_add_options_page() {
	add_options_page('App Display Page Options', 'App Display Page', 'manage_options', __FILE__, 'adp_render_form');
}

// Render the Plugin options form
function adp_render_form() {
	?>
	<div class="wrap">
		
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>App Display Page Options</h2>

		<form method="post" action="options.php">
			<?php settings_fields('adp_plugin_options'); ?>
			<?php $options = get_option('adp_options'); ?>

			<table class="form-table">

				<tr>
					<th scope="row">Icon Width/Height:<br /><small>(in px.)</small></th>
					<td>
						<input type="text" size="10" name="adp_options[icon_size]" value="<?php echo $options['icon_size']; ?>" />
					</td>
				</tr>

				<tr>
					<th scope="row">Screenshot Width:<br /><small>(in px. Height is automatic.)</small></th>
					<td>
						<input type="text" size="10" name="adp_options[ss_size]" value="<?php echo $options['ss_size']; ?>" />
					</td>
				</tr>

				<tr>
					<th scope="row">Data cache time:</th>
					<td>
						<select name='adp_options[cache_time_select_box]'>
						
							<?php $cache_intervals = array(
														'Don\'t cache'=>0,
														'1 minute'=>1*60,
														'5 minutes'=>5*60,
														'10 minutes'=>10*60,
														'30 minutes'=>30*60,
														'1 hour'=>1*60*60,
														'6 hours'=>6*60*60,
														'12 hours'=>12*60*60,
														'24 hours'=>24*60*60,
														'1 Week'=>24*60*60*7,
														'1 Month'=>24*60*60*7*30,
														'1 Year'=>24*60*60*7*30*365
													);
							
							foreach ($cache_intervals as $key => $value) {
								echo '<option value="' . $value . '" ' . selected($value, $options['cache_time_select_box']) . '>' . $key . '</option>';
							}
							
							?>
							
						</select>
						<span style="color:#666666;margin-left:2px;">This option determines how long before the plugin requests new data from Apple's servers.</span>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Cache images locally:</th>
					<td>
						<!-- First checkbox button -->
						<label><input name="adp_options[cache_images_locally]" type="checkbox" value="1" <?php if (isset($options['cache_images_locally'])) { checked('1', $options['cache_images_locally']); } ?> /> Yes</label><br />
						<span style="color:#666666;margin-left:2px;">Load icons, screenshots, etc. locally instead of using Apple's CDN server. Your wp-content/uploads/ directory MUST be writeable for this option to have any effect.</span>
					</td>
				</tr>

			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function adp_validate_options($input) {
	return $input;
}

// Display a Settings link on the main Plugins page
function adp_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$adp_links = '<a href="'.get_admin_url().'options-general.php?page=plugin-options-starter-kit/plugin-options-starter-kit.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $adp_links );
	}

	return $links;
}