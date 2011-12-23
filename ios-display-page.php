<?php 
/*
 * Plugin Name: iOS Display Page
 * Version: 1.1
 * Plugin URI: http://www.ear-fung.us
 * Description: Adds a shortcode so that you can pull and display app store applications.
 * Author: Mark Rickert
 * Author URI: http://www.ear-fung.us/
 */

define('IOS_APP_PAGE_APPSTORE_URL', 'http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/wa/wsLookup?id=');
define('IOS_APP_PAGE_ICON_SIZE', 175);
define('IOS_APP_PAGE_IMAGE_SIZE', 120);
define('IOS_APP_PAGE_CACHE_TIME', 60 * 60 * 24); //One Day
define('IOS_APP_PAGE_CACHE_IMAGES', true);

add_shortcode('ios-app', 'ios_display_page_shortcode');
add_shortcode('ios_app', 'ios_display_page_shortcode');
add_action('wp_print_styles', 'ios_display_page_add_stylesheet');

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
	if(IOS_APP_PAGE_CACHE_IMAGES == true)
	{
		$upload_dir = wp_upload_dir();
		$artwork_url = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($app->artworkUrl100);
	}

	return $artwork_url;
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
	return 'Rated' . $app->averageUserRating . ' out of 5 by ' . $app->userRatingCount . ' users.';
}

function ios_app_iphoness( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));
	$retval = '<ul>';
	foreach($app->screenshotUrls as $ssurl) {
		$ssurl = str_replace(".png", ".320x480-75.jpg", $ssurl);
		if(IOS_APP_PAGE_CACHE_IMAGES == true)
		{
			$upload_dir = wp_upload_dir();
			$ssurl = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($ssurl);
		}
		$retval .= '<li class="app-screenshot"><a href="' . $ssurl . '" alt="Full Size Screenshot"><img src="' . $ssurl . '" width="' . IOS_APP_PAGE_IMAGE_SIZE . '" /></a></li>';
	}
	$retval .= '</ul>';
	return $retval;
}

function ios_app_ipadss( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));
	$retval = '<ul>';
	foreach($app->ipadScreenshotUrls as $ssurl) {
		if(IOS_APP_PAGE_CACHE_IMAGES == true)
		{
			$upload_dir = wp_upload_dir();
			$ssurl = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($ssurl);
		}
		$retval .= '<li class="app-screenshot"><a href="' . $ssurl . '" alt="Full Size Screenshot"><img src="' . $ssurl . '" width="' . IOS_APP_PAGE_IMAGE_SIZE . '" /></a></li>';
	}
	$retval .= '</ul>';
	return $retval;
}

function ios_app_itunes_link( $atts ) {
	$app = ios_app_get_data(ios_ap_extract_id($atts));
	return $app->trackViewUrl;
}

function ios_display_page_shortcode( $atts ) {

	extract( shortcode_atts( array(
		'id' => '',
		'download_url' => ''
	), $atts ) );
	
	//Don't do anything if the ID is blank or non-numeric
	if($id == "" || !is_numeric($id))return;
	
	$app = ios_app_get_data($id);
	if($app)
		ios_display_page_output($app, $download_url);
	else
		wp_die('No valid data for app id: ' . $id);
}

function ios_app_get_data( $id ) {	
	//Check to see if we have a cached version of the JSON.
	$ios_app_options = get_option('ios-app-' . $id, '');
		
	if($ios_app_options == '' || $ios_app_options['next_check'] < time()) {
		
		$ios_app_options_data = ios_display_page_get_json($id);
		$ios_app_options = array('next_check' => time() + IOS_APP_PAGE_CACHE_TIME, 'app_data' => $ios_app_options_data);

		update_option('ios-app-' . $id, $ios_app_options);
		if(IOS_APP_PAGE_CACHE_IMAGES == true)ios_app_save_images_locally($ios_app_options['app_data']);
	}
	
	return $ios_app_options['app_data'];
}

