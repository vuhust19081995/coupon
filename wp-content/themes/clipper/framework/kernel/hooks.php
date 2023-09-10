<?php
/**
 * Add and initiate the AppThemes hooks
 *
 * @uses add_action() calls to trigger the hooks.
 * @package Framework\Hooks
 *
 * DO NOT UPDATE WITHOUT UPDATING ALL OTHER THEMES!
 * This is a shared file so changes need to be propagated to insure sync
 */

/**
 * Called after theme files are included but before theme is loaded
 */
function appthemes_init() {
	/**
	 * This hook runs after all theme files have been included, but before
	 * anything else is loaded into the theme.
	 */
	do_action( 'appthemes_init' );
}


/**
 * Called in header.php after the opening body tag
 */
function appthemes_before() {
	/**
	 * Runs in the header after the opening html body tag and before any divs.
	 */
	do_action( 'appthemes_before' );
}


/**
 * Called in footer.php before the closing body tag
 */
function appthemes_after() {
	/**
	 * Runs in the footer.php before the closing html body tag and after all divs.
	 */
	do_action( 'appthemes_after' );
}


/**
 * Called in header.php before the theme header hook loads
 */
function appthemes_before_header() {
	/**
	 * Runs in the header.php after the opening div tag and before the main
	 * header section is called at the top of the theme.
	 */
	do_action( 'appthemes_before_header' );
}


/**
 * Called in header.php and loads the theme header
 */
function appthemes_header() {
	/**
	 * Runs in the header.php and loads in the theme header code found in /includes/theme-header.php.
	 */
	do_action( 'appthemes_header' );
}


/**
 * Called in header.php after the theme header hook loads
 */
function appthemes_after_header() {
	/**
	 * Runs in the header.php file and loads after the theme header code.
	 * Usage This hook provides no parameters.
	 */
	do_action( 'appthemes_after_header' );
}


/**
 * Page action hooks
 *
 */


/**
 * called in page.php before the loop executes
 *
 */
function appthemes_before_page_loop() {
	do_action( 'appthemes_before_page_loop' );
}


/**
 * called in page.php before the page post section
 *
 */
function appthemes_before_page() {
	do_action( 'appthemes_before_page' );
}


/**
 * called in page.php before the page post title tag
 *
 */
function appthemes_before_page_title() {
	do_action( 'appthemes_before_page_title' );
}


/**
 * called in page.php after the page post title tag
 *
 */
function appthemes_after_page_title() {
	do_action( 'appthemes_after_page_title' );
}


/**
 * called in page.php before the page post content
 *
 */
function appthemes_before_page_content() {
	do_action( 'appthemes_before_page_content' );
}


/**
 * called in page.php after the page post content
 *
 */
function appthemes_after_page_content() {
	do_action( 'appthemes_after_page_content' );
}


/**
 * called in page.php after the page post section
 *
 */
function appthemes_after_page() {
	do_action( 'appthemes_after_page' );
}


/**
 * called in page page.php after the loop endwhile
 *
 */
function appthemes_after_page_endwhile() {
	do_action( 'appthemes_after_page_endwhile' );
}


/**
 * called in page page.php after the loop else
 *
 */
function appthemes_page_loop_else() {
	do_action( 'appthemes_page_loop_else' );
}


/**
 * called in page page.php after the loop executes
 *
 */
function appthemes_after_page_loop() {
	do_action( 'appthemes_after_page_loop' );
}


/**
 * called in page comments-page.php before the comments list block
 *
 */
function appthemes_before_page_comments() {
	do_action( 'appthemes_before_page_comments' );
}


/**
 * called in page comments-page.php in the ol block
 *
 */
function appthemes_list_page_comments() {
	do_action( 'appthemes_list_page_comments' );
}


/**
 * called in page comments-page.php after the comments list block
 *
 */
function appthemes_after_page_comments() {
	do_action( 'appthemes_after_page_comments' );
}


/**
 * called in page comments.php before the pings list block
 *
 */
function appthemes_before_page_pings() {
	do_action( 'appthemes_before_page_pings' );
}


/**
 * called in page comments.php in the ol block
 *
 */
function appthemes_list_page_pings() {
	do_action( 'appthemes_list_page_pings' );
}


/**
 * called in page comments.php after the pings list block
 *
 */
function appthemes_after_page_pings() {
	do_action( 'appthemes_after_page_pings' );
}


/**
 * called in page comments-page.php before the comments respond block
 *
 */
