<?php
/**
 * Views API
 *
 * @package Framework\Views
 */

/**
 * Helper class for controlling all aspects of a view.
 *
 * Supported methods (automatically hooked):
 * - `init()`                    - for registering post types, taxonomies, rewrite rules etc.
 * - `parse_query()`             - for correcting query flags
 * - `pre_get_posts()`           - for altering the query, without affecting the query flags
 * - `posts_search()`,
 * - `posts_clauses()`,
 * - `posts_request()`           - for direct SQL manipulation
 * - `the_posts()`               - for various other manipulations
 * - `template_redirect()`       - for enqueuing scripts etc.
 * - `template_include( $path )` - for loading a different template file
 * - `title_parts( $parts )`     - for changing the title
 * - `breadcrumbs( $trail )`     - for changing the breadcrumbs
 * - `notices()`                 - for displaying notices
 */
abstract class APP_View {

	/**
	 * Test if this class should handle the current view.
	 *
	 * Use is_*() conditional tags and get_query_var()
	 *
	 * @return bool
	 */
	abstract function condition();


	public function __construct() {
		// 'init' hook (always ran)
		if ( method_exists( $this, 'init' ) ) {
			add_action( 'init', array( $this, 'init' ) );
		}

		// $wp_query hooks
		$actions = array( 'parse_query', 'pre_get_posts' );
		$filters = array( 'posts_search', 'posts_clauses', 'posts_request', 'the_posts' );

		foreach ( $actions as $method ) {
			if ( method_exists( $this, $method ) ) {
				add_action( $method, array( $this, '_action' ) );
			}
		}

		foreach ( $filters as $method ) {
			if ( method_exists( $this, $method ) ) {
				add_filter( $method, array( $this, '_filter' ), 10, 2 );
			}
		}

		// other hooks
		add_action( 'template_redirect', array( $this, '_template_redirect' ), 9 );
	}

	final function _action( $wp_query ) {
		if ( $wp_query->is_main_query() && $this->condition() ) {
			$method = current_filter();

//			debug( get_class( $this ) . '->' . $method . '()' );

			$this->$method( $wp_query );
		}
	}

	final function _filter( $value, $wp_query ) {
		if ( $wp_query->is_main_query() && $this->condition() ) {
			$method = current_filter();

//			debug( get_class( $this ) . '->' . $method . '()' );

			$value = $this->$method( $value, $wp_query );
		}

		return $value;
	}

	final function _template_redirect() {
		if ( ! $this->condition() ) {
			return;
		}

		if ( method_exists( $this, 'template_redirect' ) ) {
			$this->template_redirect();
		}

		$filters = array(
			'template_include' => 'template_include',
			'appthemes_title_parts' => 'title_parts',
			'appthemes_notices' => 'notices',
			'appthemes_open_graph_meta_tags' => 'meta_tags',
			'breadcrumb_trail_items' => 'breadcrumbs',
		);

		// register any vars that need to be passed to loaded template
		if ( method_exists( $this, 'template_vars' ) ) {
			appthemes_add_template_var( $this->template_vars() );
		}

		foreach ( $filters as $filter => $method ) {
			if ( method_exists( $this, $method ) ) {
				add_filter( $filter, array( $this, $method ) );
			}
		}
	}

	public function notices() {
		appthemes_display_notices();
	}
}


/**
 * Class for handling special pages that have a specific template file.
 */
class APP_View_Page extends APP_View {

	/**
	 * Page templates array.
	 *
	 * @access private
	 * @var array $template An array of template and fallback associated with current page
	 */
	private $template;

	/**
	 * The page title.
	 *
	 * @access private
	 * @var string $default_title Translated page title
	 */
	private $default_title;

	/**
	 * The cached page ID.
	 *
	 * @access private
	 * @var int $page_id Page ID
	 */
	private $page_id;

	/**
	 * The list of instances.
	 *
	 * @deprecated
	 * @access private
	 * @var array $instances An array of instances, keyed with class name
	 */
	private static $instances = array();

	/**
	 * The list of instances.
	 *
	 * @access private
	 * @var array $registry An array of instances, keyed with template file name
	 */
	private static $registry = array();

	/**
	 * The type of viewing item.
	 *
	 * Default: "page";
	 *
	 * @var string
	 */
	private $ptype;

