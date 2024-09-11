<?php
/**
 * The template to display Admin notices
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.1
 */
 
$palladio_theme_obj = wp_get_theme();
?>
<div class="update-nag" id="palladio_admin_notice">
	<h3 class="palladio_notice_title"><?php echo sprintf(esc_html__('Welcome to %s v.%s', 'palladio'), $palladio_theme_obj->name, $palladio_theme_obj->version); ?></h3>
	<?php
	if (!palladio_exists_trx_addons()) {
		?><p><?php echo wp_kses_data(__('<b>Attention!</b> Plugin "ThemeREX Addons is required! Please, install and activate it!', 'palladio')); ?></p><?php
	}
	?><p>
		<a href="<?php echo esc_url(admin_url().'themes.php?page=palladio_about'); ?>" class="button-primary"><i class="dashicons dashicons-nametag"></i> <?php echo sprintf(esc_html__('About %s', 'palladio'), $palladio_theme_obj->name); ?></a>
		<?php
		if (palladio_get_value_gp('page')!='tgmpa-install-plugins') {
			?>
			<a href="<?php echo esc_url(admin_url().'themes.php?page=tgmpa-install-plugins'); ?>" class="button-primary"><i class="dashicons dashicons-admin-plugins"></i> <?php esc_html_e('Install plugins', 'palladio'); ?></a>
			<?php
		}
		if (function_exists('palladio_exists_trx_addons') && palladio_exists_trx_addons() && class_exists('trx_addons_demo_data_importer')) {
			?>
			<a href="<?php echo esc_url(admin_url().'themes.php?page=trx_importer'); ?>" class="button-primary"><i class="dashicons dashicons-download"></i> <?php esc_html_e('One Click Demo Data', 'palladio'); ?></a>
			<?php
		}
		?>
        <a href="<?php echo esc_url(admin_url().'themes.php?page=theme_options'); ?>" class="button-primary"><i class="dashicons dashicons-admin-appearance"></i> <?php esc_html_e('Theme Options', 'palladio'); ?></a>
		<span> <?php esc_html_e('or', 'palladio'); ?> </span>
        <a href="<?php echo esc_url(admin_url().'customize.php'); ?>" class="button-primary"><i class="dashicons dashicons-admin-appearance"></i> <?php esc_html_e('Theme Customizer', 'palladio'); ?></a>
        <a href="#" class="button palladio_hide_notice"><i class="dashicons dashicons-dismiss"></i> <?php esc_html_e('Hide Notice', 'palladio'); ?></a>
	</p>
</div>