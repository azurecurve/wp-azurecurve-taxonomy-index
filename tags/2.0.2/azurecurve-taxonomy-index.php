<?php
/*
Plugin Name: azurecurve Taxonomy Index
Plugin URI: http://development.azurecurve.co.uk/plugins/taxonomy-index
Description: Displays Index of Categories/Tags or other taxonomy types using taxonomy-index Shortcode. This plugin is multi-site compatible.
Version: 2.0.2
Author: azurecurve
Author URI: http://development.azurecurve.co.uk

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

The full copy of the GNU General Public License is available here: http://www.gnu.org/licenses/gpl.txt

*/

//include menu
require_once( dirname(  __FILE__ ) . '/includes/menu.php');

add_shortcode( 'taxonomy-index', 'azc_ti_display_index' );
add_action('wp_enqueue_scripts', 'azc_ti_load_css');

function azc_ti_load_css(){
	wp_enqueue_style( 'azc_ti', plugins_url( 'style.css', __FILE__ ), '', '1.0.0' );
}

function azc_ti_display_index($atts, $content = null) {
	extract(shortcode_atts(array(
		'taxonomy' => '',
		'slug' => ''
	), $atts));
	
	$taxonomy_meta = get_term_by('slug', $slug, $taxonomy);
	if ($taxonomy == 'tag'){
		$taxonomy = 'post_tag';
	}
	
	$args = array( 'parent' => $taxonomy_meta->term_id, 'taxonomy' => $taxonomy );
	$categories = get_categories( $args ); 
	
	$output = '';
	foreach ($categories as $category) {
		$category_link = get_category_link( $category->term_id );
		$output .= "<a href='$category_link' class='azc_ti'>$category->name</a>";
	}
	
	if (strlen($output) > 0){
		$output = "<span class='azc_ti'>".$output."</span>";
	}
	
	$args = array( 'category' => $taxonomy_meta->term_id );
	
	$posts = get_posts( $args );
	
	foreach ( $posts as $post ){
		$output .= "<a href='" . get_permalink($post->ID) ."' class='azc_ti'>" . $post->post_title . "</a>";
	}
  
	return "<span class='azc_ti'>".$output."</span>";
	
}


// azurecurve menu
function azc_create_ti_plugin_menu() {
	global $admin_page_hooks;
    
	add_submenu_page( "azc-plugin-menus"
						,"Taxonomy Index"
						,"Taxonomy Index"
						,'manage_options'
						,"azc-ti"
						,"azc_ti_settings" );
}
add_action("admin_menu", "azc_create_ti_plugin_menu");

function azc_ti_settings() {
	if (!current_user_can('manage_options')) {
		$error = new WP_Error('not_found', __('You do not have sufficient permissions to access this page.' , 'azc_siw'), array('response' => '200'));
		if(is_wp_error($error)){
			wp_die($error, '', $error->get_error_data());
		}
    }
	?>
	<div id="azc-t-general" class="wrap">
			<h2>azurecurve Taxonomy Index</h2>
			<p>
				<?php _e('Displays Index of Categories/Tags or other taxonomy types using taxonomy-index Shortcode. This plugin is multi-site compatible.', 'azc_ti'); ?>
			</p>
				<p><?php _e('Example use: [taxonomy-index taxonomy="category" slug="ice-cream"]', 'azc_ti'); ?></p>
				<p><?php _e('Alternative <strong>ti</strong> shortcode can also be used', 'azc_ti'); ?></p>
				<p><?php _e('Taxonomy can be set to <strong>category</strong> or <strong>tag</strong> or other taxonomy post type.', 'azc_ti'); ?></p>
			<p><label for="additional-plugins">
				azurecurve <?php _e('has the following plugins which allow shortcodes to be used in comments and widgets:', 'azc_gpi'); ?>
			</label>
			<ul class='azc_plugin_index'>
				<li>
					<?php
					if ( is_plugin_active( 'azurecurve-shortcodes-in-comments/azurecurve-shortcodes-in-comments.php' ) ) {
						echo "<a href='admin.php?page=azc-sic' class='azc_plugin_index'>Shortcodes in Comments</a>";
					}else{
						echo "<a href='https://wordpress.org/plugins/azurecurve-shortcodes-in-comments/' class='azc_plugin_index'>Shortcodes in Comments</a>";
					}
					?>
				</li>
				<li>
					<?php
					if ( is_plugin_active( 'azurecurve-shortcodes-in-widgets/azurecurve-shortcodes-in-widgets.php' ) ) {
						echo "<a href='admin.php?page=azc-siw' class='azc_plugin_index'>Shortcodes in Widgets</a>";
					}else{
						echo "<a href='https://wordpress.org/plugins/azurecurve-shortcodes-in-widgets/' class='azc_plugin_index'>Shortcodes in Widgets</a>";
					}
					?>
				</li>
			</ul></p>
	</div>
	
<?php
}

?>