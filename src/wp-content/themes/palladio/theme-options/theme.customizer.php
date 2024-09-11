<?php
/**
 * Theme customizer
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */


//--------------------------------------------------------------
//-- First run actions after switch theme
//--------------------------------------------------------------
if (!function_exists('palladio_customizer_action_switch_theme')) {
	add_action('after_switch_theme', 'palladio_customizer_action_switch_theme');
	function palladio_customizer_action_switch_theme() {
		// Duplicate theme options between parent and child themes
		$duplicate = palladio_get_theme_setting('duplicate_options');
		if (in_array($duplicate, array('child', 'both'))) {
			$theme_slug = get_option( 'template' );
			$theme_time = (int) get_option( "palladio_options_timestamp_{$theme_slug}" );
			$stylesheet_slug = get_option( 'stylesheet' );

			// If child-theme is activated - duplicate options from template to the child-theme
			if ($theme_slug != $stylesheet_slug) {
				$stylesheet_time = (int) get_option( "palladio_options_timestamp_{$stylesheet_slug}" );
				if ($theme_time > $stylesheet_time) palladio_customizer_duplicate_theme_options($theme_slug, $stylesheet_slug, $theme_time);
			
			// If main theme (template) is activated and 'duplicate_options' == 'child'
			// (duplicate options only from template to the child-theme) - regenerate CSS  with custom colors and fonts
			} else if ($duplicate == 'child' && $theme_time > 0) {
				palladio_customizer_save_css();
			}
		}
	}
}


// Duplicate theme options between template and child-theme
if (!function_exists('palladio_customizer_duplicate_theme_options')) {
	function palladio_customizer_duplicate_theme_options($from, $to, $timestamp = 0) {
		if ($timestamp == 0) $timestamp = get_option("palladio_options_timestamp_{$from}");
		$from = "theme_mods_{$from}";
		$from_options = get_option($from);
		$to = "theme_mods_{$to}";
		$to_options = get_option($to);
		if (is_array($from_options)) {
			if (!is_array($to_options)) $to_options = array();
			$theme_options = palladio_storage_get('options');
			foreach ($from_options as $k => $v) {
				if (isset($theme_options[$k])) $to_options[$k] = $v;
			}
			update_option($to, $to_options);
			update_option("palladio_options_timestamp_{$to}", $timestamp);
		}
	}
}


//--------------------------------------------------------------
//-- New panel in the Customizer Controls
//--------------------------------------------------------------

// Theme init priorities:
// 3 - add/remove Theme Options elements
if (!function_exists('palladio_customizer_setup3')) {
	add_action( 'after_setup_theme', 'palladio_customizer_setup3', 3 );
	function palladio_customizer_setup3() {
		palladio_storage_merge_array('options', '', array(
			'cpt' => array(
				"title" => esc_html__('Plugins settings', 'palladio'),
				"desc" => '',
				"priority" => 400,
				"type" => "panel"
				)
			)
		);
	}
}
// 3 - add/remove Theme Options elements
if (!function_exists('palladio_customizer_setup9999')) {
	add_action( 'after_setup_theme', 'palladio_customizer_setup9999', 9999 );
	function palladio_customizer_setup9999() {
		palladio_storage_merge_array('options', '', array(
			'cpt_end' => array(
				"type" => "panel_end"
				)
			)
		);
	}
}


//--------------------------------------------------------------
//-- Register Customizer Controls
//--------------------------------------------------------------

define('PALLADIO_CUSTOMIZE_PRIORITY', 200);		// Start priority for the new controls

