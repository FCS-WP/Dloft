<?php
/**
 * Setup theme-specific fonts and colors
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.22
 */

if (!defined("PALLADIO_THEME_FREE")) define("PALLADIO_THEME_FREE", false);

// Theme storage
$PALLADIO_STORAGE = array(
	// Theme required plugin's slugs
	'required_plugins' => array_merge(

		// List of plugins for both - FREE and PREMIUM versions
		//-----------------------------------------------------
		array(
			// Required plugins
			// DON'T COMMENT OR REMOVE NEXT LINES!
			'trx_addons'					=> esc_html__('ThemeREX Addons', 'palladio'),
			
			// Recommended (supported) plugins
			// If plugin not need - comment (or remove) it
			'contact-form-7'				=> esc_html__('Contact Form 7', 'palladio'),
			'elegro-payment'				=> esc_html__('elegro Crypto Payment', 'palladio'),			
			'mailchimp-for-wp'				=> esc_html__('MailChimp for WP', 'palladio'),
			'woocommerce'					=> esc_html__('WooCommerce', 'palladio'),
			'trx_updater'					=> esc_html__('ThemeREX Updater', 'palladio'),
			'instagram-feed'				=> esc_html__('Instagram Feed', 'palladio')
		),

		// List of plugins for PREMIUM version only
		//-----------------------------------------------------
		PALLADIO_THEME_FREE ? array() : array(

			// Recommended (supported) plugins
			// If plugin not need - comment (or remove) it
			'js_composer'					=> esc_html__('WPBakery Page Builder', 'palladio'),
			'essential-grid'				=> esc_html__('Essential Grid', 'palladio'),
			'revslider'						=> esc_html__('Revolution Slider', 'palladio')
			
		)
	),
	
	// Theme-specific URLs (will be escaped in place of the output)
	'theme_demo_url' => '//palladio.ancorathemes.com',
	'theme_doc_url' => 'http://palladio.ancorathemes.com/doc',
    'theme_support_url' => 'https://themerex.net/support/',
    'theme_download_url'=> 'https://themeforest.net/item/palladio-interior-design-architecture-theme/20830679',
);

// Theme init priorities:
// Action 'after_setup_theme'
// 1 - register filters to add/remove lists items in the Theme Options
// 2 - create Theme Options
// 3 - add/remove Theme Options elements
// 5 - load Theme Options. Attention! After this step you can use only basic options (not overriden)
// 9 - register other filters (for installer, etc.)
//10 - standard Theme init procedures (not ordered)
// Action 'wp_loaded'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)