	public function __construct( $template, $default_title, $ptype = 'page' ) {
		$this->ptype = $ptype;
		$this->template = (array) $template;
		$this->default_title = $default_title;

		self::$registry[ $this->get_template() ] = $this;

		// DEPRECATED
		self::$instances[ get_class( $this ) ] = $this;

		add_filter( "theme_{$ptype}_templates", array( $this, '_register_new_template' ) );
		if ( is_admin() ) {
			// prevent to set preserved template if it's already in use
			add_filter( 'wp_insert_post_data', array( $this, '_preserve_template' ), 10, 2 );
			add_action( "save_post_{$ptype}", array( __CLASS__, '_restore_preserved_template' ) );
		}

		add_filter( 'template_include', array( $this, 'pre_template_include' ), 9 );

		parent::__construct();
	}

	public function condition() {
		if ( is_singular( $this->get_post_type() ) && is_page_template( $this->get_template() ) ) {
			return true;
		}

		$page_id = (int) get_query_var( 'page_id' );

		return $page_id && $page_id == $this->get_page_id(); // for 'page_on_front'
	}

	/**
	 * Locates fallback template if original wasn't found in the standard
	 * template directories.
	 *
	 * If falback wasn't found, fires an action to allow to continue location in
	 * the custom template directories.
	 *
	 * @param string $path The path to located template file or null
	 *
	 * @return string The path to located template fallback file or original path
	 */
	public function pre_template_include( $path ) {
		if ( ! $this->condition() ) {
			return $path;
		}

		// template located by standard WordPress behaviour
		$default = basename( $path );

		// original template was found in the theme or child theme directories
		if ( in_array( $default, $this->template ) ) {
			return $path;
		}
		// otherwise maybe template fallback can be located there
		else if ( 1 < count( $this->template ) ) {
			$located = locate_template( $this->template );
			// return located template fallback
			if ( $located ) {
				return $located;
			}
		}

		/**
		 * Fires if page template or its fallback wasn't found in the theme or
		 * child theme directories.
		 *
		 * Further template location might be continued in other template
		 * directories and included using 'template_include' filter.
		 *
		 * If 'app-theme-compat' module is active, it will try to find template
		 * in other template directories and put generated HTML to the content
		 * of default page template.
		 *
		 * @param array  $template Current page template and its fallbacks.
		 * @param string $path     Default page template file path.
		 */
		do_action( 'appthemes_template_was_not_located', $this->template, $path );
		return $path;
	}

	/**
	 * Retrieves page ID by the given class name
	 *
	 * @deprecated
	 * @param string $class A class name associated with given page
	 *
	 * @return int Page ID
	 */
	public static function _get_id( $class ) {
		_doing_it_wrong( __METHOD__, __( 'Retrieving page id by APP_View_Page subclass name is deprecated, use APP_View_Page::_get_page_id( $template ) instead!', APP_TD ), null );

		$template = self::$instances[ $class ]->get_template();
		return self::_get_page_id( $template );
	}

	/**
	 * Retrieves page ID associated with current instance
	 *
	 * @return int Page ID
	 */
	public final function get_page_id() {
		return self::_get_page_id( $this->get_template() );
	}

	/**
	 * Retrieves the template file name associated with current instance
	 *
	 * @return string Template file name
	 */
	public final function get_template() {
		return $this->template[0];
	}

	/**
	 * Retrieves page ID by the given template
	 *
	 * @param string $template Template file name
	 *
	 * @return int Page ID
	 */
	public static function _get_page_id( $template ) {

		if ( isset( self::$registry[ $template ]->page_id ) ) {
			return self::$registry[ $template ]->page_id;
		}

		// don't use 'fields' => 'ids' because it skips caching
		$page_q = new WP_Query( array(
			'post_type' => self::$registry[ $template ]->get_post_type(),
			'meta_key' => '_wp_page_template',
			'meta_value' => $template,
			'posts_per_page' => 1,
			'no_found_rows' => true,
			'suppress_filters' => true,
		) );

		if ( empty( $page_q->posts ) ) {
			$page_id = 0;
		} else {
			$page_id = $page_q->posts[0]->ID;
		}

		/**
		 * Allows changing page id for a given template.
		 *
		 * @param int    $page_id  The page id to be changed.
		 * @param string $template The template file name.
		 */
		$page_id = apply_filters( 'appthemes_page_id_for_template', $page_id, $template );

		self::$registry[ $template ]->page_id = $page_id;

		return $page_id;
	}

