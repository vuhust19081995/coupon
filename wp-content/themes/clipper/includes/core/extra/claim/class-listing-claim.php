<?php
/**
 * Listing Claim submodule
 *
 * @package Listing\Modules\Claim
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Claim class
 */
class APP_Listing_Claim {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Module settings object.
	 *
	 * @var type APP_Listing_Claim_Settings
	 */
	public $settings;

	/**
	 * Claimable Listing metabox object.
	 *
	 * @var APP_Listing_Claimable_Metabox
	 */
	public $claim_box;

	/**
	 * Moderation Listing Claim metabox object.
	 *
	 * @var APP_Listing_Claim_Moderation_Metabox
	 */
	public $moderation_box;

	/**
	 * Construct module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;
		$this->listing->options->set_defaults( $this->get_defaults() );
		$this->activate_modules();

		if ( did_action( 'init' ) ) {
			$this->init();
		} else {
			add_action( 'init', array( $this, 'init' ), 1000 );
		}
	}

	/**
	 * Init module.
	 */
	public function init() {
		$this->_register_post_status();
		$this->_register_payment_item();
	}

	/**
	 * Registers Pending Claimed post status.
	 */
	public function _register_post_status() {

		$label = _x( 'Pending Claimed', 'listing post status label', APP_TD );

		register_post_status( 'pending-claimed', array(
			'label'                     => $label,
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>' ),
		) );
	}

	/**
	 * Registers Listing Claim payment item.
	 */
	public function _register_payment_item() {

		$type  = "{$this->listing->get_type()}-claim";
		$title = __( 'Listing Claim', APP_TD );

		APP_Item_Registry::register( $type, $title );
	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 */
	public function get_defaults() {
		return array( 'moderate_claimed_listings' => 'yes' );
	}

	/**
	 * Activates submodules
	 */
	protected function activate_modules() {

		// Setup process.
		new APP_View_Process_Claim( $this->listing );

		if ( ! $this->settings && is_admin() ) {
			//$this->settings = new APP_Listing_Claim_Settings( $this->listing );
		}

		if ( ! $this->claim_box && is_admin() ) {
			$this->claim_box = new APP_Listing_Claimable_Metabox( $this->listing->get_type() );
		}

		if ( ! $this->moderation_box && is_admin() ) {
			$this->moderation_box = new APP_Listing_Claim_Moderation_Metabox( $this->listing->get_type() );
		}

		if ( is_admin() ) {
			new APP_Listing_Claim_Importer( $this->listing );
		}
	}
}
