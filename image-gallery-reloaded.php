<?php
/*
Plugin Name: Image Gallery Reloaded
Plugin URI: http://18elements.com/tools/wordpress-image-gallery-reloaded
Description: The plugin replaces the default Wordpress gallery with full featured, jquery-powered Galleria.
Version: 2.1.6
Author: Daniel Sachs
Author URI: http://18elements.com
License: GPL
*/
function igr_requires_wordpress_version() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );

	if ( version_compare($wp_version, "3.2", "<" ) ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.2 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
		}
	}
}
add_action( 'admin_init', 'igr_requires_wordpress_version' );



register_activation_hook(__FILE__, 'igr_add_defaults');
register_uninstall_hook(__FILE__, 'igr_delete_plugin_options');
add_action('wp_enqueue_scripts', 'igr_gallery_source');
add_action('wp_footer', 'igr_gallery_ini');


function igr_gallery_source()
{	
	$options = get_option('igr_options');
	$theme = $options['igr_main_theme'];
	if(!is_admin()){
		wp_enqueue_script ('jquery');	
		wp_enqueue_script ('gallery_reloaded_source', plugins_url('/galleria-1.2.8.min.js', __FILE__), array('jquery'));
		#wp_enqueue_script ('gallery_reloaded', plugins_url('/themes/' . $theme . '/galleria.theme.js', __FILE__), array('jquery', 'gallery_reloaded_source'));
		wp_register_style ( 'gallery_style', plugins_url('/themes/' . $theme . '/galleria.theme.css', __FILE__) );
        wp_enqueue_style  ( 'gallery_style' );
		
	}
}




function igr_gallery_ini()
{ 
	$options = get_option('igr_options');
	?>
	<script type='text/javascript'>
		
		Galleria.configure({
			debug			: false, // debug is now off for deployment
			imageCrop		: true,
			thumbCrop		: true,
			carousel		: <?php echo $options['carousel']; ?>,
			thumbnails		: <?php echo $options['carousel']; ?>,
			transition		: '<?php echo $options['igr_main_transition']; ?>',
			transitionSpeed	: <?php echo $options['igr_main_transition_speed']; ?>,
			thumbEventType	: '<?php echo $options['igr_thumb_event_type']; ?>',
			autoplay		: <?php echo $options['autoplay']; ?>,
			clicknext		: <?php echo $options['clicknext']; ?>,
			showImagenav	: <?php echo $options['showImagenav']; ?>,
			showCounter		: <?php echo $options['showCounter']; ?>,
			lightbox		: <?php echo $options['lightbox']; ?>,
			imagePan		: <?php echo $options['imagePan']; ?>,
			width			: <?php echo $options['total_width']; ?>,
			height			: <?php echo $options['total_height']; ?>,
			showInfo		: <?php echo $options['showCaption']; ?>,
			_toggleInfo		: <?php echo $options['showCaptionToggle']; ?>

		});
	</script>
	<?php include_once ( dirname(__FILE__) . '/image-gallery-reloaded-style.php');
}


function igr_get_gallery_images( $args = array() ) 
{
	$defaults = array(
		'custom_key'		=> array( 'Thumbnail', 'thumbnail' ),
		'post_id'			=> false,
		'attachment'		=> true,
		'default_size'		=> 'thumbnail',
		'default_image'		=> false,
		'order_of_image'	=> 1,
		'link_to_post'		=> true,
		'image_class'		=> false,
		'image_scan'		=> false,
		'width'				=> false,
		'height'			=> false,
		'format'			=> 'img',
		'echo'				=> true
	);
	$args = apply_filters( 'igr_get_gallery_images_args', $args );
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	if ( !is_array( $custom_key ) ) :
		$custom_key = str_replace( ' ', '', $custom_key) ;
		$custom_key = str_replace( array( '+' ), ',', $custom_key );
		$custom_key = explode( ',', $custom_key );
		$args['custom_key'] = $custom_key;
	endif;
	if ( $custom_key && $custom_key !== 'false' && $custom_key !== '0' )
		$image = image_by_custom_field( $args );
	if ( !$image && $attachment && $attachment !== 'false' && $attachment !== '0' )
		$image = image_by_attachment( $args );
	if ( !$image && $image_scan )
		$image = image_by_scan( $args );
	if (!$image && $default_image )
		$image = image_by_default( $args );
	if ( $image )
		$image = display_the_image( $args, $image );
	$image = apply_filters( 'igr_get_gallery_images', $image );
	if ( $echo && $echo !== 'false' && $echo !== '0' && $format !== 'array' )
		echo $image;
	else
		return $image;
}