if ( !function_exists('palladio_customizer_theme_setup1') ) {
	add_action( 'after_setup_theme', 'palladio_customizer_theme_setup1', 1 );
	function palladio_customizer_theme_setup1() {

		// -----------------------------------------------------------------
		// -- ONLY FOR PROGRAMMERS, NOT FOR CUSTOMER
		// -- Internal theme settings
		// -----------------------------------------------------------------
		palladio_storage_set('settings', array(
			
			'duplicate_options'		=> 'child',		// none  - use separate options for template and child-theme
													// child - duplicate theme options from the main theme to the child-theme only
													// both  - sinchronize changes in the theme options between main and child themes
			
			'custmize_refresh'		=> 'auto',		// Refresh method for preview area in the Appearance - Customize:
													// auto - refresh preview area on change each field with Theme Options
													// manual - refresh only obn press button 'Refresh' at the top of Customize frame
		
			'max_load_fonts'		=> 5,			// Max fonts number to load from Google fonts or from uploaded fonts
		
			'comment_maxlength'		=> 1000,		// Max length of the message from contact form

			'comment_after_name'	=> true,		// Place 'comment' field before the 'name' and 'email'
			
			'socials_type'			=> 'icons',		// Type of socials:
													// icons - use font icons to present social networks
													// images - use images from theme's folder trx_addons/css/icons.png
			
			'icons_type'			=> 'icons',		// Type of other icons:
													// icons - use font icons to present icons
													// images - use images from theme's folder trx_addons/css/icons.png
			
			'icons_selector'		=> 'internal',	// Icons selector in the shortcodes:
													// vc (default) - standard VC icons selector (very slow and don't support images)
													// internal - internal popup with plugin's or theme's icons list (fast)
			'disable_jquery_ui'		=> false,		// Prevent loading custom jQuery UI libraries in the third-party plugins
		
			'use_mediaelements'		=> true,		// Load script "Media Elements" to play video and audio

			'tgmpa_upload'          => false,                   // Allow upload not pre-packaged plugins via TGMPA
			
			'allow_theme_layouts'	=> false		// Include theme's default headers and footers to the list after custom layouts
													// or leave in the list only custom layouts
		));


		// -----------------------------------------------------------------
		// -- Theme fonts (Google and/or custom fonts)
		// -----------------------------------------------------------------
		
		// Fonts to load when theme start
		// It can be Google fonts or uploaded fonts, placed in the folder /css/font-face/font-name inside the theme folder
		// Attention! Font's folder must have name equal to the font's name, with spaces replaced on the dash '-'
		
		palladio_storage_set('load_fonts', array(
			// Google font
			array(
				'name'	 => 'Inconsolata',
				'family' => 'monospace',
				'styles' => '400,700'		// Parameter 'style' used only for the Google fonts
				),
			array(
				'name'	 => 'Poppins',
				'family' => 'sans-serif',
				'styles' => '400,600,700'		// Parameter 'style' used only for the Google fonts
				),
			// Font-face packed with theme
			array(
				'name'   => 'Montserrat',
				'family' => 'sans-serif'
				)
		));
		
		// Characters subset for the Google fonts. Available values are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese
		palladio_storage_set('load_fonts_subset', 'latin,latin-ext');
		
		// Settings of the main tags
		palladio_storage_set('theme_fonts', array(
			'p' => array(
				'title'				=> esc_html__('Main text', 'palladio'),
				'description'		=> esc_html__('Font settings of the main text of the site', 'palladio'),
				'font-family'		=> '"Inconsolata",monospace',
				'font-size' 		=> '1rem',
				'font-weight'		=> '400',
				'font-style'		=> 'normal',
				'line-height'		=> '1.5em',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> '',
				'margin-top'		=> '0em',
				'margin-bottom'		=> '1.4em'
				),
			'h1' => array(
				'title'				=> esc_html__('Heading 1', 'palladio'),
				'font-family'		=> '"Poppins",sans-serif',
				'font-size' 		=> '4.706em',
				'font-weight'		=> '700',
				'font-style'		=> 'normal',
				'line-height'		=> '1.25em',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> '',
				'margin-top'		=> '0.9583em',
				'margin-bottom'		=> '0.5833em'
				),
			'h2' => array(
				'title'				=> esc_html__('Heading 2', 'palladio'),
				'font-family'		=> '"Poppins",sans-serif',
				'font-size' 		=> '4.118em',
				'font-weight'		=> '700',
				'font-style'		=> 'normal',
				'line-height'		=> '1.1111em',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> '',
				'margin-top'		=> '1.0444em',
				'margin-bottom'		=> '0.76em'
				),
			'h3' => array(
				'title'				=> esc_html__('Heading 3', 'palladio'),
				'font-family'		=> '"Poppins",sans-serif',
				'font-size' 		=> '3.235em',
				'font-weight'		=> '700',
				'font-style'		=> 'normal',
				'line-height'		=> '1.1515em',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> '',
				'margin-top'		=> '1.3333em',
				'margin-bottom'		=> '0.7879em'
				),
			'h4' => array(
				'title'				=> esc_html__('Heading 4', 'palladio'),
				'font-family'		=> '"Poppins",sans-serif',
				'font-size' 		=> '2.353em',
				'font-weight'		=> '700',
				'font-style'		=> 'normal',
				'line-height'		=> '1.3043em',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> '',
				'margin-top'		=> '1.6565em',
				'margin-bottom'		=> '1.0435em'
				),
			'h5' => array(
				'title'				=> esc_html__('Heading 5', 'palladio'),
				'font-family'		=> '"Poppins",sans-serif',
				'font-size' 		=> '1.765em',
				'font-weight'		=> '700',
				'font-style'		=> 'normal',
				'line-height'		=> '1.35em',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> '',
				'margin-top'		=> '1.5em',
				'margin-bottom'		=> '1.3em'
				),
			'h6' => array(
				'title'				=> esc_html__('Heading 6', 'palladio'),
				'font-family'		=> '"Inconsolata",monospace',
				'font-size' 		=> '1.176em',
				'font-weight'		=> '700',
				'font-style'		=> 'normal',
				'line-height'		=> '1.4706em',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> '',
				'margin-top'		=> '2.1176em',
				'margin-bottom'		=> '0.9412em'
				),
			'logo' => array(
				'title'				=> esc_html__('Logo text', 'palladio'),
				'description'		=> esc_html__('Font settings of the text case of the logo', 'palladio'),
				'font-family'		=> '"Poppins",sans-serif',
				'font-size' 		=> '1.765em',
				'font-weight'		=> '700',
				'font-style'		=> 'normal',
				'line-height'		=> '1.25em',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> 'normal'
				),
			'button' => array(
				'title'				=> esc_html__('Buttons', 'palladio'),
				'font-family'		=> '"Inconsolata",monospace',
				'font-size' 		=> '1.059em',
				'font-weight'		=> '700',
				'font-style'		=> 'normal',
				'line-height'		=> '1.5em',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> 'normal'
				),
			'input' => array(
				'title'				=> esc_html__('Input fields', 'palladio'),
				'description'		=> esc_html__('Font settings of the input fields, dropdowns and textareas', 'palladio'),
				'font-family'		=> 'inherit',
				'font-size' 		=> '1em',
				'font-weight'		=> 'normal',
				'font-style'		=> 'normal',
				'line-height'		=> 'normal',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> 'normal'
				),
			'info' => array(
				'title'				=> esc_html__('Post meta', 'palladio'),
				'description'		=> esc_html__('Font settings of the post meta: date, counters, share, etc.', 'palladio'),
				'font-family'		=> 'inherit',
				'font-size' 		=> 'inherit',
				'font-weight'		=> 'normal',
				'font-style'		=> 'normal',
				'line-height'		=> 'normal',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> 'normal',
				'margin-top'		=> '0.4em',
				'margin-bottom'		=> ''
				),
			'menu' => array(
				'title'				=> esc_html__('Main menu', 'palladio'),
				'description'		=> esc_html__('Font settings of the main menu items', 'palladio'),
				'font-family'		=> '"Poppins",sans-serif',
				'font-size' 		=> '1em',
				'font-weight'		=> 'normal',
				'font-style'		=> 'normal',
				'line-height'		=> '1.5em',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> '0px'
				),
			'submenu' => array(
				'title'				=> esc_html__('Dropdown menu', 'palladio'),
				'description'		=> esc_html__('Font settings of the dropdown menu items', 'palladio'),
				'font-family'		=> '"Inconsolata",sans-serif',
				'font-size' 		=> '1em',
				'font-weight'		=> 'normal',
				'font-style'		=> 'normal',
				'line-height'		=> '1.5em',
				'text-decoration'	=> 'none',
				'text-transform'	=> 'none',
				'letter-spacing'	=> '0px'
				)
		));
		
		
		// -----------------------------------------------------------------
		// -- Theme colors for customizer
		// -- Attention! Inner scheme must be last in the array below
		// -----------------------------------------------------------------
		palladio_storage_set('scheme_color_groups', array(
			'main'	=> array(
							'title'			=> esc_html__('Main', 'palladio'),
							'description'	=> esc_html__('Colors of the main content area', 'palladio')
							),
			'alter'	=> array(
							'title'			=> esc_html__('Alter', 'palladio'),
							'description'	=> esc_html__('Colors of the alternative blocks (sidebars, etc.)', 'palladio')
							),
			'extra'	=> array(
							'title'			=> esc_html__('Extra', 'palladio'),
							'description'	=> esc_html__('Colors of the extra blocks (dropdowns, price blocks, table headers, etc.)', 'palladio')
							),
			'inverse' => array(
							'title'			=> esc_html__('Inverse', 'palladio'),
							'description'	=> esc_html__('Colors of the inverse blocks - when link color used as background of the block (dropdowns, blockquotes, etc.)', 'palladio')
							),
			'input'	=> array(
							'title'			=> esc_html__('Input', 'palladio'),
							'description'	=> esc_html__('Colors of the form fields (text field, textarea, select, etc.)', 'palladio')
							),
			)
		);
		palladio_storage_set('scheme_color_names', array(
			'bg_color'	=> array(
							'title'			=> esc_html__('Background color', 'palladio'),
							'description'	=> esc_html__('Background color of this block in the normal state', 'palladio')
							),
			'bg_hover'	=> array(
							'title'			=> esc_html__('Background hover', 'palladio'),
							'description'	=> esc_html__('Background color of this block in the hovered state', 'palladio')
							),
			'bd_color'	=> array(
							'title'			=> esc_html__('Border color', 'palladio'),
							'description'	=> esc_html__('Border color of this block in the normal state', 'palladio')
							),
			'bd_hover'	=>  array(
							'title'			=> esc_html__('Border hover', 'palladio'),
							'description'	=> esc_html__('Border color of this block in the hovered state', 'palladio')
							),
			'text'		=> array(
							'title'			=> esc_html__('Text', 'palladio'),
							'description'	=> esc_html__('Color of the plain text inside this block', 'palladio')
							),
			'text_dark'	=> array(
							'title'			=> esc_html__('Text dark', 'palladio'),
							'description'	=> esc_html__('Color of the dark text (bold, header, etc.) inside this block', 'palladio')
							),
			'text_light'=> array(
							'title'			=> esc_html__('Text light', 'palladio'),
							'description'	=> esc_html__('Color of the light text (post meta, etc.) inside this block', 'palladio')
							),
			'text_link'	=> array(
							'title'			=> esc_html__('Link', 'palladio'),
							'description'	=> esc_html__('Color of the links inside this block', 'palladio')
							),
			'text_hover'=> array(
							'title'			=> esc_html__('Link hover', 'palladio'),
							'description'	=> esc_html__('Color of the hovered state of links inside this block', 'palladio')
							),
			'text_link2'=> array(
							'title'			=> esc_html__('Link 2', 'palladio'),
							'description'	=> esc_html__('Color of the accented texts (areas) inside this block', 'palladio')
							),
			'text_hover2'=> array(
							'title'			=> esc_html__('Link 2 hover', 'palladio'),
							'description'	=> esc_html__('Color of the hovered state of accented texts (areas) inside this block', 'palladio')
							),
			'text_link3'=> array(
							'title'			=> esc_html__('Link 3', 'palladio'),
							'description'	=> esc_html__('Color of the other accented texts (buttons) inside this block', 'palladio')
							),
			'text_hover3'=> array(
							'title'			=> esc_html__('Link 3 hover', 'palladio'),
							'description'	=> esc_html__('Color of the hovered state of other accented texts (buttons) inside this block', 'palladio')
							)
			)
		);
		palladio_storage_set('schemes', array(
		
			// Color scheme: 'default'
			'default' => array(
				'title'	 => esc_html__('Default', 'palladio'),
				'colors' => array(
					
					// Whole block border and background
					'bg_color'			=> '#ffffff',
					'bd_color'			=> '#e5e5e5',
		
					// Text and links colors
					'text'				=> '#818181',
					'text_light'		=> '#818181',
					'text_dark'			=> '#000000',
					'text_link'			=> '#000000',
					'text_hover'		=> '#818181',
					'text_link2'		=> '#d9e1c3',
					'text_hover2'		=> '#8be77c',
					'text_link3'		=> '#ddb837',
					'text_hover3'		=> '#eec432',
		
					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'	=> '#f7f7f7',
					'alter_bg_hover'	=> '#d9e1c3',
					'alter_bd_color'	=> '#e5e5e5',
					'alter_bd_hover'	=> '#818181',
					'alter_text'		=> '#818181',
					'alter_light'		=> '#cdcdcd',
					'alter_dark'		=> '#000000',
					'alter_link'		=> '#000000',
					'alter_hover'		=> '#72cfd5',
					'alter_link2'		=> '#8be77c',
					'alter_hover2'		=> '#80d572',
					'alter_link3'		=> '#eec432',
					'alter_hover3'		=> '#ddb837',
		
					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'	=> '#161616',
					'extra_bg_hover'	=> '#28272e',
					'extra_bd_color'	=> '#313131',
					'extra_bd_hover'	=> '#3d3d3d',
					'extra_text'		=> '#ffffff',
					'extra_light'		=> '#161616',
					'extra_dark'		=> '#ffffff',
					'extra_link'		=> '#818181',
					'extra_hover'		=> '#ffffff',
					'extra_link2'		=> '#80d572',
					'extra_hover2'		=> '#8be77c',
					'extra_link3'		=> '#ddb837',
					'extra_hover3'		=> '#eec432',
		
					// Input fields (form's fields and textarea)
					'input_bg_color'	=> '#ffffff',
					'input_bg_hover'	=> '#ffffff',
					'input_bd_color'	=> '#000000',
					'input_bd_hover'	=> '#e5e5e5',
					'input_text'		=> '#818181',
					'input_light'		=> '#d0d0d0',
					'input_dark'		=> '#1d1d1d',
					
					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color'	=> '#1e1d22',
					'inverse_bd_hover'	=> '#5aa4a9',
					'inverse_text'		=> '#ffffff',
					'inverse_light'		=> '#333333',
					'inverse_dark'		=> '#000000',
					'inverse_link'		=> '#ffffff',
					'inverse_hover'		=> '#1d1d1d'
				)
			),
		
			// Color scheme: 'dark'
			'dark' => array(
				'title'  => esc_html__('Dark', 'palladio'),
				'colors' => array(
					
					// Whole block border and background
					'bg_color'			=> '#0e0d12',
					'bd_color'			=> '#1c1b1f',
		
					// Text and links colors
					'text'				=> '#b7b7b7',
					'text_light'		=> '#5f5f5f',
					'text_dark'			=> '#ffffff',
					'text_link'			=> '#000000',
					'text_hover'		=> '#ffffff',
					'text_link2'		=> '#80d572',
					'text_hover2'		=> '#8be77c',
					'text_link3'		=> '#ddb837',
					'text_hover3'		=> '#eec432',

					// Alternative blocks (sidebar, tabs, alternative blocks, etc.)
					'alter_bg_color'	=> '#1e1d22',
					'alter_bg_hover'	=> '#28272e',
					'alter_bd_color'	=> '#313131',
					'alter_bd_hover'	=> '#3d3d3d',
					'alter_text'		=> '#a6a6a6',
					'alter_light'		=> '#5f5f5f',
					'alter_dark'		=> '#ffffff',
					'alter_link'		=> '#818181',
					'alter_hover'		=> '#fe7259',
					'alter_link2'		=> '#8be77c',
					'alter_hover2'		=> '#80d572',
					'alter_link3'		=> '#eec432',
					'alter_hover3'		=> '#ddb837',

					// Extra blocks (submenu, tabs, color blocks, etc.)
					'extra_bg_color'	=> '#1e1d22',
					'extra_bg_hover'	=> '#28272e',
					'extra_bd_color'	=> '#313131',
					'extra_bd_hover'	=> '#3d3d3d',
					'extra_text'		=> '#ffffff',
					'extra_light'		=> '#5f5f5f',
					'extra_dark'		=> '#ffffff',
					'extra_link'		=> '#818181',
					'extra_hover'		=> '#fe7259',
					'extra_link2'		=> '#80d572',
					'extra_hover2'		=> '#8be77c',
					'extra_link3'		=> '#ddb837',
					'extra_hover3'		=> '#eec432',

					// Input fields (form's fields and textarea)
					'input_bg_color'	=> '#2e2d32',
					'input_bg_hover'	=> '#2e2d32',
					'input_bd_color'	=> '#2e2d32',
					'input_bd_hover'	=> '#353535',
					'input_text'		=> '#b7b7b7',
					'input_light'		=> '#5f5f5f',
					'input_dark'		=> '#ffffff',
					
					// Inverse blocks (text and links on the 'text_link' background)
					'inverse_bd_color'	=> '#e36650',
					'inverse_bd_hover'	=> '#cb5b47',
					'inverse_text'		=> '#1d1d1d',
					'inverse_light'		=> '#5f5f5f',
					'inverse_dark'		=> '#000000',
					'inverse_link'		=> '#ffffff',
					'inverse_hover'		=> '#1d1d1d'
				)
			)
		
		));
		
		// Simple schemes substitution
		palladio_storage_set('schemes_simple', array(
			// Main color	// Slave elements and it's darkness koef.
			'text_link'		=> array('alter_hover' => 1,	'extra_link' => 1, 'inverse_bd_color' => 0.85, 'inverse_bd_hover' => 0.7),
			'text_hover'	=> array('alter_link' => 1,		'extra_hover' => 1),
			'text_link2'	=> array('alter_hover2' => 1,	'extra_link2' => 1),
			'text_hover2'	=> array('alter_link2' => 1,	'extra_hover2' => 1),
			'text_link3'	=> array('alter_hover3' => 1,	'extra_link3' => 1),
			'text_hover3'	=> array('alter_link3' => 1,	'extra_hover3' => 1)
		));
	}
}

			
// Additional (calculated) theme-specific colors
// Attention! Don't forget setup custom colors also in the theme.customizer.color-scheme.js
if (!function_exists('palladio_customizer_add_theme_colors')) {
	function palladio_customizer_add_theme_colors($colors) {
		if (substr($colors['text'], 0, 1) == '#') {
			$colors['bg_color_0']  = palladio_hex2rgba( $colors['bg_color'], 0 );
			$colors['bg_color_02']  = palladio_hex2rgba( $colors['bg_color'], 0.2 );
			$colors['bg_color_07']  = palladio_hex2rgba( $colors['bg_color'], 0.7 );
			$colors['bg_color_08']  = palladio_hex2rgba( $colors['bg_color'], 0.8 );
			$colors['bg_color_09']  = palladio_hex2rgba( $colors['bg_color'], 0.9 );
			$colors['alter_bg_color_07']  = palladio_hex2rgba( $colors['alter_bg_color'], 0.7 );
			$colors['alter_bg_color_04']  = palladio_hex2rgba( $colors['alter_bg_color'], 0.4 );
			$colors['alter_bg_color_02']  = palladio_hex2rgba( $colors['alter_bg_color'], 0.2 );
			$colors['alter_bd_color_02']  = palladio_hex2rgba( $colors['alter_bd_color'], 0.2 );
			$colors['extra_bg_color_07']  = palladio_hex2rgba( $colors['extra_bg_color'], 0.7 );
			$colors['text_dark_07']  = palladio_hex2rgba( $colors['text_dark'], 0.7 );
			$colors['text_link_02']  = palladio_hex2rgba( $colors['text_link'], 0.2 );
			$colors['text_link_07']  = palladio_hex2rgba( $colors['text_link'], 0.7 );
			$colors['text_link_blend'] = palladio_hsb2hex(palladio_hex2hsb( $colors['text_link'], 2, -5, 5 ));
			$colors['alter_link_blend'] = palladio_hsb2hex(palladio_hex2hsb( $colors['alter_link'], 2, -5, 5 ));
		} else {
			$colors['bg_color_0'] = '{{ data.bg_color_0 }}';
			$colors['bg_color_02'] = '{{ data.bg_color_02 }}';
			$colors['bg_color_07'] = '{{ data.bg_color_07 }}';
			$colors['bg_color_08'] = '{{ data.bg_color_08 }}';
			$colors['bg_color_09'] = '{{ data.bg_color_09 }}';
			$colors['alter_bg_color_07'] = '{{ data.alter_bg_color_07 }}';
			$colors['alter_bg_color_04'] = '{{ data.alter_bg_color_04 }}';
			$colors['alter_bg_color_02'] = '{{ data.alter_bg_color_02 }}';
			$colors['alter_bd_color_02'] = '{{ data.alter_bd_color_02 }}';
			$colors['extra_bg_color_07'] = '{{ data.extra_bg_color_07 }}';
			$colors['text_dark_07'] = '{{ data.text_dark_07 }}';
			$colors['text_link_02'] = '{{ data.text_link_02 }}';
			$colors['text_link_07'] = '{{ data.text_link_07 }}';
			$colors['text_link_blend'] = '{{ data.text_link_blend }}';
			$colors['alter_link_blend'] = '{{ data.alter_link_blend }}';
		}
		return $colors;
	}
}


			
// Additional theme-specific fonts rules
// Attention! Don't forget setup fonts rules also in the theme.customizer.color-scheme.js
if (!function_exists('palladio_customizer_add_theme_fonts')) {
	function palladio_customizer_add_theme_fonts($fonts) {
		$rez = array();	
		foreach ($fonts as $tag => $font) {
			if (substr($font['font-family'], 0, 2) != '{{') {
				$rez[$tag.'_font-family'] 		= !empty($font['font-family']) && !palladio_is_inherit($font['font-family'])
														? 'font-family:' . trim($font['font-family']) . ';' 
														: '';
				$rez[$tag.'_font-size'] 		= !empty($font['font-size']) && !palladio_is_inherit($font['font-size'])
														? 'font-size:' . palladio_prepare_css_value($font['font-size']) . ";"
														: '';
				$rez[$tag.'_line-height'] 		= !empty($font['line-height']) && !palladio_is_inherit($font['line-height'])
														? 'line-height:' . trim($font['line-height']) . ";"
														: '';
				$rez[$tag.'_font-weight'] 		= !empty($font['font-weight']) && !palladio_is_inherit($font['font-weight'])
														? 'font-weight:' . trim($font['font-weight']) . ";"
														: '';
				$rez[$tag.'_font-style'] 		= !empty($font['font-style']) && !palladio_is_inherit($font['font-style'])
														? 'font-style:' . trim($font['font-style']) . ";"
														: '';
				$rez[$tag.'_text-decoration'] 	= !empty($font['text-decoration']) && !palladio_is_inherit($font['text-decoration'])
														? 'text-decoration:' . trim($font['text-decoration']) . ";"
														: '';
				$rez[$tag.'_text-transform'] 	= !empty($font['text-transform']) && !palladio_is_inherit($font['text-transform'])
														? 'text-transform:' . trim($font['text-transform']) . ";"
														: '';
				$rez[$tag.'_letter-spacing'] 	= !empty($font['letter-spacing']) && !palladio_is_inherit($font['letter-spacing'])
														? 'letter-spacing:' . trim($font['letter-spacing']) . ";"
														: '';
				$rez[$tag.'_margin-top'] 		= !empty($font['margin-top']) && !palladio_is_inherit($font['margin-top'])
														? 'margin-top:' . palladio_prepare_css_value($font['margin-top']) . ";"
														: '';
				$rez[$tag.'_margin-bottom'] 	= !empty($font['margin-bottom']) && !palladio_is_inherit($font['margin-bottom'])
														? 'margin-bottom:' . palladio_prepare_css_value($font['margin-bottom']) . ";"
														: '';
			} else {
				$rez[$tag.'_font-family']		= '{{ data["'.$tag.'_font-family"] }}';
				$rez[$tag.'_font-size']			= '{{ data["'.$tag.'_font-size"] }}';
				$rez[$tag.'_line-height']		= '{{ data["'.$tag.'_line-height"] }}';
				$rez[$tag.'_font-weight']		= '{{ data["'.$tag.'_font-weight"] }}';
				$rez[$tag.'_font-style']		= '{{ data["'.$tag.'_font-style"] }}';
				$rez[$tag.'_text-decoration']	= '{{ data["'.$tag.'_text-decoration"] }}';
				$rez[$tag.'_text-transform']	= '{{ data["'.$tag.'_text-transform"] }}';
				$rez[$tag.'_letter-spacing']	= '{{ data["'.$tag.'_letter-spacing"] }}';
				$rez[$tag.'_margin-top']		= '{{ data["'.$tag.'_margin-top"] }}';
				$rez[$tag.'_margin-bottom']		= '{{ data["'.$tag.'_margin-bottom"] }}';
			}
		}
		return $rez;
	}
}




