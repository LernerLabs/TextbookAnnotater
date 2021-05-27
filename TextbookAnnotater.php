<?php
   /*
   Plugin Name: Textbook Annotater
   Plugin URI: http://bookannotater.com
   description: a plugin to annotate textbooks
   Version: 0.1
   Author: Ali Farahmand
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




?>