function ios_display_page_add_stylesheet() {
	wp_register_style('ios-app-styles', plugins_url( 'ios-app-styles.css', __FILE__ ));
	wp_enqueue_style( 'ios-app-styles');
}

function ios_display_page_get_json($id) {

	if(function_exists('file_get_contents') && ini_get('allow_url_fopen'))
		$json_data  = ios_display_page_get_json_via_fopen($id);
	else if(function_exists('curl_exec'))
		$json_data = ios_display_page_get_json_via_curl($id);
	else
		wp_die('<h1>You must have either file_get_contents() or curl_exec() enabled on your web server. Please contact your hosting provider.</h1>');		

	if($json_data->resultCount == 0) {
		wp_die('<h1>Apple returned no record for that app ID.<br />Please check your app ID.</h1>');
	}
	
	return $json_data->results[0];

}

function ios_display_page_get_json_via_fopen($id) {
	return json_decode(ios_app_fopenme(IOS_APP_PAGE_APPSTORE_URL . $id));
}

function ios_display_page_get_json_via_curl($id) {
	return json_decode(ios_app_curlme(IOS_APP_PAGE_APPSTORE_URL . $id));
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


function ios_display_page_output($app, $download_url) {
?>
<div class="app-wrapper">

	<?php 
 		$artwork_url = $app->artworkUrl100;
		if(IOS_APP_PAGE_CACHE_IMAGES == true)
		{
			$upload_dir = wp_upload_dir();
			$artwork_url = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($app->artworkUrl100);
		}

 	?>
	
	<img class="app-icon" src="<?php echo $artwork_url; ?>" width="<?php echo IOS_APP_PAGE_ICON_SIZE; ?>" height="<?php echo IOS_APP_PAGE_ICON_SIZE; ?>" />
	
	<h1 class="app-title"><?php echo $app->trackName; ?><span class="app-version"> <?php echo $app->version; ?></span></h1>

	<div class="app-rating">
		Rated <?php echo $app->averageUserRating; ?> out of 5 by <?php echo $app->userRatingCount; ?> users.
	</div>

	
	<div class="app-purchase">
		<?php if($app->price == 0) { ?>
		Free!<br />
		<?php } else { ?>
		Only $<?php echo $app->price; ?>!<br />
		<?php } ?>
		<a href="<?php if($download_url)echo $download_url; else echo $app->trackViewUrl; ?>">
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

			if(IOS_APP_PAGE_CACHE_IMAGES == true)
			{
				$upload_dir = wp_upload_dir();
				$ssurl = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($ssurl);
			}

			echo '<li class="app-screenshot"><a href="' . $ssurl . '" alt="Full Size Screenshot"><img src="' . $ssurl . '" width="' . IOS_APP_PAGE_IMAGE_SIZE . '" /></a></li>';
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
			if(IOS_APP_PAGE_CACHE_IMAGES == true)
			{
				$upload_dir = wp_upload_dir();
				$ssurl = $upload_dir['baseurl'] . '/ios-app/' . $app->trackId . '/' . basename($ssurl);
			}
			
			echo '<li class="app-screenshot"><a href="' . $ssurl . '" alt="Full Size Screenshot"><img src="' . $ssurl . '" width="' . IOS_APP_PAGE_IMAGE_SIZE . '" /></a></li>';
		}
		?>
	</div>
	<div style="clear:left;">&nbsp;</div>
	<?php }
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
		echo 'upload dir is not writeable';
		define('IOS_APP_PAGE_CACHE_IMAGES', false);
		return;
	} else {
		//Loop through screenshots and the app icons and cache everything
		if(!is_dir($upload_dir['basedir'] . '/ios-app/' . $app->trackId)) {
			if(!mkdir($upload_dir['basedir'] . '/ios-app/' . $app->trackId, 0755, true))
			{
				define('IOS_APP_PAGE_CACHE_IMAGES', false);
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
				define('IOS_APP_PAGE_CACHE_IMAGES', false);
				return;
			}
		}
	}
}