//-------------------------------------------------------
//-- Thumb sizes
//-------------------------------------------------------

if ( !function_exists('palladio_customizer_theme_setup') ) {
	add_action( 'after_setup_theme', 'palladio_customizer_theme_setup' );
	function palladio_customizer_theme_setup() {

		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size(370, 0, false);
		
		// Add thumb sizes
		// ATTENTION! If you change list below - check filter's names in the 'trx_addons_filter_get_thumb_size' hook
		$thumb_sizes = apply_filters('palladio_filter_add_thumb_sizes', array(
			'palladio-thumb-huge'		=> array(1170, 658, true),
			'palladio-thumb-big' 		=> array( 760, 428, true),
			'palladio-thumb-med' 		=> array( 370, 302, true),
			'palladio-thumb-tiny' 		=> array(  90,  90, true),
			'palladio-thumb-masonry-big' => array( 760,   0, false),		// Only downscale, not crop
			'palladio-thumb-masonry'		=> array( 370,   0, false),		// Only downscale, not crop
			'palladio-thumb-avatar'		=> array( 370, 457, true),
            'palladio-thumb-big-vertical'=> array( 600, 480, true)
            )
		);
		$mult = palladio_get_theme_option('retina_ready', 1);
		if ($mult > 1) $GLOBALS['content_width'] = apply_filters( 'palladio_filter_content_width', 1170*$mult);
		foreach ($thumb_sizes as $k=>$v) {
			// Add Original dimensions
			add_image_size( $k, $v[0], $v[1], $v[2]);
			// Add Retina dimensions
			if ($mult > 1) add_image_size( $k.'-@retina', $v[0]*$mult, $v[1]*$mult, $v[2]);
		}

	}
}

if ( !function_exists('palladio_customizer_image_sizes') ) {
	add_filter( 'image_size_names_choose', 'palladio_customizer_image_sizes' );
	function palladio_customizer_image_sizes( $sizes ) {
		$thumb_sizes = apply_filters('palladio_filter_add_thumb_sizes', array(
			'palladio-thumb-huge'		=> esc_html__( 'Huge image', 'palladio' ),
			'palladio-thumb-big'			=> esc_html__( 'Large image', 'palladio' ),
			'palladio-thumb-med'			=> esc_html__( 'Medium image', 'palladio' ),
			'palladio-thumb-tiny'		=> esc_html__( 'Small square avatar', 'palladio' ),
			'palladio-thumb-masonry-big'	=> esc_html__( 'Masonry Large (scaled)', 'palladio' ),
			'palladio-thumb-masonry'		=> esc_html__( 'Masonry (scaled)', 'palladio' ),
			'palladio-thumb-avatar'		=> esc_html__( 'Avatar', 'palladio' ),
			'palladio-thumb-big-vertical'=> esc_html__( 'Large vertical image', 'palladio' ),
			)
		);
		$mult = palladio_get_theme_option('retina_ready', 1);
		foreach($thumb_sizes as $k=>$v) {
			$sizes[$k] = $v;
			if ($mult > 1) $sizes[$k.'-@retina'] = $v.' '.esc_html__('@2x', 'palladio' );
		}
		return $sizes;
	}
}

// Remove some thumb-sizes from the ThemeREX Addons list
if ( !function_exists( 'palladio_customizer_trx_addons_add_thumb_sizes' ) ) {
	add_filter( 'trx_addons_filter_add_thumb_sizes', 'palladio_customizer_trx_addons_add_thumb_sizes');
	function palladio_customizer_trx_addons_add_thumb_sizes($list=array()) {
		if (is_array($list)) {
			foreach ($list as $k=>$v) {
				if (in_array($k, array(
								'trx_addons-thumb-huge',
								'trx_addons-thumb-big',
								'trx_addons-thumb-medium',
								'trx_addons-thumb-tiny',
								'trx_addons-thumb-masonry-big',
								'trx_addons-thumb-masonry',
								'trx_addons-thumb-avatar',
								'trx_addons-thumb-big-vertical'
								)
							)
						) unset($list[$k]);
			}
		}
		return $list;
	}
}

