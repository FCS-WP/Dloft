<?php
/**
 * Theme Options, Color Schemes and Fonts utilities
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

// -----------------------------------------------------------------
// -- Create and manage Theme Options
// -----------------------------------------------------------------

// Theme init priorities:
// 2 - create Theme Options
if (!function_exists('palladio_options_theme_setup2')) {
	add_action( 'after_setup_theme', 'palladio_options_theme_setup2', 2 );
	function palladio_options_theme_setup2() {
		palladio_create_theme_options();
	}
}

// Step 1: Load default settings and previously saved mods
if (!function_exists('palladio_options_theme_setup5')) {
	add_action( 'after_setup_theme', 'palladio_options_theme_setup5', 5 );
	function palladio_options_theme_setup5() {
		palladio_storage_set('options_reloaded', false);
		palladio_load_theme_options();
	}
}

// Step 2: Load current theme customization mods
if (is_customize_preview()) {
	if (!function_exists('palladio_load_custom_options')) {
		add_action( 'wp_loaded', 'palladio_load_custom_options' );
		function palladio_load_custom_options() {
			if (!palladio_storage_get('options_reloaded')) {
				palladio_storage_set('options_reloaded', true);
				palladio_load_theme_options();
			}
		}
	}
}

// Load current values for each customizable option
if ( !function_exists('palladio_load_theme_options') ) {
	function palladio_load_theme_options() {
		$options = palladio_storage_get('options');
		$reset = (int) get_theme_mod('reset_options', 0);
		foreach ($options as $k=>$v) {
			if (isset($v['std'])) {
				$value = palladio_get_theme_option_std($k, $v['std']);
				if (!$reset) {
					if (isset($_GET[$k]))
						$value = wp_kses_data( wp_unslash( $_GET[$k] ) );
					else {
						$tmp = get_theme_mod($k, -987654321);
						if ($tmp != -987654321) $value = $tmp;
					}
				}
				palladio_storage_set_array2('options', $k, 'val', $value);
				if ($reset) remove_theme_mod($k);
			}
		}
		if ($reset) {
			// Unset reset flag
			set_theme_mod('reset_options', 0);
			// Regenerate CSS with default colors and fonts
			palladio_customizer_save_css();
		} else {
			do_action('palladio_action_load_options');
		}
	}
}

// Override options with stored page/post meta
if ( !function_exists('palladio_override_theme_options') ) {
	add_action( 'wp', 'palladio_override_theme_options', 1 );
	function palladio_override_theme_options($query=null) {
		if (is_page_template('blog.php')) {
			palladio_storage_set('blog_archive', true);
			palladio_storage_set('blog_template', get_the_ID());
		}
		palladio_storage_set('blog_mode', palladio_detect_blog_mode());
		if (is_singular()) {
			palladio_storage_set('options_meta', get_post_meta(get_the_ID(), 'palladio_options', true));
		}
	}
}


// Return 'std' value of the option, processed by special function (if specified)
if (!function_exists('palladio_get_theme_option_std')) {
	function palladio_get_theme_option_std($opt_name, $opt_std) {
		if (strpos($opt_std, '$palladio_')!==false) {
			$func = substr($opt_std, 1);
			if (function_exists($func)) {
				$opt_std = $func($opt_name);
			}
		}
		return $opt_std;
	}
}


// Return customizable option value
if (!function_exists('palladio_get_theme_option')) {
	function palladio_get_theme_option($name, $defa='', $strict_mode=false, $post_id=0) {
		$rez = $defa;
		$from_post_meta = false;

		if ($post_id > 0) {
			if (!palladio_storage_isset('post_options_meta', $post_id))
				palladio_storage_set_array('post_options_meta', $post_id, get_post_meta($post_id, 'palladio_options', true));
			if (palladio_storage_isset('post_options_meta', $post_id, $name)) {
				$tmp = palladio_storage_get_array('post_options_meta', $post_id, $name);
				if (!palladio_is_inherit($tmp)) {
					$rez = $tmp;
					$from_post_meta = true;
				}
			}
		}

		if (!$from_post_meta && palladio_storage_isset('options')) {

			$blog_mode = palladio_storage_get('blog_mode');

			if ( !palladio_storage_isset('options', $name) && (empty($blog_mode) || !palladio_storage_isset('options', $name.'_'.$blog_mode)) ) {
				$rez = $tmp = '_not_exists_';
				if (function_exists('trx_addons_get_option'))
					$rez = trx_addons_get_option($name, $tmp, false);
				if ($rez === $tmp) {
					if ($strict_mode) {
						$s = debug_backtrace();
						$s = array_shift($s);
						echo '<pre>' . sprintf(esc_html__('Undefined option "%s" called from:', 'palladio'), $name);
						if (function_exists('dco')) dco($s);
						else print_r($s);
						echo '</pre>';
						wp_die();
					} else
						$rez = $defa;
				}

			} else {

				// Override option from GET or POST for current blog mode
				if (!empty($blog_mode) && isset($_REQUEST[$name . '_' . $blog_mode])) {
					$rez = wp_kses_data( wp_unslash( $_REQUEST[$name . '_' . $blog_mode] ) );

				// Override option from GET
				} else if (isset($_REQUEST[$name])) {
					$rez = wp_kses_data( wp_unslash( $_REQUEST[$name] ) );

				// Override option from current page settings (if exists)
				} else if (palladio_storage_isset('options_meta', $name) && !palladio_is_inherit(palladio_storage_get_array('options_meta', $name))) {
					$rez = palladio_storage_get_array('options_meta', $name);

				// Override option from current blog mode settings: 'home', 'search', 'page', 'post', 'blog', etc. (if exists)
				} else if (!empty($blog_mode) && palladio_storage_isset('options', $name . '_' . $blog_mode, 'val') && !palladio_is_inherit(palladio_storage_get_array('options', $name . '_' . $blog_mode, 'val'))) {
					$rez = palladio_storage_get_array('options', $name . '_' . $blog_mode, 'val');

				// Get saved option value
				} else if (palladio_storage_isset('options', $name, 'val')) {
					$rez = palladio_storage_get_array('options', $name, 'val');

				// Get ThemeREX Addons option value
				} else if (function_exists('trx_addons_get_option')) {
					$rez = trx_addons_get_option($name, $defa, false);
				}
			}
		}
		return $rez;
	}
}


// Check if customizable option exists
if (!function_exists('palladio_check_theme_option')) {
	function palladio_check_theme_option($name) {
		return palladio_storage_isset('options', $name);
	}
}


// Return customizable option value, stored in the posts meta
if (!function_exists('palladio_get_theme_option_from_meta')) {
	function palladio_get_theme_option_from_meta($name, $defa='') {
		$rez = $defa;
		if (palladio_storage_isset('options_meta')) {
			if (palladio_storage_isset('options_meta', $name))
				$rez = palladio_storage_get_array('options_meta', $name);
			else
				$rez = 'inherit';
		}
		return $rez;
	}
}


// Get dependencies list from the Theme Options
if ( !function_exists('palladio_get_theme_dependencies') ) {
	function palladio_get_theme_dependencies() {
		$options = palladio_storage_get('options');
		$depends = array();
		foreach ($options as $k=>$v) {
			if (isset($v['dependency'])) 
				$depends[$k] = $v['dependency'];
		}
		return $depends;
	}
}



// -----------------------------------------------------------------
// -- Theme Settings utilities
// -----------------------------------------------------------------

// Return internal theme setting value
if (!function_exists('palladio_get_theme_setting')) {
	function palladio_get_theme_setting($name) {
		if ( !palladio_storage_isset('settings', $name) ) {
			$s = debug_backtrace();
			$s = array_shift($s);
			echo '<pre>' . sprintf(esc_html__('Undefined setting "%s" called from:', 'palladio'), $name);
			if (function_exists('dco')) dco($s);
			else print_r($s);
			echo '</pre>';
			wp_die();
		} else
			return palladio_storage_get_array('settings', $name);
	}
}

// Set theme setting
if ( !function_exists( 'palladio_set_theme_setting' ) ) {
	function palladio_set_theme_setting($option_name, $value) {
		if (palladio_storage_isset('settings', $option_name))
			palladio_storage_set_array('settings', $option_name, $value);
	}
}



// -----------------------------------------------------------------
// -- Color Schemes utilities
// -----------------------------------------------------------------

// Load saved values into color schemes
if (!function_exists('palladio_load_schemes')) {
	add_action('palladio_action_load_options', 'palladio_load_schemes');
	function palladio_load_schemes() {
		$schemes = palladio_storage_get('schemes');
		$storage = palladio_unserialize(palladio_get_theme_option('scheme_storage'));
		if (is_array($storage) && count($storage) > 0)  {
			foreach ($storage as $k=>$v) {
				if (isset($schemes[$k])) {
					$schemes[$k] = $v;
				}
			}
			palladio_storage_set('schemes', $schemes);
		}
	}
}

// Return specified color from current (or specified) color scheme
if ( !function_exists( 'palladio_get_scheme_color' ) ) {
	function palladio_get_scheme_color($color_name, $scheme = '') {
		if (empty($scheme)) $scheme = palladio_get_theme_option( 'color_scheme' );
		if (empty($scheme) || palladio_storage_empty('schemes', $scheme)) $scheme = 'default';
		$colors = palladio_storage_get_array('schemes', $scheme, 'colors');
		return $colors[$color_name];
	}
}

// Return colors from current color scheme
if ( !function_exists( 'palladio_get_scheme_colors' ) ) {
	function palladio_get_scheme_colors($scheme = '') {
		if (empty($scheme)) $scheme = palladio_get_theme_option( 'color_scheme' );
		if (empty($scheme) || palladio_storage_empty('schemes', $scheme)) $scheme = 'default';
		return palladio_storage_get_array('schemes', $scheme, 'colors');
	}
}

// Return colors from all schemes
if ( !function_exists( 'palladio_get_scheme_storage' ) ) {
	function palladio_get_scheme_storage($scheme = '') {
		return serialize(palladio_storage_get('schemes'));
	}
}

// Return schemes list
if ( !function_exists( 'palladio_get_list_schemes' ) ) {
	function palladio_get_list_schemes($prepend_inherit=false) {
		$list = array();
		$schemes = palladio_storage_get('schemes');
		if (is_array($schemes) && count($schemes) > 0) {
			foreach ($schemes as $slug => $scheme) {
				$list[$slug] = $scheme['title'];
			}
		}
		return $prepend_inherit ? palladio_array_merge(array('inherit' => esc_html__("Inherit", 'palladio')), $list) : $list;
	}
}



// -----------------------------------------------------------------
// -- Theme Fonts utilities
// -----------------------------------------------------------------

// Load saved values into fonts list
if (!function_exists('palladio_load_fonts')) {
	add_action('palladio_action_load_options', 'palladio_load_fonts');
	function palladio_load_fonts() {
		// Fonts to load when theme starts
		$fonts = array();
		for ($i=1; $i<=palladio_get_theme_setting('max_load_fonts'); $i++) {
			if (($name = palladio_get_theme_option("load_fonts-{$i}-name")) != '') {
				$fonts[] = array(
					'name'	 => $name,
					'family' => palladio_get_theme_option("load_fonts-{$i}-family"),
					'styles' => palladio_get_theme_option("load_fonts-{$i}-styles")
				);
			}
		}
		palladio_storage_set('load_fonts', $fonts);
		palladio_storage_set('load_fonts_subset', palladio_get_theme_option("load_fonts_subset"));
		
		// Font parameters of the main theme's elements
		$fonts = palladio_get_theme_fonts();
		foreach ($fonts as $tag=>$v) {
			foreach ($v as $css_prop=>$css_value) {
				if (in_array($css_prop, array('title', 'description'))) continue;
				$fonts[$tag][$css_prop] = palladio_get_theme_option("{$tag}_{$css_prop}");
			}
		}
	palladio_storage_set('theme_fonts', $fonts);
	}
}

// Return slug of the loaded font
if (!function_exists('palladio_get_load_fonts_slug')) {
	function palladio_get_load_fonts_slug($name) {
		return str_replace(' ', '-', $name);
	}
}

// Return load fonts parameter's default value
if (!function_exists('palladio_get_load_fonts_option')) {
	function palladio_get_load_fonts_option($option_name) {
		$rez = '';
		$parts = explode('-', $option_name);
		$load_fonts = palladio_storage_get('load_fonts');
		if ($parts[0] == 'load_fonts' && count($load_fonts) > $parts[1]-1 && isset($load_fonts[$parts[1]-1][$parts[2]])) {
			$rez = $load_fonts[$parts[1]-1][$parts[2]];
		}
		return $rez;
	}
}

// Return load fonts subset's default value
if (!function_exists('palladio_get_load_fonts_subset')) {
	function palladio_get_load_fonts_subset($option_name) {
		return palladio_storage_get('load_fonts_subset');
	}
}

// Return load fonts list
if (!function_exists('palladio_get_list_load_fonts')) {
	function palladio_get_list_load_fonts($prepend_inherit=false) {
		$list = array();
		$load_fonts = palladio_storage_get('load_fonts');
		if (is_array($load_fonts) && count($load_fonts) > 0) {
			foreach ($load_fonts as $font) {
				$list['"'.trim($font['name']).'"'.(!empty($font['family']) ? ','.trim($font['family']): '')] = $font['name'];
			}
		}
		return $prepend_inherit ? palladio_array_merge(array('inherit' => esc_html__("Inherit", 'palladio')), $list) : $list;
	}
}

// Return font settings of the theme specific elements
if ( !function_exists( 'palladio_get_theme_fonts' ) ) {
	function palladio_get_theme_fonts() {
		return palladio_storage_get('theme_fonts');
	}
}

// Return theme fonts parameter's default value
if (!function_exists('palladio_get_theme_fonts_option')) {
	function palladio_get_theme_fonts_option($option_name) {
		$rez = '';
		$parts = explode('_', $option_name);
		$theme_fonts = palladio_storage_get('theme_fonts');
		if (!empty($theme_fonts[$parts[0]][$parts[1]])) {
			$rez = $theme_fonts[$parts[0]][$parts[1]];
		}
		// For the font-families update options list also
		if ($parts[1] == 'font-family') {
			palladio_storage_set_array2('options', $option_name, 'options', palladio_get_list_load_fonts(true));
		}
		return $rez;
	}
}



// -----------------------------------------------------------------
// -- Other options utilities
// -----------------------------------------------------------------

// Return current theme-specific border radius for form's fields and buttons
if ( !function_exists( 'palladio_get_border_radius' ) ) {
	function palladio_get_border_radius() {
		$rad = str_replace(' ', '', palladio_get_theme_option('border_radius'));
		if (empty($rad)) $rad = 0;
		return palladio_prepare_css_value($rad); 
	}
}




// -----------------------------------------------------------------
// -- Theme Options page
// -----------------------------------------------------------------

if ( !function_exists('palladio_options_init_page_builder') ) {
	add_action( 'after_setup_theme', 'palladio_options_init_page_builder' );
	function palladio_options_init_page_builder() {
		if ( is_admin() ) {
			add_action('admin_enqueue_scripts',	'palladio_options_add_scripts');
		}
	}
}
	
// Load required styles and scripts for admin mode
if ( !function_exists( 'palladio_options_add_scripts' ) ) {
	
	function palladio_options_add_scripts() {
		$screen = function_exists('get_current_screen') ? get_current_screen() : false;
		if (is_object($screen) && $screen->id == 'appearance_page_theme_options') {
			wp_enqueue_style( 'fontello-style',  palladio_get_file_url('css/font-icons/css/fontello-embedded.css') );
			wp_enqueue_script( 'jquery-ui-tabs', false, array('jquery', 'jquery-ui-core'), null, true );
			wp_enqueue_script( 'jquery-ui-accordion', false, array('jquery', 'jquery-ui-core'), null, true );
			wp_enqueue_script( 'palladio-options', palladio_get_file_url('theme-options/theme.options.js'), array('jquery'), null, true );
			wp_localize_script( 'palladio-options', 'palladio_dependencies', palladio_get_theme_dependencies() );
			wp_localize_script( 'palladio-options', 'palladio_color_schemes', palladio_storage_get('schemes') );
			wp_localize_script( 'palladio-options', 'palladio_simple_schemes', palladio_storage_get('schemes_simple') );
		}
	}
}

// Add Theme Options item in the Appearance menu
if (!function_exists('palladio_options_add_menu_items')) {
	add_action( 'admin_menu', 'palladio_options_add_menu_items' );
	function palladio_options_add_menu_items() {
		add_theme_page(
			esc_html__('Theme Options', 'palladio'),	//page_title
			esc_html__('Theme Options', 'palladio'),	//menu_title
			'manage_options',						//capability
			'theme_options',						//menu_slug
			'palladio_options_page_builder'			//callback
		);
	}
}


// Build options page
if (!function_exists('palladio_options_page_builder')) {
	function palladio_options_page_builder() {
		?>
		<div class="palladio_options">
			<h2 class="palladio_options_title"><?php esc_html_e('Theme Options', 'palladio'); ?></h2>
			<?php palladio_show_admin_messages(); ?>
			<form id="palladio_options_form" action="#" method="post" enctype="multipart/form-data">
				<input type="hidden" name="palladio_nonce" value="<?php echo esc_attr(wp_create_nonce(admin_url())); ?>" />
				<?php palladio_options_show_fields(); ?>
				<div class="palladio_options_buttons">
					<input type="submit" value="<?php esc_attr_e('Save Options', 'palladio'); ?>">
				</div>
			</form>
		</div>
		<?php
	}
}


// Display all option's fields
if ( !function_exists('palladio_options_show_fields') ) {
	function palladio_options_show_fields($options=false) {
		if (empty($options)) $options = palladio_storage_get('options');
		$tabs_titles = $tabs_content = array();
		$last_panel = $last_section = $last_group = '';
		foreach ($options as $k=>$v) {
			// New tab
			if ($v['type']=='panel' || ($v['type']=='section' && empty($last_panel))) {
				if (!isset($tabs_titles[$k])) {
					$tabs_titles[$k] = $v['title'];
					$tabs_content[$k] = '';
				}
				if (!empty($last_group)) {
					$tabs_content[$last_section] .= '</div></div>';
					$last_group = '';
				}
				$last_section = $k;
				if ($v['type']=='panel') $last_panel = $k;

			// New group
			} else if ($v['type']=='group' || ($v['type']=='section' && !empty($last_panel))) {
				if (empty($last_group))
					$tabs_content[$last_section] = (!isset($tabs_content[$last_section]) ? '' : $tabs_content[$last_section]) 
													. '<div class="palladio_accordion palladio_options_groups">';
				else
					$tabs_content[$last_section] .= '</div>';
				$tabs_content[$last_section] .= '<h4 class="palladio_accordion_title palladio_options_group_title">' . esc_html($v['title']) . '</h4>'
												. '<div class="palladio_accordion_content palladio_options_group_content">';
				$last_group = $k;
			
			// End panel, section or group
			} else if (in_array($v['type'], array('group_end', 'section_end', 'panel_end'))) {
				if (!empty($last_group) && ($v['type'] != 'section_end' || empty($last_panel))) {
					$tabs_content[$last_section] .= '</div></div>';
					$last_group = '';
				}
				if ($v['type'] == 'panel_end') $last_panel = '';
				
			// Field's layout
			} else {
				$tabs_content[$last_section] = (!isset($tabs_content[$last_section]) ? '' : $tabs_content[$last_section]) 
												. palladio_options_show_field($k, $v);
			}
		}
		if (!empty($last_group)) {
			$tabs_content[$last_section] .= '</div></div>';
		}
		
		if (count($tabs_content) > 0) {
			// Remove empty sections
			foreach ($tabs_content as $k=>$v) {
				if (empty($v)) {
					unset($tabs_titles[$k]);
					unset($tabs_content[$k]);
				}
			}
			?>
			<div id="palladio_options_tabs" class="palladio_tabs <?php echo count($tabs_titles) > 1 ? 'with_tabs' : 'no_tabs'; ?>">
				<?php if (count($tabs_titles) > 1) { ?>
					<ul><?php
						$cnt = 0;
						foreach ($tabs_titles as $k=>$v) {
							$cnt++;
							?><li><a href="#palladio_options_section_<?php echo esc_attr($cnt); ?>"><?php echo esc_html($v); ?></a></li><?php
						}
					?></ul>
				<?php
				}
				$cnt = 0;
				foreach ($tabs_content as $k=>$v) {
					$cnt++;
					?>
					<div id="palladio_options_section_<?php echo esc_attr($cnt); ?>" class="palladio_tabs_section palladio_options_section">
						<?php palladio_show_layout($v); ?>
					</div>
					<?php
				}
				?>
			</div>
			<?php
		}
	}
}


// Display single option's field
if ( !function_exists('palladio_options_show_field') ) {
	function palladio_options_show_field($name, $field, $post_type='') {

		$inherit_allow = !empty($post_type);
		$inherit_state = !empty($post_type) && isset($field['val']) && palladio_is_inherit($field['val']);
		
		$field_data_present = $field['type']!='info' || !empty($field['override']['desc']) || !empty($field['desc']);

		if ($field['type'] == 'hidden' || (!empty($field['hidden']) && !$inherit_allow)) return '';
		
		$output = (!empty($field['class']) && strpos($field['class'], 'palladio_new_row')!==false 
					? '<div class="palladio_new_row_before"></div>'
					: '')
					. '<div class="palladio_options_item palladio_options_item_'.esc_attr($field['type'])
								. ($inherit_allow ? ' palladio_options_inherit_'.($inherit_state ? 'on' : 'off' ) : '')
								. (!empty($field['class']) ? ' '.esc_attr($field['class']) : '')
								. '">'
						. '<h4 class="palladio_options_item_title">'
							. esc_html($field['title'])
							. ($inherit_allow 
									? '<span class="palladio_options_inherit_lock" id="palladio_options_inherit_'.esc_attr($name).'"></span>'
									: '')
						. '</h4>'
						. ($field_data_present
							? '<div class="palladio_options_item_data">'
								. '<div class="palladio_options_item_field" data-param="'.esc_attr($name).'"'
									. (!empty($field['linked']) ? ' data-linked="'.esc_attr($field['linked']).'"' : '')
									. '>'
							: '');
	
		// Type 'checkbox'
		if ($field['type']=='checkbox') {
			$output .= '<label class="palladio_options_item_label">'
						. '<input type="checkbox" name="palladio_options_field_'.esc_attr($name).'" value="1"'
								.($field['val']==1 ? ' checked="checked"' : '')
								.' />'
						. esc_html($field['title'])
					. '</label>';
		
		// Type 'switch' (2 choises) or 'radio' (3+ choises)
		} else if (in_array($field['type'], array('switch', 'radio'))) {
			$field['options'] = apply_filters('palladio_filter_options_get_list_choises', $field['options'], $name);
			$first = true;
			foreach ($field['options'] as $k=>$v) {
				$output .= '<label class="palladio_options_item_label">'
							. '<input type="radio" name="palladio_options_field_'.esc_attr($name).'"'
									. ' value="'.esc_attr($k).'"'
									. ($field['val']==$k || ($first && !isset($field['options'][$field['val']])) ? ' checked="checked"' : '')
									. ' />'
							. esc_html($v)
						. '</label>';
				$first = false;
			}

		// Type 'text' or 'time' or 'date'
		} else if (in_array($field['type'], array('text', 'time', 'date'))) {
			$output .= '<input type="text" name="palladio_options_field_'.esc_attr($name).'"'
							. ' value="'.esc_attr(palladio_is_inherit($field['val']) ? '' : $field['val']).'"'
							. ' />';
		
		// Type 'textarea'
		} else if ($field['type']=='textarea') {
			$output .= '<textarea name="palladio_options_field_'.esc_attr($name).'">'
							. esc_html(palladio_is_inherit($field['val']) ? '' : $field['val'])
						. '</textarea>';
			
		// Type 'select'
		} else if ($field['type']=='select') {
			$field['options'] = apply_filters('palladio_filter_options_get_list_choises', $field['options'], $name);
			$output .= '<select size="1" name="palladio_options_field_'.esc_attr($name).'">';
			foreach ($field['options'] as $k=>$v) {
				$output .= '<option value="'.esc_attr($k).'"'.($field['val']==$k ? ' selected="selected"' : '').'>'.esc_html($v).'</option>';
			}
			$output .= '</select>';

		// Type 'image', 'media', 'video' or 'audio'
		} else if (in_array($field['type'], array('image', 'media', 'video', 'audio'))) {
			$output .= (!empty($field['multiple'])
						? '<input type="hidden" id="palladio_options_field_'.esc_attr($name).'"'
							. ' name="palladio_options_field_'.esc_attr($name).'"'
							. ' value="'.esc_attr(palladio_is_inherit($field['val']) ? '' : $field['val']).'"'
							. ' />'
						: '<input type="text" id="palladio_options_field_'.esc_attr($name).'"'
							. ' name="palladio_options_field_'.esc_attr($name).'"'
							. ' value="'.esc_attr(palladio_is_inherit($field['val']) ? '' : $field['val']).'"'
							. ' />')
					. palladio_show_custom_field('palladio_options_field_'.esc_attr($name).'_button',
												array(
													'type'			 => 'mediamanager',
													'multiple'		 => !empty($field['multiple']),
													'data_type'		 => $field['type'],
													'linked_field_id'=> 'palladio_options_field_'.esc_attr($name)
												),
												palladio_is_inherit($field['val']) ? '' : $field['val']);
		
		// Type 'icon'
		} else if ($field['type']=='icon') {
			$output .= '<input type="text" id="palladio_options_field_'.esc_attr($name).'"'
							. ' name="palladio_options_field_'.esc_attr($name).'"'
							. ' value="'.esc_attr(palladio_is_inherit($field['val']) ? '' : $field['val']).'"'
							. ' />'
						. palladio_show_custom_field('palladio_options_field_'.esc_attr($name).'_button',
													array(
														'type'	 => 'icons',
														'button' => true,
														'icons'	 => true
													),
													$field['val']);
		
		// Type 'checklist'
		} else if ($field['type']=='checklist') {
			$output .= '<input type="hidden" id="palladio_options_field_'.esc_attr($name).'"'
							. ' name="palladio_options_field_'.esc_attr($name).'"'
							. ' value="'.esc_attr(palladio_is_inherit($field['val']) ? '' : $field['val']).'"'
							. ' />'
						. palladio_show_custom_field('palladio_options_field_'.esc_attr($name).'_list',
													array(
														'type'	 => 'checklist',
														'options' => $field['options'],
														'sortable' => !empty($field['sortable']),
														'dir' => !empty($field['dir']) ? $field['dir'] : 'horizontal'
													),
													$field['val']);
		
		// Type 'scheme_editor'
		} else if ($field['type']=='scheme_editor') {
			$output .= '<input type="hidden" id="palladio_options_field_'.esc_attr($name).'"'
							. ' name="palladio_options_field_'.esc_attr($name).'"'
							. ' value="'.esc_attr(palladio_is_inherit($field['val']) ? '' : $field['val']).'"'
							. ' />'
						. palladio_show_custom_field('palladio_options_field_'.esc_attr($name).'_scheme',
													array('type' => 'scheme_editor'),
													palladio_unserialize($field['val']));
		}
		
		$output .= ($inherit_allow
						? '<div class="palladio_options_inherit_cover'.(!$inherit_state ? ' palladio_hidden' : '').'">'
							. '<span class="palladio_options_inherit_label">' . esc_html__('Inherit', 'palladio') . '</span>'
							. '<input type="hidden" name="palladio_options_inherit_'.esc_attr($name).'"'
									. ' value="'.esc_attr($inherit_state ? 'inherit' : '').'"'
									. ' />'
							. '</div>'
						: '')
					. ($field_data_present ? '</div>' : '')
					. (!empty($field['override']['desc']) || !empty($field['desc'])
						? '<div class="palladio_options_item_description">'
							. (!empty($field['override']['desc']) 	// param 'desc' already processed with wp_kses()!
									? $field['override']['desc'] 
									: $field['desc'])
							. '</div>'
						: '')
				. ($field_data_present ? '</div>' : '')
			. '</div>';
		return $output;
	}
}


// Show theme specific fields
function palladio_show_custom_field($id, $field, $value) {
	$output = '';
	switch ($field['type']) {
		
		case 'mediamanager':
			wp_enqueue_media( );
			$title = empty($field['data_type']) || $field['data_type']=='image'
							? esc_html__( 'Choose Image', 'palladio')
							: esc_html__( 'Choose Media', 'palladio');
			$output .= '<a id="'.esc_attr($id).'"'
							. ' class="button mediamanager palladio_media_selector"'
							. '	data-param="' . esc_attr($id) . '"'
							. '	data-choose="'.esc_attr(!empty($field['multiple']) ? esc_html__( 'Choose Images', 'palladio') : $title).'"'
							. ' data-update="'.esc_attr(!empty($field['multiple']) ? esc_html__( 'Add to Gallery', 'palladio') : $title).'"'
							. '	data-multiple="'.esc_attr(!empty($field['multiple']) ? '1' : '0').'"'
							. '	data-type="'.esc_attr(!empty($field['data_type']) ? $field['data_type'] : 'image').'"'
							. '	data-linked-field="'.esc_attr($field['linked_field_id']).'"'
							. '>'
							. (!empty($field['multiple'])
									? (empty($field['data_type']) || $field['data_type']=='image'
										? esc_html__( 'Add Images', 'palladio')
										: esc_html__( 'Add Files', 'palladio')
										)
									: esc_html($title)
								)
							. '</a>';
			$output .= '<span class="palladio_options_field_preview">';
			$images = explode('|', $value);
			if (is_array($images)) {
				foreach ($images as $img)
					$output .= $img && !palladio_is_inherit($img)
							? '<span>'
									. (in_array(palladio_get_file_ext($img), array('gif', 'jpg', 'jpeg', 'png'))
											? '<img src="' . esc_url($img) . '" >'
											: '<a href="' . esc_attr($img) . '">' . esc_html(basename($img)) . '</a>'
										)
								. '</span>' 
							: '';
			}
			$output .= '</span>';
			break;

		case 'icons':
			$icons_type = !empty($field['style']) 
							? $field['style'] 
							: palladio_get_theme_setting('icons_type');
			if (empty($field['return']))
				$field['return'] = 'full';
			$palladio_icons = $icons_type=='images'
								? palladio_get_list_images()
								: palladio_array_from_list(palladio_get_list_icons());
			if (is_array($palladio_icons)) {
				if (!empty($field['button']))
					$output .= '<span id="'.esc_attr($id).'"'
									. ' class="palladio_list_icons_selector'
											. ($icons_type=='icons' && !empty($value) ? ' '.esc_attr($value) : '')
											.'"'
									. ' title="'.esc_attr__('Select icon', 'palladio').'"'
									. ' data-style="'.($icons_type=='images' ? 'images' : 'icons').'"'
									. ($icons_type=='images' && !empty($value) 
										? ' style="background-image: url('.esc_url($field['return']=='slug' 
																							? $palladio_icons[$value] 
																							: $value).');"' 
											: '')
								. '></span>';
				if (!empty($field['icons'])) {
					$output .= '<div class="palladio_list_icons">';
					foreach($palladio_icons as $slug=>$icon) {
						$output .= '<span class="'.esc_attr($icons_type=='icons' ? $icon : $slug)
								. (($field['return']=='full' ? $icon : $slug) == $value ? ' palladio_list_active' : '')
								. '"'
								. ' title="'.esc_attr($slug).'"'
								. ' data-icon="'.esc_attr($field['return']=='full' ? $icon : $slug).'"'
								. ($icons_type=='images' ? ' style="background-image: url('.esc_url($icon).');"' : '')
								. '></span>';
					}
					$output .= '</div>';
				}
			}
			break;

		case 'checklist':
			if (!empty($field['sortable']))
				wp_enqueue_script('jquery-ui-sortable', false, array('jquery', 'jquery-ui-core'), null, true);
			$output .= '<div class="palladio_checklist palladio_checklist_'.esc_attr($field['dir'])
						. (!empty($field['sortable']) ? ' palladio_sortable' : '') 
						. '">';
			if (!is_array($value)) {
				if (!empty($value) && !palladio_is_inherit($value)) parse_str(str_replace('|', '&', $value), $value);
				else $value = array();
			}
			// Sort options by values order
			if (!empty($field['sortable']) && is_array($value)) {
				$field['options'] = palladio_array_merge($value, $field['options']);
			}
			foreach ($field['options'] as $k=>$v) {
				$output .= '<label class="palladio_checklist_item_label' 
								. (!empty($field['sortable']) ? ' palladio_sortable_item' : '') 
								. '">'
							. '<input type="checkbox" value="1" data-name="'.$k.'"'
								.( isset($value[$k]) && (int) $value[$k] == 1 ? ' checked="checked"' : '')
								.' />'
							. (substr($v, 0, 4)=='http' ? '<img src="'.esc_url($v).'">' : esc_html($v))
						. '</label>';
			}
			$output .= '</div>';
			break;
			
		case 'scheme_editor':
			if (!is_array($value)) break;
			$output .= '<div class="palladio_scheme_editor">';
			// Select scheme
			$output .= '<select class="palladio_scheme_editor_selector">';
			foreach ($value as $scheme=>$v)
				$output .= '<option value="' . esc_attr($scheme) . '">' . esc_html($v['title']) . '</option>';
			$output .= '</select>';
			// Select type
			$output .= '<div class="palladio_scheme_editor_type">'
							. '<div class="palladio_scheme_editor_row">'
								. '<span class="palladio_scheme_editor_row_cell">'
									. esc_html__('Editor type', 'palladio')
								. '</span>'
								. '<span class="palladio_scheme_editor_row_cell palladio_scheme_editor_row_cell_span">'
									.'<label>'
										. '<input name="palladio_scheme_editor_type" type="radio" value="simple" checked="checked"> '
										. esc_html__('Simple', 'palladio')
									. '</label>'
									. '<label>'
										. '<input name="palladio_scheme_editor_type" type="radio" value="advanced"> '
										. esc_html__('Advanced', 'palladio')
									. '</label>'
								. '</span>'
							. '</div>'
						. '</div>';
			// Colors
			$groups = palladio_storage_get('scheme_color_groups');
			$colors = palladio_storage_get('scheme_color_names');
			$output .= '<div class="palladio_scheme_editor_colors">';
			foreach ($value as $scheme=>$v) {
				$output .= '<div class="palladio_scheme_editor_header">'
								. '<span class="palladio_scheme_editor_header_cell"></span>';
				foreach ($groups as $group_name=>$group_data) {
					$output .= '<span class="palladio_scheme_editor_header_cell" title="'.esc_attr($group_data['description']).'">'
								. esc_html($group_data['title'])
								. '</span>';
				}
				$output .= '</div>';
				foreach ($colors as $color_name=>$color_data) {
					$output .= '<div class="palladio_scheme_editor_row">'
								. '<span class="palladio_scheme_editor_row_cell" title="'.esc_attr($color_data['description']).'">'
								. esc_html($color_data['title'])
								. '</span>';
					foreach ($groups as $group_name=>$group_data) {
						$slug = $group_name == 'main' 
									? $color_name 
									: str_replace('text_', '', "{$group_name}_{$color_name}");
						$output .= '<span class="palladio_scheme_editor_row_cell">'
									. (isset($v['colors'][$slug])
										? "<input type=\"text\" name=\"{$slug}\" class=\"iColorPicker\" value=\"".esc_attr($v['colors'][$slug])."\">"
										: ''
										)
									. '</span>';
					}
					$output .= '</div>';
				}
				break;
			}
			$output .= '</div>'
					. '</div>';
			break;
	}
	return apply_filters('palladio_filter_show_custom_field', $output, $id, $field, $value);
}



// Save options
if (!function_exists('palladio_options_save')) {
	add_action('after_setup_theme', 'palladio_options_save', 4);
	function palladio_options_save() {

		if (!isset($_REQUEST['page']) || $_REQUEST['page']!='theme_options' || palladio_get_value_gp('palladio_nonce')=='') return;

		// verify nonce
		if ( !wp_verify_nonce( palladio_get_value_gp('palladio_nonce'), admin_url() ) ) {
			palladio_add_admin_message(esc_html__('Bad security code! Options are not saved!', 'palladio'), 'error', true);
			return;
		}

		// Check permissions
		if (!current_user_can('manage_options')) {
			palladio_add_admin_message(esc_html__('Manage options is denied for the current user! Options are not saved!', 'palladio'), 'error', true);
			return;
		}

		// Save options
		$options = palladio_storage_get('options');
		$values = get_theme_mods();
		$external_storages = array();
		foreach ($options as $k=>$v) {
			// Skip non-data options - sections, info, etc.
			if (!isset($v['std'])) continue;
			// Get option value from POST
			$value = isset($_POST['palladio_options_field_' . $k])
							? palladio_get_value_gp('palladio_options_field_' . $k)
							: ($v['type']=='checkbox' ? 0 : '');
			if ($value != palladio_get_theme_option_std($k, $v['std']))
				$values[$k] = $value;
			else if (isset($values[$k]))
				unset($values[$k]);
			// External plugin's options
			if (!empty($v['options_storage'])) {
				if (!isset($external_storages[$v['options_storage']]))
					$external_storages[$v['options_storage']] = array();
				$external_storages[$v['options_storage']][$k] = $value;
			}
		}

		// Update options in the external storages
		foreach ($external_storages as $storage_name => $storage_values) {
			$storage = get_option($storage_name, false);
			if (is_array($storage)) {
				foreach ($storage_values as $k=>$v)
					$storage[$k] = $v;
				update_option($storage_name, $storage);
			}
		}

		// Update Theme Mods (internal Theme Options)
		$stylesheet_slug = get_option('stylesheet');
		update_option("theme_mods_{$stylesheet_slug}", $values);

		// Store new schemes colors
		if (!empty($values['scheme_storage'])) {
			$schemes = palladio_unserialize($values['scheme_storage']);
			if (is_array($schemes) && count($schemes) > 0) 
				palladio_storage_set('schemes', $schemes);
		}
		
		// Store new fonts parameters
		$fonts = palladio_get_theme_fonts();
		foreach ($fonts as $tag=>$v) {
			foreach ($v as $css_prop=>$css_value) {
				if (in_array($css_prop, array('title', 'description'))) continue;
				if (isset($values["{$tag}_{$css_prop}"])) $fonts[$tag][$css_prop] = $values["{$tag}_{$css_prop}"];
			}
		}
		palladio_storage_set('theme_fonts', $fonts);

		// Update ThemeOptions save timestamp
		$stylesheet_time = time();
		update_option("palladio_options_timestamp_{$stylesheet_slug}", $stylesheet_time);

		// Sinchronize theme options between child and parent themes
		if (palladio_get_theme_setting('duplicate_options') == 'both') {
			$theme_slug = get_option('template');
			if ($theme_slug != $stylesheet_slug) {
				palladio_customizer_duplicate_theme_options($stylesheet_slug, $theme_slug, $stylesheet_time);
			}
		}

		// Regenerate CSS with new colors
		palladio_customizer_save_css();

		// Return result
		// Return result
		palladio_add_admin_message( esc_html__( 'Options are saved', 'palladio' ) );
		wp_redirect( get_admin_url( null, 'admin.php?page=theme_options' ) );
		exit();
	}
}


// Refresh data in the linked field
// according the main field value
if (!function_exists('palladio_refresh_linked_data')) {
	function palladio_refresh_linked_data($value, $linked_name) {
		if ($linked_name == 'parent_cat') {
			$tax = palladio_get_post_type_taxonomy($value);
			$terms = !empty($tax) ? palladio_get_list_terms(false, $tax) : array();
			$terms = palladio_array_merge(array(0 => esc_html__('- Select category -', 'palladio')), $terms);
			palladio_storage_set_array2('options', $linked_name, 'options', $terms);
		}
	}
}


// AJAX: Refresh data in the linked fields
if (!function_exists('palladio_callback_get_linked_data')) {
	add_action('wp_ajax_palladio_get_linked_data', 		'palladio_callback_get_linked_data');
	add_action('wp_ajax_nopriv_palladio_get_linked_data','palladio_callback_get_linked_data');
	function palladio_callback_get_linked_data() {
		if ( !wp_verify_nonce( palladio_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			wp_die();
		$chg_name = wp_kses_data( wp_unslash( $_REQUEST['chg_name'] ) );
		$chg_value = wp_kses_data( wp_unslash( $_REQUEST['chg_value'] ) );
		$response = array('error' => '');
		if ($chg_name == 'post_type') {
			$tax = palladio_get_post_type_taxonomy($chg_value);
			$terms = !empty($tax) ? palladio_get_list_terms(false, $tax) : array();
			$response['list'] = palladio_array_merge(array(0 => esc_html__('- Select category -', 'palladio')), $terms);
		}
		echo json_encode($response);
		wp_die();
	}
}
?>