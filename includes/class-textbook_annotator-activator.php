<?php

/**
 * Fired during plugin activation
 *
 * @link       https://bookannotater.com
 * @since      1.0.0
 *
 * @package    Textbook_annotator
 * @subpackage Textbook_annotator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Textbook_annotator
 * @subpackage Textbook_annotator/includes
 * @author     Ali Farahmand, Michael Lerner <mglerner@protonmail.com>
 */
class Textbook_annotator_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		Create_textbook_responses_tables();
	}

}
 function Create_textbook_responses_tables(){

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$sql = array();

	$responses_table_name = $wpdb->prefix . 'student_responses';
	$sql[] = "CREATE TABLE $responses_table_name (
	   id mediumint(9) NOT NULL AUTO_INCREMENT,
	   time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	   student_name tinytext NOT NULL,
	   description mediumtext,
	   textbook_id integer,
	   image_url mediumtext,
	   approved bool DEFAULT 0 NOT NULL,
	   PRIMARY KEY  (id)
	) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
 }


