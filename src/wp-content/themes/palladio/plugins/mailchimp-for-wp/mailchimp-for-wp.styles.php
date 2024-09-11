<?php
// Add plugin-specific colors and fonts to the custom CSS
if (!function_exists('palladio_mailchimp_get_css')) {
	add_filter('palladio_filter_get_css', 'palladio_mailchimp_get_css', 10, 4);
	function palladio_mailchimp_get_css($css, $colors, $fonts, $scheme='') {
		
		if (isset($css['fonts']) && $fonts) {
			$css['fonts'] .= <<<CSS

CSS;
		
			
			$rad = palladio_get_border_radius();
			$css['fonts'] .= <<<CSS

.mc4wp-form .mc4wp-form-fields input[type="email"],
.mc4wp-form .mc4wp-form-fields input[type="submit"] {
	-webkit-border-radius: {$rad};
	    -ms-border-radius: {$rad};
			border-radius: {$rad};
}

CSS;
		}

		
		if (isset($css['colors']) && $colors) {
			$css['colors'] .= <<<CSS

.mc4wp-form input[type="email"] {
	background-color: {$colors['input_bg_color']};
	border-color: {$colors['input_bd_color']};
	color: {$colors['input_text']};
}
.mc4wp-form .mc4wp-alert {
	background-color: {$colors['input_bd_color']};
	border-color: {$colors['input_bd_color']};
	color: {$colors['input_text']};
}
.mailchimp_form button{
	background-color: {$colors['bg_color']};
	border-color: {$colors['text_dark']};
	color: {$colors['text_dark']};
}
.mailchimp_form button:hover{
	background-color: {$colors['text_dark']};
	border-color: {$colors['text_dark']};
	color: {$colors['inverse_text']};
}

CSS;
		}

		return $css;
	}
}
?>