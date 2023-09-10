<?php
/**
 * Implement an optional custom header.
 *
 * See http://codex.wordpress.org/Custom_Headers
 *
 * @package Clipper\Header
 * @author  AppThemes
 * @since   Clipper 1.6
 */


/**
 * Set up the WordPress core custom header arguments and settings.
 *
 * @uses add_theme_support() to register support for 3.4 and up.
 * @uses clpr_header_style() to style front-end.
 * @uses clpr_admin_header_image() to add custom markup to wp-admin form.
 *
 * @return void
 */
function clpr_custom_header_setup() {
	global $clpr_options;

	$text_colors = array(
		'red.css'    => '#850017',
		'blue.css'   => '#0364A7',
		'orange.css' => '#bb3602',
		'green.css'  => '#477400',
		'gray.css'   => '#8f8f8f',
	);

	// determine default text color
	if ( isset( $text_colors[ $clpr_options->stylesheet ] ) ) {
		$default_text_color = $text_colors[ $clpr_options->stylesheet ];
	} else {
		$default_text_color = '#07609e';
	}
	$default_text_color  = trim( $default_text_color, '#' );

	$args = array(
		// Text color and image (empty to use none).
		'default-text-color'     => $default_text_color,
		'header-text'            => true,
		'default-image'          => appthemes_locate_template_uri( 'images/logo.png' ),

		'flex-height'            => true,
		'flex-width'             => true,

		// Set height and width.
		'height'                 => 105,
		'width'                  => 293,

		// Random image rotation off by default.
		'random-default'         => false,

		// Callbacks for styling the header and the admin preview.
		'wp-head-callback'       => 'clpr_header_style',
		'admin-preview-callback' => 'clpr_admin_header_image',
	);

	add_theme_support( 'custom-header', $args );
}
add_action( 'after_setup_theme', 'clpr_custom_header_setup' );


/**
 * Style the header text displayed on the blog.
 * get_header_textcolor() options: fff is default, hide text (returns 'blank'), or any hex value.
 *
 * @return void
 */
function clpr_header_style() {
	$text_color = get_header_textcolor();
	if ( $text_color && false === strpos( $text_color, '#' ) ) {
		$text_color = '#' . $text_color;
	}

	// If we get this far, we have custom styles.
	?>
	<style type="text/css" id="clpr-header-css">
	<?php
		// Has the text been hidden?
		if ( ! display_header_text() ) {
	?>
		.site-branding .description {
			position: absolute;
			clip: rect(1px 1px 1px 1px); /* IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		}
		if ( get_header_image() ) {
	?>
		.site-branding .site-title {
			position: absolute;
			clip: rect(1px 1px 1px 1px); /* IE7 */
			clip: rect(1px, 1px, 1px, 1px);
			display: block;
		}
	<?php
		// If the user has set a custom color for the text, use that.
		} else {
	?>
		.site-branding .site-title a,
		.site-branding .site-title a:hover {
			color: <?php echo $text_color; ?>;
		}
		<?php } ?>

	</style>
	<?php
}


/**
 * Output markup to be displayed on the Appearance > Header admin panel.
 * This callback overrides the default markup displayed there.
 *
 * @return void
 */
function clpr_admin_header_image() {
	$text_color = get_header_textcolor();
	if ( $text_color && false === strpos( $text_color, '#' ) ) {
		$text_color = '#' . $text_color;
	}
	?>
	<div id="headimg">
		<?php
		$nologo = '';
		if ( ! display_header_text() ) {
			$style = ' style="display:none;"';
		} else {
			$style = ' style="color:' . $text_color . ';"';
		}
		?>
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) { ?>
			<img src="<?php echo esc_url( $header_image ); ?>" class="header-image" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" />
		<?php } elseif ( display_header_text() ) {
			$nologo = ' nologo'; ?>
			<h1 class="displaying-header-text">
				<a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php bloginfo( 'name' ); ?>
				</a>
			</h1>
		<?php } ?>
		<?php if ( display_header_text() ) { ?>
			<div class="description displaying-header-text<?php echo $nologo; ?>"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<?php } ?>
	</div>
<?php }