function igr_gallery_shortcode($attr)
{
	global $post;
	$options = get_option('igr_options');
	
	if (isset($attr['orderby']))
	{
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}
	
	extract(shortcode_atts(array(
		'orderby'		=> 'menu_order ASC, ID ASC',
		'id'			=> $post->ID,
		'itemtag'		=> 'dl',
		'icontag'		=> 'dt',
		'captiontag'	=> 'dd',
		'columns'		=> 3,
		'size'			=> 'thumbnail',
		'include'		=> '',
		'ids'			=> ''
	), $attr));

    $count = 1;
	$id = intval($id);
	
	if(!empty($ids))
	{
		$ids = preg_replace( '/[^0-9,]+/', '', $ids );
		$_attachments = get_posts( array(
										'include'			=> $ids,
										'post_type'			=> 'attachment',
										'post_mime_type'	=> 'image',
										'orderby'			=> $orderby
										)
								  );
		$attachments = array();
		foreach ( $_attachments as $key => $val )
		{
			$attachments[$val->ID] = $_attachments[$key];
		}
	}
	else 
	{
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		
		$_attachments = get_posts( array(
										'include'			=> $include,
										'post_parent'		=> $id,
										'post_type'			=> 'attachment',
										'post_mime_type'	=> 'image',
										'orderby'			=> $orderby
										)
								  );
		$attachments = array();
		foreach ( $_attachments as $key => $val )
		{
			$attachments[$val->ID] = $_attachments[$key];
		}
	}
	


	
	
	if ( is_feed() )
	{
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link($id, $size, true) . "\n";
		return $output;
	}

	$listtag = tag_escape($listtag);
	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	
	$theme = $options['igr_main_theme'];
	
	$output = apply_filters('gallery_style', '<div class="gallery">');
	$output .= '<div class="galleria">';
	foreach ( $attachments as $id => $attachment ) {
		$a_img = wp_get_attachment_url($id);
		$att_page = get_attachment_link($id);
		$img = wp_get_attachment_image_src($id, $size);
		$img = $img[0];
		$desc = $attachment -> post_content;
		$title = $attachment -> post_excerpt;
		if($title == '') $title = $attachment->post_title;
		
		
		$link = $a_img;
		$output .= '<a href="' . $link . '">';
		$output .= '<img src="' . $img . '" data-description="' . $desc . '" data-title="' . $title . '" data-big="' . $link . '" />';
		$output .= '</a>';
		$count++;
	}
	
    $output .= '</div></div>';
	
	$output .= "<script type='text/javascript'>Galleria.loadTheme('" . plugins_url('/image-gallery-reloaded/themes/'. $theme . '/galleria.theme.min.js') . "');</script>";
	$output .= "<script type='text/javascript'>Galleria.run('.galleria');</script>";
	return $output;
}
remove_shortcode('gallery');
add_shortcode('gallery', 'igr_gallery_shortcode');


















/**
 * Gallery Reloaded Options Page
 */ 
add_action('admin_init', 'igr_init' );
add_action('admin_menu', 'igr_add_options_page');
add_filter( 'plugin_action_links', 'igr_plugin_action_links', 10, 2 );



function igr_delete_plugin_options()
{
	delete_option('igr_options');
}


