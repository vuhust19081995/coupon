<?php
/**
 * Business Listing Taxonomy Limit submodule.
 *
 * This module allows to limit the number of selected taxonomy terms on the
 * listing form.
 *
 * @package Listing\Modules\TaxLimit
 * @author  AppThemes
 * @since   4.0.0
 */

/**
 * Business Listing Taxonomy Limit class.
 */
class APP_Listing_Taxonomy_Limit {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Current taxonomy name.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Plan metabox object.
	 *
	 * @var APP_Listing_Plans_Taxonomy_Limit_Box
	 */
	protected $plan_metabox;

	/**
	 * Settings object.
	 *
	 * @var type APP_Listing_Taxonomy_Limit_Settings
	 */
	public $settings;

	/**
	 * Taxonomy limit meta key.
	 *
	 * @var string
	 */
	protected $meta_key;

	/**
	 * Constructs Listing Taxonomy Limit object.
	 *
	 * @param APP_Listing $listing  Listing object to assign process with.
	 * @param string      $taxonomy Limited taxonomy name.
	 * @param string      $meta_key Optional. Custom meta key to store current
	 *                              taxonomy limit in the plan and options.
	 *                              Default: "included_{$taxonomy}".
	 */
	public function __construct( APP_Listing $listing, $taxonomy, $meta_key = '' ) {

		$this->listing  = $listing;
		$this->taxonomy = $taxonomy;
		$this->meta_key = ( $meta_key ) ? $meta_key : "included_{$taxonomy}";
		$this->listing->options->set_defaults( $this->get_defaults() );

		if ( isset( $listing->multi_plans ) && is_admin() ) {
			$this->plan_metabox = new APP_Listing_Plans_Taxonomy_Limit_Box( $this, $listing->multi_plans );
		}

		if ( is_admin() ) {
			$this->settings = new APP_Listing_Taxonomy_Limit_Settings( $this, $listing );
		}

		add_filter( 'appthemes_form_field', array( $this, 'form_field' ), 10, 2 );
		add_action( 'appthemes_purchase_plan_fields', array( $this, '_render' ) );
	}

	/**
	 * Retrieves taxonomy limit meta key.
	 *
	 * @return type
	 */
	public function get_meta_key() {
		return $this->meta_key;
	}

	/**
	 * Adds extra parameters to listing form field.
	 *
	 * @param array $field   Form field.
	 * @param int   $item_id Listing item ID.
	 *
	 * @return array Modified Form field.
	 */
	public function form_field( $field, $item_id ) {

		if ( array( 'tax_input', $this->taxonomy ) !== $field['name'] ) {
			return $field;
		}

		$checkout = appthemes_get_checkout();
		$item     = get_post( $item_id );

		if ( ! $item || $this->listing->get_type() !== $item->post_type || ! $checkout ) {
			return $field;
		}

		$plan_id = $checkout->get_data( 'plan' );
		$plan    = null;
		$key     = $this->get_meta_key();

		if ( $this->listing->plans ) {
			// Fixing missing categories limit on the edit listing form.
			if ( ! $plan_id ) {
				$plan = $this->listing->plans->get_current_plan( $item_id );
			} else {
				$plan = $this->listing->plans->get_plan( $plan_id );
			}
		}

		if ( $plan instanceof APP_Listing_Plan_I ) {
			$maxchoice = (int) $plan->$key;
		} else {
			$maxchoice = (int) $this->listing->options->$key;
		}

		if ( 0 === $maxchoice ) {
			$title = __( 'Categories (choose unlimited categories)', APP_TD );
		} else {
			$title = sprintf( _n( 'Category (choose %d category)', 'Categories (choose %d categories)', $maxchoice, APP_TD ), $maxchoice );
		}

		$field['title'] = $title;
		$field['extra']['maxchoice'] = $maxchoice;
		$field['sanitizers'][] = array( $this, 'validate' );

		return $field;
	}

	/**
	 * Validates field.
	 *
	 * @param mixed          $value  Posted field value.
	 * @param scbCustomField $inst   Field object.
	 * @param WP_Error       $errors Errors object.
	 *
	 * @return mixed Validated value.
	 */
	public function validate( $value, $inst, $errors ) {
		$value = array_filter( (array) $value );
		if ( $inst->extra['maxchoice'] && count( $value ) > $inst->extra['maxchoice'] ) {
			$msg   = str_replace( '{0}', $inst->extra['maxchoice'], __( 'You have exceeded the category selection quantity limit ({0}).', APP_TD ) );
			$errors->add( 'category_limit', $msg );
		}

		return $value;
	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 */
	public function get_defaults() {
		return array( $this->get_meta_key() => 0 );
	}

	/**
	 * Renders Taxonomy Limits on the Select Plan step.
	 *
	 * @param APP_Listing_Plan_I $plan Current listing Plan.
	 */
	public function _render( APP_Listing_Plan_I $plan ) {

		if ( ! $this->listing->plans || ! $this->listing->plans->get_plan( $plan->get_type() ) ) {
			return;
		}

		$output = '';
		$key    = $this->get_meta_key();
		$limit  = $plan->$key;

		if ( ! $limit ) {
			$output = __( 'Unlimited categories', APP_TD );
		} else {
			$output = sprintf( _n( 'Includes %d category', 'Includes %d categories', $limit, APP_TD ), $limit );
		}

		echo html( 'div class="plan-categories"', $output );
	}

}