if (!function_exists('palladio_customizer_register_controls')) {
	add_action( 'customize_register', 'palladio_customizer_register_controls', 11 );
	function palladio_customizer_register_controls( $wp_customize ) {

		$refresh_auto = palladio_get_theme_setting('custmize_refresh') != 'manual';
		
		$panels = array('');
		$p = 0;
		$sections = array('');
		$s = 0;
		
		$i = PALLADIO_CUSTOMIZE_PRIORITY;

		// Reload Theme Options before create controls
		if (is_admin()) {
			palladio_storage_set('options_reloaded', true);
			palladio_load_theme_options();
		}
		$options = palladio_storage_get('options');
		
		foreach ($options as $id=>$opt) {
			
			$i = !empty($opt['priority']) 
					? $opt['priority'] 
					: (in_array($opt['type'], array('panel', 'section'))
							? PALLADIO_CUSTOMIZE_PRIORITY
							: $i++
						);
			
			if (!empty($opt['hidden'])) continue;
			
			if ($opt['type'] == 'panel') {

				$sec = $wp_customize->get_panel( $id );
				if ( is_object($sec) && !empty($sec->title) ) {
					$sec->title      = $opt['title'];
					$sec->description= $opt['desc'];
					if ( !empty($opt['priority']) )	$sec->priority = $opt['priority'];
				} else {
					$wp_customize->add_panel( esc_attr($id) , array(
						'title'      => $opt['title'],
						'description'=> $opt['desc'],
						'priority'	 => $i
					) );
				}
				array_push($panels, $id);
				$p++;

			} else if ($opt['type'] == 'panel_end') {

				array_pop($panels);
				$p--;

			} else if ($opt['type'] == 'section') {

				$sec = $wp_customize->get_section( $id );
				if ( is_object($sec) && !empty($sec->title) ) {
					$sec->title      = $opt['title'];
					$sec->description= $opt['desc'];
					if ( !empty($opt['priority']) )	$sec->priority = $opt['priority'];
				} else {
					$wp_customize->add_section( esc_attr($id) , array(
						'title'      => $opt['title'],
						'description'=> $opt['desc'],
						'panel'  => esc_attr($panels[$p]),
						'priority'	 => $i
					) );
				}
				array_push($sections, $id);
				$s++;

			} else if ($opt['type'] == 'section_end') {

				array_pop($sections);
				$s--;

			} else if ($opt['type'] == 'select') {

				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority'	 => $i,
					'type'     => 'select',
					'choices'  => apply_filters('palladio_filter_options_get_list_choises', $opt['options'], $id)
				) );

			} else if ($opt['type'] == 'radio') {

				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority'	 => $i,
					'type'     => 'radio',
					'choices'  => apply_filters('palladio_filter_options_get_list_choises', $opt['options'], $id)
				) );

			} else if ($opt['type'] == 'switch') {

				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( new Palladio_Customize_Switch_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'choices'  => apply_filters('palladio_filter_options_get_list_choises', $opt['options'], $id),
					'input_attrs' => array(
						'value' => palladio_get_theme_option($id),
					)
				) ) );

			} else if ($opt['type'] == 'checkbox') {

				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority'	 => $i,
					'type'     => 'checkbox'
				) );

			} else if ($opt['type'] == 'color') {

				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority'	 => $i,
				) ) );

			} else if ($opt['type'] == 'image') {

				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
				) ) );

			} else if (in_array($opt['type'], array('media', 'audio', 'video'))) {
				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
				) ) );

			} else if ($opt['type'] == 'icon') {

				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( new Palladio_Customize_Icon_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'input_attrs' => array(
						'value' => palladio_get_theme_option($id),
					)
				) ) );

			} else if ($opt['type'] == 'checklist') {

				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( new Palladio_Customize_Checklist_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'choices' => apply_filters('palladio_filter_options_get_list_choises', $opt['options'], $id),
					'input_attrs' => array(
						'value' => palladio_get_theme_option($id),
						'sortable' => !empty($opt['sortable']),
						'dir' => !empty($opt['dir']) ? $opt['dir'] : 'horizontal'
					)
				) ) );

			} else if ($opt['type'] == 'scheme_editor') {

				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( new Palladio_Customize_Scheme_Editor_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
					'input_attrs' => array(
						'value' => palladio_get_theme_option($id),
					)
				) ) );

			} else if ($opt['type'] == 'button') {
			
				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );

				$wp_customize->add_control( new Palladio_Customize_Button_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'input_attrs' => array(
						'caption' => $opt['caption'],
						'action' => $opt['action']
					),
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
				) ) );

			} else if ($opt['type'] == 'info') {
			
				$wp_customize->add_setting( $id, array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'postMessage'
				) );

				$wp_customize->add_control( new Palladio_Customize_Info_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
				) ) );

			} else if ($opt['type'] == 'hidden') {
			
				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => 'palladio_sanitize_html',
					'transport'         => 'postMessage'
				) );

				$wp_customize->add_control( new Palladio_Customize_Hidden_Control( $wp_customize, $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority' => $i,
				) ) );

			} else {

				$wp_customize->add_setting( $id, array(
					'default'           => palladio_get_theme_option($id),
					'sanitize_callback' => $opt['type'] == 'text' ? 'sanitize_text_field' : 'sanitize_textarea_field',
					'transport'         => $refresh_auto && (!isset($opt['refresh']) || $opt['refresh']) ? 'refresh' : 'postMessage'
				) );
			
				$wp_customize->add_control( $id, array(
					'label'    => $opt['title'],
					'description' => $opt['desc'],
					'section'  => esc_attr($sections[$s]),
					'priority'	 => $i,
					'type'     => $opt['type']
				) );
			}

		}

		// Setup standard WP Controls
		// ---------------------------------

		// Reorder standard WP sections
		$sec = $wp_customize->get_panel( 'nav_menus' );
		if (is_object($sec)) $sec->priority = 1000;
		$sec = $wp_customize->get_panel( 'widgets' );
		if (is_object($sec)) $sec->priority = 1010;
		$sec = $wp_customize->get_section( 'static_front_page' );
		if (is_object($sec)) $sec->priority = 1020;
		$sec = $wp_customize->get_section( 'custom_css' );
		if (is_object($sec)) $sec->priority = 2000;
		
		// Modify standard WP controls
		$sec = $wp_customize->get_control( 'blogname' );
		if (is_object($sec))
			$sec->description = esc_html__('Use "[[" and "]]" to modify style and color of parts of the text, "||" to break current line',
											'palladio');
		$sec = $wp_customize->get_setting( 'blogname' );
		if (is_object($sec)) $sec->transport = 'postMessage';

		$sec = $wp_customize->get_setting( 'blogdescription' );
		if (is_object($sec)) $sec->transport = 'postMessage';

		$sec = $wp_customize->get_section( 'header_image' );
		$sec2 = $wp_customize->get_control( 'header_image_info' );
		$sec2->description = (!empty($sec2->description) ? $sec2->description . '<br>' : '') . $sec->description;

		$sec = $wp_customize->get_control( 'header_image' );
		if (is_object($sec)) {
			$sec->priority = 300;
			$sec->section = 'header';
		}
		$sec = $wp_customize->get_control( 'header_video' );
		if (is_object($sec)) {
			$sec->priority = 310;
			$sec->section = 'header';
		}
		$sec = $wp_customize->get_control( 'external_header_video' );
		if (is_object($sec)) {
			$sec->priority = 320;
			$sec->section = 'header';
		}
		
		$sec = $wp_customize->get_section( 'background_image' );
		if (is_object($sec)) {
			$sec->title = esc_html__('Background', 'palladio');
			$sec->priority = 310;
			$sec->description = esc_html__('Used only if "General settings - Body style" equal to "boxed"', 'palladio');
		}

		$sec = $wp_customize->get_control( 'background_color' );
		if (is_object($sec)) {
			$sec->priority = 10;
			$sec->section = 'background_image';
		}

		// Remove unused sections
		$wp_customize->remove_section( 'colors');
		$wp_customize->remove_section( 'header_image');
	}
}


