<?php
/**
 * Coupons type load
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.0
 */

APP_Listing_Autoload::add_class_map( array(
	'CLPR_Coupon_Listing_Builder' => dirname( __FILE__ ) . '/class-coupon-listing-builder.php',
	'CLPR_Coupon_Listing_Form'    => dirname( __FILE__ ) . '/class-coupon-listing-form.php',
	'CLPR_Step_Edit_Info'         => dirname( __FILE__ ) . '/class-coupon-listing-step-edit-info.php',
	'CLPR_Step_Edit_Info_New'     => dirname( __FILE__ ) . '/class-coupon-listing-step-edit-info-new.php',
	'CLPR_Coupon_Listing_Expire'  => dirname( __FILE__ ) . '/class-coupon-listing-expire.php',

	'CLPR_Coupon_Custom_Form_View'                => dirname( __FILE__ ) . '/class-coupon-custom-form-view.php',
	'CLPR_Coupon_Type_Code_Custom_Form_View'      => dirname( __FILE__ ) . '/class-coupon-custom-form-view.php',
	'CLPR_Coupon_Type_Printable_Custom_Form_View' => dirname( __FILE__ ) . '/class-coupon-custom-form-view.php',

	'CLPR_Coupon_Listing_Current_Plan_Expire_Decorator'  => dirname( __FILE__ ) . '/class-coupon-listing-current-plan-expire-decorator.php',
	'CLPR_Coupon_Listing_Plan_Expire_Decorator'          => dirname( __FILE__ ) . '/class-coupon-listing-plan-expire-decorator.php',

	'CLPR_Coupon_Listing_Fields_Metabox'      => dirname( __FILE__ ) . '/admin/class-coupon-listing-fields-metabox.php',
	'CLPR_Coupon_Listing_Type_Metabox'        => dirname( __FILE__ ) . '/admin/class-coupon-listing-type-metabox.php',
	'CLPR_Coupon_Listing_Type_Fields_Metabox' => dirname( __FILE__ ) . '/admin/class-coupon-listing-type-fields-metabox.php',
	'CLPR_Coupon_Listing_Plans_Settings'      => dirname( __FILE__ ) . '/admin/class-coupon-listing-plans-settings.php',
) );

new CLPR_Coupon_Type_Code_Custom_Form_View( 'tpl-coupon-type-code.php', __( 'Coupon Type Code Subform', APP_TD ), APP_FORMS_PTYPE );
new CLPR_Coupon_Type_Printable_Custom_Form_View( 'tpl-coupon-type-printable.php', __( 'Coupon Type Printable Subform', APP_TD ), APP_FORMS_PTYPE );
