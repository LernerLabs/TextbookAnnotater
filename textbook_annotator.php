<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://bookannotater.com
 * @since             1.0.0
 * @package           Textbook_annotator
 *
 * @wordpress-plugin
 * Plugin Name:       Textbook Annotator
 * Plugin URI:        https://bookannotater.com
 * Description: a plugin to annotate textbooks
 * Version:           0.2
 * Author:            Ali Farahmand, Michael Lerner
 * Author URI:        https://bookannotater.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       textbook_annotator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TEXTBOOK_ANNOTATOR_VERSION', '0.2' );

// directory helpers
define( 'TEXTBOOK_ANNOTATER__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TEXTBOOK_ANNOTATER__PLUGIN_URL', plugin_dir_url(__FILE__ ));


// show warning to install the DCO-comment-attachment plugin as a dependency
$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
if ( !in_array( 'dco-comment-attachment/dco-comment-attachment.php', $active_plugins ) ) {
	add_action( 'admin_notices', 'display_admin_notice'  );
}
function display_admin_notice(){
	echo "<div style='margin-top:50px;' class='alert alert-danger alert-dismissible fade show' role='alert'>";
	echo "Textbook Annotator plugin requires the DCO-comment-attachment plugin as a dependency to include images in student responses. Please install the DCO-comment-attachment plugin if you are planning to let students upload images of scientists.";
	echo "<br>";
	echo "You can install and activate this plugin here: ";
	$DCO_CA_link = admin_url('plugin-install.php?s=DCO-comment-attachment&tab=search&type=term');
	echo "<a href='$DCO_CA_link'>Install DCO-comment-attachment</a>";
	echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
	echo "</div>";
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-textbook_annotator-activator.php
 */
function activate_textbook_annotator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-textbook_annotator-activator.php';
	Textbook_annotator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-textbook_annotator-deactivator.php
 */
function deactivate_textbook_annotator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-textbook_annotator-deactivator.php';
	Textbook_annotator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_textbook_annotator' );
register_deactivation_hook( __FILE__, 'deactivate_textbook_annotator' );


// add response controller
require_once( TEXTBOOK_ANNOTATER__PLUGIN_DIR . 'includes/responses_controller.php' );

// add textbook controller
require_once( TEXTBOOK_ANNOTATER__PLUGIN_DIR . 'includes/textbook_controller.php' );

// Get the Admin page view
require_once( TEXTBOOK_ANNOTATER__PLUGIN_DIR . 'admin/admin_page.php' );


// Add admin menu 
add_action('admin_menu', 'textbook_annotater_setup_menu');
 
function textbook_annotater_setup_menu(){
    add_menu_page( 'Textbook Annotater Admin', 'Textbook Annotater', 'manage_options', 'textbook_annotater_plugin', 'show_admin_page' );
}

