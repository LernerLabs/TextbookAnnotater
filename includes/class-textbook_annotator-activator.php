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
        add_option("textbook_annotator_use_images", 1);
        add_option("textbook_annotator_delete_on_uninstall", 0);
        
	}

}


