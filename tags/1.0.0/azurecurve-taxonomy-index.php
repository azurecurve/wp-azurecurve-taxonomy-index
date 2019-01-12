<?php
/*
Plugin Name: azurecurve Taxonomy Index
Plugin URI: http://development.azurecurve.co.uk/plugins/taxonomy-index
Description: Displays Index of Categories/Tags or other taxonomy types using taxonomy-index Shortcode. This plugin is multi-site compatible.
Version: 1.0.0
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

?>