// Create custom controls for customizer
if (!function_exists('palladio_customizer_custom_controls')) {
	add_action( 'customize_register', 'palladio_customizer_custom_controls' );
	function palladio_customizer_custom_controls( $wp_customize ) {
	
		class Palladio_Customize_Info_Control extends WP_Customize_Control {
			public $type = 'info';

			public function render_content() {
				?><label><?php
				if (!empty($this->label)) {
					?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
				}
				if (!empty($this->description)) {
					?><span class="customize-control-description desctiption"><?php palladio_show_layout( $this->description ); ?></span><?php
				}
				?></label><?php
			}
		}
	
		class Palladio_Customize_Switch_Control extends WP_Customize_Control {
			public $type = 'switch';

			public function render_content() {
				?><label><?php
				if (!empty($this->label)) {
					?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
				}
				if (!empty($this->description)) {
					?><span class="customize-control-description desctiption"><?php palladio_show_layout( $this->description ); ?></span><?php
				}
				if (is_array($this->choices) && count($this->choices)>0) {
					foreach ($this->choices as $k=>$v) {
						?><label><input type="radio" name="_customize-radio-<?php echo esc_attr($this->id); ?>" <?php
										$this->link();
										if ($k == $this->input_attrs['value']) echo ' checked="checked"';
										?> value="<?php echo esc_attr($k); ?>">
						<?php echo esc_html($v); ?></label><?php
					}
				}
				?></label><?php
			}
		}
	
		class Palladio_Customize_Icon_Control extends WP_Customize_Control {
			public $type = 'icon';

			public function render_content() {
				?><label><?php
				if (!empty($this->label)) {
					?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
				}
				if (!empty($this->description)) {
					?><span class="customize-control-description desctiption"><?php palladio_show_layout( $this->description ); ?></span><?php
				}
				?><span class="customize-control-field-wrap"><input type="text" <?php $this->link(); ?> /><?php
				palladio_show_layout(palladio_show_custom_field('_customize-icon-selector-'.esc_attr($this->id),
															array(
																'type'	 => 'icons',
																'button' => true,
																'icons'	 => true
															),
															$this->input_attrs['value']
															)
									);
				?></span></label><?php
			}
		}
	
		class Palladio_Customize_Checklist_Control extends WP_Customize_Control {
			public $type = 'checklist';

			public function render_content() {
				?><label><?php
				if (!empty($this->label)) {
					?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
				}
				if (!empty($this->description)) {
					?><span class="customize-control-description desctiption"><?php palladio_show_layout( $this->description ); ?></span><?php
				}
				?><span class="customize-control-field-wrap"><input type="hidden" <?php $this->link(); ?> /><?php
				palladio_show_layout(palladio_show_custom_field('_customize-checklist-'.esc_attr($this->id),
															array(
																'type'	 => 'checklist',
																'options' => $this->choices,
																'sortable' => !empty($this->input_attrs['sortable']),
																'dir' => !empty($this->input_attrs['dir']) ? $this->input_attrs['dir'] : 'horizontal'
															),
															$this->input_attrs['value']
															)
									);
				?></span></label><?php
			}
		}
	
		class Palladio_Customize_Button_Control extends WP_Customize_Control {
			public $type = 'button';

			public function render_content() {
				?><label><?php
				if (!empty($this->label)) {
					?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
				}
				if (!empty($this->description)) {
					?><span class="customize-control-description desctiption"><?php palladio_show_layout( $this->description ); ?></span><?php
				}
				?>
				<input type="button" 
						name="_customize-button-<?php echo esc_attr($this->id); ?>" 
						value="<?php echo esc_attr($this->input_attrs['caption']); ?>"
						data-action="<?php echo esc_attr($this->input_attrs['action']); ?>">
				</label>
				<?php
			}
		}

		class Palladio_Customize_Hidden_Control extends WP_Customize_Control {
			public $type = 'info';

			public function render_content() {
				?><input type="hidden" name="_customize-hidden-<?php echo esc_attr($this->id); ?>" <?php $this->link(); ?> value=""><?php
			}
		}
	
		class Palladio_Customize_Scheme_Editor_Control extends WP_Customize_Control {
			public $type = 'scheme_editor';

			public function render_content() {
				?><label><?php
				if (!empty($this->label)) {
					?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
				}
				if (!empty($this->description)) {
					?><span class="customize-control-description desctiption"><?php palladio_show_layout( $this->description ); ?></span><?php
				}
				?><span class="customize-control-field-wrap"><input type="hidden" <?php $this->link(); ?> /><?php
				palladio_show_layout(palladio_show_custom_field('_customize-scheme-editor-'.esc_attr($this->id),
															array('type' => 'scheme_editor'),
															palladio_unserialize($this->input_attrs['value'])
															)
									);
				?></span></label><?php
			}
		}
	
	}
}


