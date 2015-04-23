<?php
/*
Plugin Name: S3Bubble Cloud Video With Adverts & Analytics
Plugin URI: https://www.s3bubble.com/
Description: S3Bubble offers simple, secure media streaming from Amazon S3 to WordPress and adding your very own adverts with analytics. In just 4 simple steps. 
Version: 0.6
Author: S3Bubble
Author URI: https://s3bubble.com/
License: GPL2
*/ 
 
/*  Copyright YEAR  Samuel East  (email : mail@samueleast.co.uk)

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


if (!class_exists("s3bubble_video_adverts")) {
	class s3bubble_video_adverts {

		/*
		 * Class properties
		 * @author sameast
		 * @params noen
		 */ 
        public  $s3bubble_video_adverts_accesskey   = '';
		public  $s3bubble_video_adverts_secretkey   = '';
		public  $s3bubble_video_adverts_bar_colours = '#adadad';
		public  $s3bubble_video_adverts_bar_seeks   = '#dd0000';
		public  $s3bubble_video_adverts_controls_bg = '#010101';
		public  $s3bubble_video_adverts_icons       = '#FFFFFF';
		public  $version                            = 6;
		private $endpoint                           = 'https://api.s3bubble.com/v1/';
		
		/*
		 * Constructor method to intiat the class
		 * @author sameast
		 * @params none
		 */ 
		function s3bubble_video_adverts(){
			
			/*
			 * Add default option to database
			 * @author sameast
			 * @params none
			 */ 
			add_option("s3bubble_video_adverts_accesskey", $this->s3bubble_video_adverts_accesskey);
			add_option("s3bubble_video_adverts_secretkey", $this->s3bubble_video_adverts_secretkey);
			add_option("s3bubble_video_adverts_bar_colours", $this->s3bubble_video_adverts_bar_colours);
			add_option("s3bubble_video_adverts_bar_seeks", $this->s3bubble_video_adverts_bar_seeks);
			add_option("s3bubble_video_adverts_controls_bg", $this->s3bubble_video_adverts_controls_bg);
			add_option("s3bubble_video_adverts_icons", $this->s3bubble_video_adverts_icons);
			

			/*
			 * Run the add admin menu class
			 * @author sameast
			 * @params none
			 */ 
			add_action('admin_menu', array( $this, 's3bubble_video_adverts_admin_menu' ));
			
			/*
			 * Add css to the header of the document
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'wp_head', array( $this, 's3bubble_video_adverts_css' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 's3bubble_video_adverts_javascript' ), 11 );
			
			/*
			 * Add javascript to the frontend footer connects to wp_footer
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'admin_enqueue_scripts', array( $this, 's3bubble_video_adverts_admin_scripts' ) );
			
			/*
			 * Setup shortcodes for the plugin
			 * @author sameast
			 * @params none
			 */ 
			add_shortcode( 's3bubbleVideoAdvert', array( $this, 's3bubble_video_adverts_player' ) );	
			
			/*
			 * Tiny mce button for the plugin
			 * @author sameast
			 * @params none
			 */
			add_action( 'init', array( $this, 's3bubble_video_adverts_wysiwyg_buttons' ) );
			add_action( 'wp_ajax_s3bubble_video_adverts_wysiwyg_ajax', array( $this, 's3bubble_video_adverts_wysiwyg_ajax' ) );

			/*
			 * Internal Ajax
			 */
			add_action( 'wp_ajax_s3bubble_video_adverts_internal_ajax', array( $this, 's3bubble_video_adverts_internal_ajax' ) );
			add_action('wp_ajax_nopriv_s3bubble_video_adverts_internal_ajax', array( $this, 's3bubble_video_adverts_internal_ajax' ) ); 

			/*
			 * Admin dismiss message
			 */
			add_action('admin_notices', array( $this, 's3bubble_adverts_admin_notice' ) );
			add_action('admin_init', array( $this, 's3bubble_adverts_nag_ignore' ) );

		}

		/*
		* Sets up a admin alert notice
		* @author sameast
		* @none
		*/ 
		function s3bubble_adverts_admin_notice() {
			global $current_user ;
	        $user_id = $current_user->ID;
	        $params = array_merge($_GET, array("s3bubble_adverts_nag_ignore" => 0));
			$new_query_string = http_build_query($params); 
		    /* Check that the user hasn't already clicked to ignore the message */
			if ( ! get_user_meta($user_id, 's3bubble_adverts_nag_ignore') ) {
		        echo '<div class="updated"><p>'; 
		        echo 'Thankyou for upgrading your S3Bubble media streaming plugin. Any issues please contact us at <a href="mailto:support@s3bubble.com">support@s3bubble.com</a> if you are stuck you can always roll back within the S3Bubble WP admin download and re-install the old plugin. Want to see the great new features please watch this video <a href="https://s3bubble.com/video_tutorials/s3bubble-plugin-upgrade/" target="_blank">Watch Video</a>. | <a href="' . $_SERVER['PHP_SELF'] . "?" . $new_query_string . '" class="pull-right">Hide Notice</a>';
		        echo "</p></div>";
			}
		}

		/*
		* Allows users to ignore the message
		* @author sameast
		* @none
		*/ 
		function s3bubble_adverts_nag_ignore() {
			global $current_user;
		        $user_id = $current_user->ID;
		        /* If user clicks to ignore the notice, add that to their user meta */
		        if ( isset($_GET['s3bubble_adverts_nag_ignore']) && '0' == $_GET['s3bubble_adverts_nag_ignore'] ) {
		             add_user_meta($user_id, 's3bubble_adverts_nag_ignore', 'true', true);
			}
		}
        
		/*
		* Adds the menu item to the wordpress admin
		* @author sameast
		* @none
		*/ 
        function s3bubble_video_adverts_admin_menu(){	
			add_menu_page( 's3bubble_video_adverts', 'S3Bubble Adverts', 'manage_options', 's3bubble_video_adverts', array($this, 's3bubble_video_adverts_admin'), plugins_url('assets/images/s3bubblelogo.png',__FILE__ ) );
    	}
        
        /*
		* Add javascript to the admin header
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_admin_scripts(){
			
			// Css
			wp_register_style( 's3bubble.video.all.admin', plugins_url('assets/css/s3bubble.video.all.admin.min.css', __FILE__), array(), $this->version );
			wp_register_style( 's3bubble.video.all.plugin', plugins_url('assets/css/s3bubble.video.all.plugin.min.css', __FILE__), array(), $this->version );
			
			
			wp_enqueue_style('s3bubble.video.all.admin');
			wp_enqueue_style('s3bubble.video.all.plugin');
			
			// Javascript
			wp_enqueue_script( 's3bubble.video.adverts.plugin', plugins_url( 'assets/js/s3bubble.video.adverts.plugin.min.js', __FILE__ ), array( ), false, true ); 
			wp_enqueue_style( 'wp-color-picker' );
			// Javascript
			wp_enqueue_script( 's3bubble.video.all.colour', plugins_url( 'assets/js/s3bubble.video.all.colour.min.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
			
		} 
		
		/*
		* Add css ties into wp_head() function
		* @author sameast
		* @params none
        */ 
		function s3bubble_video_adverts_css(){

			$progress	= get_option("s3bubble_video_adverts_bar_colours");
			$background	= get_option("s3bubble_video_adverts_controls_bg");
			$seek	    = get_option("s3bubble_video_adverts_bar_seeks");
			$icons	    = get_option("s3bubble_video_adverts_icons");
			
			wp_register_style( 'font-s3bubble.min', plugins_url('assets/css/font-awesome.min.css', __FILE__), array(), $this->version );
			wp_register_style( 's3bubble.video.all.main', plugins_url('assets/css/s3bubble.video.all.main.min.css', __FILE__), array(), $this->version );
			
			wp_enqueue_style('font-s3bubble.min');
			wp_enqueue_style('s3bubble.video.all.main');
			
			echo '<style type="text/css">
                    .s3bubble-media-main-progress, .s3bubble-media-main-volume-bar {background-color: '.stripcslashes($progress).' !important;}
					.s3bubble-media-main-play-bar, .s3bubble-media-main-volume-bar-value {background-color: '.stripcslashes($seek).' !important;}
					.s3bubble-media-main-interface, .s3bubble-media-main-video-play {background-color: '.stripcslashes($background).' !important;color: '.stripcslashes($icons).' !important;}
					.s3bubble-media-main-video-loading {color: '.stripcslashes($icons).' !important;}
					.s3bubble-media-main-interface  > * a, .s3bubble-media-main-current-time, .s3bubble-media-main-duration, .time-sep {color: '.stripcslashes($icons).' !important;}
			</style>';
			
		}
		
		/*
		* Add javascript to the footer connect to wp_footer()
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_javascript(){

			wp_register_script( 's3player.all.s3bubble', plugins_url('assets/js/s3player.all.main.player.min.js',__FILE__ ), array('jquery'), $this->version, true  );
			wp_localize_script('s3player.all.s3bubble', 's3bubble_advert_object', array(
				's3appid' => get_option("s3bubble_video_adverts_accesskey"),
				'serveraddress' => $_SERVER['REMOTE_ADDR']
			));
			wp_register_script( 's3bubble.mobile.browser.check', plugins_url('assets/js/mobile.browser.check.min.js',__FILE__ ), array('jquery'),  $this->version, true );
			wp_register_script( 's3bubble.analytics.min', plugins_url('assets/js/s3analytics.min.js',__FILE__ ), array('jquery'),  $this->version, true );
			
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-migrate');
			wp_enqueue_script('s3player.all.s3bubble');
			wp_enqueue_script('s3bubble.mobile.browser.check');
			wp_enqueue_script('s3bubble.analytics.min');

		}

		/*
		* Video playlist internal ajax call
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_internal_ajax(){
			
			$s3bubble_video_adverts_accesskey	= get_option("s3bubble_video_adverts_accesskey");
			$s3bubble_video_adverts_secretkey	= get_option("s3bubble_video_adverts_secretkey");

			//set POST variables
			$url = $this->endpoint . 'main_plugin/single_video_object';
			$fields = array(
				'AccessKey' => $s3bubble_video_adverts_accesskey,
			    'SecretKey' => $s3bubble_video_adverts_secretkey,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $_POST['Bucket'],
			    'Key' => $_POST['Key'],
			    'Cloudfront' => $_POST['Cloudfront']
			);

			if(!function_exists('curl_init')){
    			echo json_encode(array("error" => "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>"));
    			exit();
    		}
			
			//open connection
			$ch = curl_init();
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			//execute post
		    $result = curl_exec($ch);
			echo $result;
			curl_close($ch);

			die();	
			
		}

        /*
		* Audio playlist button callback
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_wysiwyg_ajax(){
		    // echo the form
		    $s3bubble_access_key = get_option("s3bubble_video_adverts_accesskey");
		    ?>
		    <script type="text/javascript">
		        jQuery( document ).ready(function( $ ) {
		        	$('#TB_ajaxContent').css({
                    	'width' : 'auto',
                    	'height' : '450px'
                    });
			        var sendData = {
						AccessKey: '<?php echo $s3bubble_access_key; ?>'
					};
					$.post("<?php echo $this->endpoint; ?>main_plugin/live_buckets/", sendData, function(response) {
						console.log(response);
						if(response.error){
							$(".s3bubble-video-main-form-alerts").html("<p>Oh Snap! " + response.message + ". If you do not understand this error please contact support@s3bubble.com</p>");
						}else{
							$(".s3bubble-video-main-form-alerts").html("<p>Awesome! " + response.message + ".</p>");
							var isSingle = response.data.Single;
							var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
						    $.each(response.data.Buckets, function (i, item) {
						    	var bucket = item.Name;
						    	if(isSingle === true){
						    		html += '<option value="s3bubble.users">' + bucket + '</option>';
						    	}else{
						    		html += '<option value="' + bucket + '">' + bucket + '</option>';	
						    	}
							});
							html += '</select>';
							$('#s3bubble-buckets-shortcode').html(html);
						}
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/assets/js/ajax-loader.gif',__FILE__); ?>"/> loading videos files');
						   var bucket = $(this).val();
						   if(isSingle === true){
						   		bucket = $("#s3bucket option:selected").text();
						   }
							var data = {
								AccessKey: '<?php echo $s3bubble_access_key; ?>',
								Bucket: bucket
							};
							$.post("<?php echo $this->endpoint; ?>main_plugin/video_files/", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose video</option>';
							    $.each(response, function (i, item) {
							    	if(isSingle === true){
										html += '<option value="' + item + '">' + item + '</option>';
									}else{
								    	var folder = item.Key;
								    	var ext    = folder.split('.').pop();
								    	if(ext == 'mp4' || ext === 'm4v'){
								    		html += '<option value="' + folder + '">' + folder + '</option>';
								    	}
								    }
								});
								html += '</select>';
								$('#s3bubble-folders-shortcode').html(html);
						   },'json');
						});				
					},'json');
			        $('#s3bubble-mce-submit').click(function(){
			        	var bucket     = $('#s3bucket').val();
			        	var folder     = $('#s3folder').val();
			        	var link       = $('#s3bubble-advert-link').val();
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						var aspect = '16:9';
						if($('#s3aspect').val() != ''){
						    aspect = $('#s3aspect').val();
						}
						var shortcode = '[s3bubbleVideoAdvert bucket="' + bucket + '" track="' + folder + '" aspect="' + aspect + '" autoplay="' + autoplay + '" link="' + link + '"/]';
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <form class="s3bubble-form-general">
                <div class="s3bubble-video-main-form-alerts"></div>
		    	<p>
			    	<span class="s3bubble-pull-left" id="s3bubble-buckets-shortcode">loading buckets...</span>
					<span class="s3bubble-pull-right" id="s3bubble-folders-shortcode"></span>
				</p>
				<p>
					<span class="s3bubble-pull-right">
				    	<label for="fname">Advert Link</label><input type="text" class="s3bubble-form-input" name="s3bubble-advert-link" id="s3bubble-advert-link">
				    </span>
					<span class="s3bubble-pull-left">
				    	<label for="fname">Aspect Ratio: (Example: 16:9 / 4:3 Default: 16:9)</label><input type="text" class="s3bubble-form-input" name="aspect" id="s3aspect">
				    </span>
				</p> 
                <blockquote class="bs-callout-s3bubble"><strong>Extra options:</strong> please just select any extra options from the list below, and S3Bubble will automatically add it to the shortcode.</blockquote>
				<p><input type="checkbox" name="autoplay" id="s3autoplay">Autoplay <i>(Start Video On Page Load)</i><br />
				<p>
					<a href="#"  id="s3bubble-mce-submit" class="s3bubble-pull-right button media-button button-primary button-large media-button-gallery">Insert Shortcode</a>
				</p>
			</form>
        	<?php
        	die();
		}
        
		/*
		* Sets up tiny mce plugins
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_wysiwyg_buttons() {
			if ( current_user_can( 'manage_options' ) )  {
				add_filter( 'mce_external_plugins', array( $this, 's3bubble_video_adverts_wysiwyg_add_buttons' ) ); 
				add_filter( 'mce_buttons', array( $this, 's3bubble_video_adverts_wysiwyg_register_buttons' ) );
			} 
		}
		
		/*
		* Adds the menu item to the tiny mce
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_wysiwyg_add_buttons( $plugin_array ) {
		    $plugin_array['s3bubbleVideoAdverts'] = plugins_url('/assets/js/s3bubble.video.adverts.plugin.js',__FILE__);
		    return $plugin_array;
		}
		
		/*
		* Registers the amount of buttons
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_wysiwyg_register_buttons( $buttons ) {
		    array_push( $buttons, 's3bubble_video_adverts_wysiwyg_video_shortcode' ); 
		    return $buttons;
		}
        
		/*
		* Add javascript to the footer connect to wp_footer()
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_admin(){	
			if ( isset($_POST['submit']) ) {
				$nonce = $_REQUEST['_wpnonce'];
				if (! wp_verify_nonce($nonce, 'isd-updatesettings') ) die('Security check failed'); 
				if (!current_user_can('manage_options')) die(__('You cannot edit the search-by-category options.'));
				check_admin_referer('isd-updatesettings');	
				// Get our new option values
				$s3bubble_video_adverts_accesskey	= $_POST['s3bubble_video_adverts_accesskey'];
				$s3bubble_video_adverts_secretkey	= $_POST['s3bubble_video_adverts_secretkey'];
				$s3bubble_video_adverts_bar_colours	= $_POST['s3bubble_video_adverts_bar_colours'];
				$s3bubble_video_adverts_bar_seeks	= $_POST['s3bubble_video_adverts_bar_seeks'];
				$s3bubble_video_adverts_controls_bg	= $_POST['s3bubble_video_adverts_controls_bg'];
				$s3bubble_video_adverts_icons	    = $_POST['s3bubble_video_adverts_icons'];

			    // Update the DB with the new option values
				update_option("s3bubble_video_adverts_accesskey", $s3bubble_video_adverts_accesskey);
				update_option("s3bubble_video_adverts_secretkey", $s3bubble_video_adverts_secretkey);
				update_option("s3bubble_video_adverts_bar_colours", $s3bubble_video_adverts_bar_colours);
				update_option("s3bubble_video_adverts_bar_seeks", $s3bubble_video_adverts_bar_seeks);
				update_option("s3bubble_video_adverts_controls_bg", $s3bubble_video_adverts_controls_bg);
				update_option("s3bubble_video_adverts_icons", $s3bubble_video_adverts_icons);

			}
			
			$s3bubble_video_adverts_accesskey	= get_option("s3bubble_video_adverts_accesskey");
			$s3bubble_video_adverts_secretkey	= get_option("s3bubble_video_adverts_secretkey");
			$s3bubble_video_adverts_bar_colours	= get_option("s3bubble_video_adverts_bar_colours");
			$s3bubble_video_adverts_bar_seeks	= get_option("s3bubble_video_adverts_bar_seeks");
			$s3bubble_video_adverts_controls_bg	= get_option("s3bubble_video_adverts_controls_bg");
			$s3bubble_video_adverts_icons	    = get_option("s3bubble_video_adverts_icons");

		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>S3Bubble Amazon S3 Video & Custom Advertising</h2>
			<div id="message" class="updated fade"><p>Please sign up for a S3Bubble account at <a href="https://s3bubble.com" target="_blank">https://s3bubble.com</a></p></div>
			<div class="metabox-holder has-right-sidebar">
				<div class="inner-sidebar" style="width:40%">
					<div class="postbox">
						<h3 class="hndle">PLEASE USE WYSIWYG EDITOR BUTTONS</h3>
						<div class="inside">
							<img style="width: 100%;" src="https://isdcloud.s3.amazonaws.com/wp_editor.png" />
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">Track Video Analytics</h3>
						<div class="inside">
							<ul class="s3bubble-adverts">
								<li>
									<img src="<?php echo plugins_url('/assets/images/analytics.png',__FILE__); ?>" alt="S3Bubble wordpress plugin" /> 
									<p>S3Bubble is excited to present to you its first consumer analytics page. All your videos that display on your WordPress site will now link to our management system. Find out where your target audience is so you can start strategically promoting your site and grow a global audience.</p>
									<a href="https://s3bubble.com/" target="_blank">Visit s3bubble.com</a>
								</li>
							</ul>        
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">S3Bubble Support</h3>
						<div class="inside">
							<ul class="s3bubble-adverts">
								<li>
									<img src="<?php echo plugins_url('/assets/images/support.png',__FILE__); ?>" alt="S3Bubble iPhone" /> 
									<h3>
										Are you stuck upgraded and not happy?
									</h3>
									<p>If you are stuck at any point or preferred the old version please just click the download below and delete this version and re upload the plugin.</p>
									<a class="button button-s3bubble" href="https://s3.amazonaws.com/s3bubble.assets/video.adverts/s3bubble-amazon-s3-html-5-video-with-adverts.old.zip" target="_blank">DOWNLOAD OLD VERISON</a>
								</li>
							</ul>        
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">S3Bubble Mobile Apps - Monitor Analytics</h3>
						<div class="inside">
							<ul class="s3bubble-adverts">
								<li>
									<img src="<?php echo plugins_url('/assets/images/plugin-mobile-icon.png',__FILE__); ?>" alt="S3Bubble iPhone" /> 
									<h3>
										iPhone Mobile App
									</h3>
									<p>Record Manage Watch Download Share. Manage all your video and audio analytics.</p>
									<a class="button button-s3bubble" href="https://itunes.apple.com/us/app/s3bubble/id720256052?ls=1&mt=8" target="_blank">GET THE APP</a>
								</li>
								<li>
									<img src="<?php echo plugins_url('/assets/images/plugin-mobile-icon.png',__FILE__); ?>" alt="S3Bubble Android" /> 
									<h3>
										Android Mobile App
									</h3>
									<p>Record Manage Watch Download Share. Manage all your video and audio analytics.</p>
									<a class="button button-s3bubble" href="https://play.google.com/store/apps/details?id=com.s3bubble" target="_blank">GET THE APP</a>
								</li>
							</ul>        
						</div> 
					</div>
				</div>
				<div id="post-body">
					<div id="post-body-content" style="margin-right: 41%;">
						<div class="postbox">
							<h3 class="hndle">Fill in details below if stuck please <a class="button button-s3bubble" style="float: right;margin: -5px -10px;" href="https://www.youtube.com/watch?v=VFG3-nvV6F0" target="_blank">Watch Video</a></h3>
							<div class="inside">
								<form action="" method="post" class="s3bubble-video-popup-form" style="overflow: hidden;">
								    <table class="form-table">
								      <?php if (function_exists('wp_nonce_field')) { wp_nonce_field('isd-updatesettings'); } ?>
								       <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_adverts_accesskey">App Access Key:</label></th>
								        <td><input type="text" name="s3bubble_video_adverts_accesskey" id="s3bubble_video_adverts_accesskey" class="regular-text" value="<?php echo $s3bubble_video_adverts_accesskey; ?>"/>
								        	<br />
								       <span class="description">App Access Key can be found <a href="https://s3bubble.com/admin/#/apps" target="_blank">here</a></span>	
								        </td>
								      </tr> 
								       <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_adverts_secretkey">App Secret Key:</label></th>
								        <td><input type="password" name="s3bubble_video_adverts_secretkey" id="s3bubble_video_adverts_secretkey" class="regular-text" value="<?php echo $s3bubble_video_adverts_secretkey; ?>"/>
								        	<br />
								        	<span class="description">App Secret Key can be found <a href="https://s3bubble.com/admin/#/apps" target="_blank">here</a></span>
								        </td>
								      </tr> 
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_adverts_bar_colours">Player Bar Colours:</label></th>
								        <td> <input type="text" name="s3bubble_video_adverts_bar_colours" id="s3bubble_video_adverts_bar_colours" value="<?php echo $s3bubble_video_adverts_bar_colours; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the progress bar and volume bar colour</span>
								        </td>
								      </tr>
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_adverts_bar_seeks">Seek Bar Colours:</label></th>
								        <td> <input type="text" name="s3bubble_video_adverts_bar_seeks" id="s3bubble_video_adverts_bar_seeks" value="<?php echo $s3bubble_video_adverts_bar_seeks; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the progress bar and volume bar seek bar colours</span>
								        </td>
								      </tr>
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_adverts_controls_bg">Player Controls Colour:</label></th>
								        <td> <input type="text" name="s3bubble_video_adverts_controls_bg" id="s3bubble_video_adverts_controls_bg" value="<?php echo $s3bubble_video_adverts_controls_bg; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the controls background colour</span>
								        </td>
								      </tr> 
								      <tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_adverts_icons">Player Icon Colours:</label></th>
								        <td> <input type="text" name="s3bubble_video_adverts_icons" id="s3bubble_video_adverts_icons" value="<?php echo $s3bubble_video_adverts_icons; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the player icons colours</span>
								        </td>
								      </tr>  
								    </table>
								    <br/>
								    <span class="submit" style="border: 0;">
								    <input type="submit" name="submit" class="button button-s3bubble button-hero" value="Save Settings" />
								    </span>
								  </form>
							</div><!-- .inside -->
						</div>
					</div> <!-- #post-body-content -->
				</div> <!-- #post-body -->
			</div> <!-- .metabox-holder -->
		</div> <!-- .wrap -->
		<?php	
       } 
	   
		/*
		* Run the s3bubble jplayer video playlist function
		* @author sameast
		* @none
		*/ 
        function s3bubble_video_adverts_player($atts){	
			
        	// get option from database	
			$loggedin            = get_option("s3-loggedin");
			$search              = get_option("s3-search");
			$responsive          = get_option("s3-responsive");
			$stream              = get_option("s3-stream");
	        extract( shortcode_atts( array(
				'playlist'   => '',
				'height'     => '',
				'track'      => '',
				'bucket'     => '',
				'folder'     => '',
				'cloudfront' => '',
				'download'   => 'false',
				'aspect'     => '16:9',
				'link'       => '',
				'responsive' => $responsive,
				'autoplay'   => 'false',
			), $atts, 's3bubbleVideoAdvert' ) );
			
			// Check download
			if($loggedin == 'true'){
				if ( is_user_logged_in() ) {
					$download = 1;
				}else{
					$download = 0;
				}
			}
            $player_id = uniqid();
		
            return '<div id="s3bubble-media-main-container-' . $player_id . '" class="s3bubble-media-main-video">
			    <div id="jquery_jplayer_' . $player_id . '" class="s3bubble-media-main-jplayer"></div>
			    <div class="s3bubble-media-main-video-skip">
					<h2>Skip Ad</h2>
					<i class="s3icon s3icon-step-forward"></i>
					<img id="s3bubble-media-main-skip-tumbnail" src=""/>
				</div>
			    <div class="s3bubble-media-main-video-loading">
			    	<i class="s3icon s3icon-refresh s3icon-spin"></i>
			    </div>
			    <div class="s3bubble-media-main-video-play">
					<i class="s3icon s3icon-play"></i>
				</div>
			    <div class="s3bubble-media-main-gui">
			        <div class="s3bubble-media-main-interface">
			            <div class="s3bubble-media-main-controls-holder">
							<a href="javascript:;" class="s3bubble-media-main-play" tabindex="1"><i class="s3icon s3icon-play"></i></a>
							<a href="javascript:;" class="s3bubble-media-main-pause" tabindex="1"><i class="s3icon s3icon-pause"></i></a>
							<div class="s3bubble-media-main-progress" dir="auto">
							    <div class="s3bubble-media-main-seek-bar" dir="auto">
							        <div class="s3bubble-media-main-play-bar" dir="auto"><span></span></div>
							    </div>
							</div>
							<a href="javascript:;" class="s3bubble-media-main-full-screen" tabindex="3" title="full screen"><i class="s3icon s3icon-arrows-alt"></i></a>
							<a href="javascript:;" class="s3bubble-media-main-restore-screen" tabindex="3" title="restore screen"><i class="s3icon s3icon-arrows-alt"></i></a>
							<div class="s3bubble-media-main-volume-bar" dir="auto">
							    <div class="s3bubble-media-main-volume-bar-value" dir="auto"><span class="handle"></span></div>
							</div>
							<a href="javascript:;" class="s3bubble-media-main-mute" tabindex="2" title="mute"><i class="s3icon s3icon-volume-up"></i></a>
							<a href="javascript:;" class="s3bubble-media-main-unmute" tabindex="2" title="unmute"><i class="s3icon s3icon-volume-off"></i></a>
							<div class="s3bubble-media-main-time-container">
								<div class="s3bubble-media-main-duration"></div>
							</div>
			            </div>
			        </div>
			    </div>
			    <div class="s3bubble-media-main-playlist" style="display:none !important;">
					<ul>
						<li></li>
					</ul>
				</div>
			    <div class="s3bubble-media-main-no-solution" style="display:none;">
			        <span>Update Required</span>
			        Flash player is needed to use this player please download here. <a href="https://get2.adobe.com/flashplayer/" target="_blank">Download</a>
			    </div>
			</div>
            <script type="text/javascript">
				jQuery(document).ready(function($) {
					
					var S3Bucket = "' . $bucket. '";
					var Current  = -1;
					var link     = "' . $link . '";
					var aspects  = "' . $aspect . '";
					var aspects  = aspects.split(":");
					var aspect   = $("#s3bubble-media-main-container-' . $player_id . '").width()/aspects[0]*aspects[1];
				
					var IsMobile = false;
					if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
						IsMobile = true;
					}
					var videoAdvertS3bubble = new jPlayerPlaylist({
						jPlayer: "#jquery_jplayer_' . $player_id . '",
						cssSelectorAncestor: "#s3bubble-media-main-container-' . $player_id . '"
					}, videoAdvertS3bubble, {
						playlistOptions : {
							autoPlay : '.$autoplay.',
							downloadSet: '.$download.'
						},
						ready : function(event) {
							// Add Responsive
							var main_width = $("#s3bubble-media-main-container-' . $player_id . '").width();
							if(main_width < 400){
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-volume-bar").hide();
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-mute").hide();
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-unmute").hide();
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").width(main_width-160);	
							}else{
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-mute").show();
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-volume-bar").show();
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").width(main_width-280);	
							}
							$( window ).resize(function() {
								var main_width_resize = $("#s3bubble-media-main-container-' . $player_id . '").width();
								if(main_width_resize < 400){
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-mute").hide();
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-unmute").hide();
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-volume-bar").hide();
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").width(main_width_resize-160);	
								}else{
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-mute").show();
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-volume-bar").show();
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").width(main_width_resize-280);	
								}
							});
							var sendData = {
								"action": "s3bubble_video_adverts_internal_ajax",
								"Timezone":"America/New_York",
							    "Bucket" : "' . $bucket. '",
							    "Key" : "' . $track. '",
							    "Cloudfront" : "' . $cloudfront .'"
							}
							$.post("' . admin_url('admin-ajax.php') . '", sendData, function(response) {
								if(response.error){
									console.log(response.message);   
								}else{
									$("#s3bubble-media-main-container-' . $player_id . ' #s3bubble-media-main-skip-tumbnail").attr("src", response.results[0].poster);
									videoAdvertS3bubble.setPlaylist(response.results);
									$("video").bind("contextmenu", function(e) {
										return false;
									}); 
									$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-skip").on( "click", function() {
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn();
										$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-skip").animate({
										    left: "-230"
										}, 50, function() {
										    videoAdvertS3bubble.next();
										});
										
									});
								}
							},"json");
						},
						timeupdate : function(t) {
							if (t.jPlayer.status.currentTime > 1) {
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
							}
						},
						resize: function (event) {
					    	$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").fadeOut();
					    	setTimeout(function(){
					    		$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-progress").width($("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-gui").width()-280).fadeIn();	
					    	},2000);
					    },
					    click: function (event) {
					    	var CurrentState = videoAdvertS3bubble.current;
							var PlaylistKey  = videoAdvertS3bubble.playlist[CurrentState];
							if(PlaylistKey.advert && link){
								var win = window.open(link, "_blank");
  								win.focus();
							}else{
						    	if(event.jPlayer.status.paused){
						    		videoAdvertS3bubble.play();
						    	}else{
						    		videoAdvertS3bubble.pause();
						    	}
						    }
					    },
					    error: function (event) {
					    	console.log(event.jPlayer.error);
        					console.log(event.jPlayer.error.type);
					    },
						loadedmetadata : function(t) {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
						},
						loadeddata : function(t) {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut();
						},
						emptied : function(t) {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn()
						},
						ended : function(t) {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn();
							var CurrentState = videoAdvertS3bubble.current;
							var PlaylistKey  = videoAdvertS3bubble.playlist[CurrentState];
							if(PlaylistKey.advert && IsMobile === false){
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-skip").animate({
								    left: "0"
								}, 50, function() {
								    // Animation complete.
								});
							}else{
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-skip").animate({
								    left: "-230"
								}, 50, function() {
								    
								});
							}
						},
						stalled : function(t) {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn()
						},
						waiting: function() {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeIn(); 
						},
						canplay: function() {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut(); 
						},
						pause: function() { 

						},
						playing: function() {
							$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut(); 
						},
						play: function() { 
						    $("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-loading").fadeOut(); 
							var CurrentState = videoAdvertS3bubble.current;
							var PlaylistKey  = videoAdvertS3bubble.playlist[CurrentState];
							if(PlaylistKey.advert && IsMobile === false){
								$("#s3bubble-media-main-container-' . $player_id . ' .s3bubble-media-main-video-skip").animate({
								    left: "0"
								}, 50, function() {
								    // Animation complete.
								});
							}
							if(Current !== CurrentState){
								addListener({
									app_id: s3bubble_advert_object.s3appid,
									server: s3bubble_advert_object.serveraddress,
									bucket: "' . $bucket. '",
									key: PlaylistKey.key,
									type: "video",
									advert: false
								});
								Current = CurrentState;
							}

						},
						suspend: function() { 
						    
						},
						keyBindings: {
						  play: {
						    key: 32, // space
						    fn: function(f) {
						      if(f.status.paused) {
						        f.play();
						      } else {
						        f.pause();
						      }
						    }
						  },
						  fullScreen: {
						    key: 70, // f
						    fn: function(f) {
						      if(f.status.video || f.options.audioFullScreen) {
						        f._setOption("fullScreen", !f.options.fullScreen);
						      }
						    }
						  },
						  muted: {
						    key: 77, // m
						    fn: function(f) {
						      f._muted(!f.options.muted);
						    }
						  },
						  volumeUp: {
						    key: 190, // .
						    fn: function(f) {
						      f.volume(f.options.volume + 0.1);
						    }
						  },
						  volumeDown: {
						    key: 188, // ,
						    fn: function(f) {
						      f.volume(f.options.volume - 0.1);
						    }
						  },
						  loop: {
						    key: 76, // l
						    fn: function(f) {
						      f._loop(!f.options.loop);
						    }
						  }
						},
						swfPath: "https://s3.amazonaws.com/s3bubble.davec/jquery.jplayer.swf",
	                    supplied: "m4v",
		                wmode: "window",
		                preload: "metadata",
						useStateClassSkin: true,
						autoBlur: false,
						smoothPlayBar: false,
						keyEnabled: true,
						remainingDuration: true,
						size: {
				            width: "100%",
				            height: aspect
				        },
				        autohide : {
							full : true,
							restored : true,
							hold : 3000
						}
					});
				});
			</script>';
				
			curl_close($ch);

		}

	}
	/*
	* Initiate the class
	* @author sameast
	* @none
	*/ 
	$s3bubble_video_adverts = new s3bubble_video_adverts();

} //End Class s3audible