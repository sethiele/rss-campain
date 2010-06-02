<?php
/*
Plugin Name: Campaign
Plugin URI: http://sebastian.thiele.me
Description: This Plugin adds Campaign Infos to the RSS URL to tracking in Google Analytics or Piwik
Author: Sebastian Thiele
Version: 1.2.2
Author URI: http://sebastian.thiele.me
*/


// Build the URL
function rssc_helper_buildParam($url, $source){
  global $post;
	$urlelement = parse_url($url);
	$rsscOptions = get_option('rssc');
	if($rsscOptions['rssc-'.$source.'-piwik_campaign'])	$attr[] = "piwik_campaign="	.urlencode($rsscOptions['rssc-'.$source.'-piwik_campaign']);
	if($rsscOptions['rssc-'.$source.'-piwik_kwd']) 			$attr[] = "piwik_kwd="			.urlencode($rsscOptions['rssc-'.$source.'-piwik_kwd']);
	if($rsscOptions['rssc-'.$source.'-utm_source']) 		$attr[] = "utm_source="			.urlencode($rsscOptions['rssc-'.$source.'-utm_source']);
	if($rsscOptions['rssc-'.$source.'-utm_medium']) 		$attr[] = "utm_medium="			.urlencode($rsscOptions['rssc-'.$source.'-utm_medium']);
	if($rsscOptions['rssc-'.$source.'-utm_campaign']) 	$attr[] = "utm_campaign="		.urlencode($rsscOptions['rssc-'.$source.'-utm_campaign']);
	$attr = str_replace("%25POSTID%25", $post->ID, $attr);
	$attr = str_replace("%25POSTTITLE%25", urlencode($post->post_title), $attr);
	$ret .= $url;
	if($attr && !$urlelement[query]) $ret .= "?";
	elseif($attr)
		$ret .= "&";
	if($attr) {
		$anzattr = count($attr);
		foreach ($attr as $attribut){
			$ret .= $attribut;
			$i++;
			if($i < $anzattr) $ret .= "&";
		}
	}
	return urlencode($ret);
}

function rss_campaign_post_link($content){
  global $post;
	if(is_feed()){
		return str_replace("&", "&amp;", urldecode(rssc_helper_buildParam($content, "rss")));
	} else {
		return $content;
	}
}
// END Build the URL

function rss_campaign_admin() {
	add_options_page('Campaign', 'Campaign', 9, 'rss-campaign', rss_campaign_admin_show);
}

