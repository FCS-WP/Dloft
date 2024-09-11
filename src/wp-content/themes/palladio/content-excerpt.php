<?php
/**
 * The default template to display the content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

$palladio_post_format = get_post_format();
$palladio_post_format = empty($palladio_post_format) ? 'standard' : str_replace('post-format-', '', $palladio_post_format);
$palladio_animation = palladio_get_theme_option('blog_animation');

?><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_excerpt post_format_'.esc_attr($palladio_post_format) ); ?>
	<?php echo (!palladio_is_off($palladio_animation) ? ' data-animation="'.esc_attr(palladio_get_animation_classes($palladio_animation)).'"' : ''); ?>
	><?php

	// Sticky label
	if ( is_sticky() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

    do_action('palladio_action_before_post_meta');

    // Post meta
    $palladio_components = palladio_is_inherit(palladio_get_theme_option_from_meta('meta_parts'))
        ? 'categories,author,date,counters'
        : palladio_array_get_keys_by_value(palladio_get_theme_option('meta_parts'));
    $palladio_counters = palladio_is_inherit(palladio_get_theme_option_from_meta('counters'))
        ? 'comments'
        : palladio_array_get_keys_by_value(palladio_get_theme_option('counters'));

    if (!empty($palladio_components))
        palladio_show_post_meta(apply_filters('palladio_filter_post_meta_args', array(
                'components' => $palladio_components,
                'counters' => $palladio_counters,
                'seo' => false
            ), 'excerpt', 1)
        );

	
	// Post content
	?><div class="post_content entry-content"><?php

        // Featured image
        palladio_show_post_featured(array( 'thumb_size' => palladio_get_thumb_size( strpos(palladio_get_theme_option('body_style'), 'full')!==false ? 'full' : 'big' ) ));

        ?><div class="post_content_wrap"><?php

            // Title and post meta
        if (get_the_title() != '') {
            ?>
            <div class="post_header entry-header">
                <?php
                do_action('palladio_action_before_post_title');

                // Post title
                the_title( sprintf( '<h5 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h5>' );

                ?>
            </div><!-- .post_header --><?php
        }

		if (palladio_get_theme_option('blog_content') == 'fullpost') {
			// Post content area
			?><div class="post_content_inner"><?php
				the_content( '' );
			?></div><?php
			// Inner pages
			wp_link_pages( array(
				'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'palladio' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'palladio' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );

		} else {

			$palladio_show_learn_more = !in_array($palladio_post_format, array('link', 'aside', 'status', 'quote'));

			// Post content area
			?><div class="post_content_inner"><?php
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
			?></div><?php
			// More button
			if ( $palladio_show_learn_more ) {
                ?><p><a class="sc_button sc_button_simple sc_button_size_normal" href="<?php the_permalink(); ?>"><span class="sc_button_text"><?php esc_html_e('Read more', 'palladio'); ?></span></a></p><?php
			}

		}
	?></div><!-- .entry-content_wrap -->
	</div><!-- .entry-content -->
</article>