// and replace removed styles with theme-specific thumb size
if ( !function_exists( 'palladio_customizer_trx_addons_get_thumb_size' ) ) {
	add_filter( 'trx_addons_filter_get_thumb_size', 'palladio_customizer_trx_addons_get_thumb_size');
	function palladio_customizer_trx_addons_get_thumb_size($thumb_size='') {
		return str_replace(array(
							'trx_addons-thumb-huge',
							'trx_addons-thumb-huge-@retina',
							'trx_addons-thumb-big',
							'trx_addons-thumb-big-@retina',
							'trx_addons-thumb-medium',
							'trx_addons-thumb-medium-@retina',
							'trx_addons-thumb-tiny',
							'trx_addons-thumb-tiny-@retina',
							'trx_addons-thumb-masonry-big',
							'trx_addons-thumb-masonry-big-@retina',
							'trx_addons-thumb-masonry',
							'trx_addons-thumb-masonry-@retina',
							'trx_addons-thumb-avatar',
							'trx_addons-thumb-avatar-@retina',
							),
							array(
							'palladio-thumb-huge',
							'palladio-thumb-huge-@retina',
							'palladio-thumb-big',
							'palladio-thumb-big-@retina',
							'palladio-thumb-med',
							'palladio-thumb-med-@retina',
							'palladio-thumb-tiny',
							'palladio-thumb-tiny-@retina',
							'palladio-thumb-masonry-big',
							'palladio-thumb-masonry-big-@retina',
							'palladio-thumb-masonry',
							'palladio-thumb-masonry-@retina',
							'palladio-thumb-avatar',
							'palladio-thumb-avatar-@retina',
							),
							$thumb_size);
	}
}




//------------------------------------------------------------------------
// One-click import support
//------------------------------------------------------------------------

// Set theme specific importer options
if ( !function_exists( 'palladio_importer_set_options' ) ) {
	add_filter( 'trx_addons_filter_importer_options', 'palladio_importer_set_options', 9 );
	function palladio_importer_set_options($options=array()) {
		if (is_array($options)) {
			// Save or not installer's messages to the log-file
			$options['debug'] = false;
			// Prepare demo data
			$options['demo_url'] = esc_url(palladio_get_protocol() . '://demofiles.ancorathemes.com/palladio');
			// Required plugins
			$options['required_plugins'] = array_keys(palladio_storage_get('required_plugins'));
			// Default demo
			$options['files']['default']['title'] = esc_html__('Palladio Demo', 'palladio');
			$options['files']['default']['domain_dev']  = palladio_add_protocol( '//palladio.ancorathemes.com' );                // Developers domain
			$options['files']['default']['domain_demo'] = palladio_add_protocol( palladio_storage_get( 'theme_demo_url' ) );   // Demo-site domain
			// If theme need more demo - just copy 'default' and change required parameter
		}
		return $options;
	}
}




