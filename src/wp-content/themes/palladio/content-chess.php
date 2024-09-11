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
$palladio_columns = empty($palladio_blog_style[1]) ? 1 : max(1, $palladio_blog_style[1]);
$palladio_expanded = !palladio_sidebar_present() && palladio_is_on(palladio_get_theme_option('expand_content'));
$palladio_post_format = get_post_format();
$palladio_post_format = empty($palladio_post_format) ? 'standard' : str_replace('post-format-', '', $palladio_post_format);
$palladio_animation = palladio_get_theme_option('blog_animation');

?><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_chess post_layout_chess_'.esc_attr($palladio_columns).' post_format_'.esc_attr($palladio_post_format) ); ?>
	<?php echo (!palladio_is_off($palladio_animation) ? ' data-animation="'.esc_attr(palladio_get_animation_classes($palladio_animation)).'"' : ''); ?>>

	<?php
	// Add anchor
	if ($palladio_columns == 1 && shortcode_exists('trx_sc_anchor')) {
		echo do_shortcode('[trx_sc_anchor id="post_'.esc_attr(get_the_ID()).'" title="'.the_title_attribute( array( 'echo' => false ) ).'"]');
	}

	// Sticky label
	if ( is_sticky() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	// Featured image
	palladio_show_post_featured( array(
											'class' => $palladio_columns == 1 ? 'trx-stretch-height' : '',
											'show_no_image' => true,
											'thumb_bg' => true,
											'thumb_size' => palladio_get_thumb_size(
																	strpos(palladio_get_theme_option('body_style'), 'full')!==false
																		? ( $palladio_columns > 1 ? 'huge' : 'original' )
																		: (	$palladio_columns > 2 ? 'big' : 'huge')
																	)
											) 
										);

	?><div class="post_inner"><div class="post_inner_content"><?php 

		?><div class="post_header entry-header"><?php 
			do_action('palladio_action_before_post_title'); 

			// Post title
			the_title( sprintf( '<h3 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
			
			do_action('palladio_action_before_post_meta'); 

			// Post meta
			$palladio_components = palladio_is_inherit(palladio_get_theme_option_from_meta('meta_parts')) 
										? 'author,date'.($palladio_columns < 3 ? ',counters' : '')
										: palladio_array_get_keys_by_value(palladio_get_theme_option('meta_parts'));
			$palladio_counters = palladio_is_inherit(palladio_get_theme_option_from_meta('counters')) 
										? 'comments'
										: palladio_array_get_keys_by_value(palladio_get_theme_option('counters'));
			$palladio_post_meta = empty($palladio_components) 
										? '' 
										: palladio_show_post_meta(apply_filters('palladio_filter_post_meta_args', array(
												'components' => $palladio_components,
												'counters' => $palladio_counters,
												'seo' => false,
												'echo' => false
												), $palladio_blog_style[0], $palladio_columns)
											);
			palladio_show_layout($palladio_post_meta);
		?></div><!-- .entry-header -->
	
		<div class="post_content entry-content">
			<div class="post_content_inner">
				<?php
				$palladio_show_learn_more = !in_array($palladio_post_format, array('link', 'aside', 'status', 'quote'));
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
				palladio_show_layout($palladio_post_meta);
			}
			// More button
			if ( $palladio_show_learn_more ) {
				?><p><a class="sc_button sc_button_simple sc_button_size_normal" href="<?php the_permalink(); ?>"><?php esc_html_e('Read more', 'palladio'); ?></a></p><?php
			}
			?>
		</div><!-- .entry-content -->

	</div></div><!-- .post_inner -->

</article>