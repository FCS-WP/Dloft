<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

// Page (category, tag, archive, author) title

if ( palladio_need_page_title() ) {
	palladio_sc_layouts_showed('title', true);
	palladio_sc_layouts_showed('postmeta', true);
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( is_single() )  {
							?><div class="sc_layouts_title_meta"><?php
								palladio_show_post_meta(apply_filters('palladio_filter_post_meta_args', array(
									'components' => 'categories,date,counters,edit',
									'counters' => 'views,comments,likes',
									'seo' => true
									), 'header', 1)
								);
							?></div><?php
						}
						
						// Blog/Post title
						?><div class="sc_layouts_title_title"><?php
							$palladio_blog_title = palladio_get_blog_title();
							$palladio_blog_title_text = $palladio_blog_title_class = $palladio_blog_title_link = $palladio_blog_title_link_text = '';
							if (is_array($palladio_blog_title)) {
								$palladio_blog_title_text = $palladio_blog_title['text'];
								$palladio_blog_title_class = !empty($palladio_blog_title['class']) ? ' '.$palladio_blog_title['class'] : '';
								$palladio_blog_title_link = !empty($palladio_blog_title['link']) ? $palladio_blog_title['link'] : '';
								$palladio_blog_title_link_text = !empty($palladio_blog_title['link_text']) ? $palladio_blog_title['link_text'] : '';
							} else
								$palladio_blog_title_text = $palladio_blog_title;
							?>
							<h1 itemprop="headline" class="sc_layouts_title_caption<?php echo esc_attr($palladio_blog_title_class); ?>"><?php
								$palladio_top_icon = palladio_get_category_icon();
								if (!empty($palladio_top_icon)) {
									$palladio_attr = palladio_getimagesize($palladio_top_icon);
									?><img src="<?php echo esc_url($palladio_top_icon); ?>"  <?php if (!empty($palladio_attr[3])) palladio_show_layout($palladio_attr[3]);?>><?php
								}
								echo wp_kses_post($palladio_blog_title_text);
							?></h1>
							<?php
							if (!empty($palladio_blog_title_link) && !empty($palladio_blog_title_link_text)) {
								?><a href="<?php echo esc_url($palladio_blog_title_link); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html($palladio_blog_title_link_text); ?></a><?php
							}
							
							// Category/Tag description
							if ( is_category() || is_tag() || is_tax() ) 
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
		
						?></div><?php
	
						// Breadcrumbs
						?><div class="sc_layouts_title_breadcrumbs"><?php
							do_action( 'palladio_action_breadcrumbs');
						?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
?>