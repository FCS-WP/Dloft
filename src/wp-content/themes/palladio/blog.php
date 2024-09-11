<?php
/**
 * The template to display blog archive
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

/*
Template Name: Blog archive
*/

/**
 * Make page with this template and put it into menu
 * to display posts as blog archive
 * You can setup output parameters (blog style, posts per page, parent category, etc.)
 * in the Theme Options section (under the page content)
 * You can build this page in the WPBakery Page Builder to make custom page layout:
 * just insert %%CONTENT%% in the desired place of content
 */

// Get template page's content
$palladio_content = '';
$palladio_blog_archive_mask = '%%CONTENT%%';
$palladio_blog_archive_subst = sprintf('<div class="blog_archive">%s</div>', $palladio_blog_archive_mask);
if ( have_posts() ) {
	the_post(); 
	if (($palladio_content = apply_filters('the_content', get_the_content())) != '') {
		if (($palladio_pos = strpos($palladio_content, $palladio_blog_archive_mask)) !== false) {
			$palladio_content = preg_replace('/(\<p\>\s*)?'.$palladio_blog_archive_mask.'(\s*\<\/p\>)/i', $palladio_blog_archive_subst, $palladio_content);
		} else
			$palladio_content .= $palladio_blog_archive_subst;
		$palladio_content = explode($palladio_blog_archive_mask, $palladio_content);
		// Add VC custom styles to the inline CSS
		$vc_custom_css = get_post_meta( get_the_ID(), '_wpb_shortcodes_custom_css', true );
		if ( !empty( $vc_custom_css ) ) palladio_add_inline_css(strip_tags($vc_custom_css));
	}
}

// Prepare args for a new query
$palladio_args = array(
	'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish'
);
$palladio_args = palladio_query_add_posts_and_cats($palladio_args, '', palladio_get_theme_option('post_type'), palladio_get_theme_option('parent_cat'));
$palladio_page_number = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
if ($palladio_page_number > 1) {
	$palladio_args['paged'] = $palladio_page_number;
	$palladio_args['ignore_sticky_posts'] = true;
}
$palladio_ppp = palladio_get_theme_option('posts_per_page');
if ((int) $palladio_ppp != 0)
	$palladio_args['posts_per_page'] = (int) $palladio_ppp;
// Make a new query
query_posts( $palladio_args );
// Set a new query as main WP Query
$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];

// Set query vars in the new query!
if (is_array($palladio_content) && count($palladio_content) == 2) {
	set_query_var('blog_archive_start', $palladio_content[0]);
	set_query_var('blog_archive_end', $palladio_content[1]);
}

get_template_part('index');
?>