// Admin Backend
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
					"rssc-twitter-enable"					=> $_POST['rssc-twitter-enable'],
					"rssc-bitly-user"             => $_POST['rssc-bitly-user'],
					"rssc-bitly-api"              => $_POST['rssc-bitly-api']
				);
				update_option('rssc', $rsscOptions);
			}
			
			
			$rsscOptions = get_option('rssc');
			
			global $post;
			$post->ID = "POSTID";
			$post->post_title = "POSTTITLE";
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
						<td class="rssc-value"><input type="text" name="rssc-rss-utm_source" value="<?php echo urldecode($rsscOptions['rssc-rss-utm_source']);?>" /> (<?php _e('referrer: google, citysearch, newsletter4', 'rsscampaign')?>)</td>
					</tr>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Medium', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-rss-utm_medium" value="<?php echo urldecode($rsscOptions['rssc-rss-utm_medium']);?>" />  (<?php _e('marketing medium: cpc, banner, email', 'rsscampaign')?>)</td>
					</tr>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Name', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-rss-utm_campaign" value="<?php echo urldecode($rsscOptions['rssc-rss-utm_campaign']);?>" />  (<?php _e('product, promo code, or slogan', 'rsscampaign')?>)</td>
					</tr>
				</table>
				
				<div id="rssc-example">
					<h3 style=""><?php _e('Example URL', 'rsscampaign')?></h3>
					RSS: <?php echo urldecode(rssc_helper_buildParam(get_bloginfo('wpurl'), 'rss')); ?>
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
						<td class="rssc-value"><input type="text" name="rssc-twitter-utm_source" value="<?php echo urldecode($rsscOptions['rssc-twitter-utm_source']);?>" /> (<?php _e('referrer: google, citysearch, newsletter4', 'rsscampaign')?>)</td>
					</tr>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Medium', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-twitter-utm_medium" value="<?php echo urldecode($rsscOptions['rssc-twitter-utm_medium']);?>" />  (<?php _e('marketing medium: cpc, banner, email', 'rsscampaign')?>)</td>
					</tr>
					<tr>
						<td class="rssc-name"><?php _e('Campaign Name', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-twitter-utm_campaign" value="<?php echo urldecode($rsscOptions['rssc-twitter-utm_campaign']);?>" />  (<?php _e('product, promo code, or slogan', 'rsscampaign')?>)</td>
					</tr>
				</table>
				
				<div id="rssc-example">
					<h3 style=""><?php _e('Example URL', 'rsscampaign')?></h3>
					Twitter: <?php echo urldecode(rssc_helper_buildParam(get_bloginfo('wpurl'), 'twitter')); ?>
				</div>
				
			</div>
			
			<br>
			
			<h3 class="rssc-head" id="rssc-head-shorter"><?php _e('URL shortener', 'rsscampaign')?></h3>
			<div class="rssc-optionen" id="rssc-shorter-optionen">
			  <?php _e('Up to now only Bit.ly is supportet.', 'rsscampaign')?><br><br>
			  
			  <table>
					<tr>
						<td class="rssc-name"><?php _e('Bit.ly Username', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-bitly-user" value="<?php echo urldecode($rsscOptions['rssc-bitly-user']);?>" /></td>
					</tr>
					<tr>
						<td class="rssc-name"><?php _e('Bit.ly ApiKey', 'rsscampaign')?></td>
						<td class="rssc-value"><input type="text" name="rssc-bitly-api" value="<?php echo urldecode($rsscOptions['rssc-bitly-api']);?>" /></td>
					</tr>
				</table>
			</div>
			
			<br>
			
			<h3 class="rssc-head" id="rssc-head-placeholder"><?php _e('Placeholder', 'rsscampaign')?></h3>
			<div class="rssc-optionen" id="rssc-placeholder-optionen">
			  <?php _e('The following placeholder can be added to every value and will replaced automatically.', 'rsscampaign')?><br><br>
			  <ul>
			    <li><b>%POSTID%</b> - <?php _e('Adds the post id to the campaign', 'rsscampaign'); ?></li>
			    <li><b>%POSTTITLE%</b> - <?php _e('Adds the posttitle to the campaign', 'rsscampaign'); ?></li>
			    <li><?php _e('More comming soon.', 'rsscampaign'); ?></li>
			  </ul>
			</div>
			
			
			
			<input type="hidden" name="rsscsubmit" value="submit" />
			<input type="hidden" name="action" value="update" />
			
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save', 'rsscampaign');?>" />
			</p>
				
		</form>
		<a href='http://www.pledgie.com/campaigns/10886'><img alt='Click here to lend your support to: Open Source by Sebastian and make a donation at www.pledgie.com !' src='http://www.pledgie.com/campaigns/10841.png?skin_name=chrome' border='0' style="float:left; padding-right: 5px" /></a>
		<?php printf(__('The Source Code is open at <a href="%s">github</a>', 'rsscampaign'), "http://github.com/sethiele/rss-campain"); ?> | 
		<?php printf(__('Feature request/ bugreport  at <a href="%s">github</a>', 'rsscampaign'), 'http://github.com/sethiele/rss-campain/issues'); ?> <br>
		<?php printf(__('<a href="%s">Project Page</a>', 'rsscampaign'), "http://sebastian.thiele.me/projekte/wordpress-plugin-campaign?piwik_campaign=Plugins&piwik_kwd=RSS-Campaign"); ?> |
		<?php printf(__('<a href="%s">Author Page</a>', 'rsscampaign'), "http://sebastian.thiele.me/?piwik_campaign=Plugins&piwik_kwd=RSS-Campaign"); ?>

<?php
}
// END Admin Backend

// Area in new Posts
function rssc_post_twitter_meta(){
	global $post;
	$rsscOptions = get_option('rssc');
	if($post->post_status == "publish") {
	  $rsscPostLink = rssc_helper_buildParam(get_permalink(), "twitter");
	  if($rsscOptions['rssc-bitly-user'] && $rsscOptions['rssc-bitly-api'])
	  echo'
	  <script type="text/javascript">
      jQuery(document).ready(function(){
        jQuery("#rssc-shorten").click(function(){
          jQuery.getJSON("http://api.bit.ly/v3/shorten?login='.$rsscOptions['rssc-bitly-user'].'&apiKey='.$rsscOptions['rssc-bitly-api'].'&longUrl='.$rsscPostLink.'&format=json&callback=?", function(data){
            if(data.status_txt == "OK"){
              jQuery("#campaignURL").val(data.data.url);
            }
          });
        });
      });
	  </script>
	  ';
		$twitterlink = "http://twitter.com/home?status=".urlencode($post->post_title." ").$rsscPostLink;
		printf(__("Click <a href=\"%s\" target=\"_blank\">here</a> to publish your post at twitter.", "rsscampaign"), $twitterlink);
		echo "<br>";
		_e('Your Campain Link is:', 'rsscampaign'); if($rsscOptions['rssc-bitly-user'] && $rsscOptions['rssc-bitly-api']) echo" (<span id=\"rssc-shorten\">".__('Shorten this Link', 'rsscampaign')."</span>)"; echo "<br>";
		echo '<input type="text" name="campaignURL" id="campaignURL" value="'.urldecode($rsscPostLink).'" style="width:100%" />';
	}
	else _e('Wait for Post publishing', 'rsscampaign');
}
// END Area in new Posts

// New Field in new-post.php
function post_rssc_meta(){
	add_meta_box('rssc', __('Twitter with Campaign'), 'rssc_post_twitter_meta', 'post', 'normal');
}

$rsscOptions = get_option('rssc');

add_filter( 'post_link', 'rss_campaign_post_link');
add_action(	'admin_menu', 'rss_campaign_admin');
if($rsscOptions['rssc-twitter-enable']) add_action('admin_init', 'post_rssc_meta');

$plugindir = basename(dirname(__FILE__));
load_plugin_textdomain( 'rsscampaign', 'wp-content/plugins/' . $plugindir.'/lang', false );
 if(is_admin() && (($_GET['page'] == 'rss-campaign') || substr(basename($_SERVER['REQUEST_URI']), 0, 8) == 'post.php') ) {
	wp_enqueue_script('jquery');
	wp_enqueue_script('rssc', WP_CONTENT_URL .'/plugins/'. $plugindir. '/js/rssc.js',  array('jquery'));
	wp_enqueue_style('rssc', WP_CONTENT_URL .'/plugins/'. $plugindir. '/css/rssc.css');
 }
?>