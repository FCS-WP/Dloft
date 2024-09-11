<?php
/**
 * The template to display the 404 page
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

// Tribe Events & EDD hack - create empty post object
if (!isset($GLOBALS['post'])) {
	$GLOBALS['post'] = new stdClass();
	$GLOBALS['post']->post_type = 'unknown';
	$GLOBALS['post']->post_content = '';
}
// End Tribe Events hack

get_header(); 

get_template_part( 'content', '404' );

get_footer();
?>