function appthemes_before_page_respond() {
	do_action( 'appthemes_before_page_respond' );
}


/**
 * called in page comments-page.php after the comments respond block
 *
 */
function appthemes_after_page_respond() {
	do_action( 'appthemes_after_page_respond' );
}


/**
 * called in page comments-page.php before the comments form block
 *
 */
function appthemes_before_page_comments_form() {
	do_action( 'appthemes_before_page_comments_form' );
}


/**
 * called in page comments-page.php to include the comments form block
 *
 */
function appthemes_page_comments_form() {
	do_action( 'appthemes_page_comments_form' );
}


/**
 * called in page comments-page.php after the comments form block
 *
 */
function appthemes_after_page_comments_form() {
	do_action( 'appthemes_after_page_comments_form' );
}



/**
 * Blog action hooks
 *
 */


/**
 * called in loop.php before the loop executes
 *
 */
function appthemes_before_blog_loop() {
	do_action( 'appthemes_before_blog_loop' );
}


/**
 * called in loop.php before the blog post section
 *
 */
function appthemes_before_blog_post() {
	do_action( 'appthemes_before_blog_post' );
}


/**
 * called in loop.php before the blog post title tag
 *
 */
function appthemes_before_blog_post_title() {
	do_action( 'appthemes_before_blog_post_title' );
}


/**
 * called in loop.php after the blog post title tag
 *
 */
function appthemes_after_blog_post_title() {
	do_action( 'appthemes_after_blog_post_title' );
}


/**
 * called in loop.php before the blog post content
 *
 */
function appthemes_before_blog_post_content() {
	do_action( 'appthemes_before_blog_post_content' );
}


/**
 * called in loop.php after the blog post content
 *
 */
function appthemes_after_blog_post_content() {
	do_action( 'appthemes_after_blog_post_content' );
}


/**
 * called in loop.php after the blog post section
 *
 */
function appthemes_after_blog_post() {
	do_action( 'appthemes_after_blog_post' );
}


/**
 * called in blog loop.php after the loop endwhile
 *
 */
function appthemes_after_blog_endwhile() {
	do_action( 'appthemes_after_blog_endwhile' );
}


/**
 * called in blog loop.php after the loop else
 *
 */
function appthemes_blog_loop_else() {
	do_action( 'appthemes_blog_loop_else' );
}


/**
 * called in blog loop.php after the loop executes
 *
 */
function appthemes_after_blog_loop() {
	do_action( 'appthemes_after_blog_loop' );
}


/**
 * called in blog comments-blog.php before the comments list block
 *
 */
function appthemes_before_blog_comments() {
	do_action( 'appthemes_before_blog_comments' );
}


/**
 * called in blog comments.php in the ol block
 *
 */
function appthemes_list_blog_comments() {
	do_action( 'appthemes_list_blog_comments' );
}


/**
 * called in blog comments-blog.php after the comments list block
 *
 */
function appthemes_after_blog_comments() {
	do_action( 'appthemes_after_blog_comments' );
}


/**
 * called in blog comments.php before the pings list block
 *
 */
function appthemes_before_blog_pings() {
	do_action( 'appthemes_before_blog_pings' );
}


/**
 * called in blog comments.php in the ol block
 *
 */
function appthemes_list_blog_pings() {
	do_action( 'appthemes_list_blog_pings' );
}


/**
 * called in blog comments.php after the pings list block
 *
 */
function appthemes_after_blog_pings() {
	do_action( 'appthemes_after_blog_pings' );
}


/**
 * called in blog comments-blog.php before the comments respond block
 *
 */
function appthemes_before_blog_respond() {
	do_action( 'appthemes_before_blog_respond' );
}


/**
 * called in blog comments-blog.php after the comments respond block
 *
 */
function appthemes_after_blog_respond() {
	do_action( 'appthemes_after_blog_respond' );
}


/**
 * called in blog comments-blog.php before the comments form block
 *
 */
function appthemes_before_blog_comments_form() {
	do_action( 'appthemes_before_blog_comments_form' );
}


/**
 * called in blog comments-blog.php to include the comments form block
 *
 */
function appthemes_blog_comments_form() {
	do_action( 'appthemes_blog_comments_form' );
}


/**
 * called in blog comments-blog.php after the comments form block
 *
 */
function appthemes_after_blog_comments_form() {
	do_action( 'appthemes_after_blog_comments_form' );
}



/**
 * Custom post type action hooks
 */


/**
 * Called in loop-[custom-post-type].php before the loop executes.
 *
 * @param string $type The post type name.
 */
