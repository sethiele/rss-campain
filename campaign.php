<?php
/*
Plugin Name: Campaign
Plugin URI: http://sebastian.thiele.me
Description: This Plugin adds Campaign Infos to the RSS URL to tracking in Google Analytics or Piwik
Author: Sebastian Thiele
Version: 0.2
Author URI: http://sebastian.thiele.me
*/

function rssc_helper_buildParam($url, $source){
	$urlelement = parse_url($url);
	$rsscOptions = get_option('rssc');
	if($rsscOptions['rssc-'.$source.'-piwik_campaign'])	$attr[] = "piwik_campaign="	.urlencode($rsscOptions['rssc-'.$source.'-piwik_campaign']);
	if($rsscOptions['rssc-'.$source.'-piwik_kwd']) 			$attr[] = "piwik_kwd="			.urlencode($rsscOptions['rssc-'.$source.'-piwik_kwd']);
	if($rsscOptions['rssc-'.$source.'-utm_source']) 		$attr[] = "utm_source="			.urlencode($rsscOptions['rssc-'.$source.'-utm_source']);
	if($rsscOptions['rssc-'.$source.'-utm_medium']) 		$attr[] = "utm_medium="			.urlencode($rsscOptions['rssc-'.$source.'-utm_medium']);
	if($rsscOptions['rssc-'.$source.'-utm_campaign']) 	$attr[] = "utm_campaign="		.urlencode($rsscOptions['rssc-'.$source.'-utm_campaign']);
	$ret .= $url;
	if($attr && !$urlelement[query]) $ret .= "?";
	elseif($attr)
		$ret .= "&amp;";
	if($attr) {
		$anzattr = count($attr);
		foreach ($attr as $attribut){
			$ret .= $attribut;
			$i++;
			if($i < $anzattr) $ret .= "&amp;";
		}
	}
	return $ret;
}

function rss_campaign_post_link($content){
	if(is_feed()){
		return rssc_helper_buildParam($content, "rss");
	} else {
		return $content;
	}
}

function rss_campaign_admin() {
	add_options_page('Campaign', 'Campaign', 9, 'rss-campaign', rss_campaign_admin_show);
}