function igr_add_defaults()
{
	$tmp = get_option('igr_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp)))
	{
		delete_option('igr_options');
		$arr = array(	"total_width"						=> "660",
						"total_height"						=> "460",
						"thumb_width"						=> "60",
						"thumb_height"						=> "40",
						"gallery_border_width"				=> "10",
						"gallery_border_top_height"			=> "10",
						"gallery_border_bottom_height"		=> "60",
						"lightbox_border_width"				=> "10",
						"color_background"					=> "000000",
						"color_thumb_border"				=> "000000",
						"color_infolink_background"			=> "000000",
						"color_lightbox_background"			=> "FFFFFF",
						"color_lightbox_border"				=> "000000",
						"color_carusel_arrows_background"	=> "000000",
						"igr_main_transition" 				=> "fade",
						"igr_thumb_event_type"				=> "click",
						"igr_main_theme" 					=> "classic",
						"igr_main_transition_speed"			=> "400",
						"autoplay"							=> "false",
						"clicknext"							=> "false",
						"imagePan"							=> "false",
						"showImagenav"						=> "false",
						"showCounter"						=> "false",
						"lightbox"							=> "false",
						"showCaption"						=> "true",
						"carousel"							=> "true",
						"showCaptionToggle"					=> "true",
						"hideDonations"						=> "false",
						"disableSelective"					=> "false",
						"chk_default_options_db"			=> ""
		);
		update_option('igr_options', $arr);
	}
}




function igr_init()
{
	register_setting( 'igr_plugin_options', 'igr_options', 'igr_validate_options' );
}

function igr_add_options_page()
{
	add_options_page('Image Gallery Reloaded Options', 'Image Gallery Reloaded Options', 'manage_options', __FILE__, 'igr_render_form');
	add_action( 'admin_print_scripts', 'igr_admin_scripts' );
}

function igr_admin_scripts() 
{
	wp_enqueue_script ('jquery');
	wp_enqueue_script ('igr_color_picker', plugins_url('/picker/colorpicker.js', __FILE__ ), array('jquery'));
	wp_register_style ( 'igr_color_picker_style', plugins_url('/picker/colorpicker.css', __FILE__) );
    wp_enqueue_style  ( 'igr_color_picker_style' );
}


function igr_render_form() 
{
	?>
    <script type="text/javascript">
		jQuery(document).ready(function() {
		jQuery('#color1, #color2, #color3, #color4, #color5, #color6').ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				jQuery(el).val(hex);
				jQuery(el).ColorPickerHide();
			},
			onBeforeShow: function () {
				jQuery(this).ColorPickerSetColor(this.value);
			}
		})
		.bind('keyup', function(){
			jQuery(this).ColorPickerSetColor(this.value);
		});
										});
	</script>
	<style type="text/css"> 
    .form-table {
        width: 700px;
        padding: 0;
        margin: 0;
        border-top:1px solid #C1DAD7;
    }
    
    th {
        font: bold 11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
        color: #4f6b72;
        border-right: 1px solid #C1DAD7;
        border-bottom: 1px solid #C1DAD7;
        border-top: 1px solid #C1DAD7;
        letter-spacing: 2px;
        text-transform: uppercase;
        text-align: left;
        padding: 6px 6px 6px 12px;
        background: #CAE8EA url(images/bg_header.jpg) no-repeat;
    }
    td {
        border-right: 1px solid #C1DAD7;
        border-bottom: 1px solid #C1DAD7;
        background: #fff;
        padding: 6px 6px 6px 12px;
        color: #4f6b72;
    }
    tr:nth-child(odd) td { background:#F5FAFA; }
    tr:nth-child(even) td { background:#fff; }
	td.divider {border-right: 1px solid #FFF; background:#fff !important;}
    </style>
	<div class="wrap">
		
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Image Gallery Reloaded Options</h2>
		<p></p>

		<form method="post" action="options.php">
			<?php settings_fields('igr_plugin_options'); ?>
			<?php $options = get_option('igr_options'); ?>

			<table class="form-table">
				<tr valign="top"><th scope="row"><h3>Dimentions</h3></th><td></td></tr>
				<tr>
					<th scope="row">Gallery Width</th>
					<td>
						<input type="text" size="20" name="igr_options[total_width]" value="<?php echo $options['total_width']; ?>" /> pixels
					</td>
				</tr>
				<tr>
					<th scope="row">Gallery Height</th>
					<td>
						<input type="text" size="20" name="igr_options[total_height]" value="<?php echo $options['total_height']; ?>" /> pixels
					</td>
				</tr>
                
                <tr>
					<th scope="row">Thumbnail Width</th>
					<td>
						<input type="text" size="20" name="igr_options[thumb_width]" value="<?php echo $options['thumb_width']; ?>" /> pixels
					</td>
				</tr>
                
                <tr>
					<th scope="row">Thumbnail Height</th>
					<td>
						<input type="text" size="20" name="igr_options[thumb_height]" value="<?php echo $options['thumb_height']; ?>" /> pixels
					</td>
				</tr>
                <tr>
					<th scope="row">Gallery Border Width</th>
					<td>
						<input type="text" size="20" name="igr_options[gallery_border_width]" value="<?php echo $options['gallery_border_width']; ?>" /> pixels
					</td>
				</tr>
                <tr>
					<th scope="row">Main Image Top Margin</th>
					<td>
						<input type="text" size="20" name="igr_options[gallery_border_top_height]" value="<?php echo $options['gallery_border_top_height']; ?>" /> pixels
					</td>
				</tr>
                <tr>
					<th scope="row">Main Image Bottom Margin</th>
					<td>
						<input type="text" size="20" name="igr_options[gallery_border_bottom_height]" value="<?php echo $options['gallery_border_bottom_height']; ?>" /> pixels
					</td>
				</tr>
                <tr>
					<th scope="row">Lightbox Border Width</th>
					<td>
						<input type="text" size="20" name="igr_options[lightbox_border_width]" value="<?php echo $options['lightbox_border_width']; ?>" /> pixels
					</td>
				</tr>
                
                
                <tr><td colspan="2" class="divider"><div style="margin-top:10px;"></div></td></tr>
                <tr valign="top"><th scope="row"><h3>Colors</h3></th><td></td></tr>
                <tr>
					<th scope="row">Background Color</th>
					<td>
						#<input type="text" id="color1" size="20" name="igr_options[color_background]" value="<?php echo $options['color_background']; ?>" /> 
					</td>
				</tr>
                
                <tr>
					<th scope="row">Thumbnail Border Color</th>
					<td>
						#<input type="text" id="color2" size="20" name="igr_options[color_thumb_border]" value="<?php echo $options['color_thumb_border']; ?>" /> 
					</td>
				</tr>
                
                <tr>
					<th scope="row">Carousel Scrolling Arrows Backrgound Color</th>
					<td>
						#<input type="text" id="color2" size="20" name="igr_options[color_carusel_arrows_background]" value="<?php echo $options['color_carusel_arrows_background']; ?>" /> 
					</td>
				</tr>
                                
                <tr>
					<th scope="row">Infolink Background Color</th>
					<td>
						#<input type="text" id="color3" size="20" name="igr_options[color_infolink_background]" value="<?php echo $options['color_infolink_background']; ?>" /> 
					</td>
				</tr>
                
                <tr>
					<th scope="row">Lightbox Border Color</th>
					<td>
						#<input type="text" id="color4" size="20" name="igr_options[color_lightbox_border]" value="<?php echo $options['color_lightbox_border']; ?>" /> 
					</td>
				</tr>
                
                <tr>
					<th scope="row">Lightbox Content Background Color</th>
					<td>
						#<input type="text" id="color5" size="20" name="igr_options[color_lightbox_background]" value="<?php echo $options['color_lightbox_background']; ?>" /> 
					</td>
				</tr>
                
                
                <tr><td colspan="2" class="divider"><div style="margin-top:10px;"></div></td></tr>
                <tr valign="top"><th scope="row"><h3>Themes</h3></th><td></td></tr>
                <!-- Select Drop-Down Control -->
				<tr>
					<th scope="row">Select Gallery theme</th>
					<td>
						<select name='igr_options[igr_main_theme]'>
							<option value='classic' <?php selected('classic', $options['igr_main_theme']); ?>>Classic</option>
                            <option value='classic_tumbs_top' <?php selected('classic_tumbs_top', $options['igr_main_theme']); ?>>Classic | Thumbs on Top</option>
							<!--option value='custom' <?php #selected('custom', $options['igr_main_theme']); ?>>Custom</option-->
						</select>
					</td>
				</tr>
                <tr>
					<th scope="row">Use custom CSS</th>
					<td>
						<textarea type="textarea" rows="10" cols="20" name="igr_options[custom_css]"><?php echo $options['custom_css']; ?></textarea>
					</td>
				</tr>
				
                
                
                <tr><td colspan="2" class="divider"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top"><th scope="row"><h3>Effects & Transitions</h3></th><td></td></tr>
                <!-- Select Drop-Down Control -->
				<tr>
					<th scope="row">Main Image Transition</th>
					<td>
						<select name='igr_options[igr_main_transition]'>
							<option value='fade' <?php selected('fade', $options['igr_main_transition']); ?>>fade</option>
							<option value='flash' <?php selected('flash', $options['igr_main_transition']); ?>>flash</option>
							<option value='pulse' <?php selected('pulse', $options['igr_main_transition']); ?>>pulse</option>
							<option value='slide' <?php selected('slide', $options['igr_main_transition']); ?>>slide</option>
							<option value='fadeslide' <?php selected('fadeslide', $options['igr_main_transition']); ?>>fadeslide</option>
						</select>
                        <br />
						<span style="color:#666666;margin-left:2px;">The transition that is used when displaying the images. </span>
                        <span style="color:#666666;margin-left:2px;">
                        <ul>
                            <li>‘fade’ crossfade betweens images</li>
                            <li>‘flash’ fades into background color between images</li>
                            <li>‘pulse’ quickly removes the image into background color, then fades the next image</li>
                            <li>‘slide’ slides the images depending on image position</li>
                            <li>‘fadeslide’ fade between images and slide slightly at the same time</li>
                        </ul>
						</span>
					</td>
				</tr>
				
				
				
                <tr>
					<th scope="row">Transition Speed</th>
					<td>
						<input type="text" size="20" name="igr_options[igr_main_transition_speed]" value="<?php echo $options['igr_main_transition_speed']; ?>" /> milliseconds
					</td>
				</tr>
                
                <tr>
					<th scope="row">Main Image Transition Trigger</th>
                    <td>
						<select name='igr_options[igr_thumb_event_type]'>
							<option value='click' <?php selected('click', $options['igr_thumb_event_type']); ?>>click</option>
							<option value='mouseover' <?php selected('mouseover', $options['igr_thumb_event_type']); ?>>mouseover</option>
						</select>
                        <span style="color:#666666;margin-left:2px;">
                        <ul>
                            <li>‘click’ changes the main image by clicking the thumbnail</li>
                            <li>‘mouseover’ changes the main image by hovering over the thumbnail</li>
                        </ul>
						</span>
					</td>
				</tr>
                
                
                <tr><td colspan="2" class="divider"><div style="margin-top:10px;"></div></td></tr>
                <tr valign="top" style="border-top:#dddddd 1px dashed;"><th scope="row"><h3>General Settings</h3></th><td></td></tr>
                <tr>
					<th scope="row"></th>
					<td>
						<label><input type="hidden" name="igr_options[autoplay]" value="false" /> <input name="igr_options[autoplay]" type="checkbox" value="true" <?php if (isset($options['autoplay'])) { checked('true', $options['autoplay']); } ?> />  Autoplay gallery on page load.</label><br />
                        
                        <label><input type="hidden" name="igr_options[clicknext]" value="false" /> <input name="igr_options[clicknext]" type="checkbox" value="true" <?php if (isset($options['clicknext'])) { checked('true', $options['clicknext']); } ?> />  Adds a click event over the stage that navigates to the next image in the gallery. Useful for mobile browsers <br /><strong>NOTE:</strong>disable in order to use lightbox.</label><br />
                        
                        
                        <label><input type="hidden" name="igr_options[lightbox]" value="false" /> <input name="igr_options[lightbox]" type="checkbox" value="true" <?php if (isset($options['lightbox'])) { checked('true', $options['lightbox']); } ?> />  Display full images in lightbox popup.</label><br />
                                              
                        <label><input type="hidden" name="igr_options[carousel]" value="false" /> <input name="igr_options[carousel]" type="checkbox" value="true" <?php if (isset($options['carousel'])) { checked('true', $options['carousel']); } ?> />  Show thumbnail strip</label><br />
                        
                        <label><input type="hidden" name="igr_options[imagePan]" value="false" /> <input name="igr_options[imagePan]" type="checkbox" value="true" <?php if (isset($options['imagePan'])) { checked('true', $options['imagePan']); } ?> />  Use image panning effect to reveal the cropped parts.</label><br />
                        
                        <label><input type="hidden" name="igr_options[showImagenav]" value="false" /> <input name="igr_options[showImagenav]" type="checkbox" value="true" <?php if (isset($options['showImagenav'])) { checked('true', $options['showImagenav']); } ?> />  Display the image navigation (next/prev) arrows.</label><br />
                                                
                        <label><input type="hidden" name="igr_options[showCounter]" value="false" /> <input name="igr_options[showCounter]" type="checkbox" value="true" <?php if (isset($options['showCounter'])) { checked('true', $options['showCounter']); } ?> />  Display the image counter.</label><br />
                        
                        <label><input type="hidden" name="igr_options[showCaption]" value="false" /> <input name="igr_options[showCaption]" type="checkbox" value="true" <?php if (isset($options['showCaption'])) { checked('true', $options['showCaption']); } ?> />  Show Image Info: Title and Description </label><br />
                        
                        <label><input type="hidden" name="igr_options[showCaptionToggle]" value="false" /> <input name="igr_options[showCaptionToggle]" type="checkbox" value="true" <?php if (isset($options['showCaptionToggle'])) { checked('true', $options['showCaptionToggle']); } ?> />  Use Image Info toggle button</label><br />
                        
					</td>
				</tr>
                

				<tr><td colspan="2" class="divider"><div style="margin-top:10px;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td>
						<label><input name="igr_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon plugin deactivation/reactivation</label>
						<br /><span style="color:#666666;margin-left:2px;">Only check this if you want to reset plugin settings upon Plugin reactivation</span>
					</td>
				</tr>
                
                
			</table>
            
            
           
        	 
        
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
       
       
        <div id="donations">
        <span>If you like and use this free plugin, please consider a small contribution to support further development</span>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_donations">
            <input type="hidden" name="business" value="billing@18elements.com">
            <input type="hidden" name="lc" value="US">
            <input type="hidden" name="item_name" value="Thanks for your Wordpress plugin">
            <input type="hidden" name="no_note" value="0">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="bn" value="PP-DonationsBF:btn_donate_SM.gif:NonHostedGuest">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
        </div>
        
       
       
       
       
       </div>
	<?php	
}




function igr_validate_options($input)
{
	//$input['textarea_one'] =  wp_filter_nohtml_kses($input['textarea_one']); // Sanitize textarea input (strip html tags, and escape characters)
	
	return $input;
}


function igr_plugin_action_links( $links, $file ) 
{

	if ( $file == plugin_basename( __FILE__ ) ) {
		$igr_links = '<a href="'.get_admin_url().'options-general.php?page=image-gallery-reloaded/image-gallery-reloaded.php">'.__('Settings').'</a>';
		array_unshift( $links, $igr_links );
	}

	return $links;
}