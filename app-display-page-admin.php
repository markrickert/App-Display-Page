<?php

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'adp_add_defaults');
register_uninstall_hook(__FILE__, 'adp_delete_plugin_options');
add_action('admin_init', 'adp_init' );
add_action('admin_menu', 'adp_add_options_page');

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
						"cache_images_locally" => "1",
						"linkshare_partner_id" => ""
		);
		update_option('adp_options', $arr);
	}
}


// Init plugin options to white list our options
function adp_init(){
	$settings = get_option('adp_options');
	if(!$settings) adp_add_defaults();

	//Added store country option
	if(!ios_app_setting('store_country'))
		ios_app_set_setting('store_country', 'us');

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
					<th scope="row">Store Country:</th>
					<td>
						<select name='adp_options[store_country]'>

							<?php $stores = array(
											"AL" => "Albania",
											"DZ" => "Algeria",
											"AO" => "Angola",
											"AI" => "Anguilla",
											"AG" => "Antigua and Barbuda",
											"AR" => "Argentina",
											"AM" => "Armenia",
											"AU" => "Australia",
											"AT" => "Austria",
											"AZ" => "Azerbaijan",
											"BS" => "Bahamas",
											"BH" => "Bahrain",
											"BB" => "Barbados",
											"BY" => "Belarus",
											"BE" => "Belgium",
											"BZ" => "Belize",
											"BJ" => "Benin",
											"BM" => "Bermuda",
											"BT" => "Bhutan",
											"BO" => "Bolivia",
											"BW" => "Botswana",
											"BR" => "Brazil",
											"BN" => "Brunei Darussalam",
											"BG" => "Bulgaria",
											"BF" => "Burkina Faso",
											"KH" => "Cambodia",
											"CA" => "Canada",
											"CV" => "Cape Verde",
											"KY" => "Cayman Islands",
											"TD" => "Chad",
											"CL" => "Chile",
											"CN" => "China",
											"CO" => "Colombia",
											"CG" => "Congo, Republic of the",
											"CR" => "Costa Rica",
											"HR" => "Croatia",
											"CY" => "Cyprus",
											"CZ" => "Czech Republic",
											"DK" => "Denmark",
											"DM" => "Dominica",
											"DO" => "Dominican Republic",
											"EC" => "Ecuador",
											"EG" => "Egypt",
											"SV" => "El Salvador",
											"EE" => "Estonia",
											"FJ" => "Fiji",
											"FI" => "Finland",
											"FR" => "France",
											"GM" => "Gambia",
											"DE" => "Germany",
											"GH" => "Ghana",
											"GR" => "Greece",
											"GD" => "Grenada",
											"GT" => "Guatemala",
											"GW" => "Guinea-Bissau",
											"GY" => "Guyana",
											"HN" => "Honduras",
											"HK" => "Hong Kong",
											"HU" => "Hungary",
											"IS" => "Iceland",
											"IN" => "India",
											"ID" => "Indonesia",
											"IE" => "Ireland",
											"IL" => "Israel",
											"IT" => "Italy",
											"JM" => "Jamaica",
											"JP" => "Japan",
											"JO" => "Jordan",
											"KZ" => "Kazakhstan",
											"KE" => "Kenya",
											"KR" => "Korea, Republic Of",
											"KW" => "Kuwait",
											"KG" => "Kyrgyzstan",
											"LA" => "Lao, People's Democratic Republic",
											"LV" => "Latvia",
											"LB" => "Lebanon",
											"LR" => "Liberia",
											"LT" => "Lithuania",
											"LU" => "Luxembourg",
											"MO" => "Macau",
											"MK" => "Macedonia",
											"MG" => "Madagascar",
											"MW" => "Malawi",
											"MY" => "Malaysia",
											"ML" => "Mali",
											"MT" => "Malta",
											"MR" => "Mauritania",
											"MU" => "Mauritius",
											"MX" => "Mexico",
											"FM" => "Micronesia, Federated States of",
											"MD" => "Moldova",
											"MN" => "Mongolia",
											"MS" => "Montserrat",
											"MZ" => "Mozambique",
											"NA" => "Namibia",
											"NP" => "Nepal",
											"NL" => "Netherlands",
											"NZ" => "New Zealand",
											"NI" => "Nicaragua",
											"NE" => "Niger",
											"NG" => "Nigeria",
											"NO" => "Norway",
											"OM" => "Oman",
											"PK" => "Pakistan",
											"PW" => "Palau",
											"PA" => "Panama",
											"PG" => "Papua New Guinea",
											"PY" => "Paraguay",
											"PE" => "Peru",
											"PH" => "Philippines",
											"PL" => "Poland",
											"PT" => "Portugal",
											"QA" => "Qatar",
											"RO" => "Romania",
											"RU" => "Russia",
											"ST" => "São Tomé and Príncipe",
											"SA" => "Saudi Arabia",
											"SN" => "Senegal",
											"SC" => "Seychelles",
											"SL" => "Sierra Leone",
											"SG" => "Singapore",
											"SK" => "Slovakia",
											"SI" => "Slovenia",
											"SB" => "Solomon Islands",
											"ZA" => "South Africa",
											"ES" => "Spain",
											"LK" => "Sri Lanka",
											"KN" => "St. Kitts and Nevis",
											"LC" => "St. Lucia",
											"VC" => "St. Vincent and The Grenadines",
											"SR" => "Suriname",
											"SZ" => "Swaziland",
											"SE" => "Sweden",
											"CH" => "Switzerland",
											"TW" => "Taiwan",
											"TJ" => "Tajikistan",
											"TZ" => "Tanzania",
											"TH" => "Thailand",
											"TT" => "Trinidad and Tobago",
											"TN" => "Tunisia",
											"TR" => "Turkey",
											"TM" => "Turkmenistan",
											"TC" => "Turks and Caicos",
											"UG" => "Uganda",
											"GB" => "United Kingdom",
											"UA" => "Ukraine",
											"AE" => "United Arab Emirates",
											"UY" => "Uruguay",
											"US" => "USA",
											"UZ" => "Uzbekistan",
											"VE" => "Venezuela",
											"VN" => "Vietnam",
											"VG" => "Virgin Islands, British",
											"YE" => "Yemen",
											"ZW" => "Zimbabwe"
													);

							foreach ($stores as $key => $value) {
								echo '<option value="' . $key . '"' . selected($key, $options['store_country'], false) . '>' . $value . '</option>' . "\n";
							}

							?>

						</select>
						<span style="color:#666666;margin-left:2px;">This option determines how long before the plugin requests new data from Apple's servers.</span>
					</td>
				</tr>

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
								echo '<option value="' . $value . '"' . selected($value, $options['cache_time_select_box'], false) . '>' . $key . '</option>';
							}

							?>

						</select>
						<span style="color:#666666;margin-left:2px;">This option determines how long before the plugin requests new data from Apple's servers.</span>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">Cache images locally:</th>
					<td>
						<label><input name="adp_options[cache_images_locally]" type="checkbox" value="1" <?php if (isset($options['cache_images_locally'])) { checked('1', $options['cache_images_locally']); } ?> /> Yes</label><br />
						<span style="color:#666666;margin-left:2px;">Load icons, screenshots, etc. locally instead of using Apple's CDN server. Your wp-content/uploads/ directory MUST be writeable for this option to have any effect.</span>
					</td>
				</tr>

				<tr>
					<th scope="row">Linkshare Partner ID:</th>
					<td>
						<input type="text" size="10" name="adp_options[linkshare_partner_id]" value="<?php echo $options['linkshare_partner_id']; ?>" />
						<span style="color:#666666;margin-left:2px;">Leave this blank if you don not have an Linkshare iTunes affiliate account.<br />
						You can find this by looking at a generated Linkshare URL and taking the property after "id=" like this:<br />
						<em>http://click.linksynergy.com/fs-bin/stat?id=<b>BiWowje1A</b>&amp;offerid=146261&amp;type=3&amp;subid=0...</em></span>
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