function appthemes_before_loop( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in the loop file and runs before the have_posts() loop whenever
	 * any custom post type content (i.e. ad listing, coupon, job listing, etc)
	 * is loaded.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_before_loop`.
	 */
	do_action( 'appthemes_before_' . $type . 'loop' );
}


/**
 * Called in loop-[custom-post-type].php before the post section.
 *
 * @param string $type The post type name.
 */
function appthemes_before_post( $type = 'post' ) {
	/**
	 * Called in loop-[custom-post-type].php before the post section.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_before_post`.
	 */
	do_action( 'appthemes_before_' . $type );
}


/**
 * Called in loop-[custom-post-type].php before the post title.
 *
 * @param string $type The post type name.
 */
function appthemes_before_post_title( $type = 'post' ) {
	/**
	 * Called in loop-[custom-post-type].php before the post title.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_before_post_title`.
	 */
	do_action( 'appthemes_before_' . $type . '_title' );
}


/**
 * Called in loop-[custom-post-type].php after the post title.
 *
 * @param string $type The post type name.
 */
function appthemes_after_post_title( $type = 'post' ) {
	/**
	 * Called in loop-[custom-post-type].php after the post title.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_after_post_title`.
	 */
	do_action( 'appthemes_after_' . $type . '_title' );
}


/**
 * Called in loop-[custom-post-type].php before the post content.
 *
 * @param string $type The post type name.
 */
function appthemes_before_post_content( $type = 'post' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in loop-[custom-post-type].php before the post content.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_before_post_content`.
	 */
	do_action( 'appthemes_before_' . $type . 'content' );
}


/**
 * Called in loop-[custom-post-type].php after the post content.
 *
 * @param string $type The post type name.
 */
function appthemes_after_post_content( $type = 'post' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in loop-[custom-post-type].php after the post content.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_after_post_content`.
	 */
	do_action( 'appthemes_after_' . $type . 'content' );
}


/**
 * Called in loop-[custom-post-type].php after the post section.
 *
 * @param string $type The post type name.
 */
function appthemes_after_post( $type = 'post' ) {
	/**
	 * Called in loop-[custom-post-type].php after the post section.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_after_post`.
	 */
	do_action( 'appthemes_after_' . $type );
}


/**
 * Called in loop-[custom-post-type].php after the loop endwhile.
 *
 * @param string $type The post type name.
 */
function appthemes_after_endwhile( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in loop-[custom-post-type].php after the loop endwhile.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_after_endwhile`.
	 */
	do_action( 'appthemes_after_' . $type . 'endwhile' );
}


/**
 * Called in loop-[custom-post-type].php after the loop else.
 *
 * @param string $type The post type name.
 */
function appthemes_loop_else( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in loop-[custom-post-type].php after the loop else.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_loop_else`.
	 */
	do_action( 'appthemes_' . $type . 'loop_else' );
}


/**
 * Called in loop-[custom-post-type].php after the loop executes.
 *
 * @param string $type The post type name.
 */
function appthemes_after_loop( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in loop-[custom-post-type].php after the loop executes.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_after_loop`.
	 */
	do_action( 'appthemes_after_' . $type . 'loop' );
}


/**
 * Called in comments-[custom-post-type].php before the comments list block.
 *
 * @param string $type The post type name.
 */
function appthemes_before_comments( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in comments-[custom-post-type].php before the comments list block.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_before_comments`.
	 */
	do_action( 'appthemes_before_' . $type . 'comments' );
}


/**
 * Called in comments-[custom-post-type].php in the ol block.
 *
 * @param string $type The post type name.
 */
function appthemes_list_comments( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in comments-[custom-post-type].php in the ol block.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_list_comments`.
	 */
	do_action( 'appthemes_list_' . $type . 'comments' );
}


/**
 * Called in comments-[custom-post-type].php after the comments list block.
 *
 * @param string $type The post type name.
 */
function appthemes_after_comments( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in comments-[custom-post-type].php after the comments list block.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_after_comments`.
	 */
	do_action( 'appthemes_after_' . $type . 'comments' );
}


/**
 * Called in comments-[custom-post-type].php before the pings list block.
 *
 * @param string $type The post type name.
 */
function appthemes_before_pings( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in comments-[custom-post-type].php before the pings list block.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_before_pings`.
	 */
	do_action( 'appthemes_before_' . $type . 'pings' );
}


/**
 * Called in comments-[custom-post-type].php in the ol block.
 *
 * @param string $type The post type name.
 */
function appthemes_list_pings( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in comments-[custom-post-type].php in the ol block.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_list_pings`.
	 */
	do_action( 'appthemes_list_' . $type . 'pings' );
}


