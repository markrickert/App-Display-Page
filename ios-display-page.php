<?php 
/*
 * Plugin Name: iOS Display Page
 * Version: 1.0
 * Plugin URI: http://www.ear-fung.us
 * Description: Adds a shortcode so that you can pull and display app store applications.
 * Author: Mark Rickert
 * Author URI: http://www.ear-fung.us/
 */

define('IOS_DISPLAY_PAGE_APPSTORE_URL', 'http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/wa/wsLookup?id=');
define('IOS_DISPLAY_PAGE_ICON_SIZE', 175);
define('IOS_DISPLAY_PAGE_IMAGE_SIZE', 120);

add_shortcode('ios-app', 'ios_display_page_shortcode');
add_action('wp_print_styles', 'ios_display_page_add_stylesheet');

function ios_display_page_shortcode( $atts ) {


	extract( shortcode_atts( array(
		'id' => ''
	), $atts ) );
	
	//Don't do anything if the ID is blank or non-numeric
	if($id == "" || !is_numeric($id))return;
	
	//Check to see if we have a cached version of the JSON.
	$ios_app_options = get_option('ios-app-' . $id, '');
	
	$check_interval = 60 * 60 * 24;
	if($ios_app_options == '' || $ios_app_options['last_checked'] + $check_interval > time()) {
		$ios_app_options_data = ios_display_page_get_json($id);
		$ios_app_options = array('last_checked' => time(), 'app_data' => $ios_app_options_data);

		update_option('ios-app-' . $id, $ios_app_options);
	}
	
	ios_display_page_output($ios_app_options['app_data']);
}

function ios_display_page_add_stylesheet() {
    $myStyleUrl = WP_PLUGIN_URL . '/ios-display-page/ios-app-styles.css';
    $myStyleFile = WP_PLUGIN_DIR . '/ios-display-page/ios-app-styles.css';
    if ( file_exists($myStyleFile) ) {
        wp_register_style('ios-app-styles', $myStyleUrl);
        wp_enqueue_style( 'ios-app-styles');
    }
}

function ios_display_page_get_json($id) {
	//Get and parse the data from Apple
	$json_data = json_decode(file_get_contents(IOS_DISPLAY_PAGE_APPSTORE_URL . $id));
	
	if($json_data->resultCount == 0) {
		wp_die('<h1>Apple returned no record for that app ID.<br />Please check your variable.</h1>');
	}
	
	return $json_data->results[0];	
}

function ios_display_page_output($app) {
?>
<div class="app-wrapper">

	<img class="app-icon" src="<?php echo $app->artworkUrl100; ?>" width="<?php echo IOS_DISPLAY_PAGE_ICON_SIZE; ?>" height="<?php echo IOS_DISPLAY_PAGE_ICON_SIZE; ?>" />
	
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
		<a href="<?php echo $app->trackViewUrl ;?>">
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
			echo '<li class="app-screenshot"><a href="' . $ssurl . '" alt="Full Size Screenshot"><img src="' . $ssurl . '" width="' . IOS_DISPLAY_PAGE_IMAGE_SIZE . '" /></a></li>';
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
			echo '<li class="app-screenshot"><a href="' . $ssurl . '" alt="Full Size Screenshot"><img src="' . $ssurl . '" width="' . IOS_DISPLAY_PAGE_IMAGE_SIZE . '" /></a></li>';
		}
		?>
	</div>
	<div style="display:none;clear:left;">&nbsp;</div>
	<?php }


}