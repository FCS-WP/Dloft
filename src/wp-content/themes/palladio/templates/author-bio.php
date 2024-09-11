<?php
/**
 * The template to display the Author bio
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */
?>

<div class="author_info author vcard" itemprop="author" itemscope itemtype="//schema.org/Person">

	<div class="author_avatar" itemprop="image">
		<?php 
		$palladio_mult = palladio_get_retina_multiplier();
		echo get_avatar( get_the_author_meta( 'user_email' ), 120*$palladio_mult ); 
		?>
	</div><!-- .author_avatar -->

	<div class="author_description">
		<p class="author_about"><?php esc_html_e('About author', 'palladio'); ?></p>
		<h5 class="author_title" itemprop="name"><span class="fn"><?php echo get_the_author(); ?></span></h5>

		<div class="author_bio" itemprop="description">
			<?php echo wp_kses(wpautop(get_the_author_meta( 'description' )), 'palladio_kses_content'  ); ?>
			<a class="author_link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
				<?php printf( esc_html__( 'View all posts by %s', 'palladio' ), '<span class="author_name">' . esc_html(get_the_author()) . '</span>' ); ?>
			</a>
			<?php do_action('palladio_action_user_meta'); ?>
		</div><!-- .author_bio -->

	</div><!-- .author_description -->

</div><!-- .author_info -->