function rss_campaign_admin_show() {
?>
	<div class="wrap">
		<h2><?php _e('Campaign Option Page', 'rsscampaign'); ?></h2>
		
		<?php
			if($_POST[rsscsubmit]){
				$rsscOptions = array(
					"rssc-rss-piwik_campaign"			=> $_POST['rssc-rss-piwik_campaign'],
					"rssc-rss-piwik_kwd" 					=> $_POST['rssc-rss-piwik_kwd'],
					"rssc-rss-utm_source" 				=> $_POST['rssc-rss-utm_source'],
					"rssc-rss-utm_medium" 				=> $_POST['rssc-rss-utm_medium'],
					"rssc-rss-utm_campaign" 			=> $_POST['rssc-rss-utm_campaign'],
					"rssc-twitter-piwik_campaign"	=> $_POST['rssc-twitter-piwik_campaign'],
					"rssc-twitter-piwik_kwd" 			=> $_POST['rssc-twitter-piwik_kwd'],
					"rssc-twitter-utm_source" 		=> $_POST['rssc-twitter-utm_source'],
					"rssc-twitter-utm_medium" 		=> $_POST['rssc-twitter-utm_medium'],
					"rssc-twitter-utm_campaign" 	=> $_POST['rssc-twitter-utm_campaign'],
					"rssc-twitter-enable"					=> $_POST['rssc-twitter-enable']
				);
				update_option('rssc', $rsscOptions);
			}
			
			
			$rsscOptions = get_option('rssc');
		?>
		
		<form method="post" action="">
		<?php wp_nonce_field('update-options'); ?>
		
			<h3 class="rssc-head" id="rssc-head-rss"><?php _e('Options for RSS', 'rsscampaign')?></h3>
			<div class="rssc-optionen" id="rssc-rss-optionen">			
			<h4 style=""><?php _e('Piwik Options', 'rsscampaign'); ?></h4>
				<table>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Name', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" id="rssc-rss-piwik_campaign" name="rssc-rss-piwik_campaign" value="<?php echo urldecode($rsscOptions['rssc-rss-piwik_campaign']);?>" /></td>
					</tr>
					<tr>
						<td class="rssc-name"><?php _e('Keyword', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-rss-piwik_kwd" value="<?php echo urldecode($rsscOptions['rssc-rss-piwik_kwd']);?>" /></td>
					</tr>
				</table>
		
				<h4 style=""><?php _e('Google Analytics Options', 'rsscampaign'); ?></h4>
				<table>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Source', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-rss-utm_source" value="<?php echo urldecode($rsscOptions['rssc-rss-utm_source']);?>" /> <?php _e('(referrer: google, citysearch, newsletter4)', 'rsscampaign')?></td>
					</tr>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Medium', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-rss-utm_medium" value="<?php echo urldecode($rsscOptions['rssc-rss-utm_medium']);?>" />  <?php _e('(marketing medium: cpc, banner, email)', 'rsscampaign')?></td>
					</tr>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Name', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-rss-utm_campaign" value="<?php echo urldecode($rsscOptions['rssc-rss-utm_campaign']);?>" />  <?php _e('(product, promo code, or slogan)', 'rsscampaign')?></td>
					</tr>
				</table>
				
				<div id="rssc-example">
					<h3 style=""><?php _e('Example URL', 'rsscampaign')?></h3>
					RSS: <?php echo rssc_helper_buildParam(get_bloginfo('wpurl'), 'rss'); ?>
				</div>
				
			</div>
			
			<br>
			
			<h3 class="rssc-head" id="rssc-head-twitter"><?php _e('Options for Twitter', 'rsscampaign')?></h3>
			<div class="rssc-optionen" id="rssc-twitter-optionen">			
				
				<p>
					<?php _e('Enable Campaign for Twitter', 'rsscampaign')?> <input type="checkbox" name="rssc-twitter-enable" value="checked" <?php echo $rsscOptions['rssc-twitter-enable']; ?>> (<?php _e('No auto publishing', 'rsscampaign')?>)
				</p>
				
			<h4 style=""><?php _e('Piwik Options', 'rsscampaign'); ?></h4>
				<table>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Name', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" id="rssc-twitter-piwik_campaign" name="rssc-twitter-piwik_campaign" value="<?php echo urldecode($rsscOptions['rssc-twitter-piwik_campaign']);?>" /></td>
					</tr>
					<tr>
						<td class="rssc-name"><?php _e('Keyword', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-twitter-piwik_kwd" value="<?php echo urldecode($rsscOptions['rssc-twitter-piwik_kwd']);?>" /></td>
					</tr>
				</table>
		
				<h4 style=""><?php _e('Google Analytics Options', 'rsscampaign'); ?></h4>
				<table>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Source', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-twitter-utm_source" value="<?php echo urldecode($rsscOptions['rssc-twitter-utm_source']);?>" /> <?php _e('(referrer: google, citysearch, newsletter4)', 'rsscampaign')?></td>
					</tr>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Medium', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-twitter-utm_medium" value="<?php echo urldecode($rsscOptions['rssc-twitter-utm_medium']);?>" />  <?php _e('(marketing medium: cpc, banner, email)', 'rsscampaign')?></td>
					</tr>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Name', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-twitter-utm_campaign" value="<?php echo urldecode($rsscOptions['rssc-twitter-utm_campaign']);?>" />  <?php _e('(product, promo code, or slogan)', 'rsscampaign')?></td>
					</tr>
				</table>
				
				<div id="rssc-example">
					<h3 style=""><?php _e('Example URL', 'rsscampaign')?></h3>
					RSS: <?php echo rssc_helper_buildParam(get_bloginfo('wpurl'), 'twitter'); ?>
				</div>
				
			</div>
			
			
			<input type="hidden" name="rsscsubmit" value="submit" />
			<input type="hidden" name="action" value="update" />
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save', 'rsscampaign');?>" />
			</p>
				
		</form>

<?php
}

function rssc_post_twitter_meta(){
	global $post;
	if($post->post_status == "publish") {
		$twitterlink = "http://twitter.com/home?status=".urldecode($post->post_title." ".rssc_helper_buildParam(get_permalink(), "twitter"));
		printf(__("Klick <a href=\"%s\" target=\"_blank\">here</a> to publish your post at twitter."), $twitterlink);
		echo "<br>";
		_e('Your Campain Link is:', 'rsscampaign'); echo "<br>";
		echo rssc_helper_buildParam(get_permalink(), "twitter");
	}
	
	// echo rssc_helper_buildParam(get_permalink(), "twitter");
	else _e('Wait for Post publishing', 'rsscampaign');
	// print_r($post);
}

// New Field in new-post.php
function post_rssc_meta(){
	add_meta_box('rssc', __('Twitter with Campaign'), 'rssc_post_twitter_meta', 'post', 'normal');
}

$rsscOptions = get_option('rssc');

add_filter( 'post_link', 'rss_campaign_post_link');
add_action(	'admin_menu', 'rss_campaign_admin');
if($rsscOptions['rssc-twitter-enable']) add_action('admin_init', 'post_rssc_meta');

$plugindir = basename(dirname(__FILE__));
if(is_admin() && ($_GET['page'] == 'rss-campaign')) {
	wp_enqueue_script('jquery');
	wp_enqueue_script('rssc', WP_CONTENT_URL .'/plugins/'. $plugindir. '/js/rssc.js',  array('jquery'));
	wp_enqueue_style('rssc', WP_CONTENT_URL .'/plugins/'. $plugindir. '/css/rssc.css');
}

?>