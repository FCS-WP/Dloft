<?php
/**
 * The template to display the Structured Data Snippets
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.30
 */

// Structured data snippets
if (palladio_is_on(palladio_get_theme_option('seo_snippets'))) {
	?><div class="structured_data_snippets">
		<meta itemprop="headline" content="<?php the_title_attribute(); ?>">
		<meta itemprop="datePublished" content="<?php echo esc_attr(get_the_date('Y-m-d')); ?>">
		<meta itemprop="dateModified" content="<?php echo esc_attr(get_the_modified_date('Y-m-d')); ?>">
		<div itemscope itemprop="publisher" itemtype="https://schema.org/Organization">
			<meta itemprop="name" content="<?php echo esc_attr(get_bloginfo( 'name' )); ?>">
			<meta itemprop="telephone" content="">
			<meta itemprop="address" content="">
			<?php
			$palladio_logo_image = palladio_get_retina_multiplier(2) > 1 
								? palladio_get_theme_option( 'logo_retina' )
								: palladio_get_theme_option( 'logo' );
			if (!empty($palladio_logo_image)) {
				?><meta itemprop="logo" itemtype="https://schema.org/logo" content="<?php echo esc_url($palladio_logo_image); ?>"><?php
			}
			?>
		</div>
	</div>
	<?php
}
?>