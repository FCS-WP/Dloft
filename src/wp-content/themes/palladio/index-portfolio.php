<?php
/**
 * The template for homepage posts with "Portfolio" style
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

palladio_storage_set('blog_archive', true);


get_header(); 

if (have_posts()) {

	echo get_query_var('blog_archive_start');

	$palladio_stickies = is_home() ? get_option( 'sticky_posts' ) : false;
	$palladio_sticky_out = palladio_get_theme_option('sticky_style')=='columns' 
							&& is_array($palladio_stickies) && count($palladio_stickies) > 0 && get_query_var( 'paged' ) < 1;
	
	// Show filters
	$palladio_cat = palladio_get_theme_option('parent_cat');
	$palladio_post_type = palladio_get_theme_option('post_type');
	$palladio_taxonomy = palladio_get_post_type_taxonomy($palladio_post_type);
	$palladio_show_filters = palladio_get_theme_option('show_filters');
	$palladio_tabs = array();
	if (!palladio_is_off($palladio_show_filters)) {
		$palladio_args = array(
			'type'			=> $palladio_post_type,
			'child_of'		=> $palladio_cat,
			'orderby'		=> 'name',
			'order'			=> 'ASC',
			'hide_empty'	=> 1,
			'hierarchical'	=> 0,
			'exclude'		=> '',
			'include'		=> '',
			'number'		=> '',
			'taxonomy'		=> $palladio_taxonomy,
			'pad_counts'	=> false
		);
		$palladio_portfolio_list = get_terms($palladio_args);
		if (is_array($palladio_portfolio_list) && count($palladio_portfolio_list) > 0) {
			$palladio_tabs[$palladio_cat] = esc_html__('All', 'palladio');
			foreach ($palladio_portfolio_list as $palladio_term) {
				if (isset($palladio_term->term_id)) $palladio_tabs[$palladio_term->term_id] = $palladio_term->name;
			}
		}
	}
	if (count($palladio_tabs) > 0) {
		$palladio_portfolio_filters_ajax = true;
		$palladio_portfolio_filters_active = $palladio_cat;
		$palladio_portfolio_filters_id = 'portfolio_filters';
		?>
		<div class="portfolio_filters palladio_tabs palladio_tabs_ajax">
			<ul class="portfolio_titles palladio_tabs_titles">
				<?php
				foreach ($palladio_tabs as $palladio_id=>$palladio_title) {
					?><li><a href="<?php echo esc_url(palladio_get_hash_link(sprintf('#%s_%s_content', $palladio_portfolio_filters_id, $palladio_id))); ?>" data-tab="<?php echo esc_attr($palladio_id); ?>"><?php echo esc_html($palladio_title); ?></a></li><?php
				}
				?>
			</ul>
			<?php
			$palladio_ppp = palladio_get_theme_option('posts_per_page');
			if (palladio_is_inherit($palladio_ppp)) $palladio_ppp = '';
			foreach ($palladio_tabs as $palladio_id=>$palladio_title) {
				$palladio_portfolio_need_content = $palladio_id==$palladio_portfolio_filters_active || !$palladio_portfolio_filters_ajax;
				?>
				<div id="<?php echo esc_attr(sprintf('%s_%s_content', $palladio_portfolio_filters_id, $palladio_id)); ?>"
					class="portfolio_content palladio_tabs_content"
					data-blog-template="<?php echo esc_attr(palladio_storage_get('blog_template')); ?>"
					data-blog-style="<?php echo esc_attr(palladio_get_theme_option('blog_style')); ?>"
					data-posts-per-page="<?php echo esc_attr($palladio_ppp); ?>"
					data-post-type="<?php echo esc_attr($palladio_post_type); ?>"
					data-taxonomy="<?php echo esc_attr($palladio_taxonomy); ?>"
					data-cat="<?php echo esc_attr($palladio_id); ?>"
					data-parent-cat="<?php echo esc_attr($palladio_cat); ?>"
					data-need-content="<?php echo (false===$palladio_portfolio_need_content ? 'true' : 'false'); ?>"
				>
					<?php
					if ($palladio_portfolio_need_content) 
						palladio_show_portfolio_posts(array(
							'cat' => $palladio_id,
							'parent_cat' => $palladio_cat,
							'taxonomy' => $palladio_taxonomy,
							'post_type' => $palladio_post_type,
							'page' => 1,
							'sticky' => $palladio_sticky_out
							)
						);
					?>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	} else {
		palladio_show_portfolio_posts(array(
			'cat' => $palladio_cat,
			'parent_cat' => $palladio_cat,
			'taxonomy' => $palladio_taxonomy,
			'post_type' => $palladio_post_type,
			'page' => 1,
			'sticky' => $palladio_sticky_out
			)
		);
	}

	echo get_query_var('blog_archive_end');

} else {

	if ( is_search() )
		get_template_part( 'content', 'none-search' );
	else
		get_template_part( 'content', 'none-archive' );

}

get_footer();
?>