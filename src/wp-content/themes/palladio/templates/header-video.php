<?php
/**
 * The template to display the background video in the header
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.14
 */
$palladio_header_video = palladio_get_header_video();
$palladio_embed_video = '';
if (!empty($palladio_header_video) && !palladio_is_from_uploads($palladio_header_video)) {
	if (palladio_is_youtube_url($palladio_header_video) && preg_match('/[=\/]([^=\/]*)$/', $palladio_header_video, $matches) && !empty($matches[1])) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr($matches[1]); ?>"></div><?php
	} else {
		global $wp_embed;
		if (false && is_object($wp_embed)) {
			$palladio_embed_video = do_shortcode($wp_embed->run_shortcode( '[embed]' . trim($palladio_header_video) . '[/embed]' ));
			$palladio_embed_video = palladio_make_video_autoplay($palladio_embed_video);
		} else {
			$palladio_header_video = str_replace('/watch?v=', '/embed/', $palladio_header_video);
			$palladio_header_video = palladio_add_to_url($palladio_header_video, array(
				'feature' => 'oembed',
				'controls' => 0,
				'autoplay' => 1,
				'showinfo' => 0,
				'modestbranding' => 1,
				'wmode' => 'transparent',
				'enablejsapi' => 1,
				'origin' => home_url(),
				'widgetid' => 1
			));
			$palladio_embed_video = '<iframe src="' . esc_url($palladio_header_video) . '" width="1170" height="658" allowfullscreen="0" frameborder="0"></iframe>';
		}
		?><div id="background_video"><?php palladio_show_layout($palladio_embed_video); ?></div><?php
	}
}
?>