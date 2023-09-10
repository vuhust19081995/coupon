<?php
/**
 * Installation functions.
 *
 * @package Clipper\Admin\Install
 * @author  AppThemes
 * @since   Clipper 1.0
 */


/**
 * Fires theme installation scripts.
 *
 * @return void
 */
function clpr_install_theme() {

	// run the table install script
	clpr_tables_install();

}
add_action( 'appthemes_first_run', 'clpr_install_theme' );


/**
 * Creates the theme database custom tables.
 *
 * @return void
 */
function clpr_tables_install() {

	// create the recent search terms table
	$sql = "
		id int(11) NOT NULL AUTO_INCREMENT,
		terms varchar(50) NOT NULL,
		datetime datetime NOT NULL,
		hits int(11) NOT NULL,
		details text NOT NULL,
		PRIMARY KEY  (id),
		KEY datetimeindex (datetime)";

	scb_install_table( 'clpr_search_recent', $sql );


	// create the total search terms table
	$sql = "
		id int(11) NOT NULL AUTO_INCREMENT,
		terms varchar(50) NOT NULL,
		date date NOT NULL,
		count int(11) NOT NULL,
		last_hits int(11) NOT NULL,
		status tinyint(1) NOT NULL DEFAULT '0',
		PRIMARY KEY  (id,date)";

	scb_install_table( 'clpr_search_total', $sql );


	// create the meta table for the custom stores taxonomy
	$sql = "
		meta_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		stores_id bigint(20) unsigned NOT NULL default '0',
		meta_key varchar(255) DEFAULT NULL,
		meta_value longtext,
		PRIMARY KEY  (meta_id),
		KEY stores_id (stores_id),
		KEY meta_key (meta_key)";

	scb_install_table( 'clpr_storesmeta', $sql );


	// create the votes total table
	$sql = "
		id int(11) NOT NULL AUTO_INCREMENT,
		post_id int(11) NOT NULL,
		user_id int(11) NOT NULL,
		vote int(4) NOT NULL,
		ip_address varchar(15) NOT NULL,
		date_stamp datetime NOT NULL,
		PRIMARY KEY  (id)";

	scb_install_table( 'clpr_votes', $sql );


	// create the votes total table
	$sql = "
		id int(11) NOT NULL AUTO_INCREMENT,
		post_id int(11) NOT NULL,
		votes_up int(11) NOT NULL DEFAULT '0',
		votes_down int(11) NOT NULL DEFAULT '0',
		votes_total int(11) NOT NULL DEFAULT '0',
		last_update datetime NOT NULL,
		PRIMARY KEY  (id)";

	scb_install_table( 'clpr_votes_total', $sql );


}
