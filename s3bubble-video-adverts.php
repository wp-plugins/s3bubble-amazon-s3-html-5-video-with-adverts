<?php
/*
Plugin Name: S3Bubble Amazon S3 HTML 5 Video With Adverts
Plugin URI: https://www.s3bubble.com/
Description: S3Bubble offers simple, secure media streaming from Amazon S3 to WordPress and adding your very own adverts. In just 4 simple steps. 
Version: 0.3
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
        public  $s3bubble_video_adverts_accesskey  = '';
		public  $s3bubble_video_adverts_secretkey  = '';
		public  $version                           = 4;
		private $endpoint                          = 'https://api.s3bubble.com/';
		
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
			 * Add css to the wordpress admin document
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'admin_head', array( $this, 's3bubble_video_adverts_css_admin' ) );
			
			/*
			 * Add javascript to the frontend footer connects to wp_footer
			 * @author sameast
			 * @params none
			 */ 
			add_action( 'admin_footer', array( $this, 's3bubble_video_adverts_javascript_admin' ) );
			
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
		* Add css to wordpress admin to run colourpicker
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_css_admin(){
			wp_register_style( 's3bubble.video.advert.admin', plugins_url('assets/css/s3bubble.video.advert.admin.css', __FILE__), array(), $this->version );
			wp_enqueue_style('s3bubble.video.advert.admin');
			wp_register_style( 's3bubble.video.advert.plugin', plugins_url('assets/css/s3bubble.video.advert.plugin.css', __FILE__), array(), $this->version );
			wp_enqueue_style('s3bubble.video.advert.plugin');
		}
		
        /*
		* Add javascript to the admin header
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_javascript_admin(){

		} 
		
		/*
		* Add css ties into wp_head() function
		* @author sameast
		* @params none
        */ 
		function s3bubble_video_adverts_css(){
			wp_register_style( 'ytube.jplayer', plugins_url('assets/css/ytube.jplayer.css', __FILE__), array(), $this->version );
			wp_enqueue_style('ytube.jplayer');

		}
		
		/*
		* Add javascript to the footer connect to wp_footer()
		* @author sameast
		* @none
		*/ 
		function s3bubble_video_adverts_javascript(){
           if (!is_admin()) {
	            wp_deregister_script( 'jquery' );
	            wp_register_script( 'jquery', '//code.jquery.com/jquery-1.11.0.min.js', false, null);
	            wp_enqueue_script('jquery');
	            wp_register_script( 'jquery-migrate', '//code.jquery.com/jquery-migrate-1.2.1.min.js', false, null);
	            wp_enqueue_script('jquery-migrate');
				wp_register_script( 's3player.adverts.jplayer.min', plugins_url('assets/js/s3player.adverts.jplayer.min.js',__FILE__ ), array(), $this->version );
	            wp_enqueue_script('s3player.adverts.jplayer.min');
				wp_register_script( 's3player.adverts.s3bubble.min', plugins_url('assets/js/s3player.adverts.s3bubble.min.js',__FILE__ ), array(), $this->version );
	            wp_enqueue_script('s3player.adverts.s3bubble.min');
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
			        var sendData = {
						AccessKey: '<?php echo $s3bubble_access_key; ?>'
					};
					$.post("<?php echo $this->endpoint; ?>s3media/buckets/", sendData, function(response) {
						var html = '<select class="form-control input-lg" tabindex="1" name="s3bucket" id="s3bucket"><option value="">Choose bucket</option>';
					    $.each(response.Buckets, function (i, item) {
					    	var bucket = item.Name;
					    	html += '<option value="' + bucket + '">' + bucket + '</option>';
						});
						html += '</select>';
						$('#s3bubble-buckets-shortcode').html(html);
						$( "#s3bucket" ).change(function() {
						   $('#s3bubble-folders-shortcode').html('<img src="<?php echo plugins_url('/assets/js/ajax-loader.gif',__FILE__); ?>"/> loading videos files');
						   var bucket = $(this).val();
							var data = {
								AccessKey: '<?php echo $s3bubble_access_key; ?>',
								Bucket: bucket
							};
							$.post("<?php echo $this->endpoint; ?>s3media/video_files/", data, function(response) {
								var html = '<select class="form-control input-lg" tabindex="1" name="s3folder" id="s3folder"><option value="">Choose video</option>';
							    $.each(response, function (i, item) {
							    	var folder = item.Key;
							    	var ext    = folder.split('.').pop();
							    	if(ext == 'mp4' || ext === 'm4v'){
							    		html += '<option value="' + folder + '">' + folder + '</option>';
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
			        	if($("#s3autoplay").is(':checked')){
						    var autoplay = true;
						}else{
						    var autoplay = false;
						}
						var shortcode = '[s3bubbleVideoAdvert bucket="' + bucket + '" key="' + folder + '" autoplay="' + autoplay + '" time="' + skiptime + '" aspect="' + aspectRatio + '"/]';
	                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
	                    tb_remove();
			        });
		        })
		    </script>
		    <form class="s3bubble-form-general">
		    	<blockquote class="bs-callout-s3bubble"><strong>Please select your bucket and then folder below</strong> when you select your bucket S3Bubble will auto generate a list of folders to choose from.</blockquote>
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
				</p>
				<input type="hidden" class="s3bubble-form-input" name="cloudfront" id="s3cloudfront">
				<blockquote class="bs-callout-s3bubble"><strong>Extra options</strong> please just select any extra options from the list below and S3Bubble will automatically add it to the shortcode.</blockquote>
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


			    // Update the DB with the new option values
				update_option("s3bubble_video_adverts_accesskey", $s3bubble_video_adverts_accesskey);
				update_option("s3bubble_video_adverts_secretkey", $s3bubble_video_adverts_secretkey);

			}
			
			$s3bubble_video_adverts_accesskey	= get_option("s3bubble_video_adverts_accesskey");
			$s3bubble_video_adverts_secretkey	= get_option("s3bubble_video_adverts_secretkey");


		?>
		<style>
			.s3bubble-pre {
				white-space: pre-wrap;
				white-space: -moz-pre-wrap; 
				white-space: -pre-wrap; 
				white-space: -o-pre-wrap; 
				word-wrap: break-word; 
				background: #202020;
				padding: 15px;
				color: white;
			}
		</style>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h2>S3Bubble Amazon S3 Video & Custom Advertising</h2>
			<div id="message" class="updated fade"><p>Please sign up for a S3Bubble account at <a href="https://s3bubble.com" target="_blank">https://s3bubble.com</a></p></div>
			<div class="metabox-holder has-right-sidebar">
				<div class="inner-sidebar" style="width:40%">
					
					<div class="postbox">
						<h3 class="hndle">BRAND NEW WYSIWYG EDITOR BUTTONS</h3>
						<div class="inside">
							<img style="width: 100%;" src="https://isdcloud.s3.amazonaws.com/wp_editor.png" />
						</div> 
					</div>
					
					<div class="postbox">
						<h3 class="hndle">S3Bubble Plugins</h3>
						<div class="inside">
							<ul class="s3bubble-adverts">
								<li>
										<img src="https://s3bubble.com/wp-content/uploads/2014/07/s3streamin.png" alt="S3Bubble wordpress plugin" /> 
										<h3>
											Video Popup WP Plugin
										</h3>
										<p>Add a popup promotional or advertising video.</p>
										<a class="button button-s3bubble" href="https://wordpress.org/plugins/s3bubble-amazon-s3-video-popup/" target="_blank">DOWNLOAD</a>
										<a class="button button-s3bubble" href="https://www.youtube.com/watch?v=GcTLSOvddaM" target="_blank">VIDEO</a>
								</li>
								<li>
			
										<img src="https://s3bubble.com/wp-content/uploads/2014/07/s3streamin.png" alt="S3Bubble wordpress plugin" /> 
										<h3>
											Video Adverts WP Plugin 
											
										</h3>
										<p>Add adverts before your videos & skip time.</p>
										<a class="button button-s3bubble" href="https://wordpress.org/plugins/s3bubble-amazon-s3-html-5-video-with-adverts/" target="_blank">DOWNLOAD</a>
										<a class="button button-s3bubble" href="https://www.youtube.com/watch?v=z3DZ1fpXR0I" target="_blank">VIDEO</a>
								</li>
								<li>
			
										<img src="https://s3bubble.com/wp-content/uploads/2014/07/s3streamin.png" alt="S3Bubble wordpress plugin" /> 
										<h3>
											Wordpress Media Plugin
										</h3>
										<p>Stream media directly onto your wordpress blogs.</p>
										<a class="button button-s3bubble" href="https://wordpress.org/plugins/s3bubble-amazon-s3-audio-streaming/" target="_blank">DOWNLOAD</a>
										<a class="button button-s3bubble" href="https://www.youtube.com/watch?v=EyBTpJ9GJCw" target="_blank">VIDEO</a>
								</li>
								<li>
										<img src="https://s3bubble.com/wp-content/uploads/2014/07/s3backup.png" alt="S3Bubble wordpress backup plugin" />
										<h3>
											Free WP Backup Plugin
										</h3>
										<p>Store your data securely and ensure you website is safe.</p>
										<a class="button button-s3bubble" href="http://wordpress.org/plugins/s3bubble-amazon-s3-backup/" target="_blank">DOWNLOAD</a>
										<a class="button button-s3bubble" href="https://www.youtube.com/watch?v=niqugoI8gis" target="_blank">VIDEO</a>
								</li>
							</ul>        
						</div> 
					</div>
					<div class="postbox">
						<h3 class="hndle">S3Bubble Mobile Apps</h3>
						<div class="inside">
							<ul class="s3bubble-adverts">
								<li>
										<img src="https://s3bubble.com/wp-content/themes/s3audible/img/plugins/iphoneapp.jpg" alt="S3Bubble iPhone" /> 
										<h3>
											iPhone Mobile App
										</h3>
										<p>Record Manage Watch Download Share.</p>
										<a class="button button-s3bubble" href="https://itunes.apple.com/us/app/s3bubble/id720256052?ls=1&mt=8" target="_blank">GET THE APP</a>
								</li>
								<li>
			
										<img src="https://s3bubble.com/wp-content/themes/s3audible/img/plugins/androidapp.png" alt="S3Bubble Android" /> 
										<h3>
											Android Mobile App
											
										</h3>
										<p>Record Manage Watch Download Share.</p>
										<a class="button button-s3bubble" href="https://play.google.com/store/apps/details?id=com.s3bubbleAndroid" target="_blank">GET THE APP</a>
								</li>
								<li>
			
										<img src="https://s3bubble.com/wp-content/themes/s3audible/img/plugins/flix.png" alt="S3Bubble Flix" /> 
										<h3>
											S3Bubble Flix Android
										</h3>
										<p>Create your own personal video network.</p>
										<a class="button button-s3bubble" href="https://play.google.com/store/apps/details?id=com.s3bubbleflixandroid" target="_blank">GET THE APP</a>
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
				'autoplay'   => 'false',
				'aspect'     => '16:9'
			), $atts, 's3bubbleVideoAdvert' ) );

            //set POST variables
			$url = $this->endpoint . 's3stream/advert';
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

           return '<div id="uniquePlayer-' . $player_id . '" class="mediaPlayer mediaAdvert-' . $player_id . ' darkskin"><div class="s3bubble-video-advert-loading"></div><div id="uniqueContainer-' . $player_id . '" class="Player"></div></div>
            <script type="text/javascript">
			jQuery(document).ready(function($) {
				var aspect  = "' . $aspect . '";
				var aspects = aspect.split(":");
				var advert = "' . $result[0]->advert . '";
				var conWidth = $("#uniquePlayer-' . $player_id . '").width();
				var valueHeight = Math.round((conWidth/aspects[0])*aspects[1]);
				var skipTime = "' . $time . '"; 
				var autoplay = "' . $autoplay . '";
				if(advert !== "null" && advert !== ""){
					var advertOnce = true;
					$(".mediaAdvert-' . $player_id . '").mediaPlayer({
						media: {
							m4v: "' . $result[0]->advert . '",
							poster: "' . $result[0]->thumbImg . '"
						},
	                    ended: function () {
	                    	$(".s3bubble-video-advert-loading").fadeIn();
	                    	$(".skipAdvert").hide(); 
							$("#uniqueContainer-' . $player_id . '").jPlayer("setMedia", {
								m4v: "' . $result[0]->mp4 . '",
								poster: "' . $result[0]->thumbImg . '"
							});
							$("#uniqueContainer-' . $player_id . '").jPlayer( "play" );
						},
						size: {
							width: "100%",
							height: valueHeight
						},
						loadstart: function() {
							if(autoplay === "true"){
								$("#uniqueContainer-' . $player_id . '").jPlayer( "play" );
							}
						}
					});
					$("video").bind("contextmenu", function(e) {
					   return false;
					});
					$(".video-play").click(function() {
					  	if(advertOnce === true){ 
							$(".skipAdvert").show();
							if(skipTime !== ""){
								var second = 1; 
								var skipTimePas = parseInt(skipTime) + 2; 
								var skipTimeInt = setInterval(function(){
									var secs = ++second;
									$(".skipAdvert").html("Skip Advert in " + (skipTimePas - secs));
									if((skipTimePas - secs) === 0){
										clearInterval(skipTimeInt);
										$(".skipAdvert").html("Skip Advert");
										$(".skipAdvert").addClass("open");
									}
								}, 1000);
							}else{
								$(".skipAdvert").html("Skip Advert");
								$(".skipAdvert").addClass("open");
							}
							advertOnce = false;
						}
					});
					$(".skipAdvert").click(function() {
						if ( $(this).hasClass("open") ) {
						  	$(".skipAdvert").hide(); 
							$("#uniqueContainer-' . $player_id . '").jPlayer("setMedia", {
								m4v: "' . $result[0]->mp4 . '",
								poster: "' . $result[0]->thumbImg . '"
							});
							$("#uniqueContainer-' . $player_id . '").jPlayer( "play" );
							$(".s3bubble-video-advert-loading").fadeIn();
						}
						return false;
					});
				}else{
					$(".mediaAdvert-' . $player_id . '").mediaPlayer({
						media: {
							m4v: "' . $result[0]->mp4 . '",
							poster: "' . $result[0]->thumbImg . '"
						},
						size: {
							width: "100%",
							height: valueHeight
						},
						loadstart: function() {
							if(autoplay === "true"){
								$("#uniqueContainer-' . $player_id . '").jPlayer( "play" );
							}
						}
					});
				}
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