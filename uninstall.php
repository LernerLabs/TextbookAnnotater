<?php
// add hook 
register_uninstall_hook(__FILE__, 'drop_textbook_table');

function drop_textbook_table(){
	global $wpdb;
	$textbook_table = $wpdb->prefix . 'textbooks';
	$wpdb->query( "DROP TABLE IF EXISTS $textbook_table" );
}


?>