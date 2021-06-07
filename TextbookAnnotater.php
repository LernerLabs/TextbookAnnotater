<?php
   /*
   Plugin Name: Textbook Annotater
   Plugin URI: http://bookannotater.com
   description: a plugin to annotate textbooks
   Version: 0.1
   Author: Ali Farahmand, Michael Lerner
   Author URI: https://af3.tech
   License: MIT
   */


// directory helpers
define( 'TEXTBOOK_ANNOTATER__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TEXTBOOK_ANNOTATER__PLUGIN_URL', plugin_dir_url(__FILE__ ));


// Get the Admin page view
require_once( TEXTBOOK_ANNOTATER__PLUGIN_DIR . 'admin_page.php' );


// Add admin menu 
add_action('admin_menu', 'textbook_annotater_setup_menu');
 
function textbook_annotater_setup_menu(){
    add_menu_page( 'Textbook Annotater Admin', 'Textbook Annotater', 'manage_options', 'textbook_annotater_plugin', 'show_admin_page' );
}


// Create DB table for textbooks
register_activation_hook( __FILE__, 'Create_textbook_responses_tables' );
function Create_textbook_responses_tables(){
   
   global $wpdb;
   $textbooks_table_name = $wpdb->prefix . 'textbooks';
   $charset_collate = $wpdb->get_charset_collate();

   $sql = array();

   $sql[] = "CREATE TABLE $textbooks_table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      name tinytext NOT NULL,
      author tinytext NOT NULL,
      page_url mediumtext,
      PRIMARY KEY  (id)
   ) $charset_collate;";

   $responses_table_name = $wpdb->prefix . 'student_responses';
   $sql[] = "CREATE TABLE $responses_table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      student_name tinytext NOT NULL,
      description mediumtext,
      textbook_id integer,
      image_url mediumtext,
      PRIMARY KEY  (id)
   ) $charset_collate;";
   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
}

// add response controller
require_once( TEXTBOOK_ANNOTATER__PLUGIN_DIR . 'responses.php' );

// uninstall steps
require_once( TEXTBOOK_ANNOTATER__PLUGIN_DIR . 'uninstall.php' );

?>