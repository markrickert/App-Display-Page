<?php
/*  Copyright 2011  Mark Rickert  (email : mjar81@gmail.com)

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

$app_id = 421049699; //Customize this for your application
$apple_search_url = "http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/wa/wsLookup?id=";

//Get and parse the data from Apple
$json_data = json_decode(file_get_contents($apple_search_url . $app_id));

if($json_data->resultCount == 0) {
	die('<h1>Apple returned no record for that app ID.<br />Please check your variable.</h1>');
}

$app = $json_data->results[0];

//Lay out the data
?>

<div class="app-wrapper">

	<img src="<?php echo $app->artworkUrl60; ?>" />
	
	<h1 class="app-title"><?php echo $app->trackName; ?><span class="app-version"> <?php echo $app->version; ?></span></h1>
	<h2 class="app-artist">By <a href="<?php echo $app->sellerUrl; ?>"><?php echo $app->artistName; ?></a></h2>

	<div class="app-meta">
		Rated <?php echo $app->averageUserRating; ?> out of 5 by <?php echo $app->userRatingCount; ?> users.
	</div>

	
	<div class="app-purchase">
		<?php if($app->price == 0) { ?>
		Free!<br />
		<?php } else { ?>
		<?php echo $app->currency; ?> <?php echo $app->price; ?><br />
		<?php } ?>
		<a href="<?php echo $app->trackViewUrl ;?>">Buy Now!</a>
	</div>

	<div class="app-releasenotes">
		<h3>Latest Release Notes:</h3>
		<?php echo nl2br($app->releaseNotes); ?>
	</div>

	<div class="app-description">
		<h3>Description:</h3>
		<?php echo nl2br($app->description); ?>
	</div>

	<?php if(count($app->screenshotUrls) > 0) { ?>
	<div class="app-screenshots-iphone">
		<h3>iPhone Screenshots:</h3>
		<ul class="app-screenshots">
		<?php
		foreach($app->screenshotUrls as $ssurl) {
			echo '<li class="app-screenshot"><img src="' . $ssurl . '" /></li>';
		}
		?>
	</div>
	<?php } ?>

	<?php if(count($app->ipadScreenshotUrls) > 0) { ?>
	<div class="app-screenshots-ipad">
		<h3>iPad Screenshots:</h3>
		<ul class="app-screenshots">
		<?php
		foreach($app->ipadScreenshotUrls as $ssurl) {
			echo '<li class="app-screenshot"><img src="' . $ssurl . '" /></li>';
		}
		?>
	</div>
	<?php } ?>

</div>