// Sanitize plain value - remove all tags and spaces
if (!function_exists('palladio_sanitize_value')) {
	function palladio_sanitize_value($value) {
		return empty($value) ? $value : trim(strip_tags($value));
	}
}


// Sanitize html value - keep only allowed tags
if (!function_exists('palladio_sanitize_html')) {
	function palladio_sanitize_html($value) {
		return empty($value) ? $value : wp_kses_post($value);
	}
}


//--------------------------------------------------------------
// Save custom settings in CSS file
//--------------------------------------------------------------

// Save CSS with custom colors and fonts after save custom options
if (!function_exists('palladio_customizer_action_save_after')) {
	add_action('customize_save_after', 'palladio_customizer_action_save_after');
	function palladio_customizer_action_save_after($api=false) {

		// Get saved settings
		$settings = $api->settings();

		// Store new schemes colors
		$schemes = palladio_unserialize($settings['scheme_storage']->value());
		if (is_array($schemes) && count($schemes) > 0) 
			palladio_storage_set('schemes', $schemes);

		// Store new fonts parameters
		$fonts = palladio_get_theme_fonts();
		foreach ($fonts as $tag=>$v) {
			foreach ($v as $css_prop=>$css_value) {
				if (in_array($css_prop, array('title', 'description'))) continue;
				$fonts[$tag][$css_prop] = $settings["{$tag}_{$css_prop}"]->value();
			}
		}
		palladio_storage_set('theme_fonts', $fonts);

		// Collect options from the external storages
		$options = palladio_storage_get('options');
		$external_storages = array();
		foreach ($options as $k=>$v) {
			// Skip non-data options - sections, info, etc.
			if (!isset($v['std']) || empty($v['options_storage'])) continue;
			// Get option value from Customizer
			$value = isset($settings[$k])
							? $settings[$k]->value()
							: ($v['type']=='checkbox' ? 0 : '');
			if (!isset($external_storages[$v['options_storage']]))
				$external_storages[$v['options_storage']] = array();
			$external_storages[$v['options_storage']][$k] = $value;
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

		// Update ThemeOptions save timestamp
		$stylesheet_slug = get_option('stylesheet');
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
	}
}

// Save CSS with custom colors and fonts into custom.css
if (!function_exists('palladio_customizer_save_css')) {
	add_action('trx_addons_action_save_options', 'palladio_customizer_save_css');
	function palladio_customizer_save_css() {
		$msg = 	'/* ' . esc_html__("ATTENTION! This file was generated automatically! Don't change it!!!", 'palladio') 
				. "\n----------------------------------------------------------------------- */\n";

		// Save CSS with custom colors and fonts into custom.css
		$css = palladio_customizer_get_css();
		$file = palladio_get_file_dir('css/__colors.css');
		if (file_exists($file)) palladio_fpc($file, $msg . $css );

		// Merge stylesheets
		$list = apply_filters( 'palladio_filter_merge_styles', array() );
		$css = '';
		foreach ($list as $f) {
			$css .= palladio_fgc(palladio_get_file_dir($f));
		}
		if ( $css != '') {
			palladio_fpc( palladio_get_file_dir('css/__styles.css'), $msg . apply_filters( 'palladio_filter_prepare_css', $css, true ) );
		}

		// Merge scripts
		$list = apply_filters( 'palladio_filter_merge_scripts', array(
																	'js/skip-link-focus.js',
																	'js/bideo.js',
																	'js/jquery.tubular.js',
																	'js/_utils.js',
																	'js/_init.js'
																	)
							);
		$js = '';
		foreach ($list as $f) {
			$js .= palladio_fgc(palladio_get_file_dir($f));
		}
		if ( $js != '') {
			palladio_fpc( palladio_get_file_dir('js/__scripts.js'), $msg . apply_filters( 'palladio_filter_prepare_js', $js, true ) );
		}
	}
}


//--------------------------------------------------------------
// Customizer JS and CSS
//--------------------------------------------------------------

// Binds JS listener to make Customizer color_scheme control.
// Passes color scheme data as colorScheme global.
if ( !function_exists( 'palladio_customizer_control_js' ) ) {
	add_action( 'customize_controls_enqueue_scripts', 'palladio_customizer_control_js' );
	function palladio_customizer_control_js() {
		wp_enqueue_style( 'palladio-customizer', palladio_get_file_url('theme-options/theme.customizer.css') );
		wp_enqueue_script( 'palladio-customizer',
									palladio_get_file_url('theme-options/theme.customizer.js'),
									array( 'customize-controls', 'iris', 'underscore', 'wp-util' ), null, true );
		wp_localize_script( 'palladio-customizer', 'palladio_color_schemes', palladio_storage_get('schemes') );
		wp_localize_script( 'palladio-customizer', 'palladio_simple_schemes', palladio_storage_get('schemes_simple') );
		wp_localize_script( 'palladio-customizer', 'palladio_theme_fonts', palladio_storage_get('theme_fonts') );
		wp_localize_script( 'palladio-customizer', 'palladio_customizer_vars', array(
			'max_load_fonts' => palladio_get_theme_setting('max_load_fonts'),
			'msg_refresh' => esc_html__('Refresh', 'palladio'),
			'msg_reset' => esc_html__('Reset', 'palladio'),
			'msg_reset_confirm' => esc_html__('Are you sure you want to reset all Theme Options?', 'palladio'),
			) );
		wp_localize_script( 'palladio-customizer', 'palladio_dependencies', palladio_get_theme_dependencies() );
	}
}

// Binds JS handlers to make the Customizer preview reload changes asynchronously.
if ( !function_exists( 'palladio_customizer_preview_js' ) ) {
	add_action( 'customize_preview_init', 'palladio_customizer_preview_js' );
	function palladio_customizer_preview_js() {
		wp_enqueue_script( 'palladio-customizer-preview',
							palladio_get_file_url('theme-options/theme.customizer.preview.js'), 
							array( 'customize-preview' ), null, true );
	}
}

// Output an Underscore template for generating CSS for the color scheme.
// The template generates the css dynamically for instant display in the Customizer preview.
if ( !function_exists( 'palladio_customizer_css_template' ) ) {
	add_action( 'customize_controls_print_footer_scripts', 'palladio_customizer_css_template' );
	function palladio_customizer_css_template() {
		$colors = array();
		foreach (palladio_get_scheme_colors() as $k=>$v)
			$colors[$k] = '{{ data.'.esc_attr($k).' }}';

		$tmpl_holder = 'script';

		$schemes = array_keys(palladio_get_list_schemes());
		if (count($schemes) > 0) {
			foreach ($schemes as $scheme) {
				echo '<' . esc_attr($tmpl_holder) . ' type="text/html" id="tmpl-palladio-color-scheme-'.esc_attr($scheme).'">'
						. palladio_customizer_get_css( $colors, false, false, $scheme )
					. '</' . esc_attr($tmpl_holder) . '>';
			}
		}


		// Fonts
		$fonts = palladio_get_theme_fonts();
		if (is_array($fonts) && count($fonts) > 0) {
			foreach ($fonts as $tag => $font) {
				$fonts[$tag]['font-family']		= '{{ data["'.$tag.'"]["font-family"] }}';
				$fonts[$tag]['font-size']		= '{{ data["'.$tag.'"]["font-size"] }}';
				$fonts[$tag]['line-height']		= '{{ data["'.$tag.'"]["line-height"] }}';
				$fonts[$tag]['font-weight']		= '{{ data["'.$tag.'"]["font-weight"] }}';
				$fonts[$tag]['font-style']		= '{{ data["'.$tag.'"]["font-style"] }}';
				$fonts[$tag]['text-decoration']	= '{{ data["'.$tag.'"]["text-decoration"] }}';
				$fonts[$tag]['text-transform']	= '{{ data["'.$tag.'"]["text-transform"] }}';
				$fonts[$tag]['letter-spacing']	= '{{ data["'.$tag.'"]["letter-spacing"] }}';
				$fonts[$tag]['margin-top']		= '{{ data["'.$tag.'"]["margin-top"] }}';
				$fonts[$tag]['margin-bottom']	= '{{ data["'.$tag.'"]["margin-bottom"] }}';
			}
			echo '<'.esc_attr(trim($tmpl_holder)).' type="text/html" id="tmpl-palladio-fonts">'
					. trim(palladio_customizer_get_css( false, $fonts, false, false ))
				. '</'.esc_attr(trim($tmpl_holder)).'>';
		}

	}
}


// Add scheme name in each selector in the CSS (priority 100 - after complete css)
if (!function_exists('palladio_customizer_add_scheme_in_css')) {
	add_action( 'palladio_filter_get_css', 'palladio_customizer_add_scheme_in_css', 100, 4 );
	function palladio_customizer_add_scheme_in_css($css, $colors, $fonts, $scheme) {
		if ($colors && !empty($css['colors'])) {
			$rez = '';
			$in_comment = $in_rule = false;
			$allow = true;
			$scheme_class = sprintf('.scheme_%s ', $scheme);
			$self_class = '.scheme_self';
			$self_class_len = strlen($self_class);
			$css_str = str_replace(array('{{', '}}'), array('[[',']]'), $css['colors']);
			for ($i=0; $i<strlen($css_str); $i++) {
				$ch = $css_str[$i];
				if ($in_comment) {
					$rez .= $ch;
					if ($ch=='/' && $css_str[$i-1]=='*') {
						$in_comment = false;
						$allow = !$in_rule;
					}
				} else if ($in_rule) {
					$rez .= $ch;
					if ($ch=='}') {
						$in_rule = false;
						$allow = !$in_comment;
					}
				} else {
					if ($ch=='/' && $css_str[$i+1]=='*') {
						$rez .= $ch;
						$in_comment = true;
					} else if ($ch=='{') {
						$rez .= $ch;
						$in_rule = true;
					} else if ($ch==',') {
						$rez .= $ch;
						$allow = true;
					} else if (strpos(" \t\r\n", $ch)===false) {
						if ($allow) {
							$pos_comma = strpos($css_str, ',', $i+1);
							$pos_bracket = strpos($css_str, '{', $i+1);
							$pos = $pos_comma === false
										? $pos_bracket
										: ($pos_bracket === false
												? $pos_comma
												: min($pos_comma, $pos_bracket)
											);
							$selector = $pos > 0 ? substr($css_str, $i, $pos-$i) : '';
							if (strpos($selector, $self_class) !== false) {
								$rez .= str_replace($self_class, trim($scheme_class), $selector);
								$i += strlen($selector) - 1;
							} else {
								$rez .= $scheme_class . trim($ch);
							}
							$allow = false;
						} else
							$rez .= $ch;
					} else {
						$rez .= $ch;
					}
				}
			}
			$rez = str_replace(array('[[',']]'), array('{{', '}}'), $rez);
			$css['colors'] = $rez;
		}
		return $css;
	}
}

// Load theme options and styles
require_once PALLADIO_THEME_DIR . 'theme-specific/theme.setup.php';
require_once PALLADIO_THEME_DIR . 'theme-specific/theme.styles.php';
require_once PALLADIO_THEME_DIR . 'theme-options/theme.options.php';
require_once PALLADIO_THEME_DIR . 'theme-options/theme.override.php';
?>