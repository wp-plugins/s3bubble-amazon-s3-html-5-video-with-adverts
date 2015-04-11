<?php
/*
Plugin Name: S3Bubble Cloud Video With Adverts
Plugin URI: https://www.s3bubble.com/
Description: S3Bubble offers simple, secure media streaming from Amazon S3 to WordPress and adding your very own adverts with analytics. In just 4 simple steps. 
Version: 0.5
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
		public  $s3bubble_video_adverts_bar_colours = '#dd0000';
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
			wp_register_style( 's3bubble.video.advert.admin', plugins_url('assets/css/s3bubble.video.advert.admin.css', __FILE__), array(), $this->version );
			wp_register_style( 's3bubble.video.advert.plugin', plugins_url('assets/css/s3bubble.video.advert.plugin.css', __FILE__), array(), $this->version );
			
			
			wp_enqueue_style('s3bubble.video.advert.admin');
			wp_enqueue_style('s3bubble.video.advert.plugin');
			wp_enqueue_style( 'wp-color-picker' );
			
			// Javascript
			wp_enqueue_script( 's3bubble-admin-js', plugins_url( 'assets/js/s3bubble.admin.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
			
		} 
		
		/*
		* Add css ties into wp_head() function
		* @author sameast
		* @params none
        */ 
		function s3bubble_video_adverts_css(){
			
			wp_register_style( 's3bubble.video.ultimate.plugin', plugins_url('assets/plugins/ultimate/start/content/global.css', __FILE__), array(), $this->version );
			wp_enqueue_style('s3bubble.video.ultimate.admin');
			
		}
		
		/*
		* Add javascript to the footer connect to wp_footer()
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_javascript(){
           if (!is_admin()) {

				wp_register_script( 's3bubble.ultimate.video', plugins_url('assets/plugins/ultimate/java/FWDUVPlayer.js',__FILE__ ), array('jquery'), $this->version );
				wp_localize_script('s3bubble.ultimate.video', 's3bubble_advert_object', array(
					's3appid' => get_option("s3bubble_video_adverts_accesskey"),
					'serveraddress' => $_SERVER['REMOTE_ADDR']
				));
				wp_register_script( 'playlist', plugins_url('assets/plugins/video/js/Playlist.js',__FILE__ ), array('jquery'), $this->version );
				wp_register_script( 's3bubble.analytics.min', plugins_url('assets/js/s3analytics.js',__FILE__ ), array('jquery'),  $this->version, true );

				
				wp_enqueue_script('jquery');
				wp_enqueue_script('jquery-migrate');
				wp_enqueue_script('s3bubble.ultimate.video');
				wp_enqueue_script('s3bubble.analytics.min');
				
            } 
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
                    $(".s3bubble-video-form-alerts").html("<p>Grabbing folders please wait...</p>");
			        var sendData = {
						AccessKey: '<?php echo $s3bubble_access_key; ?>'
					};
					$.post("<?php echo $this->endpoint; ?>wp_adverts/buckets/", sendData, function(response) {
						if(response.error){
							$(".s3bubble-video-form-alerts").html("<p>Oh Snap! " + response.message + ". If you do not understand this error please contact support@s3bubble.com</p>");
						}else{
							$(".s3bubble-video-form-alerts").html("<p>Awesome! " + response.message + ".</p>");
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
						   $.post("<?php echo $this->endpoint; ?>wp_adverts/video_files/", data, function(response) {
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
			        	var bucket      = $('#s3bucket').val();
			        	var folder      = $('#s3folder').val();
			        	var skiptime    = $('#s3bubble-video-skip-time').val();
			        	var aspectRatio = $('#s3bubble-video-aspect-ratio').val();
			        	var advertLink  = $('#s3bubble-video-advert-link').val();
			        	var autoplay    = $('#s3autoplay').val();
						var shortcode = '[s3bubbleVideoAdvert bucket="' + bucket + '" key="' + folder + '" autoplay="' + autoplay + '" skip="' + skiptime + '" aspect="' + aspectRatio + '" advert="' + advertLink + '"/]';
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <form class="s3bubble-form-general">
		    	<div class="s3bubble-video-form-alerts"></div>
		    	<p>
			    	<span class="s3bubble-pull-left" id="s3bubble-buckets-shortcode">loading buckets...</span>
					<span class="s3bubble-pull-right" id="s3bubble-folders-shortcode"></span>
				</p>
				<p>
					<span class="s3bubble-pull-left" style="width: 48.5%;">
						<label for="fname">Set Advert Skip Time:</label>
						<input type="text" class="s3bubble-form-input" name="s3bubble-video-skip-time" id="s3bubble-video-skip-time">
				    </span>
				    <span class="s3bubble-pull-right" style="width: 49%;">
				    	<label for="fname">Set Video Aspect Ratio:</label>
				    	<select name="s3bubble-video-aspect-ratio" id="s3bubble-video-aspect-ratio">
						  <option value="16:9" selected>16:9</option>
						  <option value="4:3">4:3</option>
						  <option value="3:2" >3:2</option>
						  <option value="21:9">21:9</option>
						</select>
				    </span>
				    <span class="s3bubble-pull-left" style="width:  48.5%;">
						<label for="fname">Advert Link:</label>
						<input type="text" class="s3bubble-form-input" name="s3bubble-video-advert-link" id="s3bubble-video-advert-link">
				    </span>
				     <span class="s3bubble-pull-right" style="width: 49%;">
				    	<label for="fname">Set Video Autoplay:</label>
				    	<select name="autoplay" id="s3autoplay">
						  <option value="no" selected>No</option>
						  <option value="yes">Yes</option>
						</select>
				    </span>
				</p>
				<input type="hidden" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
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
				$s3bubble_video_adverts_controls_bg	= $_POST['s3bubble_video_adverts_controls_bg'];
				$s3bubble_video_adverts_icons	    = $_POST['s3bubble_video_adverts_icons'];

			    // Update the DB with the new option values
				update_option("s3bubble_video_adverts_accesskey", $s3bubble_video_adverts_accesskey);
				update_option("s3bubble_video_adverts_secretkey", $s3bubble_video_adverts_secretkey);
				update_option("s3bubble_video_adverts_bar_colours", $s3bubble_video_adverts_bar_colours);
				update_option("s3bubble_video_adverts_controls_bg", $s3bubble_video_adverts_controls_bg);
				update_option("s3bubble_video_adverts_icons", $s3bubble_video_adverts_icons);

			}
			
			$s3bubble_video_adverts_accesskey	= get_option("s3bubble_video_adverts_accesskey");
			$s3bubble_video_adverts_secretkey	= get_option("s3bubble_video_adverts_secretkey");
			$s3bubble_video_adverts_bar_colours	= get_option("s3bubble_video_adverts_bar_colours");
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
									<a class="button button-s3bubble" href="https://s3.amazonaws.com/s3bubble.assets/video.adverts/s3bubble-amazon-s3-html-5-video-with-adverts.zip" target="_blank">DOWNLOAD OLD VERISON</a>
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
							<h3 class="hndle">Fill in details below if stuck please <a class="button button-s3bubble" style="float: right;margin: -5px -10px;" href="https://www.youtube.com/watch?v=z3DZ1fpXR0I" target="_blank">Watch Video</a></h3>
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
								      <!--<tr>
								        <th scope="row" valign="top"><label for="s3bubble_video_adverts_bar_colours">Player Bar Colours:</label></th>
								        <td> <input type="text" name="s3bubble_video_adverts_bar_colours" id="s3bubble_video_adverts_bar_colours" value="<?php echo $s3bubble_video_adverts_bar_colours; ?>" class="cpa-color-picker" >
								        	<br />
								        	<span class="description">Change the progress bar and volume bar colour</span>
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
								      </tr>-->  
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

			$s3bubble_access_key = get_option("s3bubble_video_adverts_accesskey");
			$s3bubble_secret_key = get_option("s3bubble_video_adverts_secretkey");	
        	extract( shortcode_atts( array(
				'bucket'     => '',
				'key'        => '',
				'time'       => '',
				'skip'       => 'false',
				'autoplay'   => 'false',
				'aspect'     => '16:9',
				'advert'     => ''
			), $atts, 's3bubbleVideoAdvert' ) );

            //set POST variables
			$url = $this->endpoint . 'wp_adverts/advert';
			$fields = array(
				'AccessKey' => $s3bubble_access_key,
			    'SecretKey' => $s3bubble_secret_key,
			    'Timezone' => 'America/New_York',
			    'Bucket' => $bucket,
			    'Key' => $key
			);
			
			if(!function_exists('curl_version')){
    			return "<i>Your hosting does not have PHP curl installed. Please install php curl S3Bubble requires PHP curl to work!</i>";
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
			//execute post
		   $result = json_decode(curl_exec($ch));
           $player_id = uniqid();
		   
		   if($result[0]->advert){
		   		$html = '<li data-thumb-source="' . $result[0]->poster . '" data-video-source="' . $result[0]->m4v . '" data-poster-source="' . $result[0]->poster . '" data-downloadable="yes" data-ads-source="' . $result[0]->adverturl . '" data-ads-page-to-open-url="' . $advert . '" data-ads-page-target="_blank" data-time-to-hold-ads="' . $skip . '"></li>';
		   }else{
		   		$html = '<li data-thumb-source="' . $result[0]->poster . '" data-video-source="' . $result[0]->m4v . '" data-poster-source="' . $result[0]->poster . '" data-downloadable="yes"></li>';
		   }

           return '<script type="text/javascript">jQuery(document).ready(function(o){var e="' . $aspect . '",t=e.split(":"),l=o("#s3bubble-video-' . $player_id . '").width(),n=Math.round(l/t[0]*t[1]);FWDUVPUtils.onReady(function(){new FWDUVPlayer({s3bubbleOnce:!0,s3bubbleBucket:"' . $bucket . '",s3bubbleKey:"' . $key . '",s3bubbleAdvert:"' . $result[0]->advert . '",s3bubbleAppId:s3bubble_advert_object.s3appid,s3bubbleServer:s3bubble_advert_object.serveraddress,instanceName:"player-' . $player_id . '",parentId:"s3bubble-video-' . $player_id . '",playlistsId:"playlists-' . $player_id . '",mainFolderPath:"' . plugins_url('assets/plugins/ultimate/content',__FILE__ ) . '",skinPath:"minimal_skin_dark",displayType:"responsive",facebookAppId:"213684265480896",useDeepLinking:"no",rightClickContextMenu:"developer",addKeyboardSupport:"yes",autoScale:"no",showButtonsToolTip:"yes",stopVideoWhenPlayComplete:"no",autoPlay:"' . $autoplay . '",loop:"no",shuffle:"no",maxWidth:l,maxHeight:n,volume:.8,buttonsToolTipHideDelay:1.5,backgroundColor:"#000000",videoBackgroundColor:"#000000",posterBackgroundColor:"#000000",buttonsToolTipFontColor:"#5a5a5a",showLogo:"no",hideLogoWithController:"yes",logoPosition:"topRight",logoLink:"https://s3bubble.com",logoMargins:5,showPlaylistsButtonAndPlaylists:"no",showPlaylistsByDefault:"no",thumbnailSelectedType:"opacity",startAtPlaylist:0,buttonsMargins:0,thumbnailMaxWidth:350,thumbnailMaxHeight:350,horizontalSpaceBetweenThumbnails:40,verticalSpaceBetweenThumbnails:40,showPlaylistButtonAndPlaylist:"no",showInfoButton:"no",showDownloadButton:"no",showFacebookButton:"no",showEmbedButton:"no",showFullScreenButton:"yes",repeatBackground:"no",controllerHeight:37,controllerHideDelay:3,startSpaceBetweenButtons:10,spaceBetweenButtons:10,scrubbersOffsetWidth:2,mainScrubberOffestTop:16,timeOffsetLeftWidth:2,timeOffsetRightWidth:3,timeOffsetTop:0,volumeScrubberHeight:80,volumeScrubberOfsetHeight:12,timeColor:"#bdbdbd",youtubeQualityButtonNormalColor:"#bdbdbd",youtubeQualityButtonSelectedColor:"#FFFFFF",embedAndInfoWindowCloseButtonMargins:0,borderColor:"#333333",mainLabelsColor:"#FFFFFF",secondaryLabelsColor:"#bdbdbd",shareAndEmbedTextColor:"#5a5a5a",inputBackgroundColor:"#000000",inputColor:"#FFFFFF",openNewPageAtTheEndOfTheAds:"no",adsButtonsPosition:"left",skipToVideoText:"You can skip to video in: ",skipToVideoButtonText:"Skip Ad",adsTextNormalColor:"#bdbdbd",adsTextSelectedColor:"#FFFFFF",adsBorderNormalColor:"#444444",adsBorderSelectedColor:"#FFFFFF"})})});</script>
			<div id="s3bubble-video-' . $player_id . '"></div><ul id="playlists-' . $player_id . '" style="display:none;"><li data-source="playlist-' . $player_id . '" data-playlist-name="S3Bubble Playlist" data-thumbnail-path="' . $result[0]->poster . '"></li></ul><ul id="playlist-' . $player_id . '" style="display:none;">' . $html . '</ul>';
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