<?php
/**
 * The default template to display the content of the single post, page or attachment
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

$palladio_seo = palladio_is_on(palladio_get_theme_option('seo_snippets'));
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post_item_single post_type_'.esc_attr(get_post_type()) 
												. ' post_format_'.esc_attr(str_replace('post-format-', '', get_post_format())) 
												);
		if ($palladio_seo) {
			?> itemscope="itemscope" 
			   itemprop="mainEntityOfPage" 
			   itemtype="//schema.org/<?php echo esc_attr(palladio_get_markup_schema()); ?>"
			   itemid="<?php echo esc_url(get_the_permalink()); ?>"
			   content="<?php the_title_attribute(); ?>"<?php
		}
?>><?php

	do_action('palladio_action_before_post_data'); 

	// Structured data snippets
	if ($palladio_seo)
		get_template_part('templates/seo');

	// Featured image
	if ( palladio_is_off(palladio_get_theme_option('hide_featured_on_single'))
			&& !palladio_sc_layouts_showed('featured') 
			&& strpos(get_the_content(), '[trx_widget_banner]')===false) {
		do_action('palladio_action_before_post_featured'); 
		palladio_show_post_featured();
		do_action('palladio_action_after_post_featured'); 
	} else if (has_post_thumbnail()) {
		?><meta itemprop="image" "//schema.org/ImageObject" content="<?php echo esc_url(wp_get_attachment_url(get_post_thumbnail_id())); ?>"><?php
	}

	// Title and post meta
	if ( !palladio_sc_layouts_showed('title') && !in_array(get_post_format(), array('link', 'aside', 'status', 'quote')) ) {
		do_action('palladio_action_before_post_title'); 
		?>
		<div class="post_header entry-header">
			<?php
			// Post title
			if (!palladio_sc_layouts_showed('title')) {
				the_title( '<h3 class="post_title entry-title"'.($palladio_seo ? ' itemprop="headline"' : '').'>', '</h3>' );
			}
			?>
		</div><!-- .post_header -->
		<?php
		do_action('palladio_action_after_post_title'); 
	}

	do_action('palladio_action_before_post_content');

	// Post content
	?>
	<div class="post_content entry-content" itemprop="articleBody">
		<?php
        // Post meta
        if ( !in_array(get_post_format(), array('link', 'aside', 'status', 'quote')) && palladio_is_on(palladio_get_theme_option('show_post_meta'))) {
            palladio_show_post_meta(apply_filters('palladio_filter_post_meta_args', array(
                    'components' => 'categories,author,date',
                    'counters' => 'comments',
                    'seo' => palladio_is_on(palladio_get_theme_option('seo_snippets'))
                ), 'single', 1)
            );
        }

		the_content( );

		do_action('palladio_action_before_post_pagination'); 

		wp_link_pages( array(
			'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'palladio' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span>',
			'link_after'  => '</span>',
			'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'palladio' ) . ' </span>%',
			'separator'   => '<span class="screen-reader-text">, </span>',
		) );

		// Taxonomies and share
		if ( is_single() && !is_attachment() ) {

			do_action('palladio_action_before_post_meta'); 

			?><div class="post_meta post_meta_single"><?php
				
				// Post taxonomies
				the_tags( '<span class="post_meta_item post_tags"><span class="post_meta_label">'.esc_html__('Tags:', 'palladio').'</span> ', '', '</span>' );

				// Share
				if (palladio_is_on(palladio_get_theme_option('show_share_links'))) {
					palladio_show_share_links(array(
							'type' => 'block',
							'caption' => '',
							'before' => '<span class="post_meta_item post_share">',
							'after' => '</span>'
						));
				}
			?></div><?php

			do_action('palladio_action_after_post_meta'); 
		}
		?>
	</div><!-- .entry-content -->
	

	<?php
	do_action('palladio_action_after_post_content'); 

	// Author bio.
	if ( palladio_get_theme_option('show_author_info')==1 && is_single() && !is_attachment() && get_the_author_meta( 'description' ) ) {
		do_action('palladio_action_before_post_author'); 
		get_template_part( 'templates/author-bio' );
		do_action('palladio_action_after_post_author'); 
	}

	do_action('palladio_action_after_post_data'); 
	?>
</article>