// -----------------------------------------------------------------
// -- Theme options for customizer
// -----------------------------------------------------------------
if (!function_exists('palladio_create_theme_options')) {

	function palladio_create_theme_options() {

		// Message about options override. 
		// Attention! Not need esc_html() here, because this message put in wp_kses_data() below
		$msg_override = __('<b>Attention!</b> Some of these options can be overridden in the following sections (Blog, Plugins settings, etc.) or in the settings of individual pages', 'palladio');

		palladio_storage_set('options', array(
		
			// 'Logo & Site Identity'
			'title_tagline' => array(
				"title" => esc_html__('Logo & Site Identity', 'palladio'),
				"desc" => '',
				"priority" => 10,
				"type" => "section"
				),
			'logo_text' => array(
				"title" => esc_html__('Logo from Site name', 'palladio'),
				"desc" => wp_kses_data( __('Do you want use Site name and description as Logo if images below are not specified?', 'palladio') ),
				"std" => 1,
				"type" => PALLADIO_THEME_FREE ? "hidden" : "checkbox"
				),
			'logo' => array(
				"title" => esc_html__('Logo', 'palladio'),
				"desc" => wp_kses_data( __('Select or upload site logo', 'palladio') ),
				"class" => "palladio_column-1_2 palladio_new_row",
				"std" => '',
				"type" => "image"
				),
			'logo_retina' => array(
				"title" => esc_html__('Logo for Retina', 'palladio'),
				"desc" => wp_kses_data( __('Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'palladio') ),
				"class" => "palladio_column-1_2",
				"std" => '',
				"type" => PALLADIO_THEME_FREE ? "hidden" : "image"
				),
			'logo_mobile' => array(
				"title" => esc_html__('Logo mobile', 'palladio'),
				"desc" => wp_kses_data( __('Select or upload site logo to display it in the mobile menu', 'palladio') ),
				"class" => "palladio_column-1_2 palladio_new_row",
				"std" => '',
				"type" => "image"
				),
			'logo_mobile_retina' => array(
				"title" => esc_html__('Logo mobile for Retina', 'palladio'),
				"desc" => wp_kses_data( __('Select or upload site logo used on Retina displays (if empty - use default logo from the field above)', 'palladio') ),
				"class" => "palladio_column-1_2",
				"std" => '',
				"type" => PALLADIO_THEME_FREE ? "hidden" : "image"
				),
			'logo_side' => array(
				"title" => esc_html__('Logo side', 'palladio'),
				"desc" => wp_kses_data( __('Select or upload site logo (with vertical orientation) to display it in the side menu', 'palladio') ),
				"class" => "palladio_column-1_2 palladio_new_row",
				"std" => '',
				"type" => "image"
				),
			'logo_side_retina' => array(
				"title" => esc_html__('Logo side for Retina', 'palladio'),
				"desc" => wp_kses_data( __('Select or upload site logo (with vertical orientation) to display it in the side menu on Retina displays (if empty - use default logo from the field above)', 'palladio') ),
				"class" => "palladio_column-1_2",
				"std" => '',
				"type" => PALLADIO_THEME_FREE ? "hidden" : "image"
				),
			
		
		
			// 'General settings'
			'general' => array(
				"title" => esc_html__('General Settings', 'palladio'),
				"desc" => wp_kses_data( __('Settings for the entire site', 'palladio') )
							. '<br>'
							. wp_kses_data( $msg_override ),
				"priority" => 20,
				"type" => "section",
				),

			'general_layout_info' => array(
				"title" => esc_html__('Layout', 'palladio'),
				"desc" => '',
				"type" => "info",
				),
			'body_style' => array(
				"title" => esc_html__('Body style', 'palladio'),
				"desc" => wp_kses_data( __('Select width of the body content', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Content', 'palladio')
				),
				"refresh" => false,
				"std" => 'boxed',
				"options" => array(
					'boxed'		=> esc_html__('Boxed',		'palladio'),
					'wide'		=> esc_html__('Wide',		'palladio'),
					'fullwide'	=> esc_html__('Fullwide',	'palladio'),
					'fullscreen'=> esc_html__('Fullscreen',	'palladio')
				),
				"type" => "select"
				),
			'boxed_bg_image' => array(
				"title" => esc_html__('Boxed bg image', 'palladio'),
				"desc" => wp_kses_data( __('Select or upload image, used as background in the boxed body', 'palladio') ),
				"dependency" => array(
					'body_style' => array('boxed')
				),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Content', 'palladio')
				),
				"std" => '',
				"hidden" => true,
				"type" => "image"
				),
			'remove_margins' => array(
				"title" => esc_html__('Remove margins', 'palladio'),
				"desc" => wp_kses_data( __('Remove margins above and below the content area', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Content', 'palladio')
				),
				"refresh" => false,
				"std" => 0,
				"type" => "checkbox"
				),

			'general_sidebar_info' => array(
				"title" => esc_html__('Sidebar', 'palladio'),
				"desc" => '',
				"type" => "info",
				),
			'sidebar_position' => array(
				"title" => esc_html__('Sidebar position', 'palladio'),
				"desc" => wp_kses_data( __('Select position to show sidebar', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Widgets', 'palladio')
				),
				"std" => 'right',
				"options" => array(),
				"type" => "switch"
				),
			'sidebar_widgets' => array(
				"title" => esc_html__('Sidebar widgets', 'palladio'),
				"desc" => wp_kses_data( __('Select default widgets to show in the sidebar', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Widgets', 'palladio')
				),
				"dependency" => array(
					'sidebar_position' => array('left', 'right')
				),
				"std" => 'sidebar_widgets',
				"options" => array(),
				"type" => "select"
				),
			'expand_content' => array(
				"title" => esc_html__('Expand content', 'palladio'),
				"desc" => wp_kses_data( __('Expand the content width if the sidebar is hidden', 'palladio') ),
				"refresh" => false,
				"std" => 1,
				"type" => "checkbox"
				),


			'general_widgets_info' => array(
				"title" => esc_html__('Additional widgets', 'palladio'),
				"desc" => '',
				"type" => PALLADIO_THEME_FREE ? "hidden" : "info",
				),
			'widgets_above_page' => array(
				"title" => esc_html__('Widgets at the top of the page', 'palladio'),
				"desc" => wp_kses_data( __('Select widgets to show at the top of the page (above content and sidebar)', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Widgets', 'palladio')
				),
				"std" => 'hide',
				"options" => array(),
				"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
				),
			'widgets_above_content' => array(
				"title" => esc_html__('Widgets above the content', 'palladio'),
				"desc" => wp_kses_data( __('Select widgets to show at the beginning of the content area', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Widgets', 'palladio')
				),
				"std" => 'hide',
				"options" => array(),
				"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
				),
			'widgets_below_content' => array(
				"title" => esc_html__('Widgets below the content', 'palladio'),
				"desc" => wp_kses_data( __('Select widgets to show at the ending of the content area', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Widgets', 'palladio')
				),
				"std" => 'hide',
				"options" => array(),
				"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
				),
			'widgets_below_page' => array(
				"title" => esc_html__('Widgets at the bottom of the page', 'palladio'),
				"desc" => wp_kses_data( __('Select widgets to show at the bottom of the page (below content and sidebar)', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Widgets', 'palladio')
				),
				"std" => 'hide',
				"options" => array(),
				"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
				),

			'general_effects_info' => array(
				"title" => esc_html__('Design & Effects', 'palladio'),
				"desc" => '',
				"type" => "info",
				),
			'border_radius' => array(
				"title" => esc_html__('Border radius', 'palladio'),
				"desc" => wp_kses_data( __('Specify the border radius of the form fields and buttons in pixels or other valid CSS units', 'palladio') ),
				"std" => 0,
				"type" => "hidden"//"text"
				),

			'general_misc_info' => array(
				"title" => esc_html__('Miscellaneous', 'palladio'),
				"desc" => '',
				"type" => PALLADIO_THEME_FREE ? "hidden" : "info",
				),
			'seo_snippets' => array(
				"title" => esc_html__('SEO snippets', 'palladio'),
				"desc" => wp_kses_data( __('Add structured data markup to the single posts and pages', 'palladio') ),
				"std" => 0,
				"type" => PALLADIO_THEME_FREE ? "hidden" : "checkbox"
				),
            'privacy_text' => array(
                "title" => esc_html__("Text with Privacy Policy link", 'palladio'),
                "desc"  => wp_kses_data( __("Specify text with Privacy Policy link for the checkbox 'I agree ...'", 'palladio') ),
                "std"   => wp_kses( __( 'I agree that my submitted data is being collected and stored.', 'palladio'), 'palladio_kses_content' ),
                "type"  => "text"
            ),
		
		
			// 'Header'
			'header' => array(
				"title" => esc_html__('Header', 'palladio'),
				"desc" => wp_kses_data( $msg_override ),
				"priority" => 30,
				"type" => "section"
				),

			'header_style_info' => array(
				"title" => esc_html__('Header style', 'palladio'),
				"desc" => '',
				"type" => "info"
				),
			'header_style' => array(
				"title" => esc_html__('Header style', 'palladio'),
				"desc" => wp_kses_data( __('Select style to display the site header', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Header', 'palladio')
				),
				"std" => 'header-default',
				"options" => array(),
				"type" => "select"
				),
			'header_position' => array(
				"title" => esc_html__('Header position', 'palladio'),
				"desc" => wp_kses_data( __('Select position to display the site header', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Header', 'palladio')
				),
				"std" => 'default',
				"options" => array(),
				"type" => PALLADIO_THEME_FREE ? "hidden" : "switch"
				),
			'header_fullheight' => array(
				"title" => esc_html__('Header fullheight', 'palladio'),
				"desc" => wp_kses_data( __("Enlarge header area to fill whole screen. Used only if header have a background image", 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Header', 'palladio')
				),
				"std" => 0,
				"type" => "hidden"//PALLADIO_THEME_FREE ? "hidden" : "checkbox"
				),
			'header_wide' => array(
				"title" => esc_html__('Header fullwide', 'palladio'),
				"desc" => wp_kses_data( __('Do you want to stretch the header widgets area to the entire window width?', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Header', 'palladio')
				),
				"dependency" => array(
					'header_style' => array('header-default')
				),
				"std" => 1,
				"type" => PALLADIO_THEME_FREE ? "hidden" : "checkbox"
				),

			'header_widgets_info' => array(
				"title" => esc_html__('Header widgets', 'palladio'),
				"desc" => wp_kses_data( __('Here you can place a widget slider, advertising banners, etc.', 'palladio') ),
				"type" => "info"
				),
			'header_widgets' => array(
				"title" => esc_html__('Header widgets', 'palladio'),
				"desc" => wp_kses_data( __('Select set of widgets to show in the header on each page', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Header', 'palladio'),
					"desc" => wp_kses_data( __('Select set of widgets to show in the header on this page', 'palladio') ),
				),
				"std" => 'hide',
				"options" => array(),
				"type" => "select"
				),
			'header_columns' => array(
				"title" => esc_html__('Header columns', 'palladio'),
				"desc" => wp_kses_data( __('Select number columns to show widgets in the Header. If 0 - autodetect by the widgets count', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Header', 'palladio')
				),
				"dependency" => array(
					'header_style' => array('header-default'),
					'header_widgets' => array('^hide')
				),
				"std" => 0,
				"options" => palladio_get_list_range(0,6),
				"type" => "select"
				),

			'menu_info' => array(
				"title" => esc_html__('Main menu', 'palladio'),
				"desc" => wp_kses_data( __('Select main menu style, position, color scheme and other parameters', 'palladio') ),
				"type" => PALLADIO_THEME_FREE ? "hidden" : "info"
				),
			'menu_style' => array(
				"title" => esc_html__('Menu position', 'palladio'),
				"desc" => wp_kses_data( __('Select position of the main menu', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Header', 'palladio')
				),
				"std" => 'top',
				"options" => array(
					'top'	=> esc_html__('Top',	'palladio'),
					'left'	=> esc_html__('Left',	'palladio'),
					'right'	=> esc_html__('Right',	'palladio')
				),
				"type" => "hidden"//PALLADIO_THEME_FREE ? "hidden" : "switch"
				),
			'menu_side_stretch' => array(
				"title" => esc_html__('Stretch sidemenu', 'palladio'),
				"desc" => wp_kses_data( __('Stretch sidemenu to window height (if menu items number >= 5)', 'palladio') ),
				"dependency" => array(
					'menu_style' => array('left', 'right')
				),
				"std" => 1,
				"type" => "hidden"//PALLADIO_THEME_FREE ? "hidden" : "checkbox"
				),
			'menu_side_icons' => array(
				"title" => esc_html__('Anchor sidemenu', 'palladio'),
				"desc" => wp_kses_data( __('Show the sidebar with links to the anchor of the page elements. Works only on boxed pages.', 'palladio') ),
				"std" => 1,
				"type" => PALLADIO_THEME_FREE ? "hidden" : "checkbox"
				),
			'menu_mobile_fullscreen' => array(
				"title" => esc_html__('Mobile menu fullscreen', 'palladio'),
				"desc" => wp_kses_data( __('Display mobile and side menus on full screen (if checked) or slide narrow menu from the left or from the right side (if not checked)', 'palladio') ),
				"dependency" => array(
					'menu_style' => array('left', 'right')
				),
				"std" => 1,
				"type" => "hidden"//PALLADIO_THEME_FREE ? "hidden" : "checkbox"
				),

			'header_image_info' => array(
				"title" => esc_html__('Header image', 'palladio'),
				"desc" => '',
				"type" => PALLADIO_THEME_FREE ? "hidden" : "info"
				),
			'header_image_override' => array(
				"title" => esc_html__('Header image override', 'palladio'),
				"desc" => wp_kses_data( __("Allow override the header image with the page's/post's/product's/etc. featured image", 'palladio') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'palladio')
				),
				"std" => 0,
				"type" => PALLADIO_THEME_FREE ? "hidden" : "checkbox"
				),


		
			// 'Footer'
			'footer' => array(
				"title" => esc_html__('Footer', 'palladio'),
				"desc" => wp_kses_data( __('Select set of widgets and columns number in the site footer', 'palladio') )
							. '<br>'
							. wp_kses_data( $msg_override ),
				"priority" => 50,
				"type" => "section"
				),
			'footer_style' => array(
				"title" => esc_html__('Footer style', 'palladio'),
				"desc" => wp_kses_data( __('Select style to display the site footer', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Footer', 'palladio')
				),
				"std" => 'footer-custom-footer-standard',
				"options" => array(),
				"type" => "select"
				),
			'footer_widgets' => array(
				"title" => esc_html__('Footer widgets', 'palladio'),
				"desc" => wp_kses_data( __('Select set of widgets to show in the footer', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Footer', 'palladio')
				),
				"dependency" => array(
					'footer_style' => array('footer-default')
				),
				"std" => 'footer_widgets',
				"options" => array(),
				"type" => "select"
				),
			'footer_columns' => array(
				"title" => esc_html__('Footer columns', 'palladio'),
				"desc" => wp_kses_data( __('Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Footer', 'palladio')
				),
				"dependency" => array(
					'footer_style' => array('footer-default'),
					'footer_widgets' => array('^hide')
				),
				"std" => 0,
				"options" => palladio_get_list_range(0,6),
				"type" => "select"
				),
			'footer_wide' => array(
				"title" => esc_html__('Footer fullwide', 'palladio'),
				"desc" => wp_kses_data( __('Do you want to stretch the footer to the entire window width?', 'palladio') ),
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Footer', 'palladio')
				),
				"dependency" => array(
					'footer_style' => array('footer-default')
				),
				"std" => 0,
				"type" => "checkbox"
				),
			'logo_in_footer' => array(
				"title" => esc_html__('Show logo', 'palladio'),
				"desc" => wp_kses_data( __('Show logo in the footer', 'palladio') ),
				'refresh' => false,
				"dependency" => array(
					'footer_style' => array('footer-default')
				),
				"std" => 0,
				"type" => "checkbox"
				),
			'logo_footer' => array(
				"title" => esc_html__('Logo for footer', 'palladio'),
				"desc" => wp_kses_data( __('Select or upload site logo to display it in the footer', 'palladio') ),
				"dependency" => array(
					'footer_style' => array('footer-default'),
					'logo_in_footer' => array('1')
				),
				"std" => '',
				"type" => "image"
				),
			'logo_footer_retina' => array(
				"title" => esc_html__('Logo for footer (Retina)', 'palladio'),
				"desc" => wp_kses_data( __('Select or upload logo for the footer area used on Retina displays (if empty - use default logo from the field above)', 'palladio') ),
				"dependency" => array(
					'footer_style' => array('footer-default'),
					'logo_in_footer' => array('1')
				),
				"std" => '',
				"type" => PALLADIO_THEME_FREE ? "hidden" : "image"
				),
			'socials_in_footer' => array(
				"title" => esc_html__('Show social icons', 'palladio'),
				"desc" => wp_kses_data( __('Show social icons in the footer (under logo or footer widgets)', 'palladio') ),
				"dependency" => array(
					'footer_style' => array('footer-default')
				),
				"std" => 0,
				"type" => "checkbox"
				),
			'copyright' => array(
				"title" => esc_html__('Copyright', 'palladio'),
				"desc" => wp_kses_data( __('Copyright text in the footer. Use {Y} to insert current year and press "Enter" to create a new line', 'palladio') ),
				"std" => esc_html__('AncoraThemes &copy; {Y}. All rights reserved.', 'palladio'),
				"dependency" => array(
					'footer_style' => array('footer-default')
				),
				"refresh" => false,
				"type" => "textarea"
				),
			
		
		
			// 'Blog'
			'blog' => array(
				"title" => esc_html__('Blog', 'palladio'),
				"desc" => wp_kses_data( __('Options of the the blog archive', 'palladio') ),
				"priority" => 70,
				"type" => "panel",
				),
		
				// Blog - Posts page
				'blog_general' => array(
					"title" => esc_html__('Posts page', 'palladio'),
					"desc" => wp_kses_data( __('Style and components of the blog archive', 'palladio') ),
					"type" => "section",
					),
				'blog_general_info' => array(
					"title" => esc_html__('General settings', 'palladio'),
					"desc" => '',
					"type" => "info",
					),
				'blog_style' => array(
					"title" => esc_html__('Blog style', 'palladio'),
					"desc" => '',
					"override" => array(
						'mode' => 'page',
						'section' => esc_html__('Content', 'palladio')
					),
					"dependency" => array(
						'#page_template' => array('blog.php'),
                        '.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					"std" => 'excerpt',
					"options" => array(),
					"type" => "select"
					),
				'first_post_large' => array(
					"title" => esc_html__('First post large', 'palladio'),
					"desc" => wp_kses_data( __('Make your first post stand out by making it bigger', 'palladio') ),
					"override" => array(
						'mode' => 'page',
						'section' => esc_html__('Content', 'palladio')
					),
					"dependency" => array(
						'#page_template' => array('blog.php'),
                        '.editor-page-attributes__template select' => array( 'blog.php' ),
						'blog_style' => array('classic', 'masonry')
					),
					"std" => 0,
					"type" => "checkbox"
					),
				"blog_content" => array( 
					"title" => esc_html__('Posts content', 'palladio'),
					"desc" => wp_kses_data( __("Display either post excerpts or the full post content", 'palladio') ),
					"std" => "excerpt",
					"dependency" => array(
						'blog_style' => array('excerpt')
					),
					"options" => array(
						'excerpt'	=> esc_html__('Excerpt',	'palladio'),
						'fullpost'	=> esc_html__('Full post',	'palladio')
					),
					"type" => "switch"
					),
				'excerpt_length' => array(
					"title" => esc_html__('Excerpt length', 'palladio'),
					"desc" => wp_kses_data( __("Length (in words) to generate excerpt from the post content. Attention! If the post excerpt is explicitly specified - it appears unchanged", 'palladio') ),
					"dependency" => array(
						'blog_style' => array('excerpt'),
						'blog_content' => array('excerpt')
					),
					"std" => 60,
					"type" => "text"
					),
				'blog_columns' => array(
					"title" => esc_html__('Blog columns', 'palladio'),
					"desc" => wp_kses_data( __('How many columns should be used in the blog archive (from 2 to 4)?', 'palladio') ),
					"std" => 2,
					"options" => palladio_get_list_range(2,4),
					"type" => "hidden"
					),
				'post_type' => array(
					"title" => esc_html__('Post type', 'palladio'),
					"desc" => wp_kses_data( __('Select post type to show in the blog archive', 'palladio') ),
					"override" => array(
						'mode' => 'page',
						'section' => esc_html__('Content', 'palladio')
					),
					"dependency" => array(
						'#page_template' => array('blog.php'),
                        '.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					"linked" => 'parent_cat',
					"refresh" => false,
					"hidden" => true,
					"std" => 'post',
					"options" => array(),
					"type" => "select"
					),
				'parent_cat' => array(
					"title" => esc_html__('Category to show', 'palladio'),
					"desc" => wp_kses_data( __('Select category to show in the blog archive', 'palladio') ),
					"override" => array(
						'mode' => 'page',
						'section' => esc_html__('Content', 'palladio')
					),
					"dependency" => array(
						'#page_template' => array('blog.php'),
                        '.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					"refresh" => false,
					"hidden" => true,
					"std" => '0',
					"options" => array(),
					"type" => "select"
					),
				'posts_per_page' => array(
					"title" => esc_html__('Posts per page', 'palladio'),
					"desc" => wp_kses_data( __('How many posts will be displayed on this page', 'palladio') ),
					"override" => array(
						'mode' => 'page',
						'section' => esc_html__('Content', 'palladio')
					),
					"dependency" => array(
						'#page_template' => array('blog.php'),
                        '.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					"hidden" => true,
					"std" => '',
					"type" => "text"
					),
				"blog_pagination" => array( 
					"title" => esc_html__('Pagination style', 'palladio'),
					"desc" => wp_kses_data( __('Show Older/Newest posts or Page numbers below the posts list', 'palladio') ),
					"override" => array(
						'mode' => 'page',
						'section' => esc_html__('Content', 'palladio')
					),
					"std" => "pages",
					"options" => array(
						'pages'	=> esc_html__("Page numbers", 'palladio'),
						'links'	=> esc_html__("Older/Newest", 'palladio'),
						'more'	=> esc_html__("Load more", 'palladio'),
						'infinite' => esc_html__("Infinite scroll", 'palladio')
					),
					"type" => "select"
					),
				'show_filters' => array(
					"title" => esc_html__('Show filters', 'palladio'),
					"desc" => wp_kses_data( __('Show categories as tabs to filter posts', 'palladio') ),
					"override" => array(
						'mode' => 'page',
						'section' => esc_html__('Content', 'palladio')
					),
					"dependency" => array(
						'#page_template' => array('blog.php'),
                        '.editor-page-attributes__template select' => array( 'blog.php' ),
						'blog_style' => array('portfolio', 'gallery')
					),
					"hidden" => true,
					"std" => 0,
					"type" => PALLADIO_THEME_FREE ? "hidden" : "checkbox"
					),
	
				'blog_sidebar_info' => array(
					"title" => esc_html__('Sidebar', 'palladio'),
					"desc" => '',
					"type" => "info",
					),
				'sidebar_position_blog' => array(
					"title" => esc_html__('Sidebar position', 'palladio'),
					"desc" => wp_kses_data( __('Select position to show sidebar', 'palladio') ),
					"std" => 'right',
					"options" => array(),
					"type" => "switch"
					),
				'sidebar_widgets_blog' => array(
					"title" => esc_html__('Sidebar widgets', 'palladio'),
					"desc" => wp_kses_data( __('Select default widgets to show in the sidebar', 'palladio') ),
					"dependency" => array(
						'sidebar_position_blog' => array('left', 'right')
					),
					"std" => 'sidebar_widgets',
					"options" => array(),
					"type" => "select"
					),
				'expand_content_blog' => array(
					"title" => esc_html__('Expand content', 'palladio'),
					"desc" => wp_kses_data( __('Expand the content width if the sidebar is hidden', 'palladio') ),
					"refresh" => false,
					"std" => 1,
					"type" => "checkbox"
					),
	
	
				'blog_widgets_info' => array(
					"title" => esc_html__('Additional widgets', 'palladio'),
					"desc" => '',
					"type" => PALLADIO_THEME_FREE ? "hidden" : "info",
					),
				'widgets_above_page_blog' => array(
					"title" => esc_html__('Widgets at the top of the page', 'palladio'),
					"desc" => wp_kses_data( __('Select widgets to show at the top of the page (above content and sidebar)', 'palladio') ),
					"std" => 'hide',
					"options" => array(),
					"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
					),
				'widgets_above_content_blog' => array(
					"title" => esc_html__('Widgets above the content', 'palladio'),
					"desc" => wp_kses_data( __('Select widgets to show at the beginning of the content area', 'palladio') ),
					"std" => 'hide',
					"options" => array(),
					"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
					),
				'widgets_below_content_blog' => array(
					"title" => esc_html__('Widgets below the content', 'palladio'),
					"desc" => wp_kses_data( __('Select widgets to show at the ending of the content area', 'palladio') ),
					"std" => 'hide',
					"options" => array(),
					"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
					),
				'widgets_below_page_blog' => array(
					"title" => esc_html__('Widgets at the bottom of the page', 'palladio'),
					"desc" => wp_kses_data( __('Select widgets to show at the bottom of the page (below content and sidebar)', 'palladio') ),
					"std" => 'hide',
					"options" => array(),
					"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
					),

				'blog_advanced_info' => array(
					"title" => esc_html__('Advanced settings', 'palladio'),
					"desc" => '',
					"type" => "info",
					),
				'no_image' => array(
					"title" => esc_html__('Image placeholder', 'palladio'),
					"desc" => wp_kses_data( __('Select or upload an image used as placeholder for posts without a featured image', 'palladio') ),
					"std" => '',
					"type" => "image"
					),
				'time_diff_before' => array(
					"title" => esc_html__('Easy Readable Date Format', 'palladio'),
					"desc" => wp_kses_data( __("For how many days to show the easy-readable date format (e.g. '3 days ago') instead of the standard publication date", 'palladio') ),
					"std" => 5,
					"type" => "text"
					),
				'sticky_style' => array(
					"title" => esc_html__('Sticky posts style', 'palladio'),
					"desc" => wp_kses_data( __('Select style of the sticky posts output', 'palladio') ),
					"std" => 'columns',
					"options" => array(
						'inherit' => esc_html__('Decorated posts', 'palladio'),
						'columns' => esc_html__('Mini-cards',	'palladio')
					),
					"type" => "hidden"//PALLADIO_THEME_FREE ? "hidden" : "select"
					),
				"blog_animation" => array( 
					"title" => esc_html__('Animation for the posts', 'palladio'),
					"desc" => wp_kses_data( __('Select animation to show posts in the blog. Attention! Do not use any animation on pages with the "wheel to the anchor" behaviour (like a "Chess 2 columns")!', 'palladio') ),
					"override" => array(
						'mode' => 'page',
						'section' => esc_html__('Content', 'palladio')
					),
					"dependency" => array(
						'#page_template' => array('blog.php'),
                        '.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					"std" => "none",
					"options" => array(),
					"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
					),
				'meta_parts' => array(
					"title" => esc_html__('Post meta', 'palladio'),
					"desc" => wp_kses_data( __("If your blog page is created using the 'Blog archive' page template, set up the 'Post Meta' settings in the 'Theme Options' section of that page.", 'palladio') )
								. '<br>'
								. wp_kses_data( __("<b>Tip:</b> Drag items to change their order.", 'palladio') ),
					"override" => array(
						'mode' => 'page',
						'section' => esc_html__('Content', 'palladio')
					),
					"dependency" => array(
						'#page_template' => array('blog.php'),
                        '.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => 'categories=1|author=1|date=1|counters=1|share=0|edit=0',
					"options" => array(
						'categories' => esc_html__('Categories', 'palladio'),
						'date'		 => esc_html__('Post date', 'palladio'),
						'author'	 => esc_html__('Post author', 'palladio'),
						'counters'	 => esc_html__('Views, Likes and Comments', 'palladio'),
						'share'		 => esc_html__('Share links', 'palladio'),
						'edit'		 => esc_html__('Edit link', 'palladio')
					),
					"type" => PALLADIO_THEME_FREE ? "hidden" : "checklist"
				),
				'counters' => array(
					"title" => esc_html__('Views, Likes and Comments', 'palladio'),
					"desc" => wp_kses_data( __("Likes and Views are available only if ThemeREX Addons is active", 'palladio') ),
					"override" => array(
						'mode' => 'page',
						'section' => esc_html__('Content', 'palladio')
					),
					"dependency" => array(
						'#page_template' => array('blog.php'),
                        '.editor-page-attributes__template select' => array( 'blog.php' ),
					),
					"dir" => 'vertical',
					"sortable" => true,
					"std" => 'comments=1|views=0|likes=0',
					"options" => array(
						'views' => esc_html__('Views', 'palladio'),
						'likes' => esc_html__('Likes', 'palladio'),
						'comments' => esc_html__('Comments', 'palladio')
					),
					"type" => PALLADIO_THEME_FREE ? "hidden" : "checklist"
				),

				
				// Blog - Single posts
				'blog_single' => array(
					"title" => esc_html__('Single posts', 'palladio'),
					"desc" => wp_kses_data( __('Settings of the single post', 'palladio') ),
					"type" => "section",
					),
				'hide_featured_on_single' => array(
					"title" => esc_html__('Hide featured image on the single post', 'palladio'),
					"desc" => wp_kses_data( __("Hide featured image on the single post's pages", 'palladio') ),
					"override" => array(
						'mode' => 'page,post',
						'section' => esc_html__('Content', 'palladio')
					),
					"std" => 0,
					"type" => "checkbox"
					),
				'hide_sidebar_on_single' => array(
					"title" => esc_html__('Hide sidebar on the single post', 'palladio'),
					"desc" => wp_kses_data( __("Hide sidebar on the single post's pages", 'palladio') ),
					"std" => 0,
					"type" => "checkbox"
					),
				'show_post_meta' => array(
					"title" => esc_html__('Show post meta', 'palladio'),
					"desc" => wp_kses_data( __("Display block with post's meta: date, categories, counters, etc.", 'palladio') ),
					"std" => 1,
					"type" => "checkbox"
					),
				'show_share_links' => array(
					"title" => esc_html__('Show share links', 'palladio'),
					"desc" => wp_kses_data( __("Display share links on the single post", 'palladio') ),
					"std" => 1,
					"type" => "checkbox"
					),
				'show_author_info' => array(
					"title" => esc_html__('Show author info', 'palladio'),
					"desc" => wp_kses_data( __("Display block with information about post's author", 'palladio') ),
					"std" => 1,
					"type" => "checkbox"
					),
				'show_related_posts' => array(
					"title" => esc_html__('Show related posts', 'palladio'),
					"desc" => wp_kses_data( __("Show section 'Related posts' on the single post's pages", 'palladio') ),
					"override" => array(
						'mode' => 'page,post',
						'section' => esc_html__('Content', 'palladio')
					),
					"std" => 0,
					"type" => "checkbox"
					),
				'related_posts' => array(
					"title" => esc_html__('Related posts', 'palladio'),
					"desc" => wp_kses_data( __('How many related posts should be displayed in the single post? If 0 - no related posts showed.', 'palladio') ),
					"dependency" => array(
						'show_related_posts' => array('1')
					),
					"std" => 2,
					"options" => palladio_get_list_range(1,9),
					"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
					),
				'related_columns' => array(
					"title" => esc_html__('Related columns', 'palladio'),
					"desc" => wp_kses_data( __('How many columns should be used to output related posts in the single page (from 2 to 4)?', 'palladio') ),
					"dependency" => array(
						'show_related_posts' => array('1')
					),
					"std" => 2,
					"options" => palladio_get_list_range(1,4),
					"type" => PALLADIO_THEME_FREE ? "hidden" : "switch"
					),
				'related_style' => array(
					"title" => esc_html__('Related posts style', 'palladio'),
					"desc" => wp_kses_data( __('Select style of the related posts output', 'palladio') ),
					"dependency" => array(
						'show_related_posts' => array('1')
					),
					"std" => 2,
					"options" => palladio_get_list_styles(1,2),
					"type" => PALLADIO_THEME_FREE ? "hidden" : "switch"
					),
			'blog_end' => array(
				"type" => "panel_end",
				),
			
		
		
			// 'Colors'
			'panel_colors' => array(
				"title" => esc_html__('Colors', 'palladio'),
				"desc" => '',
				"priority" => 300,
				"type" => "section"
				),

			'color_schemes_info' => array(
				"title" => esc_html__('Color schemes', 'palladio'),
				"desc" => wp_kses_data( __('Color schemes for various parts of the site. "Inherit" means that this block is used the Site color scheme (the first parameter)', 'palladio') ),
				"type" => "info",
				),
			'color_scheme' => array(
				"title" => esc_html__('Site Color Scheme', 'palladio'),
				"desc" => '',
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Colors', 'palladio')
				),
				"std" => 'default',
				"options" => array(),
				"refresh" => false,
				"type" => "switch"
				),
			'sidebar_scheme' => array(
				"title" => esc_html__('Sidebar Color Scheme', 'palladio'),
				"desc" => '',
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Colors', 'palladio')
				),
				"std" => 'default',
				"options" => array(),
				"refresh" => false,
				"type" => "switch"
				),
			'header_scheme' => array(
				"title" => esc_html__('Header Color Scheme', 'palladio'),
				"desc" => '',
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Colors', 'palladio')
				),
				"std" => 'inherit',
				"options" => array(),
				"refresh" => false,
				"type" => "switch"
				),
			'menu_scheme' => array(
				"title" => esc_html__('Menu Color Scheme', 'palladio'),
				"desc" => '',
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Colors', 'palladio')
				),
				"std" => 'inherit',
				"options" => array(),
				"refresh" => false,
				"type" => PALLADIO_THEME_FREE ? "hidden" : "switch"
				),
			'footer_scheme' => array(
				"title" => esc_html__('Footer Color Scheme', 'palladio'),
				"desc" => '',
				"override" => array(
					'mode' => 'page,cpt_team,cpt_services,cpt_cars,cpt_properties,cpt_courses,cpt_portfolio',
					'section' => esc_html__('Colors', 'palladio')
				),
				"std" => 'inherit',
				"options" => array(),
				"refresh" => false,
				"type" => "switch"
				),

			'color_scheme_editor_info' => array(
				"title" => esc_html__('Color scheme editor', 'palladio'),
				"desc" => wp_kses_data(__('Select color scheme to modify. Attention! Only those sections in the site will be changed which this scheme was assigned to', 'palladio') ),
				"type" => "info",
				),
			'scheme_storage' => array(
				"title" => esc_html__('Color scheme editor', 'palladio'),
				"desc" => '',
				"std" => '$palladio_get_scheme_storage',
				"refresh" => false,
				"type" => "scheme_editor"
				),


			// 'Hidden'
			'media_title' => array(
				"title" => esc_html__('Media title', 'palladio'),
				"desc" => wp_kses_data( __('Used as title for the audio and video item in this post', 'palladio') ),
				"override" => array(
					'mode' => 'post',
					'section' => esc_html__('Content', 'palladio')
				),
				"hidden" => true,
				"std" => '',
				"type" => PALLADIO_THEME_FREE ? "hidden" : "text"
				),
			'media_author' => array(
				"title" => esc_html__('Media author', 'palladio'),
				"desc" => wp_kses_data( __('Used as author name for the audio and video item in this post', 'palladio') ),
				"override" => array(
					'mode' => 'post',
					'section' => esc_html__('Content', 'palladio')
				),
				"hidden" => true,
				"std" => '',
				"type" => PALLADIO_THEME_FREE ? "hidden" : "text"
				),


			// Internal options.
			// Attention! Don't change any options in the section below!
			'reset_options' => array(
				"title" => '',
				"desc" => '',
				"std" => '0',
				"type" => "hidden",
				),

		));


		// Prepare panel 'Fonts'
		$fonts = array(
		
			// 'Fonts'
			'fonts' => array(
				"title" => esc_html__('Typography', 'palladio'),
				"desc" => '',
				"priority" => 200,
				"type" => "panel"
				),

			// Fonts - Load_fonts
			'load_fonts' => array(
				"title" => esc_html__('Load fonts', 'palladio'),
				"desc" => wp_kses_data( __('Specify fonts to load when theme start. You can use them in the base theme elements: headers, text, menu, links, input fields, etc.', 'palladio') )
						. '<br>'
						. wp_kses_data( __('<b>Attention!</b> Press "Refresh" button to reload preview area after the all fonts are changed', 'palladio') ),
				"type" => "section"
				),
			'load_fonts_subset' => array(
				"title" => esc_html__('Google fonts subsets', 'palladio'),
				"desc" => wp_kses_data( __('Specify comma separated list of the subsets which will be load from Google fonts', 'palladio') )
						. '<br>'
						. wp_kses_data( __('Available subsets are: latin,latin-ext,cyrillic,cyrillic-ext,greek,greek-ext,vietnamese', 'palladio') ),
				"class" => "palladio_column-1_3 palladio_new_row",
				"refresh" => false,
				"std" => '$palladio_get_load_fonts_subset',
				"type" => "text"
				)
		);

		for ($i=1; $i<=palladio_get_theme_setting('max_load_fonts'); $i++) {
			if (palladio_get_value_gp('page') != 'theme_options') {
				$fonts["load_fonts-{$i}-info"] = array(
					"title" => esc_html(sprintf(__('Font %s', 'palladio'), $i)),
					"desc" => '',
					"type" => "info",
					);
			}
			$fonts["load_fonts-{$i}-name"] = array(
				"title" => esc_html__('Font name', 'palladio'),
				"desc" => '',
				"class" => "palladio_column-1_3 palladio_new_row",
				"refresh" => false,
				"std" => '$palladio_get_load_fonts_option',
				"type" => "text"
				);
			$fonts["load_fonts-{$i}-family"] = array(
				"title" => esc_html__('Font family', 'palladio'),
				"desc" => $i==1 
							? wp_kses_data( __('Select font family to use it if font above is not available', 'palladio') )
							: '',
				"class" => "palladio_column-1_3",
				"refresh" => false,
				"std" => '$palladio_get_load_fonts_option',
				"options" => array(
					'inherit' => esc_html__("Inherit", 'palladio'),
					'serif' => esc_html__('serif', 'palladio'),
					'sans-serif' => esc_html__('sans-serif', 'palladio'),
					'monospace' => esc_html__('monospace', 'palladio'),
					'cursive' => esc_html__('cursive', 'palladio'),
					'fantasy' => esc_html__('fantasy', 'palladio')
				),
				"type" => "select"
				);
			$fonts["load_fonts-{$i}-styles"] = array(
				"title" => esc_html__('Font styles', 'palladio'),
				"desc" => $i==1 
							? wp_kses_data( __('Font styles used only for the Google fonts. This is a comma separated list of the font weight and styles. For example: 400,400italic,700', 'palladio') )
											. '<br>'
								. wp_kses_data( __('<b>Attention!</b> Each weight and style increase download size! Specify only used weights and styles.', 'palladio') )
							: '',
				"class" => "palladio_column-1_3",
				"refresh" => false,
				"std" => '$palladio_get_load_fonts_option',
				"type" => "text"
				);
		}
		$fonts['load_fonts_end'] = array(
			"type" => "section_end"
			);

		// Fonts - H1..6, P, Info, Menu, etc.
		$theme_fonts = palladio_get_theme_fonts();
		foreach ($theme_fonts as $tag=>$v) {
			$fonts["{$tag}_section"] = array(
				"title" => !empty($v['title']) 
								? $v['title'] 
								: esc_html(sprintf(__('%s settings', 'palladio'), $tag)),
				"desc" => !empty($v['description']) 
								? $v['description'] 
								: wp_kses_post( sprintf(__('Font settings of the "%s" tag.', 'palladio'), $tag) ),
				"type" => "section",
				);
	
			foreach ($v as $css_prop=>$css_value) {
				if (in_array($css_prop, array('title', 'description'))) continue;
				$options = '';
				$type = 'text';
				$title = ucfirst(str_replace('-', ' ', $css_prop));
				if ($css_prop == 'font-family') {
					$type = 'select';
					$options = array();
				} else if ($css_prop == 'font-weight') {
					$type = 'select';
					$options = array(
						'inherit' => esc_html__("Inherit", 'palladio'),
						'100' => esc_html__('100 (Light)', 'palladio'), 
						'200' => esc_html__('200 (Light)', 'palladio'), 
						'300' => esc_html__('300 (Thin)',  'palladio'),
						'400' => esc_html__('400 (Normal)', 'palladio'),
						'500' => esc_html__('500 (Semibold)', 'palladio'),
						'600' => esc_html__('600 (Semibold)', 'palladio'),
						'700' => esc_html__('700 (Bold)', 'palladio'),
						'800' => esc_html__('800 (Black)', 'palladio'),
						'900' => esc_html__('900 (Black)', 'palladio')
					);
				} else if ($css_prop == 'font-style') {
					$type = 'select';
					$options = array(
						'inherit' => esc_html__("Inherit", 'palladio'),
						'normal' => esc_html__('Normal', 'palladio'), 
						'italic' => esc_html__('Italic', 'palladio')
					);
				} else if ($css_prop == 'text-decoration') {
					$type = 'select';
					$options = array(
						'inherit' => esc_html__("Inherit", 'palladio'),
						'none' => esc_html__('None', 'palladio'), 
						'underline' => esc_html__('Underline', 'palladio'),
						'overline' => esc_html__('Overline', 'palladio'),
						'line-through' => esc_html__('Line-through', 'palladio')
					);
				} else if ($css_prop == 'text-transform') {
					$type = 'select';
					$options = array(
						'inherit' => esc_html__("Inherit", 'palladio'),
						'none' => esc_html__('None', 'palladio'), 
						'uppercase' => esc_html__('Uppercase', 'palladio'),
						'lowercase' => esc_html__('Lowercase', 'palladio'),
						'capitalize' => esc_html__('Capitalize', 'palladio')
					);
				}
				$fonts["{$tag}_{$css_prop}"] = array(
					"title" => $title,
					"desc" => '',
					"class" => "palladio_column-1_5",
					"refresh" => false,
					"std" => '$palladio_get_theme_fonts_option',
					"options" => $options,
					"type" => $type
				);
			}
			
			$fonts["{$tag}_section_end"] = array(
				"type" => "section_end"
				);
		}

		$fonts['fonts_end'] = array(
			"type" => "panel_end"
			);

		// Add fonts parameters into Theme Options
		palladio_storage_merge_array('options', '', $fonts);

		// Add Header Video if WP version < 4.7
		if (!function_exists('get_header_video_url')) {
			palladio_storage_set_array_after('options', 'header_image_override', 'header_video', array(
				"title" => esc_html__('Header video', 'palladio'),
				"desc" => wp_kses_data( __("Select video to use it as background for the header", 'palladio') ),
				"override" => array(
					'mode' => 'page',
					'section' => esc_html__('Header', 'palladio')
				),
				"std" => '',
				"type" => "hidden"//"video"
				)
			);
		}
	}
}


// Returns a list of options that can be overridden for CPT
if (!function_exists('palladio_options_get_list_cpt_options')) {
	function palladio_options_get_list_cpt_options($cpt, $title='') {
		if (empty($title)) $title = ucfirst($cpt);
		return array(
					"header_info_{$cpt}" => array(
						"title" => esc_html__('Header', 'palladio'),
						"desc" => '',
						"type" => "info",
						),
					"header_style_{$cpt}" => array(
						"title" => esc_html__('Header style', 'palladio'),
						"desc" => wp_kses_data( sprintf(__('Select style to display the site header on the %s pages', 'palladio'), $title) ),
						"std" => 'inherit',
						"options" => array(),
						"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
						),
					"header_position_{$cpt}" => array(
						"title" => esc_html__('Header position', 'palladio'),
						"desc" => wp_kses_data( sprintf(__('Select position to display the site header on the %s pages', 'palladio'), $title) ),
						"std" => 'inherit',
						"options" => array(),
						"type" => PALLADIO_THEME_FREE ? "hidden" : "switch"
						),
					"header_widgets_{$cpt}" => array(
						"title" => esc_html__('Header widgets', 'palladio'),
						"desc" => wp_kses_data( sprintf(__('Select set of widgets to show in the header on the %s pages', 'palladio'), $title) ),
						"std" => 'hide',
						"options" => array(),
						"type" => "select"
						),
						
					"sidebar_info_{$cpt}" => array(
						"title" => esc_html__('Sidebar', 'palladio'),
						"desc" => '',
						"type" => "info",
						),
					"sidebar_position_{$cpt}" => array(
						"title" => esc_html__('Sidebar position', 'palladio'),
						"desc" => wp_kses_data( sprintf(__('Select position to show sidebar on the %s pages', 'palladio'), $title) ),
						"refresh" => false,
						"std" => 'left',
						"options" => array(),
						"type" => "switch"
						),
					"sidebar_widgets_{$cpt}" => array(
						"title" => esc_html__('Sidebar widgets', 'palladio'),
						"desc" => wp_kses_data( sprintf(__('Select sidebar to show on the %s pages', 'palladio'), $title) ),
						"dependency" => array(
							"sidebar_position_{$cpt}" => array('left', 'right')
						),
						"std" => 'hide',
						"options" => array(),
						"type" => "select"
						),
					"hide_sidebar_on_single_{$cpt}" => array(
						"title" => esc_html__('Hide sidebar on the single pages', 'palladio'),
						"desc" => wp_kses_data( __("Hide sidebar on the single page", 'palladio') ),
						"std" => 0,
						"type" => "checkbox"
						),
						
					"footer_info_{$cpt}" => array(
						"title" => esc_html__('Footer', 'palladio'),
						"desc" => '',
						"type" => "info",
						),
					"footer_scheme_{$cpt}" => array(
						"title" => esc_html__('Footer Color Scheme', 'palladio'),
						"desc" => wp_kses_data( __('Select color scheme to decorate footer area', 'palladio') ),
						"std" => 'default',
						"options" => array(),
						"type" => "switch"
						),
					"footer_widgets_{$cpt}" => array(
						"title" => esc_html__('Footer widgets', 'palladio'),
						"desc" => wp_kses_data( __('Select set of widgets to show in the footer', 'palladio') ),
						"std" => 'footer_widgets',
						"options" => array(),
						"type" => "select"
						),
					"footer_columns_{$cpt}" => array(
						"title" => esc_html__('Footer columns', 'palladio'),
						"desc" => wp_kses_data( __('Select number columns to show widgets in the footer. If 0 - autodetect by the widgets count', 'palladio') ),
						"dependency" => array(
							"footer_widgets_{$cpt}" => array('^hide')
						),
						"std" => 0,
						"options" => palladio_get_list_range(0,6),
						"type" => "select"
						),
					"footer_wide_{$cpt}" => array(
						"title" => esc_html__('Footer fullwide', 'palladio'),
						"desc" => wp_kses_data( __('Do you want to stretch the footer to the entire window width?', 'palladio') ),
						"std" => 0,
						"type" => "checkbox"
						),
						
					"widgets_info_{$cpt}" => array(
						"title" => esc_html__('Additional panels', 'palladio'),
						"desc" => '',
						"type" => "info",
						),
					"widgets_above_page_{$cpt}" => array(
						"title" => esc_html__('Widgets at the top of the page', 'palladio'),
						"desc" => wp_kses_data( __('Select widgets to show at the top of the page (above content and sidebar)', 'palladio') ),
						"std" => 'hide',
						"options" => array(),
						"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
						),
					"widgets_above_content_{$cpt}" => array(
						"title" => esc_html__('Widgets above the content', 'palladio'),
						"desc" => wp_kses_data( __('Select widgets to show at the beginning of the content area', 'palladio') ),
						"std" => 'hide',
						"options" => array(),
						"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
						),
					"widgets_below_content_{$cpt}" => array(
						"title" => esc_html__('Widgets below the content', 'palladio'),
						"desc" => wp_kses_data( __('Select widgets to show at the ending of the content area', 'palladio') ),
						"std" => 'hide',
						"options" => array(),
						"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
						),
					"widgets_below_page_{$cpt}" => array(
						"title" => esc_html__('Widgets at the bottom of the page', 'palladio'),
						"desc" => wp_kses_data( __('Select widgets to show at the bottom of the page (below content and sidebar)', 'palladio') ),
						"std" => 'hide',
						"options" => array(),
						"type" => PALLADIO_THEME_FREE ? "hidden" : "select"
						)
					);
	}
}


// Return lists with choises when its need in the admin mode
if (!function_exists('palladio_options_get_list_choises')) {
	add_filter('palladio_filter_options_get_list_choises', 'palladio_options_get_list_choises', 10, 2);
	function palladio_options_get_list_choises($list, $id) {
		if (is_array($list) && count($list)==0) {
			if (strpos($id, 'header_style')===0)
				$list = palladio_get_list_header_styles(strpos($id, 'header_style_')===0);
			else if (strpos($id, 'header_position')===0)
				$list = palladio_get_list_header_positions(strpos($id, 'header_position_')===0);
			else if (strpos($id, 'header_widgets')===0)
				$list = palladio_get_list_sidebars(strpos($id, 'header_widgets_')===0, true);
			else if (strpos($id, 'header_scheme')===0 
					|| strpos($id, 'menu_scheme')===0
					|| strpos($id, 'color_scheme')===0
					|| strpos($id, 'sidebar_scheme')===0
					|| strpos($id, 'footer_scheme')===0)
				$list = palladio_get_list_schemes($id!='color_scheme');
			else if (strpos($id, 'sidebar_widgets')===0)
				$list = palladio_get_list_sidebars(strpos($id, 'sidebar_widgets_')===0, true);
			else if (strpos($id, 'sidebar_position')===0)
				$list = palladio_get_list_sidebars_positions(strpos($id, 'sidebar_position_')===0);
			else if (strpos($id, 'widgets_above_page')===0)
				$list = palladio_get_list_sidebars(strpos($id, 'widgets_above_page_')===0, true);
			else if (strpos($id, 'widgets_above_content')===0)
				$list = palladio_get_list_sidebars(strpos($id, 'widgets_above_content_')===0, true);
			else if (strpos($id, 'widgets_below_page')===0)
				$list = palladio_get_list_sidebars(strpos($id, 'widgets_below_page_')===0, true);
			else if (strpos($id, 'widgets_below_content')===0)
				$list = palladio_get_list_sidebars(strpos($id, 'widgets_below_content_')===0, true);
			else if (strpos($id, 'footer_style')===0)
				$list = palladio_get_list_footer_styles(strpos($id, 'footer_style_')===0);
			else if (strpos($id, 'footer_widgets')===0)
				$list = palladio_get_list_sidebars(strpos($id, 'footer_widgets_')===0, true);
			else if (strpos($id, 'blog_style')===0)
				$list = palladio_get_list_blog_styles(strpos($id, 'blog_style_')===0);
			else if (strpos($id, 'post_type')===0)
				$list = palladio_get_list_posts_types();
			else if (strpos($id, 'parent_cat')===0)
				$list = palladio_array_merge(array(0 => esc_html__('- Select category -', 'palladio')), palladio_get_list_categories());
			else if (strpos($id, 'blog_animation')===0)
				$list = palladio_get_list_animations_in();
			else if ($id == 'color_scheme_editor')
				$list = palladio_get_list_schemes();
			else if (strpos($id, '_font-family') > 0)
				$list = palladio_get_list_load_fonts(true);
		}
		return $list;
	}
}
?>