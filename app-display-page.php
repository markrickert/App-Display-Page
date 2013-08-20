<?php
/*
Plugin Name: App Display Page
Version: 1.7
Plugin URI: http://www.markrickert.me/
Description: Adds a shortcode so that you can pull and display iOS App Store applications.
Author: Mark Rickert
Author URI: http://www.markrickert.me/
*/

/*
Copyright 2011  Mark Rickert  (email : mjar81@gmail.com)

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

include_once("app-display-page-admin.php");

define('IOS_APP_PAGE_APPSTORE_URL', 'http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/wa/wsLookup?id=');

add_shortcode('ios-app', 'ios_app_page_shortcode');
add_shortcode('ios_app', 'ios_app_page_shortcode');
add_action('wp_print_styles', 'ios_app_page_add_stylesheet');
add_action('init', 'ios_app_init');

function ios_app_init() {
	if(ios_app_setting('display_smart_banner') == "1") {
		add_action('wp_head', 'ios_app_header_meta');
	}
}

//Available Actions
$actions = array('name', 'icon', 'icon_url', 'version', 'price', 'release_notes', 'description', 'rating', 'iphoness', 'ipadss', 'itunes_link');
foreach ($actions as $action) {
	add_shortcode('ios_app_' . $action, 'ios_app_' . $action);
}

function ios_app_name( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));
	return $app->trackName;
}

function ios_app_icon( $atts ) {
	return '<img src="' . ios_app_icon_url($atts) . '" />';
}

function ios_app_icon_url( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));

	$artwork_url = $app->artworkUrl100;
	if(ios_app_setting('cache_images_locally') == '1')
	{
		$upload_dir = wp_upload_dir();
		$artwork_url = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($app->artworkUrl100);
	}

	return $artwork_url;
}

// Output an app smart banner
// http://developer.apple.com/library/ios/Documentation/AppleApplications/Reference/SafariWebContent/PromotingAppswithAppBanners/PromotingAppswithAppBanners.html
function ios_app_header_meta( $atts ) {
    global $post;
    $pattern = get_shortcode_regex();

    if (   preg_match_all( '/'. $pattern .'/s', $post->post_content, $matches )
        && array_key_exists( 2, $matches )
        && in_array( 'ios-app', $matches[2] ) )
    {
        // shortcode is being used
			$atts = shortcode_parse_atts($matches[0][0]);
			$id = preg_replace("/[^0-9]/", "", $atts['id']);
			if($id)
				echo "\n" . '<meta name="apple-itunes-app" content="app-id=' . $id . '"/>' .  "\n";

    }
}

function ios_app_version( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));
	return $app->version;
}

function ios_app_price( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));
	if($app->price == 0)
		return "Free";
	else
		return $app->price;
}

function ios_app_release_notes( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));
	return nl2br($app->releaseNotes);
}

function ios_app_description( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));
	return nl2br($app->description);
}

function ios_app_rating( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));
	if(isset($app->userRatingCount))
		return 'Rated' . $app->averageUserRating . ' out of 5 by ' . $app->userRatingCount . ' users.';
	else
		return '';
}

function ios_app_iphoness( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));
	$retval = '<ul>';
	foreach($app->screenshotUrls as $ssurl) {
		$ssurl = str_replace(".png", ".320x480-75.jpg", $ssurl);
		if(ios_app_setting('cache_images_locally') == '1')
		{
			$upload_dir = wp_upload_dir();
			$ssurl = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($ssurl);
		}
		$retval .= '<li class="app-screenshot"><a href="' . $ssurl . '" alt="Full Size Screenshot"><img src="' . $ssurl . '" width="' . ios_app_setting('ss_size') . '" /></a></li>';
	}
	$retval .= '</ul>';
	return $retval;
}

function ios_app_ipadss( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));
	$retval = '<ul>';
	foreach($app->ipadScreenshotUrls as $ssurl) {
		if(ios_app_setting('cache_images_locally') == '1')
		{
			$upload_dir = wp_upload_dir();
			$ssurl = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($ssurl);
		}
		$retval .= '<li class="app-screenshot"><a href="' . $ssurl . '" alt="Full Size Screenshot"><img src="' . $ssurl . '" width="' . ios_app_setting('ss_size') . '" /></a></li>';
	}
	$retval .= '</ul>';
	return $retval;
}

function ios_app_itunes_link( $atts ) {
	if(is_object($atts))
		$app = $atts;
	else
		$app = ios_app_get_data(ios_ap_extract_id($atts));

	$url = $app->trackViewUrl;

	$partner_id = trim(ios_app_setting('phg_partner_id'));
	if($partner_id != "")
	{
		$url .= "&at=" . $partner_id;

		$campaign_id = trim(ios_app_setting('phg_campaign_id'));
		if($campaign_id != ""){
			$url .= "&ct=" . $campaign_id;
		}
	}

	return $url;
}

function ios_app_page_shortcode( $atts ) {

	extract( shortcode_atts( array(
		'id' => '',
		'download_url' => ''
	), $atts ) );

	//Don't do anything if the ID is blank or non-numeric
	if($id == "" || !is_numeric($id))return;

	$app = ios_app_get_data($id);
	if($app)
		return ios_app_page_output($app, $download_url);
	else
		wp_die('No valid data for app id: ' . $id);
}

function ios_app_get_data( $id ) {
	//Check to see if we have a cached version of the JSON.
	$ios_app_options = get_option('ios-app-' . $id, '');

	if($ios_app_options == '' || $ios_app_options['next_check'] < time()) {

		$ios_app_options_data = ios_app_page_get_json($id);
		$ios_app_options = array('next_check' => time() + ios_app_setting('cache_time_select_box'), 'app_data' => $ios_app_options_data);

		update_option('ios-app-' . $id, $ios_app_options);
		if(ios_app_setting('cache_images_locally') == '1')ios_app_save_images_locally($ios_app_options['app_data']);
	}

	return $ios_app_options['app_data'];
}

function ios_app_page_add_stylesheet() {
	wp_register_style('app-display-page-styles', plugins_url( 'app-display-page-styles.css', __FILE__ ));
	wp_enqueue_style( 'app-display-page-styles');
}

function ios_app_page_get_json($id) {

	if(function_exists('file_get_contents') && ini_get('allow_url_fopen'))
		$json_data  = ios_app_page_get_json_via_fopen($id);
	else if(function_exists('curl_exec'))
		$json_data = ios_app_page_get_json_via_curl($id);
	else
		wp_die('<h1>You must have either file_get_contents() or curl_exec() enabled on your web server. Please contact your hosting provider.</h1>');

	if($json_data->resultCount == 0) {
		wp_die('<h1>Apple returned no record for that app ID.<br />Please check your app ID.</h1>');
	}

	return $json_data->results[0];

}

function ios_app_page_get_json_via_fopen($id) {
	return json_decode( ios_app_fopenme( ios_app_page_url( $id ) ) );
}

function ios_app_page_get_json_via_curl($id) {
	return json_decode( ios_app_curlme( ios_app_page_url( $id ) ) );
}

function ios_app_page_url( $id ) {
	$url = IOS_APP_PAGE_APPSTORE_URL . $id;
	$store = ios_app_setting('store_country');
	if($store)
		$url = $url . "&country=" . $store;

	return $url;
}

function ios_app_fopenme ($url) {
	return file_get_contents($url);
}

function ios_app_curlme ($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $output = curl_exec($ch);
    curl_close($ch);
	return $output;
}

function ios_app_fopen_or_curl($url)
{
	if(function_exists('file_get_contents') && ini_get('allow_url_fopen'))
		return ios_app_fopenme($url);
	else if(function_exists('curl_exec'))
		return ios_app_curlme($url);
	else
		wp_die('<h1>You must have either file_get_contents() or curl_exec() enabled on your web server. Please contact your hosting provider.</h1>');
}


function ios_app_page_output($app, $download_url) {

	ob_start();

?>
<div class="app-wrapper">

	<?php
 		$artwork_url = $app->artworkUrl100;
		if(ios_app_setting('cache_images_locally') == '1')
		{
			$upload_dir = wp_upload_dir();
			$artwork_url = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($app->artworkUrl100);
		}

 	?>

	<div id="app-icon-container" style="width: <?php echo ios_app_setting('icon_size'); ?>px;height: <?php echo ios_app_setting('icon_size'); ?>px;">
		<img class="app-icon" src="<?php echo $artwork_url; ?>" width="<?php echo ios_app_setting('icon_size'); ?>" height="<?php echo ios_app_setting('icon_size'); ?>" />
	</div>

	<h1 class="app-title"><?php echo $app->trackName; ?><span class="app-version"> <?php echo $app->version; ?></span></h1>

	<?php if(isset($app->userRatingCount)) { ?>
	<div class="app-rating">
		Rated <?php echo $app->averageUserRating; ?> out of 5 by <?php echo $app->userRatingCount; ?> users.
	</div>
	<?php } ?>

	<div class="app-purchase">
		<?php if($app->price == 0) { ?>
		Free!<br />
		<?php } else { ?>
		Only $<?php echo $app->price; ?>!<br />
		<?php } ?>
		<a href="<?php if($download_url)echo $download_url; else echo ios_app_itunes_link($app); ?>">
			<img src="http://ax.phobos.apple.com.edgesuite.net/images/web/linkmaker/badge_appstore-lrg.gif" alt="App Store" style="border: 0;"/>
		</a>
	</div>

	<div class="app-releasenotes">
		<h2>Latest Release Notes:</h2>
		<?php echo nl2br($app->releaseNotes); ?>
	</div>

	<div class="app-description">
		<h2>Description:</h2>
		<?php echo nl2br($app->description); ?>
	</div>

	<?php if(count($app->screenshotUrls) > 0) { ?>
	<div class="app-screenshots-iphone">
		<h2>iPhone Screenshots:</h2>
		<ul class="app-screenshots">
		<?php
		foreach($app->screenshotUrls as $ssurl) {
			$ssurl = str_replace(".png", ".320x480-75.jpg", $ssurl);

			if(ios_app_setting('cache_images_locally') == '1')
			{
				$upload_dir = wp_upload_dir();
				$ssurl = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($ssurl);
			}

			echo '<li class="app-screenshot"><a href="' . $ssurl . '" alt="Full Size Screenshot"><img src="' . $ssurl . '" width="' . ios_app_setting('ss_size') . '" /></a></li>';
		}
		?>
	</div>
	<div style="clear:left;">&nbsp;</div>
	<?php } ?>

	<?php if(count($app->ipadScreenshotUrls) > 0) { ?>
	<div class="app-screenshots-ipad">
		<h2>iPad Screenshots:</h2>
		<ul class="app-screenshots">
		<?php
		foreach($app->ipadScreenshotUrls as $ssurl) {
			if(ios_app_setting('cache_images_locally') == '1')
			{
				$upload_dir = wp_upload_dir();
				$ssurl = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($ssurl);
			}

			echo '<li class="app-screenshot"><a href="' . $ssurl . '" alt="Full Size Screenshot"><img src="' . $ssurl . '" width="' . ios_app_setting('ss_size') . '" /></a></li>';
		}
		?>
	</div>
	<div style="clear:left;">&nbsp;</div>
	<?php }

	$return = ob_get_contents();
	ob_end_clean();

	return $return;
}

function ios_ap_extract_id( $atts ) {
	extract( shortcode_atts( array(
		'id' => ''
	), $atts ) );
	return $id;
}

function ios_app_save_images_locally($app) {
	$upload_dir = wp_upload_dir();

	if(!is_writeable($upload_dir['basedir'])) {
		//Uploads dir isn't writeable. bummer.
		ios_app_set_setting('cache_images_locally', '0');
		return;
	} else {
		//Loop through screenshots and the app icons and cache everything
		if(!is_dir($upload_dir['basedir'] . '/ios-app/' . $app->trackId)) {
			if(!mkdir($upload_dir['basedir'] . '/ios-app/' . $app->trackId, 0755, true))
			{
				ios_app_set_setting('cache_images_locally', '0');
				return;
			}
		}

		$urls_to_cache = array();

		$urls_to_cache[] = $app->artworkUrl60;
		$urls_to_cache[] = $app->artworkUrl100;
		$urls_to_cache[] = $app->artworkUrl512;

		foreach($app->screenshotUrls as $ssurl) {
			$ssurl2 = str_replace(".png", ".320x480-75.jpg", $ssurl);
			$urls_to_cache[] = $ssurl;
			$urls_to_cache[] = $ssurl2;
		}
		foreach($app->ipadScreenshotUrls as $ssurl) {
			$ssurl2 = str_replace(".png", ".320x480-75.jpg", $ssurl);
			$urls_to_cache[] = $ssurl;
			$urls_to_cache[] = $ssurl2;
		}

		foreach($urls_to_cache as $url) {
			$content = ios_app_fopen_or_curl($url);

			if($fp = fopen($upload_dir['basedir'] . '/ios-app/' . $app->trackId . '/' . basename($url), "w+"))
			{
				fwrite($fp, $content);
				fclose($fp);
			}
			else {
				//Couldnt write the file. Permissions must be wrong.
				ios_app_set_setting('cache_images_locally', '0');
				return;
			}
		}
	}
}

$app_display_page_settings = array();
function ios_app_setting($name) {
	global $app_display_page_settings;

	$app_display_page_settings = get_option('adp_options');
	if(!$app_display_page_settings) {
		adp_add_defaults();
		$app_display_page_settings = get_option('adp_options');
	}

	return $app_display_page_settings[$name];
}

function ios_app_set_setting($name, $value) {
	global $app_display_page_settings;

	$app_display_page_settings = get_option('adp_options');
	if(!$app_display_page_settings) {
		adp_add_defaults();
		$app_display_page_settings = get_option('adp_options');
	}

	$app_display_page_settings[$name] = $value;
}

?>