	/**
	 * Install all registered pages procedure
	 */
	public static function install() {
		foreach ( self::$registry as $template => $instance ) {

			if ( self::_get_page_id( $template ) ) {
				continue;
			}

			$page_id = wp_insert_post( array(
				'post_type' => $instance->get_post_type(),
				'post_status' => 'publish',
				'post_title' => $instance->default_title
			) );

			// Cache will have been set to 0, so update it
			$instance->page_id = $page_id;

			add_post_meta( $page_id, '_wp_page_template', $template );
		}
	}

	/**
	 * Uninstall all registered pages procedure
	 */
	public static function uninstall() {
		foreach ( self::$registry as $template => $instance ) {
			$page_id = self::_get_page_id( $template );

			if ( ! $page_id ) {
				continue;
			}

			wp_delete_post( $page_id, true );

			$instance->page_id = 0;
		}
	}

	/**
	 * Callback method for "theme_{$post_type}_templates" filter to add extra
	 * templates in WordPress template lists.
	 *
	 * @param array $templates
	 * @return array
	 */
	public final function _register_new_template( $templates ) {

		if ( ! isset( $templates[ $this->get_template() ] ) ) {
			$templates[ $this->get_template() ] = $this->default_title;
		}

		return $templates;
	}

	/**
	 * Retrieves all registered page templates.
	 *
	 * @return array An array of registered templates, keyed by filename,
	 *               with the value of the translated header name.
	 */
	public static function _get_templates() {

		$templates = self::$registry;

		foreach ( $templates as $template => $instance ) {
			$templates[ $template ] = $instance->default_title;
		}

		return $templates;
	}

	/**
	 * Preserves old template name before change page template in case that
	 * this template is already in use
	 *
	 * @param array $data    An array of slashed post data.
	 * @param array $postarr An array of sanitized, but otherwise unmodified post data.
	 *
	 * @return array An array of post data
	 */
	public final function _preserve_template( $data, $postarr ) {

		$page = get_post( $postarr['ID'] );

		if ( ! $page || $this->get_post_type() !== $page->post_type ) {
			return $data;
		}

		if ( empty( $postarr['page_template'] ) || 'default' == $postarr['page_template'] ) {
			return $data;
		}

		$new_template = $postarr['page_template'];

		if ( $new_template !== $this->get_template() ) {
			return $data;
		}

		$page_q = new WP_Query( array(
			'post_type' => $this->get_post_type(),
			'meta_key' => '_wp_page_template',
			'meta_value' => $new_template,
			'no_found_rows' => true,
			'suppress_filters' => true,
		) );

		if ( empty( $page_q->posts ) ) {
			return $data;
		}

		foreach ( $page_q->posts as $post ) {
			if ( $post->ID != $page->ID ) {
				$old_template = get_post_meta( $page->ID, '_wp_page_template', true );
				update_post_meta( $page->ID, '_preserved_page_template', $old_template );
				break;
			}
		}

		return $data;
	}

	/**
	 * Restores old template name after change page template in case that this
	 * template was already in use
	 *
	 * @param int $post_id Edited page ID
	 */
	public static function _restore_preserved_template( $post_id ) {
		$preserved_template = get_post_meta( $post_id, '_preserved_page_template', true );

		if ( $preserved_template ) {
			update_post_meta( $post_id, '_wp_page_template', $preserved_template );
			delete_post_meta( $post_id, '_preserved_page_template', $preserved_template );
		}
	}

	/**
	 * Modify open graph meta tags for special pages.
	 *
	 * @param array $tags
	 *
	 * @return array
	 */
	public function meta_tags( $tags ) {
		$tags['og:type'] = 'website';

		$to_remove = array(
			'article:author',
			'article:published_time',
			'article:modified_time',
		);

		foreach ( $to_remove as $tag ) {
			if ( isset( $tags[ $tag ] ) ) {
				unset( $tags[ $tag ] );
			}
		}

		return $tags;
	}

	/**
	 * Retrieves the type of viewing item.
	 *
	 * @return string
	 */
	public final function get_post_type() {
		return $this->ptype;
	}

}

add_action( 'appthemes_first_run', array( 'APP_View_Page', 'install' ), 9 );