<?php
/**
 * Listing Form UI
 *
 * @package Listing\Admin\Settings\Forms
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Forms Settings
 *
 * Requires theme supports:
 *  - app-framework
 *  - app-dashboard
 *  - app-custom-forms
 */
class APP_Listing_Form_Settings extends APP_Conditional_Tabs_Page {

	/**
	 * Current Listing module object
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Conditional flag
	 * @var bool
	 */
	private static $create_page = true;

	/**
	 * Construct Listing Form settings module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;

		parent::__construct( null );
	}

	/**
	 * Checks condition whether to create page or register tab in existing one
	 *
	 * @return bool
	 */
	public function conditional_create_page() {
		return self::$create_page;
	}

	/**
	 * Setup settings page
	 */
	function setup() {

		$this->textdomain = APP_TD;

		$this->args = array(
			'page_title'            => __( 'Forms', APP_TD ),
			'menu_title'            => __( 'Forms', APP_TD ),
			'page_slug'             => 'app-forms',
			'parent'                => 'app-dashboard',
			'conditional_page'      => 'app-forms',
			'conditional_parent'    => 'app-dashboard',
			'screen_icon'           => 'options-general',
			'admin_action_priority' => 11,
		);

		self::$create_page = false;
	}

	/**
	 * Adds settings page tabs
	 */
	public function init_tabs() {

		$options  = $this->listing->options;
		$type     = $this->listing->get_type();
		$form_obj = $this->listing->form;
		$name     = $this->listing->meta->get_labels( $type )->name;
		$builder  = new APP_Form_Builder_Field_Type( $form_obj->get_core_fields() );

		$this->tabs->add( $type, $name );

		$this->tab_sections[ $type ]['general'] = array(
			'title' => '',
			'fields' => array(
				array(
					'title'    => '',
					'type'     => 'custom',
					'name'     => 'app_form',
					'render'   => array( $builder, '_render' ),
					'sanitize' => array( $builder, '_sanitize' ),
				),
			),
			'options' => $options,
		);

		$tabs = $this->tabs->get_all();
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : key( $tabs ); // Input var okay.

		if ( $type === $active_tab ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 99 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_cond_enqueue_scripts' ), 100 );
		}
	}

	/**
	 * Generates a table row.
	 *
	 * @param array         $field    Field parameters.
	 * @param array|boolean $formdata Form data.
	 *
	 * @return string Generated row
	 */
	public function table_row( $field, $formdata = false ) {
		return scbForms::input( $field, $formdata );
	}

	/**
	 * Added for compatibility purposes with parent classes
	 *
	 * @param string $output Output.
	 *
	 * @return string Output
	 */
	public function table_wrap( $output, $section_id = '' ) {
		return $output;
	}

	/**
	 * Enqueues scripts which will be loaded wherever 'form-builder' scripts
	 * loaded.
	 */
	public function admin_cond_enqueue_scripts() {

		wp_enqueue_script(
			'app-listing-forms-ui',
			APP_LISTING_URI . '/parts/forms/admin/scripts/forms-ui.js',
			array( 'form-builder' ),
			APP_LISTING_VERSION
		);

		$taxonomies = get_object_taxonomies( $this->listing->get_type() );
		$tax_data = array();
		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_obj = get_taxonomy( $taxonomy );
			$tax_data[] = array(
				'name'  => $taxonomy,
				'label' => $taxonomy_obj->labels->name,
			);
		}

		wp_localize_script(
			'app-listing-forms-ui',
			'listingL10n',
			array(
				'disable_tip'       => __( 'Disable field.', APP_TD ),
				'enable_tip'        => __( 'Enable field.', APP_TD ),
				'placeholder_label' => __( 'Placeholder', APP_TD ),
				'placeholder_cond'  => __( '(for flat textarea type)', APP_TD ),
				'taxonomy_label'    => __( 'Taxonomy', APP_TD ),
				'field_type_label'  => __( 'Field Type', APP_TD ),
				'editor_type_label' => __( 'Editor Type', APP_TD ),
				'file_limit_label'  => __( 'File Limit', APP_TD ),
				'embed_limit_label' => __( 'Embed Limit', APP_TD ),
				'file_size_label'   => __( 'File Size, KB', APP_TD ),
				'file_size_tip'     => __( '(max size depends on server configuration)', APP_TD ),
				'file_size'         => round( wp_max_upload_size() / 1024 ),
				'same_tax'          => __( 'Use of same taxonomy fields on the form is prohibited.', APP_TD ),
				'taxonomies'        => $tax_data,
				'tax_inputs'        => array(
					array(
						'name'  => 'text',
						'label' => __( 'Text Field', APP_TD ),
					),
					array(
						'name'  => 'select',
						'label' => __( 'Dropdown Field', APP_TD ),
					),
					array(
						'name'  => 'checkbox',
						'label' => __( 'Multiple Checkboxes', APP_TD ),
					),
				),
				'editor_types'      => array(
					array(
						'name'  => '',
						'label' => __( 'Disabled', APP_TD ),
					),
					array(
						'name'  => 'html',
						'label' => __( 'HTML Editor', APP_TD ),
					),
					array(
						'name'  => 'tmce',
						'label' => __( 'TinyMCE Editor', APP_TD ),
					),
				),
			)
		);
	}

	/**
	 * Enqueues scripts which will be loaded only on the Listing Forms page.
	 */
	public function admin_enqueue_scripts() {

		APP_Form_Builder::enqueue_scripts();
		add_action( 'admin_print_scripts', array( 'APP_Form_Builder', 'print_templates' ), 99 );

	}

}

/**
 * Form Builder Field Type
 */
class APP_Form_Builder_Field_Type {

	/**
	 * Form fields
	 * @var array
	 */
	protected $fields;

	/**
	 * Creates Form Builder Field type object
	 *
	 * @param array $fields Form fields.
	 */
	public function __construct( $fields ) {
		$this->fields = $fields;
	}

	/**
	 * Retrieves field html
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return string Generated html
	 */
	public function _render( $value, $inst ) {

		if ( empty( $value ) ) {
			$value = $this->fields;
		} else {
			$protected_ids = wp_list_pluck( $this->fields, 'id' );
			foreach ( $value as &$field ) {
				if ( in_array( $field['id'], $protected_ids, true ) ) {
					$field['protected'] = 1;
				}
			}
		}

		ob_start();
		?>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-1">
				<div class="postbox-container">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">
						<div id="app-form-builder" class="postbox ">
							<div class="inside">
								<?php APP_Form_Builder::display_form( $value ); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	/**
	 * Sanitizes field value before save
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return mixed Sanitized value
	 */
	public function _sanitize( $value, $inst ) {
		$value = APP_Form_Builder::parse_form( $value );

		$posted = wp_list_pluck( $value, 'id' );

		// Return back accidentally deleted core fields.
		foreach ( $this->fields as $field ) {
			if ( ! in_array( $field['id'], $posted ) ) {
				$value[] = $field;
			}
		}

		return $value;
	}
}
