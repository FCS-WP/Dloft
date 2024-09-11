<?php
/* Woocommerce support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if (!function_exists('palladio_woocommerce_theme_setup1')) {
	add_action( 'after_setup_theme', 'palladio_woocommerce_theme_setup1', 1 );
	function palladio_woocommerce_theme_setup1() {

		add_theme_support( 'woocommerce', array( 'product_grid' => array( 'max_columns' => 4 ) ) );

		// Next setting from the WooCommerce 3.0+ enable built-in image zoom on the single product page
		add_theme_support( 'wc-product-gallery-zoom' );

		// Next setting from the WooCommerce 3.0+ enable built-in image slider on the single product page
		add_theme_support( 'wc-product-gallery-slider' ); 

		// Next setting from the WooCommerce 3.0+ enable built-in image lightbox on the single product page
		add_theme_support( 'wc-product-gallery-lightbox' );

		add_filter( 'palladio_filter_list_sidebars', 	'palladio_woocommerce_list_sidebars' );
		add_filter( 'palladio_filter_list_posts_types',	'palladio_woocommerce_list_post_types');

		// Detect if WooCommerce support 'Product Grid' feature
		$product_grid = palladio_exists_woocommerce() && function_exists( 'wc_get_theme_support' ) ? wc_get_theme_support( 'product_grid' ) : false;
		add_theme_support( 'wc-product-grid-enable', isset( $product_grid['min_columns'] ) && isset( $product_grid['max_columns'] ) );
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if (!function_exists('palladio_woocommerce_theme_setup3')) {
	add_action( 'after_setup_theme', 'palladio_woocommerce_theme_setup3', 3 );
	function palladio_woocommerce_theme_setup3() {
		if (palladio_exists_woocommerce()) {
		
			// Section 'WooCommerce'
			palladio_storage_merge_array('options', '', array_merge(
				array(
					'shop' => array(
						"title" => esc_html__('Shop', 'palladio'),
						"desc" => wp_kses_data( __('Select parameters to display the shop pages', 'palladio') ),
						"type" => "section"
						),

					'products_info_shop' => array(
						"title" => esc_html__('Products list', 'palladio'),
						"desc" => '',
						"type" => "info",
						),
					'shop_mode' => array(
						"title" => esc_html__('Shop mode', 'palladio'),
						"desc" => wp_kses_data( __('Select style for the products list', 'palladio') ),
						"std" => 'thumbs',
						"options" => array(
							'thumbs'=> esc_html__('Thumbnails', 'palladio'),
							'list'	=> esc_html__('List', 'palladio'),
						),
						"type" => "select"
						),
					'shop_hover' => array(
						"title" => esc_html__('Hover style', 'palladio'),
						"desc" => wp_kses_data( __('Hover style on the products in the shop archive', 'palladio') ),
						"std" => 'shop',
						"options" => apply_filters('palladio_filter_shop_hover', array(
							'none' => esc_html__('None', 'palladio'),
							'shop' => esc_html__('Icon', 'palladio'),
						)),
						"type" => "select"
						),

					'single_info_shop' => array(
						"title" => esc_html__('Single product', 'palladio'),
						"desc" => '',
						"type" => "info",
						),
					'stretch_tabs_area' => array(
						"title" => esc_html__('Stretch tabs area', 'palladio'),
						"desc" => wp_kses_data( __('Stretch area with tabs on the single product to the screen width if the sidebar is hidden', 'palladio') ),
						"std" => 1,
						"type" => "checkbox"
						),
					'show_related_posts_shop' => array(
						"title" => esc_html__('Show related products', 'palladio'),
						"desc" => wp_kses_data( __("Show section 'Related products' on the single product page", 'palladio') ),
						"std" => 1,
						"type" => "checkbox"
						),
					'related_posts_shop' => array(
						"title" => esc_html__('Related products', 'palladio'),
						"desc" => wp_kses_data( __('How many related products should be displayed on the single product page?', 'palladio') ),
						"dependency" => array(
							'show_related_posts_shop' => array('1')
						),
						"std" => 3,
						"options" => palladio_get_list_range(1,9),
						"type" => "select"
						),
					'related_columns_shop' => array(
						"title" => esc_html__('Related columns', 'palladio'),
						"desc" => wp_kses_data( __('How many columns should be used to output related products on the single product page?', 'palladio') ),
						"dependency" => array(
							'show_related_posts_shop' => array('1')
						),
						"std" => 3,
						"options" => palladio_get_list_range(1,4),
						"type" => "select"
						)
				),
				palladio_options_get_list_cpt_options('shop')
			));
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('palladio_woocommerce_theme_setup9')) {
	add_action( 'after_setup_theme', 'palladio_woocommerce_theme_setup9', 9 );
	function palladio_woocommerce_theme_setup9() {
		
		if (palladio_exists_woocommerce()) {
			add_action( 'wp_enqueue_scripts', 								'palladio_woocommerce_frontend_scripts', 1100 );
			add_filter( 'palladio_filter_merge_styles',						'palladio_woocommerce_merge_styles' );
			add_filter( 'palladio_filter_merge_scripts',						'palladio_woocommerce_merge_scripts');
			add_filter( 'palladio_filter_get_post_info',		 				'palladio_woocommerce_get_post_info');
			add_filter( 'palladio_filter_post_type_taxonomy',				'palladio_woocommerce_post_type_taxonomy', 10, 2 );
			if (!is_admin()) {
				add_filter( 'palladio_filter_detect_blog_mode',				'palladio_woocommerce_detect_blog_mode');
				add_filter( 'palladio_filter_get_post_categories', 			'palladio_woocommerce_get_post_categories');
				add_filter( 'palladio_filter_allow_override_header_image',	'palladio_woocommerce_allow_override_header_image');
				add_filter( 'palladio_filter_get_blog_title',				'palladio_woocommerce_get_blog_title');
				add_action( 'pre_get_posts',								'palladio_woocommerce_pre_get_posts');
				add_filter( 'palladio_filter_localize_script',				'palladio_woocommerce_localize_script');
			}
		}
		if (is_admin()) {
			add_filter( 'palladio_filter_tgmpa_required_plugins',			'palladio_woocommerce_tgmpa_required_plugins' );
		}

		// Add wrappers and classes to the standard WooCommerce output
		if (palladio_exists_woocommerce()) {

			// Remove WOOC sidebar
			remove_action( 'woocommerce_sidebar', 						'woocommerce_get_sidebar', 10 );

			// Remove link around product item
			remove_action('woocommerce_before_shop_loop_item',			'woocommerce_template_loop_product_link_open', 10);
			remove_action('woocommerce_after_shop_loop_item',			'woocommerce_template_loop_product_link_close', 5);

			// Remove link around product category
			remove_action('woocommerce_before_subcategory',				'woocommerce_template_loop_category_link_open', 10);
			remove_action('woocommerce_after_subcategory',				'woocommerce_template_loop_category_link_close', 10);
			
			// Open main content wrapper - <article>
			remove_action( 'woocommerce_before_main_content',			'woocommerce_output_content_wrapper', 10);
			add_action(    'woocommerce_before_main_content',			'palladio_woocommerce_wrapper_start', 10);
			// Close main content wrapper - </article>
			remove_action( 'woocommerce_after_main_content',			'woocommerce_output_content_wrapper_end', 10);		
			add_action(    'woocommerce_after_main_content',			'palladio_woocommerce_wrapper_end', 10);

			// Close header section
			add_action(    'woocommerce_archive_description',			'palladio_woocommerce_archive_description', 15 );

			// Add theme specific search form
			add_filter(    'get_product_search_form',					'palladio_woocommerce_get_product_search_form' );


			// Add list mode buttons
			add_action(    'woocommerce_before_shop_loop', 				'palladio_woocommerce_before_shop_loop', 10 );


			// Open product/category item wrapper
			add_action(    'woocommerce_before_subcategory_title',		'palladio_woocommerce_item_wrapper_start', 9 );
			add_action(    'woocommerce_before_shop_loop_item_title',	'palladio_woocommerce_item_wrapper_start', 9 );
			// Close featured image wrapper and open title wrapper
			add_action(    'woocommerce_before_subcategory_title',		'palladio_woocommerce_title_wrapper_start', 20 );
			add_action(    'woocommerce_before_shop_loop_item_title',	'palladio_woocommerce_title_wrapper_start', 20 );

			// Add tags before title
			add_action(    'woocommerce_before_shop_loop_item_title',	'palladio_woocommerce_title_tags', 30 );

			// Wrap product title into link
			add_action(    'the_title',									'palladio_woocommerce_the_title');
			/// Wrap category title into link
			remove_action( 'woocommerce_shop_loop_subcategory_title',   'woocommerce_template_loop_category_title', 10 );
			add_action(    'woocommerce_shop_loop_subcategory_title',   'palladio_woocommerce_shop_loop_subcategory_title', 9, 1);

			// Close title wrapper and add description in the list mode
			add_action(    'woocommerce_after_shop_loop_item_title',	'palladio_woocommerce_title_wrapper_end', 7);
			add_action(    'woocommerce_after_subcategory_title',		'palladio_woocommerce_title_wrapper_end2', 10 );
			// Close product/category item wrapper
			add_action(    'woocommerce_after_subcategory',				'palladio_woocommerce_item_wrapper_end', 20 );
			add_action(    'woocommerce_after_shop_loop_item',			'palladio_woocommerce_item_wrapper_end', 20 );

			// Add product ID into product meta section (after categories and tags)
			add_action(    'woocommerce_product_meta_end',				'palladio_woocommerce_show_product_id', 10);
			
			// Set columns number for the product's thumbnails
			add_filter(    'woocommerce_product_thumbnails_columns',	'palladio_woocommerce_product_thumbnails_columns' );

			// Decorate price
			add_filter(    'woocommerce_get_price_html',				'palladio_woocommerce_get_price_html' );

	
			// Detect current shop mode
			if (!is_admin()) {
				$shop_mode = palladio_get_value_gpc('palladio_shop_mode');
				if (empty($shop_mode) && palladio_check_theme_option('shop_mode'))
					$shop_mode = palladio_get_theme_option('shop_mode');
				if (empty($shop_mode))
					$shop_mode = 'thumbs';
				palladio_storage_set('shop_mode', $shop_mode);
			}
		}
	}
}

// Theme init priorities:
// Action 'wp'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)
if (!function_exists('palladio_woocommerce_theme_setup_wp')) {
	add_action( 'wp', 'palladio_woocommerce_theme_setup_wp' );
	function palladio_woocommerce_theme_setup_wp() {
		if (palladio_exists_woocommerce()) {
			// Set columns number for the related products
			if ((int) palladio_get_theme_option('show_related_posts') == 0 || (int) palladio_get_theme_option('related_posts') == 0) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			} else {
				add_filter(    'woocommerce_output_related_products_args',	'palladio_woocommerce_output_related_products_args' );
				add_filter(    'woocommerce_related_products_columns',		'palladio_woocommerce_related_products_columns' );
			}
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'palladio_woocommerce_tgmpa_required_plugins' ) ) {
	
	function palladio_woocommerce_tgmpa_required_plugins($list=array()) {
		if (palladio_storage_isset('required_plugins', 'woocommerce')) {
			$list[] = array(
					'name' 		=> palladio_storage_get_array('required_plugins', 'woocommerce'),
					'slug' 		=> 'woocommerce',
					'required' 	=> false
				);
		}
		return $list;
	}
}


// Check if WooCommerce installed and activated
if ( !function_exists( 'palladio_exists_woocommerce' ) ) {
	function palladio_exists_woocommerce() {
		return class_exists('Woocommerce');
	}
}

// Return true, if current page is any woocommerce page
if ( !function_exists( 'palladio_is_woocommerce_page' ) ) {
	function palladio_is_woocommerce_page() {
		$rez = false;
		if (palladio_exists_woocommerce())
			$rez = is_woocommerce() || is_shop() || is_product() || is_product_category() || is_product_tag() || is_product_taxonomy() || is_cart() || is_checkout() || is_account_page();
		return $rez;
	}
}

// Detect current blog mode
if ( !function_exists( 'palladio_woocommerce_detect_blog_mode' ) ) {
	
	function palladio_woocommerce_detect_blog_mode($mode='') {
		if (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy())
			$mode = 'shop';
		else if (is_product() || is_cart() || is_checkout() || is_account_page())
			$mode = 'shop';
		return $mode;
	}
}

// Return current page title
if ( !function_exists( 'palladio_woocommerce_get_blog_title' ) ) {
	
	function palladio_woocommerce_get_blog_title($title='') {
		if (!palladio_exists_trx_addons() && palladio_exists_woocommerce() && palladio_is_woocommerce_page() && is_shop()) {
			$id = palladio_woocommerce_get_shop_page_id();
			$title = $id ? get_the_title($id) : esc_html__('Shop', 'palladio');
		}
		return $title;
	}
}


// Return taxonomy for current post type
if ( !function_exists( 'palladio_woocommerce_post_type_taxonomy' ) ) {
	
	function palladio_woocommerce_post_type_taxonomy($tax='', $post_type='') {
		if ($post_type == 'product')
			$tax = 'product_cat';
		return $tax;
	}
}

// Return true if page title section is allowed
if ( !function_exists( 'palladio_woocommerce_allow_override_header_image' ) ) {
	
	function palladio_woocommerce_allow_override_header_image($allow=true) {
		return is_product() ? false : $allow;
	}
}

// Return shop page ID
if ( !function_exists( 'palladio_woocommerce_get_shop_page_id' ) ) {
	function palladio_woocommerce_get_shop_page_id() {
		return get_option('woocommerce_shop_page_id');
	}
}

// Return shop page link
if ( !function_exists( 'palladio_woocommerce_get_shop_page_link' ) ) {
	function palladio_woocommerce_get_shop_page_link() {
		$url = '';
		$id = palladio_woocommerce_get_shop_page_id();
		if ($id) $url = get_permalink($id);
		return $url;
	}
}

// Show categories of the current product
if ( !function_exists( 'palladio_woocommerce_get_post_categories' ) ) {
	
	function palladio_woocommerce_get_post_categories($cats='') {
		if (get_post_type()=='product') {
			$cats = palladio_get_post_terms('', get_the_ID(), 'product_cat');
		}
		return $cats;
	}
}

// Add 'product' to the list of the supported post-types
if ( !function_exists( 'palladio_woocommerce_list_post_types' ) ) {
	
	function palladio_woocommerce_list_post_types($list=array()) {
		$list['product'] = esc_html__('Products', 'palladio');
		return $list;
	}
}

// Show price of the current product in the widgets and search results
if ( !function_exists( 'palladio_woocommerce_get_post_info' ) ) {
	
	function palladio_woocommerce_get_post_info($post_info='') {
		if (get_post_type()=='product') {
			global $product;
			if ( $price_html = $product->get_price_html() ) {
				$post_info = '<div class="post_price product_price price">' . trim($price_html) . '</div>' . $post_info;
			}
		}
		return $post_info;
	}
}

// Show price of the current product in the search results streampage
if ( !function_exists( 'palladio_woocommerce_action_before_post_meta' ) ) {
	
	function palladio_woocommerce_action_before_post_meta() {
		if (!is_single() && get_post_type()=='product') {
			global $product;
			if ( $price_html = $product->get_price_html() ) {
				?><div class="post_price product_price price"><?php palladio_show_layout($price_html); ?></div><?php
			}
		}
	}
}
	
// Enqueue WooCommerce custom styles
if ( !function_exists( 'palladio_woocommerce_frontend_scripts' ) ) {
	
	function palladio_woocommerce_frontend_scripts() {
			if (palladio_is_on(palladio_get_theme_option('debug_mode')) && palladio_get_file_dir('plugins/woocommerce/woocommerce.css')!='')
				wp_enqueue_style( 'palladio-woocommerce',  palladio_get_file_url('plugins/woocommerce/woocommerce.css'), array(), null );
			if (palladio_is_on(palladio_get_theme_option('debug_mode')) && palladio_get_file_dir('plugins/woocommerce/woocommerce.js')!='')
				wp_enqueue_script( 'palladio-woocommerce', palladio_get_file_url('plugins/woocommerce/woocommerce.js'), array('jquery'), null, true );
	}
}
	
// Merge custom styles
if ( !function_exists( 'palladio_woocommerce_merge_styles' ) ) {
	
	function palladio_woocommerce_merge_styles($list) {
		$list[] = 'plugins/woocommerce/woocommerce.css';
		return $list;
	}
}
	
// Merge custom scripts
if ( !function_exists( 'palladio_woocommerce_merge_scripts' ) ) {
	
	function palladio_woocommerce_merge_scripts($list) {
		$list[] = 'plugins/woocommerce/woocommerce.js';
		return $list;
	}
}



// Add WooCommerce specific items into lists
//------------------------------------------------------------------------

// Add sidebar
if ( !function_exists( 'palladio_woocommerce_list_sidebars' ) ) {
	
	function palladio_woocommerce_list_sidebars($list=array()) {
		$list['woocommerce_widgets'] = array(
											'name' => esc_html__('WooCommerce Widgets', 'palladio'),
											'description' => esc_html__('Widgets to be shown on the WooCommerce pages', 'palladio')
											);
		return $list;
	}
}




// Decorate WooCommerce output: Loop
//------------------------------------------------------------------------

// Add query vars to set products per page
if (!function_exists('palladio_woocommerce_pre_get_posts')) {
	
	function palladio_woocommerce_pre_get_posts($query) {
		if (!$query->is_main_query()) return;
		if ($query->get('post_type') == 'product') {
			$ppp = get_theme_mod('posts_per_page_shop', 0);
			if ($ppp > 0)
				$query->set('posts_per_page', $ppp);
		}
	}
}


// Before main content
if ( !function_exists( 'palladio_woocommerce_wrapper_start' ) ) {
	
	function palladio_woocommerce_wrapper_start() {
		if (is_product() || is_cart() || is_checkout() || is_account_page()) {
			?>
			<article class="post_item_single post_type_product">
			<?php
		} else {
			?>
			<div class="list_products shop_mode_<?php echo !palladio_storage_empty('shop_mode') ? palladio_storage_get('shop_mode') : 'thumbs'; ?>">
				<div class="list_products_header">
			<?php
		}
	}
}

// After main content
if ( !function_exists( 'palladio_woocommerce_wrapper_end' ) ) {
	
	function palladio_woocommerce_wrapper_end() {
		if (is_product() || is_cart() || is_checkout() || is_account_page()) {
			?>
			</article><!-- /.post_item_single -->
			<?php
		} else {
			?>
			</div><!-- /.list_products -->
			<?php
		}
	}
}

// Close header section
if ( !function_exists( 'palladio_woocommerce_archive_description' ) ) {
	
	function palladio_woocommerce_archive_description() {
		?>
		</div><!-- /.list_products_header -->
		<?php
	}
}

// Add list mode buttons
if ( !function_exists( 'palladio_woocommerce_before_shop_loop' ) ) {
	
	function palladio_woocommerce_before_shop_loop() {
		?>
		<div class="palladio_shop_mode_buttons"><form action="<?php echo esc_url(palladio_get_current_url()); ?>" method="post"><input type="hidden" name="palladio_shop_mode" value="<?php echo esc_attr(palladio_storage_get('shop_mode')); ?>" /><a href="#" class="woocommerce_thumbs icon-th" title="<?php esc_attr_e('Show products as thumbs', 'palladio'); ?>"></a><a href="#" class="woocommerce_list icon-th-list" title="<?php esc_attr_e('Show products as list', 'palladio'); ?>"></a></form></div><!-- /.palladio_shop_mode_buttons -->
		<?php
	}
}

// Add column class into product item in shop streampage
if ( !function_exists( 'palladio_woocommerce_loop_shop_columns_class' ) ) {
	
	
	function palladio_woocommerce_loop_shop_columns_class($classes, $class='', $cat='') {
		global $woocommerce_loop;
		if (is_product()) {
			if (!empty($woocommerce_loop['columns'])) {
				$classes[] = ' column-1_'.esc_attr($woocommerce_loop['columns']);
			}
		} else if (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy()) {
			$classes[] = ' column-1_'.esc_attr(max(2, min(4, palladio_get_theme_option('blog_columns'))));
		}
		return $classes;
	}
}


// Open item wrapper for categories and products
if ( !function_exists( 'palladio_woocommerce_item_wrapper_start' ) ) {
	
	
	function palladio_woocommerce_item_wrapper_start($cat='') {
		palladio_storage_set('in_product_item', true);
		$hover = palladio_get_theme_option('shop_hover');
		?>
		<div class="post_item post_layout_<?php echo esc_attr(palladio_storage_get('shop_mode')); ?>">
			<div class="post_featured hover_<?php echo esc_attr($hover); ?>">
				<?php do_action('palladio_action_woocommerce_item_featured_start'); ?>
				<a href="<?php echo esc_url(is_object($cat) ? get_term_link($cat->slug, 'product_cat') : get_permalink()); ?>">
				<?php
	}
}

// Open item wrapper for categories and products
if ( !function_exists( 'palladio_woocommerce_open_item_wrapper' ) ) {
	
	
	function palladio_woocommerce_title_wrapper_start($cat='') {
				?></a><?php
				do_action('palladio_action_woocommerce_item_featured_end');
				?>
			</div><!-- /.post_featured -->
			<div class="post_data">
				<div class="post_data_inner">
					<div class="post_header entry-header">
					<?php
	}
}


// Display product's tags before the title
if ( !function_exists( 'palladio_woocommerce_title_tags' ) ) {
	
	function palladio_woocommerce_title_tags() {
		global $product;
		palladio_show_layout(wc_get_product_tag_list( $product->get_id(), ', ', '<div class="post_tags product_tags">', '</div>' ));
	}
}

// Wrap product title into link
if ( !function_exists( 'palladio_woocommerce_the_title' ) ) {
	
	function palladio_woocommerce_the_title($title) {
		if (palladio_storage_get('in_product_item') && get_post_type()=='product') {
			$title = '<a href="'.esc_url(get_permalink()).'">'.esc_html($title).'</a>';
		}
		return $title;
	}
}

// Wrap category title into link
if ( !function_exists( 'palladio_woocommerce_shop_loop_subcategory_title' ) ) {
    
    function palladio_woocommerce_shop_loop_subcategory_title($cat) {

        $cat->name = sprintf('<a href="%s">%s</a>', esc_url(get_term_link($cat->slug, 'product_cat')), $cat->name);
        ?>
        <h2 class="woocommerce-loop-category__title">
        <?php
        echo trim($cat->name);

        if ( $cat->count > 0 ) {
            echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . esc_html( $cat->count ) . ')</mark>', $cat ); // WPCS: XSS ok.
        }
        ?>
        </h2><?php
    }
}

// Add excerpt in output for the product in the list mode
if ( !function_exists( 'palladio_woocommerce_title_wrapper_end' ) ) {
	
	function palladio_woocommerce_title_wrapper_end() {
			?>
			</div><!-- /.post_header -->
		<?php
		if (palladio_storage_get('shop_mode') == 'list' && (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy()) && !is_product()) {
		    $excerpt = apply_filters('the_excerpt', get_the_excerpt());
			?>
			<div class="post_content entry-content"><?php palladio_show_layout($excerpt); ?></div>
			<?php
		}
	}
}

// Add excerpt in output for the product in the list mode
if ( !function_exists( 'palladio_woocommerce_title_wrapper_end2' ) ) {
	
	function palladio_woocommerce_title_wrapper_end2($category) {
			?>
			</div><!-- /.post_header -->
		<?php
		if (palladio_storage_get('shop_mode') == 'list' && is_shop() && !is_product()) {
			?>
			<div class="post_content entry-content"><?php palladio_show_layout($category->description); ?></div><!-- /.post_content -->
			<?php
		}
	}
}

// Close item wrapper for categories and products
if ( !function_exists( 'palladio_woocommerce_close_item_wrapper' ) ) {
	
	
	function palladio_woocommerce_item_wrapper_end($cat='') {
                if (($hover = palladio_get_theme_option('shop_hover')) != 'none') {
                    ?><div class="mask"></div><?php
                    palladio_hovers_add_icons($hover, array('cat'=>$cat));
                }
                ?>
				</div><!-- /.post_data_inner -->
			</div><!-- /.post_data -->
		</div><!-- /.post_item -->
		<?php
		palladio_storage_set('in_product_item', false);
	}
}

// Change text on 'Add to cart' button
if ( !function_exists( 'palladio_woocommerce_add_to_cart_text' ) ) {
	
	
	function palladio_woocommerce_add_to_cart_text($text='') {
		return esc_html__('Buy now', 'palladio');
	}
}

// Decorate price
if ( !function_exists( 'palladio_woocommerce_get_price_html' ) ) {
	
	function palladio_woocommerce_get_price_html($price='') {
		if (!is_admin() && !empty($price)) {
			$sep = get_option('woocommerce_price_decimal_sep');
			if (empty($sep)) $sep = '.';
			$price = preg_replace('/([0-9,]+)(\\'.trim($sep).')([0-9]{2})/', '\\1<span class="decimals">\\3</span>', $price);
		}
		return $price;
	}
}



// Decorate WooCommerce output: Single product
//------------------------------------------------------------------------

// Add WooCommerce specific vars into localize array
if (!function_exists('palladio_woocommerce_localize_script')) {
	
	function palladio_woocommerce_localize_script($arr) {
		$arr['stretch_tabs_area'] = !palladio_sidebar_present() ? palladio_get_theme_option('stretch_tabs_area') : 0;
		return $arr;
	}
}

// Add Product ID for the single product
if ( !function_exists( 'palladio_woocommerce_show_product_id' ) ) {
	
	function palladio_woocommerce_show_product_id() {
		$authors = wp_get_post_terms(get_the_ID(), 'pa_product_author');
		if (is_array($authors) && count($authors)>0) {
			echo '<span class="product_author">'.esc_html__('Author: ', 'palladio');
			$delim = '';
			foreach ($authors as $author) {
				echo  esc_html($delim) . '<span>' . esc_html($author->name) . '</span>';
				$delim = ', ';
			}
			echo '</span>';
		}
		echo '<span class="product_id">'.esc_html__('Product ID: ', 'palladio') . '<span>' . get_the_ID() . '</span></span>';
	}
}

// Number columns for the product's thumbnails
if ( !function_exists( 'palladio_woocommerce_product_thumbnails_columns' ) ) {
	
	function palladio_woocommerce_product_thumbnails_columns($cols) {
		return 4;
	}
}

// Set products number for the related products
if ( !function_exists( 'palladio_woocommerce_output_related_products_args' ) ) {
	
	function palladio_woocommerce_output_related_products_args($args) {
		$args['posts_per_page'] = (int) palladio_get_theme_option('show_related_posts') 
										? max(0, min(9, palladio_get_theme_option('related_posts'))) 
										: 0;
		$args['columns'] = max(1, min(4, palladio_get_theme_option('related_columns')));
		return $args;
	}
}

// Set columns number for the related products
if ( !function_exists( 'palladio_woocommerce_related_products_columns' ) ) {
	
	function palladio_woocommerce_related_products_columns($columns) {
		$columns = max(1, min(4, palladio_get_theme_option('related_columns')));
		return $columns;
	}
}

if ( ! function_exists( 'palladio_woocommerce_price_filter_widget_step' ) ) {
    add_filter('woocommerce_price_filter_widget_step', 'palladio_woocommerce_price_filter_widget_step');
    function palladio_woocommerce_price_filter_widget_step( $step = '' ) {
        $step = 1;
        return $step;
    }
}

// Decorate WooCommerce output: Widgets
//------------------------------------------------------------------------

// Search form
if ( !function_exists( 'palladio_woocommerce_get_product_search_form' ) ) {
	
	function palladio_woocommerce_get_product_search_form($form) {
		return '
		<form role="search" method="get" class="search_form" action="' . esc_url(home_url('/')) . '">
			<input type="text" class="search_field" placeholder="' . esc_attr__('Search for products &hellip;', 'palladio') . '" value="' . get_search_query() . '" name="s" /><button class="search_button" type="submit">' . esc_html__('Search', 'palladio') . '</button>
			<input type="hidden" name="post_type" value="product" />
		</form>
		';
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if (palladio_exists_woocommerce()) { require_once PALLADIO_THEME_DIR . 'plugins/woocommerce/woocommerce.styles.php'; }
?>