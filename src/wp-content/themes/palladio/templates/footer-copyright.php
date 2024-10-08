<?php
/**
 * The template to display the copyright info in the footer
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.10
 */

// Copyright area
$palladio_footer_scheme =  palladio_is_inherit(palladio_get_theme_option('footer_scheme')) ? palladio_get_theme_option('color_scheme') : palladio_get_theme_option('footer_scheme');
$palladio_copyright_scheme = palladio_is_inherit(palladio_get_theme_option('copyright_scheme')) ? $palladio_footer_scheme : palladio_get_theme_option('copyright_scheme');
?> 
<div class="footer_copyright_wrap scheme_<?php echo esc_attr($palladio_copyright_scheme); ?>">
	<div class="footer_copyright_inner">
		<div class="content_wrap">
			<div class="copyright_text"><?php
				// Replace {{...}} and [[...]] on the <i>...</i> and <b>...</b>
				$palladio_copyright = palladio_prepare_macros(palladio_get_theme_option('copyright'));
				if (!empty($palladio_copyright)) {
					// Replace {date_format} on the current date in the specified format
					if (preg_match("/(\\{[\\w\\d\\\\\\-\\:]*\\})/", $palladio_copyright, $palladio_matches)) {
						$palladio_copyright = str_replace($palladio_matches[1], date(str_replace(array('{', '}'), '', $palladio_matches[1])), $palladio_copyright);
					}
					// Display copyright
					echo wp_kses_data(nl2br($palladio_copyright));
				}
			?></div>
		</div>
	</div>
</div>
