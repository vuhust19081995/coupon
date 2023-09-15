<?php
/**
 * The template for displaying the header branding & search area
 *
 * @package Clipper
 * @since 2.0.0
 */
?>

<div class="row header-branding-wrap">

	<div class="medium-6 columns">

		<div class="site-branding">

			<?php
			if ( get_header_image() ) {
				?>
				<!-- <a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img src="<?php header_image(); ?>" class="header-logo" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
				</a> -->
				<?php
			}
			if ( is_front_page() ) {
				?>

				<h1 class="h1 site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
						<?php bloginfo( 'name' ); ?>
					</a>
				</h1>

			<?php } else { ?>

				<span class="h1 site-title">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
						<?php bloginfo( 'name' ); ?>
					</a>
				</span>

			<?php } ?>

			<div class="description"><?php bloginfo( 'description' ); ?></div>

		</div><!-- .site-branding -->

	</div>

	<div class="medium-6 columns top-navigation-header">

		<?php get_template_part( 'searchform-coupon' ); ?>

	</div><!-- .columns -->

</div><!-- .row -->
