<?php
/**
 * Setup the sidebars.
 *
 * @package Clipper
 * @since   2.0.0
 */

/**
 * Register sidebars.
 *
 * @return void
 */
function clpr_register_sidebars() {

	// Home Page.
	register_sidebar( array(
		'name'          => __( 'Homepage - Sidebar', APP_TD ),
		'id'            => 'sidebar_home',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="sidebox-main">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<div class="sidebox-heading"><h2>',
		'after_title'   => '</h2></div>',
	) );

	// Page.
	register_sidebar( array(
		'name'          => __( 'Page - Sidebar', APP_TD ),
		'id'            => 'sidebar_page',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="sidebox-main">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<div class="sidebox-heading"><h2>',
		'after_title'   => '</h2></div>',
	) );

	// Blog.
	register_sidebar( array(
		'name'          => __( 'Blog - Sidebar', APP_TD ),
		'id'            => 'sidebar_blog',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="sidebox-main">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<div class="sidebox-heading"><h2>',
		'after_title'   => '</h2></div>',
	) );

	// Coupon.
	register_sidebar( array(
		'name'          => __( 'Coupon - Sidebar', APP_TD ),
		'id'            => 'sidebar_coupon',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="sidebox-main">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<div class="sidebox-heading"><h2>',
		'after_title'   => '</h2></div>',
	) );

	// Store.
	register_sidebar( array(
		'name'          => __( 'Store - Sidebar', APP_TD ),
		'id'            => 'sidebar_store',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="sidebox-main">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<div class="sidebox-heading"><h2>',
		'after_title'   => '</h2></div>',
	) );

	// Submit Coupon Page.
	register_sidebar( array(
		'name'          => __( 'Submit Coupon - Sidebar', APP_TD ),
		'id'            => 'sidebar_submit',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="sidebox-main">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<div class="sidebox-heading"><h2>',
		'after_title'   => '</h2></div>',
	) );

	// Login Pages
	// @todo Deprecate since we removed sidebar from login pages in 2.0.0.
	register_sidebar( array(
		'name'          => __( 'Login - Sidebar', APP_TD ),
		'id'            => 'sidebar_login',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="sidebox-main">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<div class="sidebox-heading"><h2>',
		'after_title'   => '</h2></div>',
	) );

	// User.
	register_sidebar( array(
		'name'          => __( 'User - Sidebar', APP_TD ),
		'id'            => 'sidebar_user',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="sidebox-main">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<div class="sidebox-heading"><h2>',
		'after_title'   => '</h2></div>',
	) );

	// Footer.
	register_sidebar( array(
		'name'          => __( 'Footer Area One', APP_TD ),
		'id'            => 'sidebar_footer',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget-footer %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	) );

	// Footer.
	register_sidebar( array(
		'name'          => __( 'Footer Area Two', APP_TD ),
		'id'            => 'sidebar_footer_2',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget-footer %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	) );

	// Footer.
	register_sidebar( array(
		'name'          => __( 'Footer Area Three', APP_TD ),
		'id'            => 'sidebar_footer_3',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget-footer %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	) );

	// Footer.
	register_sidebar( array(
		'name'          => __( 'Footer Area Four', APP_TD ),
		'id'            => 'sidebar_footer_4',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget-footer %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4>',
		'after_title'   => '</h4>',
	) );
}

add_action( 'after_setup_theme', 'clpr_register_sidebars' );
