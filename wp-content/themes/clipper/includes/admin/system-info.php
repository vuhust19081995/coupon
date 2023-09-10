<?php
/**
 * System Information.
 *
 * @package Clipper\Admin\SystemInfo
 * @author  AppThemes
 * @since   Clipper 1.5
 */


/**
 * System Info page.
 */
class CLPR_Theme_System_Info extends APP_System_Info {

	/**
	 * Setups page.
	 *
	 * @param array $args (optional)
	 * @param object $options (optional)
	 *
	 * @return void
	 */
	function __construct( $args = array(), $options = null ) {

		parent::__construct( $args, $options );

		add_action( 'admin_notices', array( $this, 'admin_tools' ) );
	}

	/**
	 * Processes admin tools.
	 *
	 * @return void
	 */
	public function admin_tools() {

		if ( ! empty( $_POST['clpr_tools']['delete_tables'] ) ) {
			appthemes_delete_db_tables();
		}

		if ( ! empty( $_POST['clpr_tools']['delete_options'] ) ) {
			appthemes_delete_all_options();
		}

	}

	/**
	 * Handles form submission.
	 *
	 * @return void
	 */
	function form_handler() {
		if ( empty( $_POST['action'] ) || ! $this->tabs->contains( $_POST['action'] ) ) {
			return;
		}

		check_admin_referer( $this->nonce );

		if ( ! empty( $_POST['clpr_tools'] ) ) {
			return;
		} else {
			parent::form_handler();
		}
	}

	/**
	 * Initializes page tabs.
	 *
	 * @return void
	 */
	protected function init_tabs() {
		parent::init_tabs();

		$this->tabs->add( 'clpr_tools', __( 'Advanced', APP_TD ) );

		$this->tab_sections['clpr_tools']['uninstall'] = array(
			'title' => __( 'Uninstall Theme', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Database Tables', APP_TD ),
					'type' => 'submit',
					'name' => array( 'clpr_tools', 'delete_tables' ),
					'extra' => array(
						'class' => 'button-secondary',
						'onclick' => 'return clpr_confirmBeforeDeleteTables();',
					),
					'value' => __( 'Delete Clipper Tables', APP_TD ),
					'desc' => __( 'You will lose any custom fields and stores meta that you have created.', APP_TD ),
				),
				array(
					'title' => __( 'Config Options', APP_TD ),
					'type' => 'submit',
					'name' => array( 'clpr_tools', 'delete_options' ),
					'extra' => array(
						'class' => 'button-secondary',
						'onclick' => 'return clpr_confirmBeforeDeleteOptions();',
					),
					'value' => __( 'Delete Clipper Options', APP_TD ),
					'desc' => __( 'All values saved on the settings, pricing, etc admin pages will be erased from the wp_options table.', APP_TD ),
				),
			),
		);

	}

	/**
	 * Prints page footer.
	 *
	 * @return void
	 */
	function page_footer() {
		parent::page_footer();
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	if ( $("form input[name^='clpr_tools']").length ) {
		$('form p.submit').html('');
	}
});
</script>
<?php
	}

}

