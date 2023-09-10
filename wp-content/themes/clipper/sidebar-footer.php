<?php
/**
 * Footer widget areas.
 *
 * @package Clipper\Templates
 * @since 2.0.0
 */

// If none of the sidebars have widgets, then let's exit.
if ( ! is_active_sidebar( 'sidebar_footer' ) && ! is_active_sidebar( 'sidebar_footer_2' )
	&& ! is_active_sidebar( 'sidebar_footer_3' ) && ! is_active_sidebar( 'sidebar_footer_4' ) ) {
	return;
}

$footer_sidebar_class = clpr_footer_sidebar_class();
?>

<?php if ( is_active_sidebar( 'sidebar_footer' ) ) : ?>
	<div id="footer-widget-first" class="widget-area <?php echo esc_attr( $footer_sidebar_class ); ?> columns" role="complementary">
		<?php dynamic_sidebar( 'sidebar_footer' ); ?>
	</div> <!-- .widget-area -->
<?php endif; ?>

<?php if ( is_active_sidebar( 'sidebar_footer_2' ) ) : ?>
	<div id="footer-widget-second" class="widget-area <?php echo esc_attr( $footer_sidebar_class ); ?> columns" role="complementary">
		<?php dynamic_sidebar( 'sidebar_footer_2' ); ?>
	</div> <!-- .widget-area -->
<?php endif; ?>

<?php if ( is_active_sidebar( 'sidebar_footer_3' ) ) : ?>
	<div id="footer-widget-third" class="widget-area <?php echo esc_attr( $footer_sidebar_class ); ?> columns" role="complementary">
		<?php dynamic_sidebar( 'sidebar_footer_3' ); ?>
	</div> <!-- .widget-area -->
<?php endif; ?>

<?php if ( is_active_sidebar( 'sidebar_footer_4' ) ) : ?>
	<div id="footer-widget-fourth" class="widget-area <?php echo esc_attr( $footer_sidebar_class ); ?> columns" role="complementary">
		<?php dynamic_sidebar( 'sidebar_footer_4' ); ?>
	</div> <!-- .widget-area -->
<?php endif; ?>
