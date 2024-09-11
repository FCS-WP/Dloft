<?php
/**
 * Demo Templates: Main template with header & iframe
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.29
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="profile" href="//gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php if (trx_addons_demo_need_redirect()) { ?>
	<script>if (top.location.href != window.location.href) top.location.href = window.location.href;</script><?php } ?>
	<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>
	<?php do_action( 'trx_addons_demo_head' ); ?>
	<?php
	$theme_pt = trx_addons_get_option('demo_post_type');
	$theme_tax = trx_addons_get_option('demo_taxonomy');
	// Current theme data
	$theme_slug = trx_addons_get_value_gp(TRX_ADDONS_DEMO_PARAM);
	$theme_id = 0;
	$query = new WP_Query(array(
							'post_type' => $theme_pt,
							'post_status' => 'publish',
							'posts_per_page' => 1,
							'ignore_sticky_posts' => true,
							'meta_key' => 'trx_addons_edd_slug',
							'meta_value' => $theme_slug
							)
						);
	if ($query->found_posts > 0) {
		while ( $query->have_posts() ) { $query->the_post();
			$theme_id = get_the_ID();
		}
		wp_reset_postdata();
	}
	// Related themes list
	if ((int)$theme_id > 0) {
		$args = array(
			'ignore_sticky_posts' => true,
			'posts_per_page' => 8,
			'orderby' => 'title',
			'order' => 'ASC',
			'post_type' => $theme_pt,
			'post_status' => 'publish'
			);
		$terms = get_the_terms($theme_id, $theme_tax);
		$theme_cats = array();
		if (is_array($terms) && !empty($terms)) {
			foreach ($terms as $term) {
				$theme_cats[] = $term->term_id;
			}
		}
		if (count($theme_cats) > 0) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $theme_tax,
					'field' => 'term_taxonomy_id',
					'terms' => $theme_cats
				)
			);
		}
		$related_themes = array();
		$query = new WP_Query( $args );
		if ($query->found_posts > 0) {
			while ( $query->have_posts() ) { $query->the_post();
				$trx_addons_meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
				$image = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), trx_addons_get_thumb_size('masonry') );
				$related_themes[get_the_ID()] = array(
					'slug' => $trx_addons_meta['slug'],
					'title' => get_the_title(),
					'demo_url' => $trx_addons_meta['demo_url'],
					'top_url' => trx_addons_add_to_url(home_url(), array(TRX_ADDONS_DEMO_PARAM => $trx_addons_meta['slug'])),
					'download_url' => !empty($trx_addons_meta['download_url'])
										? trx_addons_add_referals_to_url($trx_addons_meta['download_url'], trx_addons_get_option('themes_market_referals'))
										: get_permalink(),
					'doc_url' => !empty($trx_addons_meta['doc_url']) 
										? $trx_addons_meta['doc_url'] 
										: trailingslashit($trx_addons_meta['demo_url']).'doc',
					'image' => $image[0]
				);
			}
			wp_reset_postdata();
		}

	} else {
		// Redirect to the homepage
		?><script>top.location.href = "<?php echo esc_url(trx_addons_get_option('demo_default_url') != '' ? trx_addons_get_option('demo_default_url') : home_url()); ?>";</script><?php
	}
	?>
</head>

<body class="trx_addons_demo">

	<?php do_action( 'trx_addons_demo_body' ); ?>

	<header id="header">
	
		<!-- Right col -->
			
		<a href="#" id="closeframe"></a>	
		<a href="<?php echo $related_themes[$theme_id]['download_url']; ?>" class="buynow"><?php esc_html_e('Buy Now', 'trx_addons'); ?></a>
		<a href="<?php echo $related_themes[$theme_id]['doc_url']; ?>" class="docs"><?php echo wp_kses_data('<strong>Online</strong>Documentation', 'trx_addons'); ?></a>
	
		<ul class="soc_links">
			<li><a href="http://twitter.com/themerex" class="twt"></a></li>
			<li><a href="http://facebook.com/themerex" class="fb"></a></li>
			<li><a href="http://themeforest.net/user/ThemeREX/portfolio" class="tf"></a></li>
		</ul>
					
		<div class="frame_controls">
			<ul class="controls">
				<li><a href="#" data-width="100%" class="current desktop" data-device="desktop"></a></li>
				<li><a href="#" data-width="451" class="tab_port" data-device="tab_port"></a></li>
				<li><a href="#" data-width="801" class="tab_land" data-device="tab_land"></a></li>
				<li><a href="#" data-width="320" class="mobile" data-device="mobile"></a></li>
			</ul>
			<a href="#" id="show_qr"><span></span></a>
			<div class="qr_wrap"><div id="qr_block"></div></div>
		</div>			
	
		<!-- End of right col -->	
		
		<!-- Left col -->
		<div class="left_col">	
			<?php
			$logo = trx_addons_get_option('demo_logo');
			if (!empty($logo)) {
				?>
				<div id="t_rex">
					<a href="<?php echo esc_url(trx_addons_get_option('demo_default_url') != '' ? trx_addons_get_option('demo_default_url') : home_url()); ?>">
						<img src="<?php echo esc_url($logo); ?>" alt="">
					</a>
				</div>
				<?php
			}
			?>
						
			<!-- List of related themes -->
			<div id="theme_selector">
				<a href="#" class="current_theme" data-current="<?php echo esc_attr($theme_id); ?>"><span class="themename"><?php echo esc_html($related_themes[$theme_id]['title']); ?></span><span class="switch"></span></a>
				<div class="themes_list">
					<div class="themes_list_inner">
						<h3><span class="wp_icon"></span><?php esc_html_e('Related Themes', 'trx_addons'); ?></h3>
						<ul class="themenames">
							<?php
							foreach ($related_themes as $id=>$theme) {
								?><li<?php if ($id == $theme_id) echo ' class="active"'; ?>><a href="<?php echo esc_url($theme['top_url']); ?>"
										 data-image="<?php echo esc_url($theme['image']); ?>"><?php
									 echo esc_html($theme['title']);
								?></a></li><?php
							}
							?>
						</ul>
						<div class="placeholder"><img src="<?php echo esc_url($related_themes[$theme_id]['image']); ?>" data-image="<?php echo esc_url($related_themes[$theme_id]['image']); ?>" alt="" /></div>
					</div>
				</div>
			</div>
		</div>
		<!-- End of left col -->
	</header>
	<div id="showframe"><div class="hide_wrap"><iframe src="<?php echo esc_url($related_themes[$theme_id]['demo_url']); ?>" frameborder="0" id="mainframe" style="width:100%;"></iframe></div></div>
	
	<?php do_action( 'trx_addons_demo_footer' ); ?>

</body>
</html>