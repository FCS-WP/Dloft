<?php
/**
 * Information about this theme
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.30
 */


// Redirect to the 'About Theme' page after switch theme
if (!function_exists('palladio_about_after_switch_theme')) {
	add_action('after_switch_theme', 'palladio_about_after_switch_theme', 1000);
	function palladio_about_after_switch_theme() {
		update_option('palladio_about_page', 1);
	}
}
if ( !function_exists('palladio_about_after_setup_theme') ) {
	add_action( 'init', 'palladio_about_after_setup_theme', 1000 );
	function palladio_about_after_setup_theme() {
		if (get_option('palladio_about_page') == 1) {
			update_option('palladio_about_page', 0);
			wp_safe_redirect(admin_url().'themes.php?page=palladio_about');
			exit();
		}
	}
}


// Add 'About Theme' item in the Appearance menu
if (!function_exists('palladio_about_add_menu_items')) {
	add_action( 'admin_menu', 'palladio_about_add_menu_items' );
	function palladio_about_add_menu_items() {
		add_theme_page(
			esc_html__('About Palladio', 'palladio'),	//page_title
			esc_html__('About Palladio', 'palladio'),	//menu_title
			'manage_options',						//capability
			'palladio_about',						//menu_slug
			'palladio_about_page_builder'			//callback
		);
	}
}


// Load page-specific scripts and styles
if (!function_exists('palladio_about_enqueue_scripts')) {
	add_action( 'admin_enqueue_scripts', 'palladio_about_enqueue_scripts' );
	function palladio_about_enqueue_scripts() {
		$screen = function_exists('get_current_screen') ? get_current_screen() : false;
		if (is_object($screen) && $screen->id == 'appearance_page_palladio_about') {
			// Scripts
			wp_enqueue_script( 'jquery-ui-tabs', false, array('jquery', 'jquery-ui-core'), null, true );
			if ( palladio_get_file_dir('theme-specific/theme.about/theme.about.js')!='' )
				wp_enqueue_script( 'palladio-about', palladio_get_file_url('theme-specific/theme.about/theme.about.js'), array('jquery'), null, true );
			// Styles
			wp_enqueue_style( 'fontello-style',  palladio_get_file_url('css/font-icons/css/fontello-embedded.css') );
			if ( palladio_get_file_dir('theme-specific/theme.about/theme.about.css')!='' )
				wp_enqueue_style( 'palladio-about',  palladio_get_file_url('theme-specific/theme.about/theme.about.css'), array(), null );
		}
	}
}


