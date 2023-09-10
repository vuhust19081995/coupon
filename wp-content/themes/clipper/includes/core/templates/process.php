<?php
/**
 * Template for displaying listing processes.
 *
 * @package Listing\Templates
 * @author  AppThemes
 * @since   Listing 1.0
 */

?>

<div class="app-theme-compat">

<?php
appthemes_before_page_title();
?>

<?php
if ( $step = appthemes_get_checkout() ) {
	?>
	<div class="app-singular-headline">
		<h2>
			<?php
			echo esc_html( $step->get_data( 'title' ) );
			?>
		</h2>
	</div>
<?php
}

appthemes_after_page_title();

appthemes_notices();

appthemes_before_page();
?>

	<div id="overview" class="single-content white-con step">

	<?php
	appthemes_before_page_content();

	appthemes_display_checkout();

	appthemes_after_page_content();
	?>
	</div>

<?php
appthemes_after_page();
?>

</div>
