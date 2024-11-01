<?php
/**
 * Simple Carousel Plugin
 *
 * A really simple plugin to add a carousel to your website.
 * This plugin uses a custom post type, with it's own settings page.
 *
 * Plugin Name:     Simple Carousel Plugin
 * Plugin URI:      
 * Description:     A simple plugin to add a carousel type slider to a website.
 * Author:          Tailored Internet Marketing
 * Author URI:      http://www.tailoredinternetmarketing.com
 * Version:         1.0
 * License:         GPL3+
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @license         GNU General Public License, version 2
 * @copyright       2014 Tailored Internet Marketing
 */
 if (!class_exists("TailoredSimpleCarousel")) {
    class TailoredSimpleCarousel {
		
		public function __construct() {
			
			add_action('wp_enqueue_scripts', array($this, 'carousel_enqueue_script'));
			add_action('wp_head', array($this, 'carousel_head_code'));
			add_action('init', array($this, 'tailored_carousel_init'));
			add_action("admin_menu",array($this,'admin_menu'));	
			add_action('admin_init', array($this, 'carousel_admin_init'));
			add_action( 'add_meta_boxes', array($this, 'carousel_meta_box'));
			add_action( 'admin_enqueue_scripts', array($this, 'carousel_admin_enqueue'));
			add_action( 'save_post', array($this, 'carousel_meta_box_save' ));
			
			register_activation_hook(__FILE__, array($this, 'register_plugin'));
			$options = get_option('drt_scp_options');
			add_image_size( 'tailored_carousel', $options['scp_width'], $options['scp_height'], true );
				
		}
		
		public function carousel_admin_enqueue() {
			wp_enqueue_media();
			wp_enqueue_script( 'my_custom_script', plugins_url('js/admin.js', __FILE__) );
		}
	
	
		
	
		public function carousel_meta_box() {
			 add_meta_box( 
				'carousel_meta_box',
				'Carousel Image Details',
				array($this, 'carousel_meta_box_output'),
				'simple-carousel',
				'advanced',
				'core' 
			);
		}
		
		public function carousel_meta_box_output() {
			global $post;
			$options = get_option('drt_scp_options');
			$ratio = $options['scp_width']/$options['scp_height'];
			$height = 300/$ratio;
			echo '<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="scp_overlay_text">Overlay Text</label></th>
						<td><input type="text" name="post_title" id="post_title" value="'.htmlspecialchars(get_post_meta($post->ID, 'post_title', true)).'"/></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="scp_link">Link</label></th>
						<td><input type="text" name="scp_link" id="scp_link" value="'.htmlspecialchars(get_post_meta($post->ID, 'scp_link', true)).'"/><br/><i>Please use full url inc. http://</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label>Upload Image</label></th>
						<td><input type="button" class="button-secondary action custom_media_upload" value="Upload Image" />
							<input class="custom_media_url" id="scp_image" name="scp_image" type="hidden" value="'.get_post_meta($post->ID, 'scp_image', true).'" />
						</td>
					</tr>
				</table>';
			$imageurl = wp_get_attachment_image_src( get_post_meta($post->ID, 'scp_image', true), 'tailored_carousel');
			
			echo '<p><img class="custom_media_image" src="'.$imageurl[0].'" style="max-width: 100%; height: auto;"/></p>';
		}
		
		public function carousel_meta_box_save( $post_id ) {
	  
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			  return;
			
			if ( 'page' == $_POST['post_type'] ) 
			{
				if ( !current_user_can( 'edit_page', $post_id ) )
					return;
			}
			else
			{
				if ( !current_user_can( 'edit_post', $post_id ) )
					return;
			}
			
			$mydata = array(
				'post_title' => $_POST['post_title'], 
				'scp_link' => $_POST['scp_link'], 
				'scp_image' => $_POST['scp_image'], 
			);
	
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || defined('DOING_AJAX') && DOING_AJAX ) {
				return;
			} else {
				foreach($mydata as $key=>$value) {
					update_post_meta($post_id, $key, $value) or add_post_meta($post_id, $key, $value, true);
				}
			}
		}
		
		public function carousel_admin_init() {
			register_setting( 'drt_scp_options', 'drt_scp_options' );
		}
		
		public function register_plugin() {
			if(!get_option('drt_scp_options')) {
				$options = array(
					'scp_width' 	=> 900,
					'scp_height' 	=> 300,
					'scp_delay'		=> 8,
					'scp_display'	=> 1,
				);
				update_option('drt_scp_options', $options);	
			}
		}
		
		public function admin_menu() {
			add_submenu_page( 'edit.php?post_type=simple-carousel', 'Simple Carousel Settings', 'Settings', 'manage_options', 'simple-carousel-options', array($this, 'show_admin_menu') );	
		}
		
		public function show_admin_menu(){
			//
		 	echo '
				<div class="wrap">
					<h2>Simple Carousel Plugin Settings</h2>';
			if($_REQUEST['settings-updated'] == true) {
				echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Settings saved.</strong></p></div>';
			}
			echo'
					<form method="post" action="options.php">';
					
			settings_fields( 'drt_scp_options' );
			$options = get_option('drt_scp_options');
			//print_r($options);
			echo'
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><label for="scp_width">Width (px)</label></th>
								<td><input type="text" name="drt_scp_options[scp_width]" value="'.$options['scp_width'].'"/></td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="scp_height">Height (px)</label></th>
								<td><input type="text" name="drt_scp_options[scp_height]" value="'.$options['scp_height'].'" /></td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="scp_delay">Delay (seconds)</label></th>
								<td><input type="text" name="drt_scp_options[scp_delay]" value="'.$options['scp_delay'].'" /></td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="scp_display">Display Text Overlay?</label></th>
								<td><input type="checkbox" name="drt_scp_options[scp_display]" value="1" '.checked($options['scp_display'], '1', false).' /></td>
							</tr>
						</table>';
			submit_button();
			echo'
					</form>
					<div id="message" class="error"><p>If you change the width and/or height of your carousel you will need to <a href="http://wordpress.org/plugins/regenerate-thumbnails/" target="_blank">Regenerate Thumbnails</a>.</p></div>
					<p>To use this plugin simply add the following code in your theme where you want the carousel to display:</p>
					<code>&lt;?php display_carousel(); ?&gt;</code>
				</div>';
		}
		
        public function output() {
			$options = get_option('drt_scp_options');
			
			$output = '<div id="homemast" class="mast">';
			$output .= '<div id="mastin" class="borders">';
    		$output .= '<div class="mastdiv">';
			
			$args = array(
				'post_type' => 'simple-carousel',
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);
			$i = 0;
			$loop = get_posts($args);
			foreach ( $loop as $post ) : setup_postdata( $post );
				$output .=  '<div class="slide">';
				if($options['scp_display'] == 1) {
					$output .= '<div class="slidetitle">';
					$output .=  get_the_title($post->ID);
					$output .= '</div>';
				}
				$output .= '<a href="'.get_post_meta($post->ID, 'scp_link', true).'">';
				$output .= wp_get_attachment_image( get_post_meta($post->ID, 'scp_image', true), 'tailored_carousel');
				$output .= '</a>';
				$output .= '</div>';
			endforeach;
			wp_reset_postdata();
    	    	
    		$output .= '</div>';
  			$output .= '</div>';
			$output .= '</div>';
			
			echo $output;
		}
		
		public function tailored_carousel_init() {
			register_post_type(
				'simple-carousel', 
				array(
					'label' => 'Carousel Images',
					'description' => '',
					'show_ui' => true,
					'menu_icon' => 'dashicons-images-alt',
					'show_in_menu' => true,
					'capability_type' => 'page',
					'hierarchical' => false,
					'supports' => array(
						//'title',
						//'thumbnail',
						'page-attributes',
					),
					'labels' => array (
						'name' => 'Carousel Images',
						'singular_name' => 'Carousel Image',
						'menu_name' => 'Carousel Images',
						'add_new' => 'Add Carousel Images',
						'add_new_item' => 'Add New Carousel Image',
						'edit' => 'Edit Carousel Image',
						'edit_item' => 'Edit Carousel Image',
						'new_item' => 'New Carousel Image',
						'view' => 'View Carousel Image',
						'view_item' => 'View Carousel Image',
						'search_items' => 'Search Carousel Images',
						'not_found' => 'No Carousel Images Found',
						'not_found_in_trash' => 'No Carousel Images Found in Trash',
						'parent' => 'Parent Carousel Image',
					),
				)
			);	 	
		}
		
		public function carousel_enqueue_script() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script('carousel_javascript', plugins_url('js/carousel.js', __FILE__));
			wp_enqueue_style('carousel_style',  plugins_url('css/carousel.css', __FILE__));
		}
	
	
		public function carousel_head_code() {
			$options = get_option('drt_scp_options');
			echo '
			<script type="text/javascript">
				j(document).ready(function($) {
					jQuery.rotateSwitch('.$options['scp_delay'].', '.$options['scp_width'].');
				});
			</script>';	
			echo '
			<style>
				#homemast #mastin {
					width: '.$options['scp_width'].'px;
					overflow: hidden;
					height: '.$options['scp_height'].'px;
					padding: 0;	
				}
				#mastin .slide {
					float:left;
					width: '.$options['scp_width'].'px;
					position: relative;
					padding: 0;
				}
			</style>';
		}
	} 
}

if (class_exists("TailoredSimpleCarousel")) {
    $tailored_carousel = new TailoredSimpleCarousel();
}

if ( !function_exists( 'display_carousel' ) ) {
	function display_carousel() {
		global $tailored_carousel;
		return $tailored_carousel->output( );
	}
}
?>