// Build 'About Theme' page
if (!function_exists('palladio_about_page_builder')) {
	function palladio_about_page_builder() {
		$theme = wp_get_theme();
		?>
		<div class="palladio_about">
			<div class="palladio_about_header">
				
				<?php if (PALLADIO_THEME_FREE) { ?>
					<a href="<?php echo esc_url(palladio_storage_get('theme_download_url')); ?>"
										   target="_blank"
										   class="palladio_about_pro_link button-primary"><?php
											esc_html_e('Get PRO version', 'palladio');
										?></a>
				<?php } ?>
				<h1 class="palladio_about_title"><?php
					echo sprintf(esc_html__('Welcome to %s %s v.%s', 'palladio'),
								$theme->name,
								PALLADIO_THEME_FREE ? __('Free', 'palladio') : '',
								$theme->version
								);
				?></h1>
				<div class="palladio_about_description">
					<?php
					if (PALLADIO_THEME_FREE) {
						?><p><?php
							echo wp_kses_data(sprintf(__('Now you are using Free version of <a href="%s">%s Pro Theme</a>.', 'palladio'),
														esc_url(palladio_storage_get('theme_download_url')),
														$theme->name
														)
												);
							echo '<br>' . wp_kses_data(sprintf(__('This version is SEO- and Retina-ready. It also has a built-in support for parallax and slider with swipe gestures. %s Free is compatible with many popular plugins, such as %s', 'palladio'),
														$theme->name,
														palladio_about_get_supported_plugins()
														)
												);
						?></p>
						<p><?php
							echo wp_kses_data(sprintf(__('We hope you have a great acquaintance with our themes. If you are looking for a fully functional website, you can get the <a href="%s">Pro Version here</a>', 'palladio'),
														esc_url(palladio_storage_get('theme_download_url'))
														)
												);
						?></p><?php
					} else {
						?><p><?php
							echo wp_kses_data(sprintf(__('%s is a Premium WordPress theme. It has a built-in support for parallax, slider with swipe gestures, and is SEO- and Retina-ready', 'palladio'),
														$theme->name
														)
												);
						?></p>
						<p><?php
							echo wp_kses_data(sprintf(__('The Premium Theme is compatible with many popular plugins, such as %s', 'palladio'),
														palladio_about_get_supported_plugins()
														)
												);
						?></p><?php
					}
					?>
				</div>
			</div>
			<div id="palladio_about_tabs" class="palladio_tabs palladio_about_tabs">
				<ul>
					<li><a href="#palladio_about_section_start"><?php esc_html_e('Getting started', 'palladio'); ?></a></li>
					<li><a href="#palladio_about_section_actions"><?php esc_html_e('Recommended actions', 'palladio'); ?></a></li>
					<?php if (PALLADIO_THEME_FREE) { ?>
						<li><a href="#palladio_about_section_pro"><?php esc_html_e('Free vs PRO', 'palladio'); ?></a></li>
					<?php } ?>
				</ul>
				<div id="palladio_about_section_start" class="palladio_tabs_section palladio_about_section"><?php
				
					// Install required plugins
					if (!palladio_exists_trx_addons()) {
						?><div class="palladio_about_block"><div class="palladio_about_block_inner">
							<h2 class="palladio_about_block_title">
								<i class="dashicons dashicons-admin-plugins"></i>
								<?php esc_html_e('ThemeREX Addons', 'palladio'); ?>
							</h2>
							<div class="palladio_about_block_description"><?php
								echo esc_html(sprintf(__('It is highly recommended that you install the companion plugin "ThemeREX Addons" to have access to the layouts builder, awesome shortcodes, team and testimonials, services and slider, and many other features ...', 'palladio'), $theme->name));
							?></div>
							<a href="<?php echo esc_url(admin_url().'themes.php?page=tgmpa-install-plugins'); ?>"
							   class="palladio_about_block_link button-primary"><?php
								esc_html_e('Install plugin', 'palladio');
							?></a>
						</div></div><?php
					}
					
					// Install recommended plugins
					?><div class="palladio_about_block"><div class="palladio_about_block_inner">
						<h2 class="palladio_about_block_title">
							<i class="dashicons dashicons-admin-plugins"></i>
							<?php esc_html_e('Recommended plugins', 'palladio'); ?>
						</h2>
						<div class="palladio_about_block_description"><?php
							echo esc_html(sprintf(__('Theme %s is compatible with a large number of popular plugins. You can install only those that are going to use in the near future.', 'palladio'), $theme->name));
						?></div>
						<a href="<?php echo esc_url(admin_url().'themes.php?page=tgmpa-install-plugins'); ?>"
						   class="palladio_about_block_link button-primary"><?php
							esc_html_e('Install plugins', 'palladio');
						?></a>
					</div></div><?php
					
					// Customizer or Theme Options
					?><div class="palladio_about_block"><div class="palladio_about_block_inner">
						<h2 class="palladio_about_block_title">
							<i class="dashicons dashicons-admin-appearance"></i>
							<?php esc_html_e('Setup Theme options', 'palladio'); ?>
						</h2>
						<div class="palladio_about_block_description"><?php
							esc_html_e('Using the WordPress Customizer you can easily customize every aspect of the theme. If you want to use the standard theme settings page - open Theme Options and follow the same steps there.', 'palladio');
						?></div>
						<a href="<?php echo esc_url(admin_url().'customize.php'); ?>"
						   class="palladio_about_block_link button-primary"><?php
							esc_html_e('Customizer', 'palladio');
						?></a>
						<?php esc_html_e('or', 'palladio'); ?>
						<a href="<?php echo esc_url(admin_url().'themes.php?page=theme_options'); ?>"
						   class="palladio_about_block_link button"><?php
							esc_html_e('Theme Options', 'palladio');
						?></a>
					</div></div><?php
					
					// Documentation
					?><div class="palladio_about_block"><div class="palladio_about_block_inner">
						<h2 class="palladio_about_block_title">
							<i class="dashicons dashicons-book"></i>
							<?php esc_html_e('Read full documentation', 'palladio');	?>
						</h2>
						<div class="palladio_about_block_description"><?php
							echo esc_html(sprintf(__('Need more details? Please check our full online documentation for detailed information on how to use %s.', 'palladio'), $theme->name));
						?></div>
						<a href="<?php echo esc_url(palladio_storage_get('theme_doc_url')); ?>"
						   target="_blank"
						   class="palladio_about_block_link button-primary"><?php
							esc_html_e('Documentation', 'palladio');
						?></a>
					</div></div><?php
					
					// Support
					if (!PALLADIO_THEME_FREE) {
						?><div class="palladio_about_block"><div class="palladio_about_block_inner">
							<h2 class="palladio_about_block_title">
								<i class="dashicons dashicons-sos"></i>
								<?php esc_html_e('Support', 'palladio'); ?>
							</h2>
							<div class="palladio_about_block_description"><?php
								echo esc_html(sprintf(__('We want to make sure you have the best experience using %s and that is why we gathered here all the necessary informations for you.', 'palladio'), $theme->name));
							?></div>
							<a href="<?php echo esc_url(palladio_storage_get('theme_support_url')); ?>"
							   target="_blank"
							   class="palladio_about_block_link button-primary"><?php
								esc_html_e('Support', 'palladio');
							?></a>
						</div></div><?php
					}
					
					// Online Demo
					?><div class="palladio_about_block"><div class="palladio_about_block_inner">
						<h2 class="palladio_about_block_title">
							<i class="dashicons dashicons-images-alt2"></i>
							<?php esc_html_e('On-line demo', 'palladio'); ?>
						</h2>
						<div class="palladio_about_block_description"><?php
							echo esc_html(sprintf(__('Visit the Demo Version of %s to check out all the features it has', 'palladio'), $theme->name));
						?></div>
						<a href="<?php echo esc_url(palladio_storage_get('theme_demo_url')); ?>"
						   target="_blank"
						   class="palladio_about_block_link button-primary"><?php
							esc_html_e('View demo', 'palladio');
						?></a>
					</div></div>
					
				</div>



				<div id="palladio_about_section_actions" class="palladio_tabs_section palladio_about_section"><?php
				
					// Install required plugins
					if (!palladio_exists_trx_addons()) {
						?><div class="palladio_about_block"><div class="palladio_about_block_inner">
							<h2 class="palladio_about_block_title">
								<i class="dashicons dashicons-admin-plugins"></i>
								<?php esc_html_e('ThemeREX Addons', 'palladio'); ?>
							</h2>
							<div class="palladio_about_block_description"><?php
								echo esc_html(sprintf(__('It is highly recommended that you install the companion plugin "ThemeREX Addons" to have access to the layouts builder, awesome shortcodes, team and testimonials, services and slider, and many other features ...', 'palladio'), $theme->name));
							?></div>
							<a href="<?php echo esc_url(admin_url().'themes.php?page=tgmpa-install-plugins'); ?>"
							   class="palladio_about_block_link button-primary"><?php
								esc_html_e('Install plugin', 'palladio');
							?></a>
						</div></div><?php
					}
					
					// Install recommended plugins
					?><div class="palladio_about_block"><div class="palladio_about_block_inner">
						<h2 class="palladio_about_block_title">
							<i class="dashicons dashicons-admin-plugins"></i>
							<?php esc_html_e('Recommended plugins', 'palladio'); ?>
						</h2>
						<div class="palladio_about_block_description"><?php
							echo esc_html(sprintf(__('Theme %s is compatible with a large number of popular plugins. You can install only those that are going to use in the near future.', 'palladio'), $theme->name));
						?></div>
						<a href="<?php echo esc_url(admin_url().'themes.php?page=tgmpa-install-plugins'); ?>"
						   class="palladio_about_block_link button-primary"><?php
							esc_html_e('Install plugins', 'palladio');
						?></a>
					</div></div><?php
					
					// Customizer or Theme Options
					?><div class="palladio_about_block"><div class="palladio_about_block_inner">
						<h2 class="palladio_about_block_title">
							<i class="dashicons dashicons-admin-appearance"></i>
							<?php esc_html_e('Setup Theme options', 'palladio'); ?>
						</h2>
						<div class="palladio_about_block_description"><?php
							esc_html_e('Using the WordPress Customizer you can easily customize every aspect of the theme. If you want to use the standard theme settings page - open Theme Options and follow the same steps there.', 'palladio');
						?></div>
						<a href="<?php echo esc_url(admin_url().'customize.php'); ?>"
						   target="_blank"
						   class="palladio_about_block_link button-primary"><?php
							esc_html_e('Customizer', 'palladio');
						?></a>
						<?php esc_html_e('or', 'palladio'); ?>
						<a href="<?php echo esc_url(admin_url().'themes.php?page=theme_options'); ?>"
						   class="palladio_about_block_link button"><?php
							esc_html_e('Theme Options', 'palladio');
						?></a>
					</div></div>
					
				</div>



				<?php if (PALLADIO_THEME_FREE) { ?>
					<div id="palladio_about_section_pro" class="palladio_tabs_section palladio_about_section">
						<table class="palladio_about_table" cellpadding="0" cellspacing="0" border="0">
							<thead>
								<tr>
									<td class="palladio_about_table_info">&nbsp;</td>
									<td class="palladio_about_table_check"><?php echo esc_html(sprintf(__('%s Lite', 'palladio'), $theme->name)); ?></td>
									<td class="palladio_about_table_check"><?php echo esc_html(sprintf(__('%s PRO', 'palladio'), $theme->name)); ?></td>
								</tr>
							</thead>
							<tbody>
	
	
								<?php
								// Responsive layouts
								?>
								<tr>
									<td class="palladio_about_table_info">
										<h2 class="palladio_about_table_info_title">
											<?php esc_html_e('Mobile friendly', 'palladio'); ?>
										</h2>
										<div class="palladio_about_table_info_description"><?php
											esc_html_e('Responsive layout. Looks great on any device.', 'palladio');
										?></div>
									</td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
								</tr>
	
								<?php
								// Built-in slider
								?>
								<tr>
									<td class="palladio_about_table_info">
										<h2 class="palladio_about_table_info_title">
											<?php esc_html_e('Built-in posts slider', 'palladio'); ?>
										</h2>
										<div class="palladio_about_table_info_description"><?php
											esc_html_e('Allows you to add beautiful slides using the built-in shortcode/widget "Slider" with swipe gestures support.', 'palladio');
										?></div>
									</td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
								</tr>
	
								<?php
								// Revolution slider
								if (palladio_storage_isset('required_plugins', 'revslider')) {
								?>
								<tr>
									<td class="palladio_about_table_info">
										<h2 class="palladio_about_table_info_title">
											<?php esc_html_e('Revolution Slider Compatibility', 'palladio'); ?>
										</h2>
										<div class="palladio_about_table_info_description"><?php
											esc_html_e('Our built-in shortcode/widget "Slider" is able to work not only with posts, but also with slides created  in "Revolution Slider".', 'palladio');
										?></div>
									</td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
								</tr>
								<?php } ?>
	
								<?php
								// SiteOrigin Panels
								if (palladio_storage_isset('required_plugins', 'siteorigin-panels')) {
								?>
								<tr>
									<td class="palladio_about_table_info">
										<h2 class="palladio_about_table_info_title">
											<?php esc_html_e('Free PageBuilder', 'palladio'); ?>
										</h2>
										<div class="palladio_about_table_info_description"><?php
											esc_html_e('Full integration with a nice free page builder "SiteOrigin Panels".', 'palladio');
										?></div>
									</td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
								</tr>
								<tr>
									<td class="palladio_about_table_info">
										<h2 class="palladio_about_table_info_title">
											<?php esc_html_e('Additional widgets pack', 'palladio'); ?>
										</h2>
										<div class="palladio_about_table_info_description"><?php
											esc_html_e('A number of useful shortcodes and widgets to create beautiful homepages and other sections of your website.', 'palladio');
										?></div>
									</td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-no"></i></td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
								</tr>
								<?php } ?>
	
								<?php
								// WPBakery Page Builder
								?>
								<tr>
									<td class="palladio_about_table_info">
										<h2 class="palladio_about_table_info_title">
											<?php esc_html_e('WPBakery Page Builder', 'palladio'); ?>
										</h2>
										<div class="palladio_about_table_info_description"><?php
											esc_html_e('Full integration with a very popular page builder "WPBakery Page Builder". A number of useful shortcodes and widgets to create beautiful homepages and other sections of your website.', 'palladio');
										?></div>
									</td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-no"></i></td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
								</tr>
	
								<?php
								// Layouts builder
								?>
								<tr>
									<td class="palladio_about_table_info">
										<h2 class="palladio_about_table_info_title">
											<?php esc_html_e('Headers and Footers builder', 'palladio'); ?>
										</h2>
										<div class="palladio_about_table_info_description"><?php
											esc_html_e('Powerful visual builder of headers and footers! No manual code editing - use all the advantages of drag-and-drop technology.', 'palladio');
										?></div>
									</td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-no"></i></td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
								</tr>
	
								<?php
								// WooCommerce
								if (palladio_storage_isset('required_plugins', 'woocommerce')) {
								?>
								<tr>
									<td class="palladio_about_table_info">
										<h2 class="palladio_about_table_info_title">
											<?php esc_html_e('WooCommerce Compatibility', 'palladio'); ?>
										</h2>
										<div class="palladio_about_table_info_description"><?php
											esc_html_e('Ready for e-commerce. You can build an online store with this theme.', 'palladio');
										?></div>
									</td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
								</tr>
								<?php } ?>
	
								<?php
								// Easy Digital Downloads
								if (palladio_storage_isset('required_plugins', 'easy-digital-downloads')) {
								?>
								<tr>
									<td class="palladio_about_table_info">
										<h2 class="palladio_about_table_info_title">
											<?php esc_html_e('Easy Digital Downloads Compatibility', 'palladio'); ?>
										</h2>
										<div class="palladio_about_table_info_description"><?php
											esc_html_e('Ready for digital e-commerce. You can build an online digital store with this theme.', 'palladio');
										?></div>
									</td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-no"></i></td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
								</tr>
								<?php } ?>
	
								<?php
								// Other plugins
								?>
								<tr>
									<td class="palladio_about_table_info">
										<h2 class="palladio_about_table_info_title">
											<?php esc_html_e('Many other popular plugins compatibility', 'palladio'); ?>
										</h2>
										<div class="palladio_about_table_info_description"><?php
											esc_html_e('PRO version is compatible (was tested and has built-in support) with many popular plugins.', 'palladio');
										?></div>
									</td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-no"></i></td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
								</tr>
	
								<?php
								// Support
								?>
								<tr>
									<td class="palladio_about_table_info">
										<h2 class="palladio_about_table_info_title">
											<?php esc_html_e('Support', 'palladio'); ?>
										</h2>
										<div class="palladio_about_table_info_description"><?php
											esc_html_e('Our premium support is going to take care of any problems, in case there will be any of course.', 'palladio');
										?></div>
									</td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-no"></i></td>
									<td class="palladio_about_table_check"><i class="dashicons dashicons-yes"></i></td>
								</tr>
	
								<?php
								// Get PRO version
								?>
								<tr>
									<td class="palladio_about_table_info">&nbsp;</td>
									<td class="palladio_about_table_check" colspan="2">
										<a href="<?php echo esc_url(palladio_storage_get('theme_download_url')); ?>"
										   target="_blank"
										   class="palladio_about_block_link palladio_about_pro_link button-primary"><?php
											esc_html_e('Get PRO version', 'palladio');
										?></a>
									</td>
								</tr>
	
							</tbody>
						</table>
					</div>
				<?php } ?>
				
			</div>
		</div>
		<?php
	}
}


// Utils
//------------------------------------

// Return supported plugin's names
if (!function_exists('palladio_about_get_supported_plugins')) {
	function palladio_about_get_supported_plugins() {
		return '"' . join('", "', array_values(palladio_storage_get('required_plugins'))) . '"';
	}
}
?>