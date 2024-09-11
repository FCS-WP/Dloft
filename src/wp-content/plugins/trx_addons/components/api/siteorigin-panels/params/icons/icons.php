<?php
/**
 * Plugin support: SiteOrigin Panels
 *
 * Additional param's type 'icons': dropdown or inline list with images or font icons
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.30
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// Option 'only_socials' => true | false
// Option 'style' => 'icons' | 'images'
// Option 'mode' => 'inline' | 'dropdown'
// Option 'return' => 'slug' | 'full'
class SiteOrigin_Widget_Field_Icons extends SiteOrigin_Widget_Field_Base {

	protected $icons_callback;

	protected function render_field( $value, $instance ) {
		?><div class="trx_addons_sow_param_icons">
			<input type="hidden" name="<?php echo esc_attr( $this->element_name ) ?>" value="<?php echo esc_attr( $value ) ?>"
			       class="siteorigin-widget-icons-icon siteorigin-widget-input <?php echo esc_attr($this->field_options['type']); ?>_field" />
			<?php
			$widget_icons = $this->get_widget_icons();
			if ($this->field_options['mode'] == 'dropdown') {
				?><span class="trx_addons_icon_selector<?php if ($this->field_options['style']=='icons' && !empty($value)) echo ' '.esc_attr($value); ?>"
						title="<?php esc_attr_e('Select icon', 'trx_addons'); ?>"
						data-style="<?php echo ($this->field_options['style']=='images' ? 'images' : 'icons'); ?>"
						<?php
						if ($this->field_options['style']=='images' && !empty($value)) {
							?> style="background-image: url(<?php echo esc_url($this->field_options['return']=='slug' 
																					? $widget_icons[$value] 
																					: $value); ?>);"<?php 
						}
				?>></span><?php
			}
			?><div class="trx_addons_list_icons trx_addons_list_icons_<?php echo esc_attr($this->field_options['mode']); ?>"><?php
			foreach ($widget_icons as $slug=>$icon) {
				?><span class="<?php
								echo esc_attr($this->field_options['style']=='icons' ? $icon : $slug)
									. (($this->field_options['return']=='full' ? $icon : $slug) == $value ? ' trx_addons_active' : '');
								?>"
						title="<?php echo esc_attr($slug); ?>"
						data-icon="<?php echo esc_attr($this->field_options['return']=='full' ? $icon : $slug); ?>"
						<?php if ($this->field_options['style']=='images') echo ' style="background-image: url('.esc_url($icon).');"'; ?>><?php
						if ($this->field_options['mode'] != 'dropdown') echo '<i>'.esc_html($slug).'</i>';
				?></span><?php
			}
			?></div>
		</div><?php
	}

	protected function initialize() {
		if (empty($this->field_options['style']))	$this->field_options['style'] = 'icons';
		if (empty($this->field_options['mode']))	$this->field_options['mode'] = 'dropdown';
		if (empty($this->field_options['return']))	$this->field_options['return'] = 'full';
	}

	// Alphanumeric characters and hyphens.
	protected function sanitize_field_input( $value, $instance ) {
		$sanitized_value = $value;
		if ($this->field_options['style'] == 'icons') {
			if ( preg_match( '/[\w\d]+[\w\d-]*/', $sanitized_value, $sanitized_matches ) )
				$sanitized_value = $sanitized_matches[0];
			else
				$sanitized_value = '';
		}
		$widget_icons = $this->get_widget_icons();
		if ( empty($sanitized_value) 
			|| ( ($this->field_options['style'] == 'icons' || $this->field_options['return']=='slug') && !isset($widget_icons[$sanitized_value]) ) ) {
			$sanitized_value = isset( $this->default ) ? $this->default : '';
		}
		return $sanitized_value;
	}

	private function get_widget_icons() {
		if (!empty($this->field_options['options']) && is_array($this->field_options['options']) && count($this->field_options['options']) > 0)
			$icons = $this->field_options['options'];
		else if (!empty($this->icons_callback))
			$icons = call_user_func( $this->icons_callback );
		else
			$icons = $this->field_options['style'] == 'icons' 
						? trx_addons_array_from_list(trx_addons_get_list_icons()) 
						: trx_addons_get_list_files(!empty($this->field_options['only_socials']) ? 'css/socials' : 'css/icons.png', 'png');
		return $icons;
	}

	public function enqueue_scripts(){
		wp_enqueue_script('trx_addons-sow-icon-field', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'siteorigin-panels/params/icons/icons.js'), array( 'jquery' ), TRX_ADDONS_VERSION);
	}

}
?>