/**
 * Called in comments-[custom-post-type].php after the pings list block.
 *
 * @param string $type The post type name.
 */
function appthemes_after_pings( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in comments-[custom-post-type].php after the pings list block.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_after_pings`.
	 */
	do_action( 'appthemes_after_' . $type . 'pings' );
}


/**
 * Called in comments-[custom-post-type].php before the comments respond block.
 *
 * @param string $type The post type name.
 */
function appthemes_before_respond( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in comments-[custom-post-type].php before the comments respond block.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_before_respond`.
	 */
	do_action( 'appthemes_before_' . $type . 'respond' );
}


/**
 * Called in comments-[custom-post-type].php after the comments respond block.
 *
 * @param string $type The post type name.
 */
function appthemes_after_respond( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in comments-[custom-post-type].php after the comments respond block.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_after_respond`.
	 */
	do_action( 'appthemes_after_' . $type . 'respond' );
}


/**
 * Called in comments-[custom-post-type].php before the comments form block.
 *
 * @param string $type The post type name.
 */
function appthemes_before_comments_form( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in comments-[custom-post-type].php before the comments form block.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_before_comments_form`.
	 */
	do_action( 'appthemes_before_' . $type . 'comments_form' );
}


/**
 * Called in comments-[custom-post-type].php to include the comments form block.
 *
 * @param string $type The post type name.
 */
function appthemes_comments_form( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in comments-[custom-post-type].php to include the comments form block.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_comments_form`.
	 */
	do_action( 'appthemes_' . $type . 'comments_form' );
}


/**
 * Called in comments-[custom-post-type].php after the comments form block
 *
 * @param string $type The post type name.
 */
function appthemes_after_comments_form( $type = '' ) {
	if ( $type ) {
		$type .= '_';
	}

	/**
	 * Called in comments-[custom-post-type].php after the comments form block.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the name of the
	 * post type. Default hook name is `appthemes_after_comments_form`.
	 */
	do_action( 'appthemes_after_' . $type . 'comments_form' );
}



/**
 * Sidebar hooks
 */


/**
 * Called in the sidebar template files before the widget section.
 *
 * @param string $location The sidebar name or location.
 */
function appthemes_before_sidebar_widgets( $location = '' ) {
	/**
	 * Called in the sidebar template files before the widget section.
	 *
	 * @param string $location The sidebar name or location.
	 */
	do_action( 'appthemes_before_sidebar_widgets', $location );
}


/**
 * Called in the sidebar template files after the widget section
 *
 * @param string $location The sidebar name or location.
 */
function appthemes_after_sidebar_widgets( $location = '' ) {
	/**
	 * Called in the sidebar template files after the widget section.
	 *
	 * @param string $location The sidebar name or location.
	 */
	do_action( 'appthemes_after_sidebar_widgets', $location );
}


/**
 * Footer hooks
 */


/**
 * Called in the footer.php before the footer section
 */
function appthemes_before_footer() {
	/**
	 * Called in the footer.php before the footer section
	 */
	do_action( 'appthemes_before_footer' );
}


/**
 * Invokes the footer section called in footer.php
 */
function appthemes_footer() {
	/**
	 * Invokes the footer section called in footer.php
	 */
	do_action( 'appthemes_footer' );
}


/**
 * Called in the footer.php after the footer section
 */
function appthemes_after_footer() {
	/**
	 * Called in the footer.php after the footer section.
	 */
	do_action( 'appthemes_after_footer' );
}


/**
 * Advertise hooks
 */


/**
 * Invokes the header advertise section
 */
function appthemes_advertise_header() {
	/**
	 * Invokes the header advertise section.
	 */
	do_action( 'appthemes_advertise_header' );
}


/**
 * Invokes the content advertise section
 */
function appthemes_advertise_content() {
	/**
	 * Invokes the content advertise section.
	 */
	do_action( 'appthemes_advertise_content' );
}


/**
 * Invokes notices on templates
 */
function appthemes_notices() {
	/**
	 * Invokes notices on templates.
	 */
	do_action( 'appthemes_notices' );
}

/**
 * Can be used in class method to extend method functionality with custom actions
 *
 * @param string $class Class name.
 * @param string $method Method name.
 */
function appthemes_class_method( $class, $method ) {
	do_action( strtolower( $class . '_' . $method ) );
}
