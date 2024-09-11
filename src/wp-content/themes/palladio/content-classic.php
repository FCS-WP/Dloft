<?php
/**
 * The Classic template to display the content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

$palladio_blog_style = explode('_', palladio_get_theme_option('blog_style'));
$palladio_columns = empty($palladio_blog_style[1]) ? 2 : max(2, $palladio_blog_style[1]);
$palladio_expanded = !palladio_sidebar_present() && palladio_is_on(palladio_get_theme_option('expand_content'));
$palladio_post_format = get_post_format();
$palladio_post_format = empty($palladio_post_format) ? 'standard' : str_replace('post-format-', '', $palladio_post_format);
$palladio_animation = palladio_get_theme_option('blog_animation');
$palladio_components = palladio_is_inherit(palladio_get_theme_option_from_meta('meta_parts')) 
							? 'date,counters'
							: palladio_array_get_keys_by_value(palladio_get_theme_option('meta_parts'));
$palladio_counters = palladio_is_inherit(palladio_get_theme_option_from_meta('counters')) 
							? 'comments'
							: palladio_array_get_keys_by_value(palladio_get_theme_option('counters'));

?><div class="<?php echo esc_html($palladio_blog_style[0] == 'classic' ? 'column' : 'masonry_item masonry_item'); ?>-1_<?php echo esc_attr($palladio_columns); ?>"><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_format_'.esc_attr($palladio_post_format)
					. ' post_layout_classic post_layout_classic_'.esc_attr($palladio_columns)
					. ' post_layout_'.esc_attr($palladio_blog_style[0]) 
					. ' post_layout_'.esc_attr($palladio_blog_style[0]).'_'.esc_attr($palladio_columns)
					); ?>
	<?php echo (!palladio_is_off($palladio_animation) ? ' data-animation="'.esc_attr(palladio_get_animation_classes($palladio_animation)).'"' : ''); ?>>
	<?php

	// Sticky label
	if ( is_sticky() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	// Featured image
	palladio_show_post_featured( array( 'thumb_size' => palladio_get_thumb_size($palladio_blog_style[0] == 'classic'
													? (strpos(palladio_get_theme_option('body_style'), 'full')!==false 
															? ( $palladio_columns > 2 ? 'big' : 'huge' )
															: (	$palladio_columns > 2
																? ($palladio_expanded ? 'med' : 'small')
																: ($palladio_expanded ? 'big' : 'med')
																)
														)
													: (strpos(palladio_get_theme_option('body_style'), 'full')!==false 
															? ( $palladio_columns > 2 ? 'masonry-big' : 'full' )
															: (	$palladio_columns <= 2 && $palladio_expanded ? 'masonry-big' : 'masonry')
														)
								) ) );

	if ( !in_array($palladio_post_format, array('link', 'aside', 'status', 'quote')) ) {
		?>
		<div class="post_header entry-header">
			<?php 
			do_action('palladio_action_before_post_title'); 

			// Post title
			the_title( sprintf( '<h6 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h6>' );

			do_action('palladio_action_before_post_meta'); 

			// Post meta
			if (!empty($palladio_components))
				palladio_show_post_meta(apply_filters('palladio_filter_post_meta_args', array(
					'components' => $palladio_components,
					'counters' => $palladio_counters,
					'seo' => false
					), $palladio_blog_style[0], $palladio_columns)
				);

			do_action('palladio_action_after_post_meta'); 
			?>
		</div><!-- .entry-header -->
		<?php
	}		
	?>

	<div class="post_content entry-content">
		<div class="post_content_inner">
			<?php
			$palladio_show_learn_more = false;
			if (has_excerpt()) {
				the_excerpt();
			} else if (strpos(get_the_content('!--more'), '!--more')!==false) {
				the_content( '' );
			} else if (in_array($palladio_post_format, array('link', 'aside', 'status'))) {
				the_content();
			} else if ($palladio_post_format == 'quote') {
				if (($quote = palladio_get_tag(get_the_content(), '<blockquote>', '</blockquote>'))!='')
					palladio_show_layout(wpautop($quote));
				else
					the_excerpt();
			} else if (substr(get_the_content(), 0, 1)!='[') {
				the_excerpt();
			}
			?>
		</div>
		<?php
		// Post meta
		if (in_array($palladio_post_format, array('link', 'aside', 'status', 'quote'))) {
			if (!empty($palladio_components))
				palladio_show_post_meta(apply_filters('palladio_filter_post_meta_args', array(
					'components' => $palladio_components,
					'counters' => $palladio_counters
					), $palladio_blog_style[0], $palladio_columns)
				);
		}
		// More button
		if ( $palladio_show_learn_more ) {
            ?><p><a class="sc_button sc_button_simple sc_button_size_normal" href="<?php the_permalink(); ?>"><span class="sc_button_text"><?php esc_html_e('Read more', 'palladio'); ?></span></a></p><?php
		}
		?>
	</div><!-- .entry-content